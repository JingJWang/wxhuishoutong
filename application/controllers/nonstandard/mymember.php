<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
/**
 * 体验会员入口
 * @author sun
 *
 */
 class Mymember extends CI_Controller {
 	/**
 	 * 获取体验会员权限
 	 */
 	function getMymember(){
 		$tel=$this->input->post('phone');
 		//判断是否是电话号码
 		if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$tel)){
 			Universal::Output($this->config->item('request_fall'),'手机格式不对','','');
 		} 
 		$this->load->model('nonstandard/mymember_model');
 		$this->mymember_model->mobile = $tel;
 		$result=$this->mymember_model->getMemberStatus();
 		if($result==1){
 			Universal::Output($this->config->item('request_fall'),'您已经是会员了领取失败');
 		}else if($result==2){
 			Universal::Output($this->config->item('request_succ'),'领取成功!','$result');
 		}else{
 			Universal::Output($this->config->item('request_fall'),'该手机号码没有注册账户');
 		} 
 	}
 	/*
 	 * 获取用户的会员信息具体情况
 	 */
 	function getmemberDeta(){
 		$tel=$this->input->post('phone');
 		$this->load->model('nonstandard/mymember_model');
 		$this->mymember_model->mobile = $tel;
 		$result=$this->mymember_model->getMemberStatus();
 		if($result==1){
 			Universal::Output($this->config->item('request_fall'),'您已经是会员了领取失败');
 		}else if($result==2){
 			Universal::Output($this->config->item('request_succ'),'领取成功!','$result');
 		}else{
 			Universal::Output($this->config->item('request_fall'),'该手机号码没有注册账户');
 		}
 	}
 }
?>