<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Games extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取全部游戏
     */
    function getgames(){
        $this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION,true);
        if (isset($this->userauth_model->url)) {
            $code = $this->input->post('code',true);
            $_SESSION['LoginBackUrl'] = '/view/games/gameIndex.html';
            if ($code != 'null') {
                $this->load->model('common/wxcode_model');
                $result = $this->userauth_model->wxLogin($code);//用微信登录
                if (!$this->userauth_model->UserIsLogin()) {
                    Universal::Output($this->config->item('request_fall'),'未登录','http://'.$_SERVER['HTTP_HOST'].'/index.php/nonstandard/system/Login','');
                }
            }else{
                Universal::Output($this->config->item('request_fall'),'未登录','http://'.$_SERVER['HTTP_HOST'].'/index.php/nonstandard/system/Login','');
            }
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($wx_id)) {
            Universal::Output($this->config->item('request_fall'),'参数错误','','');
        }
        $this->load->model('task/user_model');
        $str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
        $this->load->model('activity/game_model');
        $games = $this->game_model->getallgameinfo();
        if ($games===false) {
            Universal::Output($this->config->item('request_fall'),'没有游戏可以玩','','');
        }
        $userlogs = $this->game_model->getusergamelog($wx_id);
        if ($userlogs===false) {
            $userlogs = array();
        }
        $time = strtotime(date('Y-m-d'));
        foreach ($games as $k => $v) {
            if ($games[$k]['gid'] == 3) {
                $beachgame = $games[$k];
                continue;
            }
            $games[$k]['tman'] = 0;//今天最高分
            $games[$k]['lman'] = 0;//历史最高分
            $games[$k]['tnum'] = 0;//今天玩的次数
            foreach ($userlogs as $ke => $va) {
                if ($games[$k]['gid']==$userlogs[$ke]['gid']) {
                    $games[$k]['lman'] = ($userlogs[$ke]['score']>$games[$k]['lman'])?$userlogs[$ke]['score']:$games[$k]['lman'];
                    if ($userlogs[$ke]['jtime']>$time) {
                        $games[$k]['tnum']++;
                        $games[$k]['tman'] = ($userlogs[$ke]['score']>$games[$k]['tman'])?$userlogs[$ke]['score']:$games[$k]['tman'];
                    }
                    unset($userlogs[$ke]);
                }
            }
        }
        //获取海滩英雄数据
        if (!isset($_SESSION['userinfo']['user_openid'])) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $openid = Universal::filter($_SESSION['userinfo']['user_openid']);
        $beach = $this->game_model->beachlevel($openid,$beachgame);
        $data['games'] = $games;
        $data['beach'] = $beach;
        //获取大转盘信息
        $this->game_model->userid=$wx_id;
        $luck=$this->game_model->checkLuckDraw();
        $luck === true ? $data['luck'] =1 : $data['luck'] = 0;
        Universal::Output($this->config->item('request_succ'),'','',$data);
    }
    /**
     * 查看用户是否消耗通花
     * @param      int       id        游戏的id
     * @return     int       wx_id     用户id
     * @return     int       lognum    用户此游戏今天玩的数量
     * @return     int       gameinfo  此游戏的信息
     * @return     int       integral  用户用拥有的通话数量
     */
    function isconsume(){
        $this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(!isset($_SESSION['userinfo']['user_id'])){
            Universal::Output($this->config->item('request_fall'),'未登录','','');
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($this->input->post('id',true))||!is_numeric($wx_id)) {
            Universal::Output($this->config->item('request_fall'),'参数错误','','');
        }
        $this->load->model('activity/game_model');
        $lognum = $this->game_model->getlognum($wx_id);
        if (empty($_SESSION['userinfo']['user_mobile'])&&$lognum['0']['gamenum']>=1) {//未注册的玩家能玩一次免费
            Universal::Output($this->config->item('request_fall'),'注册回收通 有红包可领 众多小游戏可玩','/index.php/task/usercenter/isreg','');
        }
        $gameinfo = $this->game_model->getgameinfo();
        if ($gameinfo===false) {
            Universal::Output($this->config->item('request_fall'),'没有此游戏','','');
        }
        $integral = $this->game_model->getintegral($wx_id);
        if ($integral === false) {
            Universal::Output($this->config->item('request_fall'),'没有此用户','','');
        }
        $result = array(
            'lognum' => $lognum['0'],
            'gameinfo' => $gameinfo['0'],
            'integral' => $integral['0'],
            'wx_id' => $wx_id,
        );
        return $result;
    }
    /**
     * 点击游戏开始前调用的数据
     */
    function gameload(){
        $result = $this->isconsume();
        if ($result['lognum']['gamenum']>=$result['gameinfo']['game_freenum']) {
            $data['consume'] = $result['gameinfo']['game_integral'];
        }else{
            $data['consume'] = 0;
        }
        $result = $this->game_model->getodaynum($result['wx_id']);
        if ($result === false) {
            $data['more'] = 0;
        }else{
            $data['more'] = $result['0']['score'];
        }
        $this->load->model('common/wxcode_model','',TRUE);
        $data['signPackage']=$this->wxcode_model->getSignPackageAjax($this->input->post('url',true));
        Universal::Output($this->config->item('request_succ'),'','',$data);
    }
    /**
     * 游戏开始
     * @param      int       id      游戏的id
     * @return     json      json字符串
     */
    function gamestar(){
        $result = $this->isconsume();
        if ($result['lognum']['gamenum']>=$result['gameinfo']['game_freenum']) {
            if (($result['integral']['integral']-$result['gameinfo']['game_integral'])<0) {
                Universal::Output($this->config->item('request_fall'),'通花数不足玩此游戏','','/view/games/gameIndex.html');
            }
            $consume = (-1)*$result['gameinfo']['game_integral'];
        }else{
            $consume = 0;
        }
        $this->game_model->wx_id = $result['wx_id'];
        $this->game_model->consume = $consume;
        $result = $this->game_model->upintergral();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        Universal::Output($this->config->item('request_succ'),'','','');
    }
    /**
     * 游戏结束的操作
     * @param      int       id      游戏的id
     * @param      int       score   成绩
     */
    function endgame(){
        $this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(!isset($_SESSION['userinfo']['user_id'])){
            Universal::Output($this->config->item('request_fall'),'未登录','','');
        }
        if(empty($_SESSION['userinfo']['user_mobile'])){
            Universal::Output($this->config->item('request_fall'),'需要注册才领取奖励哦！','','');
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($this->input->post('id',true))||!is_numeric($wx_id)
            ||!is_numeric($this->input->post('score',true))) {
            Universal::Output($this->config->item('request_fall'),'参数错误','','');
        }
        $this->load->model('activity/game_model');
        $recentlog = $this->game_model->getrecentlog($wx_id);
        if ($recentlog===false||$recentlog['0']['log_status']==-1) {
            Universal::Output($this->config->item('request_fall'),'您没有购买玩此游戏','','');
        }
        $stime = strtotime(date('Y-m-d'));//获取今日凌晨的时间
        if ($recentlog['0']['log_jointime']<=$stime) {//如果此次游戏是前一天购买的
            $time = strtotime(date('Y-m-d',$recentlog['0']['log_jointime']));
        }else{
            $time = $stime;
        }
        $gameinfo = $this->game_model->getgameinfo();
        if ($gameinfo===false) {
            Universal::Output($this->config->item('request_fall'),'没有此游戏','','');
        }
        $thelog = $this->game_model->getlog($wx_id,$time);//获取用户玩游戏的记录
        if ($thelog===false) {//今天没有玩过游戏
            $tscore = 0;
        }else{
            $tscore = $thelog['0']['log_score'];
        }
        //看成绩有没达到奖励的上限
        $escore=($gameinfo['0']['game_limit']>=$this->input->post('score'))?$this->input->post('score'):$gameinfo['0']['game_limit'];
        if (($tong = ceil($escore/$gameinfo['0']['game_exchange'])-ceil($tscore/$gameinfo['0']['game_exchange']))>0) {//向上取整
            $this->game_model->wx_id = $wx_id;
            $this->game_model->consume = $tong;
            $result=$this->game_model->upgameinfo($recentlog['0']['log_id']);
            if ($result===false) {
                Universal::Output($this->config->item('request_fall'),'领奖失败','','');
            }
        }else{
            $tong = 0;
            $result=$this->game_model->upgamelog($recentlog['0']['log_id']);
            if ($result===false) {
                Universal::Output($this->config->item('request_fall'),'领奖失败','','');
            }
        }
        if ($this->input->post('score',true)>$gameinfo['0']['manscore']) {
            $this->game_model->upmanscore();//更新最高分
        }
        Universal::Output($this->config->item('request_succ'),'','',$tong);
    }
    /**
     * 检测游戏任务是否完成
     * @param        string        openid        微信用户的id
     */
    function gametask(){
        $key = $this->input->post('key',true);
        if ($key != 'ksueh78PI') {
            Universal::Output($this->config->item('request_fall'),'no','','');
        }
        $openid = Universal::filter($this->input->post('openid',true));
        $this->load->model('activity/game_model');
        $result = $this->game_model->beachtask($openid);
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),$this->game_model->msg,'','');
        }
        Universal::Output($this->config->item('request_succ'),$this->game_model->msg,'','');
    }
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}