<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author Administrator
 * 
 */
class Preshop_model extends  CI_Model{
    /**
     * 获取用户需要手机的类型信息
     */
    function getpreinfo($num,$type){
        $sql = 'select pre_id as id,pre_mobile as mobile,pre_content as content,pre_jointime as jtime 
                from h_preorder_log where pre_status='.$type.' order by pre_jointime desc limit '.$num.','.($num+20);
        $model_query = $this->db->query($sql);
        if($model_query === false || $model_query->num_rows < 1){
            return false;
        }
        return $model_query->result_array();
    }
    /**
     * 改变已经通话过的状态
     */
    function changecall($id){
        $update = array(
            'pre_updatetime' => time(),
            'pre_status' => 2,
        );
        $where = array('pre_id' => $id);
        $result = $this->db->update('h_preorder_log',$update,$where);
        if ($result === false||$this->db->affected_rows()!=1) {
            return false;
        }
        return true;
    }
}