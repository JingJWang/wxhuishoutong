<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:微信粉丝层
 */
class Wxuser extends CI_Controller {    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/wxuser_model','',TRUE);
        $this->load->library('pagination');
    }
    /**
     * @description  
     */
    function wxuserlist(){
        $data['modellist'] = $_SESSION['modellist'];
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/weixinuser');
        $this->load->view('maijinadmin/common/footer');
    }
    
    /**
     * 功能描述:根据条件检索粉丝
     */
    public function select_weixin_user(){
        $message=array();
        $data=$this->wxuser_model->select_weixin_user($_POST);
        if($data!==false){
            if($data!='0'){
                $message=array('status'=>$this->lang->line('DOSUCCESS'),'pagetotal'=>$data['pagetotal'],'pagenum'=>$data['pagenum'],'todaytotal'=>$data['todaytotal'],'yesterdaytotal'=>$data['yesterdaytotal'],'monthtotal'=>$data['monthtotal'],'total_wx'=>$data['total_wx'],'todaytotal_wx'=>$data['todaytotal_wx'],'yesterdaytotal_wx'=>$data['yesterdaytotal_wx'],'monthtotal_wx'=>$data['monthtotal_wx'],'num'=>$data['num'],'data'=>$data['data']);
            }else{
                $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('RESULTNULL'));
            }
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);
    }
}

?>