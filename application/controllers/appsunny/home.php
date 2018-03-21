<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/reg_model');
		$this->load->model('appsunny/home_model');
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
	 *  功能 : 个人中心首页.
	 */
	public function index(){
	    	    // 验证请求类型是否为POST
		//$this->method_is_post();
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
        if ( $user_id_len || $token_len){
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
		// 获取用户数据.
		$get_result = $this->home_model->get_user_status($user_id);
		if (!$get_result){
			$status = $this->config->item('app_get_data_fail');
			$msg = $this->lang->line('app_get_data_fail');
			$data = '';
		}
		else{
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = $get_result;			
		}
		// 更新用户开关状态.		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);
		exit();
	}
		
	/* 
	 *  功能 : 辅助函数.
	 */
	 
  private function get_child_array($f_data,$f_id,$select){
		$children = array();
		$index = 1;
		foreach($f_data as $k => $v){
			$v['selected'] = in_array($v['name'],$select);
			if ($v['fid'] == $f_id){
				$children = array_merge($children,array($index=>$v));
				$index++;				
			}
		}
		return $children;
	}
		
	/* 
	 *   功能 : 个人信息
	 */
	public function info(){
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
        if ( $user_id_len || $token_len){
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
		// 获取用户数据.
		$get_result = $this->home_model->get_user_info($user_id);
		// 获取用户信息数据失败时
		if(!$get_result){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->lang->line('app_get_data_fail'),
				'data' => '',
			);
			echo json_encode($result);
			exit();
		}
		// 获取产品类型.
		$f_data = $this->reg_model->get_product_type();
		// json 格式的数据.
		if ($get_result['opend'] == FALSE){
		    $opend_select = array();
		}
		else{
		      $opend_select = array_column(json_decode($get_result['opend'],TRUE),'name');
		}
		$opend = array();
		$index = 1;
		foreach ($f_data as $k => $v){
			if ($v['fid'] == '0'){
				$temp_array = array(
					'id' =>$v['id'],
					'name' => $v['name'],
					'selected' =>in_array($v['name'],$opend_select),
					'children' => $this->get_child_array($f_data,$v['id'],$opend_select),
				);
			$opend = array_merge($opend,array($index=>$temp_array));
			$index++;	
			}						
		}
		// 即将开放的类型.
		$opening_data = $this->config->item('cooperator_opening');
		if ($get_result['opening'] == FALSE){
		    $opening_data_select = array();
		}
		else{ 
		      $opening_data_select = array_column(json_decode($get_result['opening'],TRUE),'name');
		}     
		$opening = array();
		$index = 1;
		foreach ($opening_data as $k => $v){
			$temp = array(
				'id' => $k,
				'name' => $v,
				'selected' => in_array($v,$opening_data_select),
			);
			$opening = array_merge($opening,array($index=>$temp));
			$index++;
		}
		// 车辆类型.
		$car_type_data = $this->config->item('cooperator_car_type');
		$car_type = array();
		$index = 1;
		foreach ($car_type_data as $k => $v){
			$temp = array(
				'id' => $k,
				'type' => $v,
				'selected' => ($v == $get_result['car_type']),
			);
			$car_type = array_merge($car_type,array($index=>$temp));
			$index++;
		}
		// 回收范围
		$ranges_data = $this->config->item('cooperator_service_range');
		$ranges = array();
		$index = 1;
		foreach($ranges_data as $k => $v){
			$temp = array(
				'id' => $k,
				'range' => $v,
				'selected' => ($k == $get_result['ranges']),
			);
			$ranges = array_merge($ranges,array($index=>$temp));
			$index++;
		}
		$this->load->helper('url');
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg' => $this->lang->line('app_success'),
			'data'=> array(
				'user_id' =>$user_id,
				'my_photo' => base_url($get_result['my_photo']),
				'my_name' => $get_result['my_name'],
				'opend' => $opend,
				'opening' => $opening,
				'custom' => $get_result['custom'],
				'car_type' =>$car_type,
				'ranges' => $ranges,
				'area' => $get_result['area'],
				'addr_detail' => $get_result['addr_detail'],
			    'code' => $get_result['my_code'],
			),
		);
		echo json_encode($result);
		exit();		
	}	
	
	/* 
	 *   功能 : 更新个人信息.
	 */
	public function infoupdate(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$pic = $this->input->post('pic');
		$name = $this->input->post('name');
		$opend = $this->input->post('opend');
		$opening = $this->input->post('opening');
		$custom = $this->input->post('custom');
		$service_range = $this->input->post('service_range');
		$area = $this->input->post('area');
		$addr_detail = $this->input->post('addr_detail');
		$car_type = $this->input->post('car_type');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$pic_len = (strlen($pic) > 500000);
		$name_len = (strlen($name) > 10);
		$opend_len = (strlen($opend) > 255);
		$opening_len = (strlen($opening) > 255);
		$custom_len = (strlen($custom) >255);
		$service_range_len = (!is_numeric($service_range));
		$area_len = (strlen($area) > 100);
		$addr_detail_len = (strlen($addr_detail) > 100); 
        if ( $user_id_len || $token_len || $pic_len || $name_len || $opend_len || $opening_len
			|| $custom_len || $area_len || $addr_detail_len){
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
		// 保存图片
		$path = './upload/img/cooperator/photo/';
		if (!file_exists($path)) mkdir($path,'0755');
		$file = $path.(string)time().(string)mt_rand(1000,10000).'.jpg';
		$img = base64_decode($pic);
		$this->load->helper('file');		
		$pic_save_result = write_file($file,$img);
		if (!$pic_save_result){
			$result = array(
				'status' => $this->config->item('app_pic_upload_err'),
				'msg' => $this->config->item('app_pic_upload_err'),
				'data' => '',
			);
			echo json_encode($result);
			exit();
		}		
		// 提交用户数据.
		$data = array(
			'pic_path' => $file,
			'name' => $name,
			'opend' => $opend,
			'opening' => $opening,
			'custom' => $custom,
			'area' => $area,
			'service_range' => $service_range,
			'addr_detail' => $addr_detail,
		    'car_type' =>$car_type,
		);
		$get_result = $this->home_model->update_user_info($user_id,$data);				
		if($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
		}
		else{
			$status = $this->config->item('app_update_data_fail');
			$msg = $this->config->item('app_update_data_fail');
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => '',
		);
		echo json_encode($result);
		exit();				
	}	
	
	/* 功能 : 资金明细.
	 *
	 */
	public function bankroll(){
		// 校验请求方法是否为POST.
		$this->method_is_post();
		// 校验必填字段是否为空
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('rnd') && $this->input->post('signature'));
		$this->param_is_null($param_has_null);
		// 获取请求参数
		$user_id = $this->input->post('user_id');
		$timestamp = $this->input->post('timestamp');
		$rnd = $this->input->post('rnd');
		$signature = $this->input->post('signature');
		// 校验参数类型是否正确
		$param_type = (is_string($user_id) && is_numeric($timestamp)
			&& is_numeric($rnd) && is_string($signature));
		$this->param_type_is_right($param_type);
		// 校验用户是否存在
		$access_key = $this->reg_model->get_access_key($user_id);
		if (!$access_key){
			$result = array(
				'status' => $this->config->item('app_user_null'),
				'msg' => $this->lang->line('app_user_null'),
				'data' => ''
				);
			echo json_encode($result);
			exit();				
		}
        // 校验密钥是否合法
   		$access_key_check = sha1($access_key['access_key'].(string)$timestamp.(string)$rnd);
		if ($signature != $access_key_check){
			$result = array(
				'status' => $this->config->item('app_user_key_err'),
				'msg' => $this->lang->line('app_user_key_err'),
				'data' => ''
			);
			echo json_encode($result);
			exit();
			}
		// 获取数据	
		$get_result = $this->home_model->get_money_detail($user_id);
		if ($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = array();
			$index = 1;
			foreach($get_result as $k => $v){
			    $temp = array();
				$temp['account'] = '';
				$temp['type'] = $v['title'];
				$temp['id'] = $index;
				$temp['amount'] = $v['amount']/100;
				$temp['datetime'] = date('Y-m-d h:i:s',$v['times']);
				
				$data = array_merge($data,array($index=>$temp));
				$index ++;				
			}
		}
		else{
			$status = $this->config->item('app_no_more_data');
			$msg = $this->lang->line('app_no_more_data');
			$data = '';
		}			
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);	
		exit();		
	}	
	
	/* 功能 : 认证管理.
	 *
	 */
	public function auth(){
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
        if ( $user_id_len || $token_len){
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
		// 获取认证数据.
		$get_result = $this->home_model->get_auth_info($user_id);
		if ($get_result){
			switch ($get_result['auth_type']){
				case 1:
					$id_auth = '个人';
					$cash_auth = '未认证';
					break;
				case 2:
					$id_auth = '企业';
					$cash_auth = '未认证';
					break;
				case 3:
					$id_auth = '未认证';
					$cash_auth = '已认证';
					break;
				case 4:
					$id_auth = '个人';
					$cash_auth = '已认证';
					break;
				case 5:
					$id_auth = '企业';
					$cash_auth = '已认证';
					break;
				default:
					$id_auth = '未认证';
					$cash_auth = '未认证';					
			}
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = array(
				'id_auth' => $id_auth,
				'cash_auth' => $cash_auth,
			);							
		}
		else{
			$status = $this->config->item('app_get_data_fail');
			$msg = $this->lang->line('app_get_data_fail');
			$data = '';			
		}
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);	
		exit();			
	}			
	
	/* 功能 : 保证金认证.
	 *
	 */
	public function cashauth(){
		// 校验请求方法是否为POST.
		$this->method_is_post();
		// 校验必填字段是否为空
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('rnd') && $this->input->post('signature') &&
			$this->input->post('type') && $this->input->post('imei'));
		$this->param_is_null($param_has_null);
		// 获取请求参数
		$user_id = $this->input->post('user_id');
		$timestamp = $this->input->post('timestamp');
		$rnd = $this->input->post('rnd');
		$signature = $this->input->post('signature');
		$type = $this->input->post('type');
		// 校验参数类型是否正确
		$param_type = (is_string($user_id) && is_numeric($timestamp)
			&& is_numeric($rnd) && is_string($signature) && ($type == '3'));
		$this->param_type_is_right($param_type);
		// 校验用户是否存在
		$access_key = $this->reg_model->get_access_key($user_id);
		if (!$access_key){
			$result = array(
				'status' => $this->config->item('app_user_null'),
				'msg' => $this->lang->line('app_user_null'),
				'data' => ''
				);
			echo json_encode($result);
			exit();				
		}
        // 校验密钥是否合法
   		$access_key_check = sha1($access_key['access_key'] + $timestamp + $rnd);
		if ($signature != $access_key_check){
			$result = array(
				'status' => $this->config->item('app_user_key_err'),
				'msg' => $this->lang->line('app_user_key_err'),
				'data' => ''
			);
			echo json_encode($result);
			exit();
			}
		// 验证余额
		$get_sum_result = $this->home_model->get_my_sum($user_id);
		// 余额不足.
		if ($get_sum_result['sum'] < $this->config->item('cooperator_cash_auth')){
			$result = array(
				'status' => $this->config->item('app_money_less'),
				'msg' => $this->config->item('app_money_less'),
				'data' => '',
			);
			echo json_encode($result);
			exit();
		}
		// 获取用户认证类型.
		$get_user_type = $this->home_model->get_auth_info($user_id);
		if (!$get_user_type['auth_type']){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->config->item('app_get_data_fail'),
				'data' => '',
			);
			echo json_encode($result);
			exit();
		}		
		// 数据操作
		$data = array(
			'sum' => $get_sum_result['sum'], //账户余额
			'sum_auth' => $this->config->item('cooperator_cash_auth'), //保证金额.
			'auth_type' => 3,  //保证金认证3.
			'user_auth_type' => $get_user_type['auth_type'], //用户已认证的类型
		);
		// 更新状态.
		$get_result = $this->home_model->update_cash_auth($user_id,$data);
		//输出结果.	
		if($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
		}
		else{
			$status = $this->config->item('app_update_data_fail');
			$msg = $this->config->item('app_update_data_fail');
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => '',
		);
		echo json_encode($result);
		exit();				
	}		
		
	/* 功能 : 发出的评价.
	 *
	 */
	public function comments(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$pages = ($this->input->post('pages') ? $this->input->post('pages') : 1 );
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$pages_len = $pages_len = ($pages < 0);
        if ( $user_id_len || $token_len || $pages_len){
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
		$page_size = $this->config->item('cooperator_offer_list_size');   		// 每页加载的条数
		$offset = $page_size * ($pages -1); // 偏移地址.
		$get_result = $this->home_model->get_my_comments($user_id,$offset,$page_size);
		if($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$list = array();
			$index = 1;
			foreach ($get_result as $k => $v){
				$temp['id'] = $index;
				$temp['name'] = $v['name'];
				$temp['class'] = $v['class'];
				$temp['content'] = $v['content'];
				$temp['remark'] = $v['remark'];
				$temp['datetime'] = date('Y-m-d H:i:s',$v['datetime']);
				$list = array_merge($list,array($index => $temp));
				$index++;
			}
			$data = array(
				'list' => $list,
				'page_number' => $pages,
			);			
		}
		else{
			$status = $this->config->item('app_no_more_data');
			$msg = $this->lang->line('app_no_more_data');
			$data = '';
		}			
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);	
		exit();				
	}		
		
	/* 功能 : 收到的评价.
	 *
	 */
	public function commentr(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$pages = ($this->input->post('pages') ? $this->input->post('pages') : 1 );
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 15);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$pages_len = ($pages < 0);
        if ( $user_id_len || $token_len || $pages_len){
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
		// 分页.
		$page_size = $this->config->item('cooperator_offer_list_size');   		// 每页加载的条数
		$offset = ($page_size * ($pages -1)); // 偏移地址.
		$get_result = $this->home_model->get_comments($user_id,$offset,$page_size);
		if($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$list = array();
			$index = 1;
			foreach ($get_result as $k => $v){
				$temp['id'] = $index;
				$temp['name'] = $v['name'];
				$temp['class'] = $v['class'];
				$temp['content'] = $v['content'];
				$temp['reamrk'] = $v['remark'];
				$temp['datetime'] = date('Y-m-d H:i:s',$v['datetime']);
				$list = array_merge($list,array($index => $temp));
				$index++;
			}
			$data = array(
				'list' => $list,
				'page_number' => $pages,
			);			
		}
		else{
			$status = $this->config->item('app_no_more_data');
			$msg = $this->lang->line('app_no_more_data');
			$data = '';
		}			
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);	
		exit();			
	}		
	
	
	/* 功能 : 退出系统.
	 *
	 */
	public function logout(){
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
        if ( $user_id_len || $token_len){
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
		// 更新用户数据.
		$get_result = $this->home_model->logout($user_id);
		if($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
		}
		else{
			$status = $this->config->item('app_user_logout_fail');
			$msg = $this->config->item('app_user_logout_fail');
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => '',
		);
		echo json_encode($result);
		exit();				
	}		
	
	/* 功能 : 实时位置API.
	 *
	 */
	public function position(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('lng') && $this->input->post('lat'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$lng = $this->input->post('lng');
		$lat = $this->input->post('lat');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$lng_len = (strlen($lng) < 1 || strlen($lng) > 20);
		$lat_len = (strlen($lat) < 1 || strlen($lat) > 20);
        if ( $user_id_len || $token_len || $lng_len || $lat_len){
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
		$data = array(
			'lng' => $lng,
			'lat' => $lat,
		);
		// 获取用户数据.
		$get_result = $this->home_model->add_user_position($user_id,$data);
		if($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
		}
		else{
			$status = $this->config->item('app_add_data_fail');
			$msg = $this->lang->line('app_add_data_fail');
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

/* End of file home.php */
/* Location: ./application/controllers/cooperation/home.php */
