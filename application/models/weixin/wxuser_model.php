<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Wxuser_model extends CI_Model {
    
    private $table_wxuser='h_wxuser';
    
   /*
    * 
    * 功能描述:校验用户是否已经关注
    */
    public function check_wxuser_exist($openid){
       $sql='select wx_openid from h_wxuser where wx_openid="'.$openid.'"';
       $data=$this->db->customize_query($sql);
       return $data;
    }
     /*
     * 功能描述:获取用户的详细信息
     */
    public function getuserinfo($openid){
        $sql='select wx_openid,wx_name,wx_img from h_wxuser where wx_openid="'.$openid.'"';
        $data=$this->db->customize_query($sql);
        return $data;
        
    }
    /*
     * 功能描述:保存抢卷用户
     */
    public function addwxuseruser($openid){
        $sql='insert into '.$this->table_wxuser.'(wx_openid,wx_jointime,wx_status)values("'.$openid.'","'.date('Y-m-d H:i:s').'",0)';
        $res_add=$this->db->query($sql);
        if($res_add){
            return $this->db->insert_id();
        }else{
            return false;
        }
        
    }
}