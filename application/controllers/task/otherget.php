<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Otherget extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
	}

    /**
     * 用户分享界面
     */
    public function getothersay(){
        $this->load->model('common/wxcode_model','',TRUE);
        $this->load->language('task','chinese');//加载任务语言包
        if (!isset($_GET['extendnum']) || empty($_GET['extendnum'])) {
            exit();
        }
        $extendnum_instruction=$_GET['extendnum'];
        $extendnum = substr($extendnum_instruction,0,6);
        $arr = explode('_',$extendnum_instruction);
        $instruction = $arr['1'];
        $task_id = isset($arr['2'])?$arr['2']:false;
        if (!is_numeric($instruction)||($task_id!=false&&!is_numeric($task_id))) {
            exit();
        }
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserPort('/index.php/task/otherget/getothersay?extendnum='.$extendnum_instruction,'');//检查端口
        //得到说明内容
        $this->load->model('maijinadmin/instruction_model');
        $data['taskIntroduction'] = $this->instruction_model->get_instruction($instruction);
        if (empty($data['taskIntroduction'])) {
            exit();
        }
        $this->load->model('task/user_model');
        if ($instruction>=13) {
            $level = $this->user_model->get_one_level($data['taskIntroduction']['data']['about_id']);
            if (!empty($level)) {
                $data['taskshareimg'] = ($this->config->item('webhost')).$level[0]['level_img'];
            }
            $data['tasksharetitle'] = '回收通称号-'.$data['taskIntroduction']['data']['instruction_name'];
        }elseif($instruction==10){
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/taskgame/img/fulitu2.png';
            $data['tasksharedes_pe'] = $this->lang->line('share_title');
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }elseif($instruction==11){
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/taskgame/img/fulitu3.png';
            $data['tasksharedes_pe'] = $this->lang->line('share_titleT');
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }else{
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/taskgame/img/fulitu2.png';
            $data['tasksharedes_pe'] = $this->lang->line('share_title');
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }
        $share_tid = '';
        if ($task_id!=false) {//给予分享者奖励
            $this->load->model('task/otherget_model');
            $result = $this->otherget_model->shareRward($task_id,$extendnum);
            if ($result!==false) {
                $this->otherget_model->sendinfo($result);
            }
            $share_tid = '_'.$task_id;
        }
        $data['tasksharedes'] = $data['taskIntroduction']['data']['instruction_des'];
        $data['appid'] = $this->config->item('APPID');
        $data['taskshareurl']=urlencode('http://wx.recytl.com/index.php/task/otherget/getothersay').'?extendnum='.$extendnum.'_'.$instruction.$share_tid;
        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        if (isset($_SESSION['userinfo']['user_mobile'])&&$_SESSION['userinfo']['user_mobile']=='') {//此用户未注册过
            // $this->user_model->redisAddext($extendnum);
            $data['wxid'] = $_SESSION['userinfo']['user_id'];
        }else{
            $_SESSION['userinfo']['invite'] = $extendnum;
        }
        $data['instruction'] = $instruction;
        $data['next'] = 1;
        $this->load->view("task/othergethree",$data);
    }

    /**
     * 用户分享界面二
     */
    public function getothersaytwo(){
        $this->load->model('common/wxcode_model','',TRUE);
        $this->load->language('task','chinese');//加载任务语言包
        if (!isset($_GET['extendnum']) || empty($_GET['extendnum'])) {
            exit();
        }
        $extendnum_instruction=$_GET['extendnum'];
        $extendnum = substr($extendnum_instruction,0,6);
        $arr = explode('_',$extendnum_instruction);
        $instruction = $arr['1'];
        $task_id = isset($arr['2'])?$arr['2']:false;
        if (!is_numeric($instruction)||($task_id!=false&&!is_numeric($task_id))) {
            exit();
        }
        $this->load->model('auto/userauth_model','',TRUE);
        $this->userauth_model->UserPort('/index.php/task/otherget/getothersaytwo?extendnum='.$extendnum_instruction,'');//检查端口
        //得到说明内容
        $this->load->model('maijinadmin/instruction_model');
        $data['taskIntroduction'] = $this->instruction_model->get_instruction($instruction);
       
        if (empty($data['taskIntroduction'])) {
            exit();
        }
        $this->load->model('task/user_model','',TRUE);
        if ($instruction>=13) {
            $level = $this->user_model->get_one_level($data['taskIntroduction']['data']['about_id']);
            if (!empty($level)) {
                $data['taskshareimg'] = ($this->config->item('webhost')).$level[0]['level_img'];
            }
            $data['tasksharetitle'] = '回收通称号-'.$data['taskIntroduction']['data']['instruction_name'];
        }elseif($instruction==10){
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/taskgame/img/fulitu2.png';
            $data['tasksharedes_pe'] = $this->lang->line('share_title');
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }elseif($instruction==11){
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/taskgame/img/fulitu3.png';
            $data['tasksharedes_pe'] = $this->lang->line('share_titleT');
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }else{
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/taskgame/img/fulitu2.png';
            $data['tasksharedes_pe'] = $this->lang->line('share_title');
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }

        $share_tid = '';
        if ($task_id!=false) {//给予分享者奖励
            $this->load->model('task/otherget_model');
            $result = $this->otherget_model->shareRward($task_id,$extendnum);
            if ($result!==false) {
                $this->otherget_model->sendinfo($result);
            }
            $share_tid = '_'.$task_id;
        } 
        $data['tasksharedes'] = $data['taskIntroduction']['data']['instruction_des'];
        $data['appid'] = $this->config->item('APPID');
        $data['taskshareurl']=urlencode('http://wx.recytl.com/index.php/task/otherget/getothersay').'?extendnum='.$extendnum.'_'.$instruction.$share_tid;
        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        
        if (isset($_SESSION['userinfo']['user_mobile'])&&$_SESSION['userinfo']['user_mobile']=='') {//此用户未注册过
            // $this->user_model->redisAddext($extendnum);
            $data['wxid'] = $_SESSION['userinfo']['user_id'];
        }else{
            $_SESSION['userinfo']['invite'] = $extendnum;
        }
        $data['instruction'] = $instruction;
        $this->load->view("task/othergethree",$data);
    }

    public function getothersayfour(){
        exit();
        $this->load->model('common/wxcode_model','',TRUE);

        if(empty($_GET['code'])){
            $this->load->view('exception/notopenwx');
            return '';
        }else{
            $code=$_GET['code'];
                if (!isset($_GET['extendnum']) || empty($_GET['extendnum'])) {
                    exit();
                }
            $extendnum_instruction=$_GET['extendnum'];
            $extendnum = substr($extendnum_instruction,0,6);
            $arr = explode('_',$extendnum_instruction);
            $instruction = end($arr);
        }

        $openid = $this->wxcode_model->getOpenid($code);
        // $extendnum = '123123';
        // $openid = 'o9nlJt24iX3ZGewseXUO_C-2ydnw';
        // $instruction = 10;
        $this->load->model('task/user_model');
        if (!isset($openid)||empty($openid)) {
            exit();
        }

        $userid=$this->user_model->check_user(array('openid'=>$openid));
        if($userid==0){
            //获取用户详细信息
            $userdata=array(
                    'wx_openid'=>$openid,
                    'wx_jointime'=>date('Y-m-d H:i:s'),
                    'wx_logintime'=>date('Y-m-d H:i:s'),
                    'wx_loginip'=>$this->input->ip_address(),
                    'wx_status'=>1
            );
        }else{
            $userdata=array(
                'wx_logintime'=>date('Y-m-d H:i:s'),
                'wx_loginip'=>$this->input->ip_address(),
            );
            $this->db->update('h_wxuser',$userdata,array('wx_id'=>$userid['0']['wx_id']));
            //保存用户信息
            $_SESSION['userinfo']['useronline']  ='ok';
            $_SESSION['userinfo']['userlogin']   ='ok';
            $_SESSION['userinfo']['user_mobile'] =$userid['0']['wx_mobile'];
            $_SESSION['userinfo']['user_openid'] =$userid['0']['wx_openid'];
            $_SESSION['userinfo']['user_id']     = $wx_id = $userid['0']['wx_id'];
            $_SESSION['userinfo']['user_name']   =$userid['0']['wx_name'];
            $_SESSION['userinfo']['user_img']    =$userid['0']['wx_img'];
        }

        
        //得到说明内容
        $this->load->model('maijinadmin/instruction_model');
        $share_rand = $this->config->item('share_rand');

        $this->load->helper('safe_helper'); 
        $instruction = verify_id($instruction);

        $data['taskIntroduction'] = $this->instruction_model->get_instruction($instruction);
        if (empty($data['taskIntroduction'])) {
            exit();
        }
        if ($instruction>=13) {
            $level = $this->user_model->get_one_level($data['taskIntroduction']['data']['about_id']);
            if (!empty($level)) {
                $data['taskshareimg'] = ($this->config->item('webhost')).$level[0]['level_img'];
            }
            $data['tasksharetitle'] = '我是'.$data['taskIntroduction']['data']['instruction_name'].'保护动物，你也来试试吧~';
        }else{
            $data['taskshareimg'] = 'http://wx.recytl.com/static/task/images/oned.jpg';
            
            $data['tasksharedes_pe'] = '【涨姿势】参加公益环保活动得1元红包，不信来试~';
            $data['tasksharetitle'] = $data['taskIntroduction']['data']['instruction_name'];
        }
        $data['tasksharedes'] = $data['taskIntroduction']['data']['instruction_des'];

        $data['appid'] = $this->config->item('APPID');

        $data['taskshareurl']=urlencode('http://wx.recytl.com/index.php/task/otherget/getothersayfour').'?extendnum='.$extendnum.'_'.$instruction;
        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        $_SESSION['userinfo']['extendnum'] = $extendnum;//把邀请码存在session中

        $data['wxid'] = $wx_id;
        $data['instruction'] = $instruction;

        $this->load->view("task/othergetfour",$data);
    
    }
    /**
     * 广告完成
     * @param        int        task_id        任务id
     * @param        int        wx_id          用户id
     * @return       json
     */
    function advs(){
        $this->load->model('auto/userauth_model');
        $result = $this->userauth_model->UserCheck(2,$_SESSION);//校验是否已经登录
        $method=$this->input->is_ajax_request();
        if(empty($_SESSION['userinfo']['user_mobile'])||$result===false
           ||!is_numeric($task_id = $this->input->post('id',true))
           ||!is_numeric($wx_id=$_SESSION['userinfo']['user_id'])){//校验是否继续
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('task/tasks_model');
        $return     = $this->tasks_model->getonetask($task_id);//此任务的信息
        if (empty($return['0']['task_type'])||$return['0']['task_type']!=9) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $result=$this->tasks_model->getonetasklog($wx_id,$task_id);//判断用户此任务进行过程
        if (empty($result['0']['log_id'])) {//没有找到任务
            $time = time();
            $input = array(//得到要输入的基本信息
              'wx_id' => $wx_id,
              'task_id' => $task_id,
              'task_jointime' => $time,
              'task_finishtime' => $time,
              'task_process' => 3,
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($input);//插入信息
            if ($return==false) {
                Universal::Output($this->config->item('request_fall'),'','','');
            }
            Universal::Output($this->config->item('request_succ'),'',$return['0']['task_url'],'');
        }
        if ($result['0']['task_process']==3) {//有未领取的奖励则直接跳过
            Universal::Output($this->config->item('request_succ'),'',$return['0']['task_url'],'');
        }elseif($result['0']['task_process']==2){
            $this->load->model('task/taskfinish_model');
            $str=$this->taskfinish_model->uptaskprocess($wx_id,$task_id,3);
            if ($str==false) {
                Universal::Output($this->config->item('request_fall'),'','','');
            }
        }
        Universal::Output($this->config->item('request_succ'),'',$return['0']['task_url'],'');
    }
    /**
     * 获取投票信息
     * @param        int        id            任务id
     * @return       json       返回json字符串
     */
    function getvoteinfo(){
        $this->load->model('auto/userauth_model');
        $result = $this->userauth_model->UserCheck(2,$_SESSION);//校验是否已经登录
        if(empty($_SESSION['userinfo']['user_mobile'])||$result===false
           ||!is_numeric($task_id = $this->input->post('id',true))
           ||!is_numeric($wx_id=$_SESSION['userinfo']['user_id'])){//校验是否继续
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('task/otherget_model');
        $result = $this->otherget_model->thistask($task_id);
        if ($result['0']['type']!=10) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $content = json_decode($result['0']['task_difcontent'],true);
        Universal::Output($this->config->item('request_succ'),'','',$content['vote']);
    }
    /**
     * 投票任务
     * @param        int        wx_id         用户的id
     * @param        int        vid           投票的id
     * @param        int        id            任务id
     * @return       json       返回json字符串
     */
    function uservote(){
        $this->load->model('auto/userauth_model');
        $result = $this->userauth_model->UserCheck(2,$_SESSION);//校验是否已经登录
        if(empty($_SESSION['userinfo']['user_mobile'])||$result===false
           ||!is_numeric($task_id = $this->input->post('id',true))
           ||!is_numeric($vid = $this->input->post('vid',true))
           ||!is_numeric($wx_id=$_SESSION['userinfo']['user_id'])){//校验是否继续
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $arr_sex = array(1,2);//判断信息是否在规定的范围
        $arr_lin = array(1,2,3,4,5,6);
        $arr_zhiye = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);
        if (!in_array(($job=$this->input->post('zhiye',true)), $arr_zhiye)
            ||!in_array(($age=$this->input->post('lin',true)), $arr_lin)
            ||!in_array(($sex=$this->input->post('sexist',true)), $arr_sex)) {
            Universal::Output($this->config->item('request_fall'),'请填写正确的信息','','');
        }
        $this->load->model('task/otherget_model');
        $result = $this->otherget_model->taskinfo($task_id,$wx_id);
        if ($result===false || $result['0']['type']!=10) {
            Universal::Output($this->config->item('request_fall'),'投票活动结束','','');
        }
        $voteinfo = json_decode($result['0']['task_difcontent'],true)['vote'];
        $is_have = false;
        foreach ($voteinfo as $k => $v) {
            if ($k == $vid) {
                $is_have = true;
                break;
            }
        }
        $is_have==false?Universal::Output($this->config->item('request_fall'),'请选择正确的选项','',''):'';
        $text = Universal::filter($this->input->post('text',true));
        $hcity = Universal::filter($this->input->post('hcity',true));
        $hproper = Universal::filter($this->input->post('hproper',true));
        $harea = Universal::filter($this->input->post('harea',true));
        if (strlen($text)>45||strlen($hcity)>60||strlen($hproper)>60||strlen($harea)>60) {
            Universal::Output($this->config->item('request_fall'),'建议的文字不能超过15字','','');
        }
        $uservote = array(
            'vid' => $vid,
            'age' => $age,
            'sex' => $sex,
            'job' => $job,
            'where' => $hproper.'-'.$harea.'-'.$hcity,
            'other' => $text,
        );
        $integral=(empty($uservote['other']))?30:50;
        $result = $this->otherget_model->votereward($task_id,$wx_id,$result['0']['task_process'],$uservote);
        if ($result==false) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        Universal::Output($this->config->item('request_succ'),'','/index.php/task/usercenter/taskcenter',$integral);
    }
}