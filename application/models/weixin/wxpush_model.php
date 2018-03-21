<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Wxpush_model extends CI_Model {
	
	function  __construct(){
		parent::__construct();
		$this->load->database();
	}
 	/**
 	  * 获取用户上个月的收益情况
 	 */
	function getLastBonus(){
		$laststart=strtotime(date('Y-m-01 00:00:01', strtotime('-1 month')));//上个月月初
		$lastend=strtotime(date('Y-m-t 23:59:59', strtotime('-1 month')));//上个月月末
		//查询上一个月的总收益
		$lastbonus_sql='select sum(a.gdeal_bonus) as sbonus,(select wx_openid from 
						h_wxuser c where c.wx_id=b.wx_id) as openid
						from h_goodsdeal_bonus a
						left join h_wxuser_task b on a.gdeal_invitation=b.center_extend_num
						where a.gdeal_jointime >="'.$laststart.'"  and 
						a.gdeal_jointime <= "'.$lastend.'" group by a.gdeal_invitation
						having sum(a.gdeal_bonus) >0';
		$lastbonus_query=$this->db->query($lastbonus_sql);
		$lastbonus= $lastbonus_query->result_array();
		//通过openid 获取会员的到期时间
		foreach ($lastbonus as $k =>$v){
			//$v['openid']='o9nlJt2dHqi7vsNZKmPrXE5sAIz8';
			$v['openid']=$_SESSION['userinfo']['user_openid'];
			$this->load->model('common/wxcode_model');
			 if($v['openid']!='' || !empty($v['openid'])){
			 	//月账单提醒
			 	$sendtext='{"touser":"%s", "msgtype":"text","text":{"content":"您上月收益'.$v['sbonus'].',如果疑问,请咨询客服"}}';
			 	$content = sprintf($sendtext,$v['openid']);
			 	$response_wx=$this->wxcode_model->sendmessage($content);
			} 
		}
		return $lastbonus;
	}
	/**
	 * 会员到期提醒
	 */
	function getRemind(){
		//会员到期提醒
		$exp_sql='select wx_openid as openid,wx_member as member,wx_expire as expire from h_wxuser
				where wx_status=1';
		$exp_query=$this->db->query($exp_sql);
		$exp = $exp_query->result_array();
		$exps=time()-$exp['0']['expire'];
		$timediff=time()-$exp['0']['expire'];
		//$exp['0']['openid']='o9nlJt2dHqi7vsNZKmPrXE5sAIz8';
		$exp['0']['openid']=$_SESSION['userinfo']['user_openid'];
		$this->load->model('common/wxcode_model');
		if(($timediff>=0 && $timediff<=86400) && $exp['0']['member']>0 && ($exp['0']['openid']!='' || !empty($exp['0']['openid']))){
			$sendtext='{"touser":"%s", "msgtype":"text","text":{"content":"你的会员还有1天时间就到期了请注意续费,如果疑问,请咨询客服"}}';
			$content = sprintf($sendtext,$exp['0']['openid']);
			$response_wx=$this->wxcode_model->sendmessage($content);
			//var_dump($response_wx);
		}
		return  $exp;
	}
}
?>