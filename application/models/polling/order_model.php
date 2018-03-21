<?php
/**
 * 
 * @author ma
 * 定时任务  订单通知
 */
class  order_model extends  CI_Model{    
    
    //加载DB
    function  __construct() {
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取当前所有状态正常回收商的信息
     * 
     */
    function getCoopInfo(){        
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $int_exists=$this->zredis->_redis->exists('notice_coopInfo');
        //校验缓存的回收商信息 不存在 超时  没设置过期时间
        if(!$int_exists){
            //读取回收商信息
            $result=$this->getCoopList();
            $this->zredis->_redis->set('notice_coopInfo',$result);
            $this->zredis->_redis->expire('notice_coopInfo',180);
            return json_decode($result,true);
        }       
        $info=$this->zredis->_redis->get('notice_coopInfo');
        return json_decode($info,true);
    }
    /**
     * 读取当前状态正常的回收商的信息
     */
    function getCoopList(){
        $sql='select cooperator_number,cooperator_distance,cooperator_lat,
              cooperator_lng from h_cooperator_info where cooperator_status=1
              and cooperator_lng!=" " and cooperator_lat != " "';
        $query=$this->db->query($sql);
        if(!$query || $query->num_rows() < 1){
            $this->msg='没有获取到回收商的信息';
            return  false;
        }
        $result=$query->result_array();
        return json_encode($result);
    }
    /**
     * 查询 当前提交订单超过24小时的订单
     * @return  成功返回   string 订单id集合  | 执行失败 没有订单 执行退出
     */
    function getOrderList(){
        echo $sql='select order_number from  h_order_nonstandard  where  
              order_orderstatus=1 and  order_submittime  < '.strtotime("-1 day");
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            exit;
        }
        $data=$query->result_array();
        $id='';
        foreach ($data as $k=>$v){
            $id .= $v['order_number'].',';
        }
        $id=trim($id,',');
        return $id;   
    }
    /**
     * 更新订单状态为等待提交  更新对应报价为失效
     * @param   string   orderid   订单id
     * @return  成功返回 bool ture | 失败返回  bool false
     */
    function editOrder(){        
        $offer_sql='update h_cooperator_offer set offer_status=-1 where order_id in('.$this->orderid.')';
        $res_offer=$this->db->query($offer_sql);
        $order_sql='update h_order_nonstandard  set order_orderstatus=4,order_updatetime='.time().'  where
					  order_number in('.$this->orderid.')';
        $res_order=$this->db->query($order_sql);
        if($order_sql !== false && $res_order !== false){
            return true;
        }
        return false;
    }
    
}