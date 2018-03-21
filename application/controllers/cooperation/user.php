<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class User extends CI_Controller {
    
    /**
     * 显示当前用户门店的详细信息
     */
    function  ViewEditaddress(){
        $this->load->helper('array');
        $coulms=array('username');
        $data=elements($coulms, $this->input->get(), '');
        $this->load->database();
        $this->load->model('cooperation/user_model');
        $response['address']=$this->user_model->GetMerchantInfo($_SESSION['userid']);
        $this->load->model('common/wxcode_model');
        $response['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        $this->load->library('common/geohash');
        $this->load->view('cooperation/vieweditaddres',$response);
    }
    /**
     * 保存用户修改的地址信息
     */
    function  Editaddres(){
        $this->load->helper('array');
        $coulms=array('phone','province','city','latitude','longitude','address','time','status','mapaddres','name');
        $request=elements($coulms, $this->input->post(), '');
        $this->load->database();
        $this->load->model('cooperation/user_model');
        $response=$this->user_model->SaveAddres($request);
        echo json_encode($response);
        exit();
    }
    
    
}