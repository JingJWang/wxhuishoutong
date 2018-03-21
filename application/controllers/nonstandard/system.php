<?php
 /**
  * 项目入口
  * @author ma
  * 包含项目入口方法 用户发送验证码  绑定手机号码 
  */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class System extends CI_Controller { 
    /**
     * 非标准模块入口
     * @param     string   code   用于在微信中获取openid
     */
    function welcome(){
        if ($_SERVER['QUERY_STRING']!='') {
            $from = explode('=',$_SERVER['QUERY_STRING']);
            if (isset($from['1'])&&$from['1']=='baidu') {
                $_SESSION['userinfo']['spreadFrom'] = $from['1'];
            }
        }  
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserPort('/index.php/nonstandard/system/welcome','www.recytl.com');  
        //读取交易额成交记录
        $this->load->model('center/managedata_model');
        $deal=$this->managedata_model->indexVolume();        
        $view['deal']=$deal;
        //读取成交记录
        $dynamic=$this->managedata_model->indexRecord();
        $view['dynamic']=$dynamic;
        $this->load->view('nonstandard/index',$view);
    }
    /**
     * ajax请求登录
     * @param     string   code   用于在微信中获取openid
     */
    function ajaxlogin(){
        // exit();
        if(isset($_SESSION['userinfo']['userlogin']) 
            && $_SESSION['userinfo']['userlogin'] == 'ok'){
            Universal::Output($this->config->item('request_succ'));
        }
        //获取访问来源
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        //判断访问来源  是否是微信内置浏览器
        $_GET['openid'] = $this->input->post('id',true);
        if(strpos($user_agent, 'MicroMessenger') === false) {            
            $code=$this->input->post('code',true);
            $this->load->model('common/wxcode_model');
            $this->load->model('auto/userauth_model');
            $bool_login=$this->userauth_model->wxLogin($code);
        }else{
            //微信内置浏览器
            $code=$this->input->post('code',true);
            $this->load->model('common/wxcode_model');
            $this->load->model('auto/userauth_model');
            $bool_login=$this->userauth_model->wxLogin($code);
        }
        if(!isset($_SESSION['userinfo']['userlogin']) 
            && $_SESSION['userinfo']['userlogin'] != 'ok'){
            Universal::Output($this->config->item('request_fall'),'登录失败');
        }
        Universal::Output($this->config->item('request_succ'));
    }
    /**
     * 用分享出去的链接获取用户openid
     * @param     string   code   用于在微信中获取openid
     */
    function GopenidLogin(){
        if(isset($_SESSION['userinfo']['userlogin']) 
            && $_SESSION['userinfo']['userlogin'] == 'ok'){
            Universal::Output($this->config->item('request_succ'));
        }
        $code=$this->input->post('code',true);
        $this->load->model('common/wxcode_model');
        $this->load->model('auto/userauth_model');
        $bool_login=$this->userauth_model->wxLogin($code);
        if ($bool_login===false) {
            Universal::Output($this->config->item('request_fall'),'登录失败');
        }
        Universal::Output($this->config->item('request_succ'));
    }
    /**
     * 校验用户是否已经登录
     * 1 没有登录 在微信环境获取code
     * 2 测试环境中获取 openid
     * @param  string   openid  测试入口 参数
     * @param  string   code    微信传递code
     * @return json 登录失败返回 登录页面
     */
    function  isOnline(){
        $code=$this->input->post('code',true);
        if(empty($code)){
            
        }
        $openid=$this->input->post('openid',true);
        if(empty($openid)){
            
        }else{
            $openid=Universal::safe_replace($openid);
        }   
    }    
    /**
     * 获取首页的交易额,成交数据
     * @return json 返回首页交易额,交易数据 
     */
    function dynamic(){
        $dynamic=$this->config->item('dynamic');
        $dynamic === false ? $view['dynamic'] = '' : $view['dynamic'] = $dynamic;
        if(date('H:i:s') > '11:00:00'){
            $datainfo=$this->config->item('index_data');
            $view['deal']=$datainfo[date('md')];
        }else{
            $datainfo=$this->config->item('index_data');
            $view['deal']=$datainfo[date('md',strtotime("-1 day"))];
        }
        Universal::Output($this->config->item('request_succ'),'','',$view);
    }
    /**
     * 用户模块-绑定手机号码-获取验证码
     * @param    int     mobile   手机号码
     * @return   结果以json字符串返回
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
        $this->msg_model->code_type =1;
        $accept_res=$this->msg_model->CheckNum();
        if($accept_res === false){
            Universal::Output($this->config->item('request_fall'),'1分钟内或本日发送次数超过限制!');
        }
        $send_res=$this->msg_model->SendVoiceCode();
        if($send_res){
            Universal::Output($this->config->item('request_succ'),
            '验证码已经发送'.$this->config->item('alidayu_shownum'));           
        }else{
            Universal::Output($this->config->item('request_fall'),'验证码发送失败!');
        }
    }
    /**
     * 用户模块-绑定手机号码-获取验证码
     * @param    int     mobile   手机号码
     * @return   结果以json字符串返回
     */
    function mobilecode(){    
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
        /*$this->msg_model->ip=$this->input->ip_address();
        $register=$this->msg_model->checkRegister();
        if($register === false){
            Universal::Output($this->config->item('request_fall'),'您当前的注册行为存在风险!');
        }*/
        //校验当前手机号码是否已经被占用
        $this->msg_model->mobile=$mobile;
        $check_res=$this->msg_model->CheckMobile();
        if($check_res === false){
            Universal::Output($this->config->item('request_fall'),'您已经注册过请登录!','/index.php/nonstandard/system/Login');
        }        
        //校验当前手机号码是否超过当前接受次数
        $this->msg_model->code_limit=3;
        $this->msg_model->code_type =1;
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
     * @return json     结果以json字符串方式返回
     */
    function binding_mobile(){
        $mobile=$this->input->post('mobile',true);
        $code=$this->input->post('code',true);
        $passwd=$this->input->post('password',true);
        $promoCode=$this->input->post('promoCode',true);
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
            $invitation='';
        }  
        $this->load->model('nonstandard/msg_model');
        $this->msg_model->mobile=$mobile;
        $this->msg_model->code=$code;
        $this->msg_model->type=1;
        $this->msg_model->invalid=$this->config->item('checkcode_Invalid_time');
        //校验当前验证码是否已经失效或被使用
        $result=$this->msg_model->check_code();
        if(!$result){
            Universal::Output($this->config->item('request_fall'),'验证码不正确或已经失效!');
        }
        // $userid=$_SESSION['userinfo']['user_id'];
        $this->load->model('nonstandard/wxuser_model');
        //校验当前手机号码是否已经被占用
        $check_res=$this->wxuser_model->CheckBinding($this->input->post('mobile',true));
        if($check_res ===  false){
            Universal::Output($this->config->item('request_fall'),'该手机号码已经被注册');
        }        
   		if($promoCode!=null || isset($promoCode)){
        	$this->wxuser_model->promoCode=$promoCode;
        }else{
        	$this->wxuser_model->promoCode='';
        }
        $this->wxuser_model->mobile=$mobile;
        // $this->wxuser_model->userid=$userid;
        $this->wxuser_model->passwd=Universal::safe_replace($passwd);
        $this->wxuser_model->invitation=$invitation;
        $this->db->trans_begin();//事务开启
        $result=$this->wxuser_model->wxuser_binding_mobile();
        if ($result===false) {
            $this->db->trans_rollback();
            Universal::Output($this->config->item('request_fall'),'绑定手机号码出现异常!');
        }
        $str = $this->user_model->is_have_user($result);//判断用户是否登入过任务中心，不是侧插入。
        if ($this->db->trans_status()==false||$result==false||$str==false) {
            $this->db->trans_rollback();
            $_SESSION['userinfo']['user_mobile']='';
            Universal::Output($this->config->item('request_fall'),'绑定手机号码出现异常!');
        }
        $this->db->trans_commit();
        $this->load->model('common/wxcode_model');
        // $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],109);//设置微信分组 注册组
        Universal::Output($this->config->item('request_succ'),'成功绑定手机号码!',
                        '/index.php/nonstandard/system/Login');
    }
    /**
     * 用户登录 显示界面
     */
    function Login(){
        //判断用户端
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserPort('/index.php/nonstandard/system/Login','http://www.recytl.com/index.php/user/login');
        if (isset($_SESSION['userinfo']['userlogin'])&&$_SESSION['userinfo']['userlogin']=='ok') {
            if (isset($_SESSION['LoginBackUrl'])) {//检查是否有返回跳转的链接
                $BackUrl = $_SESSION['LoginBackUrl'];
                unset($_SESSION['LoginBackUrl']);
            }else{
                $BackUrl = '/index.php/nonstandard/system/welcome';
            }
            header('Location:http://'.$_SERVER['HTTP_HOST'].$BackUrl);
        }
        if (isset($_SESSION['check_code'])&&$_SESSION['check_code']==1) {
            $data['code']=1;
        }else{
            $data['code']=0;
        }
        $this->load->view('nonstandard/login',$data);
    }
    /**
     * 用户登录
     * @param   check_code   string         验证码
     * @param   name         int            账户
     * @param   pwd         string          密码
     * @return  json字符串  
     */
    function userlogin(){
        if (isset($_SESSION['userinfo']['userlogin'])&&$_SESSION['userinfo']['userlogin']=='ok') {
            Universal::Output($this->config->item('request_succ'),'','/index.php/nonstandard/system/welcome');
        }
        if (isset($_SESSION['check_code'])&&$_SESSION['check_code']==1) {
            $code = $this->input->post('code',true);
            if($_SESSION['hst_code'] != $code || $_SESSION['hst_code']==false){
                Universal::Output($this->config->item('request_fall'),'验证码有误!');
            }
        }
        $name=$this->input->post('name');
        if(!is_numeric($name) || isset($name{11})){
            Universal::Output($this->config->item('request_fall'),'电话号码有误！');
        }
        $pwd=$this->input->post('pwd');
        if(empty($pwd)){
            Universal::Output($this->config->item('request_fall'),'请输入密码！');
        }
        $this->load->model('nonstandard/wxuser_model');
        $this->wxuser_model->name=$name;
        $this->wxuser_model->pwd=Universal::safe_replace($pwd);
        $auth=$this->wxuser_model->userAuth();
        if($auth){
            if (isset($_SESSION['LoginBackUrl'])) {//检查是否有返回跳转的链接
                $BackUrl = $_SESSION['LoginBackUrl'];
                unset($_SESSION['LoginBackUrl']);
            }else{
                $BackUrl = '/index.php/nonstandard/system/welcome';
            }
            $data['isbrand'] = $_SESSION['userinfo']['user_openid']==''?0:1;
            Universal::Output($this->config->item('request_succ'),'',$BackUrl,$data);
        }else{
            //本次会话开启验证码
            $_SESSION['check_code']=1;
            $_SESSION['hst_code'] = false;
            Universal::Output($this->config->item('request_fall'),'密码或账户错误！','',array('code'=>1));
        }
    }
    /**
     * 寄售通用户登录获取信息
     * @param   secret      string          请求的密码
     * @param   tel         int             账户
     * @param   pwd         string          密码
     * @return  json字符串
     */
    function jstLogin(){
        if (($this->input->get('secret')===false)||($this->input->get('tel')===false)||($this->input->get('pwd')===false)) {
            Universal::Output($this->config->item('request_fall'),'1');
        }
        $secret = $this->input->get('secret');
        if ($secret!='jstlink201615487sfd') {
            Universal::Output('2','');
        }
        $tel = $this->input->get('tel');
        if(!is_numeric($tel) || isset($tel{11})){
            Universal::Output('1','');
        }
        $pwd = $this->input->get('pwd');
        if(empty($pwd)){
            Universal::Output('1','');
        }
        $this->load->model('nonstandard/wxuser_model');
        $this->wxuser_model->name=$tel;
        $this->wxuser_model->pwd=Universal::safe_replace($pwd);
        $result = $this->wxuser_model->getUserInfo();
        if($result===false){
            Universal::Output('4','');
        }elseif($result=='pwerror'){
            Universal::Output('3','');
        }else{
            unset($result['wx_password']);
            Universal::Output('0','','',$result);
        }
    }
    /**
     * 寄售通用户修改密码
     * @param   secret      string          请求的密码
     * @param   tel         int             账户
     * @param   pwd         string          密码
     * @return  json字符串
     */
    function jstchangepw(){
    	if (($this->input->get('secret')===false)||($this->input->get('tel')===false)||($this->input->get('pwd')===false)) {
            Universal::Output('1');
        }
        $secret = $this->input->get('secret');
        if ($secret!='jstlink201615487sfdCpwd') {
            Universal::Output('2','');
        }
        $tel = $this->input->get('tel');
        if(!is_numeric($tel) || isset($tel{11})){
            Universal::Output('1','');
        }
        $pwd = $this->input->get('pwd');
        if(empty($pwd)){
            Universal::Output('1','');
        }
        $this->load->model('nonstandard/wxuser_model');
        $result = $this->wxuser_model->CheckBinding($tel);
        if ($result) {//没有此用户
            Universal::Output('4','');
        }
        $this->wxuser_model->mobile=$tel;
        $this->wxuser_model->passwd=Universal::safe_replace($pwd);
        $result=$this->wxuser_model->changepwd();
        if ($result==true) {
            Universal::Output('0','','','');
        }else{
            Universal::Output('3','');
        }
    }
    /**
     * 注册界面
     */
    public function usereg(){ 
        if (isset($_SESSION['userinfo']['userlogin'])&&$_SESSION['userinfo']['userlogin']=='ok') {
            if (isset($_SESSION['LoginBackUrl'])) {//检查是否有返回跳转的链接
                $BackUrl = $_SESSION['LoginBackUrl'];
                unset($_SESSION['LoginBackUrl']);
            }else{
                $BackUrl = '/index.php/nonstandard/system/welcome';
            }
            header('Location:http://'.$_SERVER['HTTP_HOST'].$BackUrl);
            exit();
        }
        $array['promoCode'] = $this->input->get('rand');
        $this->load->view('nonstandard/mobile',$array);
        return '';
    }
    /**
     * 获取微信的signPackage配置
     */
    function  signPackage(){
        //获取js sdk 配置
        $this->load->model('common/wxcode_model');
        $this->load->database();
        $url=$this->input->post('url',true);
        $data=$this->wxcode_model->getSignPackageAjax($url);
        Universal::Output($this->config->item('request_succ'),'','',$data);
    }
    /**
     * 修改密码-获取验证码
     * @param    int     mobile   手机号码
     * @return   结果以json字符串返回
     */
    function send_changemobile(){    
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
        //校验当前手机号码是否已经被占用
        $this->msg_model->mobile=$mobile;
        $check_res=$this->msg_model->CheckMobile();
        if($check_res !== false){
            Universal::Output($this->config->item('request_fall'),'没有此账户');
        }        
        //校验当前手机号码是否超过当前接受次数
        $this->msg_model->code_limit=3;
        $this->msg_model->code_type =11;
        $accept_res=$this->msg_model->CheckNum();
        if($accept_res === false){
            Universal::Output($this->config->item('request_fall'),'1分钟内或本日发送次数超过限制!');
        }
        $this->msg_model->templte = $this->config->item('alidayu_templte_modifypas');
        $send_res=$this->msg_model->SendSmsCode();
        if($send_res){
            Universal::Output($this->config->item('request_succ'),
            '验证码已经发送');           
        }else{
            Universal::Output($this->config->item('request_fall'),'验证码发送失败!');
        }
    }
    /**
     * 用户登录
     */
    function changepwdin(){
        $this->load->view('nonstandard/changepwd');
    }
    /**
     * 修改密码
     * @param       mobile      手机号码
     * @param       pwd1        新密码
     * @param       pwd2        再次输入新密码
     * @param       code        验证码
     * @return   结果以json字符串返回
     */
    function changepwd(){
        $mobile = $this->input->post('mobile',true);
        $passwd1 = $this->input->post('pwd1',true);
        $passwd2 = $this->input->post('pwd2',true);
        $code = $this->input->post('code',true);
        if ($passwd1!==$passwd2) {
            Universal::Output($this->config->item('request_fall'),'输的两次密码不相同');
        }
        if(empty($mobile)){
            Universal::Output($this->config->item('request_fall'),'没有获取到手机号码!');
        }
        if(isset($mobile{11}) || !is_numeric($mobile)){
            Universal::Output($this->config->item('request_fall'),'请正确填写手机号码!');
        }
        $this->load->model('nonstandard/wxuser_model');
        $check_res=$this->wxuser_model->CheckBinding($this->input->post('mobile',true));//检查是否有此账户
        if($check_res !==  false){
            Universal::Output($this->config->item('request_fall'),'没有此账户');
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
        $this->wxuser_model->mobile=$mobile;
        $this->wxuser_model->passwd=Universal::safe_replace($passwd1);
        $result=$this->wxuser_model->changepwd();
        if ($result==false) {
            $this->wxuser_model->noticJstChange('http://platform.91jst.com/Consign/Server/User/public/usr/editpwdbytel.php?tel='.$mobile
            .'&pwd='.$passwd1.'&secret=abcdefgABCDEFG0987654321');
            $this->wxuser_model->noticJstChange('http://www.91jst.com/new/editshoppwdbytel.php?tel='.$mobile
            .'&pwd='.$passwd1.'&secret=abcdefg');
            Universal::Output($this->config->item('request_fall'),'修改失败');
        }
        Universal::Output($this->config->item('request_succ'),'修改成功');
    }
    function testcodepay(){
        $this->load->library('wxsdk/wxpay');
        $info=array(
                'body'=>'回收通商城中心-爱奇艺1个月',
                'orderid'=>$this->create_ordrenumber(),
                'moeny'=>9000,
                'pro_id'=>3212,
                'type'=>'NATIVE',
                'notifyurl'=>'http://wx.recytl.com/callback/pay.php'
        );
        $this->wxpay->code($info);  
        
        
    }
    function zhifubao(){
        $this->load->library('zhifubao/zhifubao.php');
        $this->zhifubao->out_trade_no=$this->create_ordrenumber();
        $this->zhifubao->subject='测试订单';        
        $this->zhifubao->total_amount='0.01';        
        $this->zhifubao->body='购买测试商品0.01元';
        $this->zhifubao->timeout_express='1m';
        $this->zhifubao->config=$this->config->item('zhifubao_attr');
        $this->zhifubao->pay();
    }
    function query(){
        $out_trade_no='20161213501019851098';//$this->input->get('out_trade_no',true);
        $trade_no='2016121321001004420200214511';//$this->input->get('trade_no',true);
        $this->load->library('zhifubao/zhifubao.php');
        $this->zhifubao->out_trade_no=$out_trade_no;
        $this->zhifubao->trade_no=$trade_no;
        $this->zhifubao->config=$this->config->item('zhifubao_attr');
        $res=$this->zhifubao->queryPay();        
    }
    function create_ordrenumber(){
         $number=date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
        return $number;
    }
}