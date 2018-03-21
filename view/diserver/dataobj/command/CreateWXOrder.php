<?php 
/**
 * @brief   取得信息命令类定义文件。
 * @author  赵一
 * @date    2016/06/02
 * @version 0.1
 */

/**
 * @class  GetInfo
 * @brief  取得信息命令类。
 * @author 赵一
 */
class CreateWXOrder extends BaseCommand {
    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
    	$res = self::CMD_RES_OK;

    	//检查参数信息
    	if (!isset($params['openid']) || !isset($params['desc']) || !isset($params['amount'])) {
    		//参数错误
    		$res = self::CMD_RES_ERR_PARAMS;
    	}
    	else {
    		include_once PATH_DATAOBJ_SDK_WX_PAY . 'WxPayPubHelper.php';
    		$openid = $params['openid'];   //openid
    		$desc = $params['desc'];       //商品描述
    		$amount = $params['amount'];     //商品金额

    		//使用jsapi接口
    		$jsApi = new JsApi_pub();
    		
    		//=========步骤1：网页授权获取用户openid============
    		//通过code获得openid

    		//=========步骤2：使用统一支付接口，获取prepay_id============
    		//使用统一支付接口
    		$unifiedOrder = new UnifiedOrder_pub();
    		
    		//设置统一支付接口参数
    		//设置必填参数
    		//appid已填,商户无需重复填写
    		//mch_id已填,商户无需重复填写
    		//noncestr已填,商户无需重复填写
    		//spbill_create_ip已填,商户无需重复填写
    		//sign已填,商户无需重复填写
    		$unifiedOrder->setParameter("openid", $openid);
    		$unifiedOrder->setParameter("body", $desc);   //商品描述
    		//自定义订单号，此处仅作举例
    		$timeStamp = time();
    		$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
    		$unifiedOrder->setParameter("out_trade_no", "$out_trade_no");//商户订单号
    		$unifiedOrder->setParameter("total_fee", $amount);//总金额
    		$unifiedOrder->setParameter("notify_url", WxPayConf_pub::NOTIFY_URL);//通知地址
    		$unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
    		//非必填参数，商户可根据实际情况选填
    		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
    		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
    		//$unifiedOrder->setParameter("attach","XXXX");//附加数据
    		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
    		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
    		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
    		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
    		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
    		
    		$prepay_id = $unifiedOrder->getPrepayId();
    		//=========步骤3：使用jsapi调起支付============
    		$jsApi->setPrepayId($prepay_id);
    		
    		$jsApiParameters = $jsApi->getParameters();
    		//echo $jsApiParameters;
    		$jsApiParameters = $jsApi->getParametersArray();

    		$result['wxParameters'] = $jsApiParameters;
    	}

    	//返回值设置
    	
    	$result['result'] = $res;

    	return $result;
    }
}

?>
