<?php 
/**
 * @brief   取得微信信息命令类定义文件。
 * @author  赵一
 * @date    2016/04/07
 * @version 0.1
 */

/**
 * @class  GetKeyByWX
 * @brief  取得微信信息命令类。
 * @author 赵一
 */
class GetKeyByWX extends BaseCommand {
    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
    	$res = self::CMD_RES_OK;
    	//检查参数信息
    	if (!isset($params['url'])) {
    		//参数错误
    		$res = self::CMD_RES_ERR_PARAMS;
    	}
    	else {
    		$url = $params['url'];
	    	include_once PATH_DATAOBJ_SDK_WX . 'jssdk.php';
	    	$jssdk = new JSSDK($GLOBALS['config']['key']['wx']['id'], $GLOBALS['config']['key']['wx']['secret']);
	    	$signPackage = $jssdk->getSignPackage($url);

	    	$result['signPackage'] = $signPackage;
    	}

    	//返回值设置
    	$result['result'] = $res;

    	return $result;
    }
}

?>
