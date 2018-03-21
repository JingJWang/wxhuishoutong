<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:订单管理类
 */
class order_model extends CI_Model {
    //代金券表
    private  $order='h_order';
    // 用户表
    private  $admin='h_admin';
    //微信用户表
    private  $wxuser='h_wxuser';
    /**
     * 功能描述:后台订单查询
     * 参数说明:array $data 订单条件数组
     * 被引用:
     *      controllers/maijinadmin/order.php select_order_list();
     */
    function select_order_list($data){
        $sqlnumber='select a.id from '.$this->order.' as a left join '.$this->wxuser.' as b 
              on a.weixin_id=b.wx_openid order by a.order_joindate';
        $toall=$this->db->query($sqlnumber);
        if($toall->num_rows < 1){
            return '-1';
        }
        $data['number']=$toall->num_rows;
        $sql='select b.wx_name,b.wx_status,a.id,a.order_randid,a.order_mobile,a.order_province,
              a.order_city,a.order_county,a.order_address,a.order_num,a.order_pic,a.order_weight,
              a.order_joindate,a.order_status,a.order_type from '.$this->order.' as a left join '.$this->wxuser.' as b 
              on a.weixin_id=b.wx_openid order by a.order_joindate desc limit '.$data['page'].','.$data['per_page'];
        $data['list']=$this->db->customize_query($sql);
        if($data['list'] === false){
            return false;
        }else{            
            return $data;
        }
	}
	/**
	 * 功能描述:订单搜素
	 */
	function searchorder($option){
	    $searchsql='select b.wx_name,b.wx_status,a.id,a.order_randid,a.order_mobile,a.order_province,
              a.order_city,a.order_county,a.order_address,a.order_num,a.order_pic,a.order_weight,
              a.order_joindate,a.order_status,a.user_id from '.$this->order.' as a left join '.$this->wxuser.' as b 
              on a.weixin_id=b.wx_openid left join '.$this->admin.' as  c  on a.user_id=c.id where 
	          (a.order_randid="'.$option['keyword'].'" or c.name="'.$option['keyword'].'")';
	    $res_count=$this->db->query($searchsql);
	    if($res_count === false){
	        return false;
	    }else{
	        $data['sum']=$res_count->num_rows();
	        if(!$data['sum']){	            
	            return '-1';
	        }
	        $sql=$searchsql.' order by a.order_joindate desc limit '.$option['page'].','.$option['per_page'];;
	        $data['list']=$this->db->customize_query($sql);
	        if($data === false){
	            return false;
	        }else{
	            return $data;
	        }
	    }
	   
	    
	    
	}
	/**
     * 功能描述:获取成交订单  未成交订单总数
     */
	function orderstatistics_num(){
	    $sql='select count(id) as num,order_status from '.$this->order.' group by order_status';
	    $data=$this->db->customize_query($sql);
	    if($data === false){
	        return false;
	    }else{
	        return $data;
	    }
	}    
	/**
	 * 功能描述:查询订单提交记录每天
	 */
	function orderstatistics_day(){
	    $sql='select DATE_FORMAT(order_joindate,"%Y%m%d") days ,count(id) as num from h_order group by days';
	    $data=$this->db->customize_query($sql);
	    if($data === false){
	        return false;
	    }else{
	        return $data;
	    }
	}
	/**
	 * 功能描述:查询订单提交记录 没月
	 */
	function orderstatistics_months(){
	    $sql='select DATE_FORMAT(order_joindate,"%Y%m") months,count(id) num from h_order group by months';
	    $data=$this->db->customize_query($sql);
	    if($data === false){
	        return false;
	    }else{
	        return $data;
	    }
	}
	/**
	 * 功能描述:查询最近一周每天的订单数量
	 */
	function orderstatistics_week(){
	    $sql='select date(order_joindate) as joindate, count(id) as num  from '.$this->order.' where 
	          DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(order_joindate) GROUP BY joindate';
	    $data=$this->db->customize_query($sql);
	    if($data === false){
	        return false;
	    }else{
	        return $data;
	    }
	}	
	
	
}

?>