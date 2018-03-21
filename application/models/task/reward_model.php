<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reward_model extends CI_Model {

	private $wxuser_task	 = 'h_wxuser_task';//用户任务表
	private $wxuser 		 = 'h_wxuser';//用户表
	private $bill_log		 = 'h_bill_log';//系统资金变动记录
	private $wxuser_level 	 = 'h_wxuser_level';
	private $task_reward	 = 'h_task_reward';//任务奖励表
	private $task_log	 	 = 'h_task_log';//任务奖励表
	private $task_info		 = 'h_task_info';//任务信息表
	private $task_cycle 	 = 'h_task_cycle';//任务循环表
	private $tonghua_log 	 = 'h_tonghua_log';//通花日志记录表
	public  $msg = '';
	/**
	 * 当成长值增加时，检查用户是否升级
	 * @param 	int 	all_integral 	用户最新的成长值
	 * @param 	int 	user_level 		用户的现在等级
	 * @return 	int 	level_num 		升级返回级数|不能升级返回0
	 */
	function checklevel($all_integral,$user_level){
		$sql = 'select level_num,level_name,level_integral,level_img from '.$this->wxuser_level.' where level_status=1 order by level_num asc';//获取等级信息
		$levels = $this->db->query($sql);
		if ($levels->num_rows<=0) {
			$this->msg = '无等级，稍后再试';
			return false;
		}
		$levels=$levels->result_array();
		$all_level = count($levels);
		$level_num = 0;
		foreach ($levels as $k => $v) {
			if ($k<$all_level-1 && ($levels[$k+1]['level_integral']>$all_integral && 
				$all_integral>=$v['level_integral'] && $v['level_num']!=$user_level)) {
				$level_num = $v['level_num'];//表示升级。最大等级前
				break;
			}elseif ($k==$all_level-1 && $all_integral>=$v['level_integral'] && $v['level_num']!=$user_level){
				$level_num = $v['level_num'];//表示升级,最大等级时
			}
		}
		return $level_num;
	}
	/**
	 * 用户更新任务信息和奖励获取。2016-03-17
	 * @param 	int 	wx_id 		用户的id
	 * @param 	int 	type 		任务的类型
	 * @param 	array 	task_num 	有关任务的字段的改变
	 * @param 	array 	reward 		用户要那要的奖励
	 * @param 	array 	user_have 	用户原来用有的奖励
	 * @return 	bool 	正确返回false | 错误返回true
	 * @return 	string 	msg			返回字符说明
	 */
	function getreward($wx_id,$type){
		if (!isset($this->task_num)||!isset($this->reward)||!isset($this->user_have)||
			!is_numeric($wx_id)||!is_numeric($type)) {
			$this->msg='失败，请稍后再试';
			return false;
		}
		$update= $where = array();
		$data = $this->task_num;
		$rewards = $this->reward;
		$userhave = $this->user_have;
		$time = time();
		//判断奖金减去基金后是否还有基金，并赋新值
		if (($userhave['center_fund']+=($rewards['fund']-$rewards['bonus']))<0) {
			$this->msg = '您的基金不够了，无法领取奖金！';
            return false;
		}
		$update=array(//用户拥有的奖励
			'center_fund'=>$userhave['center_fund'],
			'center_bonus'=>$userhave['center_bonus']+$rewards['bonus'],
			'center_integral'=>$userhave['center_integral']+$rewards['integral'],
			'center_all_integral'=>$userhave['center_all_integral']+$rewards['all_integral']
		);
		switch ($type) {//根据任务类型更新数据
			case '1':
				$update['center_sign'] = $data['newnum'];
				$update['center_conum'] = $data['conum'];
				$update['center_laster_sign'] = $time;
				break;
			case '2':
				$update['center_turnover'] = $data['newnum'];
				break;
			case '3':
				$update['center_share'] = $data['newnum'];
				$update['center_laster_share'] = $time;
				break;
			case '5':
				$update['center_invite_u'] = $data['newnum'];
				break;
			case '6':
				$update['center_invite_m'] = $data['newnum'];
				break;
			default:
				$this->msg = '失败，请稍后再试';
				return false;
				break;
		}
		if (isset($data['newlevel'])) {//看是否升级
			if (!is_numeric($data['newlevel'])) {
				$this->msg = '失败，请稍后再试';
				return false;
			}
			$update['level_num'] = $data['newlevel'];
		}
		$update['center_updatetime'] = $time;
		$where = array( 'wx_id'=>$wx_id,'center_status'=>'1' );
		$this->db->update($this->wxuser_task,$update,$where);
		if($this->db->affected_rows() != 1){
            $this->msg='';
            return false;
        } 
		if ($rewards['bonus']>0 || $this->balance>0) {
        	$con = $this->config->item('task_types');
			$task_name = $con[$type];
			if($this->balance>0){
			    $money=$this->balance*100;
			} else{
			    $money=$rewards['bonus']*100;
			}
			$coop_log = array(
	    	    'log_userid' => $wx_id,'log_total' => $money,
	    	    'log_title' => '做'.$task_name.'任务的收入','log_result' => 1,
	    	    'log_jointime' => time(),
	    	);   
	    	// 更新微信用户余额
			$sql = 'update '.$this->wxuser.' set wx_balance=wx_balance+'.$money.',
					wx_updatetime="'.date('Y-m-d H:i:s').'" where wx_id='.$wx_id.' and (wx_status=1 or wx_status=-1)';
			$this->db->query($sql);
			if($this->db->affected_rows() != 1){
                $this->msg='';
                return false;
            }
	    	$this->db->insert($this->bill_log,$coop_log);
	    	if($this->db->affected_rows() != 1){
                $this->msg='';
                return false;
            }
		}
		return true;
	}
	/**
	 * 用户更新任务信息和奖励获取。2016-03-17
	 * @param 	int 	wx_id 		用户的id
	 * @param 	int 	type 	    任务的类型
	 * @param 	int 	task_id 	任务的id
	 * @param 	int 	newlevel 	任务新等级
	 * @param 	array 	reward_ids 	用户选择的id
	 * @param 	array 	reward 		用户得到的奖励
	 * @param 	array 	user_have 	用户原来拥有的奖励
	 * @return 	bool 	正确返回false | 错误返回true
	 * @return 	string 	msg			返回字符说明
	 */
	function obtainward($wx_id,$type,$task_id,$newlevel,$reward_ids){
		if (!isset($this->reward)||!isset($this->user_have)||
			!is_numeric($wx_id)||!is_numeric($type) || !is_numeric($task_id)) {
			$this->msg='get_fail';
			return false;
		}
		$select_reward_id = '';
		$rewards = $this->reward;
		$userhave = $this->user_have;
		$time = time();
		//判断奖金减去基金后是否还有基金，并赋新值
		if ($task_id!=17&&$task_id!=18) {
		    if (($userhave['center_fund']+=($rewards['fund']-$rewards['bonus']))<0) {
		    	$this->msg = 'no_fund';
                return false;
		    }
		}
		$update=array(//用户拥有的奖励
			'center_fund'=>$userhave['center_fund'],
			'center_bonus'=>$userhave['center_bonus']+$rewards['bonus'],
			'center_integral'=>$userhave['center_integral']+$rewards['integral'],
			'center_all_integral'=>$userhave['center_all_integral']+$rewards['all_integral']
		);
		if (!empty($newlevel)) {//看是否升级
			if (!is_numeric($newlevel)) {
				$this->msg = 'get_fail';
				return false;
			}
			$update['level_num'] = $newlevel;
		}
		$update['center_updatetime'] = $time;
		$where = array( 'wx_id'=>$wx_id,'center_status'=>'1' );
		foreach ($reward_ids as $k => $v) {
			if (!is_numeric($v)) {
				return false;
			}
			$select_reward_id .= $v.' ';
		}
		$select_reward_id=rtrim($select_reward_id,' ');//加入数据之前去掉最后一个空格
		$uptasklog = array(
			'task_process' => 4,
			'reward_gettime' => $time,
			'task_updatetime' => $time,
			'reward_id' => $select_reward_id,
		);
 		($rewards['fund'] > 0 || $rewards['bonus'] > 0)?$uptasklog['is_obtail_money'] = 1:$uptasklog['is_obtail_money'] = -1;//表示用户领取了基金 不算公益档案中
 		($task_id==7||$task_id==8||$task_id==15)?$uptasklog['cycle_is_finish']=1:'';
 		$uptasklog['log_share']=($this->shde==1)?1:0;
 		$logwhere = array('wx_id'=>$wx_id,'task_id'=>$task_id,'cycle_is_finish'=>-1,'task_status'=>1);
		$this->db->update($this->wxuser_task,$update,$where);//奖励更新
		$this->db->update($this->task_log,$uptasklog,$logwhere);//奖励更新
		if ($rewards['bonus']>0) {
        	$con = $this->config->item('task_types');
			$task_name = $con[$type];
			$coop_log = array(
	    	    'log_userid' => $wx_id,'log_total' => $rewards['bonus'] * 100,
	    	    'log_title' => '做'.$task_name.'任务的收入','log_result' => 1,
	    	    'log_jointime' => time(),
	    	);
	    	// 更新微信用户余额
			$sql = 'update '.$this->wxuser.' set wx_balance=wx_balance+'.$rewards['bonus']*(100).',
					wx_updatetime="'.date('Y-m-d H:i:s').'" where wx_id='.$wx_id.' and (wx_status=1 or wx_status=-1)';
			$this->db->query($sql);
	    	$this->db->insert($this->bill_log,$coop_log);
		}
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
		if ($this->zredis->link===true) {
			$ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$wx_id.':'.$task_id.':*');
			if (!empty($ishavetask)) {//没有缓存直接跳过。让其它界面生成缓存。
				$ttl = $this->zredis->_redis->TTL($ishavetask['0']);//获取过期时间
				$arr = $this->zredis->_redis->HGETALL($ishavetask['0']);
				if (isset($uptasklog['cycle_is_finish'])&&$uptasklog['cycle_is_finish']==1) {
					$arr['task_process'] = $arr['log_id'] = $arr['reward_gettime'] = $arr['wx_id'] = $arr['task_jointime'] = $arr['task_overtime']  = '';
				}else{
					$arr['task_process'] = $uptasklog['task_process'];
				    $arr['reward_gettime'] = $uptasklog['reward_gettime'];
				}
				$str = $this->zredis->_redis->HMSET($ishavetask['0'],$arr);
				if ($str!=true) return false;
				$str = $this->zredis->_redis->EXPIRE($ishavetask['0'],$ttl);
				if ($str!=true) return false;
			}	
		}
		return true;
	}
	/**
	 * 获取奖励的值 和 用户现有的奖励
	 * @param	int 		wx_id  		用户的id
	 * @param 	int 		task_id 	任务id
	 * @param	array 		reward_id 	任务的id数组
	 * @return  bool 		false 	错误|返回false
	 * @return 	array 		result 	正确|返回奖励的数组，任务的信息，用户的原来用有的奖励
	 */
	function checktask($wx_id,$task_id,$reward_id){
		if (!is_numeric($wx_id) || !is_numeric($task_id)) {
			return false;
		}
		$where = '';
		$result = array(
			'reward' => array(
				'integral' => 0,
				'all_integral' => 0,
				'bonus' => 0,
				'fund' => 0,
			)
		);
		$select = 'reward_id,reward_num,task_type,task_limit_time,info_name,task_difcontent';//获得任务的信息
		$sql = 'select '.$select.' from '.$this->task_info.' where task_id='.$task_id.' and task_status=1';
		$task = $this->db->query($sql);
		if ($task->num_rows<=0) {
			return false;
		}
		$task = $task->result_array();
		$this->shde = json_decode($task['0']['task_difcontent'],true)['shde'];
		if (count($reward_id)!=$task['0']['reward_num']) {
			return false;
		}
		$task['0']['rewards'] = array();
		$reward_ids = explode(' ',$task['0']['reward_id']);
		foreach ($reward_ids as $k => $v) {
			$where .= 'reward_id='.$v.' or ';
		}
		$where = rtrim($where,' or ');//获取任务奖励的信息
		$sql = 'select reward_id,reward_type,reward_bonus,reward_integral,reward_all_integral,reward_fund from '.$this->task_reward.' where '.$where.' and reward_status=1';
		$rewards=$this->db->query($sql)->result_array();
		foreach ($rewards as $k => $v) {
			$task['0']['rewards'][]=$v;
		}
		foreach ($reward_id as $k => $v) {//获得用户该得的奖励
			foreach ($task['0']['rewards'] as $ke => $va) {
				if ($v==$va['reward_id']) {
					$result['reward']['integral'] += $va['reward_integral'];
					$result['reward']['all_integral'] += $va['reward_all_integral'];
					$result['reward']['bonus'] += $va['reward_bonus'];
					$result['reward']['fund'] += $va['reward_fund'];
				}
			}
		}
		if ($result['reward']['integral']==0&&$result['reward']['all_integral']==0&&
			$result['reward']['bonus']==0&&$result['reward']['fund']==0) {
			return false;
		}
		$sql = 'select center_integral,center_all_integral,center_bonus,center_fund,level_num,center_sign,
				center_laster_sign from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status=1';//获取用户的信息
		$userinfo = $this->db->query($sql);
		if ($userinfo->num_rows<=0) {
			return false;
		}	
		$result['userinfo'] = $userinfo->result_array();
		$result['tasktype'] = $task['0']['task_type'];
		$result['taskname'] = $task['0']['info_name'];
		return $result;
	}
	/**
	 * 做完任务后，检查邀请
	 * @param 	int 	wx_id
	 * @param 	array 	userinfo 	用户的信息
	 * @param 	bool 	true|数据不正常false
	 */
	function Chinviter($wx_id,$userinfo){
		$sql = 'select center_jointime from '.$this->wxuser_task.' where wx_id='.$wx_id;
		$result = $this->db->query($sql);
		if ($result->num_rows<=0) {
			return false;
		}
		$result = $result->result_array();
		if ($result['0']['center_jointime']<1456416000) {//加入时间早于2016年2月26号不计
			return true;
		}
		$sql = 'select count(wx_id) as num from '.$this->task_log.' where wx_id='.$wx_id.' and task_process=4 and task_status=1';
		$result = $this->db->query($sql);
		if ($result->num_rows<=0) {
			return false;
		}
		$result = $result->result_array();
		if (!(($userinfo['center_sign']==1&&$result['0']['num']==0)||($userinfo['center_sign']==0&&$result['0']['num']==1))) {
			return true;//说明不是第一次做任务。
		}
		$sql = 'select wx_invitation from '.$this->wxuser.' where wx_id='.$wx_id;
		$result = $this->db->query($sql);
		if ($result->num_rows<=0) {
			return false;
		}
		$result = $result->result_array();
		if ($result['0']['wx_invitation']==''||$result['0']['wx_invitation']=='undefine') {//说明不是被邀请的
			return true;//直接返回正确。
		}
		$extend_num = $result['0']['wx_invitation'];
		$sql = 'select wx_id,center_invite_u_num from '.$this->wxuser_task.' where center_extend_num="'.$extend_num.'" and center_status=1';
		$result = $this->db->query($sql);
		if ($result->num_rows<=0) {
			return true;//号可能被封，或者没有此人，直接跳过。
		}
		$result = $result->result_array();
		//检查此ip注册人数
		$ip_result = $this->vicInviter($extend_num);
		if ($ip_result==false) {
			return true;
		}
		// $ip=$this->input->ip_address();
		// $sql = 'select wx_id from '.$this->wxuser.' where wx_invitation="'.$extend_num.'" and wx_loginip="'.$ip.'"';
		// $ip_result = $this->db->query($sql);
		// if ($ip_result->num_rows>=8) {//同一个ip下注册人数过多。
		// 	return true;
		// }
		//任务完成后验证注册系统的会员是否是合法会员
		$sql='select a.wx_openid as openid,a.wx_img as img from h_wxuser a where 
			a.wx_invitation="'.$extend_num.'" and a.wx_regtime>CURDATE() and a.wx_logintime!=""';
		$user_query=$this->db->query($sql);
		$user_result= $user_query->result_array();
		foreach($user_result as $v){
			if($v['openid']!='' && $v['img']!=''){
				$sql = 'update '.$this->wxuser_task.' set center_invite_u=center_invite_u+1,center_updatetime='.time().'
				where center_extend_num="'.$extend_num.'" and center_status=1';
				$this->db->query($sql);
				if($this->db->affected_rows() != 1){
					$this->msg='';
					return false;
				}
			}else{
				return false;
			}
		}
		
		$sql = 'update '.$this->task_cycle.' set type_num=type_num+1,cycle_updatetime='.time().' where wx_id='.$result['0']['wx_id'].' and cycle_is_finish=-1 and cycle_num='.$result['0']['center_invite_u_num'].' and cycle_task_type=5';
		$this->db->query($sql);
		if($this->db->affected_rows() != 1){
            $this->msg='';
            return false;
        }
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载此处要使用redis。
		if($this->zredis->link === true){
			$str = $this->zredis->_redis->EXISTS('tasks_cycle:'.$result['0']['wx_id'].':'.'5');//查看邀请者有没有缓存
			if ($str==0) {//不存在直接跳过，让其它界面生成redis
				return true;
			}
			$ttl = $this->zredis->_redis->TTL('tasks_cycle:'.$result['0']['wx_id'].':'.'5');//获取过期时间
			$arr = $this->zredis->_redis->HGETALL('tasks_cycle:'.$result['0']['wx_id'].':'.'5');
			$arr['type_num'] += 1;
			$str = $this->zredis->_redis->HMSET('tasks_cycle:'.$result['0']['wx_id'].':'.'5',$arr);
			if($str!=true) return false;
			$str = $this->zredis->_redis->EXPIRE('tasks_cycle:'.$result['0']['wx_id'].':'.'5',$ttl);
			if ($str!=true) return false;
		}
		return true;
	}
	/**
	 * 验证邀请是否合法
	 * @param    extend_num 		邀请码
	 */
	function vicInviter($extend_num){
		$ip=$this->input->ip_address();
		$sql = 'select wx_id,wx_loginip from '.$this->wxuser.' where wx_invitation="'.$extend_num.'"';
		$ip_result = $this->db->query($sql);
		if ($ip_result->num_rows<=0) {//同一个ip下注册人数过多。
			return false;
		}
		$ip_num = array();//记录每个域名的个数
		$num_two = 0;//数量等于2的域名数量
		$big_three = 0;//数量大于等于3的域名数量
		$ip_result = $ip_result->result_array();
		foreach ($ip_result as $k => $v) {
			if (isset($ip_num[$v['wx_loginip']])) {
				$ip_num[$v['wx_loginip']]++;
				if ($ip_num[$v['wx_loginip']]==2) {
					$num_two++;//同ip等于2的ip个数
				}
				if ($ip_num[$v['wx_loginip']]>=3) {
					$big_three++;//同ip大于等于3的ip个数
				}
			}else{
				$ip_num[$v['wx_loginip']]=1;
			}
		}
		if (isset($ip_num[$ip])&&$ip_num[$ip]>4) {
			return false;
		}
		if ($num_two>=3||$big_three>=2) {
			return false;
		}
		return true;
	}
	/**
	 * 通花日志记录
	 * @param 	int      wx_id 		奖励的信息
	 * @param 	int      num		任务的类型
	 * @param 	string   title    	用户的名字
	 * @return 	bool     result		返回值
	 */
	function thlog($wx_id,$num,$title){
		$tonghua_log = array(
			'log_userid' => $wx_id,
			'log_total' => $num,
			'log_content' => $title,
			'log_status' => 1,
			'log_jointime' => time(),
		);
		$result = $this->db->insert($this->tonghua_log,$tonghua_log);
		if($this->db->affected_rows() != 1){
            $this->msg='';
            return false;
        }
		return $result;
	}
	/**
	 * 首页滚动条  只放入redis中
	 * @param 	array 			奖励的信息
	 * @param 	type 			任务的类型
	 * @param 	session name 	用户的名字
	 * @param 	taskname 		任务的名称
	 */
	function IndexScroll($rewards,$type,$taskname){
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
		if ($this->zredis->link === false) {
			return false;
		}
		$redis_lun = '';
		if ($rewards['bonus']>0) {
			$redis_lun .= ' '.$rewards['bonus'].'元奖金';
		}
		if ($rewards['integral']>0) {
			$redis_lun .= ' '.$rewards['integral'].'通花';
		}
		if ($rewards['all_integral']>0) {
			$redis_lun .= ' '.$rewards['all_integral'].'成长值';
		}
		if ($rewards['fund']>0) {
			$redis_lun .= ' '.$rewards['fund'].'元基金';
		}
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
	}
}