<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Administrator
 * 
 */
class Notice_model extends  CI_Model{
    public $msg = '';
    /**
     * 获取标签信息
     */
    function getalllabel(){
        $sql = 'select notice_id as id,notice_title as title from h_company_notice where notice_fid=0 and notice_type=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 插入文章的信息
     * @param      int      lid      标签id
     * @param      string   name     文章名字
     * @param      string   str      文章的内容
     * @param      string   img      图片的地址
     * @param      string   desc     文章的描述
     * @param      bool     失败 false|成功 true
     */
    function addarticle($info){
        $time = time();  
        $insert = array(
            'notice_title' => $info['name'],
            'notice_icon' => $info['image'],
            'notice_des' => $info['desc'],
            'notice_keys' => $info['akey'],
            'notice_text' => $info['str'],
            'notice_fid' => $info['lid'],
            'notice_type' => 1,
            'notice_jointime' => $time,
            'notice_status' => $info['is_up'],
        );
        $result = $this->db->insert('h_company_notice',$insert);
        if ($this->db->affected_rows()!=1 || $result==false) {
            $this->db->msg = '加入失败';
            return false;
        }
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//文章的总数添加
        if ($this->zredis->link===true) {
            $str = $this->zredis->_redis->exists('CompanyNotice_allnum');
            if ($str==1) {
                $this->zredis->_redis->incr('CompanyNotice_allnum');
            }
            $this->zredis->_redis->del('noticeTexts_'.$info['lid']);
        }
        return true;
    }
    /**
     * 查询文章
     * @param        int        num       重第num查询文章
     */
    function getarticles($num){
        $sql = 'select notice_id as id,notice_title as title,notice_des as des,notice_text as text,notice_jointime as jointime,
                notice_icon as icon,notice_status as status,notice_fid as fid from h_company_notice where notice_type=1 and 
                notice_fid!=0 order by notice_jointime desc limit '.$num.',10';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取已有的文章总数
     */
    function getnums(){
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===true) {
            $num = $this->zredis->_redis->get('CompanyNotice_allnum');
            if ($num != NULL) {
                return $num;
            }
        }
        $sql = 'select count(notice_id) as num from h_company_notice where notice_type=1 and notice_fid!=0';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1) {
            return false;
        }
        $result = $result->result_array();
        if ($this->zredis->link===true) {
            $OK = $this->zredis->_redis->set('CompanyNotice_allnum',$result['0']['num']);
            if ($OK!=true) {return false;}
        }
        return $result['0']['num'];
    }
    /**
     * 获取选定的文章
     * @param        int        id       文章的id
     */
    function gethisarticle($id){
        $sql = 'select notice_id as id,notice_title as title,notice_fid as fid,notice_des as des,notice_keys as akey,
                notice_text as text,notice_icon as icon,notice_jointime as jtime,notice_status as status 
                from h_company_notice where notice_id='.$id.' and notice_fid!=0';
        $article = $this->db->query($sql);
        if ($article->num_rows()<1) {
            return false;
        }
        $result['article'] = $article->result_array();
        return $result;
    }
    /**
     * 更新文章信息
     * @param      int      lid      标签id
     * @param      int      id       文章id
     * @param      string   name     文章名字
     * @param      string   str      文章的内容
     * @param      string   img      图片的地址
     * @param      string   desc     文章的描述
     * @param      bool     失败 false|成功 true
     */
    function uparticleinfo($info,$id){
        $time = time();
        $update = array(
            'notice_title' => $info['name'],
            'notice_icon' => $info['image'],
            'notice_des' => $info['desc'],
            'notice_keys' => $info['akey'],
            'notice_text' => $info['str'],
            'notice_fid' => $info['lid'],
            'notice_uptime' => $time,
            'notice_type' => 1,
            'notice_status' => $info['is_up'],
        );
        $result=$this->db->update('h_company_notice',$update,array('notice_id'=>$id));
        if ($this->db->affected_rows()!=1 || $result==false) {
            $this->db->msg = '更新失败';
            return false;
        }
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//文章的总数添加
        if ($this->zredis->link===true) {
            $this->zredis->_redis->del('noticeTexts_'.$info['lid']);
        }
        return true;
    }
}