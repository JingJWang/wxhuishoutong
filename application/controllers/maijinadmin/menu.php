<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:菜单控制层
 */
class Menu extends CI_Controller {    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/menu_model','',TRUE);
        $this->load->model('common/wxcode_model','',TRUE);
    }
    /**
     * @description 菜单管理
     */
    function getmenulist(){        
        $data['modellist'] = $_SESSION['modellist'];
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/menu');
        $this->load->view('maijinadmin/common/footer');
    }    
    
    /**
     * 功能描述:获取菜单
     */
	public function getmenu_L(){		
	    $message=array();
        $data=$this->menu_model->getmenu_L();
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'num'=>$data['num'],'data'=>$data['data']);
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);
	}
	/**
	 * 功能描述:保存菜单，并发布
	 */
	public function addmenu_L(){
	    $message=array();
        $access_token=$this->wxcode_model->getAccessToken();
        $data=$this->menu_model->addmenu_L($_POST['menu'],$access_token);
        if($data!==false){
            $message=array('status'=>$this->lang->line('DOSUCCESS'),'info'=>$this->lang->line('PUBLISHSUCCESS'));
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('PUBLISHFAIL'));
        }
        echo json_encode($message);
	}
}

?>