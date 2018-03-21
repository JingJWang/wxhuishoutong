<?php 
//入口核心代码

//访问控制列表
// 指定允许其他域名访问
header("Access-Control-Allow-Origin: *");
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
//header('Access-Control-Allow-Headers:x-requested-with,content-type');

include_once '../../config.inc.php';
include_once './PathConfig.php';

define('PATH_ROOT',              dirname(__FILE__) . '/../../' );       // 代码根目录
define('PATH_DATAOBJ',           PATH_ROOT . 'dataobj/');               // 数据对象目录
define('PATH_DATAOBJ_API',       PATH_DATAOBJ . 'api/');                // API目录

define('PATH_DATAOBJ_API_DATABASE', PATH_DATAOBJ_API . 'database/');    // API目录
define('PATH_DATAOBJ_API_FILE',     PATH_DATAOBJ_API . 'file/');        // API目录

define('PATH_DATAOBJ_COMMAND', PATH_DATAOBJ . 'command/');              // 命令目录
define('PATH_DATAOBJ_MODULE',  PATH_DATAOBJ . 'module/');               // 模块目录
define('PATH_DATAOBJ_SDK',  PATH_DATAOBJ . 'sdk/');                     // SDK目录

define('PATH_DATAOBJ_MODULE_USER',  PATH_DATAOBJ_MODULE . 'user/');     // 模块用户目录
define('PATH_DATAOBJ_MODULE_ORDER',  PATH_DATAOBJ_MODULE . 'order/');   // 模块订单目录
define('PATH_DATAOBJ_MODULE_TOOLS',  PATH_DATAOBJ_MODULE . 'tools/');   // 模块工具目录

define('PATH_DATAOBJ_SDK_WX',  PATH_DATAOBJ_SDK . 'wx/');               // SDK微信目录
define('PATH_DATAOBJ_SDK_WX_PAY',  PATH_DATAOBJ_SDK_WX . 'pay/');       // SDK微信支付目录

const CMD_TYPE_SC = 'SC';    //服务器->客户端
const CMD_TYPE_CS = 'CS';    //客户端->服务器

const CMD_RES_OK = 0;
const CMD_RES_ERR_PARAMS = 1;
const CMD_RES_ERR_CMD = 2;

gateway();

function gateway() {
	if (is_array($_REQUEST)) {
		_writeAccessLog(json_encode($_REQUEST));
	}
	else {
		_writeAccessLog($_REQUEST);
	}

	//请求命令格式
	//{"smartparking":{"data":"业务数据","type":"类型","version":"版本号","sign":"签名"}}
	//{"smartparking":{"data":{"cmd":"命令名称", "params":"命令参数"},"type":"类型","version":"版本号","sign":"签名"}}

	$logConfig = $GLOBALS['config']['log'];
	$dbConfig = $GLOBALS['config']['db'];
	$memcacheConfig = $GLOBALS['config']['memcache'];
	$versionConfig = $GLOBALS['config']['version'];
	
	$smartparking = json_decode(str_replace('\\', '', $_REQUEST['smartparking']), true);
	if (isset($smartparking) && !empty($smartparking) && is_array($smartparking)) {
		
		//各种配置读取
	
		//命令体解析
		$cmd = $smartparking['data']['cmd'];
		$params = $smartparking['data']['params'];
	
		$type = $smartparking['type'];
		$version = $smartparking['version'];
		$sign = $smartparking['sign'];

		$filename = '../../dataobj/command/' . $cmd . '.php';

		if (file_exists($filename)) {
			//命令文件存在
			//回调接口生成
			include_once '../../dataobj/command/' . 'BaseCommand.php';
			include_once '../../dataobj/command/' . $cmd . '.php';
			$exeCmd = new $cmd();
			$exeResult = $exeCmd->execute($params);

            $result = CMD_RES_OK;
		}
		else {
            //命令文件不存在
            $result = CMD_RES_CMD;
            $exeResult = array();
		}

		//命令建立
        $result = array('smartparking' => array('data' => array('cmd' => $cmd,
                                                                'result' => $result,
                                                                'params' => $exeResult),
                                                'type' => CMD_TYPE_SC,
                                                'version' => $versionConfig,
                                                'sign' => $sign));

        _writeCommandLog(json_encode(array("in" => $_REQUEST,
				                           "out" => $result)));

		//应答命令格式
		//{"smartparking":{"data":"业务数据","type":"类型","version":"版本号","sign":"签名"}}
		//{"smartparking":{"data":{"cmd":"命令名称", "params":"命令参数"},"type":"类型","version":"版本号","sign":"签名"}}
	}
	else {
        //TODO:错误处理
        $cmd = 'unknow';
        $result = CMD_RES_ERR_PARAMS;

        //命令建立
        $result = array('smartparking' => array('data' => array('cmd' => $cmd,
                                                                'result' => $result),
                                                'type' => CMD_TYPE_SC,
                                                'version' => $versionConfig,
                                                'sign' => $sign));
	}
	
	echo json_encode($result);
}

function _writeAccessLog($log) {
	$date = date('Ymd', time());
	
	$logPath = $GLOBALS['config']['log']['user'];
	if (isset($logPath) && !empty($logPath)) {
		$path = $logPath . '/' . $date .'/';
		if (!is_dir($path)) {
			mkdir($path, 0777);
		}
		$filePath = $path . 'access.log';
		$time = date('Y-m-d H:i:s',time());
		
		$contents = $_SERVER["REMOTE_ADDR"] . ', ' . $time . ', ' . $log;
		$putResult = file_put_contents($filePath,
				print_r($contents, TRUE),
				FILE_APPEND);
	}
	else {
		//没有日志定义
	}
}

function _writeCommandLog($log) {
	$date = date('Ymd', time());

	$logPath = $GLOBALS['config']['log']['user'];
	if (isset($logPath) && !empty($logPath)) {
		$path = $logPath . '/' . $date .'/';
		if (!is_dir($path)) {
			mkdir($path, 0777);
		}
		$filePath = $path . 'command.log';
		$time = date('Y-m-d H:i:s',time());

		$contents = $_SERVER["REMOTE_ADDR"] . ', ' . $time . ', ' . $log . '\r\n';
		$putResult = file_put_contents($filePath,
				print_r($contents, TRUE),
				FILE_APPEND);
	}
	else {
		//没有日志定义
	}
}

?>
