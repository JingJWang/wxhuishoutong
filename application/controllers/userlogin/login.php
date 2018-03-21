<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class login extends CI_Controller {    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('userlogin/userlogin_model');
    }    
    //载入登陆页面
    public function index(){
        $this->load->view('login/index');
    }
    public function userlogin(){
       
        //校验必填选项是否为空
        if($this->input->post('code') == '' || $this->input->post('pwd') == '' || $this->input->post('name') == ''){
            $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('common_optionnull'),'url'=>'','data'=>'');
            echo json_encode($response);
            exit();
        }else{
            $name=$this->input->post('name');
            $pwd=$this->input->post('pwd');
        } 
        //校验验证码
        if($this->input->post('code',true) !=$_SESSION['hst_code'] && $this->input->post('code') != ' '){
            $response=array('status'=>$this->config->item('request_codefall'),'info'=>$this->lang->line('login_codeerror'),'url'=>'','data'=>'');
            echo json_encode($response);
        }else{
            $res_userlogin=$this->userlogin_model->check_user($name,$pwd);
            if($res_userlogin){
                $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>$_SESSION['loginokusrl'],'data'=>'');
            }else{
                $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('login_error'),'url'=>'','data'=>'');
            }
            echo json_encode($response);
            exit();
        }
    }
    /*
     * 功能描述:退出登陆
     */
    public   function loginout(){
        session_destroy();
        $response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'/index.php','data'=>'');
        echo json_encode($response);
        exit();
    }
    /*
     * 校验用户是否登陆
     */
    public function check_online(){
        if($_SESSION['loginok']=='1001'){
             
        }else{
             $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('login_error'),'url'=>'/','data'=>'');
             echo json_encode($response);
             exit();
        }
    }
    /*
     * 功能描述:用户信息
     */
    public function userinfo(){
        $this->check_online();
		$data=array('name'=>$_SESSION['name'],'address'=>$_SESSION['address'],'power_name'=>$_SESSION['power_name'],'pay_type'=>$_SESSION['pay_type']);
		$response=array('status'=>$this->config->item('request_succ'),'info'=>'','url'=>'','data'=>$data);
		echo json_encode($response);
		exit();
    }
    /**
     * 微信界面用户登录
     */
    function  Viewlogin(){
        $this->load->helper('array');
        $coulms=array('username');
        $data=elements($coulms, $this->input->get(), '');
        $this->load->view('login/Login',$data);
    }
    /**
     * 校验当前用户是否回收商 登陆状态
     */
    function  Checklogin(){
        $this->load->helper('array');
        $coulms=array('username','password');
        $data=elements($coulms, $this->input->post(), '');
        $this->load->database();
        $this->load->model('userlogin/userlogin_model');
        $id=$this->userlogin_model->CheckMerchant($data['username'],$data['password']);
        if(!is_numeric($id)){
            echo json_encode($id);
            exit();
        }
        $response=array('status'=>$this->config->item('request_succ'),'info'=>'',
                'url'=>'/index.php/cooperation/user/ViewEditaddress','data'=>'');
        echo json_encode($response);
        exit();
    }
}