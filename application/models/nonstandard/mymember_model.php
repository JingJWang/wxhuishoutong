<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
/*
 * 体验会员
 */
class Mymember_model extends CI_Model {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取当前用户的会员状态
     */
    function getMemberStatus(){
    	$data=0;
    	//获取当前用户输入的手机号码是否是体验会员
     	$mem_sql='select wx_member as member,wx_expire as expire,wx_id as id from h_wxuser a where a.wx_mobile="'.$this->mobile.'"';
    	$mem_query=$this->db->query($mem_sql);
    	$mem_result=$mem_query->result_array();
    	//依据输入的手机号从用户表中获取结果 如果为空则给该用户添加体验会员,如果不为空,则提示该手机号码已经是体验会员
    	if($mem_result==null || empty($mem_result)){
    		return $data;
    	}else{
	    	if($mem_result!='' && $mem_result['0']['id']!='' && 
	    	($mem_result['0']['member'] ==1 || $mem_result['0']['member'] ==2)
	    	  &&  strlen($mem_result['0']['expire'])>2){
	    		return $data=1;
	    	}else if($mem_result!='' && $mem_result['0']['id']!=''&& 
	    	($mem_result['0']['member'] ==0 || $mem_result['0']['member'] =='')
	    	   &&  strlen($mem_result['0']['expire'])<2){
	    		$times=strtotime($this->next_month_today(date("Y-m-d")));
	    		$upmem_sql='update h_wxuser set wx_member=2,wx_expire="'.$times.'" where wx_mobile="'.$this->mobile.'"';
	    		$query=$this->db->query($upmem_sql);
	    		if($query && $this->db->affected_rows() == 1){
	    			return $data=2;
	    		}
	    	}
		}
    }
    
    /**
     * 获取下个月最后一天及下个月的总天数
     */
    function getNextMonthEndDate($date){
    	$firstday = date('Y-m-01', strtotime($date));
    	$lastday = date('Y-m-d', strtotime("$firstday +2 month -1 day"));
    	return  $lastday;
    }
    /**
     * 获取下个月的今天  注册会员免费体验一个月的会员
     */
    function next_month_today($date){
    	//获取今天是一个月中的第多少天
    	$current_month_t =  date("t", strtotime($date));
    	$current_month_d= date("d", strtotime($date));
    	$current_month_m= date("m", strtotime($date));
    
    	//获取下个月最后一天及下个月的总天数
    	$next_month_end=$this->getNextMonthEndDate($date);
    	$next_month_t =  date("t", strtotime($next_month_end));
    
    	$returnDate='';
    	if($current_month_d==$current_month_t){//月末
    		//获取下个月的月末
    		$returnDate=$next_month_end;
    	}else{//非月末
    		//获取下个月的今天
    		if($current_month_d>$next_month_t){ //如 01-30，二月没有今天,直接返回2月最后一天
    			$returnDate=$next_month_end;
    		}else{
    			$returnDate=date("Y-m", strtotime($next_month_end))."-".$current_month_d;
    		}
    	}
    	return $returnDate;
    }
}
?>