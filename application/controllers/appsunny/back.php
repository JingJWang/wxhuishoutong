<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type: text/html; charset=utf-8");
class Back extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/back_model');
	}

	/*
	 *  回收商身份审核.
	 */
	function auth(){
		// post 请求
		if ($this->input->server('REQUEST_METHOD') != "POST"){
		    $result = array(
		        'status' => 3000,
		        'msg' => '请求方法必须是post类型',
		    );
			echo json_encode($result);
		    exit();
		}		
		// 获取回收商编号
		$phone = $this->input->post('phone');
		if (!$phone){
		    $result = array(
		        'status' => 3000,
		        'msg' => '回收商手机号不能为空',
		    );
		    echo json_encode($result);
			exit();
		}
		// 验证手机号格式
		$phone_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
		    $phone);
		if ($phone_type == FALSE){
		    $result = array(
		        'status' => 3000,
		        'msg' => '手机号格式不正确',
		    );
		    echo json_encode($result);
		    exit();
		}		
		// 操作者身份验证.
		// 检测回收商身份.
		$get_user_auth = $this->back_model->get_user_auth($phone);
		if ($get_user_auth === FALSE){
		    $result = array(
		        'status' => 3000,
		        'msg' => '用户不存在',
		    );
		    echo json_encode($result);
			exit();
		}
		if ($get_user_auth['userstatus'] == 3){
		    $result = array(
		        'status' => 3000,
		        'msg' => '已经审核过了',
		    );
		    echo json_encode($result);
			exit();
		}
		// 添加回收商的基本信息.
		$update_user_info = $this->back_model->update_user_auth_info($get_user_auth['user_id']);
		// 更新成功
		if ($update_user_info){
		    // 添加消息推送.
		    $this->load->library('vendor/notice');
		    $my_msg = $this->config->item('coop_verify_pass');
		    $this->notice->JPush('alias',array($get_user_auth['user_id']),$my_msg);
		    $result = array(
		        'status' => 1000,
		        'msg' => '审核成功',
		    );
		    echo json_encode($result);
			exit();
		}
		else{
		    $result = array(
		        'status' => 3000,
		        'msg' => '审核失败',
		    );
		    echo json_encode($result);
			exit();
		}
	}
	
	
}
