<?php
header('Content-type:text/html;charset=UTF-8');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Infostatics_model extends CI_Model {
    
    private  $order  ='h_order';    //订单表    
    private  $wxuser ='h_wxuser';   //微信用户 
    private  $order_nonstandard ='h_order_nonstandard';   //微信用户
    private  $shop_record = 'h_shop_record';//商城交易记录订单表
    private  $task_log = 'h_task_log';
    private  $task_info = 'h_task_info';
    function __construct(){
       parent::__construct();
    }
    /**
     * 统计订单 总数  总成交  未成交
     * @param   int     date    日期(数据为空时则表示查询全部)
     */
    function info_statistics($date){
        if (!isset($date) || empty($date)) {
            $date = '1970-00-00';
            $sql = 'select count(wx_id) as login_num from '.$this->wxuser.' where wx_logintime>"'.date('Y-m-d').'" and wx_logintime<"'.date('Y-m-d',strtotime("+1 day")).'"';//登录时间只能统计今日的
            $result = $this->db->query($sql);
            if ($result->num_rows<=0) {
                return false;
            }
            //报价交易
            $result = $result->result_array();
            $data['login_num'] = $result['0']['login_num'];//登录时间
            $sql_join = 'select count(wx_id) as join_num from '.$this->wxuser;
            $sql_order = 'select count(order_id) as order_count,sum(order_bid_price) as order_sum from '.$this->order_nonstandard.' where order_orderstatus=10 and order_status=1 and order_updatetime>'.strtotime($date);
        }else{
            $sql_join = 'select count(wx_id) as join_num from '.$this->wxuser.' where wx_jointime>"'.$date.'" and wx_jointime<"'.date('Y-m-d',strtotime($date)+86400).'"';
            $sql_order = 'select count(order_id) as order_count,sum(order_bid_price) as order_sum from '.$this->order_nonstandard.' where order_orderstatus=10 and order_status=1 and order_updatetime>'.strtotime($date).' and order_updatetime<'.(strtotime($date)+86400);
        }
        //用户信息
        $sql = $sql_join;
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        $data['join_num'] = $result['0']['join_num'];//加入时间
        $sql = $sql_order;
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        if ($result['0']['order_sum']=='') {
            $result['0']['order_sum']=0;
        }
        $data['order_count'] = $result['0']['order_count'];//订单总数量
        $data['order_sum'] = $result['0']['order_sum'];//订单总价格
        return $data;
    }
    /**
     * 某个日期任务完成的数量
     * @param    int        type        任务类型
     * @param    string     date        日期
     * @return   bool       false       错误返回false
     * @return   array      result      返回数据
     */
    function task_count($type,$date){
        $sql = 'select count(a.log_id) as task_num from '.$this->task_log.' as a,'.$this->task_info.' as b where a.task_id=b.task_id and b.task_type='.$type.' and a.task_finishtime>'.strtotime($date).' and a.task_finishtime<'.(strtotime($date)+86400);
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        return $result['0']['task_num'];
    }
    /**
     * 某天的通化商城交易查询
     */
   function shop_count($date){
        $sql = 'select count(record_id) as record_count from '.$this->shop_record.' where record_status=1 and record_updatetime>'.strtotime($date).' and record_updatetime<'.(strtotime($date)+86400);
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        return $result['0']['record_count'];
   }
}