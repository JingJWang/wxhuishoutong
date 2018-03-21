<?php
/**
 * 后台管理
 * 手机品牌 型号
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class reprice_model extends  CI_Model{
    
    /**
     * 加载db类
     */
    function __construct(){
        parent::__construct();
        $this->load->database();
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
        }else{echo 2;
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
        //数据库读取原始数据
        $sql='select types_name as name ,types_id as id ,types_maxprice as price
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
    function editTypePrice(){
        $brand=array('types_maxprice'=>$this->price,'types_updatetime'=>time());
        $option=array('types_id'=>$this->id);
        $query=$this->db->update('h_electronic_types',$brand,$option);
        echo $this->db->last_query();
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query !== false && $row == 1){
            $key='system_mobile_typeList_price'.$this->brand;
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
       $sql='select types_name as name ,types_id as id ,types_maxprice as price
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
     * 批量修改手机型号表里面的回收最高价
     */
    function upcountprice(){
        //减价
        if($this->hoverinp==2){
            if($this->radck==1){
                //查询需要修改的品牌手机条件
                $where =' and types_maxprice>5 and types_maxprice>'.$this->hideRadio;
                //修改需要改价的手机品牌
                $upwhere='types_maxprice-'.$this->hideRadio;
            }else{
                //查询需要修改的品牌手机条件
                $where = ' and types_maxprice>5 ';
                //修改需要改价的手机品牌
                $upwhere='floor(types_maxprice-'.'types_maxprice/100*'.$this->hideRadio.')';
            }
        }else{//加价
            if($this->radck==1){
                //查询需要修改的品牌手机条件
                $where =' and types_maxprice>5 and types_maxprice>'.$this->hideRadio;
                //修改需要改价的手机品牌
                $upwhere='types_maxprice+'.$this->hideRadio;
            }else{
                //查询需要修改的品牌手机条件
                $where = ' and types_maxprice>5 ';
                //修改需要改价的手机品牌
                $upwhere='round(types_maxprice+'.'types_maxprice/100*'.$this->hideRadio.')';
            }
        }
        $sqls='select count(*) as sum from h_electronic_types where types_status=1 and brand_id in('.$this->dataid.')'.$where;
        $query=$this->db->query($sqls);
        $result=$query->result_array();
        //获取需要修改的行数
       $sum=$result[0]['sum'];
        //修改需要降价的品牌手机
        $this->db->trans_begin();
        $upsql='update h_electronic_types set types_maxprice='.$upwhere.' where types_status=1 and brand_id in('.$this->dataid.')'.$where;
        $upquery=$this->db->query($upsql);
        $count=$this->db->affected_rows();
        if($sum==$count){
            $this->db->trans_commit();
            return true;
        }else{
            $this->db->trans_rollback();
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