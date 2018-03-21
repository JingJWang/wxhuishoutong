<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tasks_model extends CI_Model {

	private $task_info    = 'h_task_info';//用户任务信息表
	private $task_reward  = 'h_task_reward';//升级名称列表
	private $task_log     = 'h_task_log';//任务日志表
	private $wxuser_task  = 'h_wxuser_task';//用户任务信息表
	private $wxuser_level = 'h_wxuser_level';//升级名称列表
	private $task_cycle   = 'h_task_cycle';//升级名称列表
	private $wxuser 	  = 'h_wxuser';//升级名称列表
	private $order_nonstandard = 'h_order_nonstandard';//用户订单表
	private $task_type    = 'h_task_type';//任务类型信息表

	private $msg 		  =	"";

	public function getonetask($puts){//任务信息，包括任务关联的奖励。

		$select = 'task_id,info_name,reward_id,reward_content,reward_num,task_limit_other,task_type,task_level,task_content,task_url,task_share_url,task_share,task_invite_u,task_invite_m,task_sign,task_turnover,task_limit_time,task_difcontent';
		$sql = 'select '.$select.' from '.$this->task_info.' where task_id='.$puts.' and task_status=1';
		$data = $this->db->query($sql)->result_array();

		//以下获取任务的关联奖励信息
		$data[0]['rewards'] = array();
		if (!empty($data[0]['reward_id'])) {
			$reward_ids = explode(' ',$data[0]['reward_id']);
			$where = '';
			foreach ($reward_ids as $k => $v) {
				$where .= 'reward_id='.$v.' or ';
			}

			$where = rtrim($where,' or ');
			$sql = 'select reward_id,reward_type,reward_bonus,reward_integral,reward_all_integral,reward_fund from '.$this->task_reward.' where '.$where.' and reward_status=1';
			$rewards=$this->db->query($sql)->result_array();
			// var_dump($rewards);

			foreach ($rewards as $k => $v) {
				$data[0]['rewards'][]=$v;
			}
		}
		

		return $data;

	}

	public function getonetasklog($wx_id,$task_id){//用于判断是否拥有这条信息
		//获取具体某一条信息
		$sql = 'select log_id,task_process from '.$this->task_log.' where wx_id='.$wx_id.' and task_id='.$task_id.' and cycle_is_finish=-1 and task_status=1';
		$data = $this->db->query($sql)->result_array();

		return $data;

	}

	public function puttasklog($input){//插入一条任务日志
		$str=$this->db->insert($this->task_log,$input);
		if(!$str) return false;
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
		if ($this->zredis->link === false) {
			return true;
		}
		$ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$input['wx_id'].':'.$input['task_id'].':*');//缓存添加
		if (empty($ishavetask)) {//没有缓存直接返回。让其它界面生成缓存。
			return true;
		}
		$ttl = $this->zredis->_redis->TTL($ishavetask['0']);//获取过期时间
		$arr = $this->zredis->_redis->HGETALL($ishavetask['0']);
		$arr['log_id'] = $this->db->insert_id();
		$arr['wx_id'] = $input['wx_id'];
		$arr['task_jointime'] = $input['task_jointime'];
		if (isset($input['task_overtime'])) {
			$arr['task_overtime'] = $input['task_overtime'];	
		}
		if (isset($input['task_process'])) {
			$arr['task_process'] = $input['task_process'];	
		}else{
			$arr['task_process'] = 2;
		}
		$str = $this->zredis->_redis->HMSET($ishavetask['0'],$arr);
		if ($str!=true) {return false;}
		$str = $this->zredis->_redis->EXPIRE($ishavetask['0'],$ttl);
		if ($str!=true) {return false;}
		return true;
	}

	public function obtainreward($wx_id,$reward_ids,$task_id,$taskname){//用户拿到奖励
		exit();
		$where = $select_reward_id = $redis_lun = '';
		$add_integral = $add_all_intergral = $add_bonus = $add_fund = 0;
		foreach ($reward_ids as $k => $v) {
			$where .= 'reward_id='.$k.' or ';
			$select_reward_id .= $k.' ';
		}
		$where = rtrim($where,' or ');
		$select_reward_id=rtrim($select_reward_id,' ');//加入数据之前去掉最后一个空格
		$sql = 'select reward_id,reward_type,reward_bonus,reward_integral,reward_all_integral,reward_fund from '.$this->task_reward.' where '.$where.' and reward_status=1';//获取此任务的奖励表
		$rewards=$this->db->query($sql)->result_array();
		$sql = 'select center_integral,center_all_integral,center_bonus,center_fund,level_num from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status=1';//获取用户的信息
		$userinfo = $this->db->query($sql)->result_array();
		$sql = 'select level_num,level_name,level_integral,level_img from '.$this->wxuser_level.' where level_status=1 order by level_num asc';//获取等级信息
		$levels = $this->db->query($sql)->result_array();
		$all_level = count($levels);
		foreach ($rewards as $k => $v) {//取得总共增加的信息
			$add_integral += $v['reward_integral'];
			$add_all_intergral += $v['reward_all_integral'];
			$add_bonus += $v['reward_bonus'];
			$add_fund += $v['reward_fund'];
		}
		if ($add_fund>0) {
			$redis_lun .= ' '.$add_integral.'元基金';
		}
		$add_fund = $add_fund-$add_bonus;//基金的总数减去奖金的总数
		$update = array();
		if ($add_integral>0) {
			$redis_lun .= ' '.$add_integral.'通花';
			$center_integral = $userinfo[0]['center_integral']+$add_integral;
			$up_data['center_integral'] = $center_integral;//更新用户积分字段
		}
		if ($add_all_intergral > 0) {
			$redis_lun .= ' '.$add_all_intergral.'成长值';
			$center_all_integral = $userinfo[0]['center_all_integral']+$add_all_intergral;
			$up_data['center_all_integral'] = $center_all_integral;
			foreach ($levels as $k => $v) {
				if ($k<$all_level-1) {
					if ($levels[$k+1]['level_integral']>$center_all_integral && $center_all_integral>=$v['level_integral'] && $v['level_num']!=$userinfo[0]['level_num']) {
						$level_num = $v['level_num'];
						$up_data['level_num'] = $level_num;//更新用户等级字段
						$return_data['is_level'] = 1;//表示用户已经升级了
						break;
					}
				}else{
					if ($center_all_integral>=$v['level_integral'] && $v['level_num']!=$userinfo[0]['level_num']) {
						$level_num = $v['level_num'];
						$up_data['level_num'] = $level_num;//更新用户等级字段
						$return_data['is_level'] = 1;//表示用户已经升级了
					}
				}
			}
		}
 		if ($add_fund != 0) {
 			$center_fund = $userinfo[0]['center_fund']+$add_fund;
 			if($center_fund<0) return "no_fund";//基金扣除如果不够。则返回基金不够
 			$up_data['center_fund'] = $center_fund;//更新用户基金字段
 		}
		if ($add_bonus > 0 ) {
			$redis_lun .= ' '.$add_integral.'元奖金';
			$sql = 'select wx_openid from '.$this->wxuser.' where wx_id='.$wx_id;//重新获取用户openid
			$userOpenid = $this->db->query($sql);
			
			if ($userOpenid->num_rows<=0 || !$userOpenid){
				return 'get_fail';
			}else{
				$userOpenid = $userOpenid->result_array();
			};
			if (!isset($_SESSION['userinfo']['user_openid']) || $userOpenid['0']['wx_openid']!=$_SESSION['userinfo']['user_openid']) {//确保openid没有错
				exit();
			}
			$openid = $userOpenid['0']['wx_openid'];

			$ord = $this->VerifyInfo($wx_id,$task_id);//发红包前验证信息
			if ($ord==false) return "get_fail";
			($add_bonus>30)?exit():'';
			$center_bonus = $userinfo[0]['center_bonus']+$add_bonus;
			$up_data['center_bonus'] = $center_bonus;//更新用户奖金字段
			//以下记录红包
			$this->load->library('hongbao/packet');
            $result=$this->packet->_route('wxpacket',array('openid'=>$openid,'money'=>$add_bonus*100));
            $result->return_code=='SUCCESS'?$send_listid=$result->send_listid:$send_listid=$result->return_msg;
            //记录发送日志
            $sendlogsql='insert into h_redbag_sendlog(send_order_id,send_money,send_openid,send_userid,
                             send_return_code,send_return_msg,send_result_code,send_err_code,send_err_code_des,
                             send_re_openid,send_total_amount,send_send_time,send_send_listid,send_jiontime)values(
                             "'.$task_id.'","'.$add_bonus.'","'.$openid.'",'.$wx_id.',"'.$result->return_code.'"
                             ,"'.$result->return_msg.'","'.$result->result_code.'",0,0,"'.$result->re_openid.'","'.$result->total_amount.'"
                              ,"'.$result->send_time.'","'.$result->send_listid.'","'.date('Y-m-d H:i:s').'")';
            $res_addlog=$this->db->query($sendlogsql);
            if(!$res_addlog){
                return false;
            }
            if ($result->return_code!='SUCCESS' && $result->return_msg!='发放失败，此请求可能存在风险，已被微信拦截') {
            	return 'red_error';
            }elseif($result->return_msg=='发放失败，此请求可能存在风险，已被微信拦截'){
            	$return_data['error'] = '发放失败，此请求可能存在风险，已被微信拦截';
            }
 		}
 		if ($add_fund > 0 || $add_bonus > 0) {
			$update_tasklog['is_obtail_money'] = 1;//表示用户领取了基金 不算公益档案中
 		}else{
 			$update_tasklog['is_obtail_money'] = -1;
 		}
 		$up_data['center_updatetime'] = time();//更新用户更新时间字段
 		$where = 'wx_id = '.$wx_id.' and center_status = 1';
		$str = $this->db->update($this->wxuser_task,$up_data,$where);//更新用户的信息。
		if(!$str){
            return false;
        }
		//更新任务日志表
        $update_tasklog['task_process'] = 4;
        $update_tasklog['reward_id'] = $select_reward_id;//奖励id加入
        $update_tasklog['reward_gettime']=$update_tasklog['task_updatetime'] = time();
        $where = 'wx_id = '.$wx_id.' and task_id = '.$task_id.' and cycle_is_finish=-1 and task_status = 1';
        $str = $this->db->update($this->task_log,$update_tasklog,$where);
        if (!$str) {
        	return false;
        }
        //更改缓存
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        $ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$wx_id.':'.$task_id.':*');
		if (!empty($ishavetask)) {//没有缓存直接跳过。让其它界面生成缓存。
			$ttl = $this->zredis->_redis->TTL($ishavetask['0']);//获取过期时间
			$arr = $this->zredis->_redis->HGETALL($ishavetask['0']);
			$arr['task_process'] = $update_tasklog['task_process'];
			$arr['reward_gettime'] = $update_tasklog['reward_gettime'];
			$str = $this->zredis->_redis->HMSET($ishavetask['0'],$arr);
			if ($str!=true) return false;
			$str = $this->zredis->_redis->EXPIRE($ishavetask['0'],$ttl);
			if ($str!=true) return false;
		}
		$return_data['add_fund'] = $add_fund;
		$return_data['add_bonus'] = $add_bonus;
		$return_data['add_integral'] = $add_integral;
		$return_data['add_all_intergral'] = $add_all_intergral;
       	$return_data['is_ok'] = 1;
       	$redis_lun = $_SESSION['userinfo']['user_name'].'做了'.$taskname.'的任务，获得'.$redis_lun;//任务首页滚动轮播提示条信息
       	$taskInScr = $this->zredis->_redis->KEYS('taskIndexScroll');
       	if (empty($taskInScr)) {
       		$this->zredis->_redis->ZADD('taskIndexScroll',1,$redis_lun);
       	}else {
       		$scroInfo = $this->zredis->_redis->zrange('taskIndexScroll',0,-1);
       		if (count($scroInfo)>5) {
       			$r = rand(0, 4);
   				$this->zredis->_redis->zrem('taskIndexScroll',$scroInfo[$r]);
       			$this->zredis->_redis->ZADD('taskIndexScroll',1,$redis_lun);
       		}else{
       			$this->zredis->_redis->ZADD('taskIndexScroll',1,$redis_lun);
       		}
       	}
        return $return_data;
	}

	/**
	 * 获取用户的推广码
	 */
	public function getextentnum($wx_id){

		$sql = 'select center_extend_num from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status=1';
		$center_info = $this->db->query($sql);
		if (!$center_info) {
			return false;
		}
		if ($center_info->num_rows <= 0) {
			return 0;
		}else{
			$data = $center_info->result_array();
			return $data;
		}
	}


	public function get_instruction($limit){
		$sql='select instruction_name,about_id,id from h_instruction order by id asc limit 7,'.$limit;
        $query=$this->db->query($sql);
        if($query !== false){
            $data=$query->result_array();
            $this->db->close();
            return $data;
        }else{
            $this->db->close();
            return false;
        } 	
	}
	/**
	 * 检查任务进度是否完成(邀请)
	 * @param        int     wx_id       用户id
	 * @param        int     task_id     任务的id
	 */
    function checktask($wx_id,$task_id){
    	$sql = 'select task_type,task_invite_u from h_task_info where task_id='.$task_id.' and task_status=1';
    	$taskinfo = $this->db->query($sql);
    	if ($taskinfo->num_rows<1) {
    		return false;
    	}
    	$taskinfo = $taskinfo->result_array();
    	if ($taskinfo['0']['task_type']!=5) {
    		return false;
    	}
    	$sql = 'select type_num from h_task_cycle where wx_id='.$wx_id.' and cycle_task_type=5 and cycle_is_finish=-1';
    	$result = $this->db->query($sql);
    	if ($result->num_rows<1) {
    		return false;
    	}
    	$result = $result->result_array();
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
		if ($this->zredis->link === true) {
			$thecycle = $this->zredis->_redis->HGETALL('tasks_cycle:'.$wx_id.':5');
		}else{
			$thecycle = false;
		}
		if ($thecycle != false && $result['0']['type_num']!=$thecycle['type_num']) {
			$ttl = $this->zredis->_redis->TTL('tasks_cycle:'.$wx_id.':5');//获取过期时间
			$thecycle['type_num'] = $result['0']['type_num'];
			$str = $this->zredis->_redis->HMSET('tasks_cycle:'.$wx_id.':5',$thecycle);
			if ($str===false) {return false;}
			$str = $this->zredis->_redis->EXPIRE('tasks_cycle:'.$wx_id.':5',$ttl);
			if ($str!=true) {return false;}
		}
		return array('now'=>$result['0']['type_num'],'need'=>$taskinfo['0']['task_invite_u']);
    }
	/**
	 * 获取任务类型信息
	 * @param  	 int    type 	任务所属类型
	 * @param 	 int 	task_id 任务的具体id
	 * @return 	 array 	result 	取得的数组
	 */
	function getypeinfo($type,$task_id){
		$where = '';
		if (isset($task_id)&&!empty($task_id)) {
			$where .= ' and task_id='.$task_id;
		}
		$sql = 'select task_process as process from '.$this->task_type.' where type_num='.$type.$where.' and task_status=1';
		$result=$this->db->query($sql);
		if ($result->num_rows<=0) {
			return 0;
		}
		return $result->result_array();
	}
	/**
	 * 用户去红包，验证用户是否完成任务
	 */
	function VerifyInfo($wx_id,$task_id){
		if (!is_numeric($wx_id)||!is_numeric($task_id)) {
			return false;
		}
		$sql = 'select a.task_id,a.task_type,a.task_invite_u,a.task_invite_m,a.task_sign,a.task_turnover,a.task_share,b.task_process from '.$this->task_info.' as a,'.$this->task_log.' as b where a.task_id='.$task_id.' and a.task_id=b.task_id and b.wx_id='.$wx_id.' and b.cycle_is_finish=-1 and b.task_status=1 and a.task_status=1';
		$Thetask=$this->db->query($sql);
		if ($Thetask->num_rows<=0 || $Thetask==false) {//是否有任务
			return false;//没有此任务
		}else{
			$Thetask = $Thetask->result_array();
		}
		if ($Thetask['0']['task_process']!=3) {
			return false;//任务未完成
		}
		$sql = 'select center_extend_num,center_invite_u,center_invite_m,center_share,center_turnover,center_sign,center_laster_share from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status = 1';
		$userinfo = $this->db->query($sql)->result_array();
		if ($userinfo === false) {
			return false;//没有此用户
		}
		switch ($task_id) {
			case '8':
			    $sql = 'select record_id from h_shop_record where record_userid='.$wx_id.' and (record_status=1 or record_status=2) and record_jointime>1468572600';
		        $record = $this->db->query($sql);
		        if ($record->num_rows<1) {
		        	return false;
		        }
		        $sql = 'select log_id from h_task_log where wx_id='.$wx_id.' and task_id=8 and task_status=1';
		        $tasks = $this->db->query($sql);
		        if ($record->num_rows<1) {
		        	return false;
		        }
		        if (count($record)<count($tasks)) {
		        	return false;
		        }
		        return true;
				break;
			case '9':
			    $sql = 'select wx_jointime,wx_regtime,wx_mobile from '.$this->wxuser.' where wx_id='.$wx_id.' and wx_status!=3';
		        $retime = $this->db->query($sql);
		        if ($retime->num_rows<=0) {
		        	return false;
		        }
				$retime = $retime->result_array();
                $reg = $retime['0']['wx_regtime'];
				$join = strtotime($retime['0']['wx_jointime']);
				if (($reg==='0000-00-00 00:00:00' && $join<1468571400 && $retime['0']['wx_mobile']!='')||($reg!=='0000-00-00 00:00:00'&&strtotime($reg)<1468571400)) {
				    return false;
				}
			    $sql='select a.offer_id as offerid from h_cooperator_offer as a left join h_order_nonstandard as b
                      on a.order_id = b.order_number left join h_order_statistic as c on
                      a.cooperator_number=c.cooperator_number where b.wx_id ='.$wx_id;
                $query=$this->db->query($sql);
                if($query === false || $query->num_rows() <= 0 ){
                   return false;
                }
                return true;
            case 17:
                return true;
            case 18:
                return true;
			default:
				break;
		}
		switch ($Thetask['0']['task_type']) {
			case '5':
				$sql = 'select count(wx_id) as all_invite_u from '.$this->wxuser.' where wx_invitation="'.$userinfo['0']['center_extend_num'].'"';
				$invitation_num = $this->db->query($sql)->result_array();//获取实际邀请人数
				if ($invitation_num === false) return false;
				
				$cycle_num = $this->cycleNums($wx_id,$Thetask['0']['task_type']);
				if ($cycle_num===false) return false;
				if ($cycle_num['thiscy']<$Thetask['0']['task_invite_u']) {return false;}//任务未达标
				if ($cycle_num['allcy']!=$userinfo['0']['center_invite_u']) {return false;}//统计的数据不符合
				if ($cycle_num['allcy'] > $invitation_num['0']['all_invite_u']) {return false;}//统计的数据不符合
				break;
			case '3'://分享任务不再循环
				if ($userinfo['0']['center_laster_share']<=0) {return false;}//用户没有分享过
				if ($userinfo['0']['center_share']<$Thetask['0']['task_share']) {return false;}
				break;
			case '2':
				$sql = 'select sum(order_bid_price) as all_turnover from '.$this->order_nonstandard.' where wx_id='.$wx_id.' and order_orderstatus=10 and order_status=1';
				$turnover_num = $this->db->query($sql)->result_array();//获取实际的交易价钱
				if ($turnover_num === false) {return false;}
				$cycle_num = $this->cycleNums($wx_id,$Thetask['0']['task_type']);
				if ($cycle_num === false) {return false;}
				if ($cycle_num['thiscy']<$Thetask['0']['task_turnover']) {return false;}//任务未达标
				if ($cycle_num['allcy']!=$userinfo['0']['center_turnover']) {return false;}//统计的数据不符合
				if ($cycle_num['allcy'] > $turnover_num['0']['all_turnover']) {return false;}//统计的数据不符合

				break;
			default:
				return false;//其它类型没有钱奖励
				break;
		}
		return true;
	}

	private function cycleNums($wx_id,$task_type){

		$sql = 'select type_num,cycle_is_finish from '.$this->task_cycle.' where wx_id='.$wx_id.' and cycle_task_type='.$task_type;
		$cycle_num = $this->db->query($sql)->result_array();
		if ($cycle_num===false) {
			return false;
		}

		$result['allcy'] = $result['thiscy'] = 0;
		foreach ($cycle_num as $k => $v) {
			$result['allcy'] += $v['type_num'];
			if ($v['cycle_is_finish']==-1) {
				$result['thiscy'] = $v['type_num'];
			}
		}

		return $result;

	}


}
