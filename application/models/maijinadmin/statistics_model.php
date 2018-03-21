<?php
header('Content-type:text/html;charset=UTF-8');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Statistics_model extends CI_Model {
    
    private  $order  ='h_order';    //订单表    
    private  $wxuser ='h_wxuser';   //微信用户
    private  $admin  ='h_admin';    //系统用户表
    private  $voucherlog ='h_voucher_log' ; //现金券表
    function __construct(){
       parent::__construct();
    }
    /**
     * 统计订单 总数  总成交  未成交
     */
    function order_statistics_view(){
        $sql='select  count(id),order_status from '.$this->order.' group by  order_status';
        $data=$this->db->customize_query($sql);
        if($data === false){
            return false;
        }else{
            return $data;
        }
    }
    /*
     * 统计当前 系统用户
     */
    function wxuser_statistics_view(){
        $sql='select count(wx_id),wx_status from '.$this->wxuser.' group by wx_status';
        $data=$this->db->customize_query($sql);
        if($data === false){
            return false;
        }else{
            return $data;
        }
    }
    /*
     * 系统内用户数量
     */
    function admin_statistics_view(){
        $sql='select count(id),power_type from '.$this->admin.' group by power_type';
        $data=$this->db->customize_query($sql);
        if($data === false){
            return false;
        }else{
            return $data;
        }
    }
    /*
     * 现金券数量
     */   
    function voucher_statistics_view(){
        $sql='select count(id),log_type from '.$this->voucherlog.' group by log_type';
        $data=$this->db->customize_query($sql);
        if($data === false){
            return false;
        }else{
            return $data;
        }
    }
}