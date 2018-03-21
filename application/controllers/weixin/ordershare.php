<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ordershare extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('common/wxcode_model','',TRUE);
    }
    /*
     * 功能描述:每周分享
     */
    public function index(){
        exit();
        if(empty($_GET['code'])){
	        $this->load->view('exception/notopenwx');
	        return '';
	    }else{
	       $code=$_GET['code'];	
	       $shareorderid=$_GET['shareid'];      
	    }
		if(!is_numeric($shareorderid)){
			exit();
		}
	    $openid=$this->wxcode_model->getOpenid($code);
	    $this->load->model('weixin/wxuser_model');
	    $data['appid']=$this->config->item('APPID');
	    $data['shareweekurl']=urlencode($this->config->item('ordershare')).'?shareid='.$shareorderid;
	    $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
	    //校验用户是否存在该类型的代金券
	    $this->load->model('weixin/shareorder_model');
	    $res_isweek=$this->shareorder_model->check_weekshare($shareorderid);
		$this->load->model('weixin/wxvoucher_model');
		 $data['usermessage']=array(
				'0'=>array(
					'share_content'=>'抢到回收通现金券，妈妈再也不用担心我的旧衣服了！'
				),
				'1'=>array(
					'share_content'=>'卖掉旧衣服,买新的！'
				),
				'2'=>array(
					'share_content'=>'现金卷,抢枪抢'
				),
				'3'=>array(
					'share_content'=>'回收手机不?'
				),
				'4'=>array(
					'share_content'=>'我家里有好多的衣服呢!'
				),
				'5'=>array(
					'share_content'=>'我要卖了 买新的'
				)
		);
	    if($res_isweek !==false && $res_isweek != '0'){
	        //有剩余名额的时候获取现有的已经领取名额
	        /*有可能出现超额领取  受限于硬件 暂不考虑*/
	        if($res_isweek['0']['order_number'] <= $this->config->item('ORDERNUM')){
	            $arr_week_ascription=explode(',',$res_isweek['0']['order_ascription']);
	        }else{
	            $data['isquota']='-1';
				$data['voucherlist']['voucherlist']=$this->wxvoucher_model->getordersharevoucher($shareorderid);
	            $this->load->view('weixin/weekshare',$data);
	            return '';
	        }
	    }else{
	        $this->load->view('exception/busy');
	    }	    
	   
	    //判断当前用户是否已经领取过
        if($res_isweek != '0' ){
            if(in_array($openid,$arr_week_ascription)){
                $data['isquota']='0';
                //获取当前分享记录下的领取记录
                $data['voucherlist']['pri']='0';
                $data['voucherlist']['voucherlist']=$this->wxvoucher_model->getordersharevoucher($shareorderid);
                $this->load->view('weixin/weekshare',$data);
                return '';
            }
        } 
	    $userinfo=$this->wxuser_model->check_wxuser_exist($openid);
	    $this->load->model('weixin/wxvoucher_model');
	    if($userinfo =='0'){
	        //当不存在用户时添加用户
	        //$add_res=$this->wxuser_model->addwxuseruser($openid);
	        //if($add_res){
	             $data['isquota']='1';
	             $data['voucherlist']=$this->check_user_weekshare($openid,$shareorderid,'4');
	             $this->load->view('weixin/weekshare',$data);
	        //}else{
	            //$this->load->view('exception/busy');
	        //}
	    }else{
	        //当存在用户
	        $data['isquota']='1';
	        $data['voucherlist']=$this->check_user_weekshare($openid,$shareorderid,'4');
	        $this->load->view('weixin/weekshare',$data);
	    }
    }
		/*
		 *功能描述:添加每周分享
		 *校验每周分享类型现金券是否已经领取
		   未领取 添加 如果已经领取 返回领取信息
		 *$userinfo,用户信息$fxopneid,分享现金券的用户opneid $voucherid现金券类型
		 **/
	public function check_user_weekshare($openid,$shareorderid,$voucherid){
	   
		//获取现金券的类型信息
		$voucherObj=$this->wxvoucher_model->get_voucherByTyid($voucherid);
		if($voucherObj === false){
		   return false;
		}
		
		//添加现金券
		$voucherObj['share_orderid']=$shareorderid;
		$vlres=$this->wxvoucher_model->insert_voucherLog($voucherObj,$openid);
		if($vlres === false){
		   return false;
		}
		
		//更新用户的现金券
		$up_res=$this->wxvoucher_model->user_update_voucher($vlres['id'],$openid);
		if($up_res === false){
		   return false;
		}
		//更新剩余名额  添加到分享记录中
    	$res_upweek=$this->shareorder_model->update_ordershare($openid,$shareorderid);
		 if($res_upweek === false){
		    return false;
		 }
		 //获取当前分享记录下的领取记录
		 $vlres['voucherlist']=$this->wxvoucher_model->getordersharevoucher($shareorderid);
		 if($vlres['voucherlist']!== false){
		    return  $vlres;
		 }else{
		    return false;
		 }
	}
    
}
/* End of file Weekshare.php */
/* Location: ./application/controllers/weixin/Weekshare.php */