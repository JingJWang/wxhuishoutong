<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Game_model extends CI_Model {

	private $wxuser_task		 = 'h_wxuser_task';//用户表
    public  $msg                 = '';//用户表
    /**
     * 获取全部游戏的信息
     * @return      array     获取全部游戏
     */
    function getallgameinfo(){
        $sql = 'select game_id as gid,game_freenum as freenum,game_integral as needinter,game_exchange as ex,
                game_playnum as playnum,game_url as url,game_name as name,game_img as img,game_text as text,
                game_limit as man from h_games where game_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取用户玩游戏的信息
     * @param       int       用户id
     * @return      array     获取全部游戏
     */
    function getusergamelog($wx_id){
        $sql = 'select log_game_id as gid,log_score as score,log_jointime as jtime from 
                h_game_log where log_userid='.$wx_id.' and log_status=-1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取游戏信息
     * @param      int      id       游戏的id
     * @return     bool     错误或者没有
     * @return     array    数组
     */
    function getgameinfo(){
        $sql = 'select game_freenum,game_integral,game_exchange,game_limit,game_manscore as manscore from h_games 
                where game_id='.$this->input->post('id',true).' and game_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 更新此游戏的最高分
     */
    function upmanscore(){
        $sql = 'update h_games set game_manscore='.$this->input->post('score',true).',game_uptime='.time().' 
                where game_id='.$this->input->post('id',true);
        $result = $this->db->query($sql);
        if ($this->db->affected_rows()!= 1 || $result==false) {
            return false;
        }
        return true;
    }
    /**
     * 获取通花值
     * @param       int      wx_id         用户的id
     * @return      bool|错误false    array|正确true
     */
    function getintegral($wx_id){
        $sql = 'select center_integral as integral from '.$this->wxuser_task.' where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取用户今天玩的此游戏
     * @param      int      id      游戏的id
     * @param      int      uid     用户的id
     * @return      bool|错误false    array|正确true
     */
    function getlognum($wx_id){
        $sql = 'select count(log_id) as gamenum from h_game_log where log_userid='.$wx_id.' and 
                log_jointime>"'.strtotime(date('Y-m-d')).'" and log_game_id='.$this->input->post('id',true);
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取玩家最近玩的游戏记录(最近两天的)
     * @param       int      id            游戏的id
     * @param       int      wx_id         用户的id
     * @return      bool|错误false    array|正确true
     */
    function getrecentlog($wx_id){
        $sql = 'select log_id,log_jointime,log_status from h_game_log where log_userid='.$wx_id.' 
                and log_game_id='.$this->input->post('id',true).' and log_jointime>="'
                .strtotime(date("Y-m-d",strtotime("-2 day"))).'" order by log_jointime desc limit 1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取用户今天玩的此游戏的全部信息
     * @param       int      wx_id         用户的id
     * @param       int      time          获取此时间段后一天的信息
     */
    function getlog($wx_id,$time){
        $sql = 'select log_id,log_score from h_game_log where log_userid='.$wx_id.' 
                and log_jointime>="'.$time.'" and log_jointime<"'.($time+86400).'" 
                and log_game_id='.$this->input->post('id',true).' and log_status=-1 order by log_score desc limit 1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 扣除通花值
     * @param       int      wx_id         用户的id
     * @param       int      integral     改变通花的数量
     * @return      bool     正确true|错误false
     */
    function upintergral(){
        $this->db->trans_begin();//事务开启
        $time = time();
        if ($this->consume<0) {
            $sql = 'update '.$this->wxuser_task.' set center_updatetime='.$time.',center_integral=center_integral+'
                    .$this->consume.' where wx_id='.$this->wx_id.' and center_status=1';
            $result = $this->db->query($sql);
            if ($this->db->affected_rows()!= 1 || $result==false) {
                $this->db->trans_rollback();
                return false;
            }
            $result = $this->integralog();
            if ($result==false) {
                $this->db->trans_rollback();
                return false;
            }
        }
        $sql = 'update h_games set game_playnum=game_playnum+1,game_uptime='.$time.' where game_id='.$this->input->post('id',true);
        $result = $this->db->query($sql);
        if ($this->db->affected_rows()!= 1 || $result==false) {
            $this->db->trans_rollback();
            return false;
        }
        $result = $this->gametask($this->wx_id);//检查用户玩游戏的任务
        if ($result==false) {
            $this->db->trans_rollback();
            return false;
        }
        $insert = array(
            'log_game_id' => $this->input->post('id',true),
            'log_userid' => $this->wx_id,
            'log_jointime' => time(),
            'log_status' => 1,
        );
        $result = $this->db->insert('h_game_log',$insert);
        if ($this->db->trans_status() === false || $this->db->affected_rows()!= 1 || $result==false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 获取今日最高分
     */
    function getodaynum($wx_id){
        $time = strtotime(date('Y-m-d'));
        $sql = 'select log_score as score from h_game_log where log_userid='.$wx_id.' 
                and log_game_id='.$this->input->post('id',true).' and log_jointime>'.$time.' 
                and log_status=-1 order by log_score desc limit 1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 更新通花和游戏日志
     * @param     int      score       成绩
     * @param     int      logid       日志id
     * @return      bool     正确true|错误false
     */
    function upgameinfo($logid){
        $time = time();
        $sql = 'update '.$this->wxuser_task.' set center_updatetime='.$time.',center_integral=center_integral+'
                .$this->consume.' where wx_id='.$this->wx_id.' and center_status=1';
        $this->db->trans_begin();//事务开启
        $result = $this->db->query($sql);
        if ($this->db->affected_rows()!= 1 || $result==false) {
            $this->db->trans_rollback();
            return false;
        }
        $result = $this->integralog();
        if ($result===false) {
            $this->db->trans_rollback();
            return false;
        }
        $result = $this->upgamelog($logid);
        if ($this->db->trans_status() === false || $result==false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 游戏日志
     * @param     int      score       成绩
     * @param     int      logid       日志id
     * @return      bool     正确true|错误false
     */
    function upgamelog($logid){
        $update = array(
            'log_score' => $this->input->post('score',true),
            'log_status' => -1,
            'log_uptime' => time(),
        );
        $result = $this->db->update('h_game_log',$update,array('log_id'=>$logid));
        if ($this->db->affected_rows()!= 1 || $result==false) {
            return false;
        }
        return true;
    }
    /**
     * 添加通话日志
     */
    function integralog(){
        $tonghua_log = array(
            'log_userid' => $this->wx_id,
            'log_total' => $this->consume,
            'log_content' => '玩游戏增减的通花',
            'log_status' => 1,
            'log_jointime' => time(),
        );
        $result = $this->db->insert('h_tonghua_log',$tonghua_log);
        if($this->db->affected_rows() != 1 || $result==false){
            $this->msg='订单交易出现异常';
            return   false;
        }
        return true;
    }    
    /**
     * 查询当前登录用户抽奖情况
     */
    function checkLuckDraw(){
        //校验当前是否是第一次抽奖
        $sqltime='select turn_id,recturn_status from  h_activity_recturn  where  wx_id='.
             $this->userid.' and recturn_jointime >'.strtotime(date('Y-m-d')).
             ' and recturn_jointime < '.time();
        $result=$this->db->query($sqltime);
        if($result->num_rows < 1){
            return true;
        }       
        //校验时候存在免费抽奖的次数
        $number='select turn_id,recturn_status from  h_activity_recturn where wx_id='.
                 $this->userid.' and  turn_id=8 and recturn_status=1';
        $res=$this->db->query($number);
        if($res->num_rows > 0 ){  
            return true;
        }
        return false;
    }
    /**
     * 获取此用户海滩英雄过的关卡次数
     * @param       string       openid        用户的openid
     */
    function beachlevel($openid,$beachgame){
        $DB_two = $this->load->database('digame',true);
        $sql = 'select stage from `user` where openid="'.$openid.'"';
        $result = $DB_two->query($sql);
        if ($result->num_rows<1) {
            $data['stage']['0']['stage'] = 0;
        }else{
            $data['stage'] = $result->result_array();
        }
        $nowtime = time();
        $intime = strtotime(date('Y-m-d H:0:0',time()));
        $is_get = 0;
        if (($nowtime>$intime&&$nowtime<($intime+1200)&&$beachgame['ex']<$intime)//每隔20分钟更新一次数据
            ||($nowtime>($intime+1200)&&$nowtime<($intime+2400)&&$beachgame['ex']<$intime+1200)
            ||($nowtime>($intime+2400)&&$nowtime<($intime+3600)&&$beachgame['ex']<$intime+2400)){
            $is_get = 1;
        }
        if ($is_get==1) {
            $sql = 'select count(openid) as num from `user`';
            $result = $DB_two->query($sql);
            if ($result->num_rows<1) {
                $DB_two->close();
                return false;
            }
            $num = $result->result_array();
            $sql = 'update h_games set game_exchange="'.$nowtime.'",game_playnum='.$num['0']['num'].',game_uptime="'.$nowtime.'" where game_id='.$beachgame['gid'];
            $result = $this->db->query($sql);
            if ($this->db->affected_rows() != 1) {
                $data['allnum'] = $beachgame['playnum'];
            }else{
                $data['allnum'] = $num['0']['num'];
            }
        }else{
            $data['allnum'] = $beachgame['playnum'];
        }
        $DB_two->close();
        return $data;
    }
    /**
     * 获取海滩任务的信息
     */
    function beachtask($openid){
        $sql = 'select wx_id,wx_mobile from h_wxuser where wx_openid="'.$openid.'" and wx_status!=3';
        $user = $this->db->query($sql);
        if ($user->num_rows<1) {
            $this->msg = 'nouser';
            return false;
        }
        $user = $user->result_array();
        if ($user['0']['wx_mobile']=='') {
            $this->msg = 'nouser';
            return false;
        }
        $this->db->trans_begin();//事务开启
        $result = $this->gametask($user['0']['wx_id']);
        if ($result==false) {
            $this->db->trans_rollback();
            $this->msg = 'error';
            return false;
        }
        if($this->db->trans_status() === true && $result==true){
            $this->db->trans_commit();
            return true;
        }
        return false;
    }
    /**
     * 检查每日玩游戏任务是否完成
     * @param        int        wx_id        用户的id
     * @return       bool       成功返回true失败返回false
     */
    function gametask($wx_id){
        $this->load->model('task/user_model');
        $str = $this->user_model->is_have_user($wx_id,',center_plgametime');//判断用户是否登入过任务中心，不是侧插入。
        if ($str===false) {
            return false;
        }elseif($str === true){
            $plgametime = 0;
        }else{
            $plgametime = $str['0']['center_plgametime'];
        }
        $ttime = strtotime(date('Y-m-d'));
        if ($plgametime>$ttime) {//今天已经玩过，直接返回
            return true;
        }
        $sql = 'select b.log_id,b.task_process from h_task_info as a left join h_task_log as b on a.task_id=b.task_id 
                and b.wx_id='.$wx_id.' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_status=1 and a.task_id=15';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {//没有任务直接返回
            return true;
        }
        $time = time();
        $result = $result->result_array();
        if ($result['0']['log_id']=='') {
            $input = array(//得到要输入的基本信息
              'wx_id' => $wx_id,
              'task_id' => 15,
              'task_jointime' => $time,
              'task_finishtime' => $time,
              'task_process' => 3,
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($input);//插入信息
            if ($return==false) {
                return false;
            }
        }else{
            if($result['0']['task_process']==2){
                $this->load->model('task/taskfinish_model');
                $str=$this->taskfinish_model->uptaskprocess($wx_id,15,3);
                if ($str==false) {
                    return false;
                }
            }
        }
        $sql = 'update h_wxuser_task set center_plgametime='.$time.',center_updatetime='.$time.' 
                where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($this->db->affected_rows()!=1) {
            return false;
        }
        return true;
    }
}
