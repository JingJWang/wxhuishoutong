<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
ignore_user_abort(true);set_time_limit(0);
class Wxpush extends CI_Controller {
	/**
	 * 获取用户上个月的奖金利润
	 */
	function getLastBonus(){
		$this->load->model('weixin/wxpush_model');
		$result=$this->wxpush_model->getLastBonus();
		if($result){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 会员到期提醒
	 */
	function getRemind(){
		$this->load->model('weixin/wxpush_model');
		$result=$this->wxpush_model->getRemind();
		if($result){
		 	 return true;
		}else{
			return false;
		}
	}
}
?>