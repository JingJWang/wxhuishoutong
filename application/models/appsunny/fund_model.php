<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Fund_model extends CI_Model{
	
	private $table_access_token = 'h_access_token';
//	private $table_sys_set = 'h_sys_set';
//	private $table_feedback = 'h_feedback';
	private $table_cooperator_info = 'h_cooperator_info';
//	private $table_cooperator_money = 'h_cooperator_money';
//	private $table_notice_update = 'h_notice_update';
//	private $table_cooperator_station = 'h_cooperator_station';
//	private $table_will_service = 'h_will_service';
//	private $table_wxuser_comment = 'h_wxuser_comment';
//	private $table_cooperator_comment ='h_cooperator_comment';
	private $table_wxuser = 'h_wxuser';
  	private $table_cooperator_offer = 'h_cooperator_offer';
	private $table_order_statistic = 'h_order_statistic';
//	private $table_trans_cancel = 'h_trans_cancel';
	private $table_order_cancel = 'h_order_cancel';
	private $table_cooperator_comment = 'h_cooperator_comment';
	private $table_order_nonstandard = 'h_order_nonstandard';
	private $table_order_content = 'h_order_content';
	private $table_bill_income = 'h_bill_income';
	private $table_bill_expenses = 'h_bill_expenses';
	private $table_bill_log = 'h_bill_log';
	private $table_cooperator_money = 'h_cooperator_money';
	private $table_cooperator_cash = 'h_cooperator_cash';
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	/*
	 * 账户充值
	 */
	function account_charge($data){
	    // 回收商充值-系统入账
	    $income_data = array(
	        'income_number' =>$data['prepay_number'],
	        'income_orderid' => $data['prepay_number'],
	        'income_userid' => $data['user_id'],
	        'income_type' => $data['pay_type'],
	        'income_totalfee' => $data['amount'],
	        'income_jointime' => time(),
	    );
		// 修改回收商余额.
		$coop_balance = array(
			'money_update_time' => time(),
		);
		$coop_where = array(
			'cooperator_number' => $data['user_id'],
		    'money_status' => 1,
		);
		// 生成回收商收入记录
		$coop_pay_log = array(
			'log_userid' => $data['user_id'],
			'log_total' => $data['amount'],
			'log_title' => '账户充值',
			'log_result' => 1,
			'log_jointime' => time(),
		);
		//开启事物
		$this->db->trans_begin();
		// 更新回收商余额.
		$this->db->set('money_balance','money_balance+'.$data['amount']/100,FALSE);
		$this->db->update($this->table_cooperator_money,$coop_balance,$coop_where);
		$row1 = $this->db->affected_rows();
		// 添加回收商收入日志.
		$this->db->insert($this->table_bill_log,$coop_pay_log);
		$row3 = $this->db->affected_rows();
		// 充值表
		$this->db->insert($this->table_bill_income,$income_data);
		$row2 = $this->db->affected_rows();
		if ($this->db->trans_status() === FALSE || $row1 != 1 || $row2 != 1 || $row3 != 1){
		    // 回滚事务
		    $this->db->trans_rollback();
		    $result = FALSE;
		}
		else{
		    // 提交事物
		    $this->db->trans_commit();
		    $result = TRUE;
		}
		return $result;
	}
	
	/*
	 * 获取用户基本信息和余额
	 */
	function get_balance($user_id){
	    $sql = 'select a.cooperator_name as name,a.cooperator_mobile as mobile,b.money_balance as balance
	        from '.$this->table_cooperator_info.' a, '.$this->table_cooperator_money.' b where
	        a.cooperator_number = '.$user_id.' and a.cooperator_status =1 and b.cooperator_number
	            = '.$user_id.' and b.money_status = 1 ';
	    $query = $this->db->query($sql);
	    $result = $query->row_array();
	    if ($result == TRUE){
	        return $result;
	    }
	    else{
	        return FALSE;
	    }
	}
	
	/*
	 * 添加提现记录
	 */
	function add_cash($data){
	    $data = array(
	        'cooperator_number'=>$data['user_id'],
	        'cooperator_name' => $data['name'],
	        'cooperator_mobile' => $data['mobile'],
	        'cash_account' => $data['account'],
	        'cash_amount' => $data['amount']*100,
	        'cash_join_time' => time(),
	        'cash_status' => 1,
	    );
	    $get_result = $this->db->insert($this->table_cooperator_cash,$data);
	    $row = $this->db->affected_rows();
	    if ($get_result && $row == 1){
	        return TRUE;
	    }
	    else{
	        return FALSE;
	    }
	}
	
	
	
}	