<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Userlogin_model extends CI_Model {
    //用户表
    private $useradmin='h_admin';
    //角色表
    private $userrole='h_admin_role';
    //权限表
    private $userpermit='h_admin_permit';
    //模块表
    private $usermodel='h_admin_model';
    /*
     * 校验用户是否有权登陆系统
     */
    public function  check_user($name,$pwd){
        $sql='select id,name,password,power_type,power_name,address,pay_type from '.$this->useradmin.' where name="'.$name.'"';
        $res_user=$this->db->customize_query($sql);
        if($res_user !== false && $res_user > 0 ){
            if($res_user[0]['password']== md5($pwd)){
                $_SESSION['loginok']='1001';
                $_SESSION['id']=$res_user[0]['id'];
                $_SESSION['name']=$res_user[0]['name'];
                $_SESSION['pwd']=$res_user[0]['password'];
                $_SESSION['power_type']=$res_user[0]['power_type'];
                $_SESSION['power_name']=$res_user[0]['power_name'];
                $_SESSION['pay_type']=$res_user[0]['pay_type'];
                $_SESSION['address']=$res_user[0]['address']; 
                $_SESSION['onlinetime']=time();
                $_SESSION['modellist']=array();
                $_SESSION['loginokusrl']='';
                return $this->getmodellist();                
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /*
     * 获取模块集合
     */
    function  getmodellist(){        
       $permit_sql='select role_permitid,role_weight,role_flag,role_defaulturl from '.$this->userrole.' where role_id='.$_SESSION['power_type'];
       $arr_permit=$this->db->customize_query($permit_sql);
       if($arr_permit === false || $arr_permit == '0'){
           return false;
       }else{
           $model_sql='select  b.model_name,b.model_url,b.model_isview from  '.$this->userpermit.' as a left join '.$this->usermodel.' 
                   as b on a.model_id=b.model_id   where  a.permit_id in('.$arr_permit['0']['role_permitid'].') and model_status=1 group by a.model_id';
           $arr_model=$this->db->customize_query($model_sql);
           if($arr_model === false || $arr_model == '0'){
               return false;
           }else{
               $_SESSION['modellist']=$arr_model;
               $_SESSION['role_weight']=$arr_permit['0']['role_weight'];
               $_SESSION['role_flag']=$arr_permit['0']['role_flag'];
               $_SESSION['loginokusrl']=$arr_permit['0']['role_defaulturl'];
               return true;
           }
       }
       
       
    }
    /**
     * 校验用户是否回收商  状态是否正常
     * @param      string   $username   用户名
     * @param      string   $password   密码
     * @return     bool                 结果
     */
    function  CheckMerchant($username,$password){
        $sql='select id  from '.$this->useradmin.' where name="'.$username.'" 
              and password="'.md5($password).'" and status=1 and power_type= 2';
        $result=$this->db->query($sql);
        $user=$this->db->fetch_query($result);
        if(is_null($user)){
            isset($_SESSION['loginnumber']) ? $_SESSION['loginnumber'] += $_SESSION['loginnumber'] + 1 :$_SESSION['loginnumber']=1;
            return array('status'=>$this->config->item('request_fall'),'info'=>'用户名或密码错误!');
        }
        $_SESSION['userid']=$user['0']['id'];
        return  $user['0']['id'];
    }
}