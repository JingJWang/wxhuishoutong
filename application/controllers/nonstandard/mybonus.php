<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
 /**
  * 我的奖金入口
  * @author sun
  * 
  */
class Mybonus extends CI_Controller { 
    /**
     * 我的奖金
     */
    function mybonusList(){
    	//校验登录
         $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
    	$this->load->model('nonstandard/mybonus_model');
    	$data=$this->mybonus_model->GetBonus();
    	$data['bonustatus']=$this->mybonus_model->bonustatus();
        $this->load->view('nonstandard/myBonus',$data);
       
    }
    /**
     * 排行榜
     */
    function ranking(){
    	//校验登录
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
    	$this->load->model('nonstandard/mybonus_model');
    	//排行榜显示
    	$data['getrank']=$this->mybonus_model->getrank();
    	//排行榜我的个人显示
    	$data['list']=$this->mybonus_model->ranking();
    	$this->load->view('nonstandard/ranking',$data);
    }
}