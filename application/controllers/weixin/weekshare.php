<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Weekshare extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('common/wxcode_model','',TRUE);
    }
	function test(){
		$data=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
		Universal::Output($this->config->item('request_succ'),'','',$data);
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
	       $weekid=$this->input->get('shareid',true);      
	    }
	    if(empty($weekid) ||!is_numeric($weekid)){
	        exit();
	    }
	    $openid=$this->wxcode_model->getOpenid($code);
	    $this->load->model('weixin/wxuser_model');
	    $data['appid']=$this->config->item('APPID');
	    $data['shareweekurl']=urlencode($this->config->item('weekshare')).'?shareid='.$weekid;
	    $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
	    //校验用户是否存在该类型的代金券
	    $this->load->model('weixin/sharevoucher_model');
	    $res_isweek=$this->sharevoucher_model->check_weekshare($weekid);
		$this->load->model('weixin/wxvoucher_model');
		//列表文字
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
	        if($res_isweek['0']['week_number'] <= $this->config->item('GUANZHUNUM')){
	            $arr_week_ascription=explode(',',$res_isweek['0']['week_ascription']);
	        }else{				
	            $data['isquota']='-1';
	            $data['voucherlist']['voucherlist']=$this->wxvoucher_model->getweeksharevoucher($weekid);				
	            $this->load->view('weixin/weekshare',$data);
	            return '';
	        }
	    }else{
	        $this->load->view('exception/busy');
			return '';
	    } 
	    //判断当前用户是否已经领取过
	    if($res_isweek != '0' ){
	       if(in_array($openid,$arr_week_ascription)){
	        $data['isquota']='0';
	        //获取当前分享记录下的领取记录
	        $data['voucherlist']['pri']='0';
	        $data['voucherlist']['voucherlist']=$this->wxvoucher_model->getweeksharevoucher($weekid);
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
	             $data['voucherlist']=$this->check_user_weekshare($openid,$weekid,'3');
	             $this->load->view('weixin/weekshare',$data);
	        //}else{
	            //$this->load->view('exception/busy');
	        //}
	    }else{
	        //当存在用户
	        $data['isquota']='1';
	        $data['voucherlist']=$this->check_user_weekshare($openid,$weekid,'3');
	        $this->load->view('weixin/weekshare',$data);
	    }
    }
		/*
		 *功能描述:添加每周分享
		 *校验每周分享类型现金券是否已经领取
		   未领取 添加 如果已经领取 返回领取信息
		 *$userinfo,用户信息$fxopneid,分享现金券的用户opneid $voucherid现金券类型
		 **/
	public function check_user_weekshare($openid,$weekid,$voucherid){
		//获取现金券的类型信息
		$voucherObj=$this->wxvoucher_model->get_voucherByTyid($voucherid);
		if($voucherObj === false){
		   return false;
		}
		//添加现金券
		$voucherObj['share_weekid']=$weekid;
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
    	$res_upweek=$this->sharevoucher_model->update_weekshare($openid,$weekid);
		 if($res_upweek === false){
		   return false;
		 }
		 //获取当前分享记录下的领取记录
		 $vlres['voucherlist']=$this->wxvoucher_model->getweeksharevoucher($weekid);
		 if($vlres['voucherlist']!== false){
		    return  $vlres;
		 }else{
		    return false;
		 }
	}
    
}
/* End of file Weekshare.php */
/* Location: ./application/controllers/weixin/Weekshare.php */