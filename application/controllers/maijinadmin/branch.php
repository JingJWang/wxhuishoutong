<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:营业网点控制层
 */
class Branch extends CI_Controller {    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/branch_model','',TRUE);
        $this->load->helper('security');
    }
    
    /**
     * @descript 营业网点管理
     */
    function getbranchlist(){
        $data['modellist'] = $_SESSION['modellist'];
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/branch');
        $this->load->view('maijinadmin/common/footer');
    }
    /**
     * 功能描述:根据id获取营业网点信息
     */
    public function get_branch(){
        $message=array();        
        $data=$this->branch_model->get_branch($_POST['id']);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'num'=>$data['num'],'data'=>$data['data']);
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:添加修改营业网点信息
     */
    public function add_edit_branch(){
        $message=array();       
        $id=$_POST['id'];
        $b_time=$_POST['b_time'];
        $b_address=xss_clean(trim($_POST['b_address']));        
        $b_sort=$_POST['b_sort'];
        $b_status=$_POST['b_status'];        
        $_POST['b_address']=$b_address;        
        if($b_time!=''&&$b_address!=''&&$b_sort!=''&&$b_status!=''){
            $this->load->database();
            $this->load->model('branch_model','',TRUE);
            if($id!=''){
                $data=$this->branch_model->update_branch($_POST);
                if($data!==false){
                    $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('UPDATESUCCESS'));
                }else{
                    $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('UPDATEFAIL'));
                }
            }else{
                $data=$this->branch_model->add_branch($_POST);
                if($data!==false){
                    $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('ADDSUCCESS'));
                }else{
                    $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('ADDFAIL'));
                }
            }
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('OPTIONNONULL'));
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:禁用营业网点，设置为无效
     */
    public function delete_branch(){
        $message=array();
        $data=$this->branch_model->delete_branch($_POST['id']);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('DELSUCCESS'));
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('DELFAIL'));
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:查询所有营业网点
     */
    public function get_branch_list(){
        $message=array();   
	    $data=$this->branch_model->get_branch_list();
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
}

?>