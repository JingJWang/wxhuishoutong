<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Scancode_model extends CI_Model { 
       
    private $subscribelog='h_subscribe_log';//微信用户表
    /*
     * 扫码人员当天业绩
     */
    function getscancodolist($userid){
        $start=date("Y-m-d");
        $end=date("Y-m-d",strtotime("+1 day"));
        $subscribe_sql='select subscribe_openid,subscribe_img,subscribe_name,subscribe_jointime,
                        subscribe_type from '.$this->subscribelog.' where user_id='.$userid.
                        ' and (subscribe_type=1 or subscribe_type=2) and subscribe_jointime >="'.$start.'" 
                        and subscribe_jointime <="'.$end.'" group by subscribe_openid  order by subscribe_jointime ';
        $sub_userlist=$this->db->customize_query($subscribe_sql);
        if($sub_userlist == '0'){
            $data['sub']=0;
            $data['unsub']=0;
            return $data;
        }  
        $unsubscribe_sql='select subscribe_openid,subscribe_jointime from '.$this->subscribelog.'  
                        where  subscribe_openid in(select subscribe_openid from h_subscribe_log 
                        where user_id='.$userid.' and subscribe_type =1 and 
                        subscribe_jointime >="'.$start.'" and subscribe_jointime <="'.$end.'") 
                        and subscribe_type=-1 group by subscribe_openid';        
        $unsub_userlist=$this->db->customize_query($unsubscribe_sql);
        if($sub_userlist !== false && $unsub_userlist !== false){
            $data['sub']=$sub_userlist;
            $data['unsub']=$unsub_userlist;
            return $data;
        }else{
            return false;
        }
    }
    
}