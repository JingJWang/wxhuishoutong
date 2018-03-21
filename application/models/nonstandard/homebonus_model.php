<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
/*
 * 我的奖金模块
 */
class Homebonus_model extends CI_Model {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取每个订单的奖金明细
     */
    function bonusAudit(){
    	//当前页
    	$page=$this->page;
    	$where =' where c.wx_status = 1 and c.wx_member>0 and c.wx_expire>"'.
    	strtotime(date('Y-m-d 00:00:00')).'"';
    	  //" and gdeal_jointime<"'.strtotime(date('Y-m-d')).'"';
    	if($this->id!=''){
    		$where.='and b.wx_id ="'.$this->id.'"';
    	}
    	if($this->bonustatus!=''){
    		if($this->bonustatus==0){
    			$where.='';
    		}else{
    			$where.=' and a.gdeal_bonustatus ='.$this->bonustatus;
    		}
    	}else{
    		$where.=' and a.gdeal_bonustatus =2';
    	}
    	$sql='select a.gdeal_id as id,a.gdeal_userid as userid,a.gdeal_goodid as goodid,a.gdeal_goodsid as goodsid,
			a.gdeal_source as source,a.gdeal_goodname as goodname,a.gdeal_bonustatus as bonustatus,
			a.gdeal_bonus as bonus,b.wx_id as wxid,c.wx_regtime as regtime from h_goodsdeal_bonus as a
			left join h_wxuser_task as b on a.gdeal_invitation=b.center_extend_num
			left join h_wxuser as c on a.gdeal_userid=c.wx_id'.$where. ' order by a.gdeal_jointime desc limit '.$page.',10';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data['list']='';
    	}
        $data['list']=$query->result_array(); 
        $sum_sql='select count(a.gdeal_id) as num from h_goodsdeal_bonus as a
			left join h_wxuser_task as b on a.gdeal_invitation=b.center_extend_num
			left join h_wxuser as c on a.gdeal_userid=c.wx_id'.$where. ' order by a.gdeal_jointime';
        $sum_query=$this->db->query($sum_sql);
        if(!$sum_query || $sum_query->num_rows < 1){
        	return false;
        }
        $data['num']=$sum_query->result_array();
        return $data;
    }
    /**
     * 批量结算奖金审核 审核过后 所得的奖金要进入用户余额表
     */
    function bonusAuditUpdate(){
    // 获取批量需要修改的数据信息 不包括今天所得的奖金
	 	$sql = 'select a.*,b.wx_id as wxid from h_goodsdeal_bonus a left join h_wxuser_task as b ' . 'on b.center_extend_num=a.gdeal_invitation
    			where a.gdeal_bonustatus=2 and a.gdeal_jointime<="' . strtotime ( date ( 'Y-m-d', time () ) ) . '" and b.wx_id!=""';
		$query = $this->db->query ( $sql );
		if (! $query || $query->num_rows < 1) {
			return false;
		}else{
			$data = $query->result_array ();
			$this->db->trans_begin ();
			// 第一步先更改物品成交奖金记录表的状态
			$bon_sql = 'update h_goodsdeal_bonus set gdeal_bonustatus=1,gdeal_updatetime="'.time().'" where gdeal_bonustatus=2 ' . 'and gdeal_jointime<"' . strtotime ( date ( 'Y-m-d', time () ) ) . '"';
			$query = $this->db->query($bon_sql );
			$rowas=$this->db->affected_rows();
			$num = 0;
			$nonum=0;
			if ($rowas >= 1) {
				// 把已经结算的奖金保存到我的余额中
				foreach ( $data as $k => $v ) {
					$money = $v ['gdeal_bonus']; // 钱数
					$wx_id = $v ['wxid']; // 用户id
					//判断奖金大于0 才修改用户的余额  并统计修改个数
					if($money>0 && !empty($wx_id)){
				 		$up_sql = 'update h_wxuser set wx_balance=wx_balance+'.$money.'*100 where wx_id=' . $wx_id . ' and wx_member!=0 and wx_expire>"'.time().'"';
						$query = $this->db->query ( $up_sql );
						$row = $this->db->affected_rows ();
						if ($row == 1){
						    $num++;
						    continue;
			    		}else{
			    		    $this->db->trans_rollback();
			    		   return false;
			    		}
					}else{
						//统计没有奖金的交易记录个数
					    $this->db->trans_rollback();
						return false;
					}
				}
				if($num==$rowas){
				    $this->db->trans_commit();
				    return true;
				}
			}else {
			    $this->db->trans_rollback();
				return false;
			}
		}
    }
    /**
     * 获取单条需要修改的奖金信息
     */
    function bonusAuditEdit(){
    	$sql='select a.gdeal_id as id,a.gdeal_goodid as goodid,a.gdeal_goodname as goodname,a.gdeal_bonus as bonus,
    			a.gdeal_bonustatus as bonustatus,a.gdeal_userid as userid from h_goodsdeal_bonus as a
    			where gdeal_id='.$this->id;
    	$query=$this->db->query($sql);
	    if(!$query || $query->num_rows < 1){
	    	$data='';
	    }
	    $data=$query->result_array();
	    return $data;
    }
    /**
     * 保存单条需要修改的奖金信息
     */
    function bonusAuditSave(){
    	$data=$this->bonusAuditEdit();
    	$money=$data['0']['bonus'];//获取奖金金额
    	$wxuid=$this->wxuid;//获取奖金的用户id
    	 if($this->bonustatus==1 && $data['0']['bonustatus']==2){
    		$this->db->trans_begin();
    		//修改用户的奖金状态
    		$sqls='update h_goodsdeal_bonus set gdeal_bonustatus=1,gdeal_updatetime="'.time().'" where gdeal_id="'.$this->id.'"';
    		$querys=$this->db->query($sqls);
    		//修改用户表中的用户剩余金额
    		$wx_sql='update h_wxuser set wx_balance=wx_balance+'.$money.'*100 where wx_id="'.$wxuid.'"';
    		$query=$this->db->query($wx_sql);
    		if($query && $querys){
    			$this->db->trans_commit();
    			return true;
    		}else{
    			$this->db->trans_rollback();
    			return false;
    		}
    	} 
    }
	/**
	 * 获取奖励增加模块信息列表
	 */
    function bonusIncreaseList(){
    	$page=$this->page;
    	//获取会员的全部个人信息
    	$where ='where a.center_extend_num != "" and b.wx_member>0 and b.wx_mobile!="" and b.wx_expire>"'.time().'" and c.gdeal_bonustatus=1';
    	if($this->id!='' ){
    		$where.=' and a.wx_id="'.$this->id.'"';
    	}
    	if($this->status!='' && $this->status!=0){
    		$where.=' and b.wx_member='.$this->status;
    	}
    	if($this->start!='' && $this->end!=''){
    		$where.=' and center_invite_u>='.$this->start.' and center_invite_u<='.$this->end;
    	}else if($this->start!='' && $this->end==''){
    		$where.=' and center_invite_u>='.$this->start;
    	}else if($this->start=='' && $this->end!=''){
    		$where.=' and center_invite_u<='.$this->end;
    	}
    	if($this->summoney!=''){
    		$where.=' and c.wxbonus_allincome='.$this->summoney;
    	}
  		$sql='select a.wx_id,b.wx_mobile as mobile,a.center_invite_u as nums,sum(c.gdeal_bonus) as aincome,
  			b.wx_regtime as regtime,b.wx_member as memeber
			from h_wxuser_task a
			left join h_wxuser b  on a.wx_id=b.wx_id
			left join h_goodsdeal_bonus c on c.gdeal_invitation=a.center_extend_num
			'.$where.' group by a.wx_id limit '.$page.',10';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data['list']='';
    	}
    	$data['list']=$query->result_array();
    	$sum_sql='select count(distinct a.wx_id) as sum from h_wxuser_task a
					left join h_wxuser b  on a.wx_id=b.wx_id
					left join h_goodsdeal_bonus c on c.gdeal_invitation=a.center_extend_num
				'.$where;
    	$sum_query=$this->db->query($sum_sql);
    	if(!$sum_query || $sum_query->num_rows < 1){
    		return false;
    	}
    	$data['num']=$sum_query->result_array();
    	return $data;
    }
    /**
     * 获取排行榜审核列表
     */
    function rankingAuditList(){
    	 //获取上周周一和上周周末
	 	$weekfirst=strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"))));
		$weeklast=strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"))));
        $sql='select  c.wx_id,b.wx_mobile as mobile, d.logbonus_money,d.logbonus_jointime,
                sum(IF(gdeal_bonustatus=1,gdeal_bonus,0)) as "ysum",
                sum(IF(gdeal_bonustatus=2,gdeal_bonus,0)) as "wsum",
                (select sum(h.gdeal_bonus) from h_goodsdeal_bonus h where h.gdeal_invitation=a.gdeal_invitation) as total
                from  h_goodsdeal_bonus a 
                left join h_wxuser_task c on a.gdeal_invitation=c.center_extend_num 
                left join h_wxuser b on c.wx_id=b.wx_id 
                left join h_log_bonus d on  d.logbonus_mobile = b.wx_mobile and d.logbonus_start>='.$weekfirst.' and d.logbonus_end<='.$weeklast.'
                where gdeal_bonustatus is not null
                and (case when gdeal_bonustatus=1 and gdeal_updatetime between '.$weekfirst.' and '.$weeklast.' 
                then 1 when gdeal_bonustatus=2 and gdeal_jointime between '.$weekfirst.' and '.$weeklast.' then 1 else 0 end)=1
                group by gdeal_invitation order by d.logbonus_jointime desc limit 3' ;
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data='';
    	}
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 获取奖金审核比例商城列表
     */
    function bonusSetShop(){
    	$page=$this->page;
    	//获取会员的全部个人信息
    	$where ='where 1=1';
    	if($this->name!=''){
    		$where.=' and shop_name like "%'.$this->name.'%"';
    	}
    	$sql='select a.shop_id as id,a.shop_name as name,a.shop_goodid as goodid,
    		a.shop_type as type,a.shop_value as value,a.shop_status as status from h_shop_bonus 
    		as a '.$where.' order by shop_jointime desc limit '.$page.',10';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data['list']='';
    	}
    	$data['list']=$query->result_array();
    	$sum_sql='select count(a.shop_goodid) as num from h_shop_bonus a '.$where.' order by shop_jointime desc';
    	$sum_query=$this->db->query($sum_sql);
    	if(!$sum_query || $sum_query->num_rows < 1){
    		return false;
    	}
    	$data['num']=$sum_query->result_array();
    	return $data;
    }
    /**
     * 获取单条需要修改的奖金比例设置商城数据
     */
    function selectShopBouns(){
    	$id=$this->id;
    	$sql='select shop_id as id,shop_name as name,shop_goodid as goodid,
    		shop_type as type,shop_value as value from h_shop_bonus 
    		where shop_id='.$id;
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data='';
    	}
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 保存单条需要修改的奖金比例设置商城数据
     */
    function updateShopSave(){
    	$sql='update h_shop_bonus set shop_type="'.$this->type.'",shop_value="'.$this->value.'" where shop_id='.$this->id;
   		$query=$this->db->query($sql);
    	if($this->db->affected_rows() == 1 && $query){
            return true;
        }
        return false;  
    }
    /**
     * 获取奖金审核比例商城列表
     */
    function bonusSetOrder(){
    	$page=$this->page;
    	//获取会员的全部个人信息
    	$where ='where 1=1';
    	if($this->goodid!=''){
    		$where.=' and digital_goodid="'.$this->goodid.'"';
    	}
    	if($this->start!=''){
    		$where.=' and digital_start<="'.$this->start.'"';
    	}
    	if($this->end!=''){
    		$where.=' and digital_end>="'.$this->end.'"';
    	}
    	$sql='select a.digital_id as id,a.digital_goodid as goodid,a.digital_goodname as goodname,a.digital_start as start,
    		a.digital_end as end,a.digital_type as type,a.digital_value as value,
    		a.digital_status as status from h_digital_bonus
    		as a '.$where.' order by digital_jointime desc limit '.$page.',10';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data['list']='';
    	}
    	$data['list']=$query->result_array();
    	$sum_sql='select count(digital_goodid) as num from h_digital_bonus a '.$where.' order by digital_jointime desc';
    	$sum_query=$this->db->query($sum_sql);
    	if(!$sum_query || $sum_query->num_rows < 1){
    		return false;
    	}
    	$data['num']=$sum_query->result_array();
    	return $data;
    }
    /**
     * 获取商城产品列表
     */
    function shoplist(){
    	$sql='select a.goods_id as id,a.goods_name as name from h_shop_goods a where a.goods_status=1';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data='';
    	}
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 保存商城奖金设置
     */
    function saveShopBonus(){
    	$data=array(
    			'shop_name'=>$this->name,
    			'shop_goodid'=>$this->id,
    			'shop_type'=>$this->type,
    			'shop_value'=>$this->ordervalue,
    			'shop_jointime'=>time(),
    			'shop_status'=>1
    	);
    	$sql='select shop_name from h_shop_bonus where shop_goodid='.$this->id;
    	$query=$this->db->query($sql);
    	if($query->num_rows<1){
    		$sql=$this->db->insert('h_shop_bonus',$data);
    		if($sql){
    			return true;
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
    /**
     * 获取回收产品列表
     */
    function orderlist(){
    	$sql='select a.types_id as id,a.types_name as name from h_electronic_types a where a.types_status=1';
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data='';
    	}
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 保存回收奖金设置
     */
    function saveOrderBonus(){
    	if(isset($this->start) && $this->start!=null && $this->start!='undefined'){
    		$start = $this->start;
    	}else{
    		$start = '' ;
    	}
    	if(isset($this->end) && $this->end!=null && $this->end!='undefined'){
    		$end = $this->end;
    	} else{
    		$end = '' ;
    	} 
    	$data=array(
    			'digital_goodid'=>$this->id,
    			'digital_goodname'=>$this->goodname,
    			'digital_start'=>$start,
    			'digital_end'=>$end,
    			'digital_type'=>$this->type,
    			'digital_value'=>$this->ordervalue,
    			'digital_jointime'=>time(),
    			'digital_status'=>1
    	);
    	if(isset($this->id) && $this->id!=''){
    		$sql='select digital_goodid as goodid from h_digital_bonus where digital_goodid='.$this->id;
    		$query=$this->db->query($sql);
    		if($query->num_rows==0){
    			$sql=$this->db->insert('h_digital_bonus',$data);
    			if($sql){
    				return true;
    			}else{
    				return false;
    			}
    		}else{
    			return false;
    		}
    	}else{
    		$sql=$this->db->insert('h_digital_bonus',$data);
    		if($sql){
    			return true;
    		}else{
    			return false;
    		}
    	}     	
    }
    /**
     * 获取单条需要修改的奖金比例设置商城数据
     */
    function selectOrderBouns(){
    	$sql='select digital_id as id,digital_start as start,digital_end as end,
    		digital_goodid as goodid,digital_type as type,digital_value as value 
    		from h_digital_bonus where digital_goodid='.$this->goodid;
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		$data='';
    	}
    	$data=$query->result_array();
    	return $data;
    }
    /**
     * 保存单条需要修改的奖金比例设置商城数据
     */
    function updateOrderSave(){
    	if($this->goodid!=''){
    		$sql='update h_digital_bonus set digital_type="'.$this->radiobutton.'",digital_value="'.$this->value.'"
    		,digital_uptime='.time().' where digital_goodid='.$this->goodid;
    	}elseif ($this->start!='' && $this->end!=''){
    		$sql='update h_digital_bonus set digital_type="'.$this->radiobutton.'",digital_value="'.$this->value.'"
    			,digital_start="'.$this->start.'",digital_end="'.$this->end.'",digital_uptime='.time().' 
    			where digital_goodid='.$this->goodid;
    	}
    	$query=$this->db->query($sql);
    	if($this->db->affected_rows() == 1 && $query){
    		return true;
    	}
    	return false;
    }
    /**
     * 随机发放奖励
     */
    function addBonusRand(){
    	$datas=array();
    	$temp=array();
    	//通过传递的价格区间 获取商城相应的物品
    	$where ='where a.goods_status=1 ';
    	if($this->startM!=''&& $this->endM!=''){
    		$startM=$this->startM*100;
    		$endM=$this->endM*100;
    		$where.=' and a.goods_ppri>="'.$startM.'" and a.goods_ppri<="'.$endM.'"';
    	}
    	$sql='select * from h_shop_goods as a '.$where; 
    	$query=$this->db->query($sql);
    	if(!$query || $query->num_rows < 1){
    		return false;
    	}else{
    		$data=$query->result_array();
    		$k=array_rand($data,1);
    		//获取随机物品的id和name
    		$id=$data[$k]['goods_id'];
    		$name=$data[$k]['goods_name'];
    		$price=$data[$k]['goods_ppri'];
    		//查询获取到的随机物品是否在商城奖金设置表中存在
    		$shop_sql='select * from h_shop_bonus a where a.shop_goodid='.$id;
    		$shop_query=$this->db->query($shop_sql);
    		if(!$shop_query || $shop_query->num_rows < 1){
    			$shop_data='';
    			$bonus='0.5';
    			$goodid=$id;
    			$name=$name;
    			$fixed=2;
    		}else{
    			$shop_data=$shop_query->result_array();
    			//获取随机物品在商城奖金设置表中的数据
    			$goodid=$shop_data['0']['shop_goodid'];
    			$name=$shop_data['0']['shop_name'];
    			$fixed=$shop_data['0']['shop_type'];
    			if($shop_data['0']['shop_type']==1){
    				$bonus=$shop_data['0']['shop_value']*$price;
    			}
    			if($shop_data['0']['shop_type']==2){
    				$bonus=$shop_data['0']['shop_value'];
    			}
    		}
    		//获取之前查询的用户的邀请码
    		$this->page=0;
    		$id_array=array();
    		//获取随机用户的用户id
    		$result=$this->bonusIncreaseList();
    		foreach($result['list'] as $k=>$v){
    			array_push($id_array,$v['wx_id']);
    		}
    		//通过上面的用户id 获取到随机用户的邀请码
    		$nums='';
    		foreach ($id_array as $k=>$v){
    			$user_sql='select center_extend_num from h_wxuser_task a where a.wx_id ='.$v;
    			$user_query=$this->db->query($user_sql);
    			$shop_data=$user_query->result_array();
    			$num='';
    			//获取到邀请码
    			if($shop_data['0']['center_extend_num']!='' && $shop_data['0']['center_extend_num']!=null){
    				$code=$shop_data['0']['center_extend_num'];
    				//订单编号
    				//$ordernum = $this->create_ordrenumber();
    				//$wx_id=rand(0,99999);
    				$phone = $this->randPhone();
    				$data=array(
    						'gdeal_userid'=>'201',
    						'gdeal_goodsid'=>$goodid,
    						'gdeal_goodname'=>$name,
    						'gdeal_goodid'=>'A',
    						'gdeal_fixed'=>$fixed,
    						'gdeal_bonus'=>$bonus,
    						'gdeal_invitation'=>$code,
    						'gdeal_source'=>1,
    						'gdeal_method'=>2,
    						'gdeal_bonustatus'=>1,
    						'gdeal_jointime'=>time(),
    						'gdeal_phone'=>$phone
    				);
    				$this->db->trans_begin();
    				//添加随机物品给随机用户
    				$this->db->insert('h_goodsdeal_bonus',$data);
    				$content=$this->db->affected_rows();
    				if($content==1){
    					//奖励发放后把相应的奖金给保存到该用户的名下
    					//通过邀请码获取用户的id
    					$in_sql='select wx_id as id from h_wxuser_task a where a.center_extend_num="'.$code.'"';
    					$in_query=$this->db->query($in_sql);
    					$in_data=$in_query->result_array();
    					//修改用户表中的余额
    					$user_sql='update h_wxuser set wx_balance=wx_balance+'.$bonus.'*100 where wx_id="'.$in_data[0]['id'].'"';
    					$user_query=$this->db->query($user_sql);
    					$contents=$this->db->affected_rows();
    					if($contents==1){
    						continue;
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
    		if($nums==count($id_array)){
    			$this->db->trans_commit();
    			return true;
    		}else{
    			$this->db->trans_rollback();
    			return false;
    		}
    	}
    }
    /**
     * 排行榜审核 发放奖励
     */
    function saveRandingaudit(){
        //获取上周周一和上周周末
        $weekfirst=strtotime(date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1-7,date("Y"))));
        $weeklast=strtotime(date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7-7,date("Y"))));
    	//1.先保存发放记录
    	$data=array(
    			'logbonus_mobile'=>$this->phone,
    			'logbonus_money'=>$this->value,
    			'logbonus_grantstatus'=>1,
    			'logbonus_status'=>1,
        	    'logbonus_start'=>$weekfirst,
        	    'logbonus_end'=>$weeklast,
    			'logbonus_jointime'=>time(),
                'logbonus_time'=>time()
    	);
    	$this->db->trans_begin();
    	$this->db->insert('h_log_bonus',$data);
    	$content=$this->db->affected_rows();
    	if($content==1){
    		//2.再修改用户的余额值 
    		//修改用户表余额
    		$user_sql='update h_wxuser set wx_balance=wx_balance+'.$this->value.'*100 where wx_mobile="'.$this->phone.'"';
    		$user_query=$this->db->query($user_sql);
    		$content=$this->db->affected_rows();
    		if($content==1){
    			$this->db->trans_commit();
    			return true; 
    		}else{
    			$this->db->trans_rollback();
    			return false;
    		}
    	}else{
    		$this->db->trans_rollback();
    		return false;
    	}
    } 
     /**
     * 随机生成手机号码
     */
    function randPhone(){
    	//匹配手机号的正则表达式 #^(13[0-9]|14[47]|15[0-35-9]|17[6-8]|18[0-9])([0-9]{8})$#
			$arr = array(
			    130,131,132,133,134,135,136,137,138,139,
			    144,147,
			    150,151,152,153,155,156,157,158,159,
			    176,177,178,
			    180,181,182,183,184,185,186,187,188,189,
			);
			for($i = 0; $i <1; $i++) {
			    $tmp = $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
			}
    	return $tmp;
    }
}