<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Taskshare extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }
    /**
     * 游戏界面
     */
    public function game_play(){
        $this->load->model('common/wxcode_model','',TRUE);
        $this->load->model('task/user_model');
        $this->load->model('task/game_model');
        $this->load->model('nonstandard/wxuser_model');
        if(empty($_GET['code'])){
            $this->load->view('exception/notopenwx');
            return '';
        }else{
           $code=$_GET['code'];
        }
        $message = $this->wxcode_model->getOpenid_token($code);//获取openid和token
        // $message['openid'] = 'o9nlJt24iX3ZGewseXUO_C-2ydnw';
        if (!isset($message['access_token'])||empty($message['access_token'])||empty($message['openid'])) {
            exit();
        }
        $info = $this->wxcode_model->get_snsapi_userinfo($message['access_token'],$message['openid']);//获取微信用户的最新消息
        $_SESSION['userid']['Login_openid'] = $info['openid'];
        $userinfo=array();
        $getuserinfo=$this->user_model->check_user(array('openid'=>$message['openid']));
        if ($getuserinfo==0) {
            $userinfo=array(
                    'wx_id' => 0,
                    'wx_name'=>$info['nickname'],
                    'wx_openid'=>$info['openid'],
                    'wx_sex'=>$info['sex'],
                    'wx_img'=>$info['headimgurl'],
                    'wx_province'=>$info['province'],
                    'wx_county'=>$info['country'],
            );
            if (isset($_SESSION['userinfo']['user_id'])&&is_numeric($_SESSION['userinfo']['user_id'])) {//已经登入了
                $userinfo['wx_id'] = $_SESSION['userinfo']['user_id'];
            }
            $this->wxcode_model->setPacket($message['openid'],110);//设置微信分组 未注册组
        }else{
            if (isset($_SESSION['userinfo']['user_id'])&&is_numeric($_SESSION['userinfo']['user_id'])
                &&!empty($userinfo['wx_openid'])&&$userinfo['wx_openid']!=$_SESSION['userinfo']['user_openid']) {//已经登入了 但openid不一样
                exit();
            }
            $userinfo = $getuserinfo[0];
            $userdata=array(
                'wx_logintime'=>date('Y-m-d H:i:s'),
                'wx_loginip'=>$this->input->ip_address(),
                'wx_name'=>$info['nickname'],
                'wx_sex'=>$info['sex'],
                'wx_img'=>$info['headimgurl'],
                'wx_province'=>$info['province'],
                'wx_county'=>$info['country'],
            );
            $this->db->update('h_wxuser',$userdata,array('wx_id'=>$userinfo['wx_id']));
            $_SESSION['userinfo']['useronline']  ='ok';
            $_SESSION['userinfo']['userlogin']   ='ok';
            $_SESSION['userinfo']['user_mobile'] =$userinfo['wx_mobile'];
            $_SESSION['userinfo']['user_openid'] =$userinfo['wx_openid'];
            $_SESSION['userinfo']['user_id']     =$userinfo['wx_id'];
            $_SESSION['userinfo']['user_name']   =$userinfo['wx_name'];
            $_SESSION['userinfo']['user_img']    =$userinfo['wx_img'];
        }
        $this->load->model('task/tasks_model');
        $data['taskshareurl']=urlencode('http://wx.recytl.com/index.php/task/taskshare/game_play');
        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        $data['userinfo'] = $userinfo;
        $data['appid'] = $this->config->item('APPID');
        $data['getitle'] = $this->game_model->get_title($message['openid']);//获取用户要分享的标题
        $this->load->view('task/taskgame',$data);
    }
    /**
     * 插入分数
     */
    public function game_score(){
        $this->load->model('task/game_model');
        $post = $this->input->post('value',true);
        if (empty($post)) {
            exit();
        }
        $params = $this->toChar($post);
        $params=json_decode(urldecode($params),true);
        $str = $this->game_model->get_paly_num();
        $str[0]['all_num'] = $str[0]['all_num']+2374;
        $message = array('num' => $str[0]['all_num']);
        $params['all_num'] = $str[0]['all_num'];
        $str = $this->game_model->add_game_score($params);
        if ($str===false) {
            return false;
        }
        echo json_encode($message);
    }
    /**
     * 获取用户排名
     */
    public function get_rank(){
        $type = $this->uri->segment(4);
        $this->load->model('task/game_model');
        if (!isset($_SESSION['userinfo']["user_openid"])) {
            exit();
        }
        $openid = htmlspecialchars($_SESSION['userinfo']["user_openid"]);
        $ret["ret"] = 0;
        $ret["data"] = $this->game_model->game_ranking($openid,$type);
        echo json_encode($ret);
    }
    /**
     * 得到分数，或者去注册
     */
    public function get_score(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $getstr = $this->uri->segment(4);
        $this->userauth_model->UserIsLoginJump('/index.php/task/taskshare/get_score/'.$getstr);
        $params = $this->unescape($getstr);
        $params = json_decode(urldecode($params),true);
        if (empty($params)) {
            exit();
        }
        if (!is_numeric($params['userid'])&&!is_numeric($params['score'])) {
            exit();
        }
        //校验用户是否绑定手机号码
        $center_fund = $this->getfund($params['score']);
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $data['center_fund'] = $center_fund;
            $data['params'] = $getstr;
            $data['score'] = $params['score'];
            $this->load->view('task/mobile_register',$data);
            return '';
        }
        $this->load->model('task/user_model');
        $str = $this->user_model->is_have_user($params['userid']);//判断用户是否登入过任务中心，不是侧插入。
        if ($str === false ) {
            return false;
        }
        $params['fund'] = $center_fund;
        $this->load->model('task/game_model');
        $str = $this->game_model->get_fund_task($params);
        if ($str == 2) {
            $error['error'] = '您已经领取过环保基金，不能再领取。';
        }elseif($str == 1){
            $error['error'] = '您已成功领取基金，快去兑换红包吧！';
        }else{
            return false;
        }
        $this->load->view('task/have_get_title',$error);
        
    }
    /**
     * 根据成绩得到基金
     * @param       int     score        成绩
     * @return      int     center_fund  对应的基金
     */
    private function getfund($score){
        switch (true) {
            case ($score<=500):
                $center_fund = 10;
                break;
            case ($score<1000):
                $center_fund = 12;
                break;
            case ($score==1000):
                $center_fund = 15;
                break;
            case ($score<=1100):
                $center_fund = 16;
                break;
            case ($score<=1200):
                $center_fund = 17;
                break;
            case ($score<=1300):
                $center_fund = 18;
                break;
            case ($score<=1400):
                $center_fund = 19;
                break;
            case ($score<=1500):
                $center_fund = 20;
                break;
            case ($score<=1600):
                $center_fund = 21;
                break;
            case ($score<=1700):
                $center_fund = 22;
                break;
            case ($score<=1800):
                $center_fund = 23;
                break;
            case ($score<=1900):
                $center_fund = 24;
                break;
            case ($score<=2000):
                $center_fund = 25;
                break;
            case ($score<=2100):
                $center_fund = 26;
                break;
            case ($score<=2200):
                $center_fund = 27;
                break;
            case ($score<=2300):
                $center_fund = 28;
                break;
            case ($score<=2400):
                $center_fund = 29;
                break;
            case ($score<=2500):
                $center_fund = 30;
                break;
            case ($score<=2600):
                $center_fund = 31;
                break;
            case ($score<=2700):
                $center_fund = 32;
                break;
            case ($score<=2800):
                $center_fund = 33;
                break;
            case ($score<=2900):
                $center_fund = 34;
                break;
            case ($score<=3000):
                $center_fund = 35;
                break;
            case ($score<=3100):
                $center_fund = 36;
                break;
            case ($score<=3200):
                $center_fund = 37;
                break;
            case ($score<=3400):
                $center_fund = 38;
                break;
            case ($score<=3500):
                $center_fund = 40;
                break;
            case ($score<=3600):
                $center_fund = 42;
                break;
            case ($score<=3700):
                $center_fund = 45;
                break;
            case ($score<=3800):
                $center_fund = 46;
                break;
            case ($score<=3900):
                $center_fund = 47;
                break;
            case ($score<=4000):
                $center_fund = 48;
                break;
            case ($score>4000):
                $center_fund = 50;
                break;
            default:
                $center_fund = 10;
                break;
        }
        return $center_fund;
    }

    //解析前台提交ascii码
    private function toChar($str){
        $str = trim($str,"|||");
        $str =explode('|||',$str);
        $result = '';
        for($i=0; $i<count($str);$i++){
            $result .=chr($str["$i"]-$i);
        }
        return $result;
    }
    private function unescape($str){
        $ret = ''; 
        $len = strlen($str); 
        for ($i = 0; $i < $len; $i++){ 
        if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
        $val = hexdec(substr($str, $i+2, 4)); 
        if ($val < 0x7f) $ret .= chr($val); 
        else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
        else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
        $i += 5; 
        } 
        else if ($str[$i] == '%'){ 
        $ret .= urldecode(substr($str, $i, 3)); 
        $i += 2; 
        } 
        else $ret .= $str[$i]; 
        } 
        return $ret; 
    }

    /**
     * 用户模块-绑定手机号码-获取验证码
     * @param    int     mobile   手机号码
     * @return   Json
     */
    function send_checkmobile(){        
        //校验参数    
        $mobile=$this->input->post('mobile');
        $imgcode=$this->input->post('imgcode',true);
        if (!isset($_SESSION['hst_code'])||$imgcode!=$_SESSION['hst_code']||$_SESSION['hst_code']===false) {
            $_SESSION['hst_code'] = false;
            Universal::Output($this->config->item('request_fall'),'图形验证码错误!');
        }
        $_SESSION['hst_code'] = false;
        if(empty($mobile)){
            Universal::Output($this->config->item('request_fall'),'没有获取到手机号码!');
        } 
        if(isset($mobile{11}) || !is_numeric($mobile)){
            Universal::Output($this->config->item('request_fall'),'请正确填写手机号码!');
        }
        $this->load->model('nonstandard/msg_model');
        //校验当前地址注册用户数量
        $this->msg_model->ip=$this->input->ip_address();
        $register=$this->msg_model->checkRegister();
        if($register === false){
            Universal::Output($this->config->item('request_fall'),'您当前的注册行为存在风险!');
        }
        //校验当前手机号码是否已经被占用
        $this->msg_model->mobile=$mobile;
        $check_res=$this->msg_model->CheckMobile();
        if($check_res === false){
            Universal::Output($this->config->item('request_fall'),'该手机号码已经被占用!');
        }        
        //校验当前手机号码是否超过当前接受次数
        $this->msg_model->code_limit=3;
        $this->msg_model->code_type =11;
        $accept_res=$this->msg_model->CheckNum();
        if($accept_res === false){
            Universal::Output($this->config->item('request_fall'),'1分钟内或本日发送次数超过限制!');
        }
        $this->msg_model->templte = $this->config->item('alidayu_templte_reg');
        $send_res=$this->msg_model->SendSmsCode();
        if($send_res){
            Universal::Output($this->config->item('request_succ'),
            '验证码已经发送');           
        }else{
            Universal::Output($this->config->item('request_fall'),'验证码发送失败!');
        }
    }
    /**
     * 用户模块-绑定手机号码-校验验证码 并为用户绑定手机号码
     * @param  int      mobile      手机号码 
     * @param  int      code        验证码
     * @param  string   password    密码
     * @param  string   invitation  邀请码
     * @param  string   params      游戏传输的信息
     * @return json     结果以json字符串方式返回
     */
    function binding_mobile(){
        $params = '';
        $mobile=$this->input->post('mobile',true);
        $code=$this->input->post('code',true);
        $passwd=$this->input->post('password',true);
        $params=$this->input->post('params',true);//游戏的参数
        if(empty($mobile) || empty($code) || empty($passwd)){
            Universal::Output($this->config->item('request_fall'),'手机号/验证码/密码为必填选项!');
        }        
        if(isset($mobile{11}) || !is_numeric($mobile)){
            Universal::Output($this->config->item('request_fall'),'请正确填写手机号码!');
        }
        if(isset($code{11}) || !is_numeric($code)){
            Universal::Output($this->config->item('request_fall'),'请正确填写验证码!');
        }
        $this->load->model('task/user_model');
        $invitation='';
        if (isset($_SESSION['userinfo']['invite'])) {
            $invitation = $_SESSION['userinfo']['invite'];
        }
        if (strlen($invitation)!=6 || !ctype_alnum($invitation) || empty($invitation)) {
            $invitation=0;
        }      
        $this->load->model('nonstandard/msg_model');
        $this->msg_model->mobile=$mobile;
        $this->msg_model->code=$code;
        $this->msg_model->type=11;
        $this->msg_model->invalid=$this->config->item('checkcode_Invalid_time');
        //校验当前验证码是否已经失效或被使用
        $result=$this->msg_model->check_code();
        if(!$result){
            Universal::Output($this->config->item('request_fall'),'验证码不正确或已经失效!');
        }
        // $userid=$_SESSION['userinfo']['user_id'];
        $this->load->model('nonstandard/wxuser_model');
        //校验当前验证码是否已经被占用
        $check_res=$this->wxuser_model->CheckBinding($this->input->post('mobile',true));
        if($check_res ===  false){
            Universal::Output($this->config->item('request_fall'),'该手机号码已经被注册');
        }        
        $this->wxuser_model->mobile=$mobile;
        // $this->wxuser_model->userid=$userid;
        $this->wxuser_model->passwd=$passwd;
        $this->wxuser_model->invitation=$invitation;
        $this->db->trans_begin();//事务开启
        $result=$this->wxuser_model->wxuser_binding_mobile();
        if ($result===false) {
            $this->db->trans_rollback();
            Universal::Output($this->config->item('request_fall'),'绑定手机号码出现异常!');
        }
        $str = $this->user_model->is_have_user($result);//判断用户是否登入过任务中心，不是侧插入。
        $qu = $this->user_model->regReward($result);
        if ($result==false||$str==false||$qu==false) {
            $this->db->trans_rollback();
            Universal::Output($this->config->item('request_fall'),'绑定手机号码出现异常!');
        }
        if (!empty($params) && $params!='undefined') {
            $params = $this->unescape($params);//用户的基金
            $params = json_decode(urldecode($params),true);
            if ($params['userid']!=0 || !is_numeric($params['score'])) {
                $this->db->trans_rollback();exit();
            }
            $center_fund = $this->getfund($params['score']);
            $params['fund'] = $center_fund;
            $this->load->model('task/game_model');
            $params['userid'] = $result;
            $gstr = $this->game_model->get_fund_task($params);
            if ($gstr==false) {
                $this->db->trans_rollback();
                $_SESSION['userinfo']['user_mobile']='';
                Universal::Output($this->config->item('request_fall'),'绑定手机号码出现异常!');
            }
        }
        if ($this->db->trans_status()==false) {
            $this->db->trans_rollback();
            $_SESSION['userinfo']['user_mobile']='';
            Universal::Output($this->config->item('request_fall'),'绑定手机号码出现异常!');
        }
        $this->db->trans_commit();
        $this->load->model('common/wxcode_model');
        // $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],109);//设置微信分组 注册组
        Universal::Output($this->config->item('request_succ'),'成功绑定手机号码!',
                          $this->config->item('url_task_succ')); 
    }    
}