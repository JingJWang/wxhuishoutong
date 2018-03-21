<?php
include 'sdk/lib/WxPayApi.php';
include "sdk/unit/WxPayJsApiPay.php";
include 'sdk/unit/log.php';
class jspay{    
    function getJsApiInfo(){
        $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'wxpay'.'.log');
        $log = Log::Init($logHandler, 15);
        if(empty($this->openid)){
            return  false;
        }
        if(empty($this->orderid) || !is_numeric($this->orderid)){
            return  false;
        }
        if(empty($this->pri) || !is_numeric($this->pri) ){
            return  false;
        }
        if(empty($this->body)){
            return  false;
        }
        if(empty($this->attach)){
            return  false;
        }
        if(empty($this->url)){
            return false;
        }
        $input = new WxPayUnifiedOrder();
        $input->SetBody($this->body);
        $input->SetAttach($this->attach);
        $input->SetOut_trade_no($this->orderid);
        $input->SetTotal_fee($this->pri);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($this->body);
        $input->SetNotify_url($this->url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($this->openid);
        try {
            $orderinfo = WxPayApi::unifiedOrder($input);
        } catch (Exception $e) {
            return false;// $e;
        }
        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($orderinfo);
        return  $jsApiParameters;
    }
    /**
     * 查询订单是否支付成功
     * @param    int  number 订单编号
     * @return   成功返回
     */
    function query(){
        $input = new WxPayOrderQuery();
        $input->SetOut_trade_no($this->number);
        $response=WxPayApi::orderQuery($input);
        if($response['return_code'] != 'SUCCESS'){
            $this->msg='查询订单支付状态失败!';
            return false;
        }
        if($response['result_code'] != 'SUCCESS'){
            $this->msg='查询订单支付状态失败!';
            return false;
        }
        if($response['result_code'] == 'SUCCESS' 
                && $response['return_code'] == 'SUCCESS'){
            $this->paydata=array(
                    'income_userid'=>$this->userid,//用户id
                    'income_orderid'=>$this->recordid,
                    'income_type'=>'2',//付款类型
                    'income_number'=>$this->number,//支付编号
                    'income_payid'=>$response['transaction_id'],//商户流水号
                    'income_totalfee'=>$response['total_fee'],//付款金额
                    'income_time'=>$response['time_end'],
                    'income_jointime'=>time(),//付款时间
                    'income_result'=>1
            );
            return true;
        }else{
            $this->msg='查询订单支付状态失败!';
            return false;
        }
    }    
}
