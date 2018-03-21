<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:员工控制层
 */
class Adminuser extends RA_Controller {
        
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/adminuser_model','',TRUE);
        $this->load->model('common/wxcode_model','',TRUE);
        $this->load->library('pagination');
    }    
    /**
     * 功能描述:员工列表     
     */
    public function userlist(){
        $this->checkauto();
        
        $data['sumweight'] = 0;
        $data['sumpic'] = 0;
        $data['sumvoucherpic'] = 0;
        $data['sumvouchernum'] = 0;
        
        $useroption['page'] = $this->uri->segment(4,0);
        $useroption['per_page'] = $this->config->item('PAGENUM_BACK');
        
        $userdata = $this->adminuser_model->select_admin_user($useroption);
        $orderdata = $this->adminuser_model->getadminorder('');
        if($orderdata !== false){
            if($orderdata !='-1'){
                $sumorderdata = $this->adminuser_model->getsum_wei_order_vou($orderdata['data']);
                $sumvoucherdata = $this->adminuser_model->getsumvoucher($sumorderdata['voucher_string']);
                if($sumvoucherdata !== false){
                    $data['sumvoucherpic'] = $sumvoucherdata['sumvoucherpic'];
                    $data['sumvouchernum'] = $sumvoucherdata['sumvouchernum'];
                }
                $data['sumweight'] = $sumorderdata['sumweight'];
                $data['sumpic'] = $sumorderdata['sumpic'];
            }              
        }
        
        $data['userlist'] = $userdata['list'];
        $data['modellist'] = $_SESSION['modellist'];
        $data['leftmenu'] = '员工管理';
        $config['use_page_numbers'] = FALSE;
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
        $config['total_rows'] = $userdata['num'];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/employee', $data);
        $this->load->view('maijinadmin/common/footer', $data);
    }
    
    public function searchuser(){
        $this->checkauto();        
        $useroption['page'] = $this->uri->segment(4,0);
        $useroption['per_page'] = $this->config->item('PAGENUM_BACK');        
        $useroption['xingming'] = $this->input->post('xingming',true);
        $useroption['address'] = $this->input->post('address',true);
        $useroption['pay_type'] = $this->input->post('pay_type',true);
        $useroption['status'] = $this->input->post('status',true);        
        $userdata = $this->adminuser_model->searchuser($useroption); 
        if ($userdata === false) {
            $message = array('status' => 3000, 'info' => '查询失败!');
            echo json_encode($message);
            exit();
        } else {
            if ($userdata == '-1') {
                $message = array('status' => 1050, 'info' => '没有查询到符合条件的记录!');
                echo json_encode($message);
                exit();
           } else {
               $config['use_page_numbers'] = FALSE;
                $config['base_url'] ='/index.php/' . $this->router->fetch_directory()
                 . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器
                $config['total_rows'] = $userdata['num'];
                $this->pagination->initialize($config);
                $data['page'] = $this->pagination->create_links();
                $data['status'] = 1000;
                $data['data'] = $userdata['list'];
                echo json_encode($data);
           }
       }
    }
    
    /**
     * 功能描述: 地堆小组 查询员工
     
    public function select_admin_user_group(){
        $message=array();       
        $data=$this->adminuser_model->select_admin_user_group($_POST);
        if($data!==false){
            if($data!='0'){
                $message=array('status'=>$this->lang->line('DOSUCCESS'),'num'=>$data['num'],'data'=>$data['data']);
            }else{
                $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('RESULTNULL'));
            }
        }else{
            $message=array('status'=>$this->lang->line('DOFAIL'),'info'=>$this->lang->line('INFOFAIL'));
        }
        echo json_encode($message);
    }*/
    
    /**
     * 功能描述:禁用员工，设置为无效
     */
    public function delete_admin_user(){
        $this->checkauto();        
        $message = array();
        $id = $this->input->post('id',true);       
        $data=$this->adminuser_model->delete_admin_user($id);
        if($data === false){
            $message = array('status' => 3000, 'info' => '禁用失败!');
        }else{
            $message = array('status' => 1000, 'info' => '禁用成功!');
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:获得权重低于自己的权限
     */
    public function get_role(){
        $this->checkauto();
        $message = array();
        if ($_SESSION['role_flag'] == 'maijinadmin') {
            $data=$this->adminuser_model->get_role_all();
        } else {
            $useroption['role_flag'] = $_SESSION['role_flag'];
            $useroption['role_weight'] = $_SESSION['role_weight'];
            $data = $this->adminuser_model->get_role($useroption);
        }
        
        if ($data === false) {
            $message = array('status' => 3000, 'info' => '查询权限失败!');
        } else {
            if ($data == '-1') {
                $message = array('status' => 1050, 'info' => '您没权限添加!');
            } else {
                $message = array('status' => 1000, 'data' => $data);
            }
        }
        echo json_encode($message);
    }
    
    /**
     * 功能描述:修改员工根据id查询员工的信息
     */
    public function get_admin_user(){
        $this->checkauto();
        
        $message=array();
        
        $id=$this->input->post('id',true);
        
        if ($_SESSION['role_flag'] == 'maijinadmin') {
            $data_role=$this->adminuser_model->get_role_all();
        } else {
            $useroption['role_flag'] = $_SESSION['role_flag'];
            $useroption['role_weight'] = $_SESSION['role_weight'];
            $data_role = $this->adminuser_model->get_role($useroption);
        }
        
        if ($data_role === false) {
            $message = array('status' => 3000, 'info' => '查询权限失败!');
            echo json_encode($message);
            exit();
        } else {
            if ($data_role == '-1') {
                $message = array('status' => 1050, 'info' => '您没权限修改!');
                echo json_encode($message);
                exit();
            }
        }
        
        $data_user=$this->adminuser_model->get_admin_user($id);
        
        $data['role']=$data_role;
        $data['user']=$data_user;
        
        if($data_user!==false){
            $message = array('status' => 1000, 'data' => $data);
        }else{
            $message = array('status' => 3000, 'info' => '获取员工信息失败!');
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:添加修改员工
     */
    public function add_edit_admin_user(){
        $this->checkauto();
        
        $message=array();
        
        $useroption['id'] = $this->input->post('id',true);
        $useroption['xingming'] = $this->input->post('xingming',true);
        $useroption['name'] = $this->input->post('name',true);
        $useroption['password'] = $this->input->post('password',true);
        $useroption['reqpassword'] = $this->input->post('reqpassword',true);
        $useroption['maile'] = $this->input->post('maile',true);
        $useroption['mobile'] = $this->input->post('mobile',true);
        $useroption['address'] = $this->input->post('address',true);
        $useroption['pay_type'] = $this->input->post('pay_type',true);
        $useroption['power_type'] = $this->input->post('power_type',true);
        $useroption['power_name'] = $this->input->post('power_name',true);
        $useroption['status'] = $this->input->post('status',true);
        
        if($useroption['xingming']==''||$useroption['name']==''||$useroption['maile']==''
            ||$useroption['mobile']==''||$useroption['address']==''||$useroption['power_type']==''
            ||$useroption['status']==''||$useroption['pay_type']==''){
            $message = array('status' => 3000, 'info' => '必填项不能为空!');
            echo json_encode($message);
            exit();
        }
        if(!preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $useroption['maile'])){
            $message = array('status' => 3000, 'info' => '邮箱格式不正确!');
            echo json_encode($message);
            exit();
        }
        if(!preg_match('/^((13[0-9])|145|147|(15[0-35-9])|(18[0-9]))[0-9]{8}$$/', $useroption['mobile'])){
            $message = array('status' => 3000, 'info' => '手机格式不正确!');
            echo json_encode($message);
            exit();
        }
        if($useroption['id']!=''){
            $this->edit_user($useroption);
        }else{
            $this->add_user($useroption);
        }
        
    }
    /**
     * 添加员工
     */
    public function add_user($useroption){
        if($useroption['password']==''&&$useroption['reqpassword']==''){
            $useroption['password']=md5('qazxsw123');
        }else{
            if($useroption['password']!=$useroption['reqpassword']){
                $message = array('status' => 3000, 'info' => '两次密码不一致!');
                echo json_encode($message);
                exit();
            }else{
                $useroption['password']=md5($useroption['password']);
            }
        }
        $datausername=$this->adminuser_model->get_admin_username($useroption['name']);
        if($datausername===false){
            $message = array('status' => 3000, 'info' => '查询用户信息失败!');
            echo json_encode($message);
            exit();
        }elseif($datausername!='-1'){
            $message = array('status' => 3000, 'info' => '用户名不能重复!');
            echo json_encode($message);
            exit();
        }
        
        $id=$this->adminuser_model->add_admin_user($useroption);
        if($id!==false){
            $imginfo=$this->wxcode_model->dowload($id);
            $data=$this->adminuser_model->update_admin_wxcode($id,$imginfo);
            if($data!==false){
                $message = array('status' => 1000, 'info' => '添加成功!');
            }else{
                $message = array('status' => 3000, 'info' => '添加失败!');
            }
        }else{
            $message = array('status' => 3000, 'info' => '添加失败!');
        }
        echo json_encode($message);
    }
    /**
     * 修改员工
     */
    public function edit_user($useroption){
        if($useroption['password']==''&&$useroption['reqpassword']==''){
            $pwdupdate=false;
        }else{
            if($useroption['password']!=$useroption['reqpassword']){
                $message = array('status' => 3000, 'info' => '两次密码不一致!');
                echo json_encode($message);
                exit();
            }else{
                $useroption['password']=md5($useroption['password']);
                $pwdupdate=true;
            }            
        }
    
        $data=$this->adminuser_model->update_admin_user($useroption,$pwdupdate);
        if($data!==false){
            $message = array('status' => 1000, 'info' => '修改成功!');
        }else{
            $message = array('status' => 3000, 'info' => '修改失败!');
        }
        echo json_encode($message);
    }
    /**
     * 功能描述:按日期查询员工业绩
     */
    public function select_admin_user_yj(){
        $message=array();
        
        $data['sumweight'] = 0;
        $data['sumpic'] = 0;
        $data['sumvoucherpic'] = 0;
        $data['sumvouchernum'] = 0;
        $data['sumyorder'] = 0;
        $data['sumdorder'] = 0;
        $data['sumdguanzhu'] = 0;
        
        $useroption['data_time'] = $this->input->post('data_time',true);
        $useroption['userid'] = $this->input->post('userid',true);
        
        $orderdata = $this->adminuser_model->getadminorder($useroption);
        if($orderdata !== false){
            if($orderdata !='-1'){
                $data['sumyorder'] = $orderdata['num'];
                $sumorderdata = $this->adminuser_model->getsum_wei_order_vou($orderdata['data']);
                $sumvoucherdata = $this->adminuser_model->getsumvoucher($sumorderdata['voucher_string']);
                if($sumvoucherdata !== false){
                    $data['sumvoucherpic'] = $sumvoucherdata['sumvoucherpic'];
                    $data['sumvouchernum'] = $sumvoucherdata['sumvouchernum'];
                }
                $data['sumweight'] = $sumorderdata['sumweight'];
                $data['sumpic'] = $sumorderdata['sumpic'];
            }
        }
        
        $guanzhunum = $this->adminuser_model->getsumguanzhu($useroption);
        $data['sumdguanzhu'] = $guanzhunum=='-1' ? 0 : $guanzhunum;
        $order_d = $this->adminuser_model->getadminorder_D($useroption);
        $data['sumdorder'] = $order_d=='-1' ? 0 : $order_d;
        
        $message = array('status' => 1000, 'data' => $data);
        
        echo json_encode($message);
    }
}

?>