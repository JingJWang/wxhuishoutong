<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Taskfinish_model extends CI_Model {

	private $task_log    = 'h_task_log ';//用户任务信息表
	private $task_info   = 'h_task_info';//升级名称列表
	private $wxuser_task = 'h_wxuser_task';//用户表
	private $task_cycle	 = 'h_task_cycle';//循环第几次数

	public function getonetypetask($wx_id,$task_type){//用户取得任务类型
		$select = 'a.log_id,a.wx_id,a.task_id,a.reward_id,a.task_overtime,b.reward_num,b.task_share,b.task_invite_u,b.task_invite_m,b.task_sign,b.task_turnover';
		$sql = 'select '.$select.' from '.$this->task_log.'as a,'.$this->task_info.' as b where a.wx_id='.$wx_id.' and (b.task_type='.$task_type.' or b.task_type=4) and a.cycle_is_finish=-1 and a.task_id=b.task_id and a.task_process=2 and a.task_status=1 and b.task_status=1';//筛选出选择的类型和精华类型
		$data = $this->db->query($sql)->result_array();
		return $data;
	}
	/**
	 * 得到此用户的此类型的所有任务(包括未领奖的)
	 * @param 	int 	wx_id 	 	用户id
	 * @return 	int 	task_type 	任务类型
	 */
	public function getdotype($wx_id,$task_type){
		$select_str = 'a.task_id,a.reward_id,a.info_name,a.task_type,a.reward_content,a.task_level,a.task_content,a.task_limit_other,a.task_url,a.task_share_url,a.task_share,a.task_invite_u,a.task_invite_m,a.task_sign,a.task_turnover,a.task_limit_time,b.reward_gettime,b.log_id,b.wx_id,b.task_jointime,b.task_overtime,b.task_process';
		$str = 'task_id,reward_id,info_name,task_type,reward_content,task_level,task_content,task_limit_other,task_url,task_share_url,task_share,task_invite_u,task_invite_m,task_sign,task_turnover,task_limit_time,task_status';
		$sql='select '.$select_str.' from (select '.$str.' from '.$this->task_info.' where task_type='.$task_type.' and task_status=1) as a left join '.$this->task_log.' as b on a.task_id=b.task_id and b.wx_id='.$wx_id.' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_status=1';
		$return = $this->db->query($sql);//获取全部任务信息
		if ($return->num_rows<0) {
			return 0;
		}
		$return = $return->result_array();
		return $return;
	}

	public function uptaskprocess($wx_id,$task_id,$process_level){//任务更新到某一个过程
		$update_tasklog['task_process'] = $process_level;
		$time = time();
		if ($process_level==3) {
			$update_tasklog['task_finishtime'] = $time;
		}elseif($process_level==6){
			$update_tasklog['log_content'] = $time;
		}
        $update_tasklog['task_updatetime'] = $time;
        $where = 'wx_id = '.$wx_id.' and task_id = '.$task_id.' and cycle_is_finish=-1 and task_status = 1';
        $str = $this->db->update($this->task_log,$update_tasklog,$where);
        if (!$str) return false;
        // 更新缓存
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
		if ($this->zredis->link === true) {
			$ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$wx_id.':'.$task_id.':*');//缓存添加
			if (empty($ishavetask)) {//没有缓存直接跳过。让其它界面生成缓存。
				return true;
			}
			$ttl = $this->zredis->_redis->TTL($ishavetask['0']);//获取过期时间
			$arr = $this->zredis->_redis->HGETALL($ishavetask['0']);
			$arr['task_process'] = $update_tasklog['task_process'];
			$str = $this->zredis->_redis->HMSET($ishavetask['0'],$arr);
			if ($str!=true) return false;
			$str = $this->zredis->_redis->EXPIRE($ishavetask['0'],$ttl);
			if ($str!=true) return false;
		} 
		return true;
	}
	/**
	 * 更新用户信息  
	 * @param 	int 	wx_id 	用户id
	 * @param 	int 	type 	任务类型
	 * @param 	int 	newnum 	要更新的信息
	 * @return 	bool  	trun 正确|false 错误
	 */
	public function updatauserinfo($wx_id,$task_type,$newnum){//更新用户信息
		$update = '';
		switch ($task_type) {
			case '1':
				$update .= 'center_sign='.$newnum.',';
				$update .= 'center_laster_sign='.time().',';
				break;
			case '2':
				$update .= 'center_turnover='.$newnum.',';
				break;
			case '3':
				$update .= 'center_share='.$newnum.',';
				$update .= 'center_laster_share='.time().',';
				break;
			case '5':
				$update .= 'center_invite_u='.$newnum.',';
				break;
			case '6':
				$update .= 'center_invite_m='.$newnum.',';
				break;
			default:
				break;
		}

		$update .= 'center_updatetime='.time().' ';
        $where = 'wx_id = '.$wx_id.' and center_status = 1';
        $sql = 'update '.$this->wxuser_task.' set '.$update.'where '.$where;
        $str = $this->db->query($sql);
        if ($str) {
        	return true;
        }else{
        	return false;
        }
	}

	/**
	 * 更新循环表的数据
	 */
	public function upadtacycleinfo($wx_id,$task_type,$user,$new_cycle){

		$update['type_num'] = $new_cycle;
		switch ($task_type) {
			case '1':
				$cycle_num = $user['center_sign_num'];
				break;
			case '2':
				$cycle_num = $user['center_turnover_num'];
				break;
			case '3':
				$cycle_num = $user['center_share_num'];
				break;
			case '5':
				$cycle_num = $user['center_invite_u_num'];
				break;
			case '6':
				$cycle_num = $user['center_invite_m_num'];
				break;
			default:
				return false;
				break;
		}
		$update['cycle_updatetime'] = time();
		$where = 'wx_id = '.$wx_id.' and cycle_num = '.$cycle_num.' and cycle_task_type = '.$task_type.' and cycle_status = 1';
		$str = $this->db->update($this->task_cycle,$update,$where);
		if (!$str) {
			return false;
		}
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	if ($this->zredis->link===true) {
            $str = $this->zredis->_redis->EXISTS('tasks_cycle:'.$wx_id.':'.$task_type);
			if ($str==0) {//不存在直接跳过，让其它界面生成redis
				return true;
			}
			$ttl = $this->zredis->_redis->TTL('tasks_cycle:'.$wx_id.':'.$task_type);//获取过期时间
			$arr = $this->zredis->_redis->HGETALL('tasks_cycle:'.$wx_id.':'.$task_type);
			$arr['type_num'] = $update['type_num'];
			$str = $this->zredis->_redis->HMSET('tasks_cycle:'.$wx_id.':'.$task_type,$arr);
			if($str!=true) return false;
			$str = $this->zredis->_redis->EXPIRE('tasks_cycle:'.$wx_id.':'.$task_type,$ttl);
			if ($str!=true) return false;
    	}
		return true;
	}

	public function is_finish_this_cycle($wx_id,$task_type){
		if ($task_type!=1&&$task_type!=2&&$task_type!=5&&$task_type!=6) {
			return 2;
		}
		if (!is_numeric($wx_id)) {
			return false;
		}
		$sql = 'select task_id,task_share,task_invite_u,task_invite_m,task_sign,task_turnover from '.$this->task_info.' where task_type='.$task_type.' and task_status=1';
		$all_task = $this->db->query($sql)->result_array();
		$sql = 'select b.task_id from '.$this->task_log.' as a,'.$this->task_info.' as b where a.wx_id='.$wx_id.' and a.cycle_is_finish=-1 and (a.task_process=4 or a.task_process=5) and a.task_id=b.task_id and b.task_type='.$task_type.' and a.task_status=1 and b.task_status=1';
		$all_finish_task = $this->db->query($sql)->result_array();
		$have=1;
		foreach ($all_task as $k => $v) {//对比此循环任务是否完成
			$i=0;
			foreach ($all_finish_task as $ke => $va) {
				if ($v['task_id']==$va['task_id']) {
					$i = 1;	
				}
			}
			if ($i!=1) {
				$have=0;
				break;
			}
		}
		
		if ($have==0) {
			return '-1';//如果未完成，退出。
		}

		switch ($task_type) {
			case 1:
				$dataline = 'center_sign_num = center_sign_num+1';
				$type_num = 'task_sign';
				break;
			case 2:
				$dataline = 'center_turnover_num = center_turnover_num+1';
				$type_num = 'task_turnover';
				break;
			case 3:
				$dataline = 'center_share_num = center_share_num+1';
				$type_num = 'task_share';
				break;
			case 5:
				$dataline = 'center_invite_u_num = center_invite_u_num+1';
				$type_num = 'task_invite_u';
				break;
			case 6:
				$dataline = 'center_invite_m_num = center_invite_m_num+1';
				$type_num = 'task_invite_m';
				break;
			default:
				return false;
				break;
		}

		$sql = 'select type_num,cycle_num from '.$this->task_cycle.' where wx_id='.$wx_id.' and cycle_task_type='.$task_type.' and cycle_is_finish=-1';
		$thenum = $this->db->query($sql)->result_array();//选择循环表中此任务在循环得到的值
		if (!$thenum) {
			return false;
		}
		$task_max = 0;
		foreach ($all_task as $k => $v) {
			if ($v[$type_num]>$task_max) {
				$task_max = $v[$type_num];//得到任务中此类型需要最大的值
			}
		}
		if ($thenum['0']['type_num']<=$task_max) {
			$nexnum = 0;
			$oldnum = $thenum['0']['type_num'];
		}else{
			$nexnum = $thenum['0']['type_num']-$task_max;
			$oldnum = $task_max;
		}
		$cycle_num = $thenum['0']['cycle_num']+1;

		$sql = 'update '.$this->task_log.' a inner join '.$this->task_info.' b set a.cycle_is_finish=1,a.task_updatetime='.time().' where a.wx_id='.$wx_id.' and a.task_id=b.task_id and b.task_type='.$task_type.' and a.cycle_is_finish=-1';
		$str = $this->db->query($sql);//更新任务日志表
		if (!$str) {
			return false;
		}

		$sql = 'update '.$this->task_cycle.' set cycle_is_finish=1,cycle_updatetime='.time().',type_num='.$oldnum.' where wx_id='.$wx_id.' and cycle_task_type='.$task_type.' and cycle_is_finish=-1';
		$str = $this->db->query($sql);//更新任务次数表
		if (!$str) {
			return false;
		}

		$sql = 'insert into '.$this->task_cycle.'(wx_id,cycle_num,cycle_task_type,type_num,cycle_jointime) value('.$wx_id.','.$cycle_num.','.$task_type.','.$nexnum.',"'.time().'");';
		$str = $this->db->query($sql);
		if (!$str) {
			return false;
		}
		$sql = 'update '.$this->wxuser_task.' set '.$dataline.',center_updatetime='.time().' where wx_id='.$wx_id.' and center_status=1';
		$str = $this->db->query($sql);//更新任务次数
		if (!$str) {
			return false;
		}
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	if ($this->zredis->link===true) {
    		$str = $this->zredis->_redis->EXISTS('tasks_cycle:'.$wx_id.':'.$task_type);
			if ($str==true) {//不存在直接跳过，让其它界面生成redis。存在侧删除，让其它界面生成
				$cyarr = $this->zredis->_redis->HGETALL('tasks_cycle:'.$wx_id.':'.$task_type);
				$cyarr['cycle_num'] = $cycle_num;
				$cyarr['type_num'] = $nexnum;
				$ttl = $this->zredis->_redis->TTL('tasks_cycle:'.$wx_id.':'.$task_type);//获取过期时间
	    		$str = $this->zredis->_redis->HMSET('tasks_cycle:'.$wx_id.':'.$task_type,$cyarr);
	    		if ($str!=true) return false;
				$str = $this->zredis->_redis->EXPIRE('tasks_cycle:'.$wx_id.':'.$task_type,$ttl);
				if ($str!=true) return false;
			}
			$ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$wx_id.':'.'*:'.$task_type);//修改缓存
			if (!empty($ishavetask)) {//没有缓存直接跳过。让其它界面生成缓存。
				foreach ($ishavetask as $k => $v) {
					$arr = $this->zredis->_redis->HGETALL($v);
					$ttl = $this->zredis->_redis->TTL($v);//获取过期时间
					$arr['task_process'] = $arr['log_id'] = $arr['reward_gettime'] = $arr['wx_id'] = $arr['task_jointime'] = $arr['task_overtime']  = '';
					$str = $this->zredis->_redis->HMSET($v,$arr);
					if ($str!=true) return false;
					$str = $this->zredis->_redis->EXPIRE($v,$ttl);
					if ($str!=true) return false;
				}
			}
    	}
		return '1';
	}

}
