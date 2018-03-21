<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Otherget_model extends CI_Model {
    /**
     * 获取任务的信息
     */
    function thistask($id){
        $sql = 'select task_difcontent,task_type as type from h_task_info where task_id='.$id.' and task_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
          return false;
        }
        $result = $result->result_array();
        return $result;
    }
	  /**
	   * 获取任务信息
	   * @param        int        id        任务id
	   * @param        int        wx_id     用户id
	   * @return       array      任务的信息
	   */
	  function taskinfo($id,$wx_id){
	  	  $sql = 'select b.log_id,b.task_process,a.task_difcontent,a.task_type as type from 
	  	        h_task_info as a left join h_task_log as b on a.task_id=b.task_id 
              and b.wx_id='.$wx_id.' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_id='.$id.' and a.task_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
        	return false;
        }
        $result = $result->result_array();
        return $result;
	  }
	  /**
	   * 关于投票后的奖励
	   * @param        int        id          任务id
	   * @param        int        wx_id       用户id
	   * @param        string     process     用户任务的过程
	   * @param        array      voinfo      用户投票的信息
	   */
	  function votereward($task_id,$wx_id,$process,$voinfo){
        $time = time();
        $this->db->trans_begin();
        if ($process=='') {
            $input = array(//得到要输入的基本信息
              'wx_id' => $wx_id,
              'task_id' => $task_id,
              'task_jointime' => $time,
              'task_finishtime' => $time,
              'reward_gettime' => $time,
              'task_process' => 4,
              'log_content' => json_encode($voinfo),
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($input);//插入信息
            if ($return==false) {
                $this->db->trans_rollback();
                return false;
            }
        }elseif ($process==2) {
            $update_tasklog = array(//得到要输入的基本信息
              'task_finishtime' => $time,
              'reward_gettime' => $time,
              'task_updatetime' => $time,
              'task_process' => 4,
              'log_content' => json_encode($voinfo),
            );
            $result = $this->uptaskprocess($wx_id,$task_id,$update_tasklog);
            if ($result===false) {
                $this->db->trans_rollback();
                return false;
            }
        }else{
            Universal::Output($this->config->item('request_fall'),'您已经投过票了','','');
        }
        $integral=(empty($voinfo['other']))?30:50;
	  	  $sql = 'update h_wxuser_task set center_integral=center_integral+'.$integral.',center_updatetime='.$time.' 
                where wx_id='.$wx_id.' and center_status=1';
	  	  $result = $this->db->query($sql);
	  	  if ($this->db->affected_rows()!=1||$result==false) {
                $this->db->trans_rollback();
                return false;
	  	  }
        $this->load->model('task/reward_model');
        $result = $this->reward_model->thlog($wx_id,$integral,'做投票任务获得');
        if ($result==false) {
            $this->db->trans_rollback();
            return false;
        }
        if ($this->db->trans_status() != true) {
            $this->db->trans_rollback();
        }
        $this->db->trans_commit();
        return true;
	  }
    /**
     * @param        int        id        任务id
     * @param        int        wx_id     用户id
     * @return       bool       正确返回true，错误返回false
     */
	public function uptaskprocess($wx_id,$task_id,$update_tasklog){//任务更新到某一个过程
        $where = 'wx_id = '.$wx_id.' and task_id = '.$task_id.' and cycle_is_finish=-1 and task_status = 1';
        $str = $this->db->update('h_task_log',$update_tasklog,$where);
        if ($this->db->affected_rows()!=1||$str==false) {return false;}
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
     * 用户购买商品时，如果从分享的进去则加入邀请码
     * @param        string        extendnum       邀请码
     * @param        int           goodid          商品的id
     * @param        int           wx_id           用户的id
     * @param        bool          正确返回true|错误返回false
     */
    public function shopinvite($extendnum,$goodid){
        if (!is_numeric($goodid)||!is_numeric($userid=$_SESSION['userinfo']['user_id'])||!ctype_alnum ($extendnum)) {
            return false;
        }
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===false){
            return false;
        }
        $result = $this->zredis->_redis->set('shop_invite_'.$goodid.'_'.$userid,$extendnum);
        if ($result===false) {
            return false;
        }
        $result = $this->zredis->_redis->expire('shop_invite_'.$goodid.'_'.$userid,3600);
        if ($result===false) {
            return false;
        }
    }
    /**
     * 用户购买，获取邀请码
     * @param        int           goodid          商品的id
     */
    public function shopgetextend($goodid,$userid){
        if (!is_numeric($goodid)||!is_numeric($userid)) {
            return false;
        }
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===false){
            return false;
        }
        $extendnum = $this->zredis->_redis->get('shop_invite_'.$goodid.'_'.$userid);
        if ($extendnum===false) {
            return false;
        }
        return $extendnum;
    }
    /**
     * 获取有此用户邀请码的订单
     * @param        int        wx_id        用户的id
     * @return       array      data         所有订单信息
     */
    function getshopinfo($wx_id){
        $center_info = $this->tasks_model->getextentnum($wx_id);//获取用户的推广码
        $sql = 'select record_id,record_divide,record_price,record_time,record_userid 
                from h_shop_record where record_invitation="'.$center_info['0']['center_extend_num'].'" 
                and record_istake=0 and (record_status=1 or record_status=2)';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1 || $result===false) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取用户购买的订单
     * @param        int        wx_id        用户的id
     * @return       array      data         返回可提现/待提现的金额
     */
    function checkshopinfo($wx_id){
        $result = $this->getshopinfo($wx_id);
        if ($result===false) {
            return false;
        }
        $time = time();
        $data['cantake'] = $data['havetake'] = 0.0;
        $data['closetime'] = 0;//离提成最近的时间
        foreach ($result as $k => $v) {
            if ($v['record_userid']==$wx_id) {
                return false;
            }
            if (($v['record_time']+604800)<$time) {
                $data['cantake'] += (intval($v['record_divide']*$v['record_price'])/100);
            }else{
                $data['closetime']=($data['closetime']==0||$data['closetime']>$v['record_time'])?$v['record_time']:$data['closetime'];
                $data['havetake'] += (intval($v['record_divide']*$v['record_price'])/100);
            }
        }
        $data['closetime'] = ($data['closetime']==0)?0:($data['closetime'] += 604800)-$time;
        return $data;
    }
    /**
     * 获得提成，更新用户信息
     * @param        string         ids        更新的id
     * @param        int            wx_id      用户的id
     * @param        int            cantake    可以得到的钱数
     * @param        int            num        奖励的订单数量
     */
    function getdivide($ids,$wx_id,$cantake,$num){
        $this->db->trans_begin();
        $sql = 'update h_shop_record set record_istake=1,record_updatetime="'.time().'" where record_id in('.$ids.')';
        $re_record=$this->db->query($sql);
        if ($this->db->affected_rows()!=$num || $re_record===false) {
            $this->db->trans_rollback();
            return false;
        }
        $sql = 'update h_wxuser set wx_balance=wx_balance+'.$cantake.',wx_updatetime="'.date('Y-m-d H:i:s').'" 
                where wx_id='.$wx_id.' and wx_status!=3';
        $re_user = $this->db->query($sql);
        if ($this->db->affected_rows()!=1 || $re_user===false) {
            $this->db->trans_rollback();
            return false;
        }
        $sql = 'insert into h_bill_log(log_userid,log_total,log_title,log_content,log_jointime,log_result) 
                values('.$wx_id.','.$cantake.',"做商品分享提成任务的提成","订单的所有id：'.$ids.'","'.time().'",1)';
        $re_log = $this->db->query($sql);
        if ($this->db->affected_rows()!=1 || $re_log===false) {
            $this->db->trans_rollback();
            return false;
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 注册借贷宝 完成后回调操作
     * @param        int        mobile        回调回来的电话号码
     */
    function taskFshJdb($mobile){
        $sql = 'select wx_id,wx_openid from h_wxuser where wx_mobile='.$mobile.' and wx_status!=3';
        $userinfo = $this->db->query($sql);
        if ($userinfo->num_rows<1 || $userinfo==false) {
            return false;
        }
        $userinfo = $userinfo->result_array();
        $sql = 'select b.log_id,b.task_process from h_task_info as a left join h_task_log as b on a.task_id=b.task_id 
                and b.wx_id='.$userinfo['0']['wx_id'].' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_status=1 and a.task_id=17';
        $result = $this->db->query($sql);
        if ($result->num_rows<1||$result==false) {//没有任务直接返回
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['log_id']=='') {//没有找到任务
            $time = time();
            $input = array(//得到要输入的基本信息
              'wx_id' => $userinfo['0']['wx_id'],
              'task_id' => 17,
              'task_jointime' => $time,
              'task_process' => 6,
              'log_content' => $time,
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($input);//插入信息
            if ($return==false) {
                return false;
            }
            $this->taskchecknum();
            return $userinfo;
        }
        if ($result['0']['task_process']==3) {//有未领取的奖励则直接跳过
            return false;
        }elseif($result['0']['task_process']==2){
            $this->load->model('task/taskfinish_model');
            $str=$this->taskfinish_model->uptaskprocess($userinfo['0']['wx_id'],17,6);
            if ($str==false) {
                return false;
            }
            $this->taskchecknum();
            return $userinfo;
        }
    }
    /**
     * 缓存增加
     */
    function taskchecknum(){
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===true) {
        $str = $this->zredis->_redis->exists('taskwaitvernum_6');
            if ($str==1) {
                $this->zredis->_redis->incr('taskwaitvernum_6');
            }
        }
      return true;
    }
    /**
     * 分享界面获取加成奖励
     * @param           int       task_id       任务的id
     * @param           string    extendnum     邀请码
     * @return          bool      成功返回true|失败返回false
     */
    function shareRward($task_id,$extendnum){
        if (!ctype_alnum($extendnum)) {
            return false;
        }
        $sql = 'select wx_id from h_wxuser_task where center_extend_num="'.$extendnum.'" and center_status=1';//检查是否本人自己点击
        $shareUser = $this->db->query($sql);
        if ($shareUser->num_rows<1) {
            return false;
        }
        $shareUser = $shareUser->result_array();
        if ($shareUser['0']['wx_id']==$_SESSION['userinfo']['user_id']) {
            return false;
        }
        $sql = 'select reward_id,log_id from h_task_log where wx_id='.$shareUser['0']['wx_id'].' and  
                task_id='.$task_id.' and task_process=4 and log_share=1 and task_status=1';//检查任务是否完成
        $taskinfo = $this->db->query($sql);
        if ($taskinfo->num_rows<1) {
            return false;
        }
        $taskinfo = $taskinfo->result_array();
        $result = $this->thistask($task_id);//检查任务是否允许分享后奖励加成
        $shde = json_decode($result['0']['task_difcontent'],true)['shde'];
        if ($shde!=1) {
            return false;
        }
        if (empty($taskinfo['0']['reward_id'])) {
            return false;
        }
        $rewards = implode(explode(' ',$taskinfo['0']['reward_id']), ',');
        $sql = 'select reward_bonus,reward_integral,reward_all_integral from h_task_reward where reward_id in ('.$rewards.') and reward_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        $r = rand(2,12)/100;
        $tr = rand(10,30)/100;
        $integral = intval($tr * $result['0']['reward_integral']);
        $bonus = intval($r * ($result['0']['reward_bonus']*100))/100;
        $time = time();
        $this->db->trans_begin();//开始更新数据
        $sql = 'update h_task_log set log_share=0,task_updatetime='.$time.' 
                where log_id='.$taskinfo['0']['log_id'].' and log_share=1 and task_process=4 and task_status=1';
        $result = $this->db->query($sql);
        if($this->db->affected_rows() != 1||$result==false){
            $this->db->trans_rollback();
            return false;
        }
        $sql = 'update h_wxuser_task set center_integral=center_integral+'.$integral.',
                center_bonus=center_bonus+'.$bonus.',center_updatetime='.$time.' where wx_id='.$shareUser['0']['wx_id'].' and center_status=1';
        $result = $this->db->query($sql);
        if($this->db->affected_rows() != 1||$result==false){
            $this->db->trans_rollback();
            return false;
        }
        if ($bonus>0) {
            $sql = 'update h_wxuser set wx_balance=wx_balance+'.($bonus*100).' where wx_id='.$shareUser['0']['wx_id'].' and wx_status!=3';
            $result=$this->db->query($sql);
            if($this->db->affected_rows() != 1||$result==false){
                $this->db->trans_rollback();
                return false;
            }
            $coop_log = array(
                'log_userid' => $shareUser['0']['wx_id'],'log_total' => $bonus*100,
                'log_title' => '做完任务分享链接获取的提成！','log_result' => 1,
                'log_jointime' => $time,
            );
            $result=$this->db->insert('h_bill_log',$coop_log);
            if($this->db->affected_rows() != 1||$result==false){
                $this->db->trans_rollback();
                return false;
            }
        }
        if ($integral>0) {
            $this->load->model('task/reward_model');
            $result = $this->reward_model->thlog($shareUser['0']['wx_id'],$integral,'做完任务分享链接获取的提成！');
            if ($result === false) {
                $this->db->trans_rollback();
                return false;
            }
        }
        $this->db->trans_commit();
        return array('wxid'=>$shareUser['0']['wx_id'],'integral'=>$integral,'bonus'=>$bonus,'pro'=>$r);
    }
    /**
     * 给获奖励者推送消息
     */
    function sendinfo($info){
        $sql = 'select wx_openid from h_wxuser where wx_id='.$info['wxid'].' and wx_status!=3';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        $this->load->model('common/wxcode_model');
        $t = '';
        if ($info['integral']>0) {
            $t .= $info['integral'].'通花 ';
        }
        if ($info['bonus']>0) {
            $t .= $info['bonus'].'元奖金 ';
        }
        $temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/task/usercenter/taskcenter&response_type=code&scope=snsapi_base&state=#wechat_redirect';
        $sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您获得翻倍奖励",
                    "description":"您分享的链接成功被观看，获得'.$t.'",
                    "url":"%s", "picurl":""}]}}';
        $content = sprintf($sendtext,$result['0']['wx_openid'],$temp_url);
        $response_wx=$this->wxcode_model->sendmessage($content);
    }
}