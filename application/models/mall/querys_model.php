<?php
/*
 * 微信端非标准产品  管理员模块
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Querys_model extends CI_Model {
    //管理员表
    private   $order_product  ='h_order_product';
    private   $brand          ='h_brand';
    private   $order_luxury   ='h_order_luxury';
    public    $msg            = '';
    //构造函数
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取奢侈品列表
     * @return 查询成功 返回array|查询失败  返回false
     */
    function luxurys(){
        $sql = 'select product_img as image,product_id as id,product_name as name from '
                .$this->order_product.' where product_fid=3 and product_status=1';
        $result = $this->db->query($sql);
        if ($result === false || $result->num_rows<1) {
            $this->msg='没有任何奢侈品可卖！';
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    /**
     * 获取奢侈品品牌列表
     * @param  int      id      物品的id
     * @return 查询成功 返回array|查询失败  返回false
     */
    function luxurybrand(){
        $id = $this->luxuryid;
        $sql = 'select brand_img as image,brand_id as id,brand_name as name from '
                .$this->brand.' where brand_classification='.$id.' and brand_status=1';
        $result = $this->db->query($sql);
        if ($result === false || $result->num_rows<1) {
            $this->msg='没有此奢侈品品牌可卖！';
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    /**
     * 搜索奢侈品品牌
     * @param  array    arr_key     搜索的信息
     * @return 查询成功 返回array|查询失败  返回false
     */
    function seachbrand(){
        $where=''; 
        foreach ($this->arr_key as $key => $val) {
           $where .= ' and a.brand_name like "%'.$val.'%" ';
        }
        if (isset($this->types)) {
            $where .= ' and a.brand_classification='.$this->types;
        }
        $sql = 'select a.brand_img as image,a.brand_id as id,a.brand_name as name,a.brand_classification as fid,
                b.product_name as fname 
                from '.$this->brand.' as a left join '.$this->order_product.' as b 
                on a.brand_classification=b.product_id where brand_type=1'.$where;
        $result=$this->db->query($sql);
        if($result === false || $result->num_rows() < 1){
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    /**
     * 用订单号搜索订单
     * @param  string   number  订单号码
     * @return 查询成功 返回array|查询失败  返回false
     */
    function orderSearch(){
        $order_num = $this->str_number;
        $sql = 'select a.luxury_number as num,a.luxury_status as status,b.brand_name as bdname,c.product_name as pname
                from h_order_luxury as a left join h_brand as b on a.brand_id=b.brand_id 
                left join h_order_product as c on b.brand_classification = c.product_id where a.luxury_number="'.$order_num .'"';
        $result = $this->db->query($sql);
        if ($result === false || $result->num_rows() < 1) {
            $this->msg = '此订单无法查到商品';
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
}