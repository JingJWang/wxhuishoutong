<?php
/*
 * 订单模块
 * funtion  save_cancelorder  订单取消
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Order_model extends CI_Model {
    //订单表(主表)
    private     $order_nonstandard ='h_order_nonstandard';
    //取消订单表
    private     $order_cancel ='h_order_cancel';
    //报价表
    private     $cooperator_offer='h_cooperator_offer';
    //初始化 db 乐类
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 订单模块-订单取消
     * @param      int       userid    用户id
     * @param      int       orderid   订单id
     * @param      int       content   取消订原因
     * @param      string    make      订单类型
     * @return     json                返回结果  
     */
    function  save_cancelorder(){        
        $where=array('order_number'=>$this->orderid);
        //记录取消信息
        $data=array('user_number'=>$this->userid,
                    'order_id'=>$this->orderid,
                    'cancel_remark'=>$this->make.','.$this->content,
                    'cancel_jointime'=>time(),
                    'cancel_status'=>1,
                    'cancel_cooperator'=>2
        );  
        //校验当前是否是预支付订单 如果是 执行退款操作
        if($this->prepay == 1){
            $coop_sql='select cooperator_number,offer_price from h_cooperator_offer
                     where order_id='.$this->orderid.' and offer_order_status =3';
            $coop_query=$this->db->query($coop_sql);
            if($coop_query->num_rows != 1){
                return false;
            }
            $offer=$coop_query->row_array();           
            $user_log=array(
                    'log_userid'=>$this->userid,
                    'log_total'=>$offer['offer_price']*100,
                    'log_title'=>'订单订单',
                    'log_content'=>'冻结余额减去当前取消订单的预支付金额'
            );
            $coop_log=array(
                    'log_userid'=>$offer['cooperator_number'],
                    'log_total'=>$offer['offer_price']*100,
                    'log_title'=>'订单取消',
                    'log_content'=>'用户取消订单,预支付金额返还'
            );
            $money=$offer['offer_price']*100;
            $user_money='update h_wxuser set wx_freeze_balance=wx_freeze_balance-'.
                         $money.' where wx_id='.$this->userid;
            $coop_money='update h_cooperator_money set money_balance=money_balance+'.
                         $offer['offer_price'].' where cooperator_number='.$offer['cooperator_number'];
        }
        //事物开始
        $this->db->trans_begin();
        $this->db->update($this->order_nonstandard,
                          array('order_orderstatus'=>'-1'),
                          $where,array());
        $rows_order=$this->db->affected_rows();
        $this->db->update($this->cooperator_offer,
                              array('offer_order_status'=>'-1'),
                              array('order_id'=>$this->orderid),
                              array());
        $rows_offer=$this->db->affected_rows();
        $this->db->insert($this->order_cancel,$data);
        $rows_cancel=$this->db->affected_rows(); 
        //但订单类型是预支时       
        if($this->prepay == 1){
            $this->db->query($user_money);
            $rows_user=$this->db->affected_rows();
            $this->db->query($coop_money);
            $rows_coop=$this->db->affected_rows();
            $this->db->insert('h_bill_log',$user_log);
            $rows_userlog=$this->db->affected_rows();
            $this->db->insert('h_bill_log',$coop_log);
            $rows_cooplog=$this->db->affected_rows();
        }
        //执行结果  
        if ($this->db->trans_status() === false){           
            $this->db->trans_rollback();
            return false;
        }else{
            if($this->prepay == 1){
                if($rows_order !=1 || $rows_cancel !=1
                        || $rows_user !=1 || $rows_coop !=1 || $rows_userlog !=1 || $rows_cooplog !=1 ){
							$expression=false;
						}else{
							$expression=true;
						}		
            }else{                
                if($rows_order !=1 || $rows_cancel !=1){
					$expression=false;
				}else{
					$expression=true;
				}
            }
           if($expression === false){
               $this->db->trans_rollback();
               return false;
           }            
           $this->db->trans_commit();
           if($this->prepay == 1){
                $this->coopnumber=$offer['cooperator_number'];
                $this->msg=1;
           }           
           $this->msg=0;
           return true;
        }        
    }
    function GetOrderStatus($check,$oid){
        $sql='select  order_orderstatus,wx_id,order_prepay from '.
              $this->order_nonstandard.' where order_number="'.$oid.'"';
        $query=$this->db->query($sql);
        if($query->num_rows == 0){
            return false;
        }        
        //校验订单是否属于当前用户
        $order=$query->row_array();
        if($order['wx_id'] !== $_SESSION['userinfo']['user_id']){
            return false;
        }          
        //校验当前订单的状态是否符合
        switch ($check){
            case 'cancel':
                     $this->prepay=$order['order_prepay'];
                     if($order['order_orderstatus'] == 10 || $order['order_orderstatus'] == -1){
                        return false;
                     }    
                     if($order['order_orderstatus'] == 3){
                        return false;
                     }           
                     return true;
                     break;
            case 'trading':
                     if($order['order_orderstatus'] == 10 ){
                         return true;
                     }
                     return false;
                    break;
            case 'pay':                    
                     if($order['order_orderstatus'] == 3){
                         $this->prepay=1;
                         return true;
                     }
                     if($order['order_orderstatus'] ==2){
                         $this->prepay=0;
                         return true;
                     }
                      return false;
                    break;
        }        
    }    
    /**
     * 获取成交的订单
     *//**
     * 订单列表-----查看待交易的订单
     * @param    int    option    订单状态
     * @param    data             返回查询订单数据
     * 
     * 订单状态(-2)订单未完成     (-1)订单被取消 
     *       (1) 在等待报价  
     *       (2) 确定报价         (3) 待交易状态  
     *       (4) 报价结束         (10)为已成交状态
     */
    function  GetOrderLsit(){ 
        switch ($this->sign){
            case 'all':
                $where=' and a.order_orderstatus != -1 and a.order_orderstatus != -2';
                break;
            case 'electron':
                $where=' and a.order_orderstatus != -1 
                         and a.order_orderstatus != -2 
                         and a.order_ctype in (5,6,7,8)';
                break;
            case 'metal':
                $where=' 
                         and a.order_orderstatus != -1
                         and a.order_orderstatus != -2
                         and a.order_ctype in (37,38,40)';;
                break;
            case 'deal':
                $where=' and (a.order_orderstatus = -1 or a.order_orderstatus = 10)';
                break;
        }
        $sql='select  
                 a.order_number as number,
                 a.order_id as id,
                 a.order_ctype as type,
                 a.order_evaluation as evaluation,
                 a.order_jointime as jointime,
                 a.order_name as name,
                 a.order_orderstatus as status ,
                 a.order_bid_price as price,
                 a.order_updatetime as uptime,
                 a.ordre_dealtime as dealtime,
                 c.cooperator_name as coopname,
                 c.cooperator_mobile as coopmobile,
                 d.cancel_remark as remark ,
                 d.cancel_reason as reason,
                 d.cancel_jointime as cantime 
                 from  
                 h_order_nonstandard as a left join  h_cooperator_offer
                 as b on a.order_number=b.order_id left join h_cooperator_info as c on
                 b.cooperator_number=c.cooperator_number left join h_order_cancel
                 as d  on a.order_number=d.order_id  
                 where wx_id="'.$this->userid.'"  and a.order_status = 1  '.$where.'
                 group by a.order_id
                 order by a.order_orderstatus,a.order_jointime desc';
            $query=$this->db->query($sql);
            if($query->num_rows() < 1){
                return  false;
            }
            $data=$query->result_array();
            $httpurl='/index.php/nonstandard/wxuser/ViewEvaluation?oid=';
            foreach ($data as $k=>$v){ 
                $resp[$k]['name']=$v['name'];
                (($resp[$k]['type']=$type=$v['type'] == 5) || ($resp[$k]['type']=$type=$v['type'] == 7)) ?  1 : 0 ;
                $resp[$k]['jointime']=date('Y-m-d H:i:s',$v['jointime']);
                $info=$this->orderStatus($v['status'], $type,$v['number']);
                $temp=array_merge($resp[$k],$info);
                $resp[$k]=$temp;
            }
             return $resp;
       
    }
    function getOrderUrl($option){
        $resp=array();
        //状态
        $status=array('1'=>'报价中','2'=>'等待预支付','3'=>'待交易',
                      '4'=>'报价结束','10'=>"已成交",'-1'=>'已取消','-2'=>'未完成');
        $resp['status']=$status[$option['status']];
        switch ($option['info']){
             //贵金属
             case '0':
                 $urlInfo='/view/gold/metalinfo.html?id='.$option['id'];
                 break;
             //电子产品
             case '1':
                 $urlInfo='/index.php/nonstandard/order/ViewOrderInfo?id='.$option['id'];
                 break;
        }
        $resp['info']=$urlInfo;
        //操作
        if($option['info'] == 1){
            if($option['permit'] == 1){
                $permit='/index.php/nonstandard/quote/ViewQuote?id='.$option['id'];
            }else{
                $permit='';
            }
        }else{
                $permit='';
        }
        $resp['permit']=$permit;
        return $resp;
    }
    /**
     * 订单状态
     * 
     */
    function orderStatus($sign,$type,$id) {
        $data=array();
        switch ($sign){
            case '1':
                    $data=$this->getOrderUrl(array('status'=>1,'info'=>$type,'permit'=>1,'id'=>$id));
                    break;
            case '2':
                    $data=$this->getOrderUrl(array('status'=>2,'info'=>$type,'permit'=>0,'id'=>$id));
                    break;
            case '3':
                    $data=$this->getOrderUrl(array('status'=>3,'info'=>$type,'permit'=>0,'id'=>$id));
                    break;
            case '4':
                    $data=$this->getOrderUrl(array('status'=>4,'info'=>$type,'permit'=>1,'id'=>$id));
                    break;
            case '10':
                    $data=$this->getOrderUrl(array('status'=>10,'info'=>$type,'permit'=>0,'id'=>$id));
                    break;
            case '-1':
                    $data=$this->getOrderUrl(array('status'=>-1,'info'=>$type,'permit'=>0,'id'=>$id));
                    break;
            case '-2':
                   $data=array('status'=>0,'info'=>0,'permit'=>0);
                    break;
        }
        return $data;
    }
    /**
     * 我的订单---删除订单
     * @param int  number   订单编号
     */
    function DelOrder($number){
        $userid=$_SESSION['userinfo']['user_id'];
        $this->db->trans_begin();
        $this->db->update('h_order_nonstandard',array('order_status'=>'-1'),
                array('wx_id'=>$userid,'order_number'=>$number));
        $this->db->update('h_order_content',array('electronic_status'=>'-1'),
                array('order_id'=>$number));
        //执行结果
        if ($this->db->trans_status() === false){
            //不成功
            $this->db->trans_rollback();
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('del_order_fall'));
            echo json_encode($response);exit;
        }else{
            //成功
            $this->db->trans_commit();
        }
        $response=array('status'=>$this->config->item('request_succ'),
                'msg'=>$this->lang->line('del_order_succ'),
                'url'=>'','data'=>'');
        echo json_encode($response);exit; 
    }
    /**
     * 校验订单状态
     * @param   int   orderid　　订单id
     * @return  成功返回bool | true ,失败返回bool | false
     */
    function  checkOrder($orderid){
        if(empty($orderid) || !is_numeric($orderid)){
            return false;
        }
        $sql='select order_id,order_ctype from h_order_nonstandard where order_orderstatus=1
              and order_number='.$orderid;
        $query=$this->db->query($sql);
        if(!$query){
            return  false;
        }
        if($query->num_rows() < 1){
            return  false;
        }
        $data=$query->row_array();
        return $data;        
    }
    /**
     * 查询订单详细信息
     * @param   int    orderid   订单id
     * @param   int    userid    用户id
     * @return  获取订单数据成功时 返回 array 订单信息| 失败返回bool  false
     */
    function  GetOrderInfo(){
        //查询订单详情
        $sql='select a.order_ctype,a.order_number,a.order_name,a.order_province,
              a.order_city,a.order_county,a.order_residential_quarters,a.order_jointime as jointime,
              a.order_orderstatus,b.electronic_oather,b.electronic_img from h_order_nonstandard
              as a left join  h_order_content as b  on a.order_number=b.order_id
              where a.order_number='.$this->orderid.' and  a.wx_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query->num_rows() == 0 ){
            return false;
        }
        $data['order']=$query->row_array();
        $data['quote']='';
        //当订单状态为等待报价时
        if($data['order']['order_orderstatus'] == 1){
            $quotesql='select offer_id as id,offer_coop_name as name,offer_price 
                        as price,cooperator_number as number from
                       h_cooperator_offer where order_id="'.$this->orderid.'"';
            $result=$this->db->query($quotesql);
            if($result->num_rows < 1){
                $data['quote']=0;
            }
            $data['quote']=$result->result_array();
            $data['order']['order_residential_quarters']='';
        }
        if($data['order']['order_orderstatus'] == 2 || $data['order']['order_orderstatus'] == 3){
            $sql='select a.offer_id as id,a.offer_coop_name as name,
                  a.offer_price as price ,a.offer_money as moeny,a.cooperator_number as number,
                  b.cooperator_mobile as mobile,a.offer_second as second ,
                  a.offer_isagree as isagree,b.cooperator_address as address,
                  b.cooperator_auth_type as auth,b.cooperator_wx as wx from  h_cooperator_offer a 
                  left join h_cooperator_info b  on a.cooperator_number=
                  b.cooperator_number where a.order_id="'.$this->orderid
                  .'" and a.offer_order_status='.$data['order']['order_orderstatus'];
            $result=$this->db->query($sql);
            if($result->num_rows < 1){
                $data['offer']=0;
            }
            $data['offer']=$result->result_array();
        }
        /* //当订单处于待交易的状态时
        if($data['order']['order_orderstatus'] == 3){
            $quotesql='select a.offer_id as id,a.offer_coop_name as name,
                       a.offer_price as price ,a.offer_money as moeny,
                       b.cooperator_mobile as mobile,a.offer_second as second ,
                       a.offer_isagree as isagree from  h_cooperator_offer a 
                       left join h_cooperator_info b  on a.cooperator_number
                       =b.cooperator_number where a.order_id="'.$this->orderid
                       .'" and a.offer_order_status=3';
            $result=$this->db->query($quotesql);
            if($result->num_rows < 1){
                $data['quote']=0;
            }
            $data['quote']=$result->result_array();
        } */
        $attrkey=$this->config->item('electronic_attribute_key');
        $data['attrname']=$attrkey[$data['order']['order_ctype']];
        $data['attrname']['proname']='分类';
        $data['attrname']['braname']='品牌';
        $data['attrname']['typename']='型号';
        return $data;
    }
    /**
     * 用户确认回收商的第二次报价
     * @param     int   id   订单id
     * @return    成功返回  bool true | 失败 返回false
     */
    function ConfirmQuote(){
        $sql='select order_id from  h_order_nonstandard where order_number="'.
              $this->orderid.'" and  wx_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query->num_rows < 1 ){
            return  false;
        }
        $data=array('offer_isagree'=>1,'offer_update_time'=>time());
        $where=array('order_id'=>$this->orderid,'offer_order_status'=>3);
        $query=$this->db->update('h_cooperator_offer',$data,$where);
        $affected=$this->db->affected_rows();
        if($query === true && $affected == 1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 根据订单订单编号查询当前与用户交易的回收商编号
     * @param    int   id   订单编号
     * @return   查询成功 int  回收商编号 | 查询失败  返回bool  false
     */
    function  getCoopNumber(){
        $sql='select cooperator_number from h_cooperator_offer  where order_id='.
              $this->orderid.' and offer_order_status=3';
        $query=$this->db->query($sql);
        if($query->num_rows < 1 || $query === false){
            return  false;
        }
        $info=$query->result_array();
        return $info['0']['cooperator_number'];
    }
    /**
     * 获取最新的订单动态
     * @return  成功返回 array 订单数据 | 失败返回 false
     */
    function orderDynamic(){
        $sql='SELECT a.wx_id,a.order_name,a.order_bid_price,b.wx_name,b.wx_mobile,a.ordre_dealtime,
              c.comment_reason FROM h_order_nonstandard AS a LEFT JOIN h_wxuser AS 
              b ON a.wx_id = b.wx_id LEFT JOIN h_wxuser_comment AS c ON a.order_number
              = c.order_id WHERE a.order_orderstatus = 10  order by  a.ordre_dealtime desc LIMIT 0, 5 ';
        $result=$this->db->query($sql);
        if($result->num_rows <  1 || $result === false){
            return false;
        }
        $data=$result->result_array();
        return $data;
    }
    /**
     * 订单评价
     * @param  int     orderid   订单id
     * @param  int     type      类型
     * @param  float   fraction  评价分数
     * @param  string  reason    评价内容
     * @param  string  make      用户评语
     * @return 成功返回 bool true | 失败 返回bool false
     */
    function orderReason(){
        //校验订单是否存在
        $sql='select order_number,cooperator_number,order_orderstatus,order_evaluation
              from h_order_nonstandard  where order_number='.$this->orderid.'
              and wx_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query->num_rows() == 0){
            return false;
        }
        //校验当前订单状态是否正常
        $order=$query->row_array();
        if ($order['order_orderstatus'] != 10 && $order['order_orderstatus'] != -1 ){
            return false;
        }
        if($order['order_evaluation'] == 1){
            return  false;
        }
        //获取订单关联的回收商
        $sql_check='select cooperator_number from h_cooperator_offer where
                    order_id="'.$this->orderid.'" and  (offer_order_status = -1
                    or offer_order_status=4)';
        $coop_res=$this->db->query($sql_check);
        $coop=$coop_res->row_array();
        //记录取消原因
        $data=array('cooperator_number'=>$coop['cooperator_number'],
                'order_id'=>$this->orderid,'wx_id'=>$this->userid,
                'comment_reason'=>$this->reason,'comment_score'=>$this->fraction,
                'comment_remark'=>$this->make,'comment_jointime'=>time(),
                'comment_type'=>$this->type,'comment_status'=>1);
        $res=$this->db->insert('h_wxuser_comment',$data);
        $add=$this->db->affected_rows();
        $query=$this->db->update('h_order_nonstandard',array('order_evaluation'=>1),
                array('order_number'=>$this->orderid));
        $up=$this->db->affected_rows();
        if($res === false || $query === false || $add !=1 || $up !=1 ){
           return false;
        }
        return true;
    }
    /**
     * 根据订单编号查询订单品牌信息
     * @param  int   number  订单编号
     * @return  查询成功返回array | 查询失败返回bool false
     */
    function getProInfo(){
        $sql='select  a.types_id,c.brand_name,b.types_name from h_order_nonstandard as a 
               left join h_electronic_types as b  on a.types_id=b.types_id
              left join  h_brand as c on b.brand_id=c.brand_id where 
                a.order_number="'.$this->number.'"';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return 0;
        }
        $data=$query->result_array();
        return $data;        
    }
    /**
     * 根据订单编号查询订单品牌信息
     * @param  int   number  订单编号
     * @return  查询成功返回array | 查询失败返回bool false
     */
    function getProInfoAttr(){
        $sql='select electronic_oather as attr from h_order_content  where
                order_id="'.$this->number.'"';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->result_array();
        return $data;
    }
}