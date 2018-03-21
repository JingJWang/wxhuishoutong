<?php
header('Content-type:text/html;charset=UTF-8');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Shoporder_model extends CI_Model {

    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 查询订单信息
     * @param    string  where        修改的筛选信息
     * @param    int     page         开始
     * @param    int     page_per     结束
     */
    function Mqueryorder($orderStatus,$page,$page_per){
        $sql = 'select type_id from h_shop_type where type_fid=2 and type_status=1';
        $return = $this->db->query($sql);
        if ($return==false) {
            return false;
        }
        $types = '';
        if ($return->num_rows>0) {
            $Atype = $return->result_array();
            foreach ($Atype as $k => $v) {
                $types .= 'c.goods_typeid='.$v['type_id'].' or ';
            }
            $types = rtrim('or '.$types,' or ');
        }
        $sql = 'select a.record_id as id,a.record_express as text,a.record_jointime as time,a.record_adress as adress,
                a.record_payid as number,b.receive_name as name,b.receive_phone as phone,b.receive_city as city,a.record_price as price,
                b.receive_details as detail,c.goods_name as gname from h_shop_record as a left join h_wxuser_receiveinfo as b on 
                a.record_adressid=b.receive_id left join h_shop_goods as c on a.record_goodid=c.goods_id 
                where (c.goods_typeid =2 or c.goods_typeid =4 '.$types.') and a.record_status='.$orderStatus.' order by a.record_jointime desc 
                limit '.$page.',10';
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result['list'] = $return->result_array();
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===true) {
            $num = $this->zredis->_redis->get('shopRnum_status_'.$orderStatus);//此状态实物商品的个数
            if ($num != NULL) {//如果有，直接返回
                $result['num']['0']['num'] = $num;
                return $result;
            }
        }
        $sql = 'select count(a.record_id) as num from h_shop_record as a left join h_wxuser_receiveinfo as b on 
                a.record_adressid=b.receive_id left join h_shop_goods as c on a.record_goodid=c.goods_id 
                where (c.goods_typeid =2 or c.goods_typeid =4 '.$types.') and a.record_status='.$orderStatus;
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result['num'] = $return->result_array();
        if ($this->zredis->link===true) {  
            $OK = $this->zredis->_redis->set('shopRnum_status_'.$orderStatus,$result['num']['0']['num']);
            if ($OK!=true) {return false;}
        }
        return $result;
    }
    /**
     * 查询订单信息
     * @param    string  where        修改的筛选信息
     * @param    int     page         开始
     * @param    int     page_per     结束
     */
    function Mseachorder(){
        $sql = 'select a.record_id as id,a.record_express as text,a.record_jointime as time,
                a.record_payid as number,b.receive_name as name,b.receive_phone as phone,b.receive_city as city,
                b.receive_details as detail,c.goods_name as gname from h_shop_record as a left join h_wxuser_receiveinfo as b on 
                a.record_adressid=b.receive_id left join h_shop_goods as c on a.record_goodid=c.goods_id 
                where a.record_payid='.$this->input->post('orderNum',true);
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result['list'] = $return->result_array();
        return $result;
    }
    /**
     * 更具情况给出订单信息
     */
    function Monequery($id){
        $sql = 'select type_id from h_shop_type where type_fid=2 and type_status=1';
        $return = $this->db->query($sql);
        if ($return==false) {
            return false;
        }
        $types = '';
        if ($return->num_rows>0) {
            $Atype = $return->result_array();
            foreach ($Atype as $k => $v) {
                $types .= 'c.goods_typeid='.$v['type_id'].' or ';
            }
            $types = rtrim('or '.$types,' or ');
        }
        $sql = 'select a.record_id as id,a.record_payid as pid,a.record_price as price,a.record_express as text,
                a.record_jointime as time,a.record_status as status,c.goods_name as gname,c.goods_typeid as typeid 
                from h_shop_record as a left join h_shop_goods as c on a.record_goodid=c.goods_id 
                where (c.goods_typeid =2 or c.goods_typeid =4 '.$types.') and a.record_id='.$id;
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result = $return->result_array();
        return $result['0'];
    }
    /**
     * 修改订单信息
     */
    function Mediteorder($express){
        $update = array(
            'record_status' => $this->input->post('orderStatus',true),
            'record_express' => $express,
            'record_updatetime' => time(),
        );
        $result = $this->db->update('h_shop_record',$update,array('record_id' => $this->input->post('orderId',true)));
        if ($result === false) {
            return false;
        }
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        if ($this->zredis->link===true) {
            $this->zredis->_redis->del(array('shopRnum_status_1','shopRnum_status_2','shopRnum_status_-1'));
        }
        return true;
    }
}