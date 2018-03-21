<?php
/*
 * 微信端非标准产品  管理员模块
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends CI_Model {
    //管理员表
    private   $table_admmin  ='h_admin';
    //构造函数
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 检测回收商是否存在
     */
    function  CheckAdmin($username){
        if(empty($username)){
            return false;
        }
        $sql='select id from  where name='.$username.' and status=1';
        $result=$this->db->query($sql);
        $data=$this->db->fetch_query($result);
        if(is_null($data)){
            return  false;
        }else{
            return true;
        }
    }
}