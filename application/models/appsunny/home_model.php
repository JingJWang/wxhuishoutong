<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  系统设置
 */

class Home_model extends CI_Model{
	
	private $table_access_token = 'h_access_token';
//	private $table_sys_set = 'h_sys_set';
//	private $table_feedback = 'h_feedback';
	private $table_cooperator_info = 'h_cooperator_info';
	private $table_cooperator_money = 'h_cooperator_money';
	private $table_notice_update = 'h_notice_update';
	private $table_cooperator_station = 'h_cooperator_station';
	private $table_will_service = 'h_will_service';
	private $table_wxuser_comment = 'h_wxuser_comment';
	private $table_cooperator_comment ='h_cooperator_comment';
	private $table_wxuser = 'h_wxuser';
	private $table_cooperator_recharge = 'h_cooperator_recharge';
	private $table_wxuser_payment = 'h_wxuser_payment';
	private $table_bill_log = 'h_bill_log';
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
    /*
	 *  获取用户首页信息.
	 */
	function get_user_status($user_id){
		// 获取余额总额和补贴总额,系统开关状态,更新状态.
		$sql = 'select a.money_balance as sum,a.money_allowance as allowance,
		b.cooperator_switch as switchs ,c.notice_sys as notice,c.notice_fund 
		as money_update,c.notice_offer as offer_update from '.$this->table_cooperator_money.' a,
		'.$this->table_cooperator_info.' b ,'.$this->table_notice_update.' c
		where a.cooperator_number = '.$user_id.' and b.cooperator_number= '.$user_id.' and c.cooperator_number =
		"'.$user_id.'" and b.cooperator_userstatus=3 and a.money_status = 1 and b.cooperator_status = 1
		    and c.notice_status = 1';
		$result = $this->db->query($sql);
		if (!$result){
			return FALSE;
		}
		else{
			return $result->row_array();			
		}	
	}
	
	/*
	 *  获取用户信息.
	 */	
	function get_user_info($user_id){
		// 获取用户信息.
		$sql = 'select a.cooperator_name as my_name,a.cooperator_pic as my_photo,a.cooperator_opened
		as opend,a.cooperator_cars as car_type,a.cooperator_distance as ranges,a.cooperator_area as area,
		a.cooperator_address as addr_detail,a.cooperator_my_code as my_code,
		    b.service_content as opening,b.service_custom_info as custom from 
		'.$this->table_cooperator_info.' a,'.$this->table_will_service.' b where a.cooperator_number =
		'.$user_id.' and
		b.cooperator_number = '.$user_id.' and a.cooperator_status = 1 and b.service_status = 1 and
		a.cooperator_userstatus =3';
		$result = $this->db->query($sql);
		if (!$result){
			return FALSE;
		}
		else{
			return $result->row_array();			
		}		
	}
	
	/*
	 *  更新用户信息.
	 */	
	function update_user_info($user_id,$data){
		$new_data1 = array(
			'cooperator_pic' => $data['pic_path'],
			'cooperator_name' => $data['name'],
			'cooperator_opened' => $data['opend'],
			'cooperator_area' => $data['area'],
			'cooperator_distance' => $data['service_range'],
			'cooperator_address' => $data['addr_detail'],
		    'cooperator_cars' => $data['car_type'],
			'cooperator_update_time' => time(),
		);
		$new_data2 = array(
			'service_content' => $data['opening'],
			'service_custom_info' => $data['custom'],	
			'service_updatetime' => time(),
		);
		$where1 = array(
			'cooperator_number' => $user_id,
			'cooperator_userstatus' => 3,
			'cooperator_status' => 1,
		);
		$where2 = array(
			'cooperator_number' => $user_id,
			'service_status' => 1,
		);
		// 开启事务
		$this->db->query('BEGIN');
		$result1 = $this->db->update($this->table_cooperator_info,$new_data1,$where1);
		$result2 = $this->db->update($this->table_will_service,$new_data2,$where2);
		if ($result1 && $result2){
			// 提交事务
			$this->db->query('COMMIT');
			$result = TRUE;
		}
		else{
			// 回滚事务
			$this->db->query('ROLLBACK');
			$result = FALSE;
		}
		return $result;
	}
	
	/*
	 *  获取用户资金详情.
	 */
	function get_money_detail($user_id){
	    $sql = 'select log_total as amount,log_title as title,log_jointime as times from '.$this->table_bill_log.'
	        where log_userid = '.$user_id.' and log_result = 1 ';
		$query = $this->db->query($sql);
		$data = $query->result_array();
		if($data){
			return $data;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 *   获取用户认证的基本信息.
	 */
	function get_auth_info($user_id){
		$sql = 'select cooperator_auth_type as auth_type from '.$this->table_cooperator_info.' where
		cooperator_number = "'.$user_id.'" and cooperator_status = 1';
		$query = $this->db->query($sql);
		if($query){
			return $query->row_array();
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 *  更新用户余额及认证状态.
	 */
	function update_cash_auth($user_id,$data){
		$money_data = array(
			'money_balance' => $data['sum'] - $data['sum_auth'],
			'money_update_time' => time(),
		);
		$money_where = array(
			'cooperator_number' => $user_id,
			'money_status' => 1,
		);
		$info_data = array(
			'cooperator_auth_type' => $data['user_auth_type'] + $data['auth_type'],
		);
		$info_where = array(
			'cooperator_number' => $user_id,
			'cooperator_status' => 1,
		);
		$detail_data = array(
			'cooperator_number' => $user_id,
			'detail_op_sum' => $data['sum_auth'],
			'detail_type' => 4,   //类型: 保证金
			'detail_sum' => $data['sum'],
			'detail_join_time' =>time(),
			'detail_status' => 1,
		);
		// 开启事务
		$this->db->query('BEGIN');
		$result1 = $this->db->update($this->table_cooperator_money,$money_data,$money_where);
		$result2 = $this->db->update($this->table_cooperator_info,$info_data,$info_where);
		$result3 = $this->db->insert($this->table_money_detail,$detail_data);
		if ($result1 && $result2 && $result3){
			// 提交事务
			$this->db->query('COMMIT');
			$result = TRUE;
		}
		else{
			// 回滚事务
			$this->db->query('ROLLBACK');
			$result = FALSE;
		}
		return $result;		
	}
	
	/*
	 *  获取用户账户余额
	 */
	function get_my_sum($user_id){
		$sql = 'select money_balance as sum from '.$this->table_cooperator_money.' where cooperator_number
		= "'.$user_id.'" and money_status = 1';
		$query = $this->db->query($sql);
		if($query){
			return $query->row_array();
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 *  发出的评价.
	 */	
	function get_my_comments($user_id,$offset,$page_size){
		$sql = 'select wx_name as name,comment_reason as content,comment_remark as remark,
		    comment_score as class,comment_jointime as datetime from
		'.$this->table_cooperator_comment.' where cooperator_number 
		= '.$user_id.' and  comment_status = 1 order by comment_jointime
		desc limit '.$offset.', '.$page_size.'';
		$query = $this->db->query($sql);
		if($query){
			return $query->result_array();
		}
		else{
			return FALSE;
		}	
	}
	
	/*
	 *  获取用户评价.
	 */
	function get_comments($user_id,$offset,$page_size){

		$sql = 'select a.wx_name as name,b.comment_reason as content,b.comment_remark as remark,
		    b.comment_score as class, b.comment_jointime as datetime
		from '.$this->table_wxuser.' a, '.$this->table_wxuser_comment.' b where a.wx_id = b.wx_id
		and b.cooperator_number = '.$user_id.' and b.comment_status = 1 order by b.comment_jointime
		 desc limit '.$offset.', '.$page_size.'';
		$query = $this->db->query($sql);
		if($query){
			return $query->result_array();
		}
		else{
			return FALSE;
		}		
	}
	
	/*
	 *  注销.
	 */
	function logout($user_id){
	    // 令牌置为-1表示注销.
	    $data = array(
	        'access_token' => -1,
	    );
	    $where = array(
	        'cooperator_number' => $user_id,
	        'access_status' => 1,
	    );
	    $result = $this->db->update($this->table_access_token,$data,$where);
	    if(!$result){
	        return FALSE;
	    }
	    else{
	        return TRUE;
	    }
	}	
		
	/*
	 *  添加用户位置信息.
	 */
	function add_user_position($user_id,$data){
		$new_data = array(
			'cooperator_lng' => $data['lng'],
			'cooperator_lat' => $data['lat'],
			'cooperator_update_time' => time(),
		);
		$where = array(
			'cooperator_number' => $user_id,
			'cooperator_status' => 1,
		);
		$result = $this->db->update($this->table_cooperator_info,$new_data,$where);
                $row  = $this->db->affected_rows();
		if ($result && $row == 1){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

}
