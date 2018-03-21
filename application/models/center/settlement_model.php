<?php
/*
 * 数码订单model
 * 
 */
class settlement_model extends CI_Model{
    
    //加载db
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
 	/*
 	 * 后台添加  回收数码11 回收奢侈品12 销售数码21 销售奢侈品22 销售其他23 寄售数据31 寄售奢侈品32 数据
 	 */
    function insertSettlement()
    {
    	$sum = '';
    	/*
    	 * 判断提交的数据是哪个模块的 依据比例抽取其中的利润
    	 */
    	switch ($this->rtype){
    		case '31':$sum = ($this->nprice*0.1-$this->oprice)*0.45; break;
    		case '32':$sum = ($this->nprice*0.1-$this->oprice)*0.45; break;
    		case '11':$sum = ($this->nprice-$this->price-$this->oprice)*0.45; break;
    		case '12':$sum = ($this->nprice-$this->price-$this->oprice)*0.45; break;
    		case '21':$sum = ($this->nprice-$this->price-$this->oprice)*0.45; break;
    		case '22':$sum = ($this->nprice-$this->price-$this->oprice)*0.45; break;
    		case '23':$sum = ($this->nprice-$this->price-$this->oprice)*0.45; break;
    		default:$sum = '';
    	}
    	$data = array(
    			'order_id' => $this->oid,
    			'royalty_mobile' =>$this->mobile,
    			'royalty_good_name' => $this->gname,
    			'royalty_price' => $this->price,
    			'royalty_new_price' => $this->nprice,
    			'royalty_other_price' =>$this->oprice,
    			'royalty_recovery_time' => $this->rtime,
    			'royalty_order_time' => $this->otime,
    			'royalty_comm_sum' => $sum,
    			'royalty_mobile_add' => $this->mobileAdd,
    			'royalty_jointime' => time(),
    			'royalty_type' => $this->rtype
    	);
    	$query=$this->db->insert('h_royalty',$data);
    	$content=$this->db->affected_rows();
    	if($content == 1){
    		return true;
    	}else{
    		return false;
    	}
    }
    //查询sql语句
    /*
     * 查询回收模块数据
     */
    function selectSettlement(){
        //开始位置
     	 $start =($this->page-1)*$this->num;
    	$sql ='select order_id as oid,royalty_good_name as gname,royalty_mobile
    	       as mobile,royalty_price as price,royalty_new_price as nprice,
    	       royalty_other_price as oprice,royalty_order_time as otime,
    	       royalty_recovery_time as rtime from h_royalty where royalty_type='.
    	       $this->rtype.' and royalty_status=1 and royalty_mobile_add="'.
    	       $this->mobileAdd.'" order by royalty_id desc ';
    	$count=$this->db->query($sql);
    	if($start >= 0){
    		 $sql .=' limit '. $start.','.$this->num;
    	}
    	$query=$this->db->query($sql);
		if($query->num_rows<0) {
	    	return false;
	 	}
	 	$result['now'] = $this->page;
        $returnList = $query->result_array();
        $return['list']=$returnList;
        $return['num']= ceil($count->num_rows/$this->num);
        return $return;
    }
}  