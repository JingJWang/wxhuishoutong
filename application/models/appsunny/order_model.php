<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  系统设置
 */

class Order_model extends CI_Model{
	
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
	
	public $test='111111111';
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	/*
	 *   待确认的订单
	 */
	private function get_my_unconfim_order($user_id,$status,$limit,$size){
		$sql = 'select a.order_id,a.offer_join_time as datetime,a.offer_price as price,a.offer_second as second,b.order_prepay as prepay,b.order_name
		as title,b.order_img as pic,c.wx_class as class from '.$this->table_cooperator_offer.' a,
		'.$this->table_order_nonstandard.' b,'.$this->table_wxuser.' c where a.cooperator_number =
		'.$user_id.' and a.offer_order_status =	'.$status.' and a.offer_status = 1 and a.order_id = b.order_number 
		and b.wx_id = c.wx_id order by a.offer_update_time desc limit '.$limit.','.$size.'';
		$query = $this->db->query($sql);
		$data = $query->result_array();
		if ($data == TRUE){
			return $data;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 *   待交易的订单
	 */
	private function get_my_untrans_order($user_id,$status,$limit,$size){
		$sql = 'select a.order_id,a.offer_update_time as datetime,a.offer_price as price,a.offer_second as second,
		b.order_prepay as prepay,b.order_name
		as title,b.order_img as pic,c.wx_class as class from '.$this->table_cooperator_offer.' a,
		'.$this->table_order_nonstandard.' b,'.$this->table_wxuser.' c where a.cooperator_number =
		'.$user_id.' and a.offer_order_status =	'.$status.' and a.offer_status = 1 and a.order_id = b.order_number 
		and b.wx_id = c.wx_id order by a.offer_update_time desc limit '.$limit.','.$size.'';
		$query = $this->db->query($sql);
		$data = $query->result_array();
		if ($data == TRUE){
			return $data;
		}
		else{
			return FALSE;
		}		
	}	
	
	/*
	 *   已报价的订单
	 */
	private function get_my_offered_order($user_id,$status,$limit,$size){
		$sql = 'select a.order_id,a.offer_join_time as datetime,a.offer_distance as distance,
		    a.offer_order_status as order_status,a.offer_second as second,
		a.offer_price as price, b.order_offer_times as offered_time,b.order_prepay as prepay,b.order_name
		as title,b.order_img as pic,c.wx_class as class from '.$this->table_cooperator_offer.' a,
		'.$this->table_order_nonstandard.' b,'.$this->table_wxuser.' c where a.cooperator_number =
		'.$user_id.' and (a.offer_order_status = 1 or a.offer_order_status = 2) and a.offer_status = 
		1 and a.order_id = b.order_number and b.wx_id = c.wx_id order by a.offer_order_status desc,
		a.offer_join_time desc limit '.$limit.','.$size.'';
		$query = $this->db->query($sql);
		$data = $query->result_array();
		if ($data == TRUE){
			return $data;
		}
		else{
			return FALSE;
		}		
	}
	
	/*
	 *   已成交的订单
	 */
	private function get_my_done_order($user_id,$status,$limit,$size){
		$sql = 'select a.order_id,a.offer_update_time as datetime,a.offer_money as deal_price,
		a.offer_comment as comment,b.order_name	as title,b.order_img as pic,b.order_prepay as prepay,c.wx_class as class 
		from '.$this->table_cooperator_offer.' a,
		'.$this->table_order_nonstandard.' b,'.$this->table_wxuser.' c where a.cooperator_number = 
		'.$user_id.' and a.offer_order_status =	'.$status.' and a.offer_status = 1 
		and a.order_id = b.order_number 
		and b.wx_id = c.wx_id order by a.offer_update_time desc limit '.$limit.','.$size.'';
		$query = $this->db->query($sql);
		$data = $query->result_array();
		if ($data == TRUE){
			return $data;
		}
		else{
			return FALSE;
		}		
	}

	/*
	 *   已取消的订单
	 */
	private function get_my_cancel_order($user_id,$status,$limit,$size){
		$sql = 'select a.order_number,a.order_img as pic, a.order_name as title,a.order_prepay as prepay,b.offer_comment as comment,
		c.cancel_cooperator as who,c.cancel_reason as reason,c.cancel_jointime as datetime from
		'.$this->table_order_nonstandard.' a,'.$this->table_cooperator_offer.' b,
		'.$this->table_order_cancel.' c where a.order_number = b.order_id and a.order_number =
		c.order_id and b.cooperator_number = '.$user_id.' and b.offer_order_status = '.$status.' and 
		(c.user_number = '.$user_id.' or c.user_number in (select wx_id from 
		    '.$this->table_order_nonstandard.' a,'.$this->table_cooperator_offer.' b where
		        a.order_number = b.order_id and b.cooperator_number = '.$user_id.')) and c.cancel_status = 1 
		           order by b.offer_update_time desc
		limit '.$limit.','.$size.'';
		$query = $this->db->query($sql);
		$data = $query->result_array();
		if ($data == TRUE){
			return $data;
		}
		else{
			return FALSE;
		}
	}
			
	/*
	 *  我的订单
	 */
	function get_order_list($user_id,$status,$limit,$size){
		switch ($status){
			case 2:
				$result = $this->get_my_unconfim_order($user_id,$status,$limit,$size);
				break;
			case 3:
			    $result = $this->get_my_untrans_order($user_id,$status,$limit,$size);
				break;
			case 1:
			    $result = $this->get_my_offered_order($user_id,$status,$limit,$size);
				break;
			case 4:
			    $result = $this->get_my_done_order($user_id,$status,$limit,$size);
				break;
			case -1:
			    $result = $this->get_my_cancel_order($user_id,$status,$limit,$size);
				break;	
			default:
			    $result = '';
			    break;	
		}
		return $result;
	}
	/*
	 *   获取订单详情信息.
	 */
	function get_order_detail($user_id,$order_id){
		$sql = 'select a.order_ctype as ctype,a.order_province as province,a.order_city as city,a.order_county as 
		county,a.order_residential_quarters as xiaoqu,a.order_house_number as house_number,a.order_ftype as
		ftype,a.order_selling_price as s_price, a.order_isused as isused,a.order_name as title,a.order_prepay as prepay,
		b.electronic_buydate as buydate,b.electronic_oather as oather,b.electronic_img as img,
		c.wx_name as name, c.wx_mobile as mobile,d.offer_order_status as status,d.offer_isagree as agree,d.offer_second as second,
		d.offer_price as my_offer,d.offer_money as my_money,d.offer_service as service,d.offer_times as times 
		from '.$this->table_order_nonstandard.' a,' .$this->table_order_content.' b, '.$this->table_wxuser.'
		c, '.$this->table_cooperator_offer.' d where a.order_number = '.$order_id.' and 
		b.order_id = '.$order_id.' and d.order_id = '.$order_id.' and a.wx_id = c.wx_id
		and d.cooperator_number = '.$user_id.' and a.order_status = 1 and b.electronic_status = 1';
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
	 *   回收商确认订单页面.
	 */
	
	function user_confim($user_id,$order_id){
		$data = array(
			'offer_order_status' => 3,
			'offer_update_time' => time(),
		);
		$where = array(
		    'cooperator_number' => $user_id,
			'order_id' => $order_id,
			'offer_status' => 1,
		);
		$user_data = array(
		    'order_orderstatus' => 3,
		    'order_updatetime' => time(),
		);
		$user_where = array(
		    'order_number' => $order_id,
		    'order_status' => 1,
		);
		$result = $this->db->update($this->table_cooperator_offer,$data,$where);
		$row = $this->db->affected_rows($this->table_cooperator_offer);
		$result2 = $this->db->update($this->table_order_nonstandard,$user_data,$user_where);
		$row2 = $this->db->affected_rows($this->table_order_nonstandard);
		if ($result == TRUE && $row == 1 && $result2 == TRUE && $row2 == 1 ){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 *  获取第一次报价.
	 */
	function get_first_offer($order_id){
		$sql = 'select a.order_id as order_id,a.offer_money as first_offer,a.offer_times as times,b.order_prepay
		    as prepay from '.$this->table_cooperator_offer.' a, '.$this->table_order_nonstandard.' b
		 where a.order_id = "'.$order_id.'"  and b.order_number = '.$order_id.' and b.order_prepay = 1 and a.offer_times = 1 and 
		     a.offer_order_status = 3 and a.offer_status = 1';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		if($result){
			return $result;
		}
		else{
			return FALSE;
		}
	}
	
	/*
	 *  修改报价
	 */
	function modify_price($data){
		// 更新订单统计数据.
		$new_data1 = array(
			'statistic_updatetime' => time(),
		);
		$where1 = array(
			'cooperator_number' => $data['user_id'],
			'statistic_status' => 1,
		);
		// 修改报价数据.
		$new_data2 = array(
			'offer_second' => $data['second_offer'],
			'offer_update_time' => time(),
			'offer_times' => 2,
		);
		$where2 = array(
			'order_id' => $data['order_id'],
			'offer_status' => 1,
			'cooperator_number' => $data['user_id'],
		);
		// 开启事物
		$this->db->trans_begin();

		$this->db->set('statistic_down','statistic_down + "'.$data['down'].'"',FALSE);
		$this->db->set('statistic_up','statistic_up + "'.$data['up'].'"',FALSE);
		$this->db->update($this->table_order_statistic,$new_data1,$where1);
		$a = $this->db->affected_rows($this->table_order_statistic);

		$this->db->update($this->table_cooperator_offer,$new_data2,$where2);
		$b = $this->db->affected_rows($this->table_cooperator_offer);

		if ($this->db->trans_status() === FALSE || $a == 0 || $b == 0 ){
			// 回滚事务
			$this->db->trans_rollback();
			$result = FALSE;
		}
		else{
			// 提交事务
			$this->db->trans_commit();
			$result = TRUE;
		}
		return $result;	
	}
	
	/*
	 *  获取订单报价状态
	 */
	function get_offer_status($user_id,$order_id){
		$sql = 'select a.wx_id as wx_id,a.order_prepay as prepay,b.offer_order_status as status,b.offer_price as amount from 
		    '.$this->table_order_nonstandard.' a, '.$this->table_cooperator_offer.' b
		where a.order_status = 1 and a.order_number = '.$order_id.' and b.cooperator_number = '.$user_id.'
		    and b.order_id = '.$order_id.' and b.offer_status = 1';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		if ($result == FALSE){
			return FALSE;
		}
		else{
			if ($result['status'] != 3){
				return FALSE;
			}
			return $result;
		}
	}
	
	/*
	 *  取消订单
	 */
	function update_offer_status($data){
		// 订单表状态数据
		$offer_data = array(
			'offer_order_status' => -1,
			'offer_update_time' => time(),
		);
		$offer_where = array(
			'order_id' => $data['order_id'],
			'offer_status' => 1,
		);
		// 取消日志表数据.
		$cancel_data = array(
		    'user_number' => $data['user_id'],
			'order_id' => $data['order_id'],
			'cancel_reason' => $data['reason'],
			'cancel_remark' => $data['reason_more'],
			'cancel_jointime' => time(),
			'cancel_status' => 1,
			'cancel_cooperator' => 1,
		);
		// 订单统计表数据
		$statistic_data  = array(
			'statistic_updatetime' => time(),
		);
		$statistic_where = array(
			'cooperator_number' => $data['user_id'],
			'statistic_status' => 1, 
		);
		$order_data = array(
			'order_updatetime' => time(),
		    'order_orderstatus' => -1
		);
		$order_where = array(
			'order_number' => $data['order_id'],
			'order_status' => 1,
		);
		// 开启事物
		$this->db->trans_begin();
		// 订单表更新.
		$this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
		$a = $this->db->affected_rows($this->table_cooperator_offer);
		// 添加取消日志
		$this->db->insert($this->table_order_cancel,$cancel_data);
		$b = $this->db->affected_rows($this->table_order_cancel);
		// 更新统计表.
		$this->db->set('statistic_cancel','statistic_cancel + 1',FALSE);
		$this->db->update($this->table_order_statistic,$statistic_data,$statistic_where);
		$c = $this->db->affected_rows($this->table_order_statistic);
		// 更新订单表.
		$this->db->set('order_cancel_times','order_cancel_times + 1',FALSE);
		$this->db->update($this->table_order_nonstandard,$order_data,$order_where);
		$d = $this->db->affected_rows($this->table_order_nonstandard);
		if ($this->db->trans_status() === FALSE || $a != 1 || $b != 1 || $c != 1 || $d != 1){
			// 回滚事务
			$this->db->trans_rollback();
			$result = FALSE;
		}
		else{
			// 提交事务
			$this->db->trans_commit();
			$result = TRUE;
		}
		return $result;
	}
	
	/*
	 * 预支付退款
	 */
	function money_back($data){
	    // 用户资金修改
	    $wx_data = array(
	        'wx_updatetime' => date('Y-m-d H:i:s'),
	    );
	    $wx_where = array(
	        'wx_id' => $data['wx_id'],
	        'wx_status' => 1,
	    );
	    // 用户资金变动日志
	    $wx_log = array(
	        'log_userid' => $data['wx_id'],
	        'log_total' => $data['amount'] * 100,
	        'log_title' => '取消订单退款',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    // 回收商资金修改
	    $coop_data = array(
	        'money_update_time' => time(),
	    );
	    $coop_where = array(
	        'cooperator_number' => $data['user_id'],
	        'money_status' => 1,
	    );
	    // 回首商资金变动日志
	    $coop_log = array(
	        'log_userid' => $data['user_id'],
	        'log_total' => $data['amount'] * 100,
	        'log_title' => '取消订单退款',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    // 开启事物
	    $this->db->trans_begin();
	    // 更新微信用户余额
	    $this->db->set('wx_freeze_balance','wx_freeze_balance-'.$data['amount']*100,FALSE);
	    $this->db->update($this->table_wxuser,$wx_data,$wx_where);
	    $a = $this->db->affected_rows();
	    // 生成微信用户余额变动日志
	    $this->db->insert($this->table_bill_log,$wx_log);
	    $b = $this->db->affected_rows();
	    // 更新回收商余额
	    $this->db->set('money_balance','money_balance+'.$data['amount'],FALSE);
	    $this->db->update($this->table_cooperator_money,$coop_data,$coop_where);
	    $c = $this->db->affected_rows();
	    // 生成回收商余额日志。
	    $this->db->insert($this->table_bill_log,$coop_log);
	    $d = $this->db->affected_rows();
	    if ($this->db->trans_status() === FALSE || $a != 1 || $b != 1 || $c != 1 || $d != 1){
	        // 回滚事务
	        $this->db->trans_rollback();
	        $result = FALSE;
	    }
	    else{
	        // 提交事务
	        $this->db->trans_commit();
	        $result = TRUE;
	    }
	    return $result;
	}
	
	/*
	 * 查询回收商是否是有用户确认的订单.
	 */
	function check_order_info($user_id,$order_id){
	    $sql = 'select a.offer_price as price,a.offer_coop_name as name,a.offer_order_name as order_name 
	        from '.$this->table_cooperator_offer.' a, '.$this->table_order_nonstandard.' b
	        where b.order_number = '.$order_id.' and b.order_orderstatus = 2 and a.cooperator_number = '.$user_id.' 
	        and a.order_id = '.$order_id.' and a.offer_order_status = 2 and a.offer_status = 1';
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
	 * 查询回收商是否是有用户确认的订单.
	 */
	function check_order_infos($user_id,$order_id){
	    $sql = 'select a.offer_price as price,a.offer_coop_name as name,a.offer_order_name as order_name,
	        a.offer_order_status as order_status
	        from '.$this->table_cooperator_offer.' a, '.$this->table_order_nonstandard.' b
	        where b.order_number = '.$order_id.' and a.cooperator_number = '.$user_id.'
	        and a.order_id = '.$order_id.' and a.offer_status = 1';
	    $query = $this->db->query($sql);
	    $result = $query->row_array();
	    if ($result == TRUE){
	        return $result;
	    }
	    else{
	        return FALSE;
	    }
	}
	
	/* 保存预支付信息
	 * 
	 */	 
	 function prepay_done($user_id,$order_id,$pay_number,$amount,$pay_type){
		 $data = array(
			 'income_number' =>$pay_number,
			 'income_orderid' => $order_id,
			 'income_userid' => $user_id,
			 'income_type' => $pay_type,
			 'income_totalfee' => $amount * 100,
			 'income_jointime' => time(),
		     'income_result'=>1
		 );
		 $result = $this->db->insert($this->table_bill_income,$data);
		 $row = $this->db->affected_rows();
		 if ($result && $row == 1){
			 return TRUE;
		 }
		 else{
			return FALSE; 
		 }
	 }
	 
	/*
	 * 获取回收商支付金额.
	 */
	function get_pay_fee($user_id,$order_id){
		// 获取订单金额
		$sql = 'select offer_price as fee from '.$this->table_cooperator_offer.' where cooperator_number = 
		    '.$user_id.' and order_id = '.$order_id.' and offer_status = 1';
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
	 *  余额预支付
	 */
	function balance_prepay($user_id,$wxid,$order_id){
		$pay_fee = $this->get_pay_fee($user_id,$order_id);
		if ($pay_fee == FALSE){
			return FALSE;
		}
		// 修改回收商余额.
		$coop_balance = array(
			'money_update_time' => time(),
		);
		$coop_where = array(
			'cooperator_number' => $user_id,
		);
		// 修改微信用户冻结金额
		$wx_freeze_balance = array(
			'wx_updatetime' => time(),
		);
		$wx_where = array(
			'wx_id' => $wxid,
		);
		// 主订单表
		$main_order_data = array(
		    'order_orderstatus' => 3,
		    'order_prepay' => 1,
		    'order_updatetime' => time(),
		);
		$main_where = array(
		    'wx_id' => $wxid,
		    'order_number' => $order_id,
		);
		// 回收商订单表
		$coop_order_data = array(
		    'offer_order_status' => 3,
		    'offer_update_time' => time(),
		);
		$coop_order_where = array(
		    'cooperator_number' => $user_id,
		    'order_id' => $order_id,
		);
		// 生成回收商支出记录
		$coop_pay_log = array(
			'log_userid' => $user_id,
			'log_total' => $pay_fee['fee'] * 100,
			'log_title' => '订单预支出',
			'log_result' => 1,
			'log_jointime' => time(),
		);
		// 生成微信用户的收入记录.
		$wx_income_log = array(
			'log_userid' => $wxid,
			'log_total' => $pay_fee['fee'] * 100,
			'log_title' => '订单冻结收入',
			'log_result' => 1,
			'log_jointime' => time(),
		);
		//开启事物
		$this->db->trans_begin();
		// 更新回收商余额.
		$this->db->set('money_balance','money_balance-'.$pay_fee['fee'],FALSE);
		$this->db->update($this->table_cooperator_money,$coop_balance,$coop_where);
		$row1 = $this->db->affected_rows();
		// 更新用户冻结余额
		$this->db->set('wx_freeze_balance','wx_freeze_balance+'.$pay_fee['fee']*100,FALSE);
		$this->db->update($this->table_wxuser,$wx_freeze_balance,$wx_where);
		$row2 = $this->db->affected_rows();
		// 添加回收商支出日志.
		$this->db->insert($this->table_bill_log,$coop_pay_log);
		$row3 = $this->db->affected_rows();
		// 添加用户收入日志
		$this->db->insert($this->table_bill_log,$wx_income_log);
		$row4 = $this->db->affected_rows();
		// 更新主订单表状态。
		$this->db->update($this->table_order_nonstandard,$main_order_data,$main_where);
		$row5 = $this->db->affected_rows();
		// 更新回收商订单表状态。
		$this->db->update($this->table_cooperator_offer,$coop_order_data,$coop_order_where);
		$row6 = $this->db->affected_rows();
		if ($this->db->trans_status() === FALSE || $row1 != 1 || $row2 != 1 || $row3 != 1 || $row4 != 1
		    || $row5 != 1 || $row6 != 1){
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
	 *  各种宝预支付
	 */ 
	function bao_prepay($user_id,$wxid,$order_id,$pay_type,$prepay_number){
		$pay_fee = $this->get_pay_fee($user_id,$order_id);
		if ($pay_fee == FALSE){
			return FALSE;
		}
		//生成用户冻结资金
		$freeze_data = array(
			'wx_updatetime' => time(),
		);
		$freeze_where = array(
			'wx_status' => 1,
			'wx_id' => $wxid,
		);
	    // 回收商充值-系统入账
	    $income_data = array(
	        'income_number' =>$prepay_number,
	        'income_orderid' => $order_id,
	        'income_userid' => $user_id,
	        'income_type' => $pay_type,
	        'income_totalfee' => $pay_fee['fee'] * 100,
	        'income_jointime' => time(),
	    );
		// 主表状态
		$main_order_data = array(
		    'order_orderstatus' => 3,
		    'order_prepay' => 1,
		    'order_updatetime' => time(),
		);
		$main_where = array(
		    'wx_id' => $wxid,
		    'order_number' => $order_id,
		);
		// 回收商表状态
		$coop_order_data = array(
		    'offer_money' => $pay_fee['fee'],
		    'offer_order_status' => 3,
		    'offer_update_time' => time(),
		);
		$coop_order_where = array(
		    'cooperator_number' => $user_id,
		    'order_id' => $order_id,
		);
		// 回收商充值记录
		$coop_charge_log = array(
			'log_userid' => $user_id,
			'log_total' => $pay_fee['fee'] * 100,
			'log_title' => '订单预充值',
			'log_result' => 1,
			'log_jointime' => time(),
		);
		// 回收商支出记录
		$coop_pay_log = array(
			'log_userid' => $user_id,
			'log_total' => $pay_fee['fee'] * 100,
			'log_title' => '订单预支出',
			'log_result' => 1,
			'log_jointime' => time(),
		);
		// 用户收入记录
		$wx_log = array(
			'log_userid' => $wxid,
			'log_total' => $pay_fee['fee'] * 100,
			'log_title' => '冻结收入',
			'log_result' => 1,
			'log_jointime' => time(),
		);
		// 开启事物
		$this->db->trans_begin();
		$this->db->set('wx_freeze_balance','wx_freeze_balance+'.$pay_fee['fee']*100,FALSE);
		$this->db->update($this->table_wxuser,$freeze_data,$freeze_where);
		$row1 = $this->db->affected_rows();
		// 充值表
		$this->db->insert($this->table_bill_income,$income_data);
		$row2 = $this->db->affected_rows();
		// 日志
		$this->db->insert($this->table_bill_log,$coop_charge_log);
		$row3 = $this->db->affected_rows();
		$this->db->insert($this->table_bill_log,$coop_pay_log);
		$row4 = $this->db->affected_rows();
		$this->db->insert($this->table_bill_log,$wx_log);
		$row5 = $this->db->affected_rows();
		$this->db->update($this->table_order_nonstandard,$main_order_data,$main_where);
		$row6 = $this->db->affected_rows();
		$this->db->update($this->table_cooperator_offer,$coop_order_data,$coop_order_where);
		$row7 = $this->db->affected_rows();
		if ($this->db->trans_status() === FALSE || $row1 != 1 || $row2 != 1 || $row3 != 1 || $row4 != 1 ||
		    $row5 != 1 || $row6 != 1 || $row7 != 1){
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
	 * 获取回收商余额信息
	 */
	function get_balance($user_id,$order_id){
		$sql = 'select a.money_balance as balance, b.offer_money as fee from '.$this->table_cooperator_money.' a, 
		'.$this->table_cooperator_offer.' b where a.cooperator_number = '.$user_id.' and a.money_status = 1 and 
		b.cooperator_number = '.$user_id.' and b.order_id = '.$order_id.' and b.offer_status = 1';
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
	 * 查询回收商待交易的订单信息.
	 */
	function get_order_info($user_id,$order_id){
	    $sql = 'select a.offer_coop_name as name,a.offer_order_name as order_name,a.offer_times as times,b.order_prepay as prepay,
	        a.offer_money as first,a.offer_second as second,a.offer_times as times,a.offer_isagree as agree,a.offer_price as price
	        from '.$this->table_cooperator_offer.' a, '.$this->table_order_nonstandard.' b
	        where a.cooperator_number = '.$user_id.' and a.order_id = '.$order_id.' and a.offer_order_status = 3 and
	            a.offer_status = 1 and b.order_number = '.$order_id.' and b.order_status = 1';
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
	 * 查询回收商待交易的订单信息.
	 */
	function get_order_infos($user_id,$order_id){
	    $sql = 'select a.offer_coop_name as name,a.offer_order_name as order_name,a.offer_times as times,b.order_prepay as prepay,
	        a.offer_money as first,a.offer_second as second,a.offer_times as times,a.offer_isagree as agree,a.offer_price as price,
	        a.offer_order_status as order_status from '.$this->table_cooperator_offer.' a, '.$this->table_order_nonstandard.' b
	        where a.cooperator_number = '.$user_id.' and a.order_id = '.$order_id.' and
	            a.offer_status = 1 and b.order_number = '.$order_id.' and b.order_status = 1';
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
	 * get_pre_info
	 */
	function get_pre_info($user_id,$order_id){
	    $sql = 'select income_number as prepay_number from '.$this->table_bill_income.' where income_userid = 
	        '.$user_id.' and income_orderid = '.$order_id.' and income_result = 1';
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
	 * 保存余额及线下支付的金额数
	 */
	function save_amount($user_id,$order_id,$amount){
	    $data = array(
	        'offer_money' => $amount,
                'offer_update_time' => time(),
	    );
	    $where = array(
	        'cooperator_number' => $user_id,
	        'order_id' => $order_id,
	        'offer_status' => 1,
	    );
	    $result = $this->db->update($this->table_cooperator_offer,$data,$where);
	    $row = $this->db->affected_rows();
	    if ($result == TRUE && $row == 1){
	        return TRUE;
	    }
	    else{
	        return FALSE;
	    }	    
	}
	
	/*
	 *  完成订单交易
	 */
	function order_done($user_id,$order_id,$prepay_number,$amount,$pay_type,$remark){
		// 用户订单表
		$order_data = array(
		  'cooperator_number' => $user_id,
//			'order_orderstatus' => 10,
			'order_bid_price' => $amount,
		    'order_pay_type' => $pay_type,
			'order_updatetime' => time(),
		);
		$order_where = array(
			'order_number' => $order_id,
			'order_status' => 1,
		);
		// 回收商订单表.
		$offer_data = array(
			'offer_money' => $amount,
			'offer_remark' => $remark,
//			'offer_order_status' => 4,
			'offer_update_time' => time(),
		);
		$offer_where = array(
		    'cooperator_number' => $user_id,
			'order_id' => $order_id,
			'offer_status' => 1,
		);
		$income_data = array(
		    'income_number' =>$prepay_number,
		    'income_orderid' => $order_id,
		    'income_userid' => $user_id,
		    'income_type' => $pay_type,
		    'income_totalfee' => $amount * 100,
		    'income_jointime' => time(),
		);
		// 统计信息表
// 		$tj_data = array(
// 			'statistic_updatetime' => time(),
// 		);
// 		$tj_where = array(
// 			'cooperator_number' => $user_id,
// 			'statistic_status' => 1,
// 		);
		// 开启事物
		$this->db->trans_begin();
		// 更新用户订单表.
		$this->db->update($this->table_order_nonstandard,$order_data,$order_where);
		$a = $this->db->affected_rows();
		//更新回收商订单表.
		$this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
		$b = $this->db->affected_rows();
		// income
		$this->db->insert($this->table_bill_income,$income_data);
		$c = $this->db->affected_rows();
// 		//更新统计表
// 		$this->db->set('statistic_sum','statistic_sum + 1',FALSE);
// 		$this->db->update($this->table_order_statistic,$tj_data,$tj_where);
// 		$c = $this->db->affected_rows($this->table_order_statistic);
		if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1){
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
	 * 预支付价格刚好。
	 */
	function order_pay_a($pay_data){
	    // 用户订单表
	    $order_data = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_bid_price' => $pay_data['price'],
	        'order_pay_type' => $pay_data['pay_type'],
	        'ordre_dealtime' => time(),
	        'order_orderstatus' => 10,
	        'order_updatetime' => time(),
	    );
	    $order_where = array(
	        'order_number' => $pay_data['order_id'],
	        'order_status' => 1,
	    );
	    // 回收商订单表.
	    $offer_data = array(
	        'offer_money' => $pay_data['price'],
	        'offer_remark' => $pay_data['remark'],
	        'offer_order_status' => 4,
	        'offer_update_time' => time(),
	    );
	    $offer_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_id' => $pay_data['order_id'],
	        'offer_status' => 1,
	    );
	    //统计信息表
	    $tj_data = array(
	        'statistic_updatetime' => time(),
	    );
	    $tj_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'statistic_status' => 1,
	    );
	    // 冻结转余额
	    $wx_data = array(
	        'wx_updatetime' => time(),
	    );
	    $wx_where = array(
	        'wx_id' => $pay_data['wx_id'],
	        'wx_status' => 1,
	    );
	    // 用户日志
	    $log_data = array(
	        'log_userid' => $pay_data['wx_id'],
	        'log_total' => $pay_data['price'] * 100,
	        'log_title' => '冻结转余',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    //开启事物
	    $this->db->trans_begin();
	    // 更新用户订单表.
	    $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
	    $a = $this->db->affected_rows($this->table_order_nonstandard);
	    //更新回收商订单表.
	    $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
	    $b = $this->db->affected_rows($this->table_cooperator_offer);
	    //更新统计表
	    $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
	    $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
	    $c = $this->db->affected_rows($this->table_order_statistic);
	    // 冻结资金转余额
	    $this->db->set('wx_freeze_balance','wx_freeze_balance -'.$pay_data['price']*100,FALSE);
	    $this->db->set('wx_balance','wx_balance+'.$pay_data['first']*100,FALSE);
	    $this->db->update($this->table_wxuser,$wx_data,$wx_where);
	    $d = $this->db->affected_rows();
	    // 冻结资金转余额日志
	    $this->db->insert($this->table_bill_log,$log_data);
	    $e = $this->db->affected_rows();
	    // log wx 
	    if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1 || $d != 1 || $e != 1){
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
	
	// 预支付有结余
	function order_pay_b($pay_data){
	    // 用户订单表
	    $order_data = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_bid_price' => $pay_data['second'],
	        'order_pay_type' => $pay_data['pay_type'],
	        'ordre_dealtime' => time(),
	        'order_orderstatus' => 10,
	        'order_updatetime' => time(),
	    );
	    $order_where = array(
	        'order_number' => $pay_data['order_id'],
	        'order_status' => 1,
	    );
	    // 回收商订单表.
	    $offer_data = array(
	        'offer_money' => $pay_data['second'],
	        'offer_remark' => $pay_data['remark'],
	        'offer_order_status' => 4,
	        'offer_update_time' => time(),
	    );
	    $offer_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_id' => $pay_data['order_id'],
	        'offer_status' => 1,
	    );
	    //统计信息表
	    $tj_data = array(
	        'statistic_updatetime' => time(),
	    );
	    $tj_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'statistic_status' => 1,
	    );
	    // 冻结转余额
	    $wx_data = array(
	        'wx_updatetime' => time(),
	    );
	    $wx_where = array(
	        'wx_id' => $pay_data['wx_id'],
	        'wx_status' => 1,
	    );
	    // 用户日志
	    $log_data = array(
	        'log_userid' => $pay_data['wx_id'],
	        'log_total' => $pay_data['second'] * 100,
	        'log_title' => '冻结转余',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    // 回首商余额
	    $coop_balance_data = array(
	        'money_update_time' => time(),
	    );
	    $coop_balance_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'money_status' => 1,
	    );
	    // 回收商余额收入日志
	    $coop_log = array(
	        'log_userid' => $pay_data['user_id'],
	        'log_total' => ($pay_data['price'] - $pay_data['second']) * 100,
	        'log_title' => '预支付余额收入',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    //开启事物
	    $this->db->trans_begin();
	    // 更新用户订单表.
	    $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
	    $a = $this->db->affected_rows($this->table_order_nonstandard);
	    //更新回收商订单表.
	    $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
	    $b = $this->db->affected_rows($this->table_cooperator_offer);
	    //更新统计表
	    $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
	    $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
	    $c = $this->db->affected_rows($this->table_order_statistic);
	    // 冻结资金转余额
	    $this->db->set('wx_freeze_balance','wx_freeze_balance -'.$pay_data['price']*100,FALSE);
	    $this->db->set('wx_balance','wx_balance+'.$pay_data['second']*100,FALSE);
	    $this->db->update($this->table_wxuser,$wx_data,$wx_where);
	    $d = $this->db->affected_rows();
	    // 冻结资金转余额日志
	    $this->db->insert($this->table_bill_log,$log_data);
	    $e = $this->db->affected_rows();
	    // 回首商预支付余额收入
	    $this->db->set('money_balance','money_balance+'.($pay_data['price']-$pay_data['second']),FALSE);
	    $this->db->update($this->table_cooperator_money,$coop_balance_data,$coop_balance_where);
	    $f = $this->db->affected_rows();
	    // 回收商收入日志
	    $this->db->insert($this->table_bill_log,$coop_log);
	    $g = $this->db->affected_rows();
	    if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1 || $d != 1 || $e != 1
	        || $f != 1 || $g != 1){
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
	 * 二次支付。
	 */
	function order_pay_c($pay_data){
	    // 二次支付的余额支付
        if ($pay_data['pay_type'] == 4){
            // 用户订单表
            $order_data = array(
                'cooperator_number' => $pay_data['user_id'],
                'order_bid_price' => $pay_data['second'],
                'order_pay_type' => $pay_data['pay_type'],
                'ordre_dealtime' => time(),
                'order_orderstatus' => 10,
                'order_updatetime' => time(),
            );
            $order_where = array(
                'order_number' => $pay_data['order_id'],
                'order_status' => 1,
            );
            // 回收商订单表.
            $offer_data = array(
                'offer_money' => $pay_data['second'],
                'offer_remark' => $pay_data['remark'],
                'offer_order_status' => 4,
                'offer_update_time' => time(),
            );
            $offer_where = array(
                'cooperator_number' => $pay_data['user_id'],
                'order_id' => $pay_data['order_id'],
                'offer_status' => 1,
            );
            //统计信息表
            $tj_data = array(
                'statistic_updatetime' => time(),
            );
            $tj_where = array(
                'cooperator_number' => $pay_data['user_id'],
                'statistic_status' => 1,
            );
            // 用户余额
            $wx_data = array(
                'wx_updatetime' => time(),
            );
            $wx_where = array(
                'wx_id' => $pay_data['wx_id'],
                'wx_status' => 1,
            );
            // 用户日志
            $log_data = array(
                'log_userid' => $pay_data['wx_id'],
                'log_total' => $pay_data['first'] * 100,
                'log_title' => '订单收入',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            // 回首商余额
            $coop_balance_data = array(
                'money_update_time' => time(),
            );
            $coop_balance_where = array(
                'cooperator_number' => $pay_data['user_id'],
                'money_status' => 1,
            );
            // 回收商余额支出日志
            $coop_log = array(
                'log_userid' => $pay_data['user_id'],
                'log_total' => $pay_data['first'] * 100,
                'log_title' => '订单支出',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            // 冻结转余额
            $wx_freeze_data = array(
                'wx_updatetime' => time(),
            );
            $wx_freeze_where = array(
                'wx_id' => $pay_data['wx_id'],
                'wx_status' => 1,
            );
            // 冻结转余额日志
            $ffreeze_log_data = array(
                'log_userid' => $pay_data['wx_id'],
                'log_total' => $pay_data['price'] * 100,
                'log_title' => '冻结转余',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            //开启事物
            $this->db->trans_begin();
            // 更新用户订单表.
            $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
            $a = $this->db->affected_rows($this->table_order_nonstandard);
            //更新回收商订单表.
            $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
            $b = $this->db->affected_rows($this->table_cooperator_offer);
            //更新统计表
            $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
            $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
            $c = $this->db->affected_rows($this->table_order_statistic);
            // 订单收入
            $this->db->set('wx_balance','wx_balance+'.$pay_data['first']*100,FALSE);
            $this->db->update($this->table_wxuser,$wx_data,$wx_where);
            $d = $this->db->affected_rows();
            // 二次收入日志
            $this->db->insert($this->table_bill_log,$log_data);
            $e = $this->db->affected_rows();
	       // 冻结资金转余额
	       $this->db->set('wx_freeze_balance','wx_freeze_balance -'.$pay_data['price']*100,FALSE);
	       $this->db->set('wx_balance','wx_balance+'.$pay_data['price']*100,FALSE);
	       $this->db->update($this->table_wxuser,$wx_freeze_data,$wx_freeze_where);
	       $h = $this->db->affected_rows();
	       // 冻结资金转余额日志
	       $this->db->insert($this->table_bill_log,$ffreeze_log_data);
	       $i = $this->db->affected_rows();           
            // 回首商余额变动
            $this->db->set('money_balance','money_balance-'.$pay_data['first'],FALSE);
            $this->db->update($this->table_cooperator_money,$coop_balance_data,$coop_balance_where);
            $f = $this->db->affected_rows();
            // 回收商支出日志
            $this->db->insert($this->table_bill_log,$coop_log);
            $g = $this->db->affected_rows();
            if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1 || $d != 1 || $e != 1
                || $f != 1 || $g != 1 || $h != 1 || $i != 1){
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
        //二次支付的宝支付
        else{
            // 用户订单表
            $order_data = array(
                'cooperator_number' => $pay_data['user_id'],
                'order_bid_price' => $pay_data['second'],
                'order_pay_type' => $pay_data['pay_type'],
                'ordre_dealtime' => time(),
                'order_orderstatus' => 10,
                'order_updatetime' => time(),
            );
            $order_where = array(
                'order_number' => $pay_data['order_id'],
                'order_status' => 1,
            );
            // 回收商订单表.
            $offer_data = array(
                'offer_money' => $pay_data['second'],
                'offer_remark' => $pay_data['remark'],
                'offer_order_status' => 4,
                'offer_update_time' => time(),
            );
            $offer_where = array(
                'cooperator_number' => $pay_data['user_id'],
                'order_id' => $pay_data['order_id'],
                'offer_status' => 1,
            );
            //统计信息表
            $tj_data = array(
                'statistic_updatetime' => time(),
            );
            $tj_where = array(
                'cooperator_number' => $pay_data['user_id'],
                'statistic_status' => 1,
            );
            // 支付到用户余额
            $wx_data = array(
                'wx_updatetime' => time(),
            );
            $wx_where = array(
                'wx_id' => $pay_data['wx_id'],
                'wx_status' => 1,
            );
            // 用户日志
            $log_data = array(
                'log_userid' => $pay_data['wx_id'],
                'log_total' => $pay_data['amount'],
                'log_title' => '订单收入',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            // 回收商充值-系统入账
            $income_data = array(
                'income_number' =>$pay_data['prepay_number'],
                'income_orderid' => $pay_data['order_id'],
                'income_userid' => $pay_data['user_id'],
                'income_type' => $pay_data['pay_type'],
                'income_totalfee' => $pay_data['amount'],
                'income_jointime' => time(),
            );
            // 回收商充值记录
            $coop_charge_log = array(
                'log_userid' => $pay_data['user_id'],
                'log_total' => $pay_data['amount'],
                'log_title' => '订单充值',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            // 回收商支出记录
            $coop_pay_log = array(
                'log_userid' => $pay_data['user_id'],
                'log_total' => $pay_data['amount'],
                'log_title' => '订单支出',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            // 冻结转余额
            $wx_freeze_data = array(
                'wx_updatetime' => time(),
            );
            $wx_freeze_where = array(
                'wx_id' => $pay_data['wx_id'],
                'wx_status' => 1,
            );
            // 冻结转余额日志
            $ffreeze_log_data = array(
                'log_userid' => $pay_data['wx_id'],
                'log_total' => $pay_data['price'] * 100,
                'log_title' => '冻结转余',
                'log_result' => 1,
                'log_jointime' => time(),
            );
            //开启事物
            $this->db->trans_begin();
            // 更新用户订单表.
            $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
            $a = $this->db->affected_rows($this->table_order_nonstandard);
            //更新回收商订单表.
            $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
            $b = $this->db->affected_rows($this->table_cooperator_offer);
            //更新统计表
            $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
            $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
            $c = $this->db->affected_rows($this->table_order_statistic);
            // 充值到余额
            $this->db->set('wx_balance','wx_balance+'.$pay_data['amount'],FALSE);
            $this->db->update($this->table_wxuser,$wx_data,$wx_where);
            $d = $this->db->affected_rows();
            // 用户余额变动记录
            $this->db->insert($this->table_bill_log,$log_data);
            $e = $this->db->affected_rows();
            // 系统入账
            $this->db->insert($this->table_bill_income,$income_data);
            $f = $this->db->affected_rows();
            // 回首商收入日志
            $this->db->insert($this->table_bill_log,$coop_charge_log);
            $g = $this->db->affected_rows();
            // 回首商支出日志
            $this->db->insert($this->table_bill_log,$coop_pay_log);
            $h = $this->db->affected_rows();
            // 冻结资金转余额
            $this->db->set('wx_freeze_balance','wx_freeze_balance -'.$pay_data['price']*100,FALSE);
            $this->db->set('wx_balance','wx_balance+'.$pay_data['price']*100,FALSE);
            $this->db->update($this->table_wxuser,$wx_freeze_data,$wx_freeze_where);
            $j = $this->db->affected_rows();
            // 冻结资金转余额日志
            $this->db->insert($this->table_bill_log,$ffreeze_log_data);
            $i = $this->db->affected_rows();
            if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1 || $d != 1 || $e != 1
                || $f != 1 || $g != 1 || $h != 1 || $i != 1 || $j != 1){
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
	}
	
	//正常支付的宝支付
	function order_pay_d($pay_data){
	    // 用户订单表
	    $order_data = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_bid_price' => $pay_data['amount']/100,
	        'order_pay_type' => $pay_data['pay_type'],
	        'order_orderstatus' => 10,
	        'ordre_dealtime' => time(),
	        'order_updatetime' => time(),
	    );
	    $order_where = array(
	        'order_number' => $pay_data['order_id'],
	        'order_status' => 1,
	    );
	    // 回收商订单表.
	    $offer_data = array(
	        'offer_money' => $pay_data['amount']/100,
	        'offer_remark' => $pay_data['remark'],
	        'offer_order_status' => 4,
	        'offer_update_time' => time(),
	    );
	    $offer_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_id' => $pay_data['order_id'],
	        'offer_status' => 1,
	    );
	    //统计信息表
	    $tj_data = array(
	        'statistic_updatetime' => time(),
	    );
	    $tj_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'statistic_status' => 1,
	    );
	    // 支付到用户余额
	    $wx_data = array(
	        'wx_updatetime' => time(),
	    );
	    $wx_where = array(
	        'wx_id' => $pay_data['wx_id'],
	        'wx_status' => 1,
	    );
	    // 用户日志
	    $log_data = array(
	        'log_userid' => $pay_data['wx_id'],
	        'log_total' => $pay_data['amount'],
	        'log_title' => '订单收入',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    // 回收商充值-系统入账
	    $income_data = array(
	        'income_number' =>$pay_data['prepay_number'],
	        'income_orderid' => $pay_data['order_id'],
	        'income_userid' => $pay_data['user_id'],
	        'income_type' => $pay_data['pay_type'],
	        'income_totalfee' => $pay_data['amount'],
	        'income_jointime' => time(),
	    );
	    // 回收商充值记录
	    $coop_charge_log = array(
	        'log_userid' => $pay_data['user_id'],
	        'log_total' => $pay_data['amount'],
	        'log_title' => '订单充值',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    // 回收商支出记录
	    $coop_pay_log = array(
	        'log_userid' => $pay_data['user_id'],
	        'log_total' => $pay_data['amount'],
	        'log_title' => '订单支出',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    //开启事物
	    $this->db->trans_begin();
	    // 更新用户订单表.
	    $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
	    $a = $this->db->affected_rows($this->table_order_nonstandard);
	    //更新回收商订单表.
	    $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
	    $b = $this->db->affected_rows($this->table_cooperator_offer);
	    //更新统计表
	    $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
	    $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
	    $c = $this->db->affected_rows($this->table_order_statistic);
	    // 充值到余额
	    $this->db->set('wx_balance','wx_balance+'.$pay_data['amount'],FALSE);
	    $this->db->update($this->table_wxuser,$wx_data,$wx_where);
	    $d = $this->db->affected_rows();
	    // 用户余额变动记录
	    $this->db->insert($this->table_bill_log,$log_data);
	    $e = $this->db->affected_rows();
	    // 系统入账
	    $this->db->insert($this->table_bill_income,$income_data);
	    $f = $this->db->affected_rows();
	    // 回首商收入日志
	    $this->db->insert($this->table_bill_log,$coop_charge_log);
	    $g = $this->db->affected_rows();
	    // 回首商支出日志
	    $this->db->insert($this->table_bill_log,$coop_pay_log);
	    $h = $this->db->affected_rows();
	    if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1 || $d != 1 || $e != 1
	        || $f != 1 || $g != 1 || $h != 1){
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
	
	// 正常支付余额支付
	function order_pay_e($pay_data){
	    // 用户订单表
	    $order_data = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_bid_price' => $pay_data['first'],
	        'order_pay_type' => $pay_data['pay_type'],
	        'order_orderstatus' => 10,
	        'ordre_dealtime' => time(),
	        'order_updatetime' => time(),
	    );
	    $order_where = array(
	        'order_number' => $pay_data['order_id'],
	        'order_status' => 1,
	    );
	    // 回收商订单表.
	    $offer_data = array(
	        'offer_money' => $pay_data['first'],
	        'offer_remark' => $pay_data['remark'],
	        'offer_order_status' => 4,
	        'offer_update_time' => time(),
	    );
	    $offer_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_id' => $pay_data['order_id'],
	        'offer_status' => 1,
	    );
	    //统计信息表
	    $tj_data = array(
	        'statistic_updatetime' => time(),
	    );
	    $tj_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'statistic_status' => 1,
	    );
	    // 用户余额
	    $wx_data = array(
	        'wx_updatetime' => time(),
	    );
	    $wx_where = array(
	        'wx_id' => $pay_data['wx_id'],
	        'wx_status' => 1,
	    );
	    // 用户日志
	    $log_data = array(
	        'log_userid' => $pay_data['wx_id'],
	        'log_total' => $pay_data['first'] * 100,
	        'log_title' => '订单收入',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    // 回首商余额
	    $coop_balance_data = array(
	        'money_update_time' => time(),
	    );
	    $coop_balance_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'money_status' => 1,
	    );
	    // 回收商余额支出日志
	    $coop_log = array(
	        'log_userid' => $pay_data['user_id'],
	        'log_total' => $pay_data['first'] * 100,
	        'log_title' => '订单支出',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    //开启事物
	    $this->db->trans_begin();
	    // 更新用户订单表.
	    $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
	    $a = $this->db->affected_rows($this->table_order_nonstandard);
	    //更新回收商订单表.
	    $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
	    $b = $this->db->affected_rows($this->table_cooperator_offer);
	    //更新统计表
	    $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
	    $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
	    $c = $this->db->affected_rows($this->table_order_statistic);
	    // 订单收入
	    $this->db->set('wx_balance','wx_balance+'.$pay_data['first']*100,FALSE);
	    $this->db->update($this->table_wxuser,$wx_data,$wx_where);
	    $d = $this->db->affected_rows();
	    // 冻结资金转余额日志
	    $this->db->insert($this->table_bill_log,$log_data);
	    $e = $this->db->affected_rows();
	    // 回首商余额变动
	    $this->db->set('money_balance','money_balance-'.$pay_data['first'],FALSE);
	    $this->db->update($this->table_cooperator_money,$coop_balance_data,$coop_balance_where);
	    $f = $this->db->affected_rows();
	    // 回收商支出日志
	    $this->db->insert($this->table_bill_log,$coop_log);
	    $g = $this->db->affected_rows();
	    if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1 || $d != 1 || $e != 1
	        || $f != 1 || $g != 1){
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
	 * 任性用户取消订单，退款到回收商余额里
	 */
	function order_pay_f($pay_data){
	    // 回收商充值-系统入账
	    $income_data = array(
	        'income_number' =>$pay_data['prepay_number'],
	        'income_orderid' => $pay_data['prepay_number'],
	        'income_userid' => $pay_data['user_id'],
	        'income_type' => $pay_data['pay_type'],
	        'income_totalfee' => $pay_data['amount'],
	        'income_jointime' => time(),
	    );
	    // 修改回收商余额.
	    $coop_balance = array(
	        'money_update_time' => time(),
	    );
	    $coop_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'money_status' => 1,
	    );
	    // 生成回收商收入记录
	    $coop_pay_log = array(
	        'log_userid' => $pay_data['user_id'],
	        'log_total' => $pay_data['amount'],
	        'log_title' => '用户取消订单，充值到余额',
	        'log_result' => 1,
	        'log_jointime' => time(),
	    );
	    //开启事物
	    $this->db->trans_begin();
	    // 更新回收商余额.
	    $this->db->set('money_balance','money_balance+'.$pay_data['amount']/100,FALSE);
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
	 * 线下支付。
	 */
	function order_pay_done($pay_data){
	    // 用户订单表
	    $order_data = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_bid_price' => $pay_data['first'],
	        'order_pay_type' => $pay_data['pay_type'],
	        'order_orderstatus' => 10,
	        'ordre_dealtime' => time(),
	        'order_updatetime' => time(),
	    );
	    $order_where = array(
	        'order_number' => $pay_data['order_id'],
	        'order_status' => 1,
	    );
	    // 回收商订单表.
	    $offer_data = array(
	        'offer_money' => $pay_data['first'],
	        'offer_remark' => $pay_data['remark'],
	        'offer_order_status' => 4,
	        'offer_update_time' => time(),
	    );
	    $offer_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'order_id' => $pay_data['order_id'],
	        'offer_status' => 1,
	    );
	    //统计信息表
	    $tj_data = array(
	        'statistic_updatetime' => time(),
	    );
	    $tj_where = array(
	        'cooperator_number' => $pay_data['user_id'],
	        'statistic_status' => 1,
	    );
	    //开启事物
	    $this->db->trans_begin();
	    // 更新用户订单表.
	    $this->db->update($this->table_order_nonstandard,$order_data,$order_where);
	    $a = $this->db->affected_rows($this->table_order_nonstandard);
	    //更新回收商订单表.
	    $this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
	    $b = $this->db->affected_rows($this->table_cooperator_offer);
	    //更新统计表
	    $this->db->set('statistic_sum','statistic_sum + 1',FALSE);
	    $this->db->update($this->table_order_statistic,$tj_data,$tj_where);
	    $c = $this->db->affected_rows($this->table_order_statistic);
	    if ($this->db->trans_status() === FALSE || $a !=1 || $b != 1 || $c != 1){
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
	 * 获取微信用户的相关信息
	 */
	function get_wxuser_info($order_id){
	    $sql = 'select a.wx_mobile as mobile,a.wx_openid as openid,a.wx_id as wx_id,b.order_name 
	        as title,b.order_bid_price as amount,c.offer_price as money,c.offer_second as second from '.$this->table_wxuser.' a,
	        '.$this->table_order_nonstandard.' b, '.$this->table_cooperator_offer.' c where a.wx_id = b.wx_id and
	            b.order_number = '.$order_id.' and b.order_status = 1 and c.order_id = '.$order_id.' and c.offer_status = 1';
	    $query = $this->db->query($sql);
	    $data = $query->row_array();
	    if ($data == TRUE){
	        if(!empty($data['second'])){
	            $data['money'] =$data['second'];
	        } 
	        return $data;
	    }
	    else{
	        return FALSE;
	    }
	}
	
	/*
	 * 获取同一订单的其它回收商编号.
	 */
	function get_others_id($user_id,$order_id){
	    $sql = 'select a.cooperator_number as coop_ids,b.cooperator_name as my_name
	        from '.$this->table_cooperator_offer.' a, '.$this->table_cooperator_info.' b
	        where a.order_id = '.$order_id.' and a.cooperator_number != '.$user_id.' 
	        and a.offer_status = 1 and b.cooperator_number = '.$user_id.'';
	    $query = $this->db->query($sql);
	    $data = $query->result_array();
	    if($data == TRUE){
	        return $data;
	    }
	    else{
	        return FALSE;
	    }
	}
	
	/*
	 *  获取回收商评价
	 */
	function get_comment($user_id,$order_id){
		$sql = 'select comment_id from '.$this->table_cooperator_comment.' where cooperator_number 
		='.$user_id.' and order_id = '.$order_id. ' and comment_status = 1';
		$query = $this->db->query($sql);
		if (!$query->row_array()){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
	
	/*
	 *  获取报价订单状态
	 */
	function verify_offer_status($user_id,$order_id){
		$sql = 'select offer_order_status as status from '.$this->table_cooperator_offer.' where
		cooperator_number = '.$user_id.' and order_id = '.$order_id.' and offer_status = 1';
		$query = $this->db->query($sql);
		if(!$query->row_array()){
			return FALSE;
		}
		else{
			return $query->row_array();
		}
	}
	
	/*
	 *  根据订单号获取微信昵称.
	 */
	function get_user_name($order_id){
		$sql = 'select wx_name as name,wx_id from '.$this->table_wxuser.' where wx_id 
		in(select wx_id from '.$this->table_order_nonstandard.' where order_number =
		'.$order_id.' and order_status = 1) and wx_status = 1';
		$query = $this->db->query($sql);
		return $query->row_array();
	}
	
	/*
	 *   提交评论.
	 */
	function add_comment($data){
		$new_data = array(
			'cooperator_number' => $data['user_id'],
			'order_id' => $data['order_id'],
			'wx_name' => $data['name'],
			'comment_reason' => $data['describe'],
			'comment_score' => $data['score'],
			'comment_remark' => $data['comment'],
		    'wx_id' => $data['wx_id'],
		    'comment_source' => $data['source'],
			'comment_jointime' => time(),
			'comment_status' => 1, 
		);
		$offer_data = array(
			'offer_comment' => 1,
			'offer_update_time' => time(),
		);
		$offer_where = array(
		    'cooperator_number' => $data['user_id'],
			'order_id' => $data['order_id'],
			'offer_status' => 1,
		);
		// 开启事物
		$this->db->trans_begin();
		// 添加评论记录
		$this->db->insert($this->table_cooperator_comment,$new_data);
		$a = $this->db->affected_rows($this->table_cooperator_comment);
		// 修改报价表里评论状态.
		$this->db->update($this->table_cooperator_offer,$offer_data,$offer_where);
		$b = $this->db->affected_rows($this->table_cooperator_offer);
		if ($this->db->trans_status() === FALSE || $a != 1 || $b != 1){
			// 回滚
			$this->db->trans_rollback();
			$result = FALSE;
		}
		else{
			// 提交
			$this->db->trans_commit();
			$result = TRUE;
		}
		return $result;
	}


	
}
