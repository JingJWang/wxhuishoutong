<?php
/**
 * 
 * @author ma
 * 定时任务  订单通知
 */
class  Turnrotate_model extends  CI_Model{    
    private $activity_turn      = 'h_activity_turn';
    //加载DB
    function  __construct() {
        parent::__construct();
        $this->load->database();
    }
    /**
     * 
     * 
     */
    function getrew(){        
        $sql = 'select turn_id as id,turn_number as number,turn_type as type,
                turn_stock as stock,turn_evednum as evednum,turn_totime as totime
                from '.$this->activity_turn.' where turn_type=2 and turn_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    function getship($ship){
        $time = time();
        if ($ship['stock']>=($need=$ship['evednum']-$ship['number'])) {
            $update['turn_stock'] = $ship['stock']-$need;
            $update['turn_number'] = $ship['evednum'];
        }else{//货物少于每天需要的数量时
            $update['turn_stock'] = 0;
            $update['turn_number'] = $ship['number']+$ship['stock'];
        }
        $update['turn_totime'] = $time;
        $update['turn_updatatime'] = $time;
        $where = array(
            'turn_id' => $ship['id'],
            'turn_status' => 1,
        );
        $result = $this->db->update($this->activity_turn,$update,$where);
        if ($result==false) {
            exit('出错');
        }
        return true;
    }      
}