<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Center extends CI_Controller {
    /**
     * 个人中心----显示模块
     */
    function ViewCenter(){
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserPort('/index.php/task/usercenter/taskcenter','www.recytl.com');//判断终端
        //校验登录
        $this->userauth_model->UserIsLoginJump('/index.php/nonstandard/center/ViewCenter');
        //查询用户的余额
        $this->load->model('nonstandard/wxuser_model');
        $response=$this->wxuser_model->GetBalance($_SESSION['userinfo']['user_id']);
        if($response !== false){
            $view=$response;
        }else{
            $view=array('balance'=>'0.00','freeze_balance'=>'0.00');
        } 
        //查询用户贵金属信息
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $view['metal']=$this->metal_model->userMtealInfo();
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        $view['iswx'] = false;
        if (strpos($user_agent, 'MicroMessenger')) {//微信平台
            $view['iswx'] = true;
        }
        $this->load->view('nonstandard/center',$view);
    }    
    /**
    *  用户提现  获取验证码
    */
    function code(){   
        $this->load->library('common/code');
        //$_SESSION['extract_code']=$this->code->getcode();
        $this->code->createImg();
    }
    /**
     * 用户申请提现
     * @param   int  pic  提现金额
     * @param   int  code 验证码
     * @return  成功返回  结果 | 失败 返回原因
     */    
    function extract(){
        //校验登录权限
        //$this->load->model('auto/userauth_model');
        //$this->userauth_model->UserCheck(2,$_SESSION);        
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $lock='extract_110';
        $res=$this->zredis->getLock($lock);
        if($res){
            echo '无锁';
            exit();
        }else{
            echo '有锁';
            exit();
        }   
        $code=$this->input->post('code',true);
        $pic=$this->input->post('pic',true);
        $name=$this->input->post('name',true);
        if(empty($code)){
            Universal::Output($this->config->item('request_fall'),'请输入验证码或验证码格式不正确!');
        }        
        if(empty($pic) || !is_numeric($pic) || $pic < 1){
            Universal::Output($this->config->item('request_fall'),'请输入金额或金额格式不正确!');
        }
        /* if(empty($name)){
             Universal::Output($this->config->item('request_fall'),'请输入实名认证姓名!');
        } */
        //校验验证码是否正确
        if(!isset($_SESSION['extract_code']) || $_SESSION['extract_code'] != $code){
            Universal::Output($this->config->item('request_fall'),'验证码不正确或已经失效!');
        }
        //校验当前用户 是否满足提现要求
        $this->load->model('nonstandard/wxuser_model');
        $this->wxuser_model->userid=$_SESSION['userinfo']['user_id'];
        $check=$this->wxuser_model->checkextract();
        if(!$check){
            Universal::Output($this->config->item('request_fall'),'本次提现失败');
        }
        //完成提现操作        
        $this->wxuser_model->name=Universal::safe_replace($name);
        $this->wxuser_model->moeny=$pic*100;
        $this->wxuser_model->openid=$_SESSION['userinfo']['user_openid'];
        $this->wxuser_model->account = '';
        $res=$this->wxuser_model->extract();
        if($res){
            Universal::Output($this->config->item('request_succ'),'提现成功,请查看钱包余额.',
            '/index.php/nonstandard/center/ViewCenter');
        }else{
            Universal::Output($this->config->item('request_fall'),'提现失败!');
        }
    }
    /**
     * 支付宝提现
     * @param   int  pic  提现金额
     * @param   int  code 验证码
     * @param   string   account  账户
     * @param   string   name    真实姓名
     * @return  成功返回  结果 | 失败 返回原因
     */
    function zfbextract(){
        //测试环境关闭
        exit();
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $code=$this->input->post('code',true);
        $pic=$this->input->post('pic',true);
        $account=$this->input->post('acc',true);
        $name=$this->input->post('name',true);
        if(empty($code)){
            Universal::Output($this->config->item('request_fall'),'请输入验证码或验证码格式不正确!');
        }
        if(empty($pic) || !is_numeric($pic) || $pic < 1){
            Universal::Output($this->config->item('request_fall'),'请输入金额或金额格式不正确!');
        }
        //校验验证码是否正确
        if(!isset($_SESSION['extract_code']) || $_SESSION['extract_code'] != $code){
            Universal::Output($this->config->item('request_fall'),'验证码不正确或已经失效!');
        }
        //校验当前用户 是否满足提现要求
        $this->load->model('nonstandard/wxuser_model');
        $this->wxuser_model->name=Universal::safe_replace($name);
        $this->wxuser_model->account=Universal::safe_replace($account);
        $this->wxuser_model->userid=$_SESSION['userinfo']['user_id'];
        $check=$this->wxuser_model->checkextract();
        if(!$check){
            Universal::Output($this->config->item('request_fall'),'本次提现失败');
        }
        //用支付宝接口提现
        $this->load->model('nonstandard/wxuser_model');
        $this->wxuser_model->out_trade_no = $this->create_ordrenumber();
        $this->wxuser_model->pic = $pic;
        $res=$this->wxuser_model->zfbextract();
        if($res){
            Universal::Output($this->config->item('request_succ'),'提现成功,请查看支付宝余额.',
            '/index.php/nonstandard/center/ViewCenter');
        }else{
            Universal::Output($this->config->item('request_fall'),'提现失败！请检查账户是否正确！');
        }
    }
    /**
     * 获取当前用户余额
     * @return  成功返回余额 string  || 失败返回 string
     */
    function  balance(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        //查询用户的余额
        $this->load->model('nonstandard/wxuser_model');
        $response=$this->wxuser_model->GetBalance($_SESSION['userinfo']['user_id']);
        if($response !== false){
            $balance=$response['balance'];
        }else{
            $balance='0.00';;
        }
        Universal::Output($this->config->item('request_succ'),'','',$balance);
    }
    /**
     * 退出登录
     */
    function loginout(){
        session_destroy();
        header('location:/index.php/nonstandard/system/welcome');
        exit;
    }
    function create_ordrenumber(){
         $number=date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
        return $number;
    }
    
}