<?php
/*
 * 回收商模块
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_model extends CI_Model {
    
     
    
    /**
     * 获取当前回收商的门店详细
     * @param    int    $id   回收商id
     * @return   array        地址信息
     */
    function  GetMerchantInfo($id){
        $sql='select id,branch_name as  name,branch_address as addres,branch_province as province,
              branch_city as city ,branch_county as county,branch_hours as hours,
              branch_phone as phone,status from h_branch where admin_id='.$id;
        $query=$this->db->query($sql);
        $info=$this->db->fetch_query($query);
        if(is_null($info)){
            return array('0'=>array('id'=>'','name'=>'','addres'=>'','addres'=>'','city'=>'',
                             'county'=>'','hours'=>'','phone'=>'','status'=>''));
        }
        return $info;
    }
    /**
     * 修改当前的地址信息
     * @param    string   userid       用户id
     * @param    string   phone        手机号码
     * @param    string   province     省份
     * @param    string   city         市
     * @param    string   area         区
     * @param    string   latitude     纬度
     * @param    string   longitude    经度
     * @param    string   address      地址
     * @param    string   time         营业时间
     * @param    string   status       状态
     */
    function SaveAddres($data){
        if(empty($data['phone']) || empty($data['province']) || empty($_SESSION['userid']) ||
                empty($data['city']) || empty($data['latitude']) || empty($data['longitude']) ||
                empty($data['time']) || empty($data['status']) || empty($data['name'])){
            return  array('status'=>$this->config->item('request_fall'),'info'=>'选项不可为空!');
        }
        $addres=array(
                'admin_id'=>$_SESSION['userid'],
                'branch_address'=>empty($data['mapaddres']) ? $data['address']:$data['mapaddres'] ,
                'branch_name'=>$data['name'],
                'branch_province'=>$data['province'],
                'branch_city'=>$data['city'],
                'branch_county'=>isset($data['county']) ? $data['county'] : '',
                'branch_hours'=>$data['time'],
                'branch_lat'=>$data['latitude'],
                'branch_lon'=>$data['longitude'],
                'branch_phone'=>$data['phone'],
                'status'=>$data['status'],
        );
        $sql='select id from h_branch where  admin_id='.$_SESSION['userid'];
        $query=$this->db->query($sql);
        $info=$this->db->fetch_query($query);
        if(is_null($info)){
            $addres['branch_joindate']=date('Y-m-d H:i:s');
            $result=$this->db->insert('h_branch',$addres);
        }else{
            $addres['branch_lastdate']=date('Y-m-d H:i:s');
            $result=$this->db->update('h_branch',$addres,array('id'=>$info['0']['id']),array());
        }
        if($result){
            return  array('status'=>1,'info'=>'更新成功!');
        }else{
            return  array('status'=>0,'info'=>'更新失败!');
        }
    }
}