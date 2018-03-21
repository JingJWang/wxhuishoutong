<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8');
class Newmobile_model extends CI_Model{
	
	/**
	 * 加载db类
	 */
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取商品类型品牌
     * @param       int       type       物品类型
     * @param       int       star       开始的页数
     * @return      失败返回false|成功返回数组
     */
    function getBrand($type){
        $sql = 'select brand_name as name,brand_id as id,brand_img as img from h_brand 
                where brand_classification='.$type.' and brand_status=1 and brand_id not in (14,15,22,28)';
        $result = $this->db->query($sql);
        if($result->num_rows<1){
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取商品列表
     * @param       int       bid       商品的id
     * @param       int       star      开始的页数
     * @param       int       num       要取的个数
     * @return      失败返回false|成功返回数组
     */
    function getShops($bid){
       $sql = 'select distinct a.types_id as id,a.types_name as name,a.types_img as img
                from h_electronic_types a left join h_quote_plan as b on a.types_id = b.types_id  
                where brand_id='.$bid.' and b.plan_base_price>5 and a.types_status=1 order by a.types_id desc';
        $result = $this->db->query($sql);
        if($result->num_rows<1){
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取手机型号市价
     */
    function mobilePrice(){
    	$sqls='select a.plan_base_price as price, a.plan_content as content from h_quote_plan a where a.types_id='.$this->id;
    	$result = $this->db->query($sqls);
    	if($result->num_rows<1){
    		return false;
    	}
    	return $result->result_array();
    }
    
	/**
	 * 保存手机选项内容
	 */
    function saveBigScreen(){
    	$data=array(
    			'screen_phoneid'=>$this->phoneid,
    			'screen_content'=>$this->content,
    			'screen_name'=>$this->name,
				'screen_orderid'=>$this->orderid,
    			'screen_price'=>$this->price*100,
    			'screen_jointime'=>time(),
    			'screen_status'=>1
    	);
    	$sql=$this->db->insert('h_big_screen',$data);
		if(!$sql){
    		return false;
    	}else{
    	    $onesqls='select screen_phoneid as id,screen_name as name,screen_orderid as orderid
    	         from h_big_screen where screen_orderid='.$this->orderid;
    	    $result = $this->db->query($onesqls);
    	    if($result->num_rows<1){
    	        return false;
    	    }
    	    return $result->result_array();
    	}
    }
    /**
     * 获取单条数据
     */
    function getOneList(){
        $onsql='select screen_phoneid as id,screen_name as name,screen_orderid as orderid,screen_content as content,
    	       screen_price as price from h_big_screen a where screen_orderid='.$this->orderid;
        $result = $this->db->query($onsql);
        if($result->num_rows<1){
            return false;
        }
        return $result->result_array();
    }
    /**
     * 搜索商品
     */
   /*  function seachShop($bids,$text){
        $where = '';
        foreach ($text as $key => $val) {
            $where .= ' and types_name like "%'.$val.'%" ';
        }
     echo   $sql = 'select  types_id as id,types_name as name,types_img as img
                from h_electronic_types where brand_id in ('.$bids.') '.$where.' and types_status=1 order by types_id desc';
        $result = $this->db->query($sql);
        if($result->num_rows<1){
            return false;
        }
        return $result->result_array();
    } */
}