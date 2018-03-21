<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class Couponuser extends CI_Controller{
	/**
	 * 查看用户优惠券信息列表
	 * @author sunbeike
	 * @param mobile 当前用户登录手机号
	 * @return json数据
	 */
	function  getCouponUserList(){
		//校验登录权限
        $this->load->model('auto/userauth_model');
		$this->userauth_model->UserCheck(1,$_SESSION);
		if(isset($this->userauth_model->url)){
			Universal::Output($this->config->item('request_fall'),'您还没有登录,正在跳转到首页自动登录',$this->userauth_model->url,'');
		}
		//加载优惠券
		$this->load->model('coupon/couponuser_model');
		$mobile = $_SESSION['userinfo']['user_mobile'];
		//获取当前用户登录手机号
		//检验传参id 
		if(!is_numeric($mobile)){
			Universal::Output($this->config->item('request_fall'),'参数错误','','');
		}
		$this->couponuser_model->mobile = $mobile;
		$result = $this->couponuser_model->getCouponUserList();
		if ($result===false) {
			Universal::Output($this->config->item('request_fall'),'您还没有优惠券','','');
		}
		Universal::Output($this->config->item('request_succ'),'获取参数成功','view/coupon/market.html',$result);
	}
}
?>