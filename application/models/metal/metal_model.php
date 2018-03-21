<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class  metal_model extends CI_Model{    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }    
    /**
     * 获取贵金属下的产品分类
     * 
     */
   function metalType() {
      $sql='select  types_name as name,types_id as id from h_electronic_types
             where brand_id=427 and types_status=1';
      $query=$this->db->query($sql);
      if($query->num_rows < 1){
          return false;
      }
      $data=$query->result_array();
      return $data;
   }
   /**
    * 获取产品参数详细信息
    */ 
   function metalOption(){
       $sql='select  types_name as name,types_id as id,types_attr as attr from  h_electronic_types
             where  types_id='.$this->id.' and types_status=1';
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $data=$query->row_array();
       return $data;
   }
   /**
    * 获取参数信息
    *  
    */
   function metalInfo(){
       $sql='select b.model_name as name,b.model_alias as alias,info_id as id,
             info_info as info from h_option_info as a left join h_option_model 
             as b on a.model_id = b.model_id where product_id=36 ';
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $data=$query->result_array();
       return $data;
       
   }
  /**
   * 获取产品报价方案
   */
   function metalQuote(){
      $sql='select  plan_base_price as base,plan_garbage_price as garbage,
            plan_content as content from 
            h_quote_plan where types_id='.$this->id.' and plan_status=1';
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $data=$query->row_array();
       return $data;
   }
   /**
    * 保存贵金属订单
    */
   function metalOrder($option){
       $data=array(
               'wx_id'=>$_SESSION['userinfo']['user_id'],
               'wx_openid'=>$_SESSION['userinfo']['user_openid'],
               'order_name'=>$option['name'],
               'order_number'=>$option['number'],
               'order_ctype'=>$option['ctype'],
               'order_ftype'=>'4',
               'order_dealtype'=>$option['type'],
               'order_mobile'=>$_SESSION['userinfo']['user_mobile'],
               'order_orderstatus'=>'2',
               'order_jointime'=>time(),
               'order_status'=>'1',
       );
       if($option['mobile']!=''){
       		$mobile=$option['mobile'];
       }else{
       		$mobile='';	
       }
       $info=array(
               'order_id'=>$option['number'],
       		   'electronic_mobile'=>$mobile,
               'brand_id'=>'',
               'electronic_type'=>'',
               'electronic_oather'=>json_encode($option['orderinfo']),
               'electronic_jointime'=>time(),
               'electronic_status'=>'1',
       );
       $this->db->trans_begin();
       $this->db->insert('h_order_nonstandard',$data);
       $row_order=$this->db->affected_rows();
       $this->db->insert('h_order_content',$info);
       $row_info=$this->db->affected_rows();
       if($this->db->trans_status() && $row_order == 1 && $row_info == 1){
           $this->db->trans_commit();
           return true;
       }else{
           $this->db->trans_rollback();
           return false;
       }
   }
   /**
    * 根据用户ID校验用户是否有贵金属信息 没有创建
    */
   function userMetal(){
       $sql='select  metal_id from h_wxuser_metal  where wx_id='.
             $_SESSION['userinfo']['user_id'];
       $query=$this->db->query($sql);
       if($query  &&  $query->num_rows < 1){
          $data=array('wx_id'=>$_SESSION['userinfo']['user_id'],
                  'metal_jointime'=>time(),'metal_status'=>1);
          $this->db->insert('h_wxuser_metal',$data);
          $rowr=$this->db->affected_rows();
          if($rowr == 1 ){
              return true;
          }else{
              return  false;
          }
       }      
       return true;
   }
   /**
    * 根据用户ID查看贵金属信息
    * 
    */
   function userMtealInfo(){
       $sql='select metal_gold as gold,metal_platinum as platinum,
             metal_silver as silver from 
             h_wxuser_metal  where wx_id='.$this->userid;
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return array('gold'=>0,'platinum'=>0,'silver'=>0);
       }
       $data=$query->row_array();
       return $data;
   }
   /**
    * 根据订单编号获取订单内容
    * 
    */
   function orderInfo(){
       $sql='select 
               a.order_dealtype as dealtype,
               a.order_orderstatus as status,
               a.order_jointime as time,
               b.order_id as id,
             b.electronic_oather as content from 
             h_order_nonstandard as a left join  h_order_content as b
             on a.order_number=b.order_id where  a.wx_id='.$this->userid.'
             and a.order_number='.$this->id.' and a.order_status=1 ';
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $data=$query->row_array();
       return $data;
   }
   /**
    * 根据订单编号获取报价信息
    * 
    */
   function metalOffer($number){
       $sql='select offer_isagree as isagree,offer_price as pri,offer_second as uppri 
             from h_cooperator_offer 
             where order_id='.$number;
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $data=$query->row_array();
       return $data;
   }
   /**
    * 读取贵金属交易价格列表
    */
   function priceList(){
       $sql='select 
             b.goods_id as id,b.price_alias alias,
             b.price_buy as buy,b.price_sold as sold,
             b.price_rose as rose,b.product_id as pid,
             a.product_name as name
             from 
             h_order_product as a left join h_metal_price as b 
             on b.product_id = a.product_id
             where 
             a.product_fid=36 and a.product_status=1 ';
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $data=$query->result_array();
       return $data;
       
   }
   /**
    * 更新贵金属交易列表价格
    */  
   function upMetalPrice(){       
       $this->db->trans_begin();
       $this->db->update('h_shop_goods',$this->goldgoods,array('goods_id'=>657));
       $up_goldGoods=$this->db->affected_rows();
       $this->db->update('h_shop_goods',$this->platinumgoods,array('goods_id'=>658));
       $up_platinumGoods=$this->db->affected_rows();
       $this->db->update('h_shop_goods',$this->silvergoods,array('goods_id'=>659));
       $up_silverGoods=$this->db->affected_rows();
       $this->db->update('h_metal_price',$this->gold,array('product_id'=>37));      
       $up_goldr=$this->db->affected_rows();       
       $this->db->update('h_metal_price',$this->platinum,array('product_id'=>38));
       $up_platinum=$this->db->affected_rows();
       $this->db->update('h_metal_price',$this->silver,array('product_id'=>40));
       $up_silver=$this->db->affected_rows();
       if ($this->db->trans_status() === false || $up_goldr != 1 || 
               $up_platinum != 1 || $up_silver !=1 || $up_goldGoods != 1 
               || $up_platinumGoods !=1 || $up_silverGoods !=1 ){
           $this->db->trans_rollback();
           return false;
       }else{
           $this->db->trans_commit();
           return true;
       }
   }
   /**
    * 贵金属卖出
    */
   function metalSellout(){
       $reqinfo=$this->reqinfo;
       //读取用户贵金属库存内容
       $metal='select metal_gold,metal_platinum,metal_silver from
                h_wxuser_metal where wx_id='.$this->userid;
       $metalquery=$this->db->query($metal);
       if($metalquery === false){
           return false;
       }
       $metalinfo=$metalquery->row_array();
       //根据卖出的产品 获取库存存货重量 校验是否满足卖出要求
       switch ($reqinfo['id']){
           case '657':
               $metalWeight=$metalinfo['metal_gold'];
               $metaldata['metal_gold']=$metalinfo['metal_gold']-$reqinfo['weight'];
               $metaltype=1;
               $dealinfo['type']='黄金';
              break;
           case '658':
               $metalWeight=$metalinfo['metal_platinum'];
               $metaldata['metal_platinum']=$metalinfo['metal_platinum']-$reqinfo['weight'];
               $metaltype=2;
               $dealinfo['type']='铂金';
              break;              
           case '659':
               $metalWeight=$metalinfo['metal_silver'];
               $metaldata['metal_silver']=$metalinfo['metal_silver']-$reqinfo['weight'];
               $metaltype=3;
               $dealinfo['type']='白银';
              break;
       }
       if($metalWeight < $reqinfo['weight'] ){
           $this->errcode=1;
           return false;
       }   
       $dealinfo['weight']=$reqinfo['weight'];
       //读取贵金属交易卖出价格
       $sql='select price_sold from h_metal_price where goods_id='.$reqinfo['id'];
       $query=$this->db->query($sql);
       if($query->num_rows < 1){
           return false;
       }
       $metal=$query->row_array();
       $dealinfo['upri']=$metal['price_sold'];
       //总价格
       $total=$metal['price_sold']*$reqinfo['weight']*100;
       $dealinfo['total']=$total/100;
       //读取用户信息     
       $usersql='select wx_balance,wx_id from  h_wxuser
              where wx_id = '.$this->userid;
       $result=$this->db->query($usersql);
       if($result->num_rows < 1 ){
           return false;
       }
       $userinfo = $result->row_array();  
       //根据卖出重量修改余额
       $userdata=array(
               'wx_balance'=>$userinfo['wx_balance']+$total,
               'wx_updatetime'=>date('Y-m-d H:i:s'),
       );
       //卖出记录
       $deallog=array(
               'wx_id'=>$this->userid,
               'record_type'=>$metaltype,
               'record_dealtype'=>2,
               'record_content'=>json_encode($dealinfo),
               'record_jointime'=>time(),
               'record_status'=>1
       );
       $this->db->trans_begin();
       $this->db->update('h_wxuser',$userdata,array('wx_id'=>$this->userid));
       $up_user=$this->db->affected_rows();
       $this->db->update('h_wxuser_metal',$metaldata,array('wx_id'=>$this->userid));
       $up_metal=$this->db->affected_rows();
       $this->db->insert('h_metal_record',$deallog);
       $up_dealinfo=$this->db->affected_rows();
       if ($this->db->trans_status() === false || $up_user != 1 ||
               $up_metal != 1 || $up_dealinfo != 1){
           $this->db->trans_rollback();
           return false;
       }else{
           $this->db->trans_commit();
           return true;
       }
       
   }
   /**
    * 根据用户ID 读取贵金属库存内容
    */
   function metalStocKInfo(){
       //读取用户贵金属库存内容
       $metal='select 
               metal_gold as gold,
               metal_platinum as platinum,
               metal_silver as silver  from
               h_wxuser_metal where wx_id='.$this->userid;
       $query=$this->db->query($metal);
       if($query === false){
           return false;
       }
       $info = $query->row_array();
       return  $info;
   }
   /**
    * 添加贵金属提货订单
    */
   function submitStock(){       
       $stock=$this->stock;
       $metal=$this->metal;
       $metaldata=array(
               'metal_gold'=>$stock['gold']-$metal['gold'],
               'metal_platinum'=>$stock['platinum']-$metal['platinum'],
               'metal_silver'=>$stock['silver']-$metal['silver']
       );
       $stoackinfo=array(
               'wx_id'=>$this->userid,
               'extract_state'=>1,
               'extract_addres'=>$this->addres,
               'extract_number'=>Universal::create_ordrenumber(),
               'extract_content'=>$this->content,
               'extract_jointime'=>time(),
               'extract_status'=>1
       );
       $this->db->trans_begin();
       $this->db->insert('h_metal_extract',$stoackinfo);
       $res_extract = $this->db->affected_rows();
       $this->db->update('h_wxuser_metal',$metaldata,array('wx_id'=>$this->userid));
       $res_metal = $this->db->affected_rows();
       if ($this->db->trans_status() === false || $res_extract != 1 ||
               $res_metal != 1 ){
           $this->db->trans_rollback();
           return false;
       }else{
           $this->db->trans_commit();
           return true;
       }
   }
   /**
    * 读取提货记录
    */
   function stockRecord() {
     //读取用户贵金属库存内容
       $metal='select 
               a.extract_number as number,
               a.extract_express as express,
               a.extract_state as state,
               a.extract_content as content,
               a.extract_jointime as jointime,
               b.receive_name as name,
               b.receive_phone as phone,
               b.receive_province as province,
               b.receive_city as city,
               b.receive_area as area,
               b.receive_details as details 
               from
               h_metal_extract as a left join h_wxuser_receiveinfo as b 
               on a.extract_addres=b.receive_id 
               where a.wx_id='.$this->userid.' and a.extract_status = 1';
       $query=$this->db->query($metal);
       if($query === false){
           return false;
       }
       $info = $query->result_array();
       return  $info;
   }
   /**
    * 读取贵金属交易记录
    */
   function dealRecord(){
       $sql='select record_content as content,record_jointime  as jointime,
             record_dealtype as dealtype,record_type as type
             from h_metal_record 
             where wx_id='.$this->userid.' order by record_jointime desc';
       $query=$this->db->query($sql);
       if($query === false){
           return false;
       }
       $info = $query->result_array();
       return  $info;
   }
   /**
    * 读取用户贵金属交易记录详情
    */
   function dealInfo(){
      $sql='select * from  h_metal_record where wx_id='.$this->userid;
      $query=$this->db->query($sql);
      if($query === false){
          return false;
      }
      $info = $query->result_array();
      return  $info;
   }
   
}