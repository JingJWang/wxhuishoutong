<?php
/*
 * 微信端非标准产品  管理员模块
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Coupon_model extends CI_Model {
    //构造函数
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 检测是否有这个用户
     */
    function checkUserMobile($mobile){
        $sql = 'select wx_id,wx_status from h_wxuser where wx_mobile='.$mobile;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        ///筛出状态不对的账户
        if ($result['0']['wx_status']!=1&&$result['0']['wx_status']!=-1) {
            return false;
        }
        return true;
    }
    /**
     * 通过openid获取用户的电话号码
     * @param       string      openid      用户与微信公众号的唯一标识
     * @return      array       return      用户的信息
     */
    function getUserMobile($openid){
        $sql = 'select wx_mobile as mobile from h_wxuser where wx_openid="'.$openid.'" and (wx_status=1 or wx_status=-1)';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        if (empty($return['0']['mobile'])) {
            return false;
        }
        return $return;
    }
    /**
     * 获取用户的增值券
     * @param       int         mobile      用户的电话号码
     * @return      array       return      用户的增值券的信息
     */
    function obtainUserCou($mobile){
        $sql = 'select a.info_id as inid,b.coupon_mobile as mobile 
                from h_coupon_info as a left join h_coupon_user as b on a.info_id=b.info_id and b.coupon_mobile='.$mobile.'
                where a.class_id=1 and a.info_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        return $return;
    }
    /**
     * 获取某个用户的增值券的具体信息
     * @param       int         mobile          用户的手机号码
     */
    function obtainUserCouInfo($mobile){
        $sql = 'select a.info_id as inid,b.coupon_mobile as mobile,a.info_name as name,a.info_amount as amount,
                info_range as thisrange,info_contime as contime from h_coupon_info as a left join 
                h_coupon_user as b on a.info_id=b.info_id and b.coupon_mobile='.$mobile.'
                where a.class_id=1 and a.info_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        return $return;
    }
    /**
     * 给用户添加增值券
     * @param       string          insertInfo       要插入数据的信息
     * @param       int             num              要插入的数据数量
     * @return      bool            成功放回true|失败放回false
     */
    function addCoupon($insertInfo,$num){
        $str = 'insert into h_coupon_user(info_id,coupon_mobile,coupon_jointime,coupon_status,coupon_from) values'.$insertInfo;
        $result = $this->db->query($str);
        if($this->db->affected_rows() != $num || $result==false){
            return false;
        }
        return true;
    }
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}