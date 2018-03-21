<?php
/*
 * 数码订单model
 */
class digitaorder_model extends CI_Model{
    
    //加载db
    function __construct() {
        parent::__construct();
        $this->load->database();
    } 
    /**
     * 获取订单列表
     * @param   int       status  订单状态
     * @param   string    data  日期
     * @return  array 列表正确获取 返回结果 | 获取失败返回 bool false
     */
    function orderList(){
        //当前页
        $start=$this->page == 1 ? 0 : ($this->page-1)*$this->num;
        $where='';        
        if(empty($this->userid)){
            $where = '';
        }else{
            $where = ' wx_id ='.$this->userid.' and ';
        } 
        //订单状态
        switch ($this->status){
            case '-1':
                $where .= ' order_orderstatus = -1 and ';
                break;
            case '-2':
                $where .= ' order_orderstatus = -2 and ';
                break;
            case '1':
                $where .= ' order_orderstatus = 1 and ';
                break;
            case '2':
                $where .= ' order_orderstatus =  2 and ';
                break;
            case '3':
                $where .= ' order_orderstatus = 3 and ';
                break;
            case '4':
                $where .= ' order_orderstatus = 4 and ';
                break;
            case '10':
                $where .= ' order_orderstatus = 10 and ';
                break;
            case 'all':
                $where .= ' ';
                break;
        }
        //时间检索条件  起始日期
        if(!empty($this->start) && !empty($this->end)){
            $where .= ' order_jointime > '.$this->start.
                      ' and order_jointime < '.$this->end;
        }else{
            //如果有时间检索条件
            switch ($this->time){
                case 'today':
                    $where .= ' to_days(order_jointime)=to_days(now()) and ';
                    break;
                case 'yesterday':
                    $where .= ' TO_DAYS(NOW()) – TO_DAYS(order_jointime) = 1 and ';
                    break;
                case 'week':
                    $where .= ' DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= date(order_jointime) and ';
                    break;
                case 'month':
                    $where .= ' DATE_FORMAT(order_jointime, "%Y%m" = DATE_FORMAT(CURDATE(), "%Y%m") and ';
                    break;
                case 'all';
                    $where .= ' ';
            }
        }
        //当检索的关键词是订单编号
        if(isset($this->number) && !empty($this->number)){
            $where .= ' order_number like "%'.$this->number.'%"  and';
        }else{
            $where .= ' ';
        }
        $sql='select order_id as id,order_name as name,order_number as
              number,order_province as province ,order_city as city,order_ctype as ctype,
              wx_id as userid,order_residential_quarters as quarters,
              order_orderstatus as orderstatus,order_jointime as jointime,
              order_county as county from h_order_nonstandard  where '.$where.'
              order_status=1 order by order_jointime desc';        
        $query=$this->db->query($sql);
        $this->db->close();
        if(!$query || $query->num_rows < 1){
            return false;
        }
        $data['total']=ceil($query->num_rows/$this->num);
        $data['list']=$query->result_array();           
        $data['list']=array_slice($data['list'],$start,$this->num);
        return $data;
    }
    /**
     * 根据手机号码搜索用户
     * @param   int   keyword 关键词
     * @return  array 存在返回查询结果 | 不存在 返回bool false
     */
    function searchUser(){
        $sql='select wx_id as id from h_wxuser where wx_mobile='.$this->keyword;
        $query=$this->db->query($sql);
        $this->db->close();
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $data=$query->result_array();
        return $data;
    }
    /**
     * 根据id 查询订单详情
     * @param  int   id　订单id
     * @param  array  成功获取订单详情  |bool 获取订单详情失败  
     */
    function orderInfo(){
        $sql='select order_name as name,order_mobile as mobile,order_number as number,
              order_province as province,order_city as city,order_county as county,
              order_residential_quarters as quarters,order_jointime as jointime,
              a.order_orderstatus as status,a.types_id as typeid,order_ctype as ctype,
              order_ftype as ftype,b.electronic_mobile as phone,
              b.electronic_oather as oather,a.order_bid_price as price from  h_order_nonstandard as a left join 
              h_order_content as b on a.order_number=b.order_id where a.order_id='.
              $this->id;
        $query=$this->db->query($sql);
        $this->db->close();
        if($query === false ||$query->num_rows <1 ){
            return false;
        }
        $data['order']=$query->result_array();
        $oather=$data['order']['0']['oather'];
        $data['order']['0']['oather']=json_decode($oather,true);
        //获取报价信息
        $data['offer']=$this->coopInfo($data['order']['0']['status'],$data['order']['0']['number']);
        //获取属性信息 当订单不是自动报价的时候
        if(empty($data['order']['0']['typeid'])){
            if($data['order']['0']['ftype'] == 4){
                $option=$this->config->item('electronic_attribute_key');
                $data['attr']=$option['11'];
                $attr = true;
            }else{
                $option=$this->config->item('electronic_attribute_key');
                $data['attr']= $data['order']['0']['ctype'] ==10 ? $option['10'] :$option['5'];
                $data['attr']['typename'] = '产品类型';
                $data['attr']['proname']  = '产品名称';
                $data['attr']['braname']  = '品牌名称';
                $attr = true;
            }
        }else{            
            $attr=$this->attrInfo($data['order']['0']['typeid']);
            //获取订单属性
            $data['attr']=$this->beautifyAttr($data['order']['0']['oather'],$attr);
        }
        if($data['offer'] === false ||  $attr === false){
            return false;
        }
        return $data;
    }
    /**
     * 根据状态返回回收商信息
     * @param   int   status  订单状态
     * @return  array  返回回收商信息
     */
    function coopInfo($status,$number){
        //订单未提交
        if($status == -2 ){
            return '';
        }
        $where = 'order_id="'.$number.'"  ';
        //订单 取消 报价中 订单超时  查询所有的报价
        if($status == 1 || $status == -1 || $status == 4){            
            $where .=' ';
        }
        //等待预支付  待交易  已成交
        if($status == 2 || $status == 3 || $status == 10){
            $where .=' and offer_status = 1 and offer_order_status='.$status;
        }
        $sql='select offer_join_time as jointime,offer_coop_name as name,
              cooperator_number as number,offer_price as price,offer_second as second from  h_cooperator_offer  
              where '.$where;
        $query=$this->db->query($sql);
        $this->db->close();
        if($query->num_rows < 1){
            return '';
        }
        $data=$query->result_array();    
        if(!empty($data['0']['second'])){
            $data['0']['price']=$data['0']['second'];
            unset($data['0']['second']);
        }
        return $data;        
    }
    /**
     * 获取订单属性信息
     * @param   int   typeid  型号id
     * @return  array  返回属性信息
     */
    function attrInfo(){
        //读取缓存信息
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->number=5;
        $this->zredis->selectDB();
        $this->zredis->key='common_order_option';
        $cache=$this->zredis->existsKey();
        if($cache != false){
            $response=json_decode($cache,true);
             return $response;
        }
        //获取当前参数配置信息
        $sql='select  info_id as id,model_id as fid,info_info as info 
              from h_option_info where info_status=1';
        $query=$this->db->query($sql);
        if($query == false && $query->num_rows < 1){
            return false;
        }
        $info=$query->result_array();
        $sql='select  model_id as id,model_name as name,model_alias as alias 
              from h_option_model where model_status=1';
        $query=$this->db->query($sql);
        if($query == false && $query->num_rows < 1){
            return false;
        }
        $model=$query->result_array();
        $response=array('model'=>$model,'info'=>$info);
        //写入缓存
        $this->zredis->number=5;
        $this->zredis->selectDB();
        $this->zredis->key='common_order_option';
        $cache=$this->zredis->existsKey();
        if($cache !== false){            
            return $response;
        }else{
            $cache=json_encode($response);
            $this->zredis->val=$cache;
            $this->zredis->setkey();
            return $response;
        }
    }
    /**
     * 重新组成订单属性详细信息
     * @param   string    attr    订单保存的详细信息
     * @param   array     option  属性信息
     * @return  json    返回订单属性信息
     */
    function  beautifyAttr($ordeinfo,$attr){
        $model=array();
        foreach ($attr['model'] as $k=>$v){
            $model[$v['alias']]=$v['name'];
        }
        return $model;        
    }
    /**
     * 根据订单id 获取订单信息
     * @param    int    orderid  订单id
     * @return   array  返回订单信息 | 失败 bool 返回false
     */
    function getOrderInfo(){
        $sql='select wx_id,order_id,order_name,order_number,order_dealtype from 
                h_order_nonstandard  where order_id='.
              $this->orderid.' and order_status=1';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $data=$query->result_array();
        return $data;        
    }
    /**
     * 根据订单编号获取 报价信息
     * @param   int   id  orderid 订单编号
     * @return  array  查询成功 | bool  获取失败
     */
    function getOrderOffer(){
        $sql='select offer_price as price,offer_id as offerid from 
              h_cooperator_offer where  order_id='.$this->number.'
                and offer_order_status=2 and offer_status=1';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $offer=$query->result_array();
        return $offer;
    }
    /**
     * 根据用户id 获取用户的状态
     * @param   int  userid 用户id
     * @return  array 获取成功返回 | 失败返回 bool
     */
    function getUser(){
        $sql='select wx_name,wx_mobile,wx_openid from h_wxuser where wx_id='.
              $this->userid;
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $user=$query->result_array();
        return $user;
    }
    /**
     * 根据订单编号 对订单进行预支付操作
     * @param   int   orderid  订单id
     * @param   int   price    预支付金额
     * @param   int   offerid  报价id
     * @param   int   userid   用户id
     * @return  bool  修改成功 true |失败返回false
     */
    function prePayment(){
        //修改订单状态
        $order_data=array(
          'order_orderstatus'=>3,
          'order_updatetime'=>time(),
          'order_prepay'=>1,
        );
        $order_where=array(
           'order_id'=>$this->orderid
        );
        //修改报价状态
        $offer_data=array(
           'offer_order_status' => 3,
           'offer_update_time' => time(),
        );
        $offer_where=array(
            'offer_id'=>$this->offerid
        );
        //修改用户冻结资金
        $sql='update h_wxuser set wx_freeze_balance=wx_freeze_balance+'.
              $this->price.' where wx_id='.$this->userid;
        // 开启事物
        $this->db->trans_begin();
        $this->db->update('h_order_nonstandard',$order_data,$order_where);
        $row_order = $this->db->affected_rows();
        $this->db->update('h_cooperator_offer',$offer_data,$offer_where);
        $row_offer = $this->db->affected_rows();
        $this->db->query($sql);
        $row_user = $this->db->affected_rows();        
        if ($this->db->trans_status() === FALSE || $row_order!=1 ||$row_offer !=1
               || $row_user !=1 ){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * 修改报价
     */
    function upQuote(){
         //读取订单用户id
         $sql='select wx_id from h_order_nonstandard where order_number='.$this->number;
         $user_query=$this->db->query($sql);
         if($user_query->num_rows != 1){
             return  false;
         }
         $user=$user_query->result_array();
         $moeny_sql='select wx_freeze_balance from h_wxuser where wx_id='.$user['0']['wx_id'];
         $moeny_query=$this->db->query($moeny_sql);
         if($moeny_query->num_rows != 1){
             return  false;
         }
         $moeny=$moeny_query->result_array();
         //修改报价中的第二次报价
         $data=array(
                 'offer_second'=>$this->upprice,
                 'offer_update_time'=>time()
         );
         $where=array(
               'order_id'=>$this->number,
               'offer_order_status'=>3,
               'offer_status'=>1
         );
         $user_data=array(
                'wx_freeze_balance'=>$moeny['0']['wx_freeze_balance']-$this->price*100+$this->upprice*100
         );
         $user_where=array(
                 'wx_id'=>$user['0']['wx_id']
         );
        $this->db->trans_begin();
        $this->db->update('h_cooperator_offer',$data,$where);
        $row_order = $this->db->affected_rows();
        $this->db->update('h_wxuser',$user_data,$user_where);
        $row_user = $this->db->affected_rows();
        if ($this->db->trans_status() === FALSE || $row_order!=1 ||$row_user !=1){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
        
    }
    /**
     * 订单支付
     * 读取订单信息 校验订单状态是否正常
     */
    function orderstatus(){
        $sql='select wx_id,order_orderstatus,order_ctype,order_number as number,order_invitation as invi,
			  order_bid_price as dprice,order_dealtype as dealtype from h_order_nonstandard
              where order_id='.$this->id;
        $query=$this->db->query($sql);
        if($query->num_rows != 1){
            return false;
        }
        $info=$query->result_array();
        $result=$info[0];
        if($result['order_orderstatus'] != 3){
            return false;
        }
		return $result;
    }
    /**
     * 订单支付
     * 读取报价信息 获取最后报价
     */
    function orderquote(){        
        $sql='select offer_isagree,offer_price,offer_second,offer_order_status 
              from  h_cooperator_offer where order_id='.$this->number.' and offer_status=1';
        $query=$this->db->query($sql);
        if($query->num_rows != 1){
            return false;
        }
        $info=$query->result_array();
        if($info['0']['offer_order_status'] != 3){
            return false;
        }
        /* if( !empty($info['0']['offer_second']) ){
            if($info['0']['offer_isagree'] != 1){
                    return false;
            }
        } */
        if(empty($info['0']['offer_second'])){
            return $info['0']['offer_price'];
        }else{
            return $info['0']['offer_second'];
        }
    }
    /**
     * 订单支付
     * 读取用户的记录 校验冻结   同时冻结转入余额  更改订单状态
     */
    function orderpay(){
        //校验订单    
        $respay='select order_dealtype,order_ftype,order_id,order_orderstatus,order_invitation as invi,
                 order_ctype from h_order_nonstandard where order_number='.$this->number;
        $orderquery=$this->db->query($respay);
        if($orderquery->num_rows != 1){
            return false;
        }
        $order=$orderquery->result_array();
        if($order['0']['order_orderstatus'] != 3){
            return  false;        }     
        //查询用户信息   
        $sql='select wx_freeze_balance,wx_balance from h_wxuser where wx_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query->num_rows != 1){
            return false;
        }
        $user=$query->result_array();
        if($user['0']['wx_freeze_balance'] < $this->moeny){           
            return false;
        }
        //读取用户贵金属库存内容
        $metal='select metal_gold,metal_platinum,metal_silver from
                h_wxuser_metal where wx_id='.$this->userid;
        $metalquery=$this->db->query($metal);
        if($metalquery === false){
            return false;
        }
        $metalinfo=$metalquery->row_array();
        //当为贵金属订单 且 交易方式 为库存 读取交易数据 重量信息
        if($order['0']['order_ftype'] == 4 &&  $order['0']['order_dealtype'] == 1){
            $weight=$this->metalInfo($this->number);
            if($weight == false){
                return false;
			}else{
            	$dealinfo['weight']=$weight;
            	$dealinfo['upri']=$this->moeny/$weight;
            }
        }
        //校验  当订单属于批量回收 并且 成交金额大于9000时候 冻结余额 不在转入余额 有线下支付
        if($order['0']['order_ctype'] == 10 && $this->moeny >10000){
            $data=array(
                    'wx_freeze_balance'=>$user['0']['wx_freeze_balance']-$this->moeny*100,
            );
        }else{   
             //当为贵金属订单 且 交易方式 为库存 修改冻结余额 且 不增余额
            if($order['0']['order_ftype'] == 4 &&  $order['0']['order_dealtype'] == 1){
                 $data=array(
                    'wx_freeze_balance'=>$user['0']['wx_freeze_balance']-$this->moeny*100,
                 );
            }else{
                //冻结转入余额
                $data=array(
                        'wx_freeze_balance'=>$user['0']['wx_freeze_balance']-$this->moeny*100,
                        'wx_balance'=>$user['0']['wx_balance']+$this->moeny*100+$this->vouchpri*100
                );
            }
        }
        //总价格
        $dealinfo['total']=$this->moeny;
	    switch($order['0']['order_ctype']){
			  //黄金
                case '37': 
					$dealinfo['type'] = '黄金';
                    $record_type=1;
                    break;
                //铂金
                case '38':
					 $dealinfo['type'] = '铂金';
                   $record_type=2;
                    break;
                //白银
                case '40':
					$dealinfo['type'] = '白银';
                   $record_type=3;
                    break;
		}
		//卖出记录
		if(isset($record_type) && !empty($record_type)){
			$deallog=array(
				   'wx_id'=>$this->userid,
				   'record_type'=>1,
				   'record_dealtype'=>1,
				   'record_content'=>json_encode($dealinfo),
				   'record_jointime'=>time(),
				   'record_status'=>1
			);
		}
        $this->db->trans_begin();
        //修改用户信息
        $this->db->update('h_wxuser',$data,array('wx_id'=>$this->userid));
        $up_user=$this->db->affected_rows();
        //修改订单信息
        $this->db->update(
                'h_order_nonstandard',
                array(
                      'ordre_dealtime'=>time(),
                      'order_updatetime'=>time(),
                      'order_orderstatus'=>10,
                      'order_bid_price'=>$this->moeny+$this->vouchpri
                ),
                array('order_number'=>$this->orderid)
              );
        $up_order=$this->db->affected_rows();
		//订单支付成功后 判断邀请者是否是会员 如果是会员  依照相应的奖金设置保存到奖金记录表中
		$bonus_num=$this->bonusSave($order['0']['invi']);
        //修改报价信息
        $this->db->update(
                'h_cooperator_offer',
                 array(
                        'offer_order_status'=>4,
                        'offer_update_time'=>time()
                 ),
                 array(
                         'order_id'=>$this->number,
                         'offer_order_status'=>3,
                         'offer_status'=>1
                 )
                );
        $up_offer=$this->db->affected_rows(); 
        //存在现金券修改现金券      
        if(!empty($this->vouchpri)){
            $this->db->update('h_coupon_user',
                    array('coupon_status'=>1,'coupon_uptime'=>time()),
                    array('coupon_id'=>$this->vouchid)
                    );
            $up_vouch=$this->db->affected_rows();
        }else{
            $up_vouch = 1;
        }
        if($order['0']['order_ftype'] == 4 &&  $order['0']['order_dealtype'] == 1){
            $data=array('metal_uptime'=>time());
            switch ($order['0']['order_ctype']){
                //黄金
                case '37': 
                    $data['metal_gold']=$metalinfo['metal_gold']+$weight;
                    break;
                //铂金
                case '38':
                    $data['metal_platinum']=$metalinfo['metal_platinum']+$weight;
                    break;
                //白银
                case '40':
                    $data['metal_silver']=$metalinfo['metal_silver']+$weight;
                    break;
            }
            $this->db->update('h_wxuser_metal',$data,array('wx_id'=>$this->userid));
            $up_metal=$this->db->affected_rows();
        }else{
            $up_metal=1;
        }
		if($order['0']['order_ctype']=='37' || $order['0']['order_ctype']=='38' || $order['0']['order_ctype']=='40'){
        	$this->db->insert('h_metal_record',$deallog);
        	$add_record=$this->db->affected_rows();
        }else{
        	$add_record=1;
        }
        if ($this->db->trans_status() === FALSE || $up_user!=1 ||$up_order !=1
                || $up_offer !=1 || $up_vouch != 1 || $up_metal !=1 || $add_record != 1 || $bonus_num!=1){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * 当为贵金属交易 并且交易方式为库存交易 读取订单贵金属重量
     */
    function metalInfo($id){
        $sql='select electronic_oather from h_order_content where order_id='.$id;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        $info=json_decode($data['electronic_oather'],true);
        $weight=$info['weight'];
        return $weight;
    }
    /**
     * 根据ID获取订单内容
     * @param  int  id　订单ID
     * @return  array  返回订单内容 | 返回false
     */
    function ordercont($id){
        $sql='select wx_id as userid,order_number as number,order_name as name,
              order_ctype as type from  h_order_nonstandard where  order_id='.$id;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        return $data;
    }
    /**
     * 根据用户ID读取用户信息
     * @param int  id  用户id
     * @return   array  返回用户信息 | 返回false
     */
    function userinfo($id){
        $sql='select wx_freeze_balance as freeze,wx_mobile as mobile
              from h_wxuser where wx_id='.$id;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        return $data;
    }
    /**
     * 根据订单ID获取报价内容
     * @param  int  id  订单id
     * @return  array  返回报价信息 | 返回false
     */
    function  offerinfo($id){
        $sql='select offer_id as offerid ,offer_price as price,offer_second 
              as second from h_cooperator_offer where
              order_id='.$id.' and offer_order_status=3 and offer_status=1';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        $resp['offerid']=$data['offerid'];
        $resp['price']=empty($data['second']) ? $data['price'] : $data['second'];
        return  $resp;
    }
    /**
     *  根据订单ID取消订单
     *  @param  int  id  订单id
     *  @param  bool 订单取消成功返回true | bool 订单取消失败返回false
     */
    function ordercall($option){
        $orderdata=array(
               'order_updatetime'=>time(),
               'order_orderstatus'=>-1,
               //'order_status'=>-1
        );
        $orderwhere=array('order_number'=>$option['number']);
        $offerdata=array(
                'offer_update_time'=>time(),
                'offer_status'=>-1
        );
        $offerwhere=array(
                'offer_id'=>$option['offerid']
        );
        $userdata=array(
                'wx_freeze_balance'=>$option['freeze']-$option['price']*100,
                'wx_updatetime'=>date('Y-m-d H:i:s')
        );
       
        $userwhere=array(
                'wx_id'=>$option['userid']
        );
        $callinfo=array(
                'user_number'=>$_SESSION['user']['id'],
                'order_id'=>$option['number'],
                'cancel_remark'=>$option['content'],
                'cancel_cooperator'=>2,
                'cancel_jointime'=>time(),
                'cancel_status'=>1
        );
        //修改订单  报价 状态   修改用户余额 冻结金额
        $this->db->trans_begin();
        $this->db->update('h_order_nonstandard',$orderdata,$orderwhere);
        $res_order=$this->db->affected_rows();
        $this->db->update('h_cooperator_offer',$offerdata,$offerwhere);
        $res_offer=$this->db->affected_rows();        
        $this->db->update('h_wxuser',$userdata,$userwhere);        
        $res_user=$this->db->affected_rows();
        $this->db->insert('h_order_cancel',$callinfo);
        $res_call=$this->db->affected_rows();
        if ($this->db->trans_status() === FALSE || $res_order != 1 ||$res_offer != 1
                || $res_user != 1 || $res_call !=1 ){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * 获取订单需要支付的金额以及用户当前的代金券
     */
    function getVouchers($id){
        $this->orderid=$id;
        $order=$this->getOrderInfo($id);
        if($order === false){
            return false;
        }
        $price=$this->offerinfo($order['0']['order_number']);
        if($price === false){
            return false;
        }
        $data=$this->vouchersInfo($order['0']['wx_id']);
        if($data === false){
            $this->price=$price;
            return false;
        }
        $response=array('price'=>$price,'vouche'=>$data,'order'=>$order);
        return $response;
    }
    /**
     * 获取现金劵列表
     * @param  int id 用户id
     * @return array 返回代金券列表集合 | false
     */
    function vouchersInfo($id){
       $this->userid=$id;
       $info=$this->getUser();
       if($info === false){
            return false;
       }
       $where = "WHERE a.info_id = b.info_id ";
       $where.= "and b.coupon_mobile  = ".$info['0']['wx_mobile'].' and b.coupon_status=0';
       //sql语句查看
       $sql="SELECT a.class_id as classid,a.info_name as name,a.info_amount
 		       as amount,a.info_range as ranges,a.info_number as number,
 		       a.info_contime as contime,a.info_jointime as jointime,
 		       b.coupon_mobile as mobile,b.coupon_status as statu,b.coupon_id as id
 			   FROM h_coupon_info AS a ,h_coupon_user AS b ".$where;
       $query=$this->db->query($sql);
       if ($query->num_rows< 1) {
        	return false;
       }
       $data=$query->result_array();
       return $data;
    }
    /**
     * 获取现金劵的详情
     * @param  int  id 现金劵id
     * @return array 现金劵详情 | false
     */
    function vouchinfo($id){
        $sql='select a.coupon_id,a.coupon_jointime,b.info_amount,b.info_range,
              b.info_contime from h_coupon_user as a,h_coupon_info as b 
               where a.info_id = b.info_id and a.coupon_id='.$id.' and a.coupon_status=0';
        $query=$this->db->query($sql);
        if ($query->num_rows< 0) {
            return false;
        }
        $data=$query->row_array();
        $endtime=$data['coupon_jointime']+$data['info_contime'];
        if(time() > $endtime){
            $this->upvouch($id, '-1');
            return false;
        }
        return $data;
    }
    /**
     * 修改现金劵的状态
     * @param  int  id 现金劵的状态
     * @return bool true |false
     */
    function upvouch($id,$status){
        $res=$this->db->update('h_coupon_user',
                array('coupon_status'=>$status,'coupon_uptime'=>time()),
                array('coupon_id'=>$id));
        return $res;
    }
    /**
     * 校验订单是否是批量回收订单
     * 
     */
    function info(){
        $sql='select order_number as number from h_order_nonstandard 
              where order_id='.$this->id.' and order_ctype=10';
        $query=$this->db->query($sql);
        if($query->num_rows > 1){
            return false;
        }
        $result=$query->row_array();
        return $result;
    }
    /**
     * 获取批量回收订单的内容
     */
    function batchinfo(){
        $sql='select  electronic_oather as content from h_order_content
              where order_id='.$this->number;
        $query=$this->db->query($sql);
        if($query->num_rows > 1){
            return false;
        }
        $result=$query->row_array();
        return $result;
    }
    /**
     * 订单支付成功后,依据相应的奖金设置表保存到数据库中,
     */
    function bonusSave($order_invi){
    	//订单支付成功 把该订单相应的奖金发给邀请者
    	if(strlen($order_invi)<5){
    		//判断用户如果没有邀请码 就不存在奖金
    	 	$bonus_num = 1;
    	}else{
    		//查询该物品的id，订单编号和价钱
    		$sqls='select b.types_id as id,b.order_name,b.order_bid_price as price from  h_order_nonstandard b where b.order_number='.$this->orderid;
    		$type_query=$this->db->query($sqls);
    		$type_result=$type_query->result_array();
    		//获取物品id
    		$gid=$type_result['0']['id'];
    		$gprice=$type_result['0']['price'];
    		$name=$type_result['0']['order_name'];
    		//从h_digital_bonus数码回收寄售奖金区间设置表
    		$sql='select digital_id,digital_type as type,digital_value as value from h_digital_bonus where digital_status=1 and digital_goodid="'.$gid.'"';
    		$goods_query=$this->db->query($sql);
    		$goods_result=$goods_query->result_array();
    		if ($this->db->affected_rows()>0){
    			//在比例或固定值表h_digital_bonus中 获取相应的数据
    			//获取该物品获取奖金的方式及值
    			$gtype=$goods_result['0']['type'];
    			$gvalue=$goods_result['0']['value'];
    			//获取奖金
    			if($gtype==1){
    				$prices=round($goods_result['0']['value']*100/100*$gprice,2);
    			}else{
    				$prices=$goods_result['0']['value'];
    			}
    			$data=array(
    					'gdeal_source'=>2,
    					'gdeal_goodsid'=>$gid,
    					'gdeal_goodid'=>$this->orderid,
    					'gdeal_goodname'=>$name,
    					'gdeal_method'=>2,
    					'gdeal_fixed'=>$gtype,
    					'gdeal_bonus'=>$prices,
    					'gdeal_jointime'=>time(),
    					'gdeal_userid'=>$this->userid,
    					'gdeal_invitation'=>$order_invi,
    					'gdeal_status'=>1
    			);
    			$this->db->insert('h_goodsdeal_bonus',$data);
    			$cons=$this->db->affected_rows();
    			if($cons==1){
    				//邀请者获取奖金收益时 发送推行提醒
    				$open_sql='select a.wx_id as id,a.wx_mobile as mobile,a.wx_openid as 
    						openid from h_wxuser a left join h_wxuser_task b
    					on a.wx_id=b.wx_id where b.center_extend_num="'.$order_invi.'"';
    				$open_query=$this->db->query($open_sql);
    				$open_result=$open_query->result_array();
    				$openid=$open_result['0']['openid'];
    				$wxid=$open_result['0']['id'];
    				$wxmobile=$open_result['0']['mobile'];
    				$this->load->model('common/wxcode_model');
    				if (isset($openid)&&$openid!='') {
    					$temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2fnonstandard/mybonus/mybonusList&response_type=code&scope=snsapi_base&state=#wechat_redirect';
    					$sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的奖金已经发放到你的奖金里面",
                        "description":"点此进入我的奖金页面",
                        "url":"%s", "picurl":""}]}}';
    					$content = sprintf($sendtext,$openid,$temp_url);
    					$response_wx=$this->wxcode_model->sendmessage($content);
	    			}
	    			$bonus_num=$this->db->affected_rows();
    			}else{
    			    $bonus_num=0;
    			}
    		}else{
    			//对比区间设置表 h_digital_bonus看交易价格是否存在于区间范围内
    			$sql='select digital_id,digital_type as type,digital_value as value from h_digital_bonus where digital_status=1 and "'.$gprice.'">=digital_start and "'.$gprice.'"<=digital_end';
    			$shop_query=$this->db->query($sql);
    			$shop_result=$shop_query->result_array();
    			if($this->db->affected_rows()>0){
    				//获取该物品获取奖金的方式及值
    				$gtype=$shop_result['0']['type'];
    				$gvalue=$shop_result['0']['value'];
    				if($shop_result['0']['type']==1){
    					$prices=round($shop_result['0']['value']*100/100*$gprice,2);
    				}else{
    					$prices=$shop_result['0']['value'];
    				}
    				$datas=array(
    						'gdeal_source'=>2,
    						'gdeal_goodsid'=>$gid,
    						'gdeal_goodid'=>$this->orderid,
    						'gdeal_goodname'=>$name,
    						'gdeal_method'=>1,
    						'gdeal_fixed'=>$gtype,
    						'gdeal_bonus'=>$prices,
    						'gdeal_jointime'=>time(),
    						'gdeal_userid'=>$this->userid,
    						'gdeal_invitation'=>$this->invitation,
    						'gdeal_status'=>1
    				);
    				$this->db->insert('h_goodsdeal_bonus',$datas);
    				$bonus_num=$this->db->affected_rows();
    				if($bonus_num==1){
    					//邀请者获取奖金收益时 发送推行提醒
    					$open_sql='select a.wx_id as id,a.wx_mobile as mobile,a.wx_openid as
    						openid from h_wxuser a left join h_wxuser_task b
    					on a.wx_id=b.wx_id where b.center_extend_num="'.$order_invi.'"';
    					$open_query=$this->db->query($open_sql);
    					$open_result=$open_query->result_array();
    					$openid=$open_result['0']['openid'];
    					$wxid=$open_result['0']['id'];
    					$wxmobile=$open_result['0']['mobile'];
    					$this->load->model('common/wxcode_model');
    					if (isset($openid)&&$openid!='') {
    						$temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2fnonstandard/mybonus/mybonusList&response_type=code&scope=snsapi_base&state=#wechat_redirect';
    						$sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的奖金已经发放到你的奖金里面",
                        "description":"点此进入我的奖金页面",
                        "url":"%s", "picurl":""}]}}';
    						$content = sprintf($sendtext,$openid,$temp_url);
    						$response_wx=$this->wxcode_model->sendmessage($content);
    					}
    				}else{
    					$bonus_num=0;
    				}
    			}else{
    				$bonus_num=1;
    			}
    		}
    	}
    	return $bonus_num;
    }
}  