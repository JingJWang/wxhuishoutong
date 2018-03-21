<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Taskverify_model extends  CI_Model{
    /**
     * 获取借贷宝任务信息
     * @param        int        star        开始的个数
     * @param        int        type       需要数据的类型
     * @param        int        type       需要数据的时间
     * @return       int        log_id     任务的id
     * @return       int        wx_id      用户的id
     * @return       int        wx_mobile  用户的电话
     */
    function getlist($star,$type,$time){
        $sql = 'select a.log_id,a.wx_id,b.wx_mobile,b.wx_openid,a.log_content from h_task_log as a,
                h_wxuser as b where a.task_id=17 and a.task_process='.$type.' and 
                a.task_status=1 and a.wx_id=b.wx_id '.$time.' order by 
                log_content desc limit '.$star.',10';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1||$result==false) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取此类型的个数
     * @param        int        type       需要数据的类型
     * @return       int        num        返回个数
     */
    function gettasknum($type,$time){
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===true && $time!='') {
            $num = $this->zredis->_redis->get('taskwaitvernum_'.$type);
            if ($num != NULL) {
                return $num;
            }
        }
        $sql = 'select count(log_id) as num from h_task_log where task_id=17 and 
                task_process='.$type.' '.$time.' and task_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1||$result==false) {
            return false;
        }
        $result = $result->result_array();
        if ($this->zredis->link===true) {
            $OK = $this->zredis->_redis->set('taskwaitvernum_'.$type,$result['0']['num']);
            if ($OK!=true) {return false;}
        }
        return $result['0']['num'];
    }
    /**
     * 更新数据
     * @param        string       fins['data']       要更新的用户id(审核通过的)
     * @param        int          fins['num']        更新的数量(审核通过的)
     * @param        string       nfins['data']      要更新的用户id(审核未通过的)
     * @param        int          nfins['num']        更新的数量(审核未通过的)
     * @return       bool         正确返回true|错误返回false
     */
    function uptaskdata($fins,$nfins){
        $time = time();
        $this->db->trans_begin();
        if (!empty($fins)) {
            $sql = 'update h_task_log set task_finishtime='.$time.',task_updatetime='.$time.'
                   ,task_process=3 where log_id in('.$fins['data'].') and task_id=17 and 
                   (task_process=6 or task_process=7) and task_status=1';
            $result = $this->db->query($sql);
            if ($this->db->affected_rows()!=$fins['num'] || $result==false) {
                $this->db->trans_rollback();
                return false;
            }
        }
        if (!empty($nfins)) {
            $sql = 'update h_task_log set task_updatetime='.$time.',task_process=7 
                   where log_id in('.$nfins['data'].') and task_id=17 and 
                   task_process=6 and task_status=1';
            $result = $this->db->query($sql);
            if ($this->db->affected_rows()!=$nfins['num'] || $result==false) {
                $this->db->trans_rollback();
                return false;
            }
        }
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===true) {
            $this->zredis->_redis->del(array('taskwaitvernum_6','taskwaitvernum_7',
                'taskwaitvernum_3'));
            $OK = $this->zredis->_redis->del('taskinfouptime');//重新生成缓存
            if ($OK===false) {
                $this->db->trans_rollback();
            }
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 更具号码获取获取注册信而富的用户
     * @param        int         phone       用户的电话号码
     */
    function xgetinfo($mobiles){
        $str_m = implode(' or a.wx_mobile=', $mobiles);
        $sql = 'select b.log_id as id,a.wx_openid as oid,a.wx_mobile as mobile,b.task_process as process,
                b.task_jointime as jointime from h_wxuser as a,h_task_log as b where a.wx_id=b.wx_id and b.task_id=18 
                and b.task_process=2 and (a.wx_mobile='.$str_m.') and a.wx_status!=3';
        $result = $this->db->query($sql);
        if ($result->num_rows()<1 || $result===false) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 更新信息
     */
    function xupinfo($ar_data){
        $time = time();
        $this->db->trans_begin();
        $sql = 'update h_task_log set task_finishtime='.$time.',task_updatetime='.$time.'
                ,task_process=3 where log_id in('.$ar_data['m'].') and task_id=18 and 
                task_process=2 and task_status=1';
        $result = $this->db->query($sql);
        if ($this->db->affected_rows()!=$ar_data['n'] || $result==false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }
}