<?php
header('Content-type:text/html;charset=UTF-8');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Luxurys_model extends CI_Model {

    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 添加订单信息
     */
    function Maddorder(){
        $inputs = array(
            'brand_id' => $this->input->post('articleId',true),
            'branch_id' => $this->input->post('shopId',true),
            'luxury_number' => $this->input->post('orderNum',true),
            'luxury_status' => 1,
            'luxury_jointime' => time(),
        );
        $result = $this->db->insert('h_order_luxury',$inputs);
        if ($result===false) {
            return false;
        }
        return true;
    }
    /**
     * 查询订单信息
     * @param    string  where        修改的筛选信息
     * @param    int     page         开始
     * @param    int     page_per     结束
     */
    function Mqueryorder($where,$where_,$page,$page_per){
        $sql = 'select a.luxury_id as lid,a.luxury_jointime as jtime,a.luxury_number as num,
                a.luxury_status as status,b.brand_name as bdname,c.product_name as pname,d.branch_name as bhname from 
                h_order_luxury as a left join h_brand as b on a.brand_id=b.brand_id 
                left join h_order_product as c on b.brand_classification = c.product_id left join h_branch as d on d.id=a.branch_id 
                where '.$where_.' order by a.luxury_jointime desc limit '.$page.','.$page_per;
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result['list'] = $return->result_array();
        $sql = 'select count(luxury_id) as num from h_order_luxury where '.$where;
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result['num'] = $return->result_array();
        return $result;
    }
    /**
     * 更具情况给出订单信息
     */
    function Monequery($where){
        $sql = 'select a.luxury_addtime as atime,a.luxury_expiretime as etime,a.luxury_dealtime as dtime,
                a.luxury_id as lid,a.brand_id as brid,a.branch_id as bhid,a.luxury_jointime as jtime,a.luxury_number as num,
                a.luxury_status as status,b.brand_name as bdname,c.product_id as pid,c.product_name as pname,
                d.branch_name as bhname from h_order_luxury as a left join h_brand as b on a.brand_id=b.brand_id 
                left join h_order_product as c on b.brand_classification = c.product_id left join h_branch as d on d.id=a.branch_id 
                where '.$where;
        $return = $this->db->query($sql);
        if ($return->num_rows<=0) {
            return false;
        }
        $result['list'] = $return->result_array();
        return $result;
    }
    /**
     * 修改订单信息
     */
    function Mediteorder(){
        $time = time();
        $update = array(
            'brand_id' => $this->input->post('articleId',true),
            'branch_id' => $this->input->post('shopId',true),
            'luxury_number' => $this->input->post('orderNum',true),
            'luxury_status' => $this->input->post('orderStatus',true),
            'luxury_uptime' => $time,
        );
        switch ($this->input->post('orderStatus',true)) {
            case '4':
                $update['luxury_addtime'] = $time;
                break;
            case '3':
                $update['luxury_dealtime'] = $time;
                break;
            case '5':
                $update['luxury_expiretime'] = $time;
            default:
                break;
        }
        $result = $this->db->update('h_order_luxury',$update,array('luxury_id' => $this->input->post('orderId',true)));
        if ($result === false) {
            return false;
        }
        return true;
    }
    /**
     * 获取物品类型
     * @return     array     错误或无信息返回false|正确 返回信息
     */
    function Mgetyeinfo(){
        $sql = 'select product_id as prid,product_name as prname from h_order_product where product_fid=3 and product_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取品牌类型
     * @return     array     错误或无信息返回false|正确 返回信息
     */
    function Mgetbrands($id){
        $sql = 'select brand_id as bdid,brand_name as bdname from h_brand where brand_classification=
               '.$id.' and brand_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取门店信息
     */
    function Mgetbranch(){
        $sql = 'select id,branch_name as bname from h_branch where status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<=0) {
            return false;
        }
        return $result->result_array();
    }
}