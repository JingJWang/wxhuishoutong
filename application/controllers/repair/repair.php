<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
/**
 * 手机维修
 * @author sun
 *
*/
class Repair extends CI_Controller {
	/**
	 * 电子产品 分类下的品牌列表
	 * @param   int      id       分类id
	 * @param   int      typeid   类型id
	 * @return  string
	 */
	function  brand(){
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		$this->load->model('repair/repair_model');
		$respone=$this->repair_model->types_brandslist();
		if($respone !== false){
			Universal::Output($this->config->item('request_succ'),'','',$respone);
		}
		Universal::Output($this->config->item('request_fall'),'请稍后,本次请求数据出现异常!');
	}
	/**
     * 电子产品 -品牌下 型号列表
     * @param   int     brandid  型号列表
     * @param   int     page     页码
     * @return  string    
     */
    function type(){
        $brandid=$this->input->post('id',true);
        if(empty($brandid) || !is_numeric($brandid) || !isset($brandid)){
            Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
        }
        $this->load->model('repair/repair_model');
        $this->repair_model->brandid=$brandid;
        $respone=$this->repair_model->brands_typeslist();
        if($respone !== false){
            Universal::Output($this->config->item('request_succ'),'','',$respone);
        }
        Universal::Output($this->config->item('request_fall'),'请稍后,本次请求数据出现异常!');
    }
	/**
	 * 获取手机故障选项
	 */
	function getRepairList(){
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		$data=$this->input->post();
		if(empty($data['id']) || !is_numeric($data['id']) || !isset($data['id'])){
			Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->mobile=$data['id'];
		$this->repair_model->userid=$_SESSION['userinfo']['user_id'];
		$response=$this->repair_model->getRepairList();
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		}
	}
	/**
	 * 保存用户输入的手机维修记录
	 */
	function saveRepairs(){
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		$data=$this->input->post();
		if(empty($data['id']) || !is_numeric($data['id']) || !isset($data['id'])){
			Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		if(empty($data['bonus']) || !is_numeric($data['bonus']) || !isset($data['bonus'])){
		    Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		if(empty($data['estprice']) || !is_numeric($data['estprice']) || !isset($data['estprice'])){
		    Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		$conn=Array();
		if(!empty($data['second'])){
		   $conn= array_filter(explode(';',$data['second']));
		}
		$con=Array();
		foreach ($conn as $key=>$val){
		    $con[]=explode(':',$val);
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->goodsid=$data['id'];
		$this->repair_model->goodsname=$data['gooodsname'];
		$this->repair_model->phone=$data['phone'];
		$this->repair_model->name=$data['name'];
		$this->repair_model->address=$data['address'];
		$this->repair_model->wxid=$_SESSION['userinfo']['user_id'];
		$this->repair_model->contentid=json_encode($con);
		$this->repair_model->content=$data['content'];
		$this->repair_model->discount=$data['bonus'];
		$this->repair_model->money=$data['estprice'];
		$this->repair_model->other=$data['other'];
		$response=$this->repair_model->saveRepairPhone();
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		} 
	}
	/**
	 * 获取维修订单记录
	 */
	function selectList(){
		$status=$this->input->post('status',true);
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		if(!isset($status) || !is_numeric($status)){
			Universal::Output($this->config->item('request_fall'),'本次请求违法!');
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->wxid=$_SESSION['userinfo']['user_id'];
		if($status!=''){
			$this->repair_model->status=$status;
		}
		$response=$this->repair_model->selectList();
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		}
	}
	/**
	 * 获取维修记录某一单详情
	 */
	function selectDetail(){
		$id=$this->input->post('id');
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		if(empty($id) || !is_numeric($id) || !isset($id)){
			Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->id=$id;
		$this->repair_model->wxid=$_SESSION['userinfo']['user_id'];
		$response=$this->repair_model->selectDetail();
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		}
	}
	/**
	 * 手机维修取消订单
	 */
	function cancelOrder(){
		$id=$this->input->post('id',true);
		if(empty($id) || !is_numeric($id) || !isset($id)){
			Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		$money=$this->input->post('money',true);
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->id=$id;
		$result=$this->repair_model->selectDetail();
		$this->repair_model->money=$result['0']['bonus'];
		$response=$this->repair_model->cancelOrder();
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		}
	}
	/**
	 * 手机维修添加快递单号
	 */
	function saveOdd(){
		$data=$this->input->post();
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->id=$data['id'];
		$this->repair_model->express=$data['express'];
		$this->repair_model->num=$data['num'];
		$response=$this->repair_model->saveOdd();
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		}
	}
	/**
	 * 订单支付
	 */
	function orderPay(){
		//判断用户是否已经登录
		if (!isset($_SESSION['userinfo']['user_id'])&&!isset($_SESSION['userinfo']['user_mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
		}
		$data=$this->input->post();
		if(empty($data['id']) || !is_numeric($data['id']) || !isset($data['id'])){
			Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
		}
		$this->load->model('repair/repair_model');
		$this->repair_model->id=$data['id'];
		$result=$this->repair_model->selectDetail();
		$this->orderInfo($result);
	}
	/**
	 * 微信JsApi支付
	 */
	function orderInfo($option){
		$this->load->library('wxpay/jspay');
		$jspay=new jspay();
		//微信公众号openid
		$jspay->openid=$_SESSION['userinfo']['user_openid'];
		//$jspay->openid='o9nlJt2dHqi7vsNZKmPrXE5sAIz8';
		//订单编号
		$jspay->orderid=$option['0']['orderid'];
		//订单金额
		$jspay->pri=intval($option['0']['money']);
		//订单内容
		$jspay->body=$option['0']['goodsname'].'手机维修';
		//回调地址
		$jspay->url='http://wx.recytl.com/callback/repair.php';
		//扩展数据
		$jspay->attach=$option['0']['orderid'];
		$info=$jspay->getJsApiInfo();
		$number=$option['0']['orderid'];
		if(!$info){
			Universal::Output($this->config->item('request_fall'),'微信支付出现异常!');
		}
		$this->wxJsPayInfo($info,$number);
	}
	function wxJsPayInfo($info,$number){
			$resp='<script type="text/javascript">
                	    function jsApiCall(){
                    		 WeixinJSBridge.invoke(
                    			"getBrandWCPayRequest",
                    			'.$info.',
                    			function(res){
                    				if(res.err_msg == "get_brand_wcpay_request:ok"){
                    					$.ajax({
	                    					 type: "POST",
											 url: "http://wx.recytl.com/index.php/repair/repair/wxPayCallback",
											 data:"number='.$number.'",
											 dataType:"json",
											 beforeSend: function(){},
											 success: function(data){
												 var response=eval(data);
											 	 if(response["status"]==request_succ){
											 		UrlGoto("http://wx.recytl.com/view/repair/repairform.html");
												 }else{
											 		UrlGoto("http://wx.recytl.com/view/repair/repairform.html");
												 }
											},
										    complete :function(XMLHttpRequest, textStatus){},
											error:function(XMLHttpRequest,textStatus,errorThrown){
											 		UrlGoto("http://wx.recytl.com/view/repair/repairform.html");
											}
										})
									}
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
	 * 微信支付回调地址处理微信支付
	 */
	function wxPayCallback(){
		//$number='20170714975350558631';
		$number=$this->input->post('number',true);
		if(!is_numeric($number)){
			Universal::Output($this->config->item('request_fall'),'没有获取到订单ID!');
		}
		//读取订单信息
		$this->load->model('repair/repair_model');
		$this->repair_model->orderid=$number;
		$orderinfo=$this->repair_model->selectDetail();
		if($orderinfo === false){
			Universal::Output($this->config->item('request_fall'),'读取订单内容出现异常!');
		}
		//保存支付记录 并且处理支付结果
		$this->load->model('repair/repair_model');
		$this->repair_model->orderinfo=$orderinfo;
		$info=$this->repair_model->payWx();
		if($info){
			Universal::Output($this->config->item('request_succ'),'处理微信支付结果已完成!');
		}else{
			Universal::Output($this->config->item('request_fall'),'处理微信支付结果出现异常!');
		}
	}
	// 取消订单 修改订单状态
	/* function cancleoOrder(){
		$id=(int)$this->input->post('id',true);
		if(!isset($id) || !is_numeric($id) || ($id<0)){
			Universal::Output($this->config->item('request_fall'),'本次请求违法!');
		}else{
			$this->load->model('repair/repair_model');
			$this->repair_model->id=$id;
			$response=$this->repair_model->orderPay();
			Universal::Output($this->config->item('request_succ'),'修改成功','',$response);
		}
	} */
}
?>