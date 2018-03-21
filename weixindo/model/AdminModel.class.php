<?php
/*
 * 管理员模块
 * 
 */
class AdminModel extends MySQL{
    /*
     *功能描述:获取用户的用户组
     */
    public function check_user_type($id){
        if($id!=''){
            $sql='select power_type from h_admin where id="'.$id.'"';
        }else{
            return FALSE;
        }
        if(!$res=$this->Query($sql)){
            return FALSE;
        }else{
            $data=$this->RowArray();
            return $data;
        }
    }
    /**
     * 检测回收商是否存在
     */
    function  CheckAdmin($username){
        if(empty($username)){
            return false;
        }
        $sql='select id from h_admin  where name='.$username.' and status=1 and power_type=2';
        $result=$this->Query($sql);
        $row=$this->Row();
        if($row === false){
              return false;
        }else{
              $url='http://wx.recytl.com/index.php/userlogin/login/Viewlogin?username='.$username;
              return array('url'=>'<a href="'.$url.'">修改地址</a>');
        }
    }
    
}