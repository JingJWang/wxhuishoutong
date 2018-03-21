<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Wxvoucher_model extends CI_Model {
    
    private $wxuser='h_wxuser'; //微信用户表    
    private $voucher='h_voucher_log';//微信现金券表
    private $vouchertype='h_voucher';//现金券类型表
    /*
     * 功能描述:获取用户是否是第一次提交订单
     */
    public  function one_submit_order($voucher,$openid){
        $sql='select id from '.$this->voucher.' where openid="'.$openid.'" and log_type='.$voucher.' limit 1';
        $arr_res=$this->db->customize_query($sql);
        if($arr_res || $arr_res !='0'){
            //当前已经领取过首次报单现金券
            return '1';
        }else{
            return $this->get_voucher($voucher,$openid);
        }
    }
    /*
     * 功能描述:获取用户现有的代金券
     */
    private function get_voucher($voucher,$openid){
        $sql='select voucher from '.$this->wxuser.' where wx_openid="'.$openid.'"';
        $arr_voucherdata=$this->db->customize_query($sql);
        if($arr_voucherdata === false){
            return false;
        }else{     
            $vou=$arr_voucherdata == '0'? 0 :$arr_voucherdata['0']['voucher'];
            return $this->update_voucher($vou,$voucher,$openid);
        }
    }
    /*
     *功能描述:给用户添加代金券
     */
    private function update_voucher($now_voucher,$voucher,$openid){
        $voucherObj=$this->get_voucherByTyid($voucher);        
        $vlres=$this->insert_voucherLog($voucherObj,$openid);
        if($voucher && $vlres){
            $voucher=trim($now_voucher.','.$vlres,',');
            $sql='update '.$this->wxuser.' set voucher="'.$voucher.'" where wx_openid="'.$openid.'"';
            $bool_res=$this->db->query($sql);
            if($bool_res){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    /*
     *功能描述: 根据代金券的类型：1,2,3，查询有效代金券的信息：金额，有效期
     */
    public function get_voucherByTyid($voucherId){
        $sql='select * from '.$this->vouchertype.' where status="1" and voucher_tyid="'.$voucherId.'"';
        $arr_voucherdata=$this->db->customize_query($sql);
        if($arr_voucherdata && $arr_voucherdata != '0'){
            return $arr_voucherdata[0];
        }else{
            return false;
        }
    }
    /*
     *根据代金券对象添加代金券日志
     *$obj,现金券类型 $openid 微信用户openid
     */
    public function insert_voucherLog($obj,$openid){
        $vouer_guid=md5(uniqid(rand()));    //代金券id
        $log_joindate=date("Y-m-d H:i:s");  //添加时间
        $log_type=$obj['voucher_tyid'];     //代金券类型
        //现金券为 每周分享 于 成交分享 随机取得现金券面值
        if($log_type==3||$log_type==4){
            $voucher_pic=$this->momeny();
        }else{
            $voucher_pic=$obj['voucher_pic'];//面值
        }
        //校验当传递过来分享着openid时加入分享着opneid
        if($log_type==4){
            $belong_orderid=$obj['share_orderid'];
        }else{
            $belong_orderid='0';
        }
        if($log_type==3){
            $provide_openid=$obj['share_weekid'];
        }else{
            $provide_openid='0';
        }
        //校验传递过来的分享订单id时 加入分享订单的id
        $log_exceed_time=time()+$obj['voucher_day']*24*3600;
        $log_exceed=date("Y-m-d H:i:s",$log_exceed_time);//过期时间
        $sql='insert into '.$this->voucher.'(vouer_guid,voucher_pic,log_joindate,log_type,openid,log_exceed,share_weekid,share_orderid)values
		("'.$vouer_guid.'","'.$voucher_pic.'","'.$log_joindate.'","'.$log_type.'","'.$openid.'","'.$log_exceed.'","'.$provide_openid.'","'.$belong_orderid.'")';
        $res=$this->db->query($sql);
        if($res){
            return $log_type==3||$log_type==4?$message=array('pri'=>$voucher_pic,'id'=>$this->db->insert_id()):$this->db->insert_id();
        }else{
            return false;
        }
    }
    /*
     * 功能描述:分享类型代金券算法
     */
    private function momeny(){
        $price_1=rand(1,1000);
        if($price_1>999){
            $price=rand(7,10);
        }else if($price_1>950){
            $price=rand(5, 6);
        }else{
            $price=rand(1,4);
        }
        return  $price;
    }
    /*
     * 功能描述:获取用户下的所有现金券
     */
    public function myvoucher($openid){
        $sql='select voucher,wx_freezedate from '.$this->wxuser.' where  wx_openid="'.$openid.'"';
        $res_wxuser=$this->db->customize_query($sql);
        if($res_wxuser !== false){
            $voucherid=$res_wxuser['0']['voucher'];
        }else{
            return $res_wxuser=='0'? '1' : false;
        }
        $voucher_sql0='select voucher_pic,log_joindate,log_voucher_status,log_exceed,log_type from h_voucher_log where log_voucher_status=1 and openid = "'.$openid.'" order by log_joindate desc';
        $voucher_res0=$this->db->customize_query($voucher_sql0);
        $voucher_sql1='select voucher_pic,log_joindate,log_voucher_status,log_exceed,log_type from h_voucher_log where log_voucher_status=2 and openid = "'.$openid.'" order by log_joindate desc';
        $voucher_res1=$this->db->customize_query($voucher_sql1);
		
        if($res_wxuser['0']['wx_freezedate'] == '0000-00-00 00:00:00' || time() > strtotime($res_wxuser['0']['wx_freezedate']) ){
			$freezedate='';
        }else{
			$freezetime=strtotime($res_wxuser['0']['wx_freezedate']);
            $freezedate=$this->lang->line('voucherinfo0').date('Y-m-d',$freezetime).$this->lang->line('voucherinfo1');
        }
        if($voucher_res0 !== false && $voucher_res1  !== false ){
            $data['use']=$voucher_res1;
            $data['notused']=$voucher_res0;
            $data['freezeinfo']=$freezedate;
            return $data;
        }else{
            return false;
        }       
    }
     /*
     * 功能描述:获取用户的现金券总额
     */
        public function getvouchernum($openid){
			$sql='select sum(voucher_pic) as picnum from `h_voucher_log` where openid="'.$openid.'"';
			$arr_voucherdata=$this->db->customize_query($sql);
			if($arr_voucherdata !== false){
			       $this->db->close();
				   return $arr_voucherdata;
			}else{
				   return false;
			}
		}
	
		/*
		 *获取当前用户的代金券 给用户添加现金券
		 *$voucher,现金券id $openid用户的openid
		 */
		public function user_update_voucher($voucher,$openid){
		    $sql0='select wx_id,voucher from h_wxuser where wx_openid="'.$openid.'"';
		    $data0=$this->db->customize_query($sql0);
		    $voucher=trim($data0[0]['voucher'].','.$voucher,',');
		    $sql1='update h_wxuser set voucher="'.$voucher.'" where wx_openid="'.$openid.'"';
		    $data1=$this->db->query($sql1);
		    if($data1 && $data0){
		        return true;
		    }else{
		        return false;
		    }
		}
		/*
		 * 功能描述:获取某一分享记录下的领取记录
		 */
		public function getweeksharevoucher($id){
		    $sql='select b.wx_img,b.wx_name,a.log_joindate,a.voucher_pic from '.$this->voucher.' as a left join '.$this->wxuser.' as b on a.openid=b.wx_openid where b.wx_status=1 and share_weekid='.$id.' order by a.voucher_pic desc,a.log_joindate desc';
		    $arr_voucherdata=$this->db->customize_query($sql);
		    if($arr_voucherdata !== false){
		        $this->db->close();
		        return $arr_voucherdata;
		    }else{
		        return false;
		    }
		}
		/*
		 * 功能描述:获取某一分享记录下的领取记录
		 */
		public function getordersharevoucher($id){
		    $sql='select b.wx_img,b.wx_name,a.log_joindate,a.voucher_pic from '.$this->voucher.' as a left join '.$this->wxuser.' as b on a.openid=b.wx_openid where b.wx_status=1 and share_orderid='.$id.' order by a.voucher_pic desc,a.log_joindate desc ';
		    $arr_voucherdata=$this->db->customize_query($sql);
		    if($arr_voucherdata !== false){
		        $this->db->close();
		        return $arr_voucherdata;
		    }else{
		        return false;
		    }
		}
}