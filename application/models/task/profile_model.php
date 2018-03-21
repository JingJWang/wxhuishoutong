<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile_model extends CI_Model {

	private $task_log    	 = 'h_task_log';//任务日志表
	private $wxuser_task 	 = 'h_wxuser_task';//用户任务信息表
	private $user_commonweal = 'h_user_commonweal';//档案表
	private $task_info		 = 'h_task_info';//任务信息表
	private $task_reward	 = 'h_task_reward';//任务奖励表
	private $help_user		 = 'h_help_user';//需要帮助表

	/**
	 *
	 *判断获取用户是否有公益档案
	 *
	 */
	public function getuserpro($wx_id){

		$sql = 'select b.center_integral from '.$this->user_commonweal.' as a,'.$this->wxuser_task.' as b where a.wx_id='.$wx_id.' and a.wx_id=b.wx_id and a.commonweal_status=1 and b.center_status=1';
		$isopen = $this->db->query($sql)->result_array();
		if (empty($isopen)) {
			return false;
		}else{
			return $isopen;
		}
		
	}

	/**
	 * 
	 *插入用户公益档案信息
	 *
	 */
	public function adduserpro($wx_id,$data){

		$sql = 'select commonweal_id from '.$this->user_commonweal.' where wx_id='.$wx_id;
		$isset = $this->db->query($sql)->result_array();
		if (!empty($isset)) {
			return false;
		}else{
			$insert_data = array(
				'wx_id' => $wx_id,
				'commonweal_name' => $data['name'],
				'commonweal_sex' => $data['sex'],
				'commonweal_birthdate' => $data['birthday'],
				'commonweal_where_pro' => $data['selectp'],
				'commonweal_where_city' => $data['selectc'],
				'commonweal_jointime' => time()
			);
			$str = $this->db->insert($this->user_commonweal,$insert_data);
			return $str;
		}

	}

	/**
	 * 
	 *插入需要帮助人的信息
	 *
	 */
	public function addneedhelp($post,$wx_id){
		$insert_data = array(
			'help_wx_id' 	  => $wx_id,
			'help_name'  	  => $post['name'],
			'help_idcard' 	  => $post['idcard'],
			'help_iphone' 	  => $post['iphone'],
			'help_address' 	  => $post['address'],
			'help_why'	   	  => $post['why_help'],
			'help_need_money' => $post['need_money'],
			'help_imgs'		  => $post['help_imgs'],
			'help_need_time'  => $post['date'],
			'help_jointime'	  => time()
		);
		$str = $this->db->insert($this->help_user,$insert_data);
		return $str;
	}

	public function taskprofileinfo($wx_id){

		$sql = 'select a.reward_id,a.task_finishtime,b.info_name,b.task_type from '.$this->task_log.' as a,'.$this->task_info.' as b where a.task_id=b.task_id and a.wx_id='.$wx_id.' and a.task_process=4 and a.is_obtail_money=-1';
		$profiles = $this->db->query($sql)->result_array();//获取奖励档案信息
		// var_dump($profiles);
		foreach ($profiles as $k => $v) {//取得每个任务完成后取得的树苗值
			$where = '';
			$reward_ids=explode(' ', $v['reward_id']);//得到奖励的信息
			$reward_num = count($reward_ids);
			for ($i=0; $i < $reward_num ; $i++) { 
				$where .= 'reward_id='.$reward_ids[$i].' or ';
			}
			$where = rtrim($where,' or ');//去除最后的‘ or ’字符
			// var_dump($where);
			$sql = 'select sum(reward_integral) as all_integral from '.$this->task_reward.' where '.$where;
			$rewards = $this->db->query($sql)->result_array();
			$profiles[$k]['get_integral'] = $rewards[0]['all_integral'];
		}	
		
		return $profiles;

	}
}
