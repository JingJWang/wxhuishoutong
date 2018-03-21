<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class scancode extends CI_Controller{ 
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('scancode/scancode_model');
    }    
    /*
     * 扫码人员业绩
     */
    function  viwelist(){
		exit();
        $userid =$this->uri->segment(4,0);
        if($userid == '0'){
            $userid=$_SESSION['id'];
        }
        if($userid== '' || !is_numeric($userid) || $_SESSION['loginok'] !== '1001'){
            $this->load->view('login/index');
            return '';
        }       
        $userlist=$this->scancode_model->getscancodolist($userid);
        if($userlist !== false){
            
            $data['unsub']=0;
            if(is_array($userlist['unsub'])){
                foreach ($userlist['unsub'] as $key=>$ubsub){
                    $data['unsub'] += 1;
                    $unsub[$ubsub['subscribe_openid']][]=$ubsub['subscribe_jointime'];
                }   
            }   
            $data['sub']=0;
            $data['cfsub']=0;
            if(is_array($userlist['sub'])){
                foreach ($userlist['sub'] as $key=>$sub){ 
                    switch ($sub['subscribe_type']){
                        case '1':   
                            $data['sub'] += 1;
                            if(array_key_exists($sub['subscribe_openid'], $unsub)){
                                $userlist['sub'][$key]['unsub']=-1;
                                $userlist['sub'][$key]['unsubinfo']=$unsub[$sub['subscribe_openid']]['0'];
                            }else{                                
                                $userlist['sub'][$key]['unsub']=1;                                
                            }
                            break;
                        case '2':  
                            $data['cfsub'] += 1;                          
                            if(array_key_exists($sub['subscribe_openid'], $unsub)){
                                $userlist['sub'][$key]['unsub']=-1;
                                $userlist['sub'][$key]['unsubinfo']=$unsub[$sub['subscribe_openid']]['0'];
                            }else{
                                $userlist['sub'][$key]['unsub']=1;
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
            $data['userlist']=$userlist['sub'];
            $this->load->view('scancode/viwelist',$data);
        }else{
            exit(0);
        }
    }
    
}
