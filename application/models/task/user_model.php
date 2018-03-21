<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {

	private $wxuser_task  	 = 'h_wxuser_task';//用户任务信息表
	private $wxuser_level 	 = 'h_wxuser_level';//升级名称列表
	private $task_log     	 = 'h_task_log';//任务日志表
	private $task_reward  	 = 'h_task_reward';//任务奖励表
	private $user_level_info = 'h_user_level_info';//用户历史升级信息表
	private $task_cycle		 = 'h_task_cycle';//循环第几次数
    private $table_wxuser	 = 'h_wxuser';//用户表
    public $extends = '';

	/**
	 * 功能：判断是否有此用户,没有直接插入
	 * @param 	wx_id 		用户的id
	 * @param   string   	field          要取得的字段(字符串必须以“,”开头)
	 * @return 	true|array 		查到用户则返回用户信息，无信息则插入信息 成功返回true
	 */
	public function is_have_user($wx_id,$field=''){
		$sql = 'select center_id,center_status'.$field.' from '.$this->wxuser_task.' where wx_id='.$wx_id;
		$data = $this->db->query($sql)->result_array();
		if ($data===false) {
			return false;
		}
		if (!empty($data) && $data['0']['center_status']=='-1') {//查看号是否被封
			exit('no');
		}
		if (!empty($data)) {//如果有此号，直接返回数组
			return $data;
		}
		//如果用户中心没有此用户信息，则新插入一个。
		$is_have = 0;
		while ($is_have == 0) {
			$center_extend_num = substr(implode(NULL, str_split(substr(uniqid(), 7, 13), 1)), 0, 8);
			$sql = 'select wx_id from '.$this->wxuser_task.' where center_extend_num="'.$center_extend_num.'"';
			$str = $this->db->query($sql)->result_array();
			if (!empty($str)) {
				$is_have = 0;
			}else{
				$is_have = 1;
			}
		}
		if (empty($_SESSION['userinfo']['user_name'])) {$user_name='';}else{
			$user_name=(!get_magic_quotes_gpc()? addslashes($_SESSION['userinfo']['user_name']):'');
		}
		if (empty($_SESSION['userinfo']['user_img'])) {$wx_img='';}else{
			$wx_img=(!get_magic_quotes_gpc()? addslashes($_SESSION['userinfo']['user_img']):'');
		}
		$sql = 'insert into '.$this->wxuser_task.'(wx_id,wx_name,wx_img_face,center_extend_num,center_jointime,center_fund,center_integral) 
		        value('.$wx_id.',"'.$user_name.'","'.$wx_img.'","'.$center_extend_num.'",'.time().',50,300)';
		$str = $this->db->query($sql);
		if ($str === false) {
			return false;
		}
		$this->extends = $center_extend_num;
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
	    if ($this->zredis->link===true) {
			$str = $this->zredis->_redis->exists('task_user');
			if ($str==1) {
				$this->zredis->_redis->incr('task_user');
			}
    	}
		return true;
	}
	/**
	 * 注册获得的奖励
	 */
    function regReward($wx_id){
        $sql = 'update '.$this->wxuser_task.' set center_fund=50,center_integral=center_integral+300,
                center_updatetime="'.time().'" where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($result && $this->db->affected_rows() == 1) {
        	return true;
        }else{
        	return false;
        }
    }
	public function usertaskinfo($wx_id){//获取用户信息以及其等级信息
		$select = 'a.center_id as center_id,a.wx_id as wx_id,a.level_num as level_num,a.level_id as level_id,
		           a.center_extend_num as center_extend_num,a.center_integral as center_integral,
		           a.center_all_integral as center_all_integral,a.center_bonus as center_bonus,a.center_fund as center_fund,
		           a.center_turnover as center_turnover,a.center_turnover_num as center_turnover_num,
		           a.center_invite_u as center_invite_u,a.center_invite_u_num as center_invite_u_num,
		           a.center_invite_m as center_invite_m,a.center_invite_m_num as center_invite_m_num,a.center_share as center_share,
		           a.center_laster_share as center_laster_share,a.center_share_num as center_share_num,a.center_sign as center_sign,
		           a.center_sign_num as center_sign_num,a.center_conum as center_conum,a.center_laster_sign as center_laster_sign,
		           a.center_klegdtime,a.center_plgametime,b.level_name as level_name,b.level_num as y_level_num,
		           a.is_have_attention as is_have_attention,b.level_integral as level_integral,b.level_img as level_img';
		$sql = 'select '.$select.' from '.$this->wxuser_task.' as a,'.$this->wxuser_level.' as b where a.wx_id='.$wx_id.' and a.level_id=b.level_id and center_status=1';
		$data = $this->db->query($sql)->result_array();
    	$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	$ishave=$taskuptime=$noftime='';
    	if ($this->zredis->link===true) {
    		$ishave = $this->zredis->_redis->KEYS('tasks_cycle:'.$wx_id.':*');
			$isset = $this->zredis->_redis->EXISTS('taskinfouptime');
			$noftime = $this->zredis->_redis->GET('noFtask:'.$wx_id.':jointime');
			if ($isset==0) {//判断后台是否更新了任务
				$time = time();
				$this->zredis->_redis->set('taskinfouptime',$time);
				$taskuptime = $time;
			}else{
				$taskuptime = $this->zredis->_redis->get('taskinfouptime');
			}
    	}
		
		if (empty($ishave)|| (!empty($ishave)&&($noftime!=$taskuptime))) {
			$sql = 'select wx_id,cycle_num,cycle_task_type,type_num,cycle_is_finish,cycle_status from '.$this->task_cycle.' where wx_id='.$wx_id.' and cycle_is_finish=-1';
			$cycle_data = $this->db->query($sql)->result_array();
			if (!empty($cycle_data)&&$this->zredis->link===true) {
				foreach ($cycle_data as $k => $v) {
					$str = $this->zredis->_redis->HMSET('tasks_cycle:'.$wx_id.':'.$v['cycle_task_type'],$v);//加入redis
					if ($str===false) {
						return false;
					}
					$str = $this->zredis->_redis->EXPIRE('tasks_cycle:'.$wx_id.':'.$v['cycle_task_type'],7200);//设置时间
					if ($str!=true) return false;
				}
			}
		}else{
			foreach ($ishave as $k => $v) {
				$cycle_data[] = $this->zredis->_redis->HGETALL($v);//在redis获得数据
			}
		}
		if (!empty($cycle_data)) {
			foreach ($cycle_data as $k => $v) {
				switch ($v['cycle_task_type']) {
					case '1':
						$data[0]['this_cycle']['center_sign'] = $v['type_num'];
						break;
					case '2':
						$data[0]['this_cycle']['center_turnover'] = $v['type_num'];
						break;
					case '3':
						$data[0]['this_cycle']['center_share'] = $v['type_num'];
						break;
					case '5':
						$data[0]['this_cycle']['center_invite_u'] = $v['type_num'];
						break;
					case '6':
						$data[0]['this_cycle']['center_invite_m'] = $v['type_num'];
						break;
					default:
						
						break;
				}
			}
		}
		$value = '';//判断是否是新的循环
		$redis_cycle=array();
		if (!isset($data[0]['this_cycle']['center_sign'])) {
			$value .= '('.$wx_id.','.$data[0]['center_sign_num'].',1,0,'.time().'),';
			$data[0]['this_cycle']['center_sign'] = 0;
			$redis_cycle['0']['cycle_task_type'] = 1;
			$redis_cycle['0']['cycle_num'] = $data[0]['center_sign_num'];
		}
		if (!isset($data[0]['this_cycle']['center_turnover'])) {
			$value .= '('.$wx_id.','.$data[0]['center_turnover_num'].',2,0,'.time().'),';
			$data[0]['this_cycle']['center_turnover'] = 0;
			$redis_cycle['1']['cycle_task_type'] = 2;
			$redis_cycle['1']['cycle_num'] = $data[0]['center_turnover_num'];
		}
		if (!isset($data[0]['this_cycle']['center_share'])) {
			$value .= '('.$wx_id.','.$data[0]['center_share_num'].',3,0,'.time().'),';
			$data[0]['this_cycle']['center_share'] = 0;
			$redis_cycle['2']['cycle_task_type'] = 3;
			$redis_cycle['2']['cycle_num'] = $data[0]['center_share_num'];
		}
		if (!isset($data[0]['this_cycle']['center_invite_u'])) {
			$value .= '('.$wx_id.','.$data[0]['center_invite_u_num'].',5,0,'.time().'),';
			$data[0]['this_cycle']['center_invite_u'] = 0;
			$redis_cycle['3']['cycle_task_type'] = 5;
			$redis_cycle['3']['cycle_num'] = $data[0]['center_invite_u_num'];
		}
		if (!isset($data[0]['this_cycle']['center_invite_m'])) {
			$value .= '('.$wx_id.','.$data[0]['center_invite_m_num'].',6,0,'.time().'),';
			$data[0]['this_cycle']['center_invite_m'] = 0;
			$redis_cycle['4']['cycle_task_type'] = 6;
			$redis_cycle['4']['cycle_num'] = $data[0]['center_invite_m_num'];
		}
		if ($value!='') {
			$value = rtrim($value,',');
			$sql = 'insert into '.$this->task_cycle.'(wx_id,cycle_num,cycle_task_type,type_num,cycle_jointime) value'.$value.';';
			$str = $this->db->query($sql);
			if (!$str) {
				return false;
			}
			if ($this->zredis->link===true) {
				foreach ($redis_cycle as $k => $v) {
					$str = $this->zredis->_redis->HMSET('tasks_cycle:'.$wx_id.':'.$v['cycle_task_type'],array('wx_id'=>$wx_id,'cycle_num'=>$v['cycle_num'],'cycle_task_type'=>$v['cycle_task_type'],'type_num'=>'0','cycle_is_finish'=>'-1','cycle_status'=>'1'));//加入redis
					if ($str===false) {
						return false;
					}
					$str = $this->zredis->_redis->EXPIRE('tasks_cycle:'.$wx_id.':'.$v['cycle_task_type'],7200);
					if ($str!=true) return false;
				}	
			}	
		}
		return $data;
	}

	/**
	*
	*判断用户是否可以取得新称号，如果可以取得则调出升到下一个称号的信息，并且得到升到下一级的信息
	*
	*/
	public function userandlevel($wx_id){

		$sql = 'select a.wx_name,a.wx_img_face,a.level_num,a.level_id,a.center_extend_num,a.center_integral,a.center_all_integral,a.center_bonus,a.center_fund,a.center_turnover,a.center_turnover_num,a.center_invite_u,a.center_invite_u_num,a.center_invite_m,a.center_invite_m_num,a.center_share,a.center_share_num,a.center_sign,a.center_sign_num,a.center_conum,a.center_laster_sign,a.is_have_attention,b.level_id as n_level_id,b.level_num as n_level_num,b.level_name,b.level_integral,b.level_img from '.$this->wxuser_task.' as a,'.$this->wxuser_level.' as b where a.wx_id='.$wx_id.' and a.level_id=b.level_id and a.center_status=1 and b.level_status=1';
		$data = $this->db->query($sql)->result_array();

		//以下为获取等级信息
		$next_level_num = $data[0]['n_level_num']+1;
		$sql = 'select level_id,level_num,level_integral from '.$this->wxuser_level.' where level_num='.$next_level_num.' and level_status=1 limit 1';
		$next_level = $this->db->query($sql)->result_array();

		$userinfo = $data[0];
		if ($data[0]['level_num']!=$data[0]['n_level_num']) {
			$sql = 'select level_id,level_num,level_name,level_integral,level_img from '.$this->wxuser_level.' where level_num='.$next_level_num.' and level_status=1';
			$can_select = $this->db->query($sql)->result_array();
			$userinfo['can_select'] = $can_select;
		}
		// var_dump($userinfo);exit;

		if (empty($next_level)) {
			$userinfo['next_level'] = 'no';
		}else{
			$userinfo['next_level'] = $next_level[0];
		}

		return $userinfo;

	}

	/**
	*
	*用户选取要升级的等级后，获取用户和等级信息用以判断。
	*
	*/
	public function getlevel($level_id,$wx_id){

		$sql = 'select level_num,level_id,level_name,level_integral,level_img from '.$this->wxuser_level.' where level_id='.$level_id.' and level_status=1';
		$levelinfo = $this->db->query($sql)->result_array();

		$sql = 'select level_id,level_num from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status=1';
		$userinfo = $this->db->query($sql)->result_array();

		$sql = 'select count(*) as level_num from '.$this->user_level_info.' as a,'.$this->wxuser_level.' as b where a.wx_id='.$wx_id.' and a.level_id=b.level_id and b.level_num='.$levelinfo[0]['level_num'].' and b.level_status=1 and a.level_status=1';
		$level_num = $this->db->query($sql)->result_array();
		
		$data = array(
			'levelinfo' => $levelinfo[0],
			'userinfo' => $userinfo[0],
			'level_num' => $level_num[0]
		);
		return $data;
	}

	public function up_level($level_id,$wx_id){//更新用户与等级的关联
		$up_user['level_id'] =$insert_data['level_id']= $level_id;
		$up_user['center_updatetime'] = $insert_data['level_info_jointime'] = time();
		$insert_data['wx_id'] = $wx_id;
		$where = 'wx_id = '.$wx_id.' and center_status = 1';

		$str = $this->db->update($this->wxuser_task,$up_user,$where);
		if (!$str) {
        	return false;
        }
        $str = $this->db->insert($this->user_level_info,$insert_data);
        if (!$str) {
        	return false;
        }
	}


	/**
	*
	*获取用户勋章。传入wx_id，输出用户选择过的用户等级信息。
	*
	*/
	public function level_medal($wx_id){

		$sql = 'select b.level_name,b.level_img,b.level_num,b.level_content_url,b.level_integral from '.$this->user_level_info.' as a,'.$this->wxuser_level.' as b where a.wx_id='.$wx_id.' and a.level_id=b.level_id and a.level_status=1 and b.level_status=1 order by b.level_num desc';
		$levels = $this->db->query($sql)->result_array();
		return $levels;

	}

	/**
	*
	*任务中心
	*
	*/
	public function taskcenterinfo($wx_id){
		//以下获取用户今天完成任务获取的树苗量
		$all_add_integral = 0;
		$where = '';
		$todayftime = strtotime(date('Y-m-d'));
		$sql = 'select reward_id from '.$this->task_log.' where reward_gettime>='.$todayftime.' and wx_id='.$wx_id;
		$todayobtain = $this->db->query($sql)->result_array();
		
		if (!empty($todayobtain)) {//用reward_id得到今日获取的树苗
			foreach ($todayobtain as $k => $v) {
				if (!empty($v['reward_id'])) {
					$str = explode(' ', $v['reward_id']);
					foreach ($str as $ke => $va) {
						$where .= 'reward_id='.$va.' or ';
					}
				}
				
			}
		}
		$where = rtrim($where,' or ');
		if ($where == '') {
			$all_add_integral = 0;
		}else{
			$sql = 'select reward_all_integral,reward_id from '.$this->task_reward.' where '.$where;
			$add_integral = $this->db->query($sql)->result_array();
			foreach ($todayobtain as $k => $v) {
				foreach ($add_integral as $ke => $va) {
					if ($v['reward_id'] == $va['reward_id']) {
						$all_add_integral+=$va['reward_all_integral'];
					}
				}
			}
		}

		if (empty($todayobtain)) {
			$data['all_add_integral'] = 0;
		}else{
			$data['all_add_integral'] = $all_add_integral;
		}

		//以下获取用户未领取奖励的任务总数
		$sql = 'select count(log_id) as alltask from '.$this->task_log.' where wx_id='.$wx_id.' and task_status=1 and (task_process=3) and cycle_is_finish=-1';
		$alltask = $this->db->query($sql)->result_array();
		$data['alltask'] = $alltask[0]['alltask'];

		return $data;

	}

	/**
	 * 等级排名
	 */
	public function userrank(){

		$sql = 'select a.wx_id,a.wx_name,a.wx_img_face,a.center_all_integral,b.level_num,b.level_name from '.$this->wxuser_task.' as a left join '.$this->wxuser_level.' as b on a.level_id=b.level_id where a.center_status=1 order by a.center_all_integral desc limit 0,50';
		$userrank = $this->db->query($sql)->result_array();
		return $userrank;

	}

	/**
	 * 获取某个等级的信息
	 */
	public function get_one_level($level_id){

		$sql = 'select level_img from '.$this->wxuser_level.' where level_id='.$level_id.' and level_status=1';
		$level = $this->db->query($sql)->result_array();
		return $level;
	}
	/**
     * 校验用户是否存在,存在返回用户id。
     * @param  string  oprnid  用户openid
     * @return int     id   微信用户id  
     */
    function check_user($option){
        $sqluser='select wx_id,wx_img,wx_mobile,wx_name,wx_openid from '.$this->table_wxuser.
                 ' where wx_openid="'.$option['openid'].'"';
        $query = $this->db->query($sqluser);
        //没有用户
        if ($query->num_rows() <= 0){
            return 0;
        }
        $userdata=$query->result_array();
        return $userdata;
    }

    /**
     * 获取任务中心的总用户 
	 *@return int 	allUser  总用户
     */
    function get_Auser(){
    	$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	if ($this->zredis->link===true) {
    		$str = $this->zredis->_redis->exists('task_user');
			if ($str==1) {
				$allUser = $this->zredis->_redis->get('task_user');
				return $allUser;
			}
    	}
    	$sql = 'select count(center_id) as allUser from '.$this->wxuser_task;
        $query = $this->db->query($sql);

        if ($query->num_rows() <= 0  || !$query){
            return 0;
        }
        $ar_allUser = $query->result_array();
        $allUser = $ar_allUser[0]['allUser'];
    	if ($this->zredis->link===true) {
        	$OK = $this->zredis->_redis->set('task_user',$allUser);
        	if ($OK!=true) return false;
    	}
        return $allUser;
    }
    /**
     * 把邀请者的邀请码加入redis
     * @param    string  extendnum 邀请码
     * @return   bool    错误返回false 
     */
    function redisAddext($extendnum){
    	$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	if ($this->zredis->link===false){
            $_SESSION['userinfo']['extendnum'] = $extendnum;//未开缓存，侧把邀请码存在session中
    		return false;
    	}
    	$userid = $_SESSION['userinfo']['user_id'];
    	if (!is_numeric($userid)||!ctype_alnum($extendnum)) {
    		return false;
    	}
        $result = $this->zredis->_redis->set('inviterExt:'.$userid,$extendnum);
        if ($result===false) {
        	return false;
        }
        $result = $this->zredis->_redis->expire('inviterExt:'.$userid,3600);
        if ($result===false) {
        	return false;
        }
    }
    /**
     * 注册时获取邀请码
     * @param    int      userid    用户id
     * @return   string   extendnum 用户的要求码
     */
    function redisGetext(){
    	$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
    	if ($this->zredis->link===false){
    		if (isset($_SESSION['userinfo']['extendnum'])) {
    			$extendnum = $_SESSION['userinfo']['extendnum'];
    		}else{
    			$extendnum = '';
    		}
    		return $extendnum;
    	}
    	$userid = $_SESSION['userinfo']['user_id'];
    	if (!is_numeric($userid)) {
    		$extendnum = '';
    	}
        $extendnum = $this->zredis->_redis->get('inviterExt:'.$userid);
        if ($extendnum===false) {
        	$extendnum = '';
        }
        return $extendnum;
    }
}
