<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Cooperation extends CI_Controller {
    /*
     * 回收商默认进入 查看待处理的订单
     */
    public function index(){
        $this->load->view('cooperation/manage');
    }
    /*
     * 功能描述:查看订单状态
     */
    public  function handleorder(){
        exit();
        if($this->input->get('type') == '1' || $this->input->get('type') == '0' ){
            $type=$this->input->get('type');
        }else{
            
        }
        $this->load->database();
        $this->load->model('cooperation/cooporder_model');
        $order=$this->cooporder_model->order_list($type);
        if($order!==false){
            $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'','data'=>$order);
        }else{
            $response=array('status'=>$this->config->item('request_fall'),'info'=>'','url'=>'','data'=>'');
        }
        echo json_encode($response);
        exit();
    }
    /*
     * 功能描述:获取订单数目
     */
    public function  listnum(){ 
        exit();
        $this->load->database();
        $this->load->model('cooperation/cooporder_model');
        $num=$this->cooporder_model->order_num();
        if($num!==false){
            $num == '0' ? $datalist=array(array('num'=>0,array('num'=>0))):$datalist=$num;
            $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'','data'=>array('num'=>$datalist));
        }else{
            $response=array('status'=>$this->config->item('request_fall'),'info'=>'','url'=>'','data'=>'');
        }
        echo json_encode($response);
        exit();
    }
    /*
     * 功能描述:查询等待处理订单
     */
    public function selectedit(){
        exit();
       $_SESSION['editid']=$this->input->post('id');
       $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'/cooperation/edit-order.html','data'=>$_SESSION['editid']);
       echo json_encode($response);
       exit();
    }
    /*
     * 功能描述:获取待处理订单的详细信息
     */
    public  function  editorderinfo(){
        exit();
        $this->load->database();
        $this->load->model('cooperation/cooporder_model');
        $id=$_SESSION['editid'];
        $data=$this->cooporder_model->edituserinfo($id);
        $data['ordertype']=$this->config->item('standard_product');
        if($data!==false){
            $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'','data'=>$data);
        }else{
            $response=array('status'=>$this->config->item('request_fall'),'info'=>'','url'=>'','data'=>'');
        }
        echo json_encode($response);
        exit();
    }
    /*
     * 处理订单
     */
    public function ordersucc(){
        exit();
       if( $this->input->post('prirce') !='' && $this->input->post('openid') != '' && $this->input->post('order_weight')){
           $data['prirce']=$this->input->post('prirce');
           $data['voucher']=$this->input->post('voucher');
           $data['make']=$this->input->post('make');
           $data['orderid']=$_SESSION['editid'];
           $data['weight']=$this->input->post('order_weight');
           $data['openid']=$this->input->post('openid');
           $this->load->database();
           $this->load->model('cooperation/cooporder_model');
           $data=$this->cooporder_model->update_order($data);
           if($data === true){
               $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'/cooperation/orderback.html','data'=>'');
           }else{
               $response=array('status'=>$this->config->item('request_fall'),'info'=>$data['info'],'url'=>'','data'=>'');
           }
           echo json_encode($response);
           exit();
       }else{
           echo 'not null';
       }
               
    }
    public function savelookid(){
        exit();
        $_SESSION['lookid']=$this->uri->segment('4');
        $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'/cooperation/lookorder.html','data'=>$_SESSION['lookid']);
        echo json_encode($response);
        exit();
    }
    /*
     * 查看已经完成的订单
     */
    public function lookorder(){
        exit();
        if($_SESSION['lookid'] == ''){
            $this->load->view('exception/notopenwx');
        }else{
            $this->load->database();
            $this->load->model('weixin/wxorder_model');
            $orderdata=$this->wxorder_model->getorderinfo($_SESSION['lookid']);
            if($orderdata !== false && $orderdata !='0'){
                $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'','data'=>$orderdata);
                echo json_encode($response);
                exit();
            }
        }
         
    }
}