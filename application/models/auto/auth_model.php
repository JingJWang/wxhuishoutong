<?php
/*
| 类名: AuthModel
|
| 说明: 根据用户请求的url,来判断请求用户是否有访问资源的权限. 
|
| 主要函数 : public function url_auth($user_id,$url).
| 参   数 : $user_id, int 用户的标识号. $url, string 请求的网页地址.
| 返   回 : Boolean.   
|     
| @author : zgmjiayou<1771600116@qq.com>
|
| @version : 1.0
|
|  
*/
class auth_model extends CI_Model{
    
    private $adminpermit='h_admin_permit'; //操作表
    
    
    /* 功能 : 获取url的permit_id值.
     * 
     * @param string ($url), 用户请求的url.
     * @return array, 返回url在库中的权限ID及权限许可组成的数组.
     */ 
    public function get_url_permit_id($url){
       $sql='select permit_id,permit_check,permit_request_type from '.$this->adminpermit.' where permit_url ="'.$url.'"';
       $data=$this->db->customize_query($sql);
        if($data ===  false){
            return false;
        }else{
            
            return $data;
        }
    }
    /*
     * 获取当前用户可访问的模块
     * 
     */
    
    
}
?>
