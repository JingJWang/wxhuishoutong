<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Turntable_model extends CI_Model {

    private $activity_turn    	 = 'h_activity_turn';//转盘奖励表
    private $activity_recturn    = 'h_activity_recturn';//转盘奖励表
    private $wxuser_task         = 'h_wxuser_task';
    private $tonghua_log         = 'h_tonghua_log';//通花日志记录表
    public $msg                  = '';
    /**
     * 获取抽奖奖励
     * @return      array    result    奖励数组
     */
    function getrew(){
        $sql = 'select turn_id as id,turn_name as name,turn_img as image
                from '.$this->activity_turn.' where turn_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '现在不能抽奖';
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    /**
     * 获取抽奖奖励全部信息
     * @return      array    result    奖励数组
     */
    function rewardinfo(){
        $sql = 'select turn_id as id,turn_name as name,turn_img as image,turn_text as text,
        turn_number as number,turn_type as type,turn_probity as probity
         from '.$this->activity_turn.' where turn_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '现在不能抽奖';
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    /**
     * 查看用户今天是否抽过奖励
     * @param   int     wx_id   用户的id
     * @return  int     num     大于表示抽过，0表示为抽过
     */
    function cnum($wx_id){
        $sql = 'select count(recode_id) as num from '.$this->activity_recturn.' where wx_id='.$wx_id.' and recturn_jointime>'.strtotime(date('Y-m-d'));
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '出错';
            return false;
        }
        $result = $result->result_array();
        return $result['0']['num'];
    }
    /**
     * 用户获取奖励
     */
    function upinfo($wx_id,$obinfo){
        $time = time();
        $insert = array(
            'wx_id' => $wx_id,
            'turn_id' => $obinfo['id'],
            'recturn_jointime' => $time
        );
        $sql = 'select center_integral as integral from '.$this->wxuser_task.' 
                where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '您没有通花，先去福利站吧！';
            return false;
        }
        $integral = 0;
        $user = $result->result_array();
        if (isset($this->cnum) && $this->cnum>=1) {//如果不是第一次，看看是否有再来一次的奖励
            $result = $this->getfree($wx_id);
            if ($result!=0) {//如果免费，不用通花
                $frees = $result;
            }else{//获取通花
                $integral = -30;
            }
        }
        if (isset($user) && ($user['0']['integral']+$integral)<0) {
            $this->msg = '您的通花不足了，请去福利站做点事吧！';
            return false;
        }
        switch ($obinfo['type']) {
            case '1'://通花
                $integral += $obinfo['text'];
                $insert['recturn_status'] = 2;
                break;
            case '2'://话费
                $insert['recturn_status'] = 1;
                break;
            case '3'://再来一次
                $insert['recturn_status'] = 1;
                break;
            default:
                $this->msg = '出错';
                return false;
                break;
        }
        $this->db->trans_begin();
        $this->db->insert($this->activity_recturn,$insert);
        if ($obinfo['type']==2) {
            $sql = 'update '.$this->activity_turn.' set turn_number=turn_number-1,turn_updatatime='.$time.' where turn_id='.$obinfo['id'].' and turn_status=1';
            $this->db->query($sql);
        }
        if ($integral!=0) {//更新用户表
            $sql = 'update '.$this->wxuser_task.' set center_integral=center_integral+'.$integral.',center_updatetime='.$time.' where wx_id='.$wx_id.' and center_status=1';
            $this->db->query($sql);
            $tonghua_log = array(
                'log_userid' => $wx_id,
                'log_total' => $integral,
                'log_content' => '大装盘抽奖',
                'log_status' => 1,
                'log_jointime' => time(),
            );
            $this->db->insert($this->tonghua_log,$tonghua_log);
        }
        if (isset($frees)&&!empty($frees['0']['id'])) {
            $sql = 'update '.$this->activity_recturn.' set recturn_status=2,recturn_uptime='.$time.' where recode_id='.$frees['0']['id'].' and recturn_status=1';
            $this->db->query($sql);
        }
        if ($this->db->trans_status() === false){
            $this->db->trans_rollback();
             $this->msg = '出错';
             return false;
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 查看是否有获取免费机会
     */
    function getfree($wx_id){
        $sql = 'select recode_id as id from '.$this->activity_recturn.' where wx_id='.$wx_id.' and turn_id=8 and recturn_status=1 limit 1';
        $result = $this->db->query($sql);
        if ($result->num_rows>=1) {//免费
            $result = $result->result_array();
        }else{
            $result = 0;
        }
        return $result;
    }
}