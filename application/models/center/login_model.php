<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Administrator
 * 
 */
class login_model extends  CI_Model{
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 查询是否存在当前用户
     * @param  string  name  用户名
     * @param  string  pwd   密码
     * @return 成功 返回 bool true || 失败返回 bool false
     */
    function checkUser(){
       $sql='select user_id,role_id,user_name,user_mobile,user_email,
             user_password,coop_number,user_status from h_admin_user where 
             user_mobile="'.$this->user.'" or user_email="'.$this->user.'"';
       $query=$this->db->query($sql);
       if($query->num_rows() < 1){
           $this->error_msg='账号不正确';
           return false;
       }       
       $data=$query->row_array();
       if($data['user_password'] != hash('sha256',$this->pwd)){
           $this->error_msg='密码不正确';
           return false;
       }
       return $data;
    }
    /**
     * 修改用户最后登录信息
     * @param   int   user_id      用户id
     * @param   int   user_loginip 用户登录IP
     * @return  修改成功  返回 bool true || 修改失败  返回 bool false
     */
    function  editLoginInfo(){
        $data=array('user_loginip'=>$this->user_loginip,'user_lasttime'=>time());
        $where=array('user_id'=>$this->user_id);
        $this->db->update('h_admin_user', $data, $where);
        if($this->db->affected_rows() != 1){
            $this->error_msg='更新用户最后登录信息没有成功';
            return  false;
        }
        return true;
    }
    /**
     * 读取用户权限 模块列表
     * @param   int   role 角色id
     * @return  array  返回当前用户的权限列表 | bool  返回 false
     */
    function getUserPermit(){
        //检验是否存在缓存结果
        /*$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->number=5;
        $this->zredis->selectDB();
        $this->zredis->key='system_rolemodel_'.$this->role;
        $cache=$this->zredis->existsKey();
        if($cache !== false){
            $response=json_decode($cache,true);
            return $response;
        }*/
       //如果 不存在缓存结果  读取角色的操作集合
       $sql='select role_name,role_permit from  h_power_role where role_id='.$this->role.' 
             and role_status=1';
       $query=$this->db->query($sql);
       if($query === false || $query->num_rows < 1){
           return false;
       }
       $role=$query->result_array();
       $_SESSION['user']['rolename']=$role['0']['role_name'];
       $user_permit=$role['0']['role_permit'];
       $model_list=$this->getPermit($user_permit);
       if($model_list === false){
           return false;
       }
       /*$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
       $this->zredis->number=5;
       $this->zredis->selectDB();
       $this->zredis->key='system_rolemodel_'.$this->role;
       $this->zredis->val=json_encode($model_list);
       $cache=$this->zredis->setKey();
       if($cache === false){
           return false;
       }*/
       return $model_list;
    }
    /**
     * 根据操作ID 获取模块id
     */
    function getPermit($permit){
        $permit=str_replace(array('[',']'),array('',''),$permit);
        $sql='select group_concat(model_id) as model_id from h_power_permit 
              where permit_id in ('.$permit.') and permit_status=1';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $permit=$query->result_array();
        //转为数组 去除重复的选项
        $model=explode(',',$permit['0']['model_id']);
        array_unique($model);
        $model=$this->getModel($model);
        return $model;
    }
    /**
     * 读取系统模块列表
     */
    function getModel($usermodel){
        //查询模块列表      
        $sql='select  model_id,model_fid,model_class,model_name,model_name,
              model_url from h_power_model where model_status=1 ';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $model=$query->result_array();
        $model_info=array();
        $model_list=array();
        //获取模块列表 以及 模块下的类型
        foreach ($model as $key=>$val){
            if($val['model_fid'] == 0){
                $model_info[$val['model_id']]=array('name'=>$val['model_name'],'class'=>$val['model_class'],'permit'=>'');
            }
            if($val['model_fid'] != 0){                
                $model_list[$val['model_fid']][]=array('id'=>$val['model_id'],'name'=>$val['model_name'],
                        'url'=>$val['model_url'],'class'=>$val['model_class']);                
            }
        }
        //获取每个模块下的操作
       $manage=array();
        foreach ($model_list as $key=>$val){
            foreach ($val as $k=>$v){
                if(in_array($v['id'],$usermodel)){
                    $model_info[$key]['permit'][]=array('id'=>$v['id'],'name'=>$v['name'],'class'=>$v['class'],'url'=>$v['url']);
                }
            }
        }
       //校验是否每个版块下都有 模块列表
       foreach ($model_info as $key=>$val){
           if(!is_array($val['permit']) ){
               unset($model_info[$key]);
           }
       }
       return $model_info;
    }
    /**
     * 校验用户是否在线
     */
    function isOnine(){
        if(!isset($_SESSION['user'])){
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/view/control/login.html');
        }
        if(!isset($_SESSION['user']['id'])){
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/view/control/login.html');
        }
    }
}