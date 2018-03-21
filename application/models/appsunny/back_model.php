<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 注册模块
 */

class Back_model extends CI_Model{
	
	private $table_cooperator_info = 'h_cooperator_info';
	private $table_cooperator_auth = 'h_cooperator_auth';
	private $table_order_statistic = 'h_order_statistic';
	private $table_cooperator_money = 'h_cooperator_money';
	private $table_notice_update = 'h_notice_update';
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	/*
	 *  获取用户认证信息
	 */
	function get_user_auth($phone){
		$sql = 'select cooperator_userstatus as userstatus,cooperator_number as user_id
		    from '.$this->table_cooperator_info.' 
		where cooperator_mobile = '.$phone.' and cooperator_status = 1';
		$query = $this->db->query($sql);
		$data = $query->row_array();
		if ($data == TRUE){
			return $data;
		}
		else{
			return FALSE;	
		}
		
	}
	
	/*
	 *  更新用户认证信息.
	 */
	function update_user_auth_info($user_id){
		// h_cooperator_info表.
		$date1 = array(
			'cooperator_userstatus' => 3, // 3表示认证通过.
			'cooperator_update_time' => time(),
		);
		$where1 = array(
			'cooperator_number' => $user_id,
			'cooperator_status' => 1,
		);
		// h_order_statistic表
		$date2 = array(
			'statistic_jointime' => time(),
			'cooperator_number' => $user_id,
			'statistic_status' => 1,
		);
		// h_cooperator_money
		$date3 = array(
			'cooperator_number' => $user_id,
			'money_status' => 1,
			'money_join_time' => time(),
		);
		// h_notice_update
		$date4 = array(
			'cooperator_number' => $user_id,
			'notice_join_time' => time(),
			'notice_status' => 1,
		);
		// 开启事物
		$this->db->trans_begin();
		$this->db->update($this->table_cooperator_info,$date1,$where1);
		$a = $this->db->affected_rows($this->table_cooperator_info);
		
		$this->db->insert($this->table_order_statistic,$date2);
		$b = $this->db->affected_rows($this->table_order_statistic);
		
		$this->db->insert($this->table_cooperator_money,$date3);
		$c = $this->db->affected_rows($this->table_cooperator_money);
		
		$this->db->insert($this->table_notice_update,$date4);
		$d = $this->db->affected_rows($this->table_notice_update);
		// 失败回滚
		if ($this->db->trans_status() === FALSE || $a != 1 || $b != 1 || $c != 1 || $d != 1){
			$this->db->trans_rollback();
			$result = FALSE;
		}
		// 成功时提交
		else{
			$this->db->trans_commit();
			$result = TRUE;
		}
		return $result;
	}
	
	
	
}	