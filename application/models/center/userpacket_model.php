<?php
header('Content-type:text/html;charset=UTF-8');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Userpacket_model extends CI_Model {
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 检查是否有此电话号码的用户，并获取他的信息
     * @param      int        mobile        用户电话号
     */
    function getuser(){
        $sql = 'select wx_id as id,wx_openid as openid,wx_status as status 
                from h_wxuser where wx_mobile='.$this->input->post('mobile',true);
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 检查用户是否交易成功
     */
    function checktransa($wx_id){
        $sql = 'select count(order_id) as num from h_order_nonstandard 
                where wx_id='.$wx_id.' and order_orderstatus=10 and order_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['num']>0) {
            return true;
        }
        return false;
    }
    /**
     * 检查通话商城是否买过商品
     */
    function checkshop($wx_id){
        $sql = 'select count(record_id) as num from h_shop_record 
                where record_userid='.$wx_id.' and (record_status=1 or record_status=2)';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['num']>0) {
            return true;
        }
        return false;
    }
    /**
     * 检查是否分享过
     */
    function checkshare($wx_id){
        $sql = 'select center_laster_share from h_wxuser_task where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['center_laster_share']>0) {
            return true;
        }
    }
    /**
     * 检查是否有 做任务，转盘，报过单
     */
    function checkconduct($wx_id){
        $sql = 'select center_laster_sign from h_wxuser_task where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['center_laster_sign']>0) {
            return true;
        }
        $sql = 'select count(recode_id) from h_activity_recturn where wx_id='.$wx_id;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['num']>0) {
            return true;
        }
        $sql = 'select count(log_id) from h_task_log where wx_id='.$wx_id.' and task_process=4 and task_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['num']>0) {
            return true;
        }
        $sql = 'select count(order_id) from h_order_nonstandard where wx_id='.$wx_id;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['num']>0) {
            return true;
        }
        return false;
    }
}