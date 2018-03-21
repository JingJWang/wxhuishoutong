<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Voucher extends CI_Controller {
    
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
	    }	    
	    $openid=$this->wxcode_model->getOpenid($code);//获取openid
	    $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
	    $this->load->model('weixin/wxuser_model','',TRUE);
	    $data['userinfo']=$this->wxuser_model->getuserinfo($openid);
	    $data['appid']=$this->config->item('APPID');
	    $this->load->model('weixin/wxvoucher_model','',TRUE);
	    $voucher_data=$this->wxvoucher_model->getvouchernum($openid);
	    $data['picnum']=$voucher_data['0']['picnum'];
	    //生成每周分享卷
	    $this->load->model('weixin/sharevoucher_model');
	    $weekid=$this->sharevoucher_model->addshareweek(3,$openid);
	    $data['shareweekurl']=urlencode($this->config->item('weekshare')).'?shareid='.$weekid;
	    if($weekid !== false){
	        $this->load->view('weixin/sendweekshare',$data);
	    }else{
	        $this->load->view('exception/notopenwx');
	    }
	}
	/*
	 * 功能描述:我的现金券列表
	 */
	public function myvoucherlist(){
	    if($this->input->get('code') === false ){
	        $this->load->view('exception/notopenwx');
	        return '';
	    }else{
	       $getcode=$this->input->get('code');	       
	    }
	    $openid=$this->wxcode_model->getOpenid($getcode);
	    $this->load->model('weixin/wxvoucher_model');
	    $voucherlist=$this->wxvoucher_model->myvoucher($openid);
	    if($voucherlist === false){
	         $this->load->view('exception/busy');
	    }else{
			 $this->load->view('weixin/myvoucher',$voucherlist);
	       
	    }
	}
	/*
	 * 现金券使用帮助
	 */
	public function voucherinfo(){
	    exit();
	    $this->load->model('weixin/wxinformation_model');
	    $info['hellpinfo']=$this->wxinformation_model->hellpinfo();
	    $this->load->view('weixin/voucherinfo',$info);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/Order.php */