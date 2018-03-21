<?php
/**
 * 任务中心  第三方回调接口
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class  task extends CI_Controller{    
    /**
     * 借贷宝 回调接口
     * @param   data   手机号码   aes25加密
     * @param   code   邀请码  
     * @param   type   用户当前行为 1 位肖像 2为绑卡  
     * @param   iv     加密解密用的随机偏移量
     * @return   返回success
     */     
    function taskJdb(){
        //$iv = "83cfd573857854cd";
        $key = 'jiedaibao@yimingshenzhouaes001==';
        //$data='BnuxIlb%2FMiuXpL3Qi2SuiA%3D%3D';                
        $iv=$this->input->get('iv',true);
        $type=$this->input->get('type',true);
        $data=$this->input->get('data',true);
        $code=$this->input->get('code',true);
        if(empty($iv) || empty($key) || empty($data) || empty($code)){
            Universal::Output($this->config->item('request_fall'),'参数不可为空');
        }
        if(!is_numeric($type)){
             Universal::Output($this->config->item('request_fall'),'用户当前行为字段类型不正确');
        }        
        $data=urldecode($data);
        $data=base64_decode($data);
        $mobile=mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data,MCRYPT_MODE_CBC, $iv);        
        $log=date('Y-m-d H:i:s').'手机号码'.$mobile.'邀请码为'.$code.'行为'.$type.'偏移量'.$iv."\r\n";
        file_put_contents('logs/jdb.log',$log,FILE_APPEND);
        echo 'success';
    }    
    /**
     * 借贷宝 回调接口 用户完成任务
     * @param   data   手机号码   aes25加密
     * @param   code   邀请码  
     * @param   type   用户当前行为 1 位肖像 2为绑卡  
     * @param   iv     加密解密用的随机偏移量
     * @return   返回success
     */
    function taskFshJdb(){
        $key = 'jiedaibao@yimingzhitonkjaeskey==';
        $iv=$this->input->get('iv',true);
        $type=$this->input->get('type',true);
        $data=$this->input->get('data',true);
        $code=$this->input->get('code',true);
        if(empty($iv) || empty($key) || empty($data) || empty($code) || $code!='21C3V11'){
            $this->sexit('',$code,$type,$iv);
        }
        if(!is_numeric($type)){
            $this->sexit('',$code,$type,$iv);
        } 
        $data=urldecode($data);
        $data=base64_decode($data);
        $mobile=trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data,MCRYPT_MODE_CBC, $iv));
        if (!is_numeric($mobile)) {
            $this->sexit('',$code,$type,$iv);
        }
        $this->load->database();
        $this->load->model('task/otherget_model');
        $result = $this->otherget_model->taskFshJdb($mobile);
        if ($result==false) {
            $this->sexit($mobile,$code,$type,$iv);
        }
        $this->db->close();
        $this->sexit($mobile,$code,$type,$iv);
    }
    /**
     * 返回成功
     */
    function sexit($mobile,$code,$type,$iv){
        $log=date('Y-m-d H:i:s').'手机号码'.$mobile.'邀请码为'.$code.'行为'.$type.'偏移量'.$iv."\r\n";
        file_put_contents('logs/jdb.log',$log,FILE_APPEND);
        echo 'success';
        exit();
    }
}