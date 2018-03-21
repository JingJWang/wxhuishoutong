<?php
include '../weixindo/model/Mysql.class.php';
require_once "sdk/lib/WxPayApi.php";
require_once 'sdk/unit/log.php';
//初始化日志

/* if(isset($_REQUEST["transaction_id"]) && $_REQUEST["transaction_id"] != ""){
	$transaction_id = $_REQUEST["transaction_id"];
	$input = new WxPayOrderQuery();
	$input->SetTransaction_id($transaction_id);
	printf_info(WxPayApi::orderQuery($input));
	exit();
} */
if(isset($_POST["out_trade_no"]) && $_POST["out_trade_no"] != ""){
	$out_trade_no = $_POST["out_trade_no"];
	$input = new WxPayOrderQuery();
	$input->SetOut_trade_no($out_trade_no);
	$response=WxPayApi::orderQuery($input);
	//jsp支付成功后 根据商户订单号 主动查询该订单是否支付成功
	if($response['result_code'] = 'SUCCESS' &&  $response['attach'] == $_POST['number']){
	           $db =new MySQL();
	           //查询报单用户openid  订单id
	           $sql='select a.order_id,b.wx_openid from h_order_nonstandard  as a left join h_wxuser as b
	                 on a.wx_id=b.wx_id where a.order_number="'.$response['attach'].'"';
	           $result=$db->Query($sql);
	           if(!$result){
	               $message=array('status'=>3000,'msg'=>'没有找到该订单的信息!');
	               echo json_encode($message);exit;
	           }
	           $orderinfo=$db->RecordsArray();
	           //修改订单信息 改为支付成功状态 保存订单支付订单号
	           $ordresql='update  h_order_nonstandard set order_orderstatus=10,order_paynumber="'.
	                   $response['out_trade_no'].'",order_updatetime='.time().' where order_id='.$orderinfo['0']['order_id'];
	           $res_ordre=$db->Query($ordresql);
	           if(!$res_ordre){
	               $message=array('status'=>3000,'msg'=>'更改订单状态失败!');
	               echo json_encode($message);exit;
	           }
	           //给用户支付 订单费用
	           include '../application/libraries/hongbao/packet.php';
	           $packet= new Packet();	           
	           $info=$packet->_route('transfers',array('openid'=>$orderinfo['0']['wx_openid'],'money'=>$response['total_fee']));
	           //当请求 结果  ,支付结果 返回正确时 更新订单记录    记录支付日志
	           if($info->return_code == 'SUCCESS' && $info->result_code == 'SUCCESS'){
    	               $ordresql='update  h_order_nonstandard set order_serialnumber="'.
    	                       $info->payment_no.'",order_updatetime='.time().' where order_id='.$orderinfo['0']['order_id'];
    	               $res_ordre=$db->Query($ordresql);
    	               if(!$res_ordre){
    	                   $message=array('status'=>3000,'msg'=>'更改订单状态失败!');
    	                   echo json_encode($message);exit;
    	               }
    	               $logsql='insert into  h_account_pay(wx_openid,order_id,order_number,
    	                        pay_partner_trade_no,pay_payment_no,pay_amount,pay_return_code,pay_result_code,
    	                        pay_payment_time)value("'.$orderinfo['0']['wx_openid'].'",'.
    	                        $orderinfo['0']['order_id'].',"'.$response['attach'].'","'.
    	                        $info->partner_trade_no.'","'.$info->payment_no.'","'.$response['total_fee'].'","'.
    	                        $info->return_code.'","'.$info->result_code.'","'.$info->payment_time.'")';
    	               $res_log=$db->Query($logsql);
    	               if(!$res_log){
    	                   $message=array('status'=>3000,'msg'=>'系统存储出现异常!');
    	                   echo json_encode($message);exit;
    	               }
    	               $message=array('status'=>1000,'msg'=>'订单支付成功!');
    	               echo json_encode($message);exit;
	           }
	           
	           
	}
	exit();
}
?>