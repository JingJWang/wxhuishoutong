<?php
/**
 * 后台管理
 * 手机品牌 型号
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class mobile_model extends  CI_Model{
    
    /**
     * 加载db类
     */
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取品牌列表
     * @param  int  sp   第几页
     * @return  array 返回列表
     */
    function getBrandList(){
        //开始位置
        $start=$this->page == 1 ? 0 : ($this->page-1)*$this->num;
        //校验是否存在cache
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->key='system_mobile_brandList';
        $cache=$this->zredis->existsKey();
        if($cache !== false){
            $response=json_decode($cache,true);
            $response['list']=array_slice($response['list'],$start,$this->num);
            return $response;
        }
        //数据库读取原始数据
        $sql='select brand_name as name ,brand_id as id ,brand_jointime as jointime
              from h_brand where brand_classification = 5 and brand_status = 1 ';              
        $query=$this->db->query($sql);
        $this->db->close();
        if($query === false && $query->num_rows < 1){
            return false;
        }   
        $brandlist=$query->result_array();
        $response['list']=$brandlist;
        $response['total']=ceil($query->num_rows/$this->num);
        $cache_content=json_encode($response);   
        //缓存结果     
        $this->zredis->key='system_mobile_brandList';
        $this->zredis->val=$cache_content;        
        $cache=$this->zredis->setKey();
        if(!$cache){
            return  false;
        }
        $response['list']=array_slice($brandlist,$start,$this->num);
        return $response;
    }
    /**
     * 修改品牌名称
     * @param  int     id     品牌id
     * @param  string  name   品牌名称
     * @return bool 修改成功返回true | 失败返回false
     */
    function editBrand(){
        $brand=array('brand_name'=>$this->name,'brand_updatetime'=>time());
        $option=array('brand_id'=>$this->id);
        $query=$this->db->update('h_brand',$brand,$option);
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query !== false && $row == 1){
            $this->delcahce('system_mobile_brandList');
            return true;
        }else{            
            return false;
        }
    }
    /**
     * 品牌管理 删除品牌
     * @param  int  id  品牌id
     * @return bool 删除成功 返回ture |删除失败返回 false 原因
     */
    function  delbrand(){
        $brand=array('brand_status'=>-1,'brand_updatetime'=>time());
        $option=array('brand_id'=>$this->id);
        $query=$this->db->update('h_brand',$brand,$option);
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query !== false && $row == 1){
            $this->delcahce('system_mobile_brandList');
            return true;
        }else{
            return false;
        }
    }
    /**
     * 品牌管理 批量删除品牌
     * @param  int  id  品牌id
     * @return bool 删除成功 返回ture |删除失败返回 false 原因
     */
    function delMultiBrand(){
        $sql='update  h_brand  set brand_status=-1,brand_updatetime='.time().' 
              where brand_id in('.$this->id.')';
        $query=$this->db->query($sql);
        $row=$this->db->affected_rows();
        $this->db->close();
        $number=explode(',',$this->id);
        if($query === true && count($number) === $row){
            $this->delcahce('system_mobile_brandList');
            return true;
        }else{
            return false;
        }
    }    
    /**
     * 品牌管理 添加品牌
     * @param  string   name  品牌名称
     * @return bool  添加成功返回 true |添加失败返回false
     */
    function addBrnad(){
        $brand=array('brand_name'=>$this->name,'brand_jointime'=>time(),
                'brand_type'=>2,'brand_classification'=>5,'brand_status'=>1);
        $query=$this->db->insert('h_brand',$brand);
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query === true && $row == 1){
            $this->delcahce('system_mobile_brandList');
            return true;
        }else{
            return false;
        }
    }
    /**
     * 品牌管理 型号列表 获取品牌列表
     * @return  array  返回品牌列表
     */
    function typeBrandlist(){       
        //校验是否存在cache
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->key='system_mobile_brandList';
        $cache=$this->zredis->existsKey();
        if($cache !== false){
            $brand=json_decode($cache,true);
            foreach ($brand['list'] as $key=>$val){
                $response[]=array('id'=>$val['id'],'name'=>$val['name']);
            }
            return $response;
        }else{
            return false;
        }
    }
    /**
     * 根据品牌id 获取型号列表
     * @param   int  id    品牌id
     * @param   int  page  分页
     * @return array  成功返回列表|boolean 失败返回false 
     */
    function typeList(){
        //开始位置
        $start=$this->page == 1 ? 0 : ($this->page-1)*$this->num;
        //校验是否存在cache
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->key='system_mobile_typeList_'.$this->id;
        $cache=$this->zredis->existsKey();
        if($cache !== false){
            $response=json_decode($cache,true);
            $response['list']=array_slice($response['list'],$start,$this->num);
            return $response;
        }
        //数据库读取原始数据
        $sql='select types_name as name ,types_id as id ,types_jointime as jointime
              from h_electronic_types where  brand_id='.$this->id.' and types_status = 1 ';
        $query=$this->db->query($sql);
        $this->db->close();
        if($query === false && $query->num_rows < 1){
            return false;
        }
        $brandlist=$query->result_array();
        $response['list']=$brandlist;
        $response['total']=ceil($query->num_rows/$this->num);
        $cache_content=json_encode($response);
        //缓存结果
        $this->zredis->key='system_mobile_typeList_'.$this->id;
        $this->zredis->val=$cache_content;
        $cache=$this->zredis->setKey();
        if(!$cache){
            return false;
        }
        $response['list']=array_slice($brandlist,$start,$this->num);
        return $response;
    }
    /**
     * 修改型号名称
     * @param  int     id    型号id
     * @param  int     brand   品牌id
     * @param  string  name  型号名称
     * @return  bool 修改成功返回true | 修改失败返回 false
     */
    function editTypeName(){
        $brand=array('types_name'=>$this->name,'types_updatetime'=>time());
        $option=array('types_id'=>$this->id);
        $query=$this->db->update('h_electronic_types',$brand,$option);
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query !== false && $row == 1){
            $key='system_mobile_typeList_'.$this->brand;
            $this->delcahce($key);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 搜索型号
     * @param   int      page  分页
     * @param   string   keyword  关键词
     * @return  array  成功返回搜索结果  | bool 失败返回false
     */
    function searchType(){
        //开始位置
       $start=$this->page == 1 ? 0 : ($this->page-1)*$this->num;
        //数据库读取原始数据
       $sql='select types_name as name ,types_id as id ,types_jointime as jointime
              from h_electronic_types where  brand_id='.$this->brand.' 
              and types_name like "%'.$this->keyword.'%" and types_status = 1 ';
        $query=$this->db->query($sql);
        $this->db->close();
        if($query === false && $query->num_rows < 1){
            return false;
        }
        $brandlist=$query->result_array();
        $response['list']=$brandlist;
        $response['total']=ceil($query->num_rows/$this->num);
        $cache_content=json_encode($response);        
        $response['list']=array_slice($brandlist,$start,$this->num);
        return $response;
    }    
    /**
     * 品牌管理 删除品牌
     * @param  int  typeid    型号id
     * @param  int  brandid   品牌id
     * @return bool 删除成功 返回ture |删除失败返回 false 原因
     */
    function  deltype(){
        $brand=array('types_status'=>-1,'types_updatetime'=>time());
        $option=array('types_id'=>$this->typeid);
        $query=$this->db->update('h_electronic_types',$brand,$option);
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query !== false && $row == 1){
            $key='system_mobile_typeList_'.$this->brandid;
            $this->delcahce($key);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 型号管理  增加新的型号信息
     * @param  int     brand 品牌id
     * @param  string  type  型号名称
     * @return  bool  添加的结果
     */
    function  addtype(){
        $brand=array('types_name'=>$this->type,'types_jointime'=>time(),
                'brand_id'=>$this->brand,'types_status'=>1);
        $query=$this->db->insert('h_electronic_types',$brand);
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query === true && $row == 1){
            $key='system_mobile_typeList_'.$this->brand;
            $this->delcahce($key);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 根据key 删除缓存
     */
    function delcahce($key){
        //删除品牌列表的缓存信息
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->_redis->del($key);
    }
}