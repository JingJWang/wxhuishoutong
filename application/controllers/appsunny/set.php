<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Set extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/reg_model');
		$this->load->model('appsunny/set_model');
	}	

	/************公共类函数***************/	
	/*  
	 * 判断请求方法是否为POST
	 */
	private function method_is_post(){
		if ($this->input->server('REQUEST_METHOD') != "POST"){
			$result = array(
				'status' => $this->config->item('app_req_method_err'),
				'msg' => $this->lang->line('app_req_method_err'),
				'data' => ''
				);
			echo json_encode($result);
		    exit();
		}
	}
	
	/* 
	 *  验证必填参数是否为空
	 *  @param $args boolean 是有空参数.(逻辑与运算, True 没有空参数, False 有空参数)
	 *  @return 
	 */
	private function param_is_null($args){
		if ($args == FALSE){
			$result = array(
				'status' => $this->config->item('app_param_null'),
				'msg' => $this->lang->line('app_param_null'),
				'data' => ''
				);
			echo json_encode($result);
			exit();
		}
	}
	
	/*
	 *  验证参数类型是否正确.
	 *  @param $args boolean 参数类型是否正确.(True 正确, False 错误)
	 *  @return 
	 */
	private function param_type_is_right($args){
		if ($args == FALSE){
			$result = array(
				'status' => $this->config->item('app_param_err'),
				'msg' => $this->lang->line('app_param_err'),
				'data' => ''
				);
			echo json_encode($result);
			exit();	
		}
	}
	
	/*
	 *  更新用户令牌值.
	 *  成功时返回True,失败时返回False.
	 */
	private function update_user_token($user_id){
		$data = array(
			'user_id' => $user_id,
			'access_token' => sha1('maijin'.(string)time().(string)mt_rand(100000,999999)),
		);
        $result = $this->reg_model->update_user_token($data);
		return $result;
	}
	
	/*
	 * 验证令牌是否有效(用户不存在,令牌错误,令牌过期)
	 * 
	 */
	private function verify_token_valid($user_id,$access_token){
		$get_result = $this->reg_model->get_token($user_id);
		// 用户不存在
		if(!$get_result){
			$result = array(
				'status' => $this->config->item('app_user_null'),
				'msg' => $this->lang->line('app_user_null'),
				'data' => ''
				);
			echo json_encode($result);
			exit();				
		}
		// 令牌错误
		if ($access_token != $get_result['access_token']){
			$result = array(
				'status' => $this->config->item('app_user_token_err'),
				'msg' => $this->lang->line('app_user_token_err'),
				'data' => ''
				);
			echo json_encode($result);
			exit();				
		}
		// 令牌过期
		$date_now = time();
		if ($access_token == '-1' || 
		    ($get_result['datetime'] + $this->config->item('cooperator_token_expire')) < $date_now){
			$this->update_user_token($user_id);
			$result = array(
				'status' => $this->config->item('app_user_token_expire'),
				'msg' => $this->lang->line('app_user_token_expire'),
				'data' => ''
				);
			echo json_encode($result);
			exit();	
		}
	}	
	
	/* 
	 *
	 *  功能 : 关于我们.
	 *
	 *
	 */
	public function about(){		
		// 类型1为关于我们
		$type = 1;
		$get_data = $this->set_model->get_content($type);
		if (!$get_data){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->lang->line('app_get_data_fail'),
				'data'=>'',
			);
		}
		else{
		    $get_about = json_decode($get_data,TRUE);
			$result = array(
				'status' => $this->config->item('app_success'),
				'msg' => $this->lang->line('app_success'),
				'data'=>array(
					'about' => $get_about['value'],
				),				
			);
		}
		echo json_encode($result);
		exit();
	}

	/* 
	 *
	 * 功能 : 版本更新.
	 *
	 *
	 */
	 
	public function update(){
		$type = 2;
		$this->load->helper('url');
		$get_data = $this->set_model->get_content($type);
		if (!$get_data){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->lang->line('app_get_data_fail'),
				'data'=>'',
			);
		}
		else{
		    $get_update = json_decode($get_data,TRUE);
			$result = array(
				'status' => $this->config->item('app_success'),
				'msg' => $this->lang->line('app_success'),
				'data' => array(
					'version' => $get_update['key'],
					'url' => base_url($get_update['value']),
				),
			);
		}
		echo json_encode($result);
		exit();
	}		

	/* 
	 *  功能 : 使用指南.
	 *
	 */
	public function guide(){
		// 类型3为使用指南
		$type = 3;
		$get_data = $this->set_model->get_content($type);
		if (!$get_data){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->lang->line('app_get_data_fail'),
				'data'=>'',
			);
		}
		else{
		    $get_guide = json_decode($get_data,TRUE);
			$result = array(
				'status' => $this->config->item('app_success'),
				'msg' => $this->lang->line('app_success'),
				'data'=> array(
					'guide' => $get_guide['value'],
				),				
			);
		}
		echo json_encode($result);
		exit();
	}
	
	/* 
	 * 功能 : 意见反馈.
	 *
	 */
	public function feedback(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token') &&
			$this->input->post('content'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$content = $this->input->post('content');
		// 检测参数长度是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$content_len = (strlen($content) == 0 || strlen($content) > 255);
        if ( $user_id_len || $token_len || $content_len){
			$result = array(
				'status' =>$this->config->item('app_param_illegal'), 
				'msg' => $this->lang->line('app_param_illegal'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();
		}
		// 验证用户身份.
		$this->verify_token_valid($user_id,$access_token);
		// 添加反馈信息到数据库.
		$data = array(
			'user_id' => $user_id,
			'content' => $content,
		);
		$add_result = $this->set_model->add_feedback($data);
		// 添加失败时.输出的信息
		if (!$add_result){
			$result = array(
				'status' =>$this->config->item('app_add_data_fail'), 
				'msg' => $this->lang->line('app_add_data_fail'),
				'data'=>'',				
			);
		}
		// 添加成功时,输出的信息.
		else{
			$result = array(
				'status' =>$this->config->item('app_success'), 
				'msg' => $this->lang->line('app_success'),
				'data'=>'',				
			);			
		}
		echo json_encode($result);
		exit();
	}
	
	/* 功能 : 法律条款.
	 */
	public function law(){
		// 类型5为法律条款
		$type = 5;
		$get_data = $this->set_model->get_content($type);
		if (!$get_data){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->lang->line('app_get_data_fail'),
				'data'=>'',
			);
		}
		else{
		    $get_law = json_decode($get_data,TRUE);
			$result = array(
				'status' => $this->config->item('app_success'),
				'msg' => $this->lang->line('app_success'),
				'data'=> array('law' => $get_law['value']),				
			);
		}
		echo json_encode($result);
		exit();
	}		
	
	/* 
	 *  功能 : 开收/关闭.
	 */
	public function switchs(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token') &&
			$this->input->post('switch'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$switch = $this->input->post('switch');
		// 检验参数类型是否合法.
		if(!is_numeric($switch)){
			$result = array(
				'status' =>$this->config->item('app_param_illegal'), 
				'msg' => $this->lang->line('app_param_illegal'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();			
		}
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$switch_value = ($switch == '-1' || $switch == '1');
        if ( $user_id_len || $token_len || !$switch_value){
			$result = array(
				'status' =>$this->config->item('app_param_illegal'), 
				'msg' => $this->lang->line('app_param_illegal'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();
		}
		// 验证用户身份.
		$this->verify_token_valid($user_id,$access_token);
		// 更新用户开关状态.
		$update_result = $this->set_model->update_switchs($user_id,$switch);
		// 验证返回结果
		if (!$update_result){
			$result = array(
				'status' =>$this->config->item('app_update_data_fail'), 
				'msg' => $this->lang->line('app_update_data_fail'),
				'data'=>'',				
			);			
		}
		else{
			$result = array(
				'status' =>$this->config->item('app_success'), 
				'msg' => $this->lang->line('app_success'),
				'data'=>'',				
			);			
		}		
		echo json_encode($result);
		exit();
	}
	
	/*
	 *   退出系统
	 */	
	function logout(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
        if ( $user_id_len || $token_len ){
			$result = array(
				'status' =>$this->config->item('app_param_illegal'), 
				'msg' => $this->lang->line('app_param_illegal'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();
		}
		// 验证用户身份.
		$this->verify_token_valid($user_id,$access_token);
		// 退出系统
		$get_result = $this->set_model->logout($user_id);
		if ($get_result == TRUE){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
		}
		else{
			$status = $this->config->item('app_user_logout_fail');
			$msg = $this->lang->line('app_user_logout_fail');
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => '',
		);
		echo json_encode($result);
		exit();		
	}
	
}

/* End of file set.php */
/* Location: ./application/controllers/cooperation/set.php */
