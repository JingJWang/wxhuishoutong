<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Order extends CI_Controller {
        
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('common/wxcode_model','',TRUE);
        $this->load->model('weixin/wxorder_model');
        $this->load->model('weixin/wxinformation_model');
    }    
	/*
	 * 功能描述:报名表单
	 */
	public function index(){
	    exit();
	    if(empty($_GET['code'])){
	        $this->load->view('exception/notopenwx');
	        return '';
	    }else{
	       $code=$_GET['code'];	       
	    }   	    
	    $openid=$this->wxcode_model->getOpenid($code);
	    if(empty($openid)){
	        $this->load->view('exception/busy');
	        return '';
	    }else{	        
	        $message=$this->wxorder_model->getlastorder($openid);
	        if($message!='0'){
	            $orderdata=$message['0'];
	        }else{
	            $orderdata=array(
	                'weixin_id'=>$openid,
	                'order_mobile'=>'',
	                'order_province'=>'',
	                'order_city'=>'',
	                'order_county'=>'',
	                'order_address'=>''
	            );
	        }
	        $orderdata['standard_product']=$this->config->item('standard_product');
	        $this->load->view('weixin/order',$orderdata);
	    }
	}
	/*
	 * 功能描述:添加订单
	 */
	public  function addorder(){
	    exit();
		if($this->input->post('openid',true) && $this->input->post('phone',true) && $this->input->post('address',true) 
	       && $this->input->post('order_num',true)  && $this->input->post('order_num',true)  && $this->input->post('order_type',true) ){
	       $bool_addordre=$this->wxorder_model->addorder();
	       if($bool_addordre=='1'){
	           $orderdata['submitinfo'] = $this->lang->line('orderright_info');	           
	           $orderdata['listaddress']=$this->wxinformation_model->addresslist();
	           $this->load->view('weixin/orderright',$orderdata);
	       }else{
	           if($bool_addordre !== false){
	               $orderdata['submitinfo'] = $this->lang->line('orderright_info_one');
	               $orderdata['listaddress']=$this->wxinformation_model->addresslist();
	               $this->load->view('weixin/orderright',$orderdata);
	           }else{
				   $data['messageinfo']='订单出现异常!';
	               $this->load->view('exception/busy');
				   return '';
	           }
	       }
	    }else{
		   $data['messageinfo']='必填字段不可为空';
		   $this->load->view('exception/busy',$data);
		   return '';
        }
	}
	/**
	 * @description 用户 我的订单 ->订单列表
	 * @param string code 连接携带的参数 获取openid 
	 */
	public function orderlist(){
	    if($this->input->get('code') === false){
	        $this->load->view('exception/notopenwx');
	        return '';
	    }else{
	       $getcode=$this->input->get('code');	       
	    }
	    $openid=$this->wxcode_model->getOpenid($getcode);
	    $orderlist=$this->wxorder_model->get_orderlist($openid);
	    if($orderlist === false){
			$message=array();
			$message['messageinfo']=$this->lang->line('notorder');
	        $this->load->view('exception/busy',$message);
	    }else{
	        $orderlist['standard_product']=$this->config->item('standard_product');
	        $orderlist['ordertype_clothes']=$this->config->item('ordertype_clothes');
	        $orderlist['openid']=$openid;
	        $this->load->view('weixin/myorder',$orderlist);
	    }
	}
	/**
	 * 查看已经完成的订单 并且生成分享记录
	 */
	public function lookorder(){
	    exit();
	    $orderid=$this->uri->segment('4');
	    if(empty($orderid) || !is_numeric($orderid)){
	        $this->load->view('exception/notopenwx');
	    }else{	       	        
	        $data['appid']=$this->config->item('APPID');
	        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
	        $shareid=$this->wxorder_model->checkshareorder($orderid);
	        if($shareid === false){
	            $this->load->view('exception/busy');
	            return '';
	        }
	        $data['ordershare']=urlencode($this->config->item('ordershare')).'?shareid='.$shareid;	       	        
    	    $data['orderdata']=$this->wxorder_model->getorderinfo($orderid);
    	    if($data['orderdata'] !== false){
    	        $this->load->view('/weixin/lookorder',$data);
    	    }
	    }
	    
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/Order.php */