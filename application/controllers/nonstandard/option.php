<?php
/*  非标准化产品-选项模块
 *    
 *  function  
 *           clothes_types()         获取旧衣表单中的选项
 *           type_attributes()       旧衣分类下属性
 *           electronic_types()      获取电子产品的分类
 *           electronic_brand()      电子产品品牌 默认获取第一个品牌下型号
 *           electronic_typeslist()  品牌下的型号列表
 *           electronic_attribute()  获取电子产品属性
 *           appliance_attribute()   获取家电产品属性
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class Option extends CI_Controller {    
    
    /**
     * 获取产品分类下的产品列表
     * @param   int   type  产品类型
     * @return  成功返回产品类型列表 | 失败返回原因
     */
     function product(){
         $id=$this->input->post('type',true);
         if(empty($id) || !is_numeric($id) || isset($id{2})){
             Universal::Output($this->config->item('request_fall'),'产品类型不正确!');
         }
         $this->load->model('nonstandard/option_model');
         $this->option_model->id=$id;
         $respone=$this->option_model->electronic_type();
         if(!$respone){
             Universal::Output($this->config->item('request_fall'),'没有改类信息!');
         }
         Universal::Output($this->config->item('request_succ'),'','',$respone);
     }
    /**
     * 电子产品 分类下的品牌列表
     * @param   int      id       分类id
     * @param   int      typeid   类型id
     * @return  string   
     */
    function  brand(){
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id) || isset($id{10})){
            Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
        }
        $this->load->model('nonstandard/option_model');
        $this->option_model->id=$id;
        $respone=$this->option_model->types_brandslist();
        if($respone !== false){
            Universal::Output($this->config->item('request_succ'),'','',$respone);
        }
        Universal::Output($this->config->item('request_fall'),'请稍后,本次请求数据出现异常!');
    }
    /**
     * 电子产品 -品牌下 型号列表
     * @param   int     brandid  型号列表
     * @param   int     page     页码
     * @return  string    
     */
    function type(){
        $brandid=$this->input->post('id',true);
        if(empty($brandid) || !is_numeric($brandid) || isset($brandid{10})){
            Universal::Output($this->config->item('request_fall'),'本次请求属于违法请求!');
        }
        $this->load->model('nonstandard/option_model');
        $this->option_model->brandid=$brandid;
        $respone=$this->option_model->brands_typeslist();
        if($respone !== false){
            Universal::Output($this->config->item('request_succ'),'','',$respone);
        }
        Universal::Output($this->config->item('request_fall'),'请稍后,本次请求数据出现异常!');
    }
}
/* End of file Option.php */
/* Location: ./application/controllers/nonstandard/Option.php */