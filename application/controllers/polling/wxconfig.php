<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class  wxconfig extends  CI_Controller{
    
    function token(){
        $this->load->model('common/wxconfig_model');
        $resp=$this->wxconfig_model->getwxconfig();
        if($resp === true){
            echo date('Y-m-d H:i:s').'更新成功';
        }else{
            echo date('Y-m-d H:i:s').'更新失败';
        }
    }
    
    
}