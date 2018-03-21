<?php 
/**
 * @brief   命令基础类定义文件。
 * @author  赵一
 * @date    2016/02/19
 * @version 0.1
 */

include_once './BasePayCallback.php';
include_once PATH_DATAOBJ_SDK_WX_PAY . 'WxPayPubHelper.php';


/**
 * @class  Command
 * @brief  命令基础类。
 * @author 赵一
 */
class WXPayCallback extends BasePayCallback {
    public function execute() {
    	//使用通用通知接口
    	$notify = new Notify_pub();
    	
    	//存储微信的回调
    	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
/*
    	$xml = "<xml><appid><![CDATA[wx22a93144a30cedcf]]></appid>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[200]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[Y]]></is_subscribe>
<mch_id><![CDATA[1335719901]]></mch_id>
<nonce_str><![CDATA[92iiw384y1kz468e5vixc5a396ppktbx]]></nonce_str>
<openid><![CDATA[o48vGvn1rgbauDoFFUa-Iszwjxp4]]></openid>
<out_trade_no><![CDATA[wx22a93144a30cedcf1465833089]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[A4FB4772ACF557F57577425D1F37E6B0]]></sign>
<time_end><![CDATA[20160613235055]]></time_end>
<total_fee>200</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[4006062001201606137225601173]]></transaction_id>
</xml>";
*/

    	$this->_writePayLog($xml);

    	$notify->saveData($xml);

    	//验证签名，并回应微信。
    	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
    	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
    	//尽可能提高通知的成功率，但微信不保证通知最终能成功。
    	if ($notify->checkSign() == FALSE) {
    		$notify->setReturnParameter("return_code","FAIL");//返回状态码
    		$notify->setReturnParameter("return_msg","签名失败");//返回信息
    	}
    	else {
    		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
    	}

    	$returnXml = $notify->returnXml();
    	echo $returnXml;
    	
    	if ($notify->checkSign() == TRUE) {
    		if ($notify->data["return_code"] == "FAIL") {
    			//此处应该更新一下订单状态，商户自行增删操作
//    			$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
    		}
    		else if($notify->data["result_code"] == "FAIL") {
    			//此处应该更新一下订单状态，商户自行增删操作
//    			$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
    		}
    		else{
    			//此处应该更新一下订单状态，商户自行增删操作
//    			$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");
    		}

    		if ($notify->data['return_code'] == 'SUCCESS') {
    			//支付结果
    			if ($notify->data['result_code'] == 'SUCCESS') {
    				//业务成功
    				$appid = $notify->data['appid'];
    				$openid = $notify->data['openid'];
    				$amount = $notify->data['cash_fee'];
    				if (isset($notify->data['coupon_fee']) && !empty($notify->data['coupon_fee'])) {
	    				$free = $notify->data['coupon_fee'];
    				}
    				else {
    					$free = 0;
    				}
    				$type = $notify->data['fee_type'];
    				$orderId = $notify->data['transaction_id'];
    				$tradeId = $notify->data['out_trade_no'];

    				$this->_addOrder($openid, $amount, $free, $type, $orderId, $tradeId, CHANNEL, time());
    			}
    			else {
    				//业务失败
    			}
    		}
    		else {
    			//
    		}
    	}
    }

    const CHANNEL = 'wx';
}

$wxCallback = new WXPayCallback();
$wxCallback->execute();

?>
