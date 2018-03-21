<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
/*
 * 我的奖金模块
 */
class Repair_model extends CI_Model {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 数码产品-获取分类下的 品牌列表
     * @param       int     id  分类id
     * @return      成功返回array  | 失败返回  false
     */
    function  types_brandslist(){
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
    	$sum = substr($sum,0,strlen($sum)-1); ;
    	$sql='select brand_id as id,brand_name as name from h_brand
               where brand_status=1 and brand_id in ('.$sum.')and brand_id != 538 ';
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
    	$sql='select b.mobile_id as id,a.types_name as name from h_electronic_types as a
		    	right join h_repair_mobile as b on b.mobile_id= a.types_id
		    	where b.mobile_pid='.$this->brandid.' and a.types_status = 1
		    	and b.model_status=1 ';
    	$query=$this->db->query($sql);
    	if($query === false || $query->num_rows < 1 ){
    		return false;
    	}
    	$type=$query->result_array();
    	return $type;
    }
    /**
     * 手机故障选择获取选项
     */
    function getRepairList(){
    	$address='';
    	//获取大类名称
    	$sql='select a.model_name as mname,a.model_id as mid from h_repair_model a
			where a.model_status=1 ';
        $query=$this->db->query($sql);
        $model=$query->result_array();
        if($query === false && $query->num_rows < 1){
            return false;
        }
        //获取小类名称
        $smallsql='select a.permit_id as pid,a.permit_name as pname,b.model_id as mid,
     				b.model_name as mname,a.permit_jointime as jointime
     				from h_repair_permit as a left join h_repair_model as b
     				on a.model_pid=b.model_id where b.model_status=1 and
     				a.permit_status=1 ';
        $smallquery=$this->db->query($smallsql);
        $small=$smallquery->result_array();
        if($smallquery=== false && $smallquery->num_rows < 1){
            return false;
        }
        //获取该手机型号配置
        $res_sql='select mobile_pid as pid,mobile_id as mid,model_bond as bond,model_content as con
    	           from h_repair_mobile where mobile_id='.$this->mobile;
        $res_query=$this->db->query($res_sql);
        $res=$res_query->result_array();
        if($res_query=== false && $res_query->num_rows < 1){
            return false;
        }
        $con=json_decode($res[0]['con']);
        $sql_addr='select a.receive_province as pro,a.receive_city as city,a.receive_area as area,a.receive_details as det,
        		a.receive_name as name,a.receive_phone as phone,a.receive_county as coun
        		from h_wxuser_receiveinfo a where user_id='.$this->userid.' order by receive_status desc ';
        $query_addr=$this->db->query($sql_addr);
        $result_addr=$query_addr->result_array();
        $response=array('model'=>$model,'small'=>$small,'con'=>$con,'addr'=>$result_addr);
        return $response;
    }
    /**
     * 保存手机维修选项
     */
    function saveRepairPhone(){
    	/* //获取当天0点 和23点59分时间戳
    	$year = date("Y");
    	$month = date("m");
    	$day = date("d");
    	$start = mktime(0,0,0,$month,$day,$year);//当天开始时间戳
    	$end= mktime(23,59,59,$month,$day,$year);//当天结束时间戳 */
    	//获取本周的周一时间和周日时间
    	$sdefaultDate = date("Y-m-d");
    	//$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    	$first=1;
    	//获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    	$w=date('w',strtotime($sdefaultDate));
    	//获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    	$week_start=strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days');
    	//本周结束日期
    	$week_end=$week_start +604799;
    	
    	$num=$this->create_ordrenumber();
    	$sql='select model_bond as price from h_repair_mobile a where mobile_id='.$this->goodsid;
    	$query=$this->db->query($sql);
    	$model=$query->result_array();
    	if($model['0']['price']!=''){
    		$bonus=$model['0']['price'];
    	}else{
    		$bonus=0;
    	}
    	//查询当前用户下单的记录条数
     	$tiao_sql='select count(repair_orderid) as count from h_repair where
    		  repair_jointime >='.$week_start.' and repair_jointime<='.$week_end.' and 
    		  repair_wxid='.$_SESSION['userinfo']['user_id'];
    	$tiao_query=$this->db->query($tiao_sql);
    	$tiao_result=$tiao_query->result_array();
    	if($tiao_result['0']['count']<5){
    		$data=array(
    				'repair_orderid'=>$num,
    				'repair_goodsid'=>$this->goodsid,
    				'repair_phone'=>$this->phone,
    				'repair_goodsname'=>$this->goodsname,
    				'repair_name'=>$this->name,
    				'repair_address'=>$this->address,
    				'repair_contentid'=>$this->contentid,
    				'repair_content'=>$this->content,
    				'repair_bonus'=>$bonus,
    				'repair_discount'=>$this->discount,
    				'repair_money'=>$this->money,
    				'repair_wxid'=>$this->wxid,
    				'repair_jointime'=>time(),
    				'repair_other'=>$this->other,
    				'repair_paystatus'=>0,
    				'repair_status'=>1
    		);
    		$sql=$this->db->insert('h_repair',$data);
    		if($sql){
    			$use_sql='update h_wxuser set wx_freeze_balance=wx_freeze_balance+'.$bonus.' where wx_id='.$this->wxid;
    			$query=$this->db->query($use_sql);
    			$row=$this->db->affected_rows();
    			if($row==1){
    				$this->load->model('repair/repairhome_model');
    				$this->repairhome_model->wxid=$this->wxid;
    				$res=$this->repairhome_model->sendMess();
    				$this->load->model('common/wxcode_model');
    				$bmoney=$bonus/100;
    				if (isset($res) && $res!='') {
    					$temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2fview/repair/repairMage.html&response_type=code&scope=snsapi_base&state=#wechat_redirect';
    					$sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"你的手机维修订单已经下单成功",
				"description":"您的'.$this->goodsname.'手机维修订单已经提交，'.$bmoney.'元保障金已经放入您的个人中心，点击查看订单",
				"url":"http://wx.recytl.com/view/repair/repairform.html", "picurl":""}]}}';
    					$content = sprintf($sendtext,$res,$temp_url);
    					$response_wx=$this->wxcode_model->sendmessage($content);
    				}
    				return true;
    			}else{
    				return false;
    			}
    		}else{
    			return false;
    		}
    	}else{
    		return false;	
    	}    	
    }
    /**
     * 获取维修手机记录
     */
    function selectList() {
    	if($this->status==5)
    	{
    		$where='';
    	}else if($this->status==4){
    		$where=' and a.repair_status in(0,4)';
    	}else{
    		$where=' and a.repair_status='.$this->status;
    	}
    	$sql='select a.repair_id as id,a.repair_goodsname as goodsname,a.repair_jointime as jointime,
    		a.repair_status as status,a.repair_orderid as orderid,a.repair_other as other from h_repair a where
    		a.repair_wxid='.$this->wxid.$where.' order by id desc';
    	$query=$this->db->query($sql);
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 获取某一单维修记录详情
     */
    function selectDetail() {
    	if(isset($this->id)  && $this->id!=''){
    		$where= 'where a.repair_id='.$this->id;
    	}else if(isset($this->orderid)  && $this->orderid!=''){
    		$where= 'where a.repair_orderid='.$this->orderid;
    	}
    	
    	$sql='select a.repair_id as id,a.repair_status as status,a.repair_goodsname as goodsname,
    		a.repair_jointime as jointime,a.repair_content as content,a.repair_orderid as orderid,
    		a.repair_phone as phone,a.repair_address as address,a.repair_wxid as wxid,
    		a.repair_name as name,a.repair_discount as discount,a.repair_bonus as bonus,
    		a.repair_money as money,a.repair_express as express,a.repair_num as num,a.repair_other as other,
    		a.repair_comment as com,a.repair_paystatus as paysta from h_repair a '.$where;
    	$query=$this->db->query($sql);
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 取消订单
     */
    function cancelOrder(){
    	$sql='update h_repair set repair_status=0,repair_bonus=0 where repair_id='.$this->id;
    	$query=$this->db->query($sql);
		$row=$this->db->affected_rows();
		if($row==1){
			$use_sql='update h_wxuser set wx_freeze_balance=wx_freeze_balance-'.$this->money.' where wx_id='.$_SESSION['userinfo']['user_id'];
			$query=$this->db->query($use_sql);
			$row=$this->db->affected_rows();
			if($row==1){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
    /**
     * 手机维修添加快递单号
     */
    function saveOdd(){
    	$sql='select a.repair_status as status from h_repair a where repair_id='.$this->id;
    	$query=$this->db->query($sql);
    	$data=$query->result_array();
    	if($data['0']['status']==1){
    	 	$sql_status='update h_repair set repair_status=2, repair_express="'.
    		$this->express.'",repair_num="'.$this->num.'" where repair_id='.$this->id;
    	}
    	$query_status=$this->db->query($sql_status);
    	$row=$this->db->affected_rows();
    	if($row==1){
    		return true;
    	}else{
    		return false;
    	}
    }
    /**
     * 取消支付订单
     */
    /* function orderPay(){
    	$sql_status='update h_repair set repair_paystatus=0 where repair_id='.$this->id;
    	$query_status=$this->db->query($sql_status);
    	$row=$this->db->affected_rows();
    	if($row==1){
			return true;
    	}else{
    		return false;
    	}
    } */
    /**
     * 处理微信支付结果
     */
    function payWx(){
    	//读取用户信息
    	$orderinfo=$this->orderinfo;
    	//获取当前用户的保证金以及wxid
    	$this->id=$orderinfo['0']['id'];
    	$res=$this->selectDetail();
    	$money=$res['0']['bonus'];
    	$wxid=$res['0']['wxid'];
    	$this->db->trans_begin();
    	//修改订单状态
    	$this->db->update('h_repair',array('repair_updatetime'=>time(),'repair_paystatus'=>1),
    			array('repair_id'=>$orderinfo['0']['id']));
    	$row_order=$this->db->affected_rows();
    	if ($this->db->trans_status() === false || $row_order !=1){
    		$this->db->trans_rollback();
    		return false;
    	}else{
    		$this->db->trans_commit();
    		return  true;
    	}
    }
    /**
     *  生成订单订单编号
     * @return string
     */
    function create_ordrenumber(){
    	return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
}