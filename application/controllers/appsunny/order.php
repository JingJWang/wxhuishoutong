<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-Type: text/html;charset=utf-8");
class Order extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/reg_model');
		$this->load->model('appsunny/order_model');
		$this->load->model('appsunny/offer_model');
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
	 *  验证用户状态(0待审核 1冻结 2未通过 3 通过) 和用户开关(-1 关闭 1 打开)
	 */	
	private function verify_user_status($user_id){
		$get_data = $this->offer_model->get_user_status($user_id);
		// 获取数据成功时.
		if ($get_data == TRUE){
			switch ($get_data['user_status']){
				case 0:
					$result = array(
						'status' => $this->config->item('app_user_wait'),
						'msg' => $this->lang->line('app_user_wait'),
						'data' => '',
					);
					echo json_encode($result);
					exit();
				case 1:
					$result = array(
						'status' => $this->config->item('app_user_freeze'),
						'msg' => $this->lang->line('app_user_freeze'),
						'data' => '',
					);
					echo json_encode($result);
					exit();				
				case 2:	
					$result = array(
						'status' => $this->config->item('app_user_fail'),
						'msg' => $this->lang->line('app_user_fail'),
						'data' => '',
					);
					echo json_encode($result);
					exit();
				default:				
			}
			if ($get_data['switchs'] == -1){
				$result = array(
					'status' => $this->config->item('app_user_swich_close'),
					'msg' => $this->lang->line('app_user_swich_close'),
					'data' => ''
					);
				echo json_encode($result);
				exit();					
			}
		}
		// 获取数据失败时.
		else{
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg' => $this->lang->line('app_get_data_fail'),
				'data' => ''
				);
			echo json_encode($result);
			exit();				
		}
	}			
	
	/* 功能 : 获取订单列表.
	 *
	 */
	public function lists(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$status = ($this->input->post('status') == FALSE) ? 1 : $this->input->post('status');
		$page_number = $this->input->post('page_number') ? $this->input->post('page_number') : 1;
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$status_len = in_array($status,array('-1','1','3','4'));
		$page_number_len = ($page_number < 1 || $page_number > 1000);
        if ( $user_id_len || $token_len || !$status_len || $page_number_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);
		// 筛选前n个符合条件的订单倒序输出.
		$size = $this->config->item('cooperator_offer_list_size');
		$limit = ($page_number - 1) * $size;
		$get_order_list = $this->order_model->get_order_list($user_id,$status,$limit,$size);
		$this->load->helper('url');
		switch ($status){
			case -1:
				//echo 'cancel';
				if ($get_order_list == TRUE){
					$status = $this->config->item('app_success');
					$msg = $this->lang->line('app_success');
					$list = array();
					$index = 1;
					$a = $this->config->item('cooperator_cancel_option');					
					foreach($get_order_list as $k => $v){
						$v['pic'] = base_url($v['pic']);
						$v['who'] = ($v['who'] == 1) ? '回收商' : '用户';
						$v['reason'] = $v['reason'] ? $a[$v['reason']] : '';
						$list = array_merge($list,array($index=>$v));
						$index ++;
					}
					$data = array(
						'list'=> $list,
						'switch' => 1,
						'page_size' =>$size,
						'page_number' =>$page_number,
					);
				}
				else{
					$status = $this->config->item('app_no_more_data');
					$msg = $this->lang->line('app_no_more_data');
					$data = '';
				}				
				break;
			case 1:
			    //echo 'offer';
				if ($get_order_list == TRUE){
					$status = $this->config->item('app_success');
					$msg = $this->lang->line('app_success');
					$list = array();
					$index = 1;
					foreach($get_order_list as $k => $v){
						$v['pic'] = base_url($v['pic']);
						$v['price'] = ($v['second'] == 0) ? $v['price'] : $v['second'];	
						$list = array_merge($list,array($index=>$v));
						$index ++;
					}
					$data = array(
						'list'=> $list,
						'switch' => 1,
						'page_size' =>$size,
						'page_number' =>$page_number,
					);
				}
				else{
					$status = $this->config->item('app_no_more_data');
					$msg = $this->lang->line('app_no_more_data');
					$data = '';
				}				
				break;
			case 2:
			    //echo 'untrans';
				if ($get_order_list == TRUE){
					$status = $this->config->item('app_success');
					$msg = $this->lang->line('app_success');
					$list = array();
					$index = 1;
					foreach($get_order_list as $k => $v){
						$v['pic'] = base_url($v['pic']);
						$v['price'] = ($v['second'] == 0) ? $v['price'] : $v['second'];						
						$list = array_merge($list,array($index=>$v));
						$index ++;
					}
					$data = array(
						'list'=> $list,
						'switch' => 1,
						'page_size' =>$size,
						'page_number' =>$page_number,
					);
				}
				else{
					$status = $this->config->item('app_no_more_data');
					$msg = $this->lang->line('app_no_more_data');
					$data = '';
				}				
				break;
			case 3:
				//echo 'unconfim';
				if ($get_order_list == TRUE){
					$status = $this->config->item('app_success');
					$msg = $this->lang->line('app_success');
					$list = array();
					$index = 1;
					foreach($get_order_list as $k => $v){
						$v['pic'] = base_url($v['pic']);
						$v['price'] = ($v['second'] == 0) ? $v['price'] : $v['second'];						
						$list = array_merge($list,array($index=>$v));
						$index ++;
					}
					$data = array(
						'list'=> $list,
						'switch' => 1,
						'page_size' =>$size,
						'page_number' =>$page_number,
					);
				}
				else{
					$status = $this->config->item('app_no_more_data');
					$msg = $this->lang->line('app_no_more_data');
					$data = '';
				}
				break;
			case 4:
				//echo 'done';
				if ($get_order_list == TRUE){
					$status = $this->config->item('app_success');
					$msg = $this->lang->line('app_success');
					$list = array();
					$index = 1;
					foreach($get_order_list as $k => $v){
						$v['pic'] = base_url($v['pic']);
						$list = array_merge($list,array($index=>$v));
						$index ++;
					}
					$data = array(
						'list'=> $list,
						'switch' => 1,
						'page_size' => $size,
						'page_number' => $page_number,
					);
				}
				else{
					$status = $this->config->item('app_no_more_data');
					$msg = $this->lang->line('app_no_more_data');
					$data = '';
				}				
				break;
			default:
				break;						
		}	
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);
		exit();
	}

	/* 
	 *  功能 : 订单确认页面.
	 */
	public function detail(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
        if ( $user_id_len || $token_len || $order_id_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);		
		// 获取订单详情
		$get_order_detail = $this->order_model->get_order_detail($user_id,$order_id);
		// 获取订单数据失败时.
		if (!$get_order_detail){
			$result = array(
				'status' =>$this->config->item('app_get_data_fail'), 
				'msg' => $this->lang->line('app_get_data_fail'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();
		}
		// 获取订单数据成功,生成数据输出.
		// 用户信息
		$order_user_name = ($get_order_detail['name'] == TRUE) ? $get_order_detail['name'] : '微信用户';
		$order_user_phone = $get_order_detail['mobile'];
		// 订单地址.
		$o_addr = $get_order_detail['province'].$get_order_detail['city'].$get_order_detail['county']
		.$get_order_detail['xiaoqu'].$get_order_detail['house_number'];
		// 订单属性.
		$name_all = $this->config->item('nonstandard_product_type');
		$name = $get_order_detail['title'];
		/*
		foreach($name_all as $k => $v){
			if ($v['type_id'] == $get_order_detail['ftype']){
				$name = $v['type_name'];
				break;
			}
		}
		*/
		// 详细属性.
		switch ($get_order_detail['ftype']){
			case 3:
				$property_temp = $this->config->item('luxurygoods_attribute_key');
				break;
			case 1:
				$property_temp = $this->config->item('electronic_attribute_key');
				break;
			case 2:
				$property_temp = $this->config->item('appliance_attribute_key');
				break;
			default:
			// 待定.
				break;				
		}
		$oather_value = json_decode($get_order_detail['oather'],TRUE);
		$property = array();
		$index = 1;
		foreach ($property_temp[$get_order_detail['ctype']] as $k=>$v){
			if (array_key_exists($k,$oather_value)){
				$property[$k] = array(
					'key' => $v,
					'value' => $oather_value[$k],
				);
			}
			else{
				$peroperty[$k] = array(
					'key' => $v,
					'value' => '无',
				);
			}
			$index++;
		}
		// 更多属性.
		$more['aviable'] = (bool)$get_order_detail['isused'];
		$more['purchase_date'] = $get_order_detail['buydate'];
		//$more['price'] = $get_order_detail['s_price'];
		// 图片属性.
		$this->load->helper('url');
		$pic = array();
		$pic_temp = explode(',',$get_order_detail['img']);
		$index = 1;
		$temp = array();
		foreach ($pic_temp as $k => $v){
			$temp['id'] = $k;
			$temp['url'] = base_url($v);
			$pic = array_merge($pic,array($index=>$temp));
			$index++;
		}
		$service_temp = explode(',', $get_order_detail['service']);
		$service = array();
		$index = 1;
		$temp = array();
		foreach($service_temp as $k => $v){
		   $temp['id'] = $k;
		   $temp['desc'] = $v;
		   $service = array_merge($service,array($index=>$temp));
		   $index++;
		}
		if ($get_order_detail['status'] == 2){
		    $data = array(
		        'user_name' => $order_user_name,
		        'user_phone' => $order_user_phone,
		        'order_id' => $order_id,
		        'name' => $name,
		        'addr' =>$o_addr,
		        'property' => $property,
		        'more' => $more,
		        'pic' => $pic,
		        'order_status' => $get_order_detail['status'],
		        'my_price' => ($get_order_detail['second'] == 0) ? $get_order_detail['my_offer'] : $get_order_detail['second'] ,
		    );
		}
		elseif ($get_order_detail['status'] == 3){
		    if ($get_order_detail['prepay'] != 1){
		        $modify = FALSE;
		        $pay = TRUE;
		    }
		    elseif ($get_order_detail['times'] == 1){
		        $modify = TRUE;
		        $pay = TRUE;
		    }
		    elseif ($get_order_detail['agree'] != 1){
		        $modify = FALSE;
		        $pay = FALSE;
		    }
		    else{
		        $modify = FALSE;
		        $pay = TRUE;
		    }
		    $data = array(
		        'user_name' => $order_user_name,
		        'user_phone' => $order_user_phone,
		        'order_id' => $order_id,
		        'name' => $name,
		        'addr' =>$o_addr,
		        'property' => $property,
		        'more' => $more,
		        'pic' => $pic,
		        'order_status' => $get_order_detail['status'],
		        'my_price' => ($get_order_detail['second'] == 0) ? $get_order_detail['my_offer'] : $get_order_detail['second'] ,
		        'modify' => $modify,
		        'pay' => $pay,
		        'prepay' => $get_order_detail['prepay'],
		    );
		}
		else{
			$data = array(
				'order_id' => $order_id,
				'name' => $name,
				'addr' =>$o_addr,
				'property' => $property,
				'more' => $more,
				'pic' => $pic,
				'order_status' => $get_order_detail['status'],
				'my_price' => ($get_order_detail['second'] == 0) ? $get_order_detail['my_offer'] : $get_order_detail['second'],
			);
		}
		// 添加服务选项.
		$data['service'] = $service;
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg' => $this->lang->line('app_success'),
			'data' => $data,	
		);		
		echo json_encode($result);
		exit();					
	}

	/* 
	 *   功能 : 订单确认按钮.
	 */	
	
	public function confim(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
        if ( $user_id_len || $token_len || $order_id_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);		
		// 订单状态变更.
		$get_result = $this->order_model->user_confim($user_id,$order_id);
		// 返回结果
		if ($get_result == TRUE){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
		}
		else{
			$status = $this->config->item('app_update_data_fail');
			$msg = $this->lang->line('app_update_data_fail');
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => '',
		);
		echo json_encode($result);
		exit();		
	}
	
	/*
	 * 生成20位长度的支付编号
	 */
	private function get_payid(){
	    $number = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
	    return $number;
	}


	/* 功能 : 预支付接口.
	 * 
	 */
	public function pre_pay(){
	    // 验证请求类型是否为POST
	    $this->method_is_post();
	    // 验证参数是否为空.
	    $param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
	        && $this->input->post('imei') && $this->input->post('access_token')
	        && $this->input->post('pay_type') && $this->input->post('order_id'));
	    $this->param_is_null($param_has_null);
	    // 获取参数
	    $user_id = $this->input->post('user_id');
	    $access_token = $this->input->post('access_token');
	    $order_id = $this->input->post('order_id');
	    $pay_type = $this->input->post('pay_type');
	    // 检测参数长度或值是否合法.
	    $user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
	    $token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
	    $order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
	    $pay_type_len = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3' || $pay_type == '4');
	    if ( $user_id_len || $token_len || $order_id_len || !$pay_type_len){
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
	    // 验证用户状态
	    $this->verify_user_status($user_id);
	    // 获取订单信息.
	    $order_info = $this->order_model->check_order_info($user_id,$order_id);
	    if ($order_info == FALSE){
	        $result = array(
	            'status' => $this->config->item('app_get_data_fail'),
	            'msg' => $this->lang->line('app_get_data_fail'),
	            'data' => '',
	        );
	        echo json_encode($result);
	        exit();
	    }
	    $amount = $order_info['price'];
	    // 生成支付编号
	    $pay_number = $this->get_payid();
	    // 支付处理.
	    switch ($pay_type){
	        // 微信
	        case 1:
	            $this->load->library('wxsdk/wxpay');
	            $info=array(
	                'body' => $order_info['order_name'],
	                'orderid' => $pay_number,
	                'pro_id' => $pay_number,
	                'moeny'=> $amount,
	                'type'=>'APP'
	            );
	            $pre_info = $this->wxpay->create_order($info);
	            $pre_info['prepay_number'] = $pay_number;
	            break;
	            // 支付宝
	        case 2:
	            $pre_info ='';
	            break;
	            // 百度钱包
	        case 3:
	            $this->load->library('baifubao/pay_unlogin');
	            $pre_info = $this->pay_unlogin->create_orderinfo($pay_type,array(
	                'order_no' => $pay_number,
	                'goods_name' => $order_info['order_name'],
	                'goods_desc' => $order_info['order_name'],
	                'total_amount' => $amount,
	                'buyer_sp_username' => $order_info['name'],
	            ));
	            break;
			// 余额	
			case 4:
			    $my_balance = $this->order_model->get_balance($user_id,$order_id);
				if ($my_balance == FALSE){
					$pre_info = FALSE;
				}
				// 余额不足
				if ($my_balance['balance'] < $amount){
					$balance_result = array(
						'status' => $this->config->item('app_money_less'),
						'msg' => $this->lang->line('app_money_less'),
						'data' => '',
					);
					echo json_encode($balance_result);
					exit();
				}
                $pre_info = array('pay_type' => 4);
                $pre_info['prepay_number'] = $pay_number;                 
				break;			
	    }
	    if ($pre_info == TRUE){
	        $status = $this->config->item('app_success');
	        $msg = $this->lang->line('app_success');
	        $data = $pre_info;
	    }
	    else{
	        $status = $this->config->item('app_pay_err');
	        $msg = $this->lang->line('app_pay_err');
	        $data = '';
	    }
	    $pre_result = array(
	        'status' => $status,
	        'msg' => $msg,
	        'data' => $data,
	    );
	    echo json_encode($pre_result);
	    exit();
	}
	
	/*
	 * 预支付查询接口
	 */
	function query_prepay(){
		// 验证请求类型是否为POST
        $this->method_is_post();
        // 验证参数是否为空.
        $param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
            && $this->input->post('imei') && $this->input->post('access_token')
            && $this->input->post('pay_type') && $this->input->post('order_id')
			&& $this->input->post('prepay_number'));
        $this->param_is_null($param_has_null);
        // 获取参数
        $user_id = $this->input->post('user_id');
        $access_token = $this->input->post('access_token');
        $order_id = $this->input->post('order_id');
        $pay_type = $this->input->post('pay_type');
		$prepay_number = $this->input->post('prepay_number');
        // 检测参数长度或值是否合法.
        $user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
        $token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
        $order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
		$prepay_number_len = (strlen($prepay_number) < 1 || strlen($prepay_number) > 20);
        $pay_type_len = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3' || $pay_type == '4');
        if ( $user_id_len || $token_len || $order_id_len || !$pay_type_len || $prepay_number_len){
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
        // 验证用户状态
        $this->verify_user_status($user_id);
        // 获取订单信息.
        $order_info = $this->order_model->check_order_infos($user_id,$order_id);
        if ($order_info == FALSE){
            $result = array(
                'status' => $this->config->item('app_get_data_fail'),
                'msg' => $this->lang->line('app_get_data_fail'),
                'data' => '',
            );
            echo json_encode($result);
            exit();
        }
        $amount = $order_info['price'];
        // 获取通知对应的信息.
        $get_wxuser_info = $this->order_model->get_wxuser_info($order_id);
        // 查询支付结果..
        switch ($pay_type){
            // 微信
            case 1:
                $this->load->library('wxsdk/wxpay');
                $info = $this->wxpay->order_query($prepay_number);
                if ($info == FALSE){
                    sleep(2);
                    $info = $this->wxpay->order_query($prepay_number);
                }
                break;
            // 支付宝
            case 2:
                $info ='';
                break;
            // 百度钱包
            case 3:
                $this->load->library('baifubao/pay_unlogin');
                $info=$this->pay_unlogin->query_order($prepay_number);
                if ($info == FALSE){
                    // 第一次查询失败后,延时2s再次查询
                    sleep(2);
                    $info=$this->pay_unlogin->query_order($prepay_number);
                }
                break;
            // 余额
            case 4:
        	    $my_balance = $this->order_model->get_balance($user_id,$order_id);
				if ($my_balance == FALSE){
					$pre_info = FALSE;
				}
				// 余额不足
				if ($my_balance['balance'] < $amount){
					$balance_result = array(
						'status' => $this->config->item('app_money_less'),
						'msg' => $this->lang->line('app_money_less'),
						'data' => '',
					);
					echo json_encode($balance_result);
					exit();
				}
                else{
                    $info = TRUE;
                }
                break;
        }        
        // 更新支付结果.
        $get_result = '';		
        if ($info == TRUE && $get_wxuser_info){
			$wxid = $get_wxuser_info['wx_id'];
			if($order_info['order_status'] == 2){
			     // 余额支付
			     if ($pay_type == '4'){
				    $get_result = $this->order_model->balance_prepay($user_id,$wxid,$order_id);
			     }
			     // 各种宝支付
			     else{
				    $get_result = $this->order_model->bao_prepay($user_id,$wxid,$order_id,$pay_type,$prepay_number);
			     }
			}
			else{
			    $pay_data = array(
			        'prepay_number' => $prepay_number,
			        'user_id' => $user_id,
			        'pay_type' => $pay_type,
			        'amount' => $amount * 100,
			    );
			    $get_result = $this->order_model->order_pay_f($pay_data);
			    if ($get_result){
			        $status = $this->config->item('cancel_pay_success');
			        $msg = $this->lang->line('cancel_pay_success');
			        $data = '';
			    }
			    else{
			        $status = $this->config->item('app_pay_err');
			        $msg = $this->lang->line('app_pay_err');
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
        }
        if ($info && $get_result){
            $status = $this->config->item('app_success');
            $msg = $this->lang->line('app_success');
            $data = '';
        }
        else{
            $status = $this->config->item('app_pay_err');
            $msg = $this->lang->line('app_pay_err');
            $data = '';
        }
        $result = array(
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data,
        );
        echo json_encode($result);
        if($info && $get_wxuser_info){
            // 给用户发送微信通知
            $this->load->model('common/wxcode_model');
            $temp = $this->config->item('coop_wxuser_done_url');
            $this->load->helper('url');
            $temp_url =  base_url($temp);
            $content = sprintf($this->config->item('coop_wxuser_prepay_done_info'),
                $get_wxuser_info['openid'],$get_wxuser_info['title'],$get_wxuser_info['money'],$temp_url);
            $this->wxcode_model->sendmessage($content);
            // 给微信用户发送短信通知
            $this->load->library('alidayu/alimsg');
            $this->alimsg=new Alimsg();
            $this->alimsg->mobile=$get_wxuser_info['mobile'];
            $this->alimsg->appkey=$this->config->item('alidayu_appkey');
            $this->alimsg->secret=$this->config->item('alidayu_secretKey');
            $this->alimsg->sign=$this->config->item('alidayu_signname');
            $this->alimsg->template=$this->config->item('APP_alidayu_prepay_msg');
            $this->alimsg->content="{\"name\":\"".$get_wxuser_info['title']."\",\"moeny\":\"".$get_wxuser_info['money']."\"}";
            $response = $this->alimsg->SendNotice();
        }
        exit();
	}

	/* 功能 : 成交按钮.
	 *
	 */	
	public function done(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('pay_type') && $this->input->post('amount')
			&& $this->input->post('order_id'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		$pay_type = $this->input->post('pay_type');
		$amount = $this->input->post('amount');
		//$serial_number = $this->input->post('serial_number');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
		$pay_type_len = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3' || $pay_type == '4'
		    || $pay_type == '5');
		$amount_len = ((int)$amount < 1 );
        if ( $user_id_len || $token_len || $order_id_len || !$pay_type_len || $amount_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);
		// 获取订单信息.
		$order_info = $this->order_model->get_order_info($user_id,$order_id);
		if ($order_info == FALSE){
		    $result = array(
		        'status' => $this->config->item('app_get_data_fail'),
		        'msg' => $this->lang->line('app_get_data_fail'),
		        'data' => '',
		    );
		    echo json_encode($result);
		    exit();
		}
		// 预支付订单处理
		if($order_info['prepay'] == 1){
		    $times = $order_info['times'];   // 报价次数。
		    switch ($times){
		        case 1:
		            $pay_type = 6;
		            break;
		        default:
		            $chajia = $order_info['price'] - $order_info['second'];
		            if ($chajia > 0){
		                $pay_type = 6;
		            }
		            else{
		                // 预支付订单不支持线下支付。且需要用户确认
		                $pay_type_check = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3' || $pay_type == '4');
		                if (!$pay_type_check || $order_info['agree'] != 1){
		                    $result = array(
		                        'status' => $this->config->item('app_req_illegal'),
		                        'msg' => $this->lang->line('app_req_illegal'),
		                        'data' => '',
		                    );
		                    echo json_encode($result);
		                    exit();
		                }
		                else{
		                    $amount = abs($chajia);
		                }
		            }
		            break;
		    }
		            
		}
		$prepay_number = $this->get_payid();
		// 支付处理.
		switch ($pay_type){
		    // 微信
		    case 1:
		        $this->load->library('wxsdk/wxpay');
		        $info=array(
		            'body' => $order_info['order_name'],
		            'orderid' => $prepay_number,
		            'pro_id' => $prepay_number,
		            'moeny'=> $amount,
		            'type'=>'APP'
		        );
		        $pre_info = $this->wxpay->create_order($info);
		        $pre_info['prepay_number'] = $prepay_number;
		        break;
		    // 支付宝
		    case 2:
		        $pre_info ='';
		        break;
		    // 百度钱包
		    case 3:
		        $this->load->library('baifubao/pay_unlogin');
		        $pre_info = $this->pay_unlogin->create_orderinfo($pay_type,array(
		            'order_no' => $prepay_number,
		            'goods_name' => $order_info['order_name'],
		            'goods_desc' => $order_info['order_name'],
		            'total_amount' => $amount,
		            'buyer_sp_username' => $order_info['name'],
		        ));
		        break;
		    // 余额
		    case 4:
			    $my_balance = $this->order_model->get_balance($user_id,$order_id);
				if ($my_balance == FALSE){
					$info = FALSE;
				}
				// 余额不足
				if ($my_balance['balance'] < $amount){
					$balance_result = array(
						'status' => $this->config->item('app_money_less'),
						'msg' => $this->lang->line('app_money_less'),
						'data' => '',
					);
					echo json_encode($balance_result);
					exit();
				}
				// 保存余额支付的金额数
				$balance_amount = $this->order_model->save_amount($user_id,$order_id,$amount);
				if ($balance_amount == TRUE){
                    $pre_info = array('pay_type' => 4);
                    $pre_info['prepay_number'] = $prepay_number;                 
				}
				else{
				    $pre_info = FALSE;
				}
                break;
		    // 线下支付
		    case 5:
		        $balance_amount = $this->order_model->save_amount($user_id,$order_id,$amount);
		        if ($balance_amount == TRUE){
		            $pre_info = array('pay_type' => 5);
		            $pre_info['prepay_number'] = $prepay_number;
		        }
		        else{
		            $pre_info = FALSE;
		        }
		        break;
		   // 回收商未进行二次报价时。
		    case 6:
		        $pre_info = array('pay_type' => 8);
		        $pre_info['prepay_number'] = $prepay_number;
		        break;
		}
		if ($pre_info == TRUE){
		    $status = $this->config->item('app_success');
		    $msg = $this->lang->line('app_success');
		    $data = $pre_info;
		}
		else{
		    $status = $this->config->item('app_pay_err');
		    $msg = $this->lang->line('app_pay_err');
		    $data = '';		    
		}
		$pre_result = array(
		    'status' => $status,
		    'msg' => $msg,
		    'data' => $data,
		);
		echo json_encode($pre_result);
		exit();
	}
	
	/*
	 * 查询订单状态,返回结果.
	 */
    function query_order(){
        // 验证请求类型是否为POST
        $this->method_is_post();
        // 验证参数是否为空.
        $param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
            && $this->input->post('imei') && $this->input->post('access_token')
            && $this->input->post('pay_type') && $this->input->post('order_id')
            && $this->input->post('prepay_number'));
        $this->param_is_null($param_has_null);
        // 获取参数
        $user_id = $this->input->post('user_id');
        $access_token = $this->input->post('access_token');
        $order_id = $this->input->post('order_id');
        $pay_type = $this->input->post('pay_type');
        $prepay_number = $this->input->post('prepay_number');
        $remark = $this->input->post('remark');
        // 检测参数长度或值是否合法.
        $user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
        $token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
        $order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
        $prepay_number_len = (strlen($prepay_number) < 1 || strlen($prepay_number) > 20);
        $pay_type_len = ($pay_type == '1' || $pay_type == '2' || $pay_type == '3' || $pay_type == '4'
            || $pay_type == '5' || $pay_type == '8');
        $remark_len = (strlen($remark) > 58);
        if ( $user_id_len || $token_len || $order_id_len || !$pay_type_len || $prepay_number_len || $remark_len){
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
        // 验证用户状态
        $this->verify_user_status($user_id);
        // 获取通知对应的信息.
        $get_wxuser_info = $this->order_model->get_wxuser_info($order_id);
        // 查询支付结果..
        switch ($pay_type){
            // 微信
            case 1:
                $this->load->library('wxsdk/wxpay');
                $info = $this->wxpay->order_query($prepay_number);
                if ($info == FALSE){
                    sleep(2);
                    $info = $this->wxpay->order_query($prepay_number);
                }
                if ($info == TRUE){
                    $info = $info['total_fee'];
                }
                break;
            // 支付宝
            case 2:
                $info ='';
                break;
            // 百度钱包
            case 3:
                $this->load->library('baifubao/pay_unlogin');
                $info=$this->pay_unlogin->query_order($prepay_number);
                if ($info == FALSE){
                    // 第一次查询失败后,延时2s再次查询
                    sleep(2);
                    $info=$this->pay_unlogin->query_order($prepay_number);
                }
                if ($info == TRUE){
                    $info = $info['total_amount'];
                }
                break;
            // 余额
            case 4:
                $my_balance = $this->order_model->get_balance($user_id,$order_id);
                if ($my_balance == FALSE){
                    $info = FALSE;
                }
                // 余额不足
                if ($my_balance['balance'] < $my_balance['fee']){
                    $balance_result = array(
                        'status' => $this->config->item('app_money_less'),
                        'msg' => $this->lang->line('app_money_less'),
                        'data' => '',
                    );
                    echo json_encode($balance_result);
                    exit();
                }
                $info = TRUE;
                break;
            // 线下支付
            case 5:
                $info = TRUE;
                break;
            // 无须二次支付.
            case 8:
                $info = TRUE;
                break;
        }
        // 更新支付结果.
        $get_result = '';
        if ($info == TRUE){
            $get_order_info = $this->order_model->get_order_infos($user_id,$order_id);
            if ($get_order_info == FALSE){
                $get_result == FALSE;
            }
            else{
                $pay_data = array(
                    'wx_id' => $get_wxuser_info['wx_id'],
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'prepay_number' => $prepay_number,
                    'amount' => $info,
                    'pay_type' => $pay_type,
                    'remark' => $remark,
                    'price' => $get_order_info['price'],
                    'times' => $get_order_info['times'],
                    'first' => $get_order_info['first'],
                    'second' => $get_order_info['second'],
                );
                if ($get_order_info['order_status'] == 3){
                    if ($get_order_info['prepay'] == 1){
                        // 正好
                        if ($get_order_info['times'] == 1){
                            $get_result = $this->order_model->order_pay_a($pay_data);
                        
                        }
                        // 有余额
                        elseif($get_order_info['price'] > $get_order_info['second']){
                            $get_result = $this->order_model->order_pay_b($pay_data);
                        }
                        // 二次支付
                        else{
                            $get_result = $this->order_model->order_pay_c($pay_data);
                        }
                    }
                    else{
                        switch ($pay_type){
                            // 余额支付
                            case 4:
                                $get_result = $this->order_model->order_pay_e($pay_data);
                                break;
                            // 线下支付
                            case 5:
                                $get_result = $this->order_model->order_pay_done($pay_data);
                                break;
                            // 宝支付
                            default:
                                $get_result = $this->order_model->order_pay_d($pay_data);
                                break;
                        }
                     
                    }
                }
                // 用户取消订单，退款到用户余额里。
                else{
                    $get_result = $this->order_model->order_pay_f($pay_data);
                    if ($get_result){
                        $status = $this->config->item('cancel_pay_success');
                        $msg = $this->lang->line('cancel_pay_success');
                        $data = '';
                    }
                    else{
                        $status = $this->config->item('app_pay_err');
                        $msg = $this->lang->line('app_pay_err');
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
            }

        }
        // 添加记录.暂时用用户输入的金额.
        if ($info && $get_result){
            $status = $this->config->item('app_success');
            $msg = $this->lang->line('app_success');
            $data = '';
        }
        else{
            $status = $this->config->item('app_pay_err');
            $msg = $this->lang->line('app_pay_err');
            $data = '';
        }        
        $get_wxuser_info = $this->order_model->get_wxuser_info($order_id);
        if($info && $get_wxuser_info){
            // 给用户发送微信通知
            $this->load->model('common/wxcode_model');
            $temp = $this->config->item('coop_wxuser_done_url');
            $this->load->helper('url');
            $temp_url =  base_url($temp);
            $content = sprintf($this->config->item('coop_wxuser_done_info'),
                $get_wxuser_info['openid'],$get_wxuser_info['title'],$get_wxuser_info['amount'],$temp_url);
            $this->wxcode_model->sendmessage($content);
            // 给微信用户发送短信通知
	    $this->load->library('alidayu/alimsg');
            $this->alimsg=new Alimsg();
 	    $this->alimsg->mobile=$get_wxuser_info['mobile'];
	    $this->alimsg->appkey=$this->config->item('alidayu_appkey');
	    $this->alimsg->secret=$this->config->item('alidayu_secretKey');
	    $this->alimsg->sign=$this->config->item('alidayu_signname');
	    $this->alimsg->template=$this->config->item('APP_alidayu_order_success');
	    $this->alimsg->content="{\"name\":\"".$get_wxuser_info['title']."\",\"money\":\"".$get_wxuser_info['amount']."\"}";
	    $response = $this->alimsg->SendNotice();
        }
        // 对相关人员发送通知.
        $get_others_id = $this->order_model->get_others_id($user_id,$order_id);
        //成功获取数据时发送通知.
        if ($info && $get_others_id){
            $this->load->library('vendor/notice');
            $coop_others_ids = array();
            $my_name = '';
            foreach ($get_others_id as $k => $v){
                $coop_others_ids = array_merge($coop_others_ids,array($k =>$v['coop_ids']));
                $my_name = $v['my_name'];
            }
            $other_msg = sprintf($this->config->item('coop_others_order'),$order_id,
                $my_name,$get_wxuser_info['amount']);
            $this->notice->JPush('alias',$coop_others_ids,$other_msg);
        }
        $result = array(
                'status' => $status,
                'msg'    => $msg,
                'data'   => $data,
        );
        echo json_encode($result);
        exit();
    }	
	/* 
	 *  功能 : 修改报价页面.
	 */	
	
	public function offermodify(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
        if ( $user_id_len || $token_len || $order_id_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);		
		// 获取订单详情.
		$get_result = $this->order_model->get_first_offer($order_id);
		if ($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = array(
				'order_id' => $get_result['order_id'],
				'first_price' => $get_result['first_offer'],
			);
		}
		else{
			$status = $this->config->item('app_req_illegal');
			$msg = $this->lang->line('app_req_illegal');
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

	/*
	 *   功能 : 确认修改报价
	 */	
	
	public function offersecond(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id') && $this->input->post('second_offer'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		$first_offer = $this->input->post('first_offer');
		$second_offer = $this->input->post('second_offer');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
		$second_offer_len = (is_numeric($second_offer) && (int)$second_offer >= 1);
        if ( $user_id_len || $token_len || $order_id_len || (!$second_offer_len)){
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
		// 验证用户状态
		$this->verify_user_status($user_id);		
		// 验证is prepay and 修改次数是否达到最大值.
		$get_times = $this->order_model->get_first_offer($order_id);
		if ($get_times['times'] != 1 || $get_times['prepay'] != 1 || $get_times['first_offer'] == $second_offer){
			$result = array(
				'status' => $this->config->item('app_req_illegal'),
				'msg' => $this->lang->line('app_req_illegal'),
				'data' => '',
			);
			echo json_encode($result);
			exit();		
		}
		// 生成提交数据
		$data = array(
			'second_offer' => $second_offer,
			'order_id' => $order_id,
			'user_id' => $user_id,
		);
		if ($get_times['first_offer'] < $second_offer){
			$data['up'] = 1;
			$data['down'] = 0;
		}
		else{
			$data['down'] = 1;
			$data['up'] = 0;
		}
		$get_result = $this->order_model->modify_price($data);
		if ($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = '';
			// send a message to user.
			
		}
		else{
			$status = $this->config->item('app_update_data_fail');
			$msg = $this->lang->line('app_update_data_fail');
			$data = '';
		}		
		$result = array(
			'status' => $status,
			'msg'    => $msg,
			'data'   => $data,
		);
		echo json_encode($result);
		// 发送提醒
		if($get_result){
		    $get_wxuser_info = $this->order_model->get_wxuser_info($order_id);
		    //微信
		    $this->load->model('common/wxcode_model');
		    $temp = $this->config->item('coop_wxuser_modify_url');
		    $this->load->helper('url');
		    $temp_url =  base_url(sprintf($temp,$order_id));
		    $content = sprintf($this->config->item('coop_wxuser_modify_info'),
		        $get_wxuser_info['openid'],$get_wxuser_info['title'],$temp_url);
		    $this->wxcode_model->sendmessage($content);
		    // 短信
		    $this->load->library('alidayu/alimsg');
		    $this->alimsg=new Alimsg();
		    $this->alimsg->mobile=$get_wxuser_info['mobile'];
		    $this->alimsg->appkey=$this->config->item('alidayu_appkey');
		    $this->alimsg->secret=$this->config->item('alidayu_secretKey');
		    $this->alimsg->sign=$this->config->item('alidayu_signname');
		    $this->alimsg->template=$this->config->item('APP_alidayu_modify_price');
		    $this->alimsg->content="{\"name\":\"".$get_wxuser_info['title']."\"}";
		    $response = $this->alimsg->SendNotice();
		}
		exit();			
	}

	/* 
	 *   功能 : 取消交易.
	 */	
	
	public function cancel(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id') && $this->input->post('reason'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		$reason = $this->input->post('reason');
		$reason_more = $this->input->post('reason_more');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) > 20);
		$reason_len = ($reason == '1' || $reason == '2' || $reason == '3' || $reason == '4');
		$reason_more_len = (strlen($reason_more) > 100 );
        if ( $user_id_len || $token_len || $order_id_len || !$reason_len || $reason_more_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);		
		// 验证订单报价状态.
		$get_offer_result = $this->order_model->get_offer_status($user_id,$order_id);
		if (!$get_offer_result){
			// 非法请求
			$result = array(
				'status' => $this->config->item('app_req_illegal'),
				'msg' => $this->lang->line('app_req_illegal'),
				'data' => '',
			);
			echo json_encode($result);
			exit();				
		}
		// 进行取消操作.
		$data = array(
			'order_id' => $order_id,
			'reason' => $reason,
			'reason_more' =>$reason_more,
			'user_id' => $user_id,
		    'wx_id' => $get_offer_result['wx_id'],
		    'amount' => $get_offer_result['amount'],
		);
		$get_result = $this->order_model->update_offer_status($data);
		if ($get_offer_result['prepay'] == 1){
		    $money_back = $this->order_model->money_back($data);
		}
		else{
		    $money_back = TRUE;
		}
		if ($get_result && $money_back){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = '';
			$get_wxuser_info = $this->order_model->get_wxuser_info($order_id);
			if($get_wxuser_info){
			    // 给微信用户发送微信通知
			    $this->load->model('common/wxcode_model');
			    $temp = $this->config->item('coop_wxuser_cancel_url');
			    $this->load->helper('url');
			    $temp_url =  base_url($temp);
			    $content = sprintf($this->config->item('coop_wxuser_cancel_info'),
			        $get_wxuser_info['openid'],$get_wxuser_info['title'],
			        date('Y-m-d H:i:s',time()),$temp_url);
			    $this->wxcode_model->sendmessage($content);			    
			    // 给微信用户发送短信通知			    			    
		        $this->load->library('alidayu/alimsg');
		        $this->alimsg=new Alimsg();
		        $this->alimsg->mobile=$get_wxuser_info['mobile'];
		        $this->alimsg->appkey=$this->config->item('alidayu_appkey');
		        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
		        $this->alimsg->sign=$this->config->item('alidayu_signname');
		        $this->alimsg->template=$this->config->item('APP_alidayu_order_cancel');
		        $this->alimsg->content="{\"content\":\"".$get_wxuser_info['title']."\"}";
		        $response = $this->alimsg->SendNotice();			
		}
		else{
			$status = $this->config->item('app_update_data_fail');
			$msg = $this->lang->line('app_update_data_fail');
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
    }

    /*
	 *  辅助验证函数.
	 * @param $str string 以逗号分割的字符串
	 * @param $array array .
	 * @return bool True 有非法字符.
	 */
	 
	private function check_checkbox($str,$array){
		$str_array = explode(',',$str);
		foreach ($str_array as $k => $v){
			if (!in_array($v,$array)){
				return TRUE;
			}
		}
		return FALSE;
	}

	/* 
	 *   功能 : 回收商评价.
	 */	
	
	public function comment(){
	  // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id') && $this->input->post('score')
		    && $this->input->post('source'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		$score = $this->input->post('score');
		$describe = $this->input->post('describe');
		$comment = $this->input->post('comment');
		$source = $this->input->post('source');
		// 检测参数长度或值是否合法.
		$source_len = ($source == '-1' || $source == '4');
		if (!$source_len){
		    $result = array(
		        'status' =>$this->config->item('app_param_illegal'),
		        'msg' => $this->lang->line('app_param_illegal'),
		        'data'=>'',
		    );
		    echo json_encode($result);
		    exit();		    
		}
		$option =  ($source == '4') ? $this->config->item('cooperator_comment_option') :
		$this->config->item('cooperator_comment_cancel');
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
		$score_len = ($score == '1' || $score == '2' || $score == '3' || $score == '4' || $score == '5');
		$describe_len = ($describe) ? $this->check_checkbox($describe,$option) : FALSE;
		$comment_len = (strlen($comment) > 60 );
        if ( $user_id_len || $token_len || $order_id_len || $comment_len || $describe_len ||
            !$score_len){
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
		// 验证用户状态
		$this->verify_user_status($user_id);		
		// 验证是否已经评价
		$get_comment = $this->order_model->get_comment($user_id,$order_id);
		if ($get_comment){
			// 非法请求
			$result = array(
				'status' => $this->config->item('app_req_illegal'),
				'msg' => $this->lang->line('app_req_illegal'),
				'data' => '',
			);
			echo json_encode($result);
			exit();			
		}
		// 验证报单状态
		$get_offer_status = $this->order_model->verify_offer_status($user_id,$order_id);
		// 报价单不存在时
		if (!$get_offer_status){
			// 非法请求
			$result = array(
				'status' => $this->config->item('app_req_illegal'),
				'msg' => $this->lang->line('app_req_illegal'),
				'data' => '',
			);
			echo json_encode($result);
			exit();			
		}
		$temp = ($get_offer_status['status'] == -1 || $get_offer_status['status'] == 4 );
		if (!$temp){
			// 非法请求
			$result = array(
				'status' => $this->config->item('app_req_illegal'),
				'msg' => $this->lang->line('app_req_illegal'),
				'data' => '',
			);
			echo json_encode($result);
			exit();			
		}
		// 生成评价数据.
		$get_user_name = $this->order_model->get_user_name($order_id); //获取订单提交者微信名.
		$name = $get_user_name ? $get_user_name['name'] : '匿名' ;
		$wx_id = $get_user_name['wx_id'];
		$data = array(
			'user_id' => $user_id,
			'order_id' => $order_id,
			'score' => $score,
			'describe' => $describe,
			'comment' => $comment,
			'name' => $name,
		    'wx_id' => $wx_id,
		    'source' => $source,
		);
		// 提交评价信息.
		$get_result = $this->order_model->add_comment($data);
		if ($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = '';
		}
		else{
			$status = $this->config->item('app_add_data_fail');
			$msg = $this->lang->line('app_add_data_fail');
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
}

/* End of file order.php */
/* Location: ./application/controllers/cooperation/order.php */
