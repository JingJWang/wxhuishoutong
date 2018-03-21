<?php
/**
 * 
 * @author ma
 * 定时任务  订单通知
 */
class  Everdaydata_model extends  CI_Model{    
    // private $activity_turn      = 'h_activity_turn';
    //加载DB
    function  __construct() {
        parent::__construct();
        $this->load->database();
    }
    /**
     * 
     * 
     */
    function getdata(){        
        $time = strtotime(date('Y-m-d'));
        //今日加入并注册人数
        $sql = 'select count(wx_id) as join_num from h_wxuser
                where wx_jointime>="'.date('Y-m-d H:i:s',$time).'" and wx_mobile!=""';
        $re['day_join'] = $this->db->query($sql);
        if ($re['day_join']->num_rows<1) {
            return false;
        }
        $re['day_join'] = $re['day_join']->result_array();
        $re['day_join'] = $re['day_join']['0']['join_num'];
        //今日登陆的人数
        $sql = 'select count(wx_id) as loginum from h_wxuser where wx_logintime>="'.date('Y-m-d H:i:s',$time).'"';
        $re['day_login'] = $this->db->query($sql);
        if ($re['day_login']->num_rows<1) {
            return false;
        }
        $re['day_login'] = $re['day_login']->result_array();
        $re['day_login'] = $re['day_login']['0']['loginum'];


        //通化商城交易记录
        $sql = 'select count(record_id) as record_count from h_shop_record where record_status=1 
                and record_updatetime>='.$time;
        $re['day_shop'] = $this->db->query($sql);
        if ($re['day_shop']->num_rows<1) {
            return false;
        }
        $re['day_shop'] = $re['day_shop']->result_array();
        $re['day_shop'] = $re['day_shop']['0']['record_count'];
        
        //成交信息
        $sql = 'select count(order_id) as order_count,sum(order_bid_price) as order_sum 
                from h_order_nonstandard where order_orderstatus=10 and order_status=1 
                and order_updatetime>"'.$time.'"';
        $day_order = $this->db->query($sql);
        if ($day_order->num_rows<=0) {
            return false;
        }
        $day_order = $day_order->result_array();
        $re['day_order_sum'] = $day_order['0']['order_sum'];
        $re['day_order_count'] = $day_order['0']['order_count'];
        if ($re['day_order_sum'] == null) {
            $re['day_order_sum'] = 0;
        }
        //任务
        $re['day_invite_u'] = $this->task_count(5,$time);//邀请任务
        $re['day_turnover'] = $this->task_count(2,$time);//回收任务
        $re['day_game'] = $this->task_count(7,$time);//回收任务
        //今日分享过的人数
        $sql = 'select count(center_id) as last_share from h_wxuser_task where center_laster_share>="'.$time.'"';
        $re['day_share'] = $this->db->query($sql);
        if ($re['day_share']->num_rows<1) {
            return false;
        }
        $re['day_share'] = $re['day_share'] ->result_array();
        $re['day_share'] = $re['day_share']['0']['last_share'];
        //今日第一次签到的用户
        $sql = 'select count(wx_id) as first_sign from h_wxuser_task where center_laster_sign>="'.$time.'" and center_sign=1';
        $re['day_firstSign'] = $this->db->query($sql);
        if ($re['day_firstSign']->num_rows<1) {
            return false;
        }
        $re['day_firstSign'] = $re['day_firstSign'] ->result_array();
        $re['day_firstSign'] = $re['day_firstSign']['0']['first_sign'];
        //领取过奖金的总人数（任务）
        $sql = 'select count(center_id) as get_reward_pep from h_wxuser_task where center_bonus>0';
        $re['day_getReward'] = $this->db->query($sql);
        if ($re['day_getReward']->num_rows<1) {
            return false;
        }
        $re['day_getReward'] = $re['day_getReward'] ->result_array();
        $re['day_getReward'] = $re['day_getReward']['0']['get_reward_pep'];
        $re['day_time'] = $time;
        $re['day_jointime'] = time();
        $result = $this->db->insert('h_datalog_everday',$re);
        if ($result==false) {
            return false;
        }
        return true;
    }
    function task_count($type,$time){
        $sql = 'select count(a.log_id) as task_num from h_task_log as a,
                h_task_info as b where a.task_id=b.task_id and b.task_type='.$type.' 
                and a.task_finishtime>'.$time;
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        $result = $result->result_array();
        return $result['0']['task_num'];
    }
}