<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:地堆小组管理控制层
 */
class Group extends CI_Controller {    
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/group_model');
        $this->load->helper('security');
        $this->load->library('pagination');
    }
    /**
     * @description 现有的地推小组列表
     */
    function getgrouplist(){
        $option['page'] = $this->uri->segment(4,0);
        $option['pagenumber'] = $this->config->item('PAGENUM_BACK');
        $groupdata=$this->group_model->getgrouplist($option);
        //数据
        $data['data_status']=$this->config->item('data_status');
        $data['grouplist']=$groupdata['list'];
        $data['modellist'] = $_SESSION['modellist'];        
        //分页配置
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' .
        $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取访问路径
        $config['total_rows'] = $groupdata['total'];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();
        //获取该模块的视图操作
        $data['viewpermit']=array();
        $data['listpermit']=array();
        if($_SESSION['role_flag'] == $this->config->item('power_type_max')){
            $data['viewpermit']=array('permit_view_addgroup');
            $data['listpermit']=array('permit_list_configure',
                    'permit_list_editgroup','permit_list_delgroup'
            );
        }
        //模板
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/group', $data);
        $this->load->view('maijinadmin/common/footer');
    }
    /**
     * @description 获取可以配置的地推组长 主管 总监
     */
    function getgroupleader(){
        $data=$this->group_model->getgroupleader();
        if($data === false){
            $data['status']=$this->config->item('request_fall');
        }else{
            $data['status']=$this->config->item('request_suss');
        }
        echo json_encode($data);
        exit();
    }
    /**
     * @description 地推添加小组
     */
    function addgroup(){        
        if(!$this->input->post('group_name',true) && !$this->input->post('group_leader',true)){
           $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('option_notnull'));
            echo json_encode($response);
            exit();
        }
        $groupdata['group_name']=$this->input->post('group_name',true);
        $groupdata['group_leader']=$this->input->post('group_leader',true);
        $groupdata['group_leader']=$this->input->post('group_executives',true);
        $groupdata['group_majordomo']=$this->input->post('group_majordomo',true);
        $res_add=$this->group_model->addgroup($groupdata);
        if($res_add){
             $response=array('status'=>$this->config->item('request_suss'),'info'=>$this->lang->line('addgroupsucc'));
        }else{
             $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('addgroupfall'));             
        }  
        echo json_encode($response);
        exit();   
    }
    /**
     * @description 获取有效的地推人员
     */
    function getgroupteam(){
        $data['group_id']=$this->input->get('group_id',true);
        if(!is_numeric($data['group_id'])){
            exit();
        }
        $response=$this->group_model->getgroupteam($data);
        if($response === false){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('lookteamfall');
        }else{
            $response['status']=$this->config->item('request_suss');
        }
        echo json_encode($response);
        exit();
    }
    /**
     * @description 修改小组的成员
     */
    function editgroupmember(){
        $data['group_id']=$this->input->post('groupid',true);
        $userid=$this->input->post('userid',true);
        $data['userid']=trim($userid,',');
        $res_group=$this->group_model->editgroupmember($data);
        if(!$res_group){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('editgroupmemberfall');
            echo json_encode($response);
            exit();
        }
        $data['userid']=explode(',', $data['userid']);
        $res_admin=$this->group_model->edit_admin_groupid($data);
        if(!$res_admin){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('editgroupmemberfall');
        }else{
            $response['status']=$this->config->item('request_suss');
            $response['info']=$this->lang->line('editgroupmembersucc');
        }
        echo json_encode($response);
        exit();        
    }
    /**
     * @description 移除当前小组的成员
     */
    function cleargroupmember(){
        $userid=$this->input->post('userid',true);
        $data['groupid']=$this->input->post('groupid',true);
        if(empty($userid) || empty($data['groupid']) ||!is_numeric($data['groupid']) ){
            $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('option_notnull'));
            echo json_encode($response);
            exit();
        }
        $str_userid=trim($userid,',');
        $data['userid']=explode(',', $str_userid);
        $res_admin=$this->group_model->clear_group_member($data);
        if(!$res_admin){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('cleargroupmemberfall');
        }else{
            $response['status']=$this->config->item('request_suss');
            $response['info']=$this->lang->line('cleargroupmembersucc');
        }
        echo json_encode($response);
        exit();
    }
    /**
     * @description 查询当前小组成员业绩明细
     */
    function group_performance(){
        //小组信息
        $data['group_id']=$this->input->get('groupid',true);
        if(empty($data['group_id']) || !is_numeric($data['group_id'])){
                $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('option_notnull'));
                echo json_encode($response);
                exit();
        }
        $response['groupinfo']=$this->group_model->group_performance($data);
        if($response['groupinfo'] === false){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('performancefall');
            echo json_encode($response);
            exit();
        }        
        $userid=$response['groupinfo']["0"]["group_leader"].','.
                 $response['groupinfo']["0"]["group_executives"].','.
                 $response['groupinfo']["0"]["group_majordomo"];
        //小组管理信息
        $response['userinfo']=$this->group_model->group_manage_userinfo($userid);
        if($response['userinfo'] === false){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('performancefall');
            echo json_encode($response);
            exit();
        }
        //小组业绩
        $member=$response['groupinfo']["0"]["group_member"];
        if(empty($member) || $member == '0'){
            $response['status']=$this->config->item('request_suss');
            $response['performance']=0;
            echo json_encode($response);
            exit();
        }
        $performance=$this->group_model->group_userperformance_info($member);
        $performance_unsub=array();
        foreach ($performance['unsub'] as $unsub){
            $performance_unsub[$unsub['user_id']]=$unsub['number'];
        }
        //生成业绩记录
        $response['performance']=array();
        foreach ($performance['sub'] as $sub){
             $response['performance'][]=array('id'=>$sub['user_id'],
                                            'sub'=>$sub['number'],
                                            'unsub'=>array_key_exists($sub['user_id'], $performance_unsub) ? $performance_unsub[$sub['user_id']] : 0,
                                            'Total'=>$sub['number']-$performance_unsub[$sub['user_id']]
             );
        }
        if($response['groupinfo'] === false || $response['userinfo'] === false || 
           $response['performance'] === false){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('performancefall');
        }else{
            $response['status']=$this->config->item('request_suss');
        }
        echo json_encode($response);
        exit();
    }
    /**
     * @description 编辑当前小组
     */
    function editgroup(){
        $data['group_id']=$this->input->get('groupid',true);
        if(!is_numeric($data['group_id'])){
            exit();
        }
        $response=$this->group_model->editgroup($data);
        if(!$response){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('busy_content');
        }else{
            $response['status']=$this->config->item('request_suss');
        }
        echo json_encode($response);
        exit();
    }
    /**
     * @description 保存当前修改的地推小组信息
     */
    function save_editgroup(){
        $groupdata['group_id']=$this->input->post('group_edit_id',true);
        $groupdata['group_name']=$this->input->post('group_name',true);
        $groupdata['group_leader']=$this->input->post('group_leader',true);
        $groupdata['group_executives']=$this->input->post('group_executives',true);
        $groupdata['group_majordomo']=$this->input->post('group_majordomo',true);
        if(!is_numeric($groupdata['group_id']) || !is_numeric($groupdata['group_leader'])
           || !is_numeric($groupdata['group_executives']) || !is_numeric($groupdata['group_majordomo']) ){
            $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('option_notnull'));
            echo json_encode($response);
            exit();
        }
        $result=$this->group_model->save_edit_datainfo($groupdata);
        if(!$result){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('busy_content');
        }else{
            $response['status']=$this->config->item('request_suss');
            $response['info']=$this->lang->line('savegroupinfo');
        }
        echo json_encode($response);
        exit();
    }
    /**
     * @description 删除地推小组
     */
    function delgroup(){
        $groupdata['group_id']=$this->input->post('groupid',true);
        if(!is_numeric($groupdata['group_id'])){
            $response=array('status'=>$this->config->item('request_fall'),'info'=>$this->lang->line('option_notnull'));
            echo json_encode($response);
            exit();
        }
        $isnull=$this->group_model->check_grouo_isnull($groupdata);
        if($isnull != '0'){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('groupisnull');
            echo json_encode($response);
            exit();
        }
        $result=$this->group_model->delgroup($groupdata);
        if(!$result){
            $response['status']=$this->config->item('request_fall');
            $response['info']=$this->lang->line('busy_content');
        }else{
            $response['status']=$this->config->item('request_suss');
            $response['info']=$this->lang->line('delgroup');
        }
        echo json_encode($response);
        exit();
    }
}