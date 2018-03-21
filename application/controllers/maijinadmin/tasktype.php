<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:微信粉丝层
 */
class Tasktype extends RA_Controller {

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/tasktype_model');
        $this->load->library('pagination');
        $this->load->helper('url');
    }
    /**
     * 获取所有任务类型
     * @param   int     page    分页的数字
     */
    public function getAlltype(){
        $this->checkauto();//判断是否登陆
        $data = array();
        $orderoption['page'] = $this->uri->segment(4,0);
        $orderoption['per_page'] = $this->config->item('PAGENUM_BACK');
        $result = $this->tasktype_model->select_task_type($orderoption);
        //以下进行分页
        if ($result==0) {
            $data = $orderoption;
            $data['list'] = array();
            $data['num']['0']['num']=0;
        }else{
            $data = $result;
        }
        $config['use_page_numbers'] = FALSE;
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
        $config['total_rows'] = $data['num'][0]['num'];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();

        $data['types'] = $this->config->item('task_types');//获取任务类型
        $data['modellist'] = $_SESSION['modellist'];//获取session中的列表参数
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/tasktype', $data);
        $this->load->view('maijinadmin/common/footer', $data);
    }
    /**
     * 获取任务类型
     * @param   int     id   要获取的任务类型id
     */
     function selectype(){
        $this->checkauto();
        $id = $this->input->post('id',true);
        if (!is_numeric($id)) {
            $message = array('status' => 1,'info' => '错误');
            echo json_encode($message);
            exit();
        }
        //检验权限
        $this->load->model('maijinadmin/task_model');
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
        $data['this_type'] = $this->tasktype_model->select_type($id);
        if ($data['this_type']==0) {
            $message = array('status' => 1, 'info' => '无效');
            echo json_encode($message);
            exit();
        }
        $message = array('status' => 0, 'info' => $data);
        echo json_encode($message);
    }
    /**
     * 添加和修改任务类型信息
     * @param   int   id       任务类型信息id
     * @param   int   type     任务类型
     * @param   int   taskid   指向具体任务的id
     * @param   int   status   状态 
     */
    public function add_editor_type(){
        $this->checkauto();
        $post=$this->input->post();
        if (empty($post['taskid'])) {
             $post['taskid'] = 0;
         } 
        if (!is_numeric($post['type'])||!is_numeric($post['taskid'])||!is_numeric($post['i_status'])) {
            $message = array('status' => 0,'info' => '错误');
            echo json_encode($message);
            exit();
        }
        $content = !get_magic_quotes_gpc()? addslashes($post['i_content']):'';

        if ($post['id']=='') {
            $str = $this->tasktype_model->addtasktype($post);
            if ($str) {
                $message = array('status' => 0, 'info' => '添加成功');
                echo json_encode($message);
            }else{
                $message = array('status' => 5, 'info' => '添加出错');
                echo json_encode($message);
            }
            exit();
        }else{
            if(!is_numeric($post['id'])){
                $message = array('status' => 0,'info' => '错误');
                echo json_encode($message);
                exit();
            }
            $str = $this->tasktype_model->uptypetask($post);
            if ($str) {
                $message = array('status' => 0, 'info' => '更新成功');
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
    function delectype(){
        $this->checkauto();
        $id = $this->input->post('id',true);
        if (!is_numeric($id)) {
            $message = array('status' => 1, 'info' => '错误');
            echo json_encode($message);
            exit();
        }

        $data=$this->tasktype_model->delect_type($id);
        if($data === false){
            $message = array('status' => 3000, 'info' => '禁用失败!');
        }else{
            $message = array('status' => 1000, 'info' => '禁用成功!');
        }
        echo json_encode($message);
    }
}

?>