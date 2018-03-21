<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 	当前用户优惠券模块
 * @author sunbeike
 * @param
 */
 class  CouponUser_model extends CI_Model{
 	
 	//加载db
 	function __construct() {
 		parent::__construct();
 		$this->load->database();
 	}
 	
 	/**
 	 * 获取当前用户的所有优惠券情况
 	 * @author sunbeike
     * @return   array $data  优惠券列表信息数组 列表正确获取 返回结果 | 获取失败返回 bool false
 	 */
 	function getCouponUserList(){
 		//where 条件 sql语句
 		$where = "WHERE a.info_id = b.info_id ";
 		//搜索条件判断
 		//优惠券编号
 		
 		if($this->mobile !=null || !empty($this->mobile)){
 			$where.= "and b.coupon_mobile  = '$this->mobile'";
 		}
 		
 		//sql语句查看
 		$sql="SELECT a.class_id as classid,a.info_name as name,a.info_amount
 		       as amount,a.info_range as ranges,a.info_number as number,
 		       a.info_contime as contime,a.info_jointime as jointime,
 		       b.coupon_mobile as mobile,b.coupon_status as statu 
 			   FROM h_coupon_info AS a ,h_coupon_user AS b ".$where;
 		$query=$this->db->query($sql);
 		
 		if ($query->num_rows<1) {
 			return false;
 		}
 		$data=$query->result_array();
 		return $data;
 	}
 	/**
 	 * 获取当前用户的报单后可以使用的优惠券情况
 	 * @author sunbeike
 	 * @return   array $data  可用优惠券信息数组 列表正确获取 返回结果 | 获取失败返回 bool false
 	 */
 	function getCouponUser(){
 		//where 条件 sql语句
 		$where = 'WHERE a.info_id = b.info_id and b.coupon_status = 0 ';
 		//搜索条件判断
 		if (empty($this->mobile)||!is_numeric($this->mobile)) {
 			return false;
 		}else{
 			$where.= 'and b.coupon_mobile  = '.$this->mobile;
 		}
 		//sql语句查看
 		$sql='SELECT a.class_id as classid,a.info_name as names,a.info_amount as amount,a.info_range as ranges,a.info_number as number,a.info_contime as contime,a.info_jointime as jointime,b.coupon_mobile as mobile,b.coupon_status as statu
 			  FROM h_coupon_info AS a ,h_coupon_user AS b '.$where;
 		$query=$this->db->query($sql);

 		if ($query->num_rows<1) {
 			return false;
 		}
 		$data=$query->result_array();
 		return $data;
 	}
 	/**
 	 * 添加用户领取优惠券模块
 	 * @author sunbeike
 	 * @return   array $data  保存用户领取优惠券返回结果集
 	 */
 	function addCouponUser(){
 		$data = array(
 				'coupon_id' => $id,
 				'info_id' => $infoid,
 				'coupon_mobile' => $mobile,
 				'coupon_jointime' => date('Y-m-d H:i:s'),
 				'coupon_uptime' => date('Y-m-d H:i:s'),
 				'coupon_status' => $status
 		);
 		$sql = $this->db->insert('h_coupon_user', $data);
 		$result =$this->db->query($sql);
 		$rows = $this->db->affected_rows();
 		if($rows != 1){
 			return false;
 		}else {
 			return  true;
 		}
 	}
 	
 	/**
 	 * 修改使用过后的优惠券
 	 * @author sunbeike
 	 * @return   保持失败返回 bool false 成功则为true
 	 */
 	function editCouponClass(){
 		$data = array(
 				'coupon_uptime' => date('Y-m-d H:i:s'),
 				'coupon_status' => $status
 		);
 		$where = "'coupon_id' => $this->id";
 		$sql = $this->db->update('h_coupon_user', $data,$where);
 		$result =$this->db->query($sql);
 		$rows = $this->db->affected_rows();
 		if($rows != 1){
 			return false;
 		}else {
 			return  true;
 		}
 	}
 }
?>