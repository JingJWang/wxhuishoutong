<?php
/**
 * 贵金属商品
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class metal_model extends CI_Model {
    
    
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    
    /**
     * 读取贵金属商品记录
     */
    function metalGoodsInfo() {
        $id=isset($this->id)? $this->id : 657;
        $sql='select 
              goods_id as id,
              goods_name as name,
              goods_ppri as pri,  
              goods_img  as img,
              goods_content as content
              from h_shop_goods 
              where goods_id='.$id.' and goods_typeid=19';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        $data['pri']=$data['pri']/100;
        return $data;
    }
    /**
     * 保存贵金属商品订单
     */
    function savelMetalOrder(){
        $data=array(
              'record_userid'=>$this->userid,
              'record_name'=>$this->name,
              'record_dealtype'=>$this->type,
              'record_content'=>$this->content,
              'record_goodid'=>$this->goodsid,
              'record_price'=>$this->price,
              'record_payid'=>$this->number,
              'record_jointime'=>time(),
              'record_status'=>0  
        );
        $query=$this->db->insert('h_shop_record',$data);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }
        $id=$this->db->insert_id();
        return $id;
    }
    /**
     * 读取订单内容
     */
    function recordInfo() {
        $sql='select 
              a.record_id as id,
              a.record_payid as number,
              a.record_name as name,
              a.record_dealtype as dealtype,
              a.record_goodid as goodid,
              a.record_price as price,
              a.record_content as content,
              b.goods_img as img
              from  h_shop_goods as b left join h_shop_record as a
              on b.goods_id=a.record_goodid
              where record_id='.
              $this->id.' and record_userid='.$this->userid;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        return $data;
    }
    /**
     * 根据订单编号读取订单内容
     */
    function numberRecordInfo(){
        $sql='select
              a.record_id as id,
              a.record_goodid as goodid,
              a.record_payid as number,
              a.record_name as name,
              a.record_dealtype as dealtype,
              a.record_goodid as goodid,
              a.record_userid as userid,
              a.record_price as price,
              a.record_status as status,
              a.record_content as content,
              b.goods_img as img
              from  h_shop_goods as b left join h_shop_record as a
              on b.goods_id=a.record_goodid
              where record_payid='.$this->number;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        return $data;
        
    }
    /**
     * 读取用户预留的ID地址
     */
    function useraddres(){
        if(isset($this->addres) && $this->addres != 0){
            $where=' and receive_id='.$this->addres;
        }else{
            $where = ' ';
        }
        $sql = 'select 
                    receive_id as id,
                    receive_name as name,
                    receive_phone as number,
                    receive_province as province,
                    receive_city as city,
                    receive_details as details,
                    receive_status as status 
            from h_wxuser_receiveinfo 
            where user_id='.$this->userid.'
            and (receive_status=1 or  receive_status=2)'.$where.' 
            order by status desc limit 1     ';
        $result = $this->db->query($sql);
        if($result == false){
            return false;
        }
        if ($result->num_rows < 1) {
            return array();
        }
        $result = $result->row_array();
        return $result;
    }
    /**
     * 余额方式购买贵金属
     */
    function payBalance(){
        //读取用户信息
        $sql='select wx_balance,wx_id from  h_wxuser where wx_id = '.$this->userid;        
        $result=$this->db->query($sql);
        if($result->num_rows < 1 ){
            return false;
        } 
        $user = $result->row_array();
        //读取订单内容
        $sql='select record_id as id,
                     record_goodid as goodid,
                     record_dealtype as delatype,
                     record_price as price,
                     record_content as content 
              from  h_shop_record
              where record_payid='.$this->number.' and record_userid='.$this->userid;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $data=$query->row_array();
        if($user['wx_balance'] < $data['price']){
            return false;
        }
        $dealinfo['total']=$data['price']/100;
        //余额减掉订单价格
        $upuser=array(
               'wx_balance'=>$user['wx_balance']-$data['price']
        );
        //获取订单重量
        $content=json_decode($data['content'],true);
        $weight=$content['weight'];
        $dealinfo['weight']=$weight;
        $dealinfo['upri']=$data['price']/$weight/100;
        //读取用户贵金属库存内容
        $metal='select metal_gold,metal_platinum,metal_silver from h_wxuser_metal where wx_id='.$user['wx_id'];
        $metalquery=$this->db->query($metal);
        if($metalquery === false){
            return false;
        }
        if($metalquery->num_rows < 1){
            $metaldata=array('metal_gold'=>$weight,'metal_jointime'=>time(),
                    'wx_id'=>$user['wx_id'],'metal_status'=>1);
            $metaltype=false;
        }else{
            $metalinfo=$metalquery->row_array();
            if($data['delatype'] == 1){
                switch ($data['goodid']){
                    case '657':
                        $metaldata=array(
                                'metal_gold'=>$metalinfo['metal_gold'] + $weight,
                                'metal_uptime'=>time()
                        );
                        $type=1;
                        $dealinfo['type']='黄金';
                        break;
                    case '658':
                        $metaldata=array(
                                'metal_platinum'=>$metalinfo['metal_platinum'] + $weight,
                                'metal_uptime'=>time()
                        );
                        $type=2;
                        $dealinfo['type']='铂金';
                        break;
                    case '659':
                        $metaldata=array(
                                'metal_silver'=>$metalinfo['metal_silver'] + $weight,
                                'metal_uptime'=>time()
                        );
                        $type=3;
                        $dealinfo['type']='白银';
                        break;
                }
                
                $metaltype=true;
            }
        }
        //贵金属买进记录
        $deallog=array(
                'wx_id'=>$this->userid,
                'record_type'=>$type,
                'record_dealtype'=>1,
                'record_content'=>json_encode($dealinfo),
                'record_jointime'=>time(),
                'record_status'=>1
        );
        $record_status=$data['delatype'] == 1 ? 1 : 2;
        $this->orderid=$data['delatype'] == 1 ? 0 : $data['id'];
        $this->db->trans_begin();
        $this->db->update('h_wxuser',$upuser,array('wx_id'=>$user['wx_id']));
        $row_user=$this->db->affected_rows();
        $this->db->update('h_shop_record',array('record_updatetime'=>time(),'record_status'=>$record_status),
                array('record_id'=>$data['id']));
        $row_order=$this->db->affected_rows();
        if($data['delatype'] == 1){
            if($metaltype){            
                $this->db->update('h_wxuser_metal',$metaldata,array('wx_id'=>$user['wx_id']));        
            }else{
                $this->db->insert('h_wxuser_metal',$metaldata);
            }
            $row_metal=$this->db->affected_rows();
        }else{
            $row_metal=1;
        }
        $this->db->insert('h_metal_record',$deallog);
        $up_dealinfo=$this->db->affected_rows();
        if ($this->db->trans_status() === false || $row_user !=1 || 
                $row_order !=1 || $row_metal !=1 || $up_dealinfo != 1){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return  true;
        }  
    }
     
    /**
     * 处理微信支付结果
     */
    function payWx(){
        //读取用户信息
        $orderinfo=$this->orderinfo;
        $payinfo=$this->payinfo;   
        //获取订单交易方式 值为1 库存 2 现货      
        $orderinfo['dealtype']; 
        if($orderinfo['dealtype'] == 1){
            $resp=$this->payWxStock($orderinfo,$payinfo);
        }else{
            $resp=$this->payWxExtract($orderinfo,$payinfo);
        }    
        return $resp;
    }
    /**
     * 微信支付 订单是库存交易
     */
    function payWxStock($orderinfo,$paydata){
        $sql='select wx_balance,wx_id from  h_wxuser 
              where wx_id = '.$orderinfo['userid'];
        $result=$this->db->query($sql);
        if($result->num_rows < 1 ){
            return false;
        }
        $user = $result->row_array();
        //获取订单重量
        $content=json_decode($orderinfo['content'],true);
        $weight=$content['weight'];
        $dealinfo['weight']=$weight;
        //读取用户贵金属库存内容
        $metal='select metal_gold,metal_platinum,metal_silver from 
                h_wxuser_metal where wx_id='.$orderinfo['userid'];
        $metalquery=$this->db->query($metal);
        if($metalquery === false){
            return false;
        }
        //当用户不存贵金属内容  创建记录 否则 修改内容
        if($metalquery->num_rows < 1){  
            $metaldata=array('metal_gold'=>$weight,'metal_jointime'=>time(),
                    'wx_id'=>$orderinfo['userid'],'metal_status'=>1);
            
            $metaltype=false;
        }else{
            $metalinfo=$metalquery->row_array();
            switch ($orderinfo['goodid']){
                case '657':
                    $metaldata=array(
                        'metal_gold'=>$metalinfo['metal_gold'] + $weight,
                        'metal_uptime'=>time()
                    );
                    $type=1;
                    $dealinfo['type']='黄金';
                    break;
                case '658':
                    $metaldata=array(
                        'metal_platinum'=>$metalinfo['metal_platinum'] + $weight,
                        'metal_uptime'=>time()
                    );
                    $type=2;
                    $dealinfo['type']='铂金';
                    break;
                case '659':
                    $metaldata=array(
                        'metal_silver'=>$metalinfo['metal_silver'] + $weight,
                        'metal_uptime'=>time()
                    );
                    $type=3;
                    $dealinfo['type']='白银';
                    break;
            }
            $metaltype=true;
        }
        $dealinfo['total']=$paydata['income_totalfee']/100;
        $dealinfo['upri']=$paydata['income_totalfee']/$weight/100;
        //买入记录
        $deallog=array(
                'wx_id'=>$orderinfo['userid'],
                'record_type'=>$type,
                'record_dealtype'=>1,
                'record_content'=>json_encode($dealinfo),
                'record_jointime'=>time(),
                'record_status'=>1
        );
        $this->db->trans_begin();
        //修改订单状态
        $this->db->update('h_shop_record',array('record_updatetime'=>time(),'record_status'=>1),
                array('record_id'=>$orderinfo['id']));
        $row_order=$this->db->affected_rows();
        //修改或添加 用户贵金属信息
        if($metaltype){
            $this->db->update('h_wxuser_metal',$metaldata,array('wx_id'=>$orderinfo['userid']));
        }else{
            $this->db->insert('h_wxuser_metal',$metaldata);
        }
        $row_metal=$this->db->affected_rows();
        //添加支付记录
        $this->db->insert('h_bill_income',$paydata);        
        $row_income=$this->db->affected_rows();
        $this->db->insert('h_metal_record',$deallog);
        $row_deallog=$this->db->affected_rows();
        if ($this->db->trans_status() === false || $row_order !=1 || 
             $row_metal !=1 || $row_income !=1 || $row_deallog !=1 ){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return  true;
        }
        
    }
    /**
     * 微信支付 订单是现货
     */
    function payWxExtract($orderinfo,$paydata) {
        $this->db->trans_begin();
        //修改订单状态
        $this->db->update('h_shop_record',array('record_updatetime'=>time(),'record_status'=>2),
                array('record_id'=>$orderinfo['id']));
        $row_order=$this->db->affected_rows();
        //添加支付记录
        $this->db->insert('h_bill_income',$paydata);
        $row_income=$this->db->affected_rows();
        if ($this->db->trans_status() === false || $row_order !=1 ||  $row_income !=1 ){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return  true;
        }
    }
    /**
     * 根据交易方式处理支付宝支付结果
     */
    function payZfb(){
        //读取用户信息
        $orderinfo=$this->orderinfo;
        $payinfo=$this->payinfo;
        //获取订单交易方式 值为1 库存 2 现货
        $orderinfo['dealtype'];
        if($orderinfo['dealtype'] == 1){
            $resp=$this->payZfbStock($orderinfo,$payinfo);
        }else{
            $resp=$this->payZfbExtract($orderinfo,$payinfo);
        }
        return $resp;
    }
    /**
     * 支付宝支付 交易方式加入库存
     */
    function payZfbStock($orderinfo,$payinfo){
        $sql='select wx_balance,wx_id from  h_wxuser
              where wx_id = '.$orderinfo['userid'];
        $result=$this->db->query($sql);
        if($result->num_rows < 1 ){
            return false;
        }
        $user = $result->row_array();
        //获取订单重量
        $content=json_decode($orderinfo['content'],true);
        $weight=$content['weight'];
        $dealinfo['weight']=$weight;
        //读取用户贵金属库存内容
        $metal='select metal_gold,metal_platinum,metal_silver from
                h_wxuser_metal where wx_id='.$orderinfo['userid'];
        $metalquery=$this->db->query($metal);
        if($metalquery === false){
            return false;
        }
        //当用户不存贵金属内容  创建记录 否则 修改内容
        if($metalquery->num_rows < 1){
            $metaldata=array('metal_gold'=>$weight,'metal_jointime'=>time(),
                    'wx_id'=>$orderinfo['userid'],'metal_status'=>1);
            $metaltype=false;
        }else{
            $metalinfo=$metalquery->row_array();
            switch ($orderinfo['goodid']){
                case '657':
                    $metaldata=array(
                        'metal_gold'=>$metalinfo['metal_gold'] + $weight,
                        'metal_uptime'=>time()
                    );
                    $type=1;
                    $dealinfo['type']='黄金';
                    break;
                case '658':
                    $metaldata=array(
                        'metal_platinum'=>$metalinfo['metal_platinum'] + $weight,
                        'metal_uptime'=>time()
                    );
                    $type=2;
                    $dealinfo['type']='铂金';
                    break;
                case '659':
                    $metaldata=array(
                        'metal_silver'=>$metalinfo['metal_silver'] + $weight,
                        'metal_uptime'=>time()
                    );
                    $type=3;
                    $dealinfo['type']='白银';
                    break;
            }
            $metaltype=true;
        }
        $dealinfo['total']=$payinfo['income_totalfee']/100;
        $dealinfo['upri']=$payinfo['income_totalfee']/$weight/100;
        //买入记录
        $deallog=array(
                'wx_id'=>$orderinfo['userid'],
                'record_type'=>$type,
                'record_dealtype'=>1,
                'record_content'=>json_encode($dealinfo),
                'record_jointime'=>time(),
                'record_status'=>1
        );
        $this->db->trans_begin();
        //修改订单状态
        $this->db->update('h_shop_record',array('record_updatetime'=>time(),'record_status'=>1),
                array('record_id'=>$orderinfo['id']));
        $row_order=$this->db->affected_rows();
        //修改或添加 用户贵金属信息
        if($metaltype){
            $this->db->update('h_wxuser_metal',$metaldata,array('wx_id'=>$orderinfo['userid']));
        }else{
            $this->db->insert('h_wxuser_metal',$metaldata);
        }
        $row_metal=$this->db->affected_rows();
        //添加支付记录
        $this->db->insert('h_bill_income',$payinfo);
        $row_income=$this->db->affected_rows();
        $this->db->insert('h_metal_record',$deallog);
        $row_deallog=$this->db->affected_rows();
        if ($this->db->trans_status() === false || $row_order !=1 ||
                 $row_metal !=1 || $row_income !=1  || $row_deallog !=1){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return  true;
        }
    }
    /**
     * 支付宝交易交易方式现货
     */
    function payZfbExtract($orderinfo,$paydata) {
        $this->db->trans_begin();
        //修改订单状态
        $this->db->update('h_shop_record',array('record_updatetime'=>time(),'record_status'=>2),
                array('record_id'=>$orderinfo['id']));
        $row_order=$this->db->affected_rows();
        //添加支付记录
        $this->db->insert('h_bill_income',$paydata);
        $row_income=$this->db->affected_rows();
        if ($this->db->trans_status() === false || $row_order !=1 ||  $row_income !=1 ){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return  true;
        }
    }
    
}