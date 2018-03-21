<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class List_model extends CI_Model {

	private $task_log    = 'h_task_log';//任务日志表
	private $task_info   = 'h_task_info';//任务信息表
	private $order_nonstandard = 'h_order_nonstandard';//用户订单表
	private $wxuser			   = 'h_wxuser';//用户表
	private $cooperator_info   = 'h_cooperator_info';//回收商信息表
	private $wxuser_task  	   = 'h_wxuser_task';//用户任务信息表

	public function tasknofinishlist($wx_id){
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	$ishavetask = $taskuptime = $noftime = '';
		if ($this->zredis->link === true) {
			$ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$wx_id.':*:*');
			$isset = $this->zredis->_redis->EXISTS('taskinfouptime');
			$noftime = $this->zredis->_redis->GET('noFtask:'.$wx_id.':jointime');
			if ($isset===false) {//判断后台是否更新了任务
				$time = time();
				$this->zredis->_redis->set('taskinfouptime',$time);
				$taskuptime = $time;
			}else{
				$taskuptime = $this->zredis->_redis->get('taskinfouptime');
			}	
		}
		if (empty($ishavetask) || (!empty($ishavetask)&&($noftime!=$taskuptime))) {
			$select_str = 'a.task_id,a.info_name,a.task_type,a.reward_content,a.task_level,a.task_content,a.task_limit_other,a.task_url,a.task_share_url,a.task_share,a.task_invite_u,a.task_invite_m,a.task_sign,a.task_turnover,a.task_limit_time,b.reward_gettime,b.log_id,b.wx_id,b.task_jointime,b.task_overtime,b.task_process,a.task_difcontent';

			$sql='select '.$select_str.' from '.$this->task_info.' as a left join '.$this->task_log.' as b on a.task_id=b.task_id and b.wx_id='.$wx_id.' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_status=1 ';
			$All_task = $this->db->query($sql)->result_array();//获取全部任务信息
			if ($this->zredis->link===true) {
				foreach ($ishavetask as $k => $v) {//如果有缓存，清除
					$str = $this->zredis->_redis->del($v);
					if ($str === false) {
						return false;
					}
				}
				foreach ($All_task as $k => $v) {
					$str = $this->zredis->_redis->HMSET('noFtask:'.$wx_id.':'.$v['task_id'].':'.$v['task_type'],$v);
					if ($str===false) return false;
					$str = $this->zredis->_redis->EXPIRE('noFtask:'.$wx_id.':'.$v['task_id'].':'.$v['task_type'],7200);
					if ($str!=true) return false;
				}
				$str = $this->zredis->_redis->SET('noFtask:'.$wx_id.':jointime',$taskuptime);	
				$str = $this->zredis->_redis->EXPIRE('noFtask:'.$wx_id.':jointime',7200);
				if ($str!=true) return false;
			}
		}else{
			foreach ($ishavetask as $k => $v) {
				$All_task[] = $this->zredis->_redis->HGETALL($v);
			}
		}
		$noFinishTask = array();
		$Finishtask = array();
		$titleOne     = '';
		$titleTwo     = '';
		$where		  = '';
		$sign = $turnover = $share = $invite_u =$invite_m = 0;//用于判断每个类型的任务是否已经显示了一个。
		$in_u_sg = $in_u_max = $in_intv_max = $in_m_max = 0;//取得此类型任务中，要求最高任务的任务值
		foreach ($All_task as $k => $v) {
			$v = $this->taskAttri($v);
			if (empty($v['log_id'])) {//把未领取的任务的步骤状态设置为数字1（未领取）。
				$v['task_process']=1;
			}
			if (!empty($v['task_overtime']) && $v['task_overtime']<time() && $v['task_process'] == 2) {//筛选出已经过期的任务,并且改变其任务过程状态。

				$task_process = $this->config->item('task_process_eng');
				$v['task_process']=$task_process['expired'];
				$update_tasklog['task_process'] = $task_process['expired'];
				$update_tasklog['task_updatetime'] = time();
				$ishavetask = '';
				if ($this->zredis->link===true) {
					$ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$wx_id.':'.$v['task_id'].':'.$v['task_type']);//缓存修改
					if (!empty($ishavetask)) {//没有缓存直接跳过。让其它界面生成缓存。
						$arr = $this->zredis->_redis->HGETALL($ishavetask['0']);
						$arr['task_process'] = $update_tasklog['task_process'];
						$Reupname = 'noFtask:'.$wx_id.':'.$v['task_id'].':'.$v['task_type'];
						$Reuptask[] = array('Reupname'=>$Reupname,'Reupcont'=>$arr);
					}
				}

				$titleOne .= ' when '.$v['log_id'].' then '.$update_tasklog['task_process'];
				$titleTwo .= ' when '.$v['log_id'].' then '.$update_tasklog['task_updatetime'];
				$where .= $v['log_id'].',';
				if ($v['task_type']==1 || $v['task_type']==2 || $v['task_type']==3 || $v['task_type']==5 || $v['task_type']==6) {
					$noFinishTask['over_task_types'] = $v['task_type'].',';//超过任务时间的类型,返回
				}
			}
			if ($v['task_process']==4 || $v['task_process']==5) {
				$Finishtask[] = $v;
				$in_u_sg = ($v['task_type']==1 && $in_u_sg<$v['task_sign'])?$v['task_sign']:$in_u_sg;
				$in_u_max = ($v['task_type']==5 && $in_u_max<$v['task_invite_u'])?$v['task_invite_u']:$in_u_max;
				$in_intv_max = ($v['task_type']==2 && $in_intv_max<$v['task_turnover'])?$v['task_turnover']:$in_intv_max;
				$in_m_max = ($v['task_type']==6 && $in_m_max<$v['task_invite_m'])?$v['task_invite_m']:$in_m_max;
			}
			if ($v['task_process']!=4 && $v['task_process']!=5) {//去除已完成的任务，得到未领取的所有任务。
				switch ($v['task_type']) {
					case '10':
					    $noFinishTask['votes'][] = $v;
					    break;
					case '9':
					    $noFinishTask['advs'][] = $v;
					    break;
					case '4':
						$noFinishTask['jinghua'][] = $v;
						break;
					case '7'://活动
						switch ($v['task_id']) {
							case '6':
								$this->load->model('common/wxcode_model');
								if (!isset($_SESSION['userinfo']['user_openid'])||$_SESSION['userinfo']['user_openid']=='') {
				        			break;
				        		}
				        		$userwxinfo = $this->wxcode_model->userinfo($_SESSION['userinfo']['user_openid']);
				        		if ($userwxinfo['subscribe']==1) {
				        			$v['task_have_finish'] = 1;//任务已经完成。在前段提醒，但未改数据库
				        		}
						        $noFinishTask['main'][] = $v;
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
							    if (!(($reg==='0000-00-00 00:00:00' && $join<1468571400 && $retime['0']['wx_mobile']!='')||($reg!=='0000-00-00 00:00:00'&&strtotime($reg)<1468571400))) {
						            $noFinishTask['main'][] = $v;
							    }
							    break;
							default:
						        $noFinishTask['main'][] = $v;
								break;
						}
						break;
					case '8':
						$noFinishTask['active'][] = $v;
						break;
					case '1'://签到
						if ($sign == 0) {
							$noFinishTask['other']['4']=$v;
							$sign = 1;
						}
						if (isset($noFinishTask['other']['4']) && $noFinishTask['other']['4']['task_level']>$v['task_level']) {
							$noFinishTask['other']['4']=$v;
						}
						break;
					case '2'://回收
						if ($turnover == 0) {
							$noFinishTask['other']['2']=$v;
							$turnover = 1;
						}
						if (isset($noFinishTask['other']['2']) && $noFinishTask['other']['2']['task_level']>$v['task_level']) {
							$noFinishTask['other']['2']=$v;
						}
						break;
					case '3':
						if ($share == 0) {
							$noFinishTask['other']['3']=$v;
							$share = 1;
						}
						if (isset($noFinishTask['other']['3']) && $noFinishTask['other']['3']['task_level']>$v['task_level']) {
							$noFinishTask['other']['3']=$v;
						}
						break;
					case '5':
						if ($invite_u == 0) {
							$noFinishTask['other']['1']=$v;
							$invite_u = 1;
						}
						if (isset($noFinishTask['other']['1']) && $noFinishTask['other']['1']['task_level']>$v['task_level']) {
							$noFinishTask['other']['1']=$v;
						}
						break;
					case '6':
						if ($invite_m == 0) {
							$noFinishTask['other']['6']=$v;
							$invite_m = 1;
						}
						if (isset($noFinishTask['other']['6']) && $noFinishTask['other']['6']['task_level']>$v['task_level']) {
							$noFinishTask['other']['6']=$v;
						}
						break;
					default:
						break;
				}
				
			}
		}
		if (isset($noFinishTask['other']['2'])) $noFinishTask['other']['2']['type_max'] = $in_u_sg;//要求最高任务的任务值赋值
		if (isset($noFinishTask['other']['1'])) $noFinishTask['other']['1']['type_max'] = $in_u_max;
		if (isset($noFinishTask['other']['5'])) $noFinishTask['other']['5']['type_max'] = $in_intv_max;
		if (isset($noFinishTask['other']['6'])) $noFinishTask['other']['6']['type_max'] = $in_m_max;
		ksort($noFinishTask['other']);
		if (!empty($titleTwo) || !empty($titleOne)) {//把前面的字符串拿来更新字段
			$where = rtrim($where,',');
			$sql = 'update '.$this->task_log.' set task_process=case log_id '.$titleOne.' end,task_updatetime=case log_id '.$titleTwo.' end where log_id in ('.$where.')';
			$str = $this->db->query($sql);
			if (!$str) {
					return false;
			}
		}
		if (isset($Reuptask) && !empty($Reuptask)) {
			foreach ($Reuptask as $k => $v) {//缓存更新
				$ttl = $this->zredis->_redis->TTL($v['Reupname']);//获取过期时间
				$str = $this->zredis->_redis->HMSET($v['Reupname'],$v['Reupcont']);
				if ($str===false) return false;
				$str = $this->zredis->_redis->EXPIRE($v['Reupname'],$ttl);
				if ($str!=true) return false;
			}
		}
		return array('finishlist' => $Finishtask,'allnofinishlist' => $noFinishTask);
	}
	/**
	 * 当用户未登录时直接调用任务
	 */
	public function AlltaskInfo(){
		$select = 'task_id,info_name,task_type,reward_content,task_level,task_content,task_limit_other,task_url,task_share_url,task_share,
					task_invite_u,task_invite_m,task_sign,task_turnover,task_limit_time,task_difcontent';
		$sql = 'select '.$select.' from h_task_info where task_level=1 and task_status=1';
		$result = $this->db->query($sql);
		if ($result->num_rows<1) {
			return false;
		}
		$All_task = $result->result_array();;
		foreach ($All_task as $k => $v) {
			$v['task_process'] = 1;
			$v = $this->taskAttri($v);
			switch ($v['task_type']) {
				case '10':
				    $noFinishTask['votes'][] = $v;
				    break;
				case '9':
				    $noFinishTask['advs'][] = $v;
				    break;
				case '4':
					$noFinishTask['jinghua'][] = $v;
					break;
				case '7'://活动
					$noFinishTask['main'][] = $v;
					break;
				case '8':
					$noFinishTask['active'][] = $v;
					break;
				case '1'://签到
					$noFinishTask['other']['4']=$v;
					break;
				case '2'://回收
					$noFinishTask['other']['2']=$v;
					break;
				case '3':
					$noFinishTask['other']['3']=$v;
					break;
				case '5':
					$noFinishTask['other']['1']=$v;
					break;
				case '6':
					$noFinishTask['other']['6']=$v;
					break;
				default:
					break;
			}
		}
		return array('finishlist' => '','allnofinishlist' => $noFinishTask);
	}
	/**
	 * 根据需求获取任务的属性
	 */
	private function taskAttri($taskinfo){
		$content = json_decode($taskinfo['task_difcontent'],true);
		$taskinfo['rety'] = $taskinfo['hard'] = '';
		if ($content!=null) {
			$taskinfo['rety'] = isset($content['rety'])?$content['rety']:'';
			$taskinfo['hard'] = isset($content['hard'])?$content['hard']:'';
		}
		unset($taskinfo['task_difcontent']);
		return $taskinfo;
	}
	/**
	 * 未领取奖励的任务
	 */
	public function goingtask($wx_id){
		
		$select_str = 'a.log_id,a.task_overtime,a.task_process,b.task_id,b.info_name,b.task_type,b.task_level,b.reward_content,b.task_content,b.task_share,b.task_url,b.task_share_url,b.task_invite_u,b.task_invite_m,b.task_sign,b.task_turnover,b.task_limit_time';
		$sql = 'select '.$select_str.' from '.$this->task_log.' as a,'.$this->task_info.' as b where a.wx_id='.$wx_id.' and a.task_status=1 and (a.task_process=3) and a.cycle_is_finish=-1 and b.task_status=1 and a.task_id=b.task_id order by b.task_type';
		$data['mytask'] = $this->db->query($sql)->result_array();
			
		return $data;
	}

	/**
	 * 获取最后一次签到任务完成的时间
	 */
	public function last_sign_time($wx_id){
		if(!isset($wx_id) || !is_numeric($wx_id)){
			return false;
		}
		$sql = 'select center_laster_sign as  laster_sign from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status=1';
		$data = $this->db->query($sql);
		if ($data->num_rows<=0) {
			return false;
		}
		$data = $data->result_array();
		return $data;
	}

	/**
	 * 获取用户已经回收金额
	 */
	public function get_user_order($wx_id){
		$sql = 'select sum(order_bid_price) as all_turnover from '.$this->order_nonstandard.' where wx_id='.$wx_id.' and order_orderstatus=10 and order_status=1';

		$data = $this->db->query($sql)->result_array();
		if ($data === false) {
			return fasle;
		}
		if (empty($data[0]['all_turnover'])) {
			$data[0]['all_turnover'] = '0';
		}

		return $data[0];
	}

	/**
	 * 获取用户已经邀请人数
	 */
	public function get_user_invite_u($extend_num){
		$sql = 'select count(wx_id) as all_invite_u from '.$this->wxuser.' where wx_invitation="'.$extend_num.'"';//获取邀请人数
		$data = $this->db->query($sql)->result_array();
		if ($data === false) {
			return fasle;
		}
		if (empty($data[0]['all_invite_u'])) {
			$data[0]['all_invite_u'] = '0';
		}

		return $data[0];
	}

	/**
	 * 获取用户已经邀请回收商的人数
	 */
	public function get_user_cooperator($extend_num){
		$sql = 'select count(cooperator_id) as all_invite_m from '.$this->cooperator_info.' where cooperator_other_code="'.$extend_num.'" and cooperator_status=1';
		
		$data = $this->db->query($sql)->result_array();
		if ($data === false) {
			return fasle;
		}
		if (empty($data[0]['all_invite_m'])) {
			$data[0]['all_invite_m'] = '0';
		}

		return $data[0];

	}


}
