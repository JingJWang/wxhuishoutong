<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 注册模块
 */

class Reg_model extends CI_Model{
	
	private $table_access_token = 'h_access_token';
	private $table_cooperator_info = 'h_cooperator_info';
	private $table_verify_code = 'h_verify_code';
	private $table_sys_set = 'h_sys_set';
	private $table_cooperator_auth = 'h_cooperator_auth';
	private $table_order_product = 'h_order_product';
	private $table_will_service = 'h_will_service';
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	/**
	 *  获取用户令牌值,生成时间
	 *  @param  $user_id string 用户编号
	 *  @return $result  array 返回值
	 */
	function get_token($user_id){
		$sql = 'select cooperator_number as user_id,access_token as access_token,access_update_time as
		datetime from '.$this->table_access_token.' where cooperator_number="'.$user_id.'" and access_status = 1';
		$query = $this->db->query($sql);
		// 用户记录不存在
		if ($query->num_rows() != 1){
			return false;
		}
		return $query->row_array();
	}
	
	
	/**
	 *  获取密钥.
	 *  @param  $user_id string 用户编号
	 *  @return $result  string 用户密钥 
	 */	
	function get_access_key($user_id){
		$sql = 'select cooperator_access_key from '.$this->table_cooperator_info.' where
		cooperator_number = '.$user_id.' and cooperator_status = 1';
		$query = $this->db->query($sql);
		if ($query->num_rows() != 1){
			return false;
		}
		$data = $query->row_array();
		return array(
			'access_key' => $data['cooperator_access_key'],
		);
	}
	
	/*
	 *  更新用户令牌.
	 *
	 */
	function update_user_token($data){
	    $new_data = array(
			'access_token' => $data['access_token'],
			'access_update_time' => time(),
		);
		$where = array(
			'cooperator_number' => $data['user_id'],
		);
		$result = $this->db->update($this->table_access_token,$new_data,$where);
		$affected_rows = $this->db->affected_rows($this->table_access_token);
		if ($result && $affected_rows == 1){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 * 登陆时更新令牌.
	 */
	function update_user_token_by_phone($phone_number,$token){
	    $sql = 'select cooperator_number as user_id from '.$this->table_cooperator_info.' 
	        where cooperator_mobile = '.$phone_number.' and cooperator_status = 1';
	    $query = $this->db->query($sql);
	    $data = $query->row_array();
	    if ($data == TRUE){
	        $result = $this->db->update($this->table_access_token, array('access_token' => $token,
	            'access_update_time' => time()),array('cooperator_number' => $data['user_id'])
	        );
	        $row = $this->db->affected_rows();
	        if($result && $row == 1){
	            return TRUE;
	        }
	    }
	    else{
	        return FALSE;
	    }


	}

	 /*
	  * 获取一天发送的短信次数。
	  */
	 function send_times($phone_number){
	     $day_start = strtotime(date('Y-m-d'));
	     $day_end = $day_start + 86400;
	     $sql = 'select count(code_id) as counts from '.$this->table_verify_code.' where code_moblie = '.$phone_number.'
	         and code_type = 3 and code_jointime >= '.$day_start.' and code_jointime < '.$day_end;
	     $query = $this->db->query($sql);
	     $result = $query->row_array();
	     if ($result == TRUE){
	         return $result['counts'];
	     }
	     else{
	         return FALSE;
	     }
	 }
	
	/*
	 * 发送短信验证码结果存入数据库.
	 * @param $data array 发送短信验证码返回的结果集.
	 * @return boolean True,保存到数据库成功,False 保存到数据库失败.
	 */
	 function save_verify_code($data){
		 $result = $this->db->insert($this->table_verify_code,$data);
		 $affect_rows = $this->db->affected_rows($this->table_verify_code);
		 if ($result && ($affect_rows == 1)){
			 return TRUE;
		 }
		 else{
			 return FALSE;
		 }				 
	 }
	 
	 /*
	  *  校验验证码
	  * @param $cell int 手机号
	  * @param $code int 验证码
	  * @return int 0 表示验证码错误.1 表示验证码过期.2 表示验证通过.
	  */
	 function check_code($cell,$code){
		 $sql = 'select code_id,code_jointime from '.$this->table_verify_code. ' where
		 code_moblie = '.$cell.' and code_number = '.$code.' and code_status = 1
		 and response_status="0" order by code_jointime desc limit 1'; 
		 $query = $this->db->query($sql);
		 $result = $query->row_array();
		 if(!$result){
			 return 0;
		 }
		 else{
			 //重置验证码失效.
			$this->db->update($this->table_verify_code,array('code_status'=>-1,'code_updatetime'=>time()),
				array('code_id'=>$result['code_id']));		 
			 if(time() - $result['code_jointime'] > $this->config->item('checkcode_Invalid_time')){
				 return 1;
			 }			 
			 return 2;
		 }
	 }
	 
	 /*
	  * 检验用户是否已经存在.
	  */
	 function check_user_is_exist($phone_number){
		 //获取密钥.
		 $sql = 'select cooperator_number as user_id,cooperator_access_key as access_key
		 from '.$this->table_cooperator_info.' where cooperator_mobile = '.$phone_number.'
		 and cooperator_status = 1';		 
		 $query = $this->db->query($sql);
		 // 不存在
		 if (!$query->row_array()){
			 return FALSE;
		 }
		 // 存在,获取令牌.
		 else{
			 $user_info_data = $query->row_array();
			 $token_data = $this->get_token($user_info_data['user_id']);
			 // 获取失败后返回错误.
			 if (!$token_data){
				 return FALSE;
			 }
			 // 返回用户编号,令牌,密钥.
			 $data = array(
				 'user_id' => $user_info_data['user_id'],
				 'secret_key' => $user_info_data['access_key'],
				 'access_token' => $token_data['access_token'],
			 );
			 return $data;
		 }
	 }
	  
	  /*
	   *  检测用户是否存在注册未完成情况.
	   */
	  private function check_reg_not_success($phone_number){
		  // 查询注册未完成记录.
		 $sql = 'select cooperator_number as user_id from '.$this->table_cooperator_info.' 
		 where cooperator_mobile = "'.$phone_number.'" and cooperator_status = 0';		 
		 $query = $this->db->query($sql);
		 // 没有未完成记录.
		 if (!$query->row_array()){
			 return FALSE;
		 }
		 else{
			 return TRUE;
		 }
	  }
	  
	  /*
	   *  添加回收商信息.
	   */

	  function add_reg_user($data){
		  // 回收商信息
		  $c_info = array(
			  'cooperator_mobile' => $data['phone_number'],
			  'cooperator_join_time' => $data['time'],
			  'cooperator_userstatus' => 0,
			  'cooperator_status' => 0,    //注册完成时置为1.(0表示注册未完成)
		  );
		  $check_reg = $this->check_reg_not_success($data['phone_number']);
		  // 不存在注册未完成情况时.
		  if (!$check_reg){
			  $result = $this->db->insert($this->table_cooperator_info,$c_info);
			  $rows = $this->db->affected_rows($this->table_cooperator_info);			  
		  }
		  // 存在时更新回收商信息.
		  else{
			  $result = $this->db->update($this->table_cooperator_info,$c_info,array(
				  'cooperator_mobile' => $data['phone_number'],
			  ));
			  $rows = $this->db->affected_rows($this->table_cooperator_info);
		  }
		  if ($result && $rows == 1){
			  return TRUE;
		  }
		  else{
			  return FALSE;
		  }
	  } 
	  
	  /*
	   *  添加用户接受许可协议结果.
	   */
	  function add_user_agree($phone_number){
		  $values = array(
			  'cooperator_agree' => 1,
		  );
		  $where  = array(
			  'cooperator_mobile' => $phone_number,
			  'cooperator_status' => 0, //0 表示注册未完成.
		  );
		  $result = $this->db->update($this->table_cooperator_info,$values,$where);
		  //$rows = $this->db->affected_rows($this->table_cooperator_info);
		  if ($result){
			  return TRUE;
		  }
		  return FALSE;
	  }
	 
	 /*
	  *  查询许可协议内容
	  *
	  */
	 function query_agreement_content($set_type){
		 $sql = 'select set_content from '.$this->table_sys_set.' where
		 set_type = "'.$set_type.'" and set_status = 1';
		 $query = $this->db->query($sql);
		 if(!$query->row_array()){
			 return FALSE;
		 }
		 else{
			 $result = $query->row_array();
			 return array(
				 'content' => $result['set_content']);
		 }
	 }
	 
	 /*
	  *   获取用户图片已上传.
	  *
	  */
	 function get_user_auth_img($phone_number){
		 $sql = 'select auth_pic_path,auth_type from '.$this->table_cooperator_auth.' where
		 cooperator_mobile = "'.$phone_number.'" and auth_status = 1';
		 $query = $this->db->query($sql);
		 $result = $query->row_array();
		 // 图片不存在时.
		 if (!$result){
			 return FALSE;
		 }
		 // 返回图片unserialize后的数组字符串.
		 else{
			 return array(
				 'pic_path' => unserialize($result['auth_pic_path']),
				 'auth_type' => $result['auth_type'],
			 );
		 }
	 }
	 
	 
	 
	 /*
	  *  添加用户认证图片.
	  *  @param $data array. 图片类型和路径组成的数组.
	  */ 

	 function save_auth_pic($data){
		 $new_data = array(
			 'cooperator_mobile' =>$data['phone_number'],
			 'auth_type' => $data['auth_type'],
			 'auth_pic_path' => $data['path'],
			 'auth_jointime' => time(),
			 'auth_status' => 1,
		 );
		 $result = $this->db->insert($this->table_cooperator_auth,$new_data);
		 $rows = $this->db->affected_rows($this->table_cooperator_auth);
		 if ($result && ($rows == 1)){
			 return TRUE;
		 }
		 return FALSE;
	 }
	
	/*
	 *  更新用户认证图片
	 */
	function update_auth_pic($data){
		$new_data = array(
			'auth_pic_path' => $data['pic_path'],
			'auth_updatetime' => time(),
		);
		$where = array(
			'cooperator_mobile' => $data['phone_number'],
			'auth_status' => 1,
		);
		$result = $this->db->update($this->table_cooperator_auth,$new_data,$where);
		$rows = $this->db->affected_rows($this->table_cooperator_auth);
		if ($result && ($rows == 1)){
			return TRUE;
		}
		return FALSE;
	}
	
	/*
	 * 获取订单产品类型.
	 */
	function get_product_type(){
		$sql = 'select product_id as id,product_name as name ,product_fid as fid
		from '.$this->table_order_product.' 
		where product_status = 1';
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}
	
	/*
	 *  查看用户是否已经提交过数据.
	 */
	function check_reg_data_exist($phone_number){
		$sql = 'select cooperator_number from '.$this->table_cooperator_info.' where cooperator_mobile = '
		.$phone_number.' and cooperator_status = 1';
		$query = $this->db->query($sql);
		if ($query->row_array() == FALSE){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	
	/*
	 *  更新用户基本信息
	 * @param $data array 用户基本信息数组.
	 */
	function update_user_info($data){
		// 回收商基本信息.
		$info_data = array(
			'cooperator_number' => $data['user_id'],
			'cooperator_access_key' => $data['access_key'],
			'cooperator_name'=>$data['name'],
			'cooperator_opened'=>$data['opend'],
			'cooperator_wx'=>$data['weixin'],
			'cooperator_cell_mobile'=>$data['cell_number'],
			'cooperator_phone'=>$data['tel_number'],
			'cooperator_work_year'=>$data['work_year'],
			'cooperator_work_place'=>$data['has_store'],
			'cooperator_cars'=>$data['car_type'],
			'cooperator_shopaddress'=>$data['shop_addr'],
			'cooperator_sex'=>$data['sex'],
			'cooperator_address'=>$data['addr_detail'],
			'cooperator_userstatus'=>0,
			'cooperator_update_time'=>time(),
			'cooperator_distance' => $data['service_range'],
			'cooperator_status' => 1,
			'cooperator_other_code' =>$data['other_code'],
		);
		$info_where = array('cooperator_mobile'=>$data['phone_number']);
		// 回收商统计信息.
		$other_data = array(
			'cooperator_number' => $data['user_id'],
			'service_content' => $data['opening'],
			'service_custom_info' => $data['custom'],
			'service_jointime' => time(),
			'service_status' => 1,
		);
		$access_data = array(
			'cooperator_number' =>$data['user_id'],
			'access_token' => $data['access_token'],
			'access_join_time' => time(),
			'access_status' => 1,
		);
		// 开启事物
		$this->db->trans_begin();
		$this->db->set('cooperator_my_code','cooperator_id + 143125',FALSE);
		$result1 = $this->db->update($this->table_cooperator_info,$info_data,$info_where);
		$row1 = $this->db->affected_rows($this->table_cooperator_info);
		$result2 = $this->db->insert($this->table_will_service,$other_data);
		$row2 = $this->db->affected_rows($this->table_will_service);
		$result3 = $this->db->insert($this->table_access_token,$access_data);
		$row3 = $this->db->affected_rows($this->table_access_token);
		
		if ($this->db->trans_status() === FALSE || $row1 == 0 || $row2 == 0 || $row3 == 0){
			// 提交事务
			$this->db->trans_rollback();
			$result = FALSE;
		}
		else{
			// 回滚事务
			$this->db->trans_commit();
			$result = TRUE;
		}
		return $result;		
	}
	
}
