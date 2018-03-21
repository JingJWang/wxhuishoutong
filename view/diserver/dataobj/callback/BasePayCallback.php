<?php 
/**
 * @brief   命令基础类定义文件。
 * @author  赵一
 * @date    2016/02/19
 * @version 0.1
 */

//include_once '../module/order/Order.php';
//include_once '../module/order/OrderCache.php';

include_once '../../config.inc.php';
include_once '../../www/json/PathConfig.php';

/**
 * @class  Command
 * @brief  命令基础类。
 * @author 赵一
 */
class BasePayCallback {
    /**
     * @brief  构造函数。
     * @author 赵一
     */
    public function __construct() {
    }

    /**
     * @brief     执行函数。
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    public function execute() {
    	
    }
    
    /**
     * @brief     执行函数。
     * @author    赵一
     * @param[in] $openid  用户id。
     * @param[in] $amount  总金额。
     * @param[in] $free    现金券金额。
     * @param[in] $type    货币种类。
     * @param[in] $orderId 支付订单号。
     * @param[in] $tradeId 商户订单号。
     * @param[in] $channel 渠道。
     * @param[in] $time    支付完成时间。
     * @return    执行结果
     */
    protected function _addOrder($openid, $amount, $free, $type, $orderId, $tradeId, $channel, $time) {
    	include_once PATH_DATAOBJ_MODULE_ORDER . 'Order.php';
    	$orderMgr = new Order();
    	$orderMgr->add($openid, $amount, $free, $type, $orderId, $tradeId, $channel, $time);
/*
    	include_once PATH_DATAOBJ_MODULE_ORDER . 'OrderCache.php';
    	$orderCacheMgr = new OrderCache();
    	$orderCacheMgr->add($openid, $amount, $free, $type, $orderId, $tradeId, $channel, $time);
*/
    }

    protected function _writePayLog($log) {
    	$date = date('Ymd', time());

    	$logPath = $GLOBALS['config']['log']['user'];
    	if (isset($logPath) && !empty($logPath)) {
    		$path = $logPath . '/' . $date .'/';
    		if (!is_dir($path)) {
    			mkdir($path, 0777);
    		}
    		$filePath = $path . 'pay.log';
    		$time = date('Y-m-d H:i:s',time());

    		$contents = $log . '\r\n';
    		$putResult = file_put_contents($filePath,
    				print_r($contents, TRUE),
    				FILE_APPEND);
    	}
    	else {
    		//没有日志定义
    	}
    }
}

?>
