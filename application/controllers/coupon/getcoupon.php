<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Getcoupon extends CI_Controller {
    private $mobile = '';           //用户的手机号码
    private $NoDefined = '';        //插入的增值券数据
    private $Nonum = 0;             //插入的数据数量
    private $from = '';             //页面来自那个媒体
    /**
     * 通过openid检查用户的电话号码
     * @param   code     string      微信传过来的code
     * 
     */
    function getphone(){
        $code = $this->input->post('code',true);
        if(isset($code)&&!empty($code)&&$code!='null'){
            $this->load->model('common/wxcode_model','',TRUE);
            $openid = $this->wxcode_model->getOpenid($code);//获取用户的openid
        }
        //如果有电话号码去领取增值券
        $this->load->model('coupon/coupon_model');
        if (isset($openid)&&!empty($openid)) {
            $mobile = $this->coupon_model->getUserMobile($openid);
        }
        if (isset($mobile)&&$mobile!==false) {
            $this->mobile = $mobile['0']['mobile'];
            // 检查此用户是否领取过增值券
            $isobtain = $this->isobtain();
            if (!$isobtain) {//如果未领取 
                $this->obtaincoupon();
            }
            Universal::Output($this->config->item('request_succ'),'haveobtain','',$mobile);//已领取
        }
        //没有领取，如果在微信中，获取微信分享的信息
        $shareInfo = array();
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        if (strpos($user_agent, 'MicroMessenger')) {
            $this->load->model('common/wxcode_model','',TRUE);
            if(isset($code)&&!empty($code)&&$code!='null'){
                $url = 'http://wx.recytl.com/view/coupon/receive.html?code='.$code.'&state=';
            }else{
                $url = 'http://wx.recytl.com/view/coupon/receive.html';
            }
            $signPackage = $this->wxcode_model->getSignPackageAjax($url);
            $shareInfo['shareurl'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22'
                    .'&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Fcoupon%2Freceive.html?'
                    .'response_type=code&scope=snsapi_base&state=#wechat_redirect';
            $shareInfo['signPackage']=$signPackage;//分享的信息
        }
        //如果没有电话号码显示让用户填写号码的界面
        Universal::Output($this->config->item('request_fall'),'noobtain','',$shareInfo);//未领取
    }
    /**
     * 检查是否存在此电话号码
     */
    function checkphone(){
        $mobile = $this->input->post('mobile',true);
        $mobilecode = $this->input->post('mobilecode',true);//获取手机验证码
        $from = $this->input->post('from',true);
        if ($from != false&&ctype_alnum($from)) {
            $this->from = $from;
        }

        $this->mobile = $mobile;
        if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$mobile)){
            Universal::Output($this->config->item('request_fall'),'手机格式不对','','');
        }
        //检测是否领取过增值券
        $isobtain = $this->isobtain();
        if ($isobtain) {
            Universal::Output($this->config->item('request_succ'),'haveobtain','','');
        }
        //如果没有领取，检查用户表里是否有此号码
        $return = $this->coupon_model->checkUserMobile($mobile);
        //如果没有此账户，则通知前段需要发送验证码。如果有电话号码，直接领取
        if ($return == false) {
            //检查输入的手机验证码是否正确
            if(!is_numeric($mobilecode)){
                Universal::Output($this->config->item('request_fall'),'请正确填写验证码!','','code_error');
            }
            $this->load->model('nonstandard/msg_model');
            $this->msg_model->mobile=$mobile;
            $this->msg_model->code=$mobilecode;
            $this->msg_model->type=4;
            $this->msg_model->invalid=$this->config->item('checkcode_Invalid_time');
            //校验当前验证码是否已经失效或被使用
            $result=$this->msg_model->check_code();
            if(!$result){
                Universal::Output($this->config->item('request_fall'),'验证码不正确或已经失效!','','code_error');
            }
        }
        $this->obtaincoupon();
    }
    /**
     * 发送验证码
     */
    function getcode(){
        $mobile = $this->input->post('mobile',true);
        $imgcode = $this->input->post('imgcode',true);
        if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$mobile)){
            Universal::Output($this->config->item('request_fall'),'手机格式不对','','');
        }
        if (isset($_SESSION['imgcode'])&&$_SESSION['imgcode']==1&&
            (!isset($_SESSION['hst_code'])||$imgcode!=$_SESSION['hst_code']||$_SESSION['hst_code']===false)) {
            //需要输入图形验证码
            Universal::Output($this->config->item('request_fall'),'imgerror');
        }
        $_SESSION['hst_code'] = false;
        $this->load->model('nonstandard/msg_model');
        $this->msg_model->mobile=$mobile;
        $this->msg_model->code_limit=3;
        $this->msg_model->code_type =4;
        $accept_res=$this->msg_model->CheckNum();
        if($accept_res === false){
            Universal::Output($this->config->item('request_fall'),'1分钟内或本日发送次数超过限制!');
        }
        $this->msg_model->templte = $this->config->item('alidayu_templte_reg');
        $send_res=$this->msg_model->SendSmsCode();
        $_SESSION['imgcode']=1;
        if($send_res){
            Universal::Output($this->config->item('request_succ'),'');
        }else{
            Universal::Output($this->config->item('request_fall'),'验证码发送失败!');
        }
    }
    /**
     * 判断用户是否领取过增值券
     * @param       int        mobile       用户手机号码
     * @return      true说明领取过了|false说明没有领取过
     */
    private function isobtain(){
        $mobile = $this->mobile;
        //判断是否是电话号码
        if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$mobile)){
            Universal::Output($this->config->item('request_fall'),'手机格式不对','','');
        }

        $this->load->model('coupon/coupon_model');
        //检查此电话号码是否已经领取过增值券
        $AllCoupon = $this->coupon_model->obtainUserCou($mobile);
        if ($AllCoupon===false) {
            //说明没有此类型的增值券，活动可能结束
            Universal::Output($this->config->item('request_fall'),'活动结束','','');
        }
        $time = time();
        $NoDefined = '';    //记录要插入的增值券
        $Nonum = 0;         //记录要插入的个数
        foreach ($AllCoupon as $k => $v) {
            if (empty($v['mobile'])) {
                $NoDefined .= '('.$v['inid'].','.$mobile.','.$time.',0,"'.$this->from.'"),';
                $Nonum++;
            }
        }
        $this->NoDefined = $NoDefined;
        $this->Nonum = $Nonum;
        if (empty($NoDefined)) {  //如果是空的，说明都领取过了
            return true;
        }
        return false;
    }
    /**
     * 用户领取增值券
     * @param   int     mobile  电话号码
     */
    private function obtaincoupon(){
        if (!empty($this->NoDefined)) {
            //如果有点券未领取，则添加点券
            $this->NoDefined = rtrim($this->NoDefined,',');
            $result = $this->coupon_model->addCoupon($this->NoDefined,$this->Nonum);
            if ($result===false) {
                Universal::Output($this->config->item('request_fall'),'送增值券失败','','');
            }
        }
        Universal::Output($this->config->item('request_succ'),'haveobtain','','');
    }
    /**
     * 用手机号码获取兑换券信息
     */
    function usercoupon(){
        $mobile = $this->input->post('mobile',true);
        //判断是否是电话号码
        if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$mobile)){
            Universal::Output($this->config->item('request_fall'),'手机格式不对','','');
        }
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        if (strpos($user_agent, 'MicroMessenger')) {
            $this->load->model('common/wxcode_model','',TRUE);
            $url = 'http://wx.recytl.com//view/coupon/obtain.html?mobile='.$mobile;
            $signPackage = $this->wxcode_model->getSignPackageAjax($url);
            $return['shareInfo']['shareurl'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22'
                    .'&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Fcoupon%2Freceive.html?'
                    .'response_type=code&scope=snsapi_base&state=#wechat_redirect';
            $return['shareInfo']['signPackage']=$signPackage;//分享的信息
        }
        $this->load->model('coupon/coupon_model');
        $result = $this->coupon_model->obtainUserCouInfo($mobile);
        if ($result==false) {
            Universal::Output($this->config->item('request_fall'),'没有增值券','','');
        }
        $return['coupon'] = array();
        foreach ($result as $k => $v) {
            if ($v['mobile']!=null) {
                $return['coupon'][] = $v;
            }
        }
        if (empty($return)) {
            Universal::Output($this->config->item('request_fall'),'您还没有增值券','','');
        }
        Universal::Output($this->config->item('request_succ'),'haveobtain','',$return);
    }
}