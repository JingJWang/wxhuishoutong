<?php
/**
 * 
 * @author xiaotao
 * 系统自动报价model
 */
class repairhome_model extends  CI_Model{
    
    /**
     * 加载db类
     */
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取手机维修管理 品牌名称
     * @param     int   typeid   分类id
     * @return    array
     */
    function getPhoneBrandList(){
    	$sql='select brand_name as name,brand_id as id,brand_classification as class
              from h_brand where brand_classification='.$this->typeid.' and brand_status=1 and brand_id not in (538,540)';
    	$query=$this->db->query($sql);
    	if($query->num_rows < 1){
    		return  false;
    	}
    	$result=$query->result_array();
    	return  $result;
    }
    /**
     * 获取手机维修管理手机型号名称
     * @param     int   typeid   分类id
     * @return    array
     */
    function getPhoneTypeList(){
    	$sql='select types_name as name,types_id as id from h_electronic_types 
    		 where brand_id='.$this->brandid.' and types_status=1 ';
    	$query=$this->db->query($sql);
    	if($query->num_rows < 1){
    		return  false;
    	}
    	$result=$query->result_array();
    	return  $result;
    }
    /**
     * 获取品牌列表
     * @param     int   typeid   分类id
     * @return    array
     */
    function getBrandList(){
    	//获取手机故障表里面的手机品牌
    	$paisql='select distinct mobile_pid from h_repair_mobile where model_status=1';
    	$paiquery=$this->db->query($paisql);
    	$pairesult=$paiquery->result_array();
		//把二维数组转换成字符串    	
    	$sum = 0;
    	$count = count($pairesult);
    	for($i = 0; $i < $count; $i++){
    		$sum .= $pairesult[$i]['mobile_pid'].',';
    	}
    	$sum = substr($sum,1);
    	$sum = substr($sum,0,strlen($sum)-1);
        $sql='select brand_name as name,brand_id as id,brand_classification as class 
              from h_brand where brand_classification='.$this->typeid.' and brand_status=1 and brand_id in('.$sum.')';
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
        $type_sql='select b.mobile_id as id,a.types_name as name from h_electronic_types as a
        		right join h_repair_mobile as b on b.mobile_id= a.types_id
        		where b.mobile_pid='.$this->brandid.' and a.types_status = 1 
        		and b.model_status=1 ';
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
     * 获取手机维修管理故障参数及保证金信息信息
     */
    function getPhoneOption(){
    	//获取所有故障大类信息
    	$bigsql='SELECT b.model_name as mname,b.model_id as mid
				FROM  h_repair_model as b where b.model_status=1 ';
    	$bigquery=$this->db->query($bigsql);
    	$data['big']=$bigquery->result_array();
    	if($bigquery=== false && $bigquery->num_rows < 1){
    		return false;
    	}
    	//获取所有故障小类信息
    	$smaillsql='SELECT a.permit_name as pname,a.permit_id as pid,b.model_id as mid
					FROM `h_repair_permit` as a
					left join h_repair_model as b 
					on a.model_pid=b.model_id
					where b.model_status=1 and permit_status=1';
    	$smaillquery=$this->db->query($smaillsql);
    	$data['smail']=$smaillquery->result_array();
    	if($bigquery=== false && $bigquery->num_rows < 1){
    		return false;
    	}
    	return $data;
    }
    
    /**
     * 获取参数配置信息
     */
    function getOption(){
    	//获取大类名称
    	$bigsql='select a.model_id as id,a.model_name as name from h_repair_model as a where a.model_status=1';
    	$bigquery=$this->db->query($bigsql);
    	$data['big']=$bigquery->result_array();
    	if($bigquery=== false && $bigquery->num_rows < 1){
    		return false;
    	}
    	//获取小类名称
    	$smallsql='select a.permit_id as pid,a.permit_name as pname,b.model_id as mid,
     				b.model_name as mname,a.permit_jointime as jointime 
     				from h_repair_permit as a left join h_repair_model as b
     				on a.model_pid=b.model_id where b.model_status=1 and 
     				a.permit_status=1 '; 
    	$smallquery=$this->db->query($smallsql);
    	$data['small']=$smallquery->result_array();
    	if($smallquery=== false && $smallquery->num_rows < 1){
    	    return false;
    	}
    	//获取该手机型号配置
    	$res_sql='select mobile_pid as pid,mobile_id as mid,model_bond as bond,model_content as cons 
    	           from h_repair_mobile where mobile_id='.$this->mobile;
        $res_query=$this->db->query($res_sql);
        if($res_query=== false && $res_query->num_rows < 1){
            return false;
        }
        $data['res']=$res_query->result_array();
        //var_dump($data['res']);
        $data['con']=json_decode($data['res'][0]['cons']);
    	return $data;
    }
     /**
      * 保存型号属性内容信息
      * @param  json  attr  属性内容
      * @param  int   id    型号id
      * @return  成功时返回true  | 失败  返回 false
      */
     function upSaveAttr(){
     	$sql='select * from h_repair_mobile where mobile_id='.$this->mid;
     	$query=$this->db->query($sql);
     	if(!$query || $query->num_rows < 1){
     		return false;
     	}else{
     		$data=array(
     				'model_content'=>$this->attr,
     				'model_updatetime'=>time()
     		);
     		$where=array('mobile_id'=>$this->mid);
             $query=$this->db->update('h_repair_mobile',$data,$where);
     	}
     	$row=$this->db->affected_rows();
     	if($row==1){
     		return true;
     	}else{
     		return false;
     	}
     }
     
     /**
      * 手机维修管理  保存型号属性内容信息
      * @param  json  attr  属性内容
      * @param  int   id    型号id
      * @return  成功时返回true  | 失败  返回 false
      */
     function savePhoneAttr(){
     	$sql='select * from h_repair_mobile where mobile_id='.$this->mid;
     	$query=$this->db->query($sql);
     	if(!$query || $query->num_rows < 1){
     		$data=array(
     				'mobile_pid'=>$this->pid,
     				'mobile_id'=>$this->mid,
     				'mobile_name'=>$this->mname,
     				'model_content'=>$this->attr,
     				'model_bond'=>$this->bond*100,
     				'model_jointime'=>time(),
     				'model_status'=>1
     		);
     		$this->db->insert('h_repair_mobile',$data);
     	}else{
     		return false;
     	}
     	$row=$this->db->affected_rows();
     	if($row==1){
     		return true;
     	}else{
     		return false;
     	}
     }
     
     /**
      * 获取手机维修订单列表
      */
     function repairList(){
     	$where =' where 1=1 ';
     	if($this->status!=''){
     		$where .=' and a.repair_status='.$this->status;
     	}
     	if($this->start!='' && $this->end!=''){
     		$where .=' and a.repair_jointime>="'.strtotime($this->start).'" and a.repair_jointime<="'.strtotime($this->end).'"';
     	}else if($this->start!='' && $this->end==''){
    		$where.=' and repair_jointime>="'.strtotime($this->start).'"';
    	}else if($this->start=='' && $this->end!=''){
    		$where.=' and repair_jointime<="'.strtotime($this->end).'"';
    	}
     	if($this->id!=''){
     		$where .=' and a.repair_id='.$this->id;
     	}
     	if($this->phone!=''){
     		$where .=' and a.repair_phone='.$this->phone;
     	}
     	
     	$sql='select a.repair_id as id,a.repair_goodsid as goodsid,a.repair_goodsname as goodsname,
				a.repair_name as name,a.repair_phone as phone,a.repair_address as adr,a.repair_content as con,
				a.repair_discount as dis,a.repair_bonus as bonus,a.repair_money as money,
				a.repair_jointime as jointime,a.repair_status as status, a.repair_wxid as wxid,
				a.repair_express as express,a.repair_num as num,a.repair_comment as comment,a.repair_other as other,
     			a.repair_updatetime as updatetime
				from h_repair a'.$where.' order by repair_id desc limit '.$this->page.',10'; 
    	$query=$this->db->query($sql);
     	$data['list']=$query->result_array();
     	$sum_sql='select count(*) as sum from h_repair a'.$where;
     	$sum_query=$this->db->query($sum_sql);
     	if(!$sum_query || $sum_query->num_rows < 1){
     		return false;
     	}
     	$data['num']=$sum_query->result_array();
     	return $data;
     }
     /*
      * 获取单条记录
      */
     function getOnerepair(){
         //获取该订单所有信息
     	$sql='select a.repair_id as id,a.repair_goodsid as goodsid,a.repair_goodsname as goodsname,
				a.repair_name as name,a.repair_phone as phone,a.repair_address as adr,a.repair_content as con,
				a.repair_discount as dis,a.repair_bonus as bonus,a.repair_money as money,
				a.repair_jointime as jointime,a.repair_status as status, a.repair_wxid as wxid,
				a.repair_express as express,a.repair_num as num,a.repair_comment as comment,a.repair_other as other,
     			a.repair_updatetime as updatetime,a.repair_contentid as conid,a.repair_paystatus as paysta
				from h_repair a where repair_id='.$this->id;
     	$query=$this->db->query($sql);
     	$data['list']=$query->result_array();
     	//取出该订单的故障信息内容
     	$data['content']=json_decode($data['list'][0]['conid']);
     	$mobileid=$data['list']['0']['goodsid'];
/*      	$sqls='SELECT c.permit_name as name,c.model_pid as mid,c.permit_id as pid,
				case when b.repair_menu is not null then b.repair_money else 0 end as money
				from h_repair_permit c left join (select t.mobile_id,substring_index(t.mobile_content,":",2) as 
				repair_menu,substring_index(t.mobile_content,":",-1) as repair_money
 				from (select a.mobile_id,substring_index(substring_index(a.model_content,",",
     			b.help_topic_id+1),",",-1) as mobile_content from h_repair_mobile a join
   				mysql.help_topic b on b.help_topic_id < (length(a.model_content) - length
     			(replace(a.model_content,",",""))+1) where a.mobile_id='.$mobileid.' ) t ) 
     			b on concat(c.model_pid,":",c.permit_id)=b.repair_menu'; */
     	/* $querys=$this->db->query($sqls);
     	$data['con']=$querys->result_array(); */
     	//获取该型号所有的故障信息内容
     	$gu_sql='select a.model_content as cont from h_repair_mobile a where a.mobile_id='.$mobileid;
     	$gu_querys=$this->db->query($gu_sql);
     	$gu_res=$gu_querys->result_array();
     	$data['gu']=json_decode($gu_res[0]['cont']);
     	//获取故障名字
     	$text_sql="select a.permit_id as id, a.permit_name as name from h_repair_permit a where a.permit_status=1";
     	$text_querys=$this->db->query($text_sql);
     	$data['text']=$text_querys->result_array();
     	return $data;
     }
     /**
      * 修改单条订单记录
      */
     function editorder(){
     	$bonus=$paysta='';
     	switch ($this->status){
     		case '1':$bonus=intval($this->bonus);$paysta=0;break;
     		case '0':$bonus=0;$paysta=0;break;
     		case '2':$bonus=intval($this->bonus);$paysta=0;break;
     		case '3';$bonus=intval($this->bonus);$paysta=$this->paysta;break;
     		case '4':$bonus=0;$paysta=1;break;
     	}
     	$data=array(
     			'repair_name'=>$this->name,
     			'repair_phone'=>$this->phones,
     			'repair_address'=>$this->adr,
     			'repair_content'=>$this->con,
     			'repair_contentid'=>$this->conid,
     			'repair_discount'=>$this->discount*100,
     			'repair_bonus'=>$bonus*100,
     			'repair_money'=>$this->money*100,
     			'repair_express'=>$this->express,
     			'repair_num'=>$this->num,
     			'repair_comment'=>$this->comment,
     			'repair_updatetime'=>time(),
     			'repair_status'=>$this->status,
     			'repair_other'=>$this->other,
     			'repair_paystatus'=>$paysta
     	);
     	$where=array('repair_id'=>$this->id);
     	$query=$this->db->update('h_repair',$data,$where);
     	$row=$this->db->affected_rows();
     	if($row==1){
     		if($this->status=='0' ||  $this->status=='4' ){
     			$use_sql='update h_wxuser set wx_freeze_balance=wx_freeze_balance-'.intval($this->bonus).'*100 where wx_id='.$this->wxid;
     			$query=$this->db->query($use_sql);
     			$rows=$this->db->affected_rows();     			
     			if($rows==1){
					return true;
     			}else{
     				return false;
     			}
     		}else{
     			return $this->status;
     		}
     	}else{
     		return false;
     	}     	
     }
     /**
      * 推送信息
      */
     function sendMess(){
     	$wxid=$this->wxid;
     	$sql='select a.wx_openid as openid from h_wxuser a where a.wx_id='.$wxid;
     	$querys=$this->db->query($sql);
     	$data=$querys->result_array();
     	$openid=$data['0']['openid'];
     	//$openid='o9nlJt2dHqi7vsNZKmPrXE5sAIz8';
     	return $openid;
     }
     /**
      * 获取手机维修一级菜单
      */
     function getMenu(){
     	switch ($this->state){
     		case '1':
     			$sql='select a.model_id as id,a.model_name as name,a.model_jointime 
     					as jointime from h_repair_model as a where a.model_status=1 
     				    limit '.$this->page.',10';
     			$sum_sql='select count(*) as sum from h_repair_model as a where a.model_status=1';
     			break;
     		case '2':
     			if(isset($this->mid) && $this->mid!='' && $this->mid!='undefined'&& $this->mid!="null"){
     				$where =' and a.model_pid='.$this->mid;
     			}else{
     				$where='';
     			}
      			$sql='select a.permit_id as pid,a.permit_name as pname,b.model_id as mid,
     				b.model_name as mname,a.permit_jointime as jointime 
     				from h_repair_permit as a left join h_repair_model as b
     				on a.model_pid=b.model_id where b.model_status=1 and 
     				a.permit_status=1 '.$where.' order by b.model_id limit '.$this->page.',10';
     			$sum_sql='select count(*) as sum from h_repair_permit as a left join h_repair_model as b
     				on a.model_pid=b.model_id where b.model_status=1 and a.permit_status=1 '.$where.' order by b.model_id';
     			break;
     	}
     	$querys=$this->db->query($sql);
     	$data['list']=$querys->result_array();
     	$sum_query=$this->db->query($sum_sql);
     	if(!$sum_query || $sum_query->num_rows < 1){
     		return false;
     	}
     	$data['num']=$sum_query->result_array();
     	if($data){
     		$data['state']=$this->state;
     		return $data;
     	}else{
     		return false;
     	}
     }
     /**
      * 保存添加的菜单内容
      */
     function saveAdd(){
     	switch ($this->state){
     		case '1':
     			$tb='h_repair_model';
     			$data=array('model_name'=>$this->name,'model_jointime'=>time(),'model_status'=>1);
     			break;
     		case '2':
     			$tb='h_repair_permit';
     			$data=array(
     					'model_pid'=>$this->mid,
     					'permit_name'=>$this->name,
     					'permit_status'=>1,
     					'permit_jointime'=>time()
     			);
     			break;
     	}
     	$query=$this->db->insert($tb,$data);
     	$row=$this->db->affected_rows();
     	if($row==1){
     		return true;
     	}else{
     		return false;
     	}
     }
     /**
      * 修改维修菜单内容
      */
     function saveUpMenu(){
     	switch ($this->state){
     		case '1':
     			$tb='h_repair_model';
     			$data=array('model_name'=>$this->name,'model_updatetime'=>time());
     			$where=array('model_id'=>$this->id);
     			break;
     		case '2':
     			$tb='h_repair_permit';
     			$data=array('permit_name'=>$this->name,'permit_updatetime'=>time());
     			$where=array('permit_id'=>$this->id);
     			break;
     	}
     	$query=$this->db->update($tb,$data,$where);
     	$row=$this->db->affected_rows();
     	if($row==1){
     		return true;
     	}else{
     		return false;
     	}
     }
     /**
      * 删除维修菜单内容
      */
     function delMenu(){
     	switch ($this->state){
     		case '1':
     			$tb='h_repair_model';
     			$where=array('model_id'=>$this->id);
     			break;
     		case '2':
     			$tb='h_repair_permit';
     			$where=array('permit_id'=>$this->id);
     			break;
     	}
     	$query=$this->db->delete($tb,$where);
     	$row=$this->db->affected_rows();
     	if($row==1){
     		return true;
     	}else{
     		return false;
     	}
     }
     /**
      * 获取一类菜单故障内容
      */
     function getFault(){
     	$sql='select a.model_id as id,a.model_name as name from h_repair_model as a where a.model_status=1';
     	$query=$this->db->query($sql);
     	$data=$query->result_array();
     	if($data){
     		return $data;
     	}else{
     		return false;
     	}
     }
     /**
      * 获取二类菜单故障内容
      */
     function getSeFault(){
     	$sql='select a.permit_id as id,a.permit_name as name  
     				from h_repair_permit as a left join h_repair_model as b
     				on a.model_pid=b.model_id where b.model_status=1 and 
     				a.permit_status=1 and a.model_pid='.$this->id;
     	$query=$this->db->query($sql);
     	$data=$query->result_array();
     	if($data){
     		return $data;
     	}else{
     		return false;
     	}
     }
}