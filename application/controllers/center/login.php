<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Administrator
 *
 */
class  login  extends  CI_Controller{
    
    /**
     * 系统登录
     * @param   string   name  用户名
     * @param   string   pwd   密码
     * @return  成功返回  string 跳转地址  || 失败返回  string 失败原因
     */
    function index(){       
        $username=$this->input->post('user',true);
        $userpwd=$this->input->post('pwd',true);
        //校验账号格式是否不正确
        if(filter_var($username,FILTER_VALIDATE_EMAIL) === false &&
                is_numeric($username) === false){
            Universal::Output($this->config->item('request_fall'),'账号填写错误!');
        }   
        //校验当前用户登录是否合法
        $this->load->model('center/login_model');
        $this->login_model->user=$username;
        $this->login_model->pwd=Universal::filter($userpwd);
        $userdata=$this->login_model->checkUser();
        if($userdata === false){
            Universal::Output('3000',$this->login_model->error_msg);
        }
        $_SESSION['user']=array('id'=>$userdata['user_id'],'name'=>$userdata['user_name'],
                'mobile'=>$userdata['user_mobile'],'email'=>$userdata['user_email'],
                'role'=>$userdata['role_id'],'coop'=>$userdata['coop_number']
        );
        //记录用户登录信息
        $this->login_model->user_id=$_SESSION['user']['id'];
        $this->login_model->user_loginip=$this->input->ip_address();
        $res=$this->login_model->editLoginInfo();
        if($res){
            Universal::Output($this->config->item('request_succ'),'','/view/control/statistics.html','');
        }else{
            Universal::Output($this->config->item('request_fall'),'','','');
        }       
    }
    /**
     * 校验当前用户是否登录过(登录页面)
     */
    function isOnLine(){
        if(!isset($_SESSION['user'])){
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/view/control/login.html');
        }
        if(!isset($_SESSION['user']['id'])){
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/view/control/login.html');
        }
        Universal::Output($this->config->item('request_succ'),'已经登录,正在跳转','/view/control/digitaoder.html');
    } 
    /**
     * 校验用户是否已经登录
     */
    function isLogin(){
        if(!isset($_SESSION['user'])){
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/view/control/login.html');
        }
        if(!isset($_SESSION['user']['id'])){
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/view/control/login.html');
        }
    }
    /**
     * 退出登录 清空当前用户的session
     */
    function outLogin(){
        unset($_SESSION['user']);
        Universal::Output($this->config->item('request_succ'),'','/view/control/login.html');
    }
    /**
     * 读取当前用户的model列表
     */
    function getmodel(){
        //校验当前用是否登录
        $this->isLogin();
        //读取用户的权限列表
        $this->load->model('center/login_model');
        $this->login_model->role=$_SESSION['user']['role'];
        $response=array(
                'name'=>$_SESSION['user']['name'],
        );
        $permit=$this->login_model->getUserPermit();
        if($permit === false){
            Universal::Output($this->config->item('request_fall'),'获取模块列表失败');
        }else{
            $response['permit']=$permit;
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }
       
    }
    
}