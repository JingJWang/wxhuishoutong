<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type: text/html; charset=utf-8");
class Offer extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/reg_model');
		$this->load->model('appsunny/offer_model');
		$this->load->model('appsunny/order_model');
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
	
	/*
	 * 验证用户状态(1冻结 2未通过) 和用户开关(-1 关闭 )
	 */
	private function verify_user_switch($user_id){
			$get_data = $this->offer_model->get_user_status($user_id);
		// 获取数据成功时.
		if ($get_data == TRUE){
			switch ($get_data['user_status']){
				case 1:
					$result = array(
						'status' => $this->config->item('app_user_freeze'),
						'msg' => $this->lang->line('app_user_freeze'),
						'data' => '',
					);
					echo json_encode($result);
					exit();			
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
	
	/*
	 *  获取指定点,指定范围的四个最值点(经度或维度).(默认五公里)
	 */
	private function get_lng_lat($lat,$lng,$distance){
		$earth_radius = 6371;  //地球半径 6371公里.
    	$dlng =  2 * asin(sin($distance / (2 * $earth_radius)) / cos(deg2rad($lat)));
    	$dlng = rad2deg($dlng);
 
    	$dlat = $distance/$earth_radius;
    	$dlat = rad2deg($dlat);	
		
		return array(
			'lat' => $lat,
			'lng' => $lng,
			'left' => $lng - $dlng,
			'right' => $lng + $dlng,
			'top' => $lat + $dlat,
			'bottom' => $lat - $dlat,
		);			
	}
	
	/*
	 *  获取两点间的距离
	 */
	private function getDistance($lng1,$lat1,$lng2,$lat2){
        //地球半径单位米 approximate radius of earth in meters
        $earthRadius = 6367000; 
        /* 
         * Convert these degrees to radians
         * to work with the formula
         */
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;        
        return round($calculatedDistance);		
	}
		
	/* 
	 *  功能 : 默认首页.
	 */
	public function index(){
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
		$fid = $this->input->post('fid');
		$cid = $this->input->post('cid');
		$filter = $this->input->post('filter');
		$sort = $this->input->post('sort');
		$pages = ($this->input->post('pages') == FALSE) ? 1 : $this->input->post('pages');
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$lng_len = preg_match('/^[1-9]d*.d*|0.d*[1-9]d*$/',$lng);
		$lat_len = preg_match('/^[1-9]d*.d*|0.d*[1-9]d*$/',$lat);
		// 父类支持电子产品(1),家电产品(2),旧衣产品(3).
		$fid_len = ($fid == '1' || $fid == '2' || $fid == '3' || $fid == '');  
		$cid_len = ($cid < 0 || $cid > 1000);
		//$check_fid_cid = ($fid && $cid);
		$pages_len = ($pages < 1 || $pages > 1000); //暂时支持1000页以内查询
		$filter_len = ($filter == '1' || $filter == '-1' || $filter == '');  //暂时只匹配1和-1.
		$sort_len = ($sort == '1' || $sort == '2' || $sort == '3' || $sort == '');
    if ( $user_id_len || $token_len || !$lng_len || !$lat_len || !$fid_len || 
			$cid_len || !$filter_len || $pages_len || !$sort_len){
			$result = array(
				'status' =>$this->config->item('app_param_illegal'), 
				'msg' => $this->lang->line('app_param_illegal'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();
		}
		// 验证用户身份.
		//$this->verify_token_valid($user_id,$access_token);
		// 验证用户开关状态
		$this->verify_user_switch($user_id);
		// 获取回收商设定的回收范围.
		$get_distance = $this->offer_model->get_cooperator_range($user_id);
		// 订单接受功能关闭时.获取用户数据失败时默认开启.
		if ($get_distance['switchs'] == -1){
		    $result = array(
		        'status' => $this->config->item('app_order_switch_off'),
		        'msg' => $this->lang->line('app_order_switch_off'),
		        'data' => '',
		    );
		    echo json_encode($result);
		    exit();
		}
		$location = $this->get_lng_lat($lat,$lng,$get_distance['distance']);
		$type = array(
			'ftype' => $fid,
			'ctype' => $cid,
		);
		// 起始数据.
		$limit = ($pages-1) * $this->config->item('cooperator_offer_list_size');
		// 每页显示的条数.
		$size = $this->config->item('cooperator_offer_list_size');
		$get_offer_result = $this->offer_model->get_user_order_by_filter($user_id,$location,$type,$filter,$sort,$limit,$size);
		// 查询成功时输出数据.
		$this->load->helper('url');
		if ($get_offer_result == TRUE){
			$list = array();
			$index = 1;
			$temp = array();
			foreach($get_offer_result as $k => $v){
				$v['id'] = $k;
				$v['pic'] = base_url($v['pic']);
				$v['datetime'] = date('Y-m-d H:i:s',$v['datetime']);
				$v['status'] = ($v['status'] == 1) ? '可用' : '不可用';
				$v['distance'] = $this->getDistance($lng,$lat,$v['lng'],$v['lat']);
				unset($v['lng'],$v['lat']);
				$list = array_merge($list,array($index=>$v));
				$index++;
			}
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = array(
				'list' => $list,
				'switch' => $get_distance['switchs'],
				'page_size' => $size,
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
			'msg' => $msg,
			'data' => $data,
		);
		echo json_encode($result);
		exit();
	}
	
	/* 
	 *  功能 : 订单详情.
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
		// 获取超过三天的未处理订单数量.
		$get_undeal_data = $this->offer_model->get_undeal_total($user_id);
		if (!$get_undeal_data){
		    $result = array(
		        'status' =>$this->config->item('app_get_data_fail'),
		        'msg' => $this->lang->line('app_get_data_fail'),
		        'data'=>'',
		    );
		    echo json_encode($result);
		    exit();
		}	
		/* // 判断三天前未处理订单总数.
		if ($get_undeal_data['total'] > 300){
		    $result = array(
		        'status' =>$this->config->item('app_order_more_then_set'),
		        'msg' => $this->lang->line('app_order_more_then_set'),
		        'data'=>'',
		    );
		    echo json_encode($result);
		    exit();
		}	 */	
		// 获取订单详情.
		$get_result = $this->offer_model->get_offer_detail($user_id,$order_id);
		// 获取订单数据失败时.
		if (!$get_result){
			$result = array(
				'status' =>$this->config->item('app_get_data_fail'), 
				'msg' => $this->lang->line('app_get_data_fail'),
				'data'=>'',				
			);
			echo json_encode($result);
			exit();
		}
	
		// 获取订单数据成功,生成数据输出.
		// 订单地址.
		$o_addr = $get_result['province'].$get_result['city'].$get_result['county'].$get_result['xiaoqu'];
		// 订单属性.
		$name_all = $this->config->item('nonstandard_product_type');
		$name = $get_result['title'];
		/*
		foreach($name_all as $k => $v){
			if ($v['type_id'] == $get_result['ftype']){
				$name = $v['type_name'];
				break;
			}
		}
		*/
		// 详细属性.
		switch ($get_result['ftype']){
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
		$oather_value = json_decode($get_result['oather'],TRUE);
		$property = array();
		$index = 1;
		foreach ($property_temp[$get_result['ctype']] as $k=>$v){
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
		$more['aviable'] = ($get_result['isused'] == 1) ? TRUE : FALSE;
		$more['purchase_date'] = $get_result['buydate'];
		$more['price'] = $get_result['s_price'];
		// 图片属性.
		$this->load->helper('url');
		$pic = array();
		$pic_temp = explode(',',$get_result['img']);
		$index = 1;
		$temp = array();
		foreach ($pic_temp as $k => $v){
			$temp['id'] = $k;
			$temp['url'] = base_url($v);
			$pic = array_merge($pic,array($index=>$temp));
			$index++;
		}
		// 支持的服务属性.
		$service = array();
		$service_temp = $this->config->item('cooperator_offer_service');
		$index = 1;
		$temp = array();
		foreach ($service_temp as $k => $v){
			$temp['id'] = $k;
			$temp['describe'] = $v;
			$service = array_merge($service,array($index=>$temp));
			$index++;
		}
		// 获取回收商手机号
		$get_user_phone = $this->offer_model->get_user_status($user_id);
		$special_phone = $this->config->item('cooperator_cell_mail');
		$special_service = $this->config->item('cooperator_special_service');
		if ($get_user_phone['mobile'] == TRUE && in_array($get_user_phone['mobile'],$special_phone)){
		    $service = array_merge($service,array($index=>$special_service));
		}
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg' => $this->lang->line('app_success'),
			'data' => array(
				'order_id' => $order_id,
				'name' => $name,
				'addr' =>$o_addr,
				'property' => $property,
				'more' => $more,
				'pic' => $pic,
				'service' =>$service,
			),	
		);		
		echo json_encode($result);
		exit();
	}
	
	/* 
	 *  功能 : 回收商报价.
	 */
	public function offers(){
	    // 验证请求类型是否为POST
		$this->method_is_post();
		// 验证参数是否为空.
		$param_has_null = ($this->input->post('user_id') && $this->input->post('timestamp')
			&& $this->input->post('imei') && $this->input->post('access_token')
			&& $this->input->post('order_id') && $this->input->post('service')
			&& $this->input->post('my_price') && $this->input->post('lat')
			&& $this->input->post('lng'));
		$this->param_is_null($param_has_null);
		// 获取参数
		$user_id = $this->input->post('user_id');
		$access_token = $this->input->post('access_token');
		$order_id = $this->input->post('order_id');
		$service = $this->input->post('service');
		$my_price = $this->input->post('my_price');
		$remark = $this->input->post('remark');
		$lat = $this->input->post('lat');
		$lng = $this->input->post('lng');
		//$low_price = substr($my_price,0,strpos($my_price,'-'));		
		// 检测参数长度或值是否合法.
		$user_id_len = (strlen($user_id) < 1 || strlen($user_id) > 32);
		$token_len = (strlen($access_token) < 1 || strlen($access_token) > 60);
		$order_id_len = (strlen($order_id) < 1 || strlen($order_id) > 20);
		//$low_price_len = ($low_price == FALSE || $low_price < 0);		
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
		// 获取超过三天的未处理订单数量.
		$get_undeal_data = $this->offer_model->get_undeal_total($user_id);
		if (!$get_undeal_data){
		    $result = array(
		        'status' =>$this->config->item('app_get_data_fail'),
		        'msg' => $this->lang->line('app_get_data_fail'),
		        'data'=>'',
		    );
		    echo json_encode($result);
		    exit();
		}
		/* // 判断三天前未处理订单总数.
		if ($get_undeal_data['total'] > 300){
		    $result = array(
		        'status' =>$this->config->item('app_order_more_then_set'),
		        'msg' => $this->lang->line('app_order_more_then_set'),
		        'data'=>'',
		    );
		    echo json_encode($result);
		    exit();
		} */
        // 验证用户报单状态.
		$get_offer_result = $this->offer_model->get_offer_result($user_id,$order_id);
		// 提示已经报过价.
		if ($get_offer_result){
			$result = array(
				'status' => $this->config->item('app_offer_exist'),
				'msg' => $this->lang->line('app_offer_exist'),
				'data' => '',
			);
			echo json_encode($result);
			exit();
		}
		// 获取订单位置坐标.
		$get_order_location = $this->offer_model->get_order_location($user_id,$order_id);
		// 用户订单已删除.
		if (!$get_order_location){
			$result = array(
				'status' => $this->config->item('app_order_delete'),
				'msg' => $this->lang->line('app_order_delete'),
				'data' => '',
			);
			echo json_encode($result);
			exit();			
		}
		// 固定位置的回收商地址坐标.
		$fixed_addr = $this->config->item('coop_addr_fixed');
		foreach ($fixed_addr as $k => $v){
		    if ($v['user_id'] == $user_id){
		        $lng = $v['lng'];
		        $lat = $v['lat'];
		        $addr = $v['addr'];
		    }
		}
		$order_distance = $this->getDistance($lng,$lat,$get_order_location['lng'],$get_order_location['lat']);
		// 生成用户数据.
		$data = array(
		    'order_name' => $get_order_location['order_name'],		
			'done_times' => $get_order_location['done_sum'],
			'coop_name' => $get_order_location['name'],
			'coop_class' => $get_order_location['class'],
			'coop_auth' => $get_order_location['auth_type'],
		    'coop_addr' => empty($addr) ? $get_order_location['addr'] : $addr,
			'user_id' => $user_id,
			'order_id' => $order_id,
			'my_price' => $my_price,
			'times' => 1,
			'service' => $service,
			'remark' => $remark,
			'status' => 1,
			'distance' => $order_distance,
			//'low_price' => $low_price,
		    'lng' => $lng,
		    'lat' => $lat,
		);
		// 提交报价.
		$get_result = $this->offer_model->add_user_offer($data);
		if ($get_result){
			$status = $this->config->item('app_success');
			$msg = $this->lang->line('app_success');
			$data = '';
			$get_wxuser_info = $this->offer_model->get_wxuser_info($order_id);
			if($get_wxuser_info){
			    // 给微信用户发送微信通知
			    $this->load->model('common/wxcode_model');
			    $temp = sprintf($this->config->item('coop_wxuser_offer_url'),$order_id);
			    $this->load->helper('url');
			    $temp_url =  base_url($temp);
			    $content = sprintf($this->config->item('coop_wxuser_offer_info'),
			        $get_wxuser_info['openid'],$temp_url);
			    $this->wxcode_model->sendmessage($content);
			    // 给微信用户发送短信通知
			    $number = $get_wxuser_info['number'];
			    if (in_array($number,$this->config->item('cooperator_wxuser_msg'))){
			         $this->load->library('message/shortmsg');
			        // 阿里大鱼短信
			        $this->load->library('alidayu/alimsg');			        
			        $this->alimsg=new Alimsg();			        
			        $this->alimsg->mobile=$get_wxuser_info['mobile'];			        
			        $this->alimsg->appkey=$this->config->item('alidayu_appkey');			        
			        $this->alimsg->secret=$this->config->item('alidayu_secretKey');			        
			        $this->alimsg->sign=$this->config->item('alidayu_signname');			        
			        $this->alimsg->template=$this->config->item('APP_alidayu_offer_msg');			        
			        $this->alimsg->content="{\"content\":\"".$get_wxuser_info['title']."\"}";			        
			        $response=$this->alimsg->SendNotice();
			    }
			}
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

/* End of file offer.php */
/* Location: ./application/controllers/cooperation/offer.php */
