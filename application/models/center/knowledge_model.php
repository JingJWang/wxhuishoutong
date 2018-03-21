<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Administrator
 * 
 */
class Knowledge_model extends  CI_Model{
    public $msg = '';
    /**
     * 获取标签信息
     */
    function getalllabel(){
        $sql = 'select label_id,label_name from h_article_label where label_status=1';
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
        $sql = 'select label_id,label_name from h_article_label where label_id='.$info['lid'].' and label_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '没有相应的标签';
            return false;
        }
        $result = $result->result_array();
        $this->db->trans_begin();   
        $insert = array(
            'text_name' => $info['name'],
            'text_image' => $info['image'],
            'text_descrip' => $info['desc'],
            'text_content' => $info['str'],
            'text_label' => $result['0']['label_name'],
            'text_jointime' => $time,
        	'text_delstatus' => 1,
            'text_status' => $info['is_up']
        );
        $result = $this->db->insert('h_article_text',$insert);
        if ($this->db->affected_rows()!=1 || $result==false) {
            $this->db->msg = '加入失败';
            $this->db->trans_rollback();
            return false;
        }
        $textid=$this->db->insert_id();
        $links = array(
            'link_textid' => $textid,
            'link_lableid' => $info['lid'],
            'link_jointime' => $time,
            'link_status' => 1,
        );
        $result = $this->db->insert('h_article_link',$links);
        if ($this->db->trans_status() === false || $this->db->affected_rows()!=1 || $result==false) {
            $this->db->msg = '加入失败';
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//文章的总数添加
        if ($this->zredis->link===true) {
            $str = $this->zredis->_redis->exists('article_allnum');
            if ($str==1) {
                $this->zredis->_redis->incr('article_allnum');
            }
        }
        return true;
    }
    /**
     * 查询文章
     * @param        int        num       重第num查询文章
     */
    function getarticles($num){
        $sql = 'select text_id as id,text_name as name,text_label as label,text_clicknum as click,text_sharenum as share,
                text_jointime as jtime,text_status as status from h_article_text where text_delstatus=1 order by text_jointime desc limit '.$num.',10 ' ;
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
            $num = $this->zredis->_redis->get('article_allnum');
            if ($num != NULL) {
                return $num;
            }
        }
        $sql = 'select count(text_id) as num from h_article_text';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1) {
            return false;
        }
        $result = $result->result_array();
        if ($this->zredis->link===true) {
            $OK = $this->zredis->_redis->set('article_allnum',$result['0']['num']);
            if ($OK!=true) {return false;}
        }
        return $result['0']['num'];
    }
    /**
     * 获取选定的文章
     * @param        int        id       文章的id
     */
    function gethisarticle($id){
        $sql = 'select text_id as id,text_name as name,text_image as img,text_descrip as des,text_content as content,text_status as status from h_article_text where text_id='.$id;
        $article = $this->db->query($sql);
        if ($article->num_rows()<1) {
            return false;
        }
        $result['article'] = $article->result_array();
        $sql = 'select link_lableid as lid from h_article_link where link_textid='.$id.' and link_status=1';
        $labels = $this->db->query($sql);
        if ($labels->num_rows()<1) {
            $result['labels'] = 0;
            return $result;
        }
        $result['labels'] = $labels->result_array();
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
        $sql = 'select label_id,label_name from h_article_label where label_id='.$info['lid'].' and label_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '没有相应的标签';
            return false;
        }
        $result = $result->result_array();
        $sql = 'select link_lableid as lid from h_article_link where link_textid='.$id.' and link_status=1';
        $labels = $this->db->query($sql);
        if ($labels->num_rows()<1) {
            $this->msg = '没有找到文章的标签';
            return false;
        }
        $labels = $labels->result_array();
        $this->db->trans_begin();   
        if ($labels['0']['lid']!=$info['lid']) {//更新标签
            $uplabel = array(
                'link_lableid' => $info['lid'],
                'link_uptime' => $time,
            );
            $return=$this->db->update('h_article_link',$uplabel,array('link_textid'=>$id));
            if ($this->db->affected_rows()!=1 || $return==false) {
                $this->db->msg = '更新失败';
                $this->db->trans_rollback();
                return false;
            }
        }
        $update = array(
            'text_name' => $info['name'],
            'text_image' => $info['image'],
            'text_descrip' => $info['desc'],
            'text_content' => $info['str'],
            'text_label' => $result['0']['label_name'],
            'text_uptime' => $time,
            'text_status' => $info['is_up'],
        );
        $result==$this->db->update('h_article_text',$update,array('text_id'=>$id));
        if ($this->db->trans_status() === false || $this->db->affected_rows()!=1 || $result==false) {
            $this->db->msg = '更新失败';
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 删除文章
     */
    function getdelart(){
    	$sql='update h_article_text a set text_delstatus=0 where text_id='.$this->id;
    	$query = $this->db->query($sql);
    	if($this->db->affected_rows()!=1){
    		$this->db->msg = '该条记录不存在';
    		return false;
    	}
    	return true;
    }
    
}