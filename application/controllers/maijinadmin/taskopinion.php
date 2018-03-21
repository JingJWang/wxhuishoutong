<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:微信粉丝层
 */
class Taskopinion extends RA_Controller {

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/taskopinion_model');
        $this->load->library('pagination');
        $this->load->helper('url');
    }

    //获取全部奖励
    public function getAllopinion(){
        
        $this->checkauto();//判断是否登陆
        $data = array();
        $orderoption['page'] = $this->uri->segment(4,0);
        $orderoption['per_page'] = $this->config->item('PAGENUM_BACK');
        $data = $this->taskopinion_model->select_opinion_list($orderoption);
        if ($data === false) {
            return false;
        }
        //以下进行分页
        $config['use_page_numbers'] = FALSE;
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
        $config['total_rows'] = $data['num'][0]['num'];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();
        $data['pages'] = $orderoption['page'];

        $this->load->model('maijinadmin/task_model');
        $data['reward'] = $this->task_model->rewards();

        $data['modellist'] = $_SESSION['modellist'];//获取session中的列表参数
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/taskopinionlist', $data);
        $this->load->view('maijinadmin/common/footer', $data);

    }

    //获取单个评论信息
    public function examine_opinion(){

        $this->checkauto();
        
        $this->load->helper('safe_helper');//载入安全函数

        $id = $this->input->post('id',true);
        $id = verify_id($id);  

        $opinion = $this->taskopinion_model->get_opinioninfo($id);
        
        if ($opinion == false) {
            $message = array('status' => 8, 'info' => '获取出错');
            echo json_encode($message);
        }else{
            $data['opinion'] = $opinion[0];
            $data['opinion']['opinion_join_time'] = date('Y-m-d H:i:s',$data['opinion']['opinion_join_time']);
            $message = array('status' => 1, 'info' => $data);
            echo json_encode($message);
        }
        
    }

    //采纳意见
    public function adoption_opinion(){

        $this->checkauto();
        $ops = $this->input->post();

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

        if ($ops['rewards']=='') {
            $message = array('status' => 2, 'info' => '您没有选择奖励!');
            echo json_encode($message);
            exit();
        }

        //检查奖励是否被采纳
        $opinion = $this->taskopinion_model->get_opinioninfo($ops['num']);
        if ($opinion[0]['opinion_status']==2) {
            $message = array('status' => 3, 'info' => '您选择的评论已经被采纳过了!');
            echo json_encode($message);
            exit();
        }

        $this->load->helper('safe_helper');//载入安全函数
        $ops['num'] = verify_id($ops['num']); 
        $ops['rewards'] = verify_id($ops['rewards']); 

        $str = $this->taskopinion_model->adoption_opinion($ops['num'],$ops['rewards']);
        if (!$str) {
            $message = array('status' => 8, 'info' => '更新出错');
            echo json_encode($message);
        }elseif($str==='no_fund'){
            $message = array('status' => 4, 'info' => '此用户没有足够的基金换取金钱！');
            echo json_encode($message);
        }else{
            $message = array('status' => 7, 'info' => '更新成功');
            echo json_encode($message);
        }

        // var_dump($ops);
    }
    
}

?>