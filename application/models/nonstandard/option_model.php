<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 非标准化  订单提交
 * @author maxiaotao
 *
*/
class Option_model extends CI_Model {
    
    private   $brand  = 'h_brand';  //品牌
    
    private   $types  = 'h_electronic_types'; //型号
    
    private   $clothes_type  ='h_clothes_type'; //类型
    
    private   $electronic_product='h_electronic_product';//电子产品分类
    
    private   $electronic_computer='h_electronic_computer';//电脑配置信息
    
    private   $order_product='h_order_product';
    
    //加载db
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取产品类型的下分类
     * @param  int  id  产品类型id
     * @param 成功返回 array | 失败 返回 bool 
     */
    function  electronic_type(){
        $sql='select product_id as id,product_name as name
              from '.$this->order_product.' where product_status=1 and product_fid='.$this->id;
        $query=$this->db->query($sql);
        if($query->num_rows < 1 || !$query){
            return false;
        }
        $data=$query->result_array();
        return $data;
    }    
    /**
     * 数码产品-获取分类下的 品牌列表
     * @param       int     id  分类id
     * @return      成功返回array  | 失败返回  false
     */
    function  types_brandslist(){
        $sql='select brand_id as id,brand_name as name from '.$this->brand.'
               where brand_status=1  and  brand_classification='.$this->id .' and brand_id != 538 ';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1 ){
            return false;
        }
        $data['brand']=$query->result_array();
        $this->brandid=$data['brand']['0']['id'];
        $data['type']=$this->brands_typeslist();
        if($data['type'] === false){
            $data['type']=0;
        }        
        return $data;
    }
    /**
     * 数码产品-获取品牌下的产品型号
     * @param   int   brandid  品牌di
     * @param   int   page     页码
     * @return  array types    型号列表
     */
    function brands_typeslist() {        
        $sql='select types_id as id,types_name as name from '.$this->types.'
               where types_status=1  and  brand_id='.$this->brandid; //.' limit '.$p.','.$n;
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1 ){
            return false;
        }
        $type=$query->result_array();
        return $type;
    }
    /**
     * 数码产品-获取笔记本的cpu 型号
     */
    function notebook_info($brandid){
        $sql='select computer_id as id,computer_cpu as cpu from '.$this->electronic_computer.'
               where computer_status=1  and  brand_id='.$brandid;
        $query=$this->db->query($sql);
        $data=$this->db->fetch_query($query);
        if(is_null($data)){
            return 0;
        }
        return $data;
    }
   /**
    * 根据产品型号获取产品信息
    */
    function getProInfo(){
        $sql='select a.product_name as pname,a.product_id as pid,b.brand_name 
              as bname,b.brand_id as bid,c.types_name as cname ,c.types_id as cid 
              from h_order_product as a left join h_brand as b on a.product_id=
               b.brand_classification left join  h_electronic_types as c on b.brand_id=
               c.brand_id where c.types_id='.$this->typeid.' and c.types_status=1';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows< 1){
            return false;
        }
        $data=$query->result_array();
        return $data;
    }
}
/* End of file Option_model.php */
/* Location: ./application/models/nonstandard/Option_model.php */