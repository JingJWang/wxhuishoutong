<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  系统设置
 */

class Offer_model extends CI_Model{
	
//	private $table_access_token = 'h_access_token';
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
//  private $table_cooperator_offer = 'h_cooperator_offer';
	private $table_order_statistic = 'h_order_statistic';
//	private $table_trans_cancel = 'h_trans_cancel';
//	private $table_cooperator_comment = 'h_cooperator_comment';
	private $table_order_nonstandard = 'h_order_nonstandard';
	private $table_clothes_order = 'h_clothes_order';
	private $table_clothes_type = 'h_clothes_type';
	private $table_electronic_order = 'h_electronic_order';
	private $table_electronic_types = 'h_electronic_types';
	private $table_cooperator_offer = 'h_cooperator_offer';
	private $table_order_content = 'h_order_content';
	
	
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	/*
	 *  获取回收商的用户状态.
	 */
	function get_user_status($user_id){
		$sql = 'select cooperator_mobile as mobile,cooperator_userstatus as user_status,cooperator_switch as switchs from 
		'.$this->table_cooperator_info.' where cooperator_number = '.$user_id.' and cooperator_status = 1';
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
	 *  获取回收商设定的距离和开关状态.
	 */
	function get_cooperator_range($user_id){
		$sql = 'select cooperator_distance as distance,cooperator_switch as switchs from '
		.$this->table_cooperator_info.' where cooperator_number = '.$user_id.' and cooperator_status = 1';
		$query = $this->db->query($sql);
		$result = $query->row_array();
		if ($result == TRUE){
			return $result;
		}
		else{
			$result['distance'] = 5;  // 回收商默认5公里回首范围.
			$result['switchs'] = 1;   // 回收商开关默认是开启状态.
			return $result;
		}
	}
	
	/*
	 *  获取筛选后的用户订单数据.
	 *  $location array 'left','right','top','bottom',代表左(经度),右(经度),上(维度),下(维度).
	 *  $type array 'ftype','ctype'分别代表父类与子类.
	 *  $filter array  1表示按距离排序 2表示按可用排序.
	 */
	function get_user_order_by_filter($user_id,$location,$type,$filter,$sort,$limit,$size){
		// 类型筛选条件, 默认全部订单.
		if ($type['ftype'] == FALSE && $type['ctype'] == FALSE){
		   $type_filter = '';
		}
		else{
			if ($type['ftype'] == TRUE){
				$type_filter = 'a.order_ftype = '.$type['ftype'].' and ';
			}
			else{
				$type_filter = 'a.order_ctype = '.$type['ctype'].' and ';
			}
		}	
		// 1 按距离排序, 2 是否使用.
		$my_filter = ($filter) ? ' and a.order_isused = '.$filter : ''; 
		switch ($sort){
		    case 1:
		        $my_sort = 'distance ';
		        break;
		    case 2:
		        $my_sort = 'a.order_jointime';
		        break;
		    case 3:
		        $my_sort = 'b.wx_class desc';
		        break;
		    default:
		        $my_sort = 'a.order_jointime desc';
		        break;
		}
		$sql = 'select a.order_number as order_id ,a.order_name as title,a.order_img as pic,
		    a.order_offer_times	as ordered,a.order_cancel_times as cancel_time,
		    (a.order_submittime + '.$this->config->item('cooperator_order_vaild_time').')
		    as datetime,a.order_isused as status,
		(POWER(MOD(ABS(a.order_longitude - '.$location['lng'].'),360),2) + POWER(ABS(a.order_latitude - 
		'.$location['lat'].'),2)) as distance,a.order_latitude as lat,a.order_longitude as lng,
		b.wx_class as user_class from '.$this->table_order_nonstandard.' a,'.$this->table_wxuser.' b
		where '.$type_filter.' a.order_orderstatus = 1 and ((a.order_submittime + 
		    '.$this->config->item('cooperator_order_vaild_time').') > unix_timestamp())
		'.$my_filter.' and not a.order_number in (select order_id from 
		'.$this->table_cooperator_offer.' where cooperator_number = '.$user_id.') and a.wx_id = b.wx_id
		and	a.order_latitude <>0 and a.order_latitude > '.$location['bottom'].' and
		a.order_latitude < '.$location['top'].' and a.order_longitude < '.$location['right'].' and 
		a.order_longitude > '.$location['left'].' and a.order_status = 1 order by '.$my_sort.' limit '.$limit.','.$size.'';
		$query = $this->db->query($sql);
		$result = $query->result_array();
		// 成功返回结果集.
		if ($result == TRUE){
			return $result;
		}
		// 失败返回FALSE.
		else{
			return FALSE;
		}
	}
	
	/*
	 *  获取超过三天未处理订单数.
	 */
	function get_undeal_total($user_id){
	    $sql = 'select count(offer_id) as total from '.$this->table_cooperator_offer.'
		    where cooperator_number = '.$user_id.' and offer_update_time < (unix_timestamp()-259200) and 
		    (offer_order_status = 2 or offer_order_status = 3) and offer_status = 1';
	    $query = $this->db->query($sql);
	    $data = $query->row_array();
	    if ($data == FALSE){
	        return FALSE;
	    }
	    else{
	        return $data;
	    }
	}
	
	/*
	 *  获取订单详情.
	 */
	function get_offer_detail($user_id,$order_id){
		$sql = 'select a.order_ctype as ctype,a.order_province as province,a.order_city as city,a.order_county as 
		county,a.order_residential_quarters as xiaoqu,a.order_ftype as ftype,a.order_selling_price as s_price,
		 a.order_isused as isused,a.order_name as title, b.electronic_buydate as buydate,
		b.electronic_oather as oather,b.electronic_img as img from '.$this->table_order_nonstandard.' a,'
		.$this->table_order_content.' b where a.order_number = "'.$order_id.'" and b.order_id = "'.$order_id.'" 
		 and a.order_status = 1 and b.electronic_status = 1';
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
	 *  查询回收商是否已经报价.
	 */
	function get_offer_result($user_id,$order_id){
		$sql = 'select offer_id from '.$this->table_cooperator_offer.' where cooperator_number = 
		    "'.$user_id.'" and order_id = "'.$order_id.'" and offer_status = 1';
		$query = $this->db->query($sql);
		if ($query->row_array() == FALSE){
			return FALSE;
		}
		else{
			return TRUE;
		}
	}
    /*
	 *  获取订单位置坐标和用户信息.
	 */
	function get_order_location($user_id,$order_id){
		$sql = 'select a.order_latitude as lat,a.order_longitude as lng,a.order_name,b.statistic_sum as done_sum,
        c.cooperator_name as name,c.cooperator_class as class,c.cooperator_auth_type as auth_type, 
		    c.cooperator_shopaddress as addr from 
		'.$this->table_order_nonstandard.' a, '.$this->table_order_statistic.' b,
		'.$this->table_cooperator_info.' c where a.order_number = '.$order_id.' and b.cooperator_number
		= '.$user_id.' and c.cooperator_number = '.$user_id.' and c.cooperator_status = 1';
		$query = $this->db->query($sql);
		if ($query->row_array() == TRUE){
			return $query->row_array();
		}
		else{
			return FALSE;
		}		
	}

	/*
	 * 获取微信用户的相关信息
	 */
	function get_wxuser_info($order_id){
	    $sql = 'select a.wx_mobile as mobile,a.wx_openid as openid,b.order_name
	        as title, (select count(*) from '.$this->table_cooperator_offer.' where
	        order_id = '.$order_id.' and offer_status = 1) as number from 
	        '.$this->table_wxuser.' a,'.$this->table_order_nonstandard.' b where 
	         a.wx_id = b.wx_id and b.order_number = '.$order_id.' and b.order_status = 1 ';
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
	 *  添加回收商报价信息
	 */
	function add_user_offer($data){
		$offer_data = array(
		    'offer_order_name' => $data['order_name'],
			'offer_done_times' => $data['done_times'],
			'offer_coop_name' => $data['coop_name'],
			'offer_coop_class' => $data['coop_class'],
			'offer_coop_auth' => $data['coop_auth'],			
			'cooperator_number' => $data['user_id'],
		    'offer_coop_addr' => $data['coop_addr'],
			'order_id' => $data['order_id'],
			'offer_price' => $data['my_price'],
			'offer_join_time' => time(),
			'offer_times' => $data['times'],
			'offer_service' => $data['service'],
			'offer_remark' => $data['remark'],
			'offer_status' => $data['status'],
			'offer_distance' => $data['distance'],
			//'offer_low_price' => $data['low_price'],
			'offer_order_status' => 1,
		    'offer_lng' => $data['lng'],
		    'offer_lat' => $data['lat'],
		);
		$update_data = array(
			'order_updatetime' => time(), 
		);
		$update_where = array(
			'order_number' => $data['order_id'],
			'order_status' => 1,
		);
		//统计表
		$s_data = array(
		    'statistic_updatetime' => time(),
		);
		$s_where = array(
		    'cooperator_number' => $data['user_id'],
		    'statistic_status' => 1,
		);
		// 开启事物.
		$this->db->trans_begin();
		//添加报价信息
		$this->db->insert($this->table_cooperator_offer,$offer_data);
		$a = $this->db->affected_rows($this->table_cooperator_offer);
		// 更新报价次数.
		$this->db->set('order_offer_times','order_offer_times + 1',FALSE);
		$this->db->update($this->table_order_nonstandard,$update_data,$update_where);
		$b = $this->db->affected_rows($this->table_order_nonstandard);
		// 更新统计表.
		$this->db->set('statistic_offersum','statistic_offersum + 1',FALSE);
		$this->db->update($this->table_order_statistic,$s_data,$s_where);
		$c = $this->db->affected_rows($this->table_order_statistic);
		if ($this->db->trans_status() === FALSE || $a != 1 || $b != 1 || $c != 1){
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
