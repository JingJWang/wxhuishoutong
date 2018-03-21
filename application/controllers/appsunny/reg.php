<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reg extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/reg_model');
	}

/************公共函数***************/
	/*  
	 *  判断请求方法是否为POST
	 */
	private function method_is_post(){
		if ($this->input->server('REQUEST_METHOD') != "POST"){
			$result = array(
				'status' => $this->config->item('app_req_method_err'),
				'msg' => $this->lang->line('app_req_method_err'),
				'data' => '',
				);
			echo json_encode($result);
		    exit();
		}
	}
	
	/* 
	 *  验证必填参数是否为空
	 *  @param $args boolean 是有空参数.(逻辑与运算, True 没有空参数, False 有空参数)
	 */
	private function param_is_null($args){
		if ($args == FALSE){
			$result = array(
				'status' => $this->config->item('app_param_null'),
				'msg' => $this->lang->line('app_param_null'),
				'data' => '',
				);
			echo json_encode($result);
			exit();
		}
	}
	
	/*
	 *  验证参数类型是否正确.
	 *  @param $args boolean 参数类型是否正确.(True 正确, False 错误)
	 */
	private function param_type_is_right($args){
		if ($args == FALSE){
			$result = array(
				'status' => $this->config->item('app_param_err'),
				'msg' => $this->lang->line('app_param_err'),
				'data' => '',
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

                 /************** 功能类api接口 ***********/	
				 
	/** 
	 *  功能 : 获取回收商令牌access_token.
	 */	
	public function get_token(){
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
		$param_type = (is_numeric($timestamp) && is_numeric($rnd));
		$this->param_type_is_right($param_type);
		// 校验用户密钥是否存在
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
        // 返回回收商令牌信息.
		$get_result = $this->reg_model->get_token($user_id);
		if (!$get_result){
			$status = $this->config->item('app_get_data_fail');
			$msg = $this->lang->line('app_get_data_fail');
			$data = '';
		}
		else{
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = array(
				'user_id' => $get_result['user_id'],
				'access_token' => $get_result['access_token'],
			);
		}
		$result = array(
			'status' => $status,
			'msg' => $msg,
			'data' => $data,
			);
		echo json_encode($result);
		exit();		
	}
	
	/* 
	 *  功能 : 校验 access_token 是否有效.
	 */	
	
	public function verify_token(){
	    // 验证请求类型是否为POST
		$this->method_is_post(); 		
		// 检验参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('access_token') && $this->input->post('imei'));
		$this->param_is_null($param_has_null);
		// 获取参数值.
        $user_id = $this->input->post('user_id');
		$timestamp = $this->input->post('timestamp');
		$access_token = $this->input->post('access_token');
		// 校验参数类型是否正确
		$param_type = (is_string('user_id') && is_string('access_token'));
		$this->param_type_is_right($param_type);
        // 令牌验证
		$this->verify_token_valid($user_id,$access_token);
		// 验证通过
		$result = array(
			'status' => $this->config->item('app_user_login_succ'),
			'msg' => $this->lang->line('app_user_login_succ'),
			'data' => ''
			);
		echo json_encode($result);
		exit();
	}		
		
	/* 
	 *  功能 : 获取手机短信验证码.
	 */
	public function get_msg(){
	    // 验证请求类型是否为POST
	    $this->method_is_post();
	    // 验证参数是否为空.
	    $param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
	        && $this->input->post('imei'));
	    $this->param_is_null($param_has_null);
	    // 获取参数值.
	    $phone_number = $this->input->post('phone_number');
	    // 验证是否为合法手机号.
	    $param_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
	        $phone_number);
	    $this->param_type_is_right($param_type);
	    // 一天最多三条短信。
	    $send_times = $this->reg_model->send_times($phone_number);
	    if($send_times >= $this->config->item('msgs_per_day')){
	        $result = array(
	            'status' => $this->config->item('app_msg_more_than_limit'),
	            'msg'    => $this->lang->line('app_msg_more_than_limit'),
	            'data'   => '',
	        );
	        echo json_encode($result);
	        exit();
	    }
	    // 伪随即验证码
	    $content = array(mt_rand(100000,1000000),$this->config->item('checkcode_Invalid_time')/60);
	    // 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID.
	    // 发送短信验证码
	    $this->load->library('alidayu/alimsg');	    
	    $this->alimsg=new Alimsg();	    
	    $this->alimsg->mobile=$phone_number;	    
	    $this->alimsg->appkey=$this->config->item('alidayu_appkey');	    
	    $this->alimsg->secret=$this->config->item('alidayu_secretKey');	    
	    $this->alimsg->sign=$this->config->item('alidayu_signname');	    
	    $this->alimsg->template=$this->config->item('APP_alidayu_reg_login');	    
	    $this->number=$content[0];  
	    $this->alimsg->content="{\"code\":\"".$this->number."\",\"minute\":\"5\"}";	    
	    $response=$this->alimsg->SendVerifyCode();
	    $data = array(
	        'code_type' => 3, //3 表示回收商短信.
	        'code_moblie' => $phone_number,
	        'code_number' => $content[0],
	        'code_jointime' => time(),
	    );
	    // 短信端口返回的结果值.
	    if($response == FALSE){
	        $data['response_status'] = $this->alimsg->code;
	        $data['response_info'] = $this->alimsg->msg;
	        $data['code_status'] = 0;
	    }
	    else{
	        $data['response_status'] = $this->alimsg->code;
	        $data['response_time'] = 0;
	        $data['response_sid'] = $this->alimsg->msg;
	        $data['code_status'] = 1;
	    }
	    // 短信信息存入数据库.
	    $msg_result = $this->reg_model->save_verify_code($data);
	    // 根据短信运营商返回的状态判断是否发送成功.
	    if ($msg_result && $this->alimsg->code == '0'){
	        $status = $this->config->item('app_success');
	        $msg = $this->lang->line('app_success');
	        $data = '';
	    }
	    else{
	        $status = $this->config->item('app_send_code_fail');
	        $msg = $this->lang->line('app_send_code_fail');
	        $data = '';
	    }
	    $result = array(
	        'status' => $status,
	        'msg'    => $msg,
	        'data'   => $data
	    );
	    echo json_encode($result);
	    exit();
	}	
	
	/* 
	 *  功能 : 注册/登陆验证.
	 */	
	public function login(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('verify_code'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$phone_number = $this->input->post('phone_number');
		$verify_code = $this->input->post('verify_code');		
		// 验证参数值是否合法.
		$param_type = (preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
			$phone_number) && is_numeric($verify_code));
	    // 验证码长度6位.
		$verify_code_len = (strlen($verify_code) == 6);
		$this->param_type_is_right($param_type && $verify_code_len);
		// 验证手机号和验证码是否匹配.
		$verify_result = $this->reg_model->check_code($phone_number,$verify_code);
		// 验证码错误.
		if ($verify_result == 0){
			$result = array(
				'status' => $this->config->item('app_param_code_err'),
				'msg' => $this->lang->line('app_param_code_err'),
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
		// 验证码过期
		if ($verify_result == 1){
			$result = array(
				'status' => $this->config->item('app_param_code_expire'),
				'msg' => $this->lang->line('app_param_code_expire'),
				'data' => ''
			);
			echo json_encode($result);
			exit();		
		}
		// 验证通过.判断是否已注册.否则生成基本信息.
		$user_is_exist = $this->reg_model->check_user_is_exist($phone_number);
		// 已经注册时,返回登陆成功状态码及用户密钥,令牌.
		if ($user_is_exist){
		    $token = sha1('maijin'.(string)time().(string)mt_rand(100000,999999));
		    $update_token_result = $this->reg_model->update_user_token_by_phone($phone_number,$token);
		    if ($update_token_result){
		        $get_token_result = $this->reg_model->check_user_is_exist($phone_number);
		        if ($get_token_result){
		            $result = array(
		                'status' => $this->config->item('app_user_login_succ'),
		                'msg' => $this->lang->line('app_user_login_succ'),
		                'data' => $get_token_result,
		            );
		            echo json_encode($result);
		            exit();		            
		        }
		    }
		    else{
		        $result = array(
		            $status = $this->config->item('app_update_data_fail'),
		            $msg = $this->lang->line('app_update_data_fail'),
		            $data = '',		            
		        );
		        echo json_encode($result);
		        exit();	        
		    }
		}       		
		// 未注册时,生成新的编号.
		else{
			$data = array(
			    'phone_number' => $phone_number,
			    'time' => time(),
			);
			// 添加新用户到库
			$add_result = $this->reg_model->add_reg_user($data);
			// 添加失败时.
			if (!$add_result){
				$status = $this->config->item('app_user_reg_fail');
				$msg = $this->lang->line('app_user_reg_fail');
				$data = '';
			}
			// 添加成功时
			else{
				$status = $this->config->item('app_success');
				$msg = $this->lang->line('app_success');
				$data = '';
				
			}
			$result = array(
				'status' => $status,
				'msg' => $msg,
				'data' => $data
			);
			echo json_encode($result);
			exit();	
		}
	}
	
	/* 
	 *  功能 : 注册协议.
	 */
	public function agree(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('agreement'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$phone_number = $this->input->post('phone_number');
		$agreement = $this->input->post('agreement');
		// 验证是否为合法手机号.
		$phone_number_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
			$phone_number);		
		$agree_type = ($agreement == '-1' || $agreement == '1');
		// 验证参数是否合法.
		$this->param_type_is_right($phone_number_type && $agree_type);
		// 接受许可协议.
		if ($agreement == '1'){
			// 添加到数据库里.
			$add_result = $this->reg_model->add_user_agree($phone_number);
			if (!$add_result){
				$result = array(
					'status' => $this->config->item('app_add_data_fail'),
					'msg'    => $this->lang->line('app_add_data_fail'),
					'data'   => '',
					);
			}
			else{
				$result = array(
					'status' => $this->config->item('app_success'),
					'msg'    => $this->lang->line('app_success'),
					'data'   => '',
					);
						
			}		
			echo json_encode($result);
			exit();			
		}
		else{
			$result = array(
				'status' => $this->config->item('app_agree_fail'),
				'msg' => $this->lang->line('app_agree_fail'),
				'data' => '',
			);
			echo json_encode($result);
			exit();			
		}
	}

	/* 
	 * 功能 : 查看用户使用协议.
	 */
	public function agreement(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 许可协议的编号为6.
		$data = $this->reg_model->query_agreement_content('6');
		if (!$data){
			$status = $this->config->item('app_get_data_fail');
			$msg = $this->lang->line('app_get_data_fail');
			$e_data = '';
		}
		else{
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$e_data = array('agreement' =>$data['content']);
		}
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $e_data
		);
		echo json_encode($result);
		exit();			
	}
	
	/*
	 * 保存图片
	 */
	private function save_img($file){
	    $config['upload_path'] = './upload/img/cooperator/id/'.date('Ymd');
	    if (!file_exists($config['upload_path'])){
	        mkdir($config['upload_path']); //,0777
	        //chmod($config['upload_path'],0777);
	    }
	    $config['allowed_types'] = 'jpg|jpeg|png';
	    $config['file_name'] = time().mt_rand(1000, 9999);
	    $this->load->library('upload',$config);
	    if ($this->upload->do_upload($file)){
	        $result = $this->upload->data();
	        return $config['upload_path'].'/'.$result['file_name'];
	    }
	    else{
	        return FALSE;
	    }
	}
	
	/*
	 * 上传照片
	 */
	function photos(){
	    // 验证请求类型是否为POST
	    $this->method_is_post();
	    // 验证参数是否为空.
	    $param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
	        && $this->input->post('imei') && $this->input->post('type')
	        && isset($_FILES['file1']) && isset($_FILES['file2']) && isset($_FILES['file3']));
	    $this->param_is_null($param_has_null);
	    // 获取参数
	    $phone_number = $this->input->post('phone_number');
	    $auth_type = $this->input->post('type');
	    $file1 = $_FILES['file1'];
	    $file2 = $_FILES['file2'];
	    $file3 = $_FILES['file3'];
	    // 验证参数类型是否正确.
	    $phone_number_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
	        $phone_number);
	    $auth_type_len = ($auth_type == '1' || $auth_type == '2');
	    $files_len = ($file1['size'] > 0 && $file2['size'] > 0 && $file3['size'] > 0);
	    $types = array('image/jpeg','image/jpg','image/png');
	    $fiels_type = (in_array($file1['type'],$types) && in_array($file2['type'],$types) && in_array($file3['type'],$types));	    
	    $this->param_type_is_right($phone_number_type && $files_len && $auth_type_len && $fiels_type);
	    // 验证图片是否已经上传。
	    $auth_type_result = $this->reg_model->get_user_auth_img($phone_number);
	    if ($auth_type_result == TRUE){
	        $result = array(
	            'status' => $this->config->item('app_param_pic_exist'),
	            'msg' => $this->lang->line('app_param_pic_exist'),
	            'data' => '',
	        );
	        echo json_encode($result);
	        exit();
	    }
	    // 保存图片
	    $result1 = $this->save_img('file1');
	    $result2 = $this->save_img('file2');
	    $result3 = $this->save_img('file3');
	    $results = ($result1 && $result2 && $result3);
	    // 保存图片到服务器失败。
	    if (!$results){
	        $result = array(
	            'status' => $this->config->item('app_pic_upload_err'),
	            'msg' => $this->lang->line('app_pic_upload_err'),
	            'data' => '',
	        );
	        echo json_encode($result);
	        exit();
	    }
	    $data = array(
	        'phone_number' => $phone_number,
	        'auth_type' => $auth_type,
	        'path' => serialize(array($result1,$result2,$result3)),
	    );
	    $save_result = $this->reg_model->save_auth_pic($data);
	    if ($save_result){
	        $result = array(
	            'status' => $this->config->item('app_success'),
	            'msg' => $this->lang->line('app_success'),
	            'data' => '',
	        );
	        echo json_encode($result);
	        exit();
	    }
	    else{
	        $result = array(
	            'status' => $this->config->item('app_add_data_fail'),
	            'msg' => $this->lang->line('app_add_data_fail'),
	            'data' => '',
	        );
	        echo json_encode($result);
	        exit();
	    }	     
	}
	
	/* 
	 *  功能 : 完善个人信息页面 辅助函数.
	 */
	 
	private function get_child_array($f_data,$f_id){
		$children = array();
		$index = 1;
		foreach($f_data as $k => $v){
			if ($v['fid'] == $f_id){
				$children = array_merge($children,array($index=>$v));
				$index++;				
			}
		}
		return $children;
	}

	/* 
	 * 功能 : 完善个人信息页面
	 */	
	public function info(){			
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空
		$param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
			&& $this->input->post('imei'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$phone_number = $this->input->post('phone_number');
		// 验证手机号类型是否正确.
		$phone_number_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
			$phone_number);	
		$this->param_type_is_right($phone_number_type);
		//获取产品类型数据.父类编号为0
		$f_data = $this->reg_model->get_product_type();
		if (!$f_data){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg'    => $this->lang->line('app_get_data_fail'),
				'data'   => '',
			);
			echo json_encode($result);
			exit();				
		}
		$opend = array();
		$index = 1;
		foreach ($f_data as $k => $v){
			if ($v['fid'] == '0'){
				$temp_array = array(
					'id' =>$v['id'],
					'name' => $v['name'],
					'fid' => 0,
					'children' => $this->get_child_array($f_data,$v['id']),
				);
			$opend = array_merge($opend,array($index=>$temp_array));
			$index++;	
			}						
		}
		// 即将开放的类型	
		$opening_temp = $this->config->item('cooperator_opening');
		$opening = array();
		$index = 1;
		foreach ($opening_temp as $k => $v){
			$temp = array(
				'id' => $k,
				'name' => $v,
			);
			$opening = array_merge($opening,array($index=>$temp));
			$index++;
		}
		// 车辆类型.
		$car_type_temp = $this->config->item('cooperator_car_type');
		$car_type = array();
		$index = 1;
		foreach ($car_type_temp as $k => $v){
			$temp = array(
				'id' => $k,
				'type' => $v,
			);
			$car_type = array_merge($car_type,array($index=>$temp));
			$index++;
		}		
		// 回收范围.
		$ranges_temp = $this->config->item('cooperator_service_range');
		$ranges = array();
		$index = 1;
		foreach ($ranges_temp as $k => $v){
			$temp = array(
				'id' => $k,
				'range' => $v,
			);
			$ranges = array_merge($ranges,array($index=>$temp));
			$index++;
		}		
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg'    => $this->lang->line('app_success'),
			'data'   => array(
				'opend' => $opend,
				'opening' => $opening,
				'car_type' => $car_type,
				'ranges' => $ranges,
			)
		);
		echo json_encode($result);
		exit();			
	}	
	
	/*
	 * 生成回收商编号
	 * 限制说明,系统时间设置为东八区标准互联网时间,当每秒生成的订单数大于100时,提升编号位数.
	 */
	private function get_id(){
	    $now = date('Y-m-d H:i:s',time());
	    $year = substr($now, 2,2);
	    $month = substr($now,5,2);
	    $day = substr($now,8,2);
	    $time = strtotime($now) - strtotime(substr($now, 0,10).'00:00:00');
	    $number = $year.$month.$day.str_pad($time,5,'0',STR_PAD_LEFT).mt_rand(1000, 10000);
	    return $number;
	}
	
	/* 
	 * 功能 : 提交个人信息.
	 */
    public function confim(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空
		$param_has_null = ($this->input->post('phone_number') && $this->input->post('timestamp')
			&& $this->input->post('imei')  && $this->input->post('addr_detail')
			&& $this->input->post('name') && $this->input->post('opend')
			&& $this->input->post('weixin') && $this->input->post('work_year')
			&& $this->input->post('has_store')
			&& $this->input->post('sex') && $this->input->post('service_range'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$phone_number = $this->input->post('phone_number');
		$name = $this->input->post('name');
		$opend = $this->input->post('opend');
		$opening = $this->input->post('opening') ? $this->input->post('opening') : '';
		$custom = $this->input->post('custom') ? $this->input->post('custom') : '';
		$weixin = $this->input->post('weixin');
		$cell_number = $this->input->post('cell_number')?$this->input->post('cell_number'):'';
		$tel_number = $this->input->post('tel_number')?$this->input->post('tel_number'):'';
		$work_year = $this->input->post('work_year');
		$has_store = $this->input->post('has_store');
		$car_type = $this->input->post('car_type');
		$shop_addr = $this->input->post('shop_addr')?$this->input->post('shop_addr'):'';
		$sex = $this->input->post('sex');
		$service_range = $this->input->post('service_range');
		$addr_detail = $this->input->post('addr_detail');
		$other_code = $this->input->post('other_code');
		// 验证参数类型是否正确.
		$phone_number_type = preg_match('#^13[\d]{9}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
			$phone_number);	
		$addr_detail_len = (strlen($addr_detail) > 100);
		$name_len = (strlen($name) > 10);
		$opend_len = (strlen($opend) > 1000);
		$weixin_len = (strlen($weixin) > 60);
		$work_year_len = ((string)intval(($work_year)) == $work_year);
		$has_store_len = ($has_store == '-1' || $has_store == '1');
		$car_type_len = (strlen($car_type) > 10);
		$sex_len = ($sex == '1' || $sex == '2');
		$service_range_len = ((string)intval(($service_range)) == $service_range);
		$this->param_type_is_right($phone_number_type && !$addr_detail_len && !$name_len && !$opend_len
			&& !$weixin_len && $work_year_len && $has_store_len && !$car_type_len && $sex_len &&
			$service_range_len);
		// 检测是否已经提交过数据.
		$check_data_exist = $this->reg_model->check_reg_data_exist($phone_number);
		if ($check_data_exist){
			$result = array(
				'status' => $this->config->item('app_req_repeat'),
				'msg' => $this->lang->line('app_req_repeat'),
				'data' =>'',
			);
			echo json_encode($result);
			exit();
		}
		// 生成数据.
		$user_id = $this->get_id();
		$access_key = md5('maijin'.$phone_number.(string)mt_rand(100000,999999).'keji');
		$access_token = sha1('maijin'.(string)time().(string)mt_rand(100000,999999));
		$data = array(
			'phone_number' => $phone_number,
			'user_id'=> $user_id,
			'access_key'=>$access_key,
			'access_token'=>$access_token,
			'name' => $name,
			'opend' => $opend,
			'opening' =>$opening,
			'custom' =>$custom,
			'weixin' =>$weixin,
			'cell_number' =>$cell_number,
			'tel_number' =>$tel_number,
			'work_year' =>$work_year,
			'has_store' =>$has_store,
			'car_type' =>$car_type,
			'shop_addr'=>$shop_addr,
			'sex' =>$sex,
			'service_range'=>$service_range,
			'addr_detail' =>$addr_detail,
			'other_code' => $other_code,
		);
		$result = $this->reg_model->update_user_info($data);
		if(!$result){
			$status = $this->config->item('app_update_data_fail');
			$msg = $this->lang->line('app_update_data_fail');
			$data = '';
		}
		else{
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = array(
				'user_id' => $user_id,
				'secret_key' => $access_key,
				'access_token' => $access_token,
			);			
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);
		exit();			
	}	
	
}


/* End of file reg.php */
/* Location: ./application/controllers/cooperation/reg.php */
