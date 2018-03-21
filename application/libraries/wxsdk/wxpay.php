<?php
include 'lib/WxPayApi.php';
include "unit/WxPayJsApiPay.php";
include 'unit/log.php';
class  Wxpay{

    
    /**
     * 微信统一下单
     * @param    string   body          订单内容       参数必填
     * @param    float    moeny         订单金额       参数必填
     * @param    int      orderid       订单编号       参数必填
     * @param    string   pro_id        订单编号       参数必填   当支付方式为NATIVE
     * @param    string   type          支付方式       参数必填   JSAPI NATIVE APP
     * @return array|成功时返回，其他抛异常|Exception
     */
    function create_order($orderinfo){ 
        //$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'wxpay'.'.log');
        //$log = Log::Init($logHandler, 15);
        //统一下单
        $input = new WxPayUnifiedOrder();
        if(empty($orderinfo['body'])){
            return false;
        }
        //订单 说明
        $input->SetBody($orderinfo['body']);
        //校验订单编号 是否为空 类型是否正确
        if(!is_numeric($orderinfo['orderid'])){
            return  false;
        }  
        //订单编号
        $input->SetAttach($orderinfo['orderid']);   
        //微信订单号     
        $input->SetOut_trade_no($orderinfo['orderid']);
        //检验订单金额 是否为空 类型是否正确
        if(empty($orderinfo['moeny'])){
            return false;
        }
        //校验金额是否是 float
        if(is_float($orderinfo['moeny'])){
            $format=explode('.',$orderinfo['moeny']);
            if(strlen($format['1']) > 2){
                return false;
            }
        }else{
            
            if(!is_numeric($orderinfo['moeny'])){
                return false;
            }
        }      
        //校验金额是否时整数
        //金额
        $input->SetTotal_fee($orderinfo['moeny']*100);
        //交易开始时间
        $input->SetTime_start(date("YmdHis"));
        //交易结束时间
        $input->SetTime_expire(date("YmdHis", time() + 600));  
        //$input->SetGoods_tag();
        $input->SetNotify_url($orderinfo['notifyurl']);
        //校验支付方式  当支付方式为       NATIVE 扫码支付的时候 该参数必填
        if(!in_array($orderinfo['type'],array('APP','JSAPI','NATIVE'))){
            return false;
        }
        //验证 支付类型
        if($orderinfo['type']  == 'NATIVE'){
            if(empty($orderinfo['pro_id'])){
                return  false;
            }
            if(!is_numeric($orderinfo['pro_id'])){
                return false;
            }
            $input->SetProduct_id($orderinfo['pro_id']);
        }
        //支付类型
        $input->SetTrade_type($orderinfo['type']);
        //微信openid 当支付方式为jspai 支付  该参数为必填
        if($orderinfo['type'] === 'JSAPI'){
            $input->SetOpenid($orderinfo['openid']);
        }
        try {
            //获取文件订单信息
            $reponse = WxPayApi::unifiedOrder($input);
            if($orderinfo['type'] == 'APP'){               
                $reponse=$this->GetAPPOption($reponse['prepay_id']);
                return $reponse;
            }
            return $reponse;            
        } catch (Exception $e) {
            return  false;
        }
    }
    /**
     * 微信支付 二维码支付
     * @param    string    type   支付方式   JSAPI，NATIVE，APP
     */
    public function code($filename,$orderinfo){
        $info=$this->create_order($orderinfo);
        if ($info['return_code']=='FAIL'||$info===false) {
            return false;
        }
        include_once 'unit/phpqrcode/phpqrcode.php';
        $url = urldecode($info['code_url']);
        QRcode::png($url,'qrcode/goods/'.$filename.'.jpg');
        return true;
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
    /**
     * 查询订单是否支付成功
     * @param    int   out_trade_no   订单编号 
     * @return array|成功时返回，其他抛异常|Exception
     */
    function  order_query($out_trade_no){
        if(empty($out_trade_no)){
         return false;   
        }
        $input = new WxPayOrderQuery();
        $input->SetOut_trade_no($out_trade_no);
        $response=WxPayApi::orderQuery($input);
        if($response['result_code'] == 'SUCCESS' && $response['return_code'] == 'SUCCESS'){
            if($response['trade_state'] == 'SUCCESS'){
                return $response;
            }
        }
        return false;
    }
    /**
     * APP支付方式  二次签名
     */
     function GetAPPOption($id){
        $reponse['appid']=WxPayConfig::APPID;
        $reponse['partnerid']=WxPayConfig::MCHID;
        $reponse['package']='Sign=WXPay';
        $reponse['noncestr']=WxPayApi::getNonceStr();
        $reponse['timestamp']=time();
        $reponse['prepayid']=$id;
        $reponse['sign']=$this->MakeSign($reponse);
        return $reponse;
    }
    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($info)
    {
        $buff = "";
        foreach ($info as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
    
        $buff = trim($buff, "&");
        return $buff;
    }    
    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign($info)
    {
        //签名步骤一：按字典序排序参数
        ksort($info);
        $string = $this->ToUrlParams($info);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".WxPayConfig::KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
}