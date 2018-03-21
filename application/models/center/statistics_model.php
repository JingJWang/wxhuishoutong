<?php
header('Content-type:text/html;charset=UTF-8');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Statistics_model extends CI_Model {
	private $wxuser = 'h_wxuser';
	private $shop_record = 'h_shop_record';
	private $task_log = 'h_task_log';
	private $task_info = 'h_task_info';
	private $order_nonstandard = 'h_order_nonstandard';

    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 统计用户信息
     * @param    date     star_d      开始时间
     * @param    date     end_d       结束时间
     */
    function userinfo($star_d,$end_d){
        $sql = 'select count(wx_id) as join_num from '.$this->wxuser.'
                where wx_jointime>"'.$star_d.'" and wx_jointime<"'.$end_d.'" and wx_mobile!=""';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        $return['joinum'] = $result['0']['join_num'];
        $sql = 'select count(wx_id) as loginum from h_wxuser where wx_logintime>="'.$star_d.'" and wx_logintime<="'.$end_d.'"';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        $return['loginum'] = $result['0']['loginum'];
        return $return;
    }
    /**
     * 通花商城的信息
     * @param    date     star_d      开始时间
     * @param    date     end_d       结束时间
     */
    function tonginfo($star_d,$end_d){
    	$sql = 'select count(record_id) as record_count from '.$this->shop_record.' where record_status=1 
    	        and record_updatetime>'.strtotime($star_d).' and record_updatetime<'.strtotime($end_d);
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        return $result['0']['record_count'];
    }
    /**
     * 某个日期任务完成的数量
     * @param    int        type        任务类型
     * @param    date       star_d      开始时间
     * @param    date       end_d       结束时间
     * @return   bool       false       错误返回false
     * @return   array      result      返回数据
     */
    function task_count($type,$star_d,$end_d){
        $sql = 'select count(a.log_id) as task_num from '.$this->task_log.' as a,
                '.$this->task_info.' as b where a.task_id=b.task_id and b.task_type='.$type.' 
                and a.task_finishtime>'.strtotime($star_d).' and a.task_finishtime<'.strtotime($end_d);
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        return $result['0']['task_num'];
    }
    /**
     * 成交信息
     * @param    date     star_d      开始时间
     * @param    date     end_d       结束时间   
     */
    function orderinfo($star_d,$end_d){
    	$sql = 'select count(order_id) as order_count,sum(order_bid_price) as order_sum 
    	        from '.$this->order_nonstandard.' where order_orderstatus=10 and order_status=1 
    	        and order_updatetime>'.strtotime($star_d).' and order_updatetime<'.strtotime($end_d);
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
     * 每日统计信息
     */
    function everday($star_d,$end_d){
        $sql = 'select sum(day_firstSign) as sign from h_datalog_everday where day_time>="'.strtotime($star_d).'" and day_time<="'.strtotime($end_d).'"';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        $return['every']['sign'] = $result['0']['sign'];
        $sql = 'select sum(day_login) as login from h_datalog_everday where day_time>="'.strtotime($star_d).'" and day_time<="'.strtotime($end_d).'"';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        $return['every']['login'] = $result['0']['login'];
        $sql = 'select day_getReward from h_datalog_everday where day_time="'.strtotime($star_d).'" or day_time="'.strtotime($end_d).'"';
        $result = $this->db->query($sql);
        if ($result->num_rows==2) {
            $result = $result->result_array();
            if ($result['0']['day_getReward']>$result['1']['day_getReward']) {
                $return['every']['reward'] = $result['0']['day_getReward'] - $result['1']['day_getReward'];
            }else{
                $return['every']['reward'] = $result['1']['day_getReward'] - $result['0']['day_getReward'];
            }
        }
        return $return;
    }
    function daycount($star_d,$end_d){
        $sql = 'select sum(day_share) as share,sum(day_firstSign) as sign,sum(day_login) as login,
                sum(day_join) as join_num,sum(day_shop) as record_count,sum(day_order_count) as order_count,
                sum(day_turnover) as turnover,sum(day_game) as game,sum(day_invite_u) as invite_u,
                sum(day_order_sum) as order_sum from h_datalog_everday 
                where day_time>="'.strtotime($star_d).'" and day_time<="'.strtotime($end_d).'"';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        $return['other'] = $result['0'];
        $sql = 'select day_getReward from h_datalog_everday where day_time="'.strtotime($star_d).'" or day_time="'.strtotime($end_d).'"';
        $result = $this->db->query($sql);
        if ($result->num_rows==2) {
            $result = $result->result_array();
            if ($result['0']['day_getReward']>$result['1']['day_getReward']) {
                $return['reward'] = $result['0']['day_getReward'] - $result['1']['day_getReward'];
            }else{
                $return['reward'] = $result['1']['day_getReward'] - $result['0']['day_getReward'];
            }
        }
        foreach ($return['other'] as $k => $v) {
            if ($v == null) {
                $return['other'][$k] = '0';
            }
        }
        return $return;
    }
}