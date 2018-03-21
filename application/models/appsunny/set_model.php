<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  系统设置
 */

class Set_model extends CI_Model{
	
	private $table_access_token = 'h_access_token';
	private $table_sys_set = 'h_sys_set';
	private $table_feedback = 'h_feedback';
	private $table_cooperator_info = 'h_cooperator_info';
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	/*
	 * 获取信息内容.
	 */
	function get_content($type){
		$sql = 'select set_content from '.$this->table_sys_set.' where set_type 
		= "'.$type.'" and set_status = 1';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		if ($result == FALSE){
			return FALSE;
		}
		return $result['set_content'];
	}
	
	/* 
	 * 添加反馈.
	 */
	function add_feedback($data){
		$new_data = array(
			'cooperator_number'=>$data['user_id'],
			'feedback_content' => $data['content'],
			'feedback_jointime' => time(),
			'feedback_status'=>1,
		);
		$result = $this->db->insert($this->table_feedback,$new_data);
		$row = $this->db->affected_rows($this->table_feedback);
		if($result == TRUE && $row == 1){
			return TRUE;
		}
		else{
			return FALSE;			
		}

	}
	
	/*
	 *  设置回收开关.
	 */
	function update_switchs($user_id,$switch){
		$data = array(
			'cooperator_switch'=>$switch,
			'cooperator_update_time' =>time(),
		);
		$where = array(
			'cooperator_number'=>$user_id,
			'cooperator_status'=>1,
		);
		$result = $this->db->update($this->table_cooperator_info,$data,$where);
		$row = $this->db->affected_rows($this->table_cooperator_info);
		if ($result == TRUE && $row == 1){
			return TRUE;
		}
		return FALSE;
		
	}
	
	/*
	 *   退出系统
	 */
	function logout($user_id){
		$data = array(
			'access_token' => '',
			'access_update_time' => time(),
		);
		$where = array(
			'cooperator_number' => $user_id,
			'access_status' => 1,
		);
		$result = $this->db->update($this->table_access_token,$data,$where);
		$row = $this->db->affected_rows($this->table_access_token);
		if ($result == TRUE && $row == 1){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

}