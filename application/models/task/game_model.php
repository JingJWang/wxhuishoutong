<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Game_model extends CI_Model {

	private $task_gamerank    	 = 'h_task_gamerank';//任务日志表
	private $task_log			 = 'h_task_log';//任务信息表
	private $wxuser_task		 = 'h_wxuser_task';//用户表

	/**
	 *
	 *添加游戏或更新成绩
	 *
	 */
	public function add_game_score($params){
		$openid = $params["openid"];
		$score = $params["score"];
		$img = $params['img'];
		$name = $params['name'];
		$all_num = $params['all_num'];
		$ret = array();
		if (empty($openid)){
			//id为空，返回错误码-2
			$ret["ret"] = -2;
			return  $ret;
		}
		$sql = 'select * from '.$this->task_gamerank.' where g_openid="'.$openid.'" and wx_status=1';
		$data=$this->db->query($sql);
		//没有用户
		$center_fund = 2*($score/100+10);
        if ($center_fund>100) {
        	$center_fund=100;
        }
        if ($data->num_rows() <= 0){
        	$data = array(
        		'g_openid' => $openid,
        		'g_score'=> $score,
        		'g_image'=> $img,
        		'g_name'=> $name,
        		'count'=> 1,
        		'game_jointime'=>time(),
        		'g_title' => '我带'.$all_num.'人冲出雾霾，获'.$center_fund.'元环保基金，秒提现~敢来比比吗',
        	);

        	$str=$this->db->insert($this->task_gamerank,$data);
        	if ($str == false) {
        		return false;
        	}
        }else{
        	$userdata=$data->result_array();

			$score = $score > $userdata[0]["g_score"] ? $score : $userdata[0]["g_score"];
			$g_title = '我带'.$all_num.'人冲出雾霾，获'.$center_fund.'元环保基金，秒提现~敢来比比吗';
			$sql = 'update '.$this->task_gamerank.' set count=count+1,g_title="'.$g_title.'",g_score='.$score.',game_updatetime='.time().' where g_openid="'.$openid.'"';
			$str=$this->db->query($sql);
        	if ($str == false) {
        		return false;
        	}
		}
	}
    /**
     * 游戏排名
     * @param       string      openid      用户的openid
     * @param       string      type        排行榜的类型
     * @return      array       ret         排名的数组以及用户的成绩和排名
     */
	public function game_ranking($openid,$type){
		$ret = array();
		$sql = 'select game_id,g_name,g_image,g_score from '.$this->task_gamerank.' where `g_openid`="'.$openid.'" and wx_status=1';
		$user=$this->db->query($sql);
		if ($user->num_rows() <= 0){
        	$user=$user->result_array();
			$user['0']['g_score']=0;
        }else{
        	$user=$user->result_array();
        }
        $sql = "select game_id,g_score,g_image,g_name from ".$this->task_gamerank." order by g_score desc limit 0,50";
        $an = $this->db->query($sql); 
        if ($an->num_rows() <= 0) {
            $an = array();
        }else{
            $an =$an->result_array();
        }
		$rankBtn = 0;
		foreach($an as $k => $v){
			if($v['game_id'] == $user['0']['game_id']){
				$rankBtn = $k+1;
			}
            $an[$k]['g_name'] = stripslashes($an[$k]['g_name']);
		}
		$ret["ret"] = 0;
		if ($rankBtn==0) {
			$ret["rank"] = '未达到排名标准';
		}else{
			$ret["rank"] = $rankBtn;
		}
		$ret["score"] = $user['0']['g_score'];
		$ret["rankList"] = $an;
		return  $ret;
	}
	/**
	 * 插入任务
	 */
	public function get_fund_task($data){
		$task_id = 5;
		$sql = 'select log_id,task_process from '.$this->task_log.' where wx_id='.$data['userid'].' and task_id='.$task_id.' and task_status=1';
		$task=$this->db->query($sql);
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
		if ($task->num_rows() <= 0){
        	$input = array(
        		'wx_id' => $data['userid'],
				'task_id' => $task_id,
				'task_jointime' => time(),
				'task_process' => 4,
				'task_finishtime' => time(),
				'is_obtail_money' => 1,
				'reward_gettime' => time(),
       		);
       		$str=$this->db->insert($this->task_log,$input);
			if(!$str){
            	return false;
        	}
            if ($this->zredis->link===true) {
                $ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$data['userid'].':'.$task_id.':*');//缓存添加
                if (!empty($ishavetask)) {//没有缓存直接跳过。让其它界面生成缓存。
                    $ttl = $this->zredis->_redis->TTL($ishavetask['0']);//获取过期时间
                    $arr = $this->zredis->_redis->HGETALL($ishavetask['0']);
                    $arr['task_process'] = $input['task_process'];
                    $arr['log_id'] = $this->db->insert_id();
                    $arr['task_jointime'] = $arr['reward_gettime'] = $input['task_jointime'];
                    $str = $this->zredis->_redis->HMSET($ishavetask['0'],$arr);
                    if ($str!=true) return false;
                    $str = $this->zredis->_redis->EXPIRE($ishavetask['0'],$ttl);
                    if ($str!=true) return false;
                }
            }   
        }else{
        	$task=$task->result_array();
        	if ($task[0]['task_process']!=2) {
        		return 2;
        	}else{
        		//更新任务日志表
        		$update_tasklog['task_process'] = 4;
                $update_tasklog['is_obtail_money'] = 1;
        		$update_tasklog['reward_gettime']=$update_tasklog['task_updatetime']=$update_tasklog['task_finishtime']=time();
        		$where = 'wx_id = '.$data['userid'].' and task_id = '.$task_id.' and cycle_is_finish=-1 and task_status = 1';
        		$str = $this->db->update($this->task_log,$update_tasklog,$where);
        		if(!$str){
        			return false;
        		}
                if ($this->zredis->link===true) {
                    $ishavetask = $this->zredis->_redis->KEYS('noFtask:'.$data['userid'].':'.$task_id.':*');
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
                }  
        	}
        }
        //更新用户信息
        $up_data['center_updatetime'] = time();//更新用户更新时间字段
        $up_data['center_fund'] = $data['fund'];//更新用户基金字段
 		$where = 'wx_id = '.$data['userid'].' and center_status = 1';
        $sql = 'update '.$this->wxuser_task.' set center_fund=center_fund+'.$up_data['center_fund'].',center_updatetime='.$up_data['center_updatetime'].' where '.$where;
		$str = $this->db->query($sql);//更新用户的信息。
		if(!$str){
            return false;
        }
        if ($this->zredis->link===true) {
            $redis_lun = $_SESSION['userinfo']['user_name'].'做游戏，获得'.$data['fund'].'元基金';//任务首页滚动轮播提示条信息
            $taskInScr = $this->zredis->_redis->KEYS('taskIndexScroll');
            if (empty($taskInScr)) {
                $this->zredis->_redis->ZADD('taskIndexScroll',1,$redis_lun);
            }else {
                $scroInfo = $this->zredis->_redis->zrange('taskIndexScroll',0,-1);
                if (count($scroInfo)>5) {
                    $this->zredis->_redis->zrem('taskIndexScroll',$scroInfo['0']);
                    $this->zredis->_redis->ZADD('taskIndexScroll',1,$redis_lun);
                }else{
                    $this->zredis->_redis->ZADD('taskIndexScroll',1,$redis_lun);
                }
            }
        }
        return 1;
	}

	// 玩游戏的总人数
	public function get_paly_num(){
		$sql = 'select count(game_id) as all_num from '.$this->task_gamerank.' where wx_status=1';
		$str = $this->db->query($sql);//更新用户的信息。
		if ($str->num_rows() <= 0) {
			$str = 0;
		}else{
			$str =$str->result_array();
		}
		return $str;
	}

	public function get_title($openid){
		$sql = 'select g_title,wx_status from '.$this->task_gamerank.' where g_openid="'.$openid.'"';
		$str = $this->db->query($sql);//更新用户的信息。
		if ($str->num_rows() <= 0) {
			$title = '来和朋友玩游戏，获取基金兑换奖金吧~';
		}else{
			$str = $str->result_array();
			$title = $str['0']['g_title'];
		}
		return $title;
	}
}
