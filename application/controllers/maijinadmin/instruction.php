<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:使用说明控制层
 */
class Instruction extends CI_Controller {    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/instruction_model','',TRUE);
        $this->load->helper('security');
    }
    
    /**
     * @descript 获取使用说明管理
     */
    function getinstructionlist(){        
        $data['modellist'] = $_SESSION['modellist'];
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/instruction');
        $this->load->view('maijinadmin/common/footer');
    }
    /**
     * 功能描述:查询所有使用说明详情
     */
    public function get_instruction_list(){
        $message=array();
        $data=$this->instruction_model->get_instruction_list();
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
     * 功能描述:根据id获取使用说明详情
     */
    public function get_instruction(){
        $message=array();
        $data=$this->instruction_model->get_instruction($_POST['id']);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'num'=>$data['num'],'data'=>$data['data']);
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);  
    }
    /**
     * 功能描述:添加,修改使用说明信息
     */
    public function add_edit_instruction(){
        $message=array();        
        $id=$_POST['id'];
        $i_name=xss_clean(trim($_POST['i_name']));        
        $i_content=trim($_POST['i_content']);        
        $i_status=$_POST['i_status'];        
        $_POST['i_name']=$i_name;
        $_POST['i_content']=$i_content;        
        if($i_name!=''&&$i_content!=''&&$i_status!=''){
            if($id!=''){
                $data=$this->instruction_model->update_instruction($_POST);
                if($data!==false){
                    $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('UPDATESUCCESS'));
                }else{
                    $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('UPDATEFAIL'));
                } 
            }else{
                $data=$this->instruction_model->add_instruction($_POST);
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
     * 功能描述:禁用使用说明，设置为无效
     */
    public function delete_instruction(){
        $message=array();
        $data=$this->instruction_model->delete_instruction($_POST['id']);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('DELSUCCESS'));
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('DELFAIL'));
        }
        echo json_encode($message);
    }
}

?>