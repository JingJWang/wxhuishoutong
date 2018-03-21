<?php
/**
 * 后台管理
 * 手机品牌 型号
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class manageprice_model extends  CI_Model{
    
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
        $sql='select brand_id as id, brand_name as name from h_brand where brand_type=2 and brand_classification=5 and brand_img!=" " and brand_status=1 ' ;  
        $query=$this->db->query($sql);
        if($query === false && $query->num_rows < 1){
            return false;
        }
        $response=$query->result_array();
        return  $response;
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
        $sql='select types_name as name ,a.types_id as id ,plan_base_price as price
              from h_quote_plan a left join h_electronic_types  b 
              on a.types_id=b.types_id  where a.plan_status=1 and b.brand_id='.$this->id.' and types_status = 1 ';
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
        $brand=array('plan_base_price'=>$this->price,'plan_updatetime'=>time());
        $option=array('types_id'=>$this->id);
        $query=$this->db->update('h_quote_plan',$brand,$option);
        $this->db->last_query();
        $row=$this->db->affected_rows();
        $this->db->close();
        if($query !== false && $row == 1){
            
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
     * 批量修改手机型号表里面的回收最高价 根据品牌
     */
    function upcountprice(){
        //减价
        if($this->hoverinp==2){
            if($this->radck==1){//按金额跳转
                //查询需要修改的品牌手机条件
                $where =' and plan_base_price>'.$this->hideRadio;
                //修改需要改价的手机品牌
                $upwhere='plan_base_price-'.$this->hideRadio;
            }else{//按百分比调整
                //查询需要修改的品牌手机条件
                $where = '';
                //修改需要改价的手机品牌
                $upwhere='floor(plan_base_price-'.'plan_base_price/100*'.$this->hideRadio.')';
            }
        }else{//加价
            if($this->radck==1){//按金额跳转
                //查询需要修改的品牌手机条件
                $where = '';
                //修改需要改价的手机品牌
                $upwhere='plan_base_price+'.$this->hideRadio;
            }else{//按百分比调整
                //查询需要修改的品牌手机条件
                $where = '';
                //修改需要改价的手机品牌
                $upwhere='round(plan_base_price+'.'plan_base_price/100*'.$this->hideRadio.')';
            }
        }
        $sqls='select  count(*) as sum from h_quote_plan a left join h_electronic_types  b 
                on a.types_id=b.types_id where a.plan_status=1 and b.types_status=1 
                and a.plan_base_price>50 and b.brand_id in ('.$this->dataid.')'.$where;
        $query=$this->db->query($sqls);
        $result=$query->result_array();
        //获取需要修改的行数
        $sum=$result[0]['sum'];
        //修改需要降价的品牌手机
        $this->db->trans_begin();
        $upsql='update h_quote_plan set plan_base_price='.$upwhere.' where plan_status=1  and plan_base_price>50
                and types_id in( select types_id from h_electronic_types where types_status=1
                and brand_id in ('.$this->dataid.'))'.$where;
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
     * 批量修改手机型号表里面的回收最高价 根据区间
     */
    function uprangeprice(){
        //价格区间
        switch($this->status){
            case '1'://没区间  取价格范围最大值
                switch($this->hoverinp){
                    case '1'://加价
                        switch($this->radck){
                            case '1'://金额
                                $where='and a.plan_base_price>='.$this->lastPrice;
                                $upwhere='a.plan_base_price+'.$this->hideRadio;
                                break;
                            case '2'://百分比
                                $where='and a.plan_base_price>='.$this->lastPrice.' and round(a.plan_base_price/100*'.$this->hideRadio.')>0';
                                $upwhere='a.plan_base_price+'.$this->hideRadio;
                                break;
                            default:break;
                        }
                        break;
                    case '2'://减价
                        switch($this->radck){
                            case '1'://金额
                                if($this->lastPrice>$this->hideRadio){
                                    $where='and a.plan_base_price>='.$this->lastPrice;
                                    $upwhere='a.plan_base_price-'.$this->hideRadio;
                                }else{
                                    $where='and a.plan_base_price>='.$this->hideRadio;
                                    $upwhere='a.plan_base_price-'.$this->hideRadio;
                                }
                                break;
                            case '2'://百分比
                                    $where='and a.plan_base_price>='.$this->lastPrice.' and round(a.plan_base_price/100*'.$this->hideRadio.')>0';
                                    $upwhere='a.plan_base_price-'.$this->hideRadio;
                                break;
                            default:break;
                        }
                        break;
                    default:break;
                }
                break;
            case '2'://有区间
                switch ($this->hoverinp){
                    case '1'://加价
                        switch ($this->radck){
                            case '1'://金额
                                $where='and a.plan_base_price between '.$this->startPirce.' and '.$this->endPirce;
                                $upwhere='a.plan_base_price+'.$this->hideRadio;
                                break;
                            case '2'://百分比
                                $where='and a.plan_base_price between '.$this->startPirce.' and '.$this->endPirce .
                                ' and round(a.plan_base_price/100*'.$this->hideRadio.')>0';
                                $upwhere='a.plan_base_price+round(a.plan_base_price/100*'.$this->hideRadio.')';
                                break;
                            default:break;
                        }
                        break;
                    case '2'://减价
                        switch ($this->radck){
                            case '1'://金额
                                if($this->hideRadio<$this->startPirce){
                                     $where='and a.plan_base_price between '.$this->startPirce.' and '.$this->endPirce;
                                     $upwhere='a.plan_base_price-'.$this->hideRadio;
                                }else if($this->hideRadio>$this->startPirce && $this->hideRadio<$this->endPirce ){
                                    $where='and a.plan_base_price between '.$this->hideRadio.' and '.$this->endPirce;
                                    $upwhere='a.plan_base_price-'.$this->hideRadio;
                                }else if($this->hideRadio>$this->endPirce){
                                    return false;
                                }
                                break;
                            case '2'://百分比
                                   $where='and a.plan_base_price between '.$this->startPirce.' and '.$this->endPirce;
                                   $upwhere='a.plan_base_price-round(a.plan_base_price/100*'.$this->hideRadio.')';
                                break;
                            default:break;
                        }
                        break;
                    default:break;
                }
                break;
             default:break;
        }
        $sqls='select count(*) as sum from h_quote_plan a where a.plan_status=1 '.$where;
        $query=$this->db->query($sqls);
        $result=$query->result_array();
        //获取需要修改的行数
        $sum=$result[0]['sum'];
        //修改需要降价的品牌手机
        $this->db->trans_begin();
        $upsql='update h_quote_plan as a set a.plan_base_price='.$upwhere.' where a.plan_status=1 '.$where;
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
}