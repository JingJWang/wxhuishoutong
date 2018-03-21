<?php
/**
 * 商城贵金属 黄金商品
 */
header("Content-type:text/html;charset=utf-8");
class metal extends  CI_Controller{
    
    
    /**
     * 贵金属商品详情
     */
    function goodsGold(){
        //校验是否登录
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserCheck(2,$_SESSION);
        //读取黄金商品内容
        $this->load->model('shop/metal_model');
        $info=$this->metal_model->metalGoodsInfo();
        if($info == false){
            Universal::Output($this->config->item('request_fall'),'没有获取商品信息!');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$info);
        }
    }
    /**
     * 添加贵金属订单
     */
    function subGoodsOrder(){ 
        //校验当前是否是交易时间
        $requrl='http://wx.recytl.com/view/gold/dealinfo.html?id=657';
        if($_SERVER['HTTP_REFERER'] == $requrl && ( date('w') == 6 || date('w') == 0) ){
             Universal::Output($this->config->item('request_fall'),'当前时间不能交易!');
        } 
        //校验是否登录
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserCheck(2,$_SESSION);
        $data=$this->input->post();
        $option=$this->checkMetalData($data);
        //计算订单价格
        $order=$this->goodsInfo($option);
        $this->load->model('shop/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->type=$option['dealtype'];
        $content=array('info'=>$order['name'] . $option['weight'] .'克','weight'=> $option['weight']);
        $this->metal_model->content=json_encode($content);
        $this->metal_model->name=$order['name'];
        $this->metal_model->number=Universal::create_ordrenumber();
        $this->metal_model->goodsid=$option['id'];
        $this->metal_model->price=$order['total']*100;
        $info=$this->metal_model->savelMetalOrder();
        if($info == false){
            Universal::Output($this->config->item('request_fall'),'提交订单出现异常!');
        }else{
            Universal::Output($this->config->item('request_succ'),'','/view/gold/paygoods.html?id='.$info);
        }
    }
    /**
     * 校验提交订单时的参数
     */
    function checkMetalData($data){
        $option=array();
        if(!is_numeric($data['id'])){
            Universal::Output($this->config->item('request_fall'),'请选择需要购买的商品!');
        }
        $option['id']=$data['id'];
        if(!is_numeric($data['type'])){
            Universal::Output($this->config->item('request_fall'),'请选择交易方式!');
        }
        $option['dealtype']=$data['type'];
        if(!is_numeric($data['weight']) || $data['weight'] < 0){
            Universal::Output($this->config->item('request_fall'),'请选择购买的商品重量!');
        }
        if(preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $data['weight']) === false){
            Universal::Output($this->config->item('request_fall'),'买有获取到卖出的重量!');
        }
        $option['weight']=$data['weight'];
        return $option;
    }
    /**
     * 读取商品详情 计算订单价格
     */
    function goodsInfo($option){
        $resp=array();
        $this->load->model('shop/metal_model');
         $this->metal_model->id=$option['id'];
        $info=$this->metal_model->metalGoodsInfo();
        if($info === false){
            Universal::Output($this->config->item('request_fall'),'请选择需要购买的商品!');
        }
        $resp['name']=$info['name'];
        $total=$info['pri']*$option['weight'];
        if(!is_numeric($total)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单价格!');
        }
        $resp['total']=$total;
        return $resp;
    }
    /**
     * 读取订单内容 用户保存的地址
     */
    function recordInfo(){
        //校验是否登录
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserCheck(2,$_SESSION);
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到相应的产品内容!');
        }
        $this->load->model('shop/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->id=$id;
        $info=$this->metal_model->recordInfo();
        if($info == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到相应的订单详细内容!');
        }
        $info['content']=json_decode($info['content'],true);
        $info['price']=$info['price']/100;
        $info['dealtype']=$info['dealtype'] == 1 ? '库存' : '现货';
        $resp['goods']=$info;
        //读取相应用户预留的地址
        $addres=$this->metal_model->useraddres();
        $resp['addres']=empty($addres) ? 0 : $addres;
        Universal::Output($this->config->item('request_succ'),'','',$resp);
    }
    /**
     * 获取当前用户的余额
     */
    function userInfo(){
        //校验是否登录
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserCheck(2,$_SESSION);
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单编号!');
        }
        //查询用户的余额
        $this->load->model('nonstandard/wxuser_model');
        $response=$this->wxuser_model->GetBalance($_SESSION['userinfo']['user_id']);
        if($response !== false){
            $balance=$response['balance'];
        }else{
            $balance=0;
        }
        $this->load->model('shop/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->id=$id;
        $info=$this->metal_model->recordInfo();
        if($info == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到相应的订单详细内容!');
        }
        $info['price']=$info['price']/100;
        if($info['price'] > $balance){
            $resp=0;
        }else{
            $resp=$balance;
        }
        Universal::Output($this->config->item('request_succ'),'','',$resp);
    }
    /**
     * 支付方式
     */
    
    function orderPay(){  
        //校验是否登录
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验提交数据
        $request=$this->input->post();        
        $payinfo=$this->checkPayData($request);
        //支付方式
        switch ($payinfo['paytype']){
            //微信
            case '1':
                $option=$this->orderInfo($payinfo);
                $this->wxjsApiPay($option);
                break;
            case '2':
                
                break;
            //支付宝    
            case '3':  
                $option=$this->orderInfo($payinfo);
                $this->zfbPay($option);       
                break;
            //余额
            case '4':
                $option=$this->orderInfo($payinfo);
                $this->balancePay($option);
                break;
            //网银
            case '5':
                
                break;
            
        }
    }
    /**
     * 校验支付参数
     * 
     */
    function checkPayData($option){
        $resp=array();
        //校验订单ID
        if(!is_numeric($option['id'])){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单编号!');
        }
        $resp['orderid']=$option['id'];
        if(!is_numeric($option['addres'])){
            Universal::Output($this->config->item('request_fall'),'没有获取到地址信息!');
        }
        $resp['addres']=$option['addres'];
        if(!is_numeric($option['paytype'])){
            Universal::Output($this->config->item('request_fall'),'没有获取到支付方式!');
        }
        $resp['paytype']=$option['paytype'];
        if(!empty($option['make'])){
            $resp['make']='';
        }else{
            $resp['make']=Universal::safe_replace($option['make']);
        }
        return $resp;        
    }
    /**
     * 读取订单详细内容
     */
    function orderInfo($option){
        $this->load->model('shop/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->id=$option['orderid'];
        $info=$this->metal_model->recordInfo();
        if($info == false){
            Universal::Output($this->config->item('request_fall'),'没有获取订单详细内容!');
        }
        switch ($option['paytype']){
            case '1':
                $resp=array('openid'=>$_SESSION['userinfo']['Login_openid'],
                        'orderid'=>$info['number'],'pri'=>$info['price'],'body'=>$info['name'],
                'url'=>'http://wx.recytl.com/callback/metal.php','attach'=>$info['number']);
                break;
            case '3':
                $resp=array('timeout_express'=>'1m',
                        'out_trade_no'=>$info['number'],'total_amoun'=>$info['price'],'subject'=>$info['name'],
                        'url'=>'http://wx.recytl.com/callback/metal.php','body '=>$info['number']);
                break;
            case '4':
                $resp=array('number'=>$info['number'],'price'=>$info['price'],'userid'=>$_SESSION['userinfo']['user_id']);
                break;
        }
        return $resp;
    }  
    /**
     * 使用余额支付
     */  
    function balancePay($option){
        $this->load->model('shop/metal_model');
        $this->metal_model->userid=$option['userid'];
        $this->metal_model->number=$option['number'];
        $this->metal_model->price=$option['price'];
        $info=$this->metal_model->payBalance();
        if($info){            
            if(empty($this->metal_model->orderid)){
                $url='/index.php/nonstandard/center/ViewCenter';
            }else{
                $url='/view/shop/details.html?id='.$this->metal_model->orderid;
            }
            Universal::Output($this->config->item('request_succ'),'余额支付成功!',$url);
        }else{
            Universal::Output($this->config->item('request_fall'),'余额支付失败!');
        }
    }  
    /**
     * 微信支付回调地址处理微信支付
     */
    function wxPayCallback(){  
        $number=$this->input->post('number',true);
        if(!is_numeric($number)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单ID!');
        }     
        //读取订单信息
        $this->load->model('shop/metal_model');
        $this->metal_model->number=$number;
        $orderinfo=$this->metal_model->numberRecordInfo();
        if($orderinfo === false){
            Universal::Output($this->config->item('request_fall'),'读取订单内容出现异常!');
        }
        if($orderinfo['status'] != 0){
            Universal::Output($this->config->item('request_fall'),'订单状态不正确!');
        }
        //查询微信账单确定支付结果
        $this->load->library('wxpay/jspay');
        $this->jspay->userid=$orderinfo['userid'];
        $this->jspay->number=$number;
        $this->jspay->recordid=$orderinfo['id'];
        $payres=$this->jspay->query();
        if($payres === false){
            Universal::Output($this->config->item('request_fall'),'查询支付结果出现异常!');
        }
        $paydata=$this->jspay->paydata;
        //保存支付记录 并且处理支付结果
        $this->load->model('shop/metal_model');
        $this->metal_model->orderinfo=$orderinfo;
        $this->metal_model->payinfo=$paydata;
        $info=$this->metal_model->payWx();
        if($info){
            Universal::Output($this->config->item('request_succ'),'处理微信支付结果已完成!');
        }else{
            Universal::Output($this->config->item('request_fall'),'处理微信支付结果出现异常!');
        }  
    }
    /**
     * 支付宝支付回调方法
     */
    function zfbPayCallback(){
        $data=$this->input->post();
        if(empty($data)){
            exit();
        }
        //根据订单编号查询支付账单订单状态
        $this->load->library('zhifubao/zhifubao.php');
        //支付宝配置文件
        $config=$this->config->item('zhifubao_attr');
        $this->zhifubao->config=$config;
        //out_trade_no 商户订单编号
        $this->zhifubao->out_trade_no=$data['out_trade_no'];
        //trade_no 支付宝交易号
        $this->zhifubao->trade_no=$data['trade_no'];
        $result=$this->zhifubao->queryPay();
        //校验支付宝传递的支付结果
        if($result->alipay_trade_query_response->code == 10000){
            $time=$result->alipay_trade_query_response->send_pay_date;
            $temptime=strtotime($time);
            $income_time=date('YmdHis',$temptime);
            $oprion=array(
                            'income_userid'=>'',//用户id
                            'income_orderid'=>'',
                            'income_type'=>'3',//付款类型
                            'income_number'=>$result->alipay_trade_query_response->out_trade_no,//支付编号
                            'income_payid'=>$result->alipay_trade_query_response->trade_no,//商户流水号
                            'income_totalfee'=>$result->alipay_trade_query_response->total_amount*100,//付款金额
                            'income_time'=>$income_time,
                            'income_jointime'=>time(),//付款时间
                            'income_result'=>1
            );
        }else{
            Universal::Output($this->config->item('request_fall'),'查询支付账单出现异常!');
        }
        //读取订单信息
        $this->load->model('shop/metal_model');
        $this->metal_model->number=$oprion['income_number'];
        $orderinfo=$this->metal_model->numberRecordInfo();
        if($orderinfo === false){
            Universal::Output($this->config->item('request_fall'),'读取订单内容出现异常!');
        }
        if($orderinfo['status'] != 0){
            Universal::Output($this->config->item('request_fall'),'订单状态不正确!');
        }
        $oprion['income_userid']=$orderinfo['userid'];
        $oprion['income_orderid']=$orderinfo['id'];
        //保存支付记录 并且处理支付结果
        $this->load->model('shop/metal_model');
        $this->metal_model->orderinfo=$orderinfo;
        $this->metal_model->payinfo=$oprion;
        $info=$this->metal_model->payZfb();
        if($info){
            Universal::Output($this->config->item('request_succ'),'处理支付宝支付结果已完成!');
        }else{
            Universal::Output($this->config->item('request_fall'),'处理支付宝支付结果出现异常!');
        }        
    }
    /**
     * 支付宝移动端支付
     */
    function zfbPay($option) {
        $this->load->library('zhifubao/zhifubao.php');
        //支付宝订单号 必填
        $this->zhifubao->out_trade_no=$option['out_trade_no'];
        //订单名称呢过
        $this->zhifubao->subject='回收通-'.$option['subject'];
        //订单金额
        $this->zhifubao->total_amount=$option['total_amoun']/100;
        //订单描述
        $this->zhifubao->body='';
        //是否返回内容
        $this->zhifubao->return='1';
        //订单超时时间
        $this->zhifubao->timeout_express='1m';
        //支付宝配置信息
        $config=$this->config->item('zhifubao_attr');
        $config['notify_url']='http://wx.recytl.com/index.php/shop/metal/zfbPayCallback';
        $config['return_url']='http://wx.recytl.com/index.php/nonstandard/center/ViewCenter';
        $this->zhifubao->config=$config;
        //调用支付宝sdk支付方法
        $info=$this->zhifubao->pay();
        Universal::Output($this->config->item('request_succ'),'','',$info);
    }
    /**
     * 微信JsApi支付
     */
    function wxjsApiPay($option){  
        $this->load->library('wxpay/jspay');
        //微信公众号openid
        $this->jspay->openid=$option['openid'];
        //订单编号
        $this->jspay->orderid=$option['orderid'];
        //订单金额
        $this->jspay->pri=$option['pri'];
        //订单内容
        $this->jspay->body=$option['body'];
        //回调地址
        $this->jspay->url=$option['url'];
        //扩展数据
        $this->jspay->attach=$option['attach'];
        //调用微信sdk jsapi 支付方案
        $wxPayInfo=$this->jspay->getJsApiInfo();
        if($wxPayInfo === false){
            Universal::Output($this->config->item('request_fall'),'微信支付出现异常!');
        }
        $this->wxJsPayInfo($wxPayInfo);
    }
    /**
     * 微信JsApi支付 js配置
     * 当成功获取微信Js支付配置信息调用此方法 输出到支付页面 可唤醒微信支付方法
     */
    function wxJsPayInfo($wxPayInfo){
        $resp='<script type="text/javascript">
                	    function jsApiCall(){
                    		 WeixinJSBridge.invoke(
                    			"getBrandWCPayRequest",
                    			'.$wxPayInfo.',
                    			function(res){
                    				window.location.href="/index.php/nonstandard/center/ViewCenter";
                    			}
                    		);
                	    }
                    	function callpay(){
                    		if (typeof WeixinJSBridge == "undefined"){
                    		    if( document.addEventListener ){
                    		        document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);
                    		    }else if (document.attachEvent){
                    		        document.attachEvent("WeixinJSBridgeReady", jsApiCall);
                    		        document.attachEvent("onWeixinJSBridgeReady", jsApiCall);
                    		    }
                    		}else{
                    		    jsApiCall();
                    		}
                    	}
                	</script>';
        Universal::Output($this->config->item('request_succ'),'','',$resp);
    }
    /**
     * 
     */
    function reckonPri(){
        
    }
}