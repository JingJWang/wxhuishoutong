<?php
/**
 * 订单模块  数码订单
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class settlement extends CI_Controller{
    /*
     * 功能描述： 回收奢侈品和回收数码添加模块
     * 
     */
    function settlementAdd(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数
        $data=$this->input->post();
        foreach ($data as $k=>$v){
            if(empty($v)){
                Universal::Output($this->config->item('request_fall'),'没有获取必填参数的值');
            }
        }
        $this->load->model('center/settlement_model');
        $this->settlement_model->oid=$data['number'];
        $this->settlement_model->mobile=$data['tel'];
        $this->settlement_model->gname=$data['names'];
        $this->settlement_model->price=$data['price'];
        $this->settlement_model->nprice=$data['sell'];
        $this->settlement_model->oprice=$data['rest'];
        $this->settlement_model->rtime=strtotime($data['stime']);
        $this->settlement_model->otime=time();
        $this->settlement_model->rtype=$data['type'];
        $this->settlement_model->mobileAdd=$_SESSION['user']['mobile'];
        $response=$this->settlement_model->insertSettlement();
        if($response == false){
        	Universal::Output($this->config->item('request_fall'),'没有获取到结果');
        }else{
            Universal::Output($this->config->item('request_succ'));
        }
    }
    //查看 列表页面
    /*
     * 查看回收列表页面
     */
    function settlementList(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	//校验获取参数
    	$postinfo=$this->input->post();
    	foreach ($postinfo as $k=>$v){
    		if(empty($v)){
    			Universal::Output($this->config->item('request_fall'),'没有获取必填参数的值');
    		}
    	}
    	$this->load->model('center/settlement_model');
    	//参数传值
    	$this->settlement_model->rtype=$postinfo['type'];
    	$this->settlement_model->mobileAdd=$_SESSION['user']['mobile'];
    	$this->settlement_model->page=$postinfo['page'];
    	$this->settlement_model->num=10;
    	$response=$this->settlement_model->selectSettlement();
    	if($response == false){
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}else{
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}
    }
}