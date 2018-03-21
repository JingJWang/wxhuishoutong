<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:微信粉丝层
 */
class Task extends RA_Controller {

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/task_model');
        $this->load->library('pagination');
        $this->load->helper('url');
    }

    public function getAlltask(){
        $this->checkauto();//判断是否登陆
        $data = array();

        $orderoption['page'] = $this->uri->segment(4,0);
        $orderoption['per_page'] = $this->config->item('PAGENUM_BACK');
        $data = $this->task_model->select_task_list($orderoption);

        //以下进行分页
        $config['use_page_numbers'] = FALSE;
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
        $config['total_rows'] = $data['num'][0]['num'];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();

        $data['reward'] = $this->task_model->rewards();

        $data['types'] = $this->config->item('task_types');//获取任务类型
        $data['modellist'] = $_SESSION['modellist'];//获取session中的列表参数
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/tasklist', $data);
        $this->load->view('maijinadmin/common/footer', $data);

    }

    /**
     * 获取全部的任务
     */
    public function gettask(){

        $this->checkauto();
        $data = $this->task_model->select_all_task();

        if ($data === FALSE) {
            $message = array('status'=>'5','info'=>'获取失败');
            echo json_encode($message);
        }else{
            $message = array('status'=>'4','data'=>$data);
            echo json_encode($message);
        }

    }

    /**
     * 添加任务
     */
    public function add_editor_task(){

        $this->checkauto();
        $this->load->helper('safe_helper');//载入安全函数
        post_check();

        $post=$this->input->post(); 
        $post['task_limit_time'] = $post['task_limit_time']*3600*24;
        if ((!empty($post['task_limit_other']) && $post['task_limit_other']<0) && $post['task_limit_other']!=-2 && $post['task_limit_other']!=-1) {
            $message = array('status' => 6, 'info' => '限制任务的信息不正确');
            echo json_encode($message); 
            exit();
        }

        if (empty($post['info_name']) || empty($post['reward_content']) || empty($post['task_content'])||empty($post['task_type'])||empty($post['task_level'])) {
            $message = array('status' => 1, 'info' => '信息未填完整');
            echo json_encode($message);
            exit();
        }
        switch ($post['task_type']) {
            case '1':
                $post['task_sign'] >0? $a=0:$a=1;
                break;
            case '2':
                $post['task_turnover'] >0? $a=0:$a=1;
                break;
            case '3':
                $post['task_share'] >0? $a=0:$a=1;
                break;
            case '5':
                $post['task_invite_u'] >0? $a=0:$a=1;
                break;
            case '6':
                $post['task_invite_m'] >0? $a=0:$a=1;
                break;
            
            default:
                break;
        }
        if (isset($a)&&$a==1) {
            $message = array('status' => 8, 'info' => '填写的任务类型未给需要完成任务的值');
            echo json_encode($message);
            exit();
        }

        $post['reward_id']='';
        $reward_num=0;
        if (!empty($post['reward_id1'])) {
            $post['reward_id'] .= $post['reward_id1'].' ';
            $reward_num++;
        }
        if (!empty($post['reward_id2'])) {
            $post['reward_id'] .= $post['reward_id2'].' ';
            $reward_num++;
        }
        if (!empty($post['reward_id3'])) {
            $post['reward_id'] .= $post['reward_id3'].' ';
            $reward_num++;
        }
        if (!empty($post['reward_id4'])) {
            $post['reward_id'] .= $post['reward_id4'].' ';
            $reward_num++;
        }
        if ($post['reward_num']>$reward_num) {
            $message = array('status' => 3, 'info' => '让用户选择奖励的数量大于奖励数量');
            echo json_encode($message);
            exit();
        }
        unset($post['reward_id1']);
        unset($post['reward_id2']);
        unset($post['reward_id3']);
        unset($post['reward_id4']);
        $post['reward_id'] = rtrim($post['reward_id'],' ');
        $post['task_jiontime'] = time();

        if ($post['task_id'] == '') {
            $str = $this->task_model->addtask($post);
            if ($str) {
                $message = array('status' => 4, 'info' => '添加成功');
                echo json_encode($message);
            }else{
                $message = array('status' => 5, 'info' => '添加出错');
                echo json_encode($message);
            }
        }else{
            $post['task_updatetime'] = time();
            $id = $post['task_id'];
            unset($post['task_id']);
            $str = $this->task_model->updatetask($post,$id);
            if ($str) {
                $message = array('status' => 7, 'info' => '更新成功');
                echo json_encode($message);
            }else{
                $message = array('status' => 8, 'info' => '更新出错');
                echo json_encode($message);
            }
        }

    }

    /**
     * 删除任务
     */
    function delecttask(){
        $this->checkauto();
        $this->load->helper('safe_helper');//载入安全函数

        $id = $this->input->post('id',true);
        $id = verify_id($id);     
        $data=$this->task_model->delect_task($id);
        if($data === false){
            $message = array('status' => 3000, 'info' => '禁用失败!');
        }else{
            $message = array('status' => 1000, 'info' => '禁用成功!');
        }
        echo json_encode($message);
    }

    /**
     * 修改某个任务
     */
    function selecttask(){
        $this->checkauto();
        $this->load->helper('safe_helper');//载入安全函数

        $id = $this->input->post('id',true);
        $id = verify_id($id);

        //检验权限
        if ($_SESSION['role_flag'] == 'maijinadmin') {
            $data_role=$this->task_model->get_role_all();
        } else {
            $useroption['role_flag'] = $_SESSION['role_flag'];
            $useroption['role_weight'] = $_SESSION['role_weight'];
            $data_role = $this->task_model->get_role($useroption);
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

        $data['this_task'] = $this->task_model->select_task($id);
        if ($data['this_task']==false) {
            $message = array('status' => 1, 'info' => '查询失败!');
            echo json_encode($message);exit();
        }
        if ($data['this_task'][0]['task_limit_time']>0) {
           $data['this_task'][0]['task_limit_time'] = $data['this_task'][0]['task_limit_time']/(3600*24); 
        }

        $reward_ids = explode(' ', $data['this_task'][0]['reward_id']);//任务奖励设置
        $data['this_task'][0]['reward_id1'] = $data['this_task'][0]['reward_id2'] = $data['this_task'][0]['reward_id3'] = $data['this_task'][0]['reward_id4'] ='';
        if (isset($reward_ids[0])) {
            $data['this_task'][0]['reward_id1'] = $reward_ids[0];
        }
        if (isset($reward_ids[1])) {
            $data['this_task'][0]['reward_id2'] = $reward_ids[1];
        }
        if (isset($reward_ids[2])) {
            $data['this_task'][0]['reward_id3'] = $reward_ids[2];
        }
        if (isset($reward_ids[3])) {
            $data['this_task'][0]['reward_id4'] = $reward_ids[3];
        }

        $data['all_task'] = $this->task_model->select_all_task();
        if ($data['all_task']==false) {
            $message = array('status' => 1, 'info' => '查询失败!');
            echo json_encode($message);exit();
        }
        
        $message = array('status' => 2, 'info' => $data);
        echo json_encode($message);

    }

}

?>