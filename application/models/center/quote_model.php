<?php
/**
 * 
 * @author xiaotao
 * 系统自动报价model
 */
class quote_model extends  CI_Model{
    
    /**
     * 加载db类
     */
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取品牌列表
     * @param     int   typeid   分类id
     * @return    array
     */
    function getBrandList(){
         $sql='select brand_name as name,brand_id as id,brand_classification as class 
              from h_brand where brand_classification='.$this->typeid.' and brand_status=1';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return  false;
        }
        $result=$query->result_array();
        return  $result;        
    }
    /**
     * 获取品牌下的型号列表
     * @param   int   brandid  品牌id
     * @return array
     */
    function getTypeList(){
        //读取配置记录   
        $plan_sql='select types_id as id,plan_base_price as base,plan_garbage_price 
        as garbage from h_quote_plan where coop_number='.$this->coop;        
        $plan_query=$this->db->query($plan_sql);
        if($plan_query === false){
            return  false;
        }
        $plan=$plan_query->result_array();
        if(is_array($plan)){
            foreach ($plan as $key=>$val){
                $plan[$val['id']]=array('base'=>$val['base'],'garbage'=>$val['garbage']);
            }
        }else{
            $plan=array();
        }        
        $type_sql='select types_id as id,types_name as name from h_electronic_types
        where brand_id='.$this->brandid.' and types_status = 1';
        $type_query=$this->db->query($type_sql);
        if($type_query->num_rows < 1){
            return  false;
        }
        $types=$type_query->result_array();
        foreach ($types as $key=>$val){
            if(array_key_exists($val['id'],$plan)){
                $list[]=array('name'=>$val['name'],'id'=>$val['id'],
                        'base'=>$plan[$val['id']]['base'],
                        'garbage'=>$plan[$val['id']]['garbage']);
            }else{
                $list[]=array('name'=>$val['name'],'id'=>$val['id'],
                        'base'=>'','garbage'=>'');
            }            
        }
       return $list;
    }
    /**
     * 获取参数配置信息
     */
    function getOption(){
        $sql='select a.model_name as name,group_concat(b.info_id) as content,a.model_id as id,
               a.model_type as type,a.model_model as model,a.model_alias as alias,
               a.model_logic as logic from  h_option_model as a left join
               h_option_info as b on a.model_id=b.model_id  where a.model_status=1 and b.info_status=1 
               group by a.model_id order by model_sort asc';
        $query=$this->db->query($sql);
        $model=$query->result_array();
        if($query === false && $query->num_rows < 1){
            return false;
        }
        $sql1='select info_id as id,info_info  as info from h_option_info where info_status=1';
        $info=$this->db->query($sql1);
        $option=$info->result_array();
        if($info=== false && $info->num_rows < 1){
            return false;
        }
        $response=array('model'=>$model,'option'=>$option);
        return $response;
    }
    /**
     * 搜素品牌
     * @param   string  key  关键词 
     * @param   int     type   类型id
     * @return  成功时返回array  | 查询失败 无结果 返回 false
     */    
    function brandSearch(){
        $sql='select  brand_name as name,brand_id as id,brand_classification as class
              from h_brand where brand_classification='.$this->type.
              ' and  brand_name like  "%'.$this->key.'%" and brand_status=1';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return  false;
        }
        $result=$query->result_array();
        return  $result;
    }
    /**
     * 搜素型号
     * @param   string  key  关键词
     * @param   int     id   品牌id
     * @return  成功时返回array  | 查询失败 无结果 返回 false
     */
    function typeSearch(){
        $sql='select a.types_id as id,a.types_name as name,coalesce(b.plan_base_price,0) 
               as base,coalesce(b.plan_garbage_price,0) as garbage from h_electronic_types 
               as a left join h_quote_plan as b on a.types_id = b.types_id
               where a.brand_id='.$this->id.' and a.types_name like "%'.$this->key.'%" and a.types_status=1';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return  false;
        }
        $result=$query->result_array();
        return  $result;
    }
    /**
     * 保存新的属性内容
     * @param   array   option 新增加的内容
     * @return  成功时返回 true  | 失败 返回 false
     */
     function saveOption(){
         $sql='select model_id as id,model_alias as alias from h_option_model 
                 where model_status=1';
         $query=$this->db->query($sql);
         if($query->num_rows < 1){
             return  false;
         }
         $result=$query->result_array();
         foreach ($result as $k=>$v){
             $model[$v['alias']]=$v['id'];
         }
         $count=0;//统计插入次数
         $conetnt='';//sql内容
         foreach ($this->option as $k=>$v){
             if(array_key_exists($k,$model)){
                foreach ($v as $n){
                    $conetnt .=  '('.$model[$k].',"'.$n.'",1)'.',';
                    echo ++$count;
                }
             }else{
                 return false;
             }
         }
         $sql='insert into  h_option_info (model_id,info_info,info_status) values '.trim($conetnt,',');
         $query=$this->db->query($sql);
         if($query  && $this->db->affected_rows() == $count){
             return true;
         }else{
             return false;
         }
     }
     /**
      * 保存型号属性内容信息
      * @param  json  attr  属性内容
      * @param  int   id    型号id
      * @return  成功时返回true  | 失败  返回 false
      */
     function saveAttr(){
         $query=$this->db->update('h_electronic_types',array('types_attr'=>$this->attr,
                 'types_updatetime'=>time()),array("types_id"=>$this->id));
         $row=$this->db->affected_rows();
         if($query && $row  == 1){
             return true;
         }else{
             return  false;
         }
     }
     /**
      * 获取型号属性内容
      * @param   int   id   型号id
      * @return  成功时返回array  | 查询失败 无结果 返回 false
      */
     function getAtrr(){
         $sql='select types_attr from h_electronic_types where types_id='.$this->id;
         $query=$this->db->query($sql);
         if(!$query || $query->num_rows < 1){
             return  false;
         }
         $result=$query->result_array();
         return  $result;
     }
     /**
      *校验当前的属性是否正在使用
      *@param  string  attr  属性id
      *@return  返回 true可以删除 | 返回 false 不可以删除
      */
     function checkAttr(){
         $sql='select types_id as id,types_name as name from h_electronic_types
                  where types_attr like "%['.$this->id.']%"';
         $query=$this->db->query($sql);
         if(!$query){
             return  false;
         }
         if($query->num_rows >= 1){
             $this->msg=$query->result_array();;
             return false;
         }
         
         return true;
     }
     /**
      * 根据属性id删除属性
      * @param  int   id   属性id
      * @return  返回 true删除成功 | 返回false删除失败
      */
     function  delAttr(){
         $query=$this->db->update('h_option_info',array('info_status'=>-1),array('info_id'=>$this->id));
         $row=$this->db->affected_rows();
         if($query && $row  == 1){
             return true;
         }else{
             return  false;
         }
     }
     /**
      * 获取某一型号参数内容
      * @param    int  id   型号id
      * @return  成功返回  array | 失败返回 false
      */
     function getOptionInfo(){
         //校验是否存在cache
         $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
         $this->zredis->key='system_order_brandList';
         $cache=$this->zredis->existsKey();
         if($cache !== false){
             $response=json_decode($cache,true);
             $response['list']=array_slice($response['list'],$start,$this->num);
             return $response;
         }
         //型号信息
         $attr_sql='select types_attr from h_electronic_types where types_id='.$this->id.
                ' and types_attr !=""';
         $attr_query=$this->db->query($attr_sql);
         if(!$attr_query || $attr_query->num_rows < 1){
             return  false;
         }
         $attr_result=$attr_query->result_array();
         $attr=json_decode($attr_result['0']['types_attr'],true);
         //报价方案
         $plan_sql='select plan_base_price as base,plan_garbage_price as garbage,
                  plan_content as content from h_quote_plan where types_id='.
                  $this->id.' and coop_number='.$this->userid;
         $plan_query=$this->db->query($plan_sql);
         if($plan_query === false || $plan_query->num_rows < 1){
             $plan=array();
         }else{
             $plan=$plan_query->result_array();
         }
         //参数类型
         $model_sql='select model_name as name,model_alias as alias,
                     model_type as type from h_option_model  where model_status=1
                     order by model_sort asc';
         $model_query=$this->db->query($model_sql);
         if($model_query === false && $model_query->num_rows < 1){
             return false;
         }
         $model=$model_query->result_array();
         //参数内容
         $info_sql='select info_id as id,info_info  as info from h_option_info where info_status=1';
         $info_query=$this->db->query($info_sql);
         if(!$info_query || $info_query->num_rows < 1){
             return  false;
         }
         $info_result=$info_query->result_array();
         $response=array('attr'=>$attr,'info'=>$info_result,'model'=>$model,'plan'=>$plan);
         return  $response;
     }
     /**
      * 保存型号报价方案
      * @param   int    typeid  型号id
      * @param   int    garbage 型号基价
      * @param   int    base    型号市价
      * @param   array  plan    参数价格
      * @return  成功返回 true || 失败 返回 false
      */
     function savePlan(){
         $sql='select types_id from h_quote_plan where types_id='.
               $this->id.' and coop_number='.$this->coop;
         $add=$this->db->query($sql);
         if($add === false){
             return false;
         }
         if($add->num_rows >= 1){
             $data=array(                     
                     'plan_base_price'=>$this->base,
                     'plan_garbage_price'=>$this->garbage,
                     'plan_content'=>$this->plan,
                     'plan_updatetime'=>time()
             );
             $where=array('types_id'=>$this->id,'coop_number'=>$this->coop);
             $query=$this->db->update('h_quote_plan',$data,$where);
             $row=$this->db->affected_rows();
         }else{
             $data=array(
                     'types_id'=>$this->id,
                     'coop_number'=>$this->coop,
                     'plan_base_price'=>$this->base,
                     'plan_garbage_price'=>$this->garbage,
                     'plan_content'=>$this->plan,
                     'plan_jointime'=>time(),
                     'plan_status'=>1
             );
             $query=$this->db->insert('h_quote_plan',$data);
             $row=$this->db->affected_rows();
         }
         if($query && $row  == 1){
             return true;
         }else{
             return  false;
         }
     }
}