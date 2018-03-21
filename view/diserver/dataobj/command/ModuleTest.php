<?php 
/**
 * @brief   模块测试命令类定义文件。
 * @author  赵一
 * @date    2016/03/18
 * @version 0.1
 */

/**
 * @class  ModuleTest
 * @brief  模块测试命令类。
 * @author 赵一
 */
class ModuleTest extends BaseCommand {
    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
    	$res = self::CMD_RES_OK;

    	//最高分值类测试
    	include_once PATH_DATAOBJ_MODULE_ORDER . 'Order.php';
    	$orderMgr = new Order();
    	
    	$openid = '123456';
    	$amount = 100;
    	$free = 50;
    	$type = 'CHN';
    	$orderId = '123';
    	$tradeId = 'tradeId-123';
    	$channel = 'wx';
    	$time = time();
    	
    	$orderMgr->add($openid, $amount, $free, $type, $orderId, $tradeId, $channel, $time);
    	
    	$datas = $orderMgr->get($openid);
    	
    	foreach ($datas as $data) {
    		$orderMgr->del($data['id']);
    	}
    	
    	//返回值设置
    	$result['result'] = $res;

    	return $result;
    }
}

?>
