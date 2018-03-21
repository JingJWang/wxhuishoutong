<?php
/**
 * 
 * @author ma
 * 定时任务  每天下午一点更新正式知识库文章
 */
class  article_model extends  CI_Model{    
    
    //加载DB
    function  __construct() {
        parent::__construct();
        $this->load->database();
    }
    /**
     * 读取当前状态正常的回收商的信息
     */
    function getArticleInfo(){
    	//获取当天的时间戳
    	$day=strtotime(date('Y-m-d',time()));
    	//昨天1点的时间戳
    	$yesday=$day-39600;
    	echo $yesday;exit;
    	//获取今天1点时间戳
    	$toyday=$day+39600;
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