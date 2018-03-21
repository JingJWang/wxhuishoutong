<?php
use Httpful\Request;
/**
 * 回收商(非标准化)
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cooperator extends CI_Controller{
    
    
    /**
     * 显示待审核列表
     */
     function cooperatorList(){
         $this->load->database();
         $page= $this->uri->segment(4,1);
         $per_page= $this->config->item('PAGENUM_BACK');
         $sql='select cooperator_mobile,cooperator_name,FROM_UNIXTIME
               (cooperator_join_time, "%Y-%m-%d %H:%i:%s") as time
               ,cooperator_userstatus from  h_cooperator_info where 
                cooperator_status=1 order by cooperator_join_time desc ';
         $query=$this->db->query($sql);
         $page=($page-1)*10;
         $sql_result='select cooperator_mobile,cooperator_name,FROM_UNIXTIME
               (cooperator_join_time, "%Y-%m-%d %H:%i:%s") as time
               ,cooperator_userstatus from  h_cooperator_info where 
                cooperator_status=1 order by cooperator_join_time desc limit '.$page.',10';
         $query_result=$this->db->query($sql_result);
         $view['coop']=$query_result->result_array();
         $view['status']=array('0'=>'待审核','1'=>'冻结','2'=>'未通过','3'=>'通过');
         $view['modellist'] = $_SESSION['modellist'];
         $this->load->library('pagination');
         $config['base_url'] = '/index.php/maijinadmin/cooperator/cooperatorList';
         $config['total_rows'] = $query->num_rows();
         $config['per_page'] = 10;
         $this->pagination->initialize($config);
         $view['page']=$this->pagination->create_links();
         $this->load->view('maijinadmin/common/header', $view);
         $this->load->view('maijinadmin/cooperator');
         $this->load->view('maijinadmin/common/footer');
         
     }
     /**
      * 显示回收商详情
      * @param    int     id   回收商id
      * 
      */
    function GetCoopInfo(){
        $this->load->database();
        $mobile=$this->input->post('mobile');
        $sql='select a.cooperator_mobile as mobile,a.cooperator_name as name,FROM_UNIXTIME
              (a.cooperator_join_time, "%Y-%m-%d %H:%i:%s") as time,a.cooperator_sex as sex,
              a.cooperator_switch as switch,a.cooperator_opened as opened,a.cooperator_work_place as work_place,
              a.cooperator_userstatus as status,a.cooperator_auth_type as autotype,a.cooperator_work_year as year,
              a.cooperator_shopaddress as shopaddress,
              a.cooperator_auth_money as moery,a.cooperator_distance as distance,a.cooperator_cars as cars
              ,b.auth_pic_path  as auth from  h_cooperator_info  a  left  join   h_cooperator_auth  b 
              on a.cooperator_mobile=b.cooperator_mobile  where  a.cooperator_mobile="'.$mobile.'" and  a.cooperator_status=1';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $info=0;
        }
        $info=$result->row_array();
        $info['sex'] == 1 ? $info['sex'] = '男' : $info['sex'] ='女';
        $info['switch'] == 1 ? $info['switch'] = '开' : $info['switch'] = '关';
        $info['work_place']  == 1 ? $info['work_place'] ='有' : $info['work_place'] = '无';
        $info['opened'] =json_decode($info['opened']);
        $info['auth']=unserialize($info['auth']);
        $status=array('0'=>'待审核','1'=>'冻结','2'=>'未通过','3'=>'通过');
        $info['aptitudes']=$this->config->item('cooperator_auth_type');
        $info['status']=$status[$info['status']];
        $response=array('status'=>$this->config->item('request_succ'),
                'info'=>'','msg'=>'','data'=>$info
        );
        echo json_encode($response);exit;
    }
    /**
     * 修改当前回收商的认证信息
     */
    function EditCoopAuto(){
        $this->load->database();
        $auto=$this->input->post('auto');
        $mobile=$this->input->post('mobile');
        $aptitudes=$this->config->item('cooperator_auth_type');
        $autoid=array_search($auto,$aptitudes);
        $query=$this->db->update('h_cooperator_info',array('cooperator_auth_type'=>$autoid),
                array('cooperator_mobile'=>$mobile));
        if($query){
            $response=array('status'=>$this->config->item('request_succ'),
                    'info'=>'','msg'=>'','data'=>''
            );
        }else{
            $response=array('status'=>$this->config->item('request_fall'),
                    'info'=>'','msg'=>'修改失败','data'=>''
            );
        }
        echo json_encode($response);exit;
    }
}