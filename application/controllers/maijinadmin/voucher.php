<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:代金券控制层
 */
class Voucher extends CI_Controller {    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/voucher_model','',TRUE);
    }
    
    /**
     * @description 现金卷类型列表
     */
    function vouchertypelist(){
        $data['modellist'] = $_SESSION['modellist'];
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/coupontype');
        $this->load->view('maijinadmin/common/footer');
    }
    
    /**
     * 功能描述:查询所有的代金券
     */
    public function select_voucher(){
        $message=array();
        $data=$this->voucher_model->select_voucher();
        if($data!==false){
            if($data!='0'){
                $message=array('status'=>$this->lang->line('DOSUCCESS'),'num'=>$data['num'],'data'=>$data['data']);
            }else{
                $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('RESULTNULL'));
            }
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:修改代金券的金额
     */
    public function update_voucher(){
        $message=array();
        $data=$this->voucher_model->update_voucher($_POST);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('UPDATESUCCESS'));
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('UPDATEFAIL'));
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:根据id查询代金券信息
     */
    public function get_voucher_id(){
        $message=array();
        $data=$this->voucher_model->get_voucher_id($_POST['id']);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'num'=>$data['num'],'data'=>$data['data']);
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);
    }
}

?>