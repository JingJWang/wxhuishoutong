<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class RA_Controller extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('auto/auth_model');
    }
    /*
     * 校验当前访问的URL是否需要校验
     */
     function checkauto(){
        
        //获取控制器
        $dir = $this->router->fetch_directory();
        //获取类
        $class = $this->router->fetch_class();
        //获取方法
        $method = $this->router->fetch_method();
        $page_url = "/index.php/".$dir.$class."/".$method; 
        $res_permit=$this->auth_model->get_url_permit_id($page_url);      
        if($res_permit == '0'){
            echo '不存在连接'.'-'.$page_url;
        }
       
        $res_check=$this->checkonline();
        /*if($res_login === false || $res_check === false){
         echo $data['messageinfo']=$this->lang->line('auto_fall');
         exit();
         //$this->load->view('exception/notopenwx');
        }*/
    }
    /*
     *校验是否登陆
     *
     */
    function checkonline(){
        if(empty($_SESSION['loginok']) || $_SESSION['loginok'] != '1001'){
                 $this->load->view('login/index');
                 $this->output->_display();
                 die();
        }else{
                $_SESSION['onlinetime']=time();
        }
    }
    
    
    
}