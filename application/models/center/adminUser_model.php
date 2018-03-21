<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Administrator
 * 
 */
class Adminuser_model extends  CI_Model{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 检查手机号码是否被注册过
     * phoneNum      int       手机号码
     */
    function checkrepeat(){
        $sql = 'select user_id from h_admin_user where user_mobile="'.$this->mobile.'"';
        $result = $this->db->query($sql);
        if ($result->num_rows()>0) {
            Universal::Output($this->config->item('request_fall'),'该手机号码已存在');
        }
        $sql = 'select user_id from h_admin_user where user_mobile="'.$this->email.'"';
        $result = $this->db->query($sql);
        if ($result->num_rows()>0) {
            Universal::Output($this->config->item('request_fall'),'该邮箱已存在');
        }
    }
    /**
     * 插入用户信息
     * @param  string  name  用户名
     * @param  string  pwd   密码
     * @return 成功 返回 bool true || 失败返回 bool false
     */
    function joinInfo(){
        $insert = array(
            'role_id' => $this->rid,
            'user_name' => $this->name,
            'user_mobile' => $this->mobile,
            'user_email' => $this->email,
            'user_password' => $this->pw,
            'user_status' => 1,
            'user_jointime' => time(),
        );
        $result = $this->db->insert('h_admin_user',$insert);
        return $result;
    }
    /**
     * 更改用户信息
     * @param     int     uid     要修改的字段id
     * @param     int     rid     所拥有的权限
     * @param     int     name    更改的名字
     * @return    bool    正确true|错误false
     */
    function modifyInfo(){
        $update = array(
            'role_id' => $this->rid,
            'user_name' => $this->name,
            'user_updatetime' => time(),
        );
        if ($this->pw != '') {
            $update['user_password'] = $this->pw;
        }
        $result = $this->db->update('h_admin_user',$update,array('user_id'=>$this->uid));
        return $result;
    }
    /**
     * 查看用户
     * @param       int       page       查看的开始条数
     * @param       int       page_per   结束的条数
     */
    function checkInfo($page,$page_per){
        $sql = 'select user_id as uid,role_id as id,user_name as name,user_mobile as mobile,user_email as email,user_status as status,
                user_lasttime as time from h_admin_user order by user_id desc limit '.$page.','.$page_per;
        $return = $this->db->query($sql);
        if ($return->num_rows<1) {
            return false;
        }
        $result['list'] = $return->result_array();
        $sql = 'select count(user_id) as number from h_admin_user';
        $return = $this->db->query($sql);
        if ($return->num_rows<1) {
            return false;
        }
        $num = $return->result_array();
        $result['num'] = $num['0'];
        return $result;
    }
    /**
     * 查找用户
     */
    function selectInfo(){
        $sql = 'select user_id as uid,role_id as id,user_name as name,user_mobile as mobile,user_email as email,user_status as status,
                user_lasttime as time from h_admin_user where user_mobile="'.$this->mobile.'"';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        return $result['0'];
    }
    /**
     * 获取某个用户的信息
     */
    function getOneInfo(){
        $sql = 'select user_id as uid,role_id as id,user_name as name,user_mobile as mobile,user_email as email,user_status as status,
                user_lasttime as time from h_admin_user where user_id='.$this->uid;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        return $result['0'];
    }
    /**
     * 删除用户
     */
    function delAdmin(){
        $del = array(
            'user_updatetime' => time(),
            'user_status' => -1,
        );
        $result = $this->db->update('h_admin_user',$del,array('user_id'=>$this->uid));
        return $result;
    }
}