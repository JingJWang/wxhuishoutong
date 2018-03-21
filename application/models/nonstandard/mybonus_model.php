<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
/*
 * 我的奖金模块
 */
class Mybonus_model extends CI_Model {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取奖金详细列表
     */
    function GetBonus(){
    	if(isset($_SESSION['userinfo']['user_mobile'])){
    		$data['mobile']=$_SESSION['userinfo']['user_mobile'];
    	}else{
    	    return false;
    	}
    	//获取当前用户的会员具体情况
    	$mem_sql='select b.wx_id as id,b.wx_openid as openid, b.wx_mobile as mobile,b.wx_img as img,b.wx_member as member,b.wx_expire as expire 
    			from h_wxuser as b where wx_id='.$_SESSION['userinfo']['user_id'];
    	$mem_query=$this->db->query($mem_sql);
        $men_result=$mem_query->result_array();
    	if(!$mem_query || $mem_query->num_rows < 1){
    		return false;
    	}else{
    	    if($men_result[0]['expire']<time() && $men_result[0]['member']!=0){
    	        $sqls='update h_wxuser set wx_member=0,wx_expire="" where wx_id='.$men_result[0]['id'];
    	        $squery=$this->db->query($sqls);
    	        if($squery){
    	              $msql='select b.wx_id as id,b.wx_openid as openid, b.wx_mobile as mobile,
    	                     b.wx_img as img,b.wx_member as member,b.wx_expire as expire 
    			             from h_wxuser as b where wx_id='.$men_result[0]['id'];
    	              $mquery=$this->db->query($msql);
    	              $data['mem']=$mquery->result_array();
    	        }
    	    }else{
    	        $data['mem']=$men_result;
    	    }
    	}
    	//获取奖金结算状态下的金额
     	$bonusql='select sum(gdeal_bonus) as sbonus,
				sum(IF(gdeal_bonustatus=1,gdeal_bonus,0)) as ybonus,
				sum(IF(gdeal_bonustatus=2,gdeal_bonus,0)) as wbonus
				from h_goodsdeal_bonus a where gdeal_bonustatus is not null
				and (select wx_id  from h_wxuser_task e where e.center_extend_num
     			=a.gdeal_invitation) = "'.$_SESSION['userinfo']['user_id'].'" group by gdeal_invitation';
    	$bonus_query=$this->db->query($bonusql);
    	if(!$bonus_query || $bonus_query->num_rows < 1){
    		$data['list']='';
    	}
    	$data['list']=$bonus_query->result_array();
    	//获取奖金详细列表
    	$sql='select gdeal_source as source,gdeal_phone as phone,(select wx_mobile from 
    		h_wxuser c where a.gdeal_userid =c.wx_id) as mobile,gdeal_goodname as "name",
	    	b.order_bid_price  as bidprice,d.record_price as reprice,
    		(select goods_ppri from h_shop_goods as e where e.goods_name=a.gdeal_goodname limit 1) as price,
	    	gdeal_bonus as bonus ,gdeal_jointime as dealtime
	    	from h_goodsdeal_bonus a
	    	left join h_order_nonstandard b on b.order_number=a.gdeal_goodid
	    	left join h_shop_record as d on a.gdeal_goodid=d.record_payid
	    	where (select wx_id from h_wxuser_task e where e.center_extend_num =a.gdeal_invitation)="'.$_SESSION['userinfo']['user_id'].'"
	    	order by a.gdeal_jointime desc';
		$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data['getList']='';
    	}
    	$data['getList']=$query->result_array();
        return $data;
    }
    /**
     * 获取会员详情
     */
    function bonustatus(){
    	if(isset($_SESSION['userinfo']['user_mobile'])){
    		$data['mobile']=$_SESSION['userinfo']['user_mobile'];
    	}else{
    	    return false;
    	}
    	$sql='select wx_img,wx_mobile,wx_expire,wx_member from h_wxuser where wx_mobile="'.$data['mobile'].'"';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$bonustatus='';
    	}
    	$bonustatus=$query->result_array();
    	return $bonustatus;
    }
    /**
     * 获取排行榜明细
     */
    function getrank(){
    	 //获取上周周一和上周周末
		$weekfirst=strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"))));
		$weeklast=strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"))));
	    $rank_sql='select gdeal_invitation as invi,c.wx_mobile as mobile,c.wx_img as img,
                   sum(IF(gdeal_bonustatus=1,gdeal_bonus,0)) as ybonus  from h_goodsdeal_bonus a 
                    left join h_wxuser_task b on a.gdeal_invitation=b.center_extend_num
                    left join h_wxuser c on b.wx_id=c.wx_id
                    where a.gdeal_bonustatus is not null
                    and a.gdeal_jointime between "'.$weekfirst.'" and "'.$weeklast.'"  
                    group by gdeal_invitation order by ybonus desc';
	    	$rank_query=$this->db->query($rank_sql);
	    	$rank_result=$rank_query->result_array();
	    	return $rank_result;
	 }
    /**
     * 获取排行榜个人详细
     */
    function ranking(){
        $weekfirst=strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"))));
        $weeklast=strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"))));
    	if(isset($_SESSION['userinfo']['user_id'])){
    		$data['id']=$_SESSION['userinfo']['user_id'];
    	}else{
    	    return false;
    	}
    	$sql='select gdeal_invitation as invi,c.wx_mobile as mobile,c.wx_img as img,
                   sum(IF(gdeal_bonustatus=1,gdeal_bonus,0)) as sbonus  from h_goodsdeal_bonus a 
                    left join h_wxuser_task b on a.gdeal_invitation=b.center_extend_num
                    left join h_wxuser c on b.wx_id=c.wx_id
                    where a.gdeal_bonustatus is not null
                    and a.gdeal_jointime between "'.$weekfirst.'" and "'.$weeklast.'" and c.wx_id="'.$data['id'].'"';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$ranking='';
    	}
    	$ranking=$query->result_array();
    	return $ranking;
    }
}