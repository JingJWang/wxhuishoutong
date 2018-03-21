<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:微信粉丝层
 */
class Taskreward extends RA_Controller {

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/taskreward_model');
        $this->load->library('pagination');
        $this->load->helper('url');
    }

    //获取全部奖励
    public function getAllreward(){
        
        $this->checkauto();//判断是否登陆
        $data = array();
        $orderoption['page'] = $this->uri->segment(4,0);
        $orderoption['per_page'] = $this->config->item('PAGENUM_BACK');
        $data = $this->taskreward_model->select_reward_list($orderoption);
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


        $data['modellist'] = $_SESSION['modellist'];//获取session中的列表参数
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/taskrewardlist', $data);
        $this->load->view('maijinadmin/common/footer', $data);

    }

    //添加或编辑奖励
    public function add_editor_reward(){

        $this->checkauto();//判断是否登陆
        $post = $this->input->post();
        
        if (empty($post['rbonus']) && empty($post['rintegral']) && empty($post['all_rintegral']) && empty($post['rfund'])) {
            $message = array('status' => 1,'info' => '您没有选择任何奖励');
            echo json_encode($message);
            return '';
        }
        if ((!empty($post['rbonus'])&&!is_numeric($post['rbonus']))||(!empty($post['rintegral'])&&!is_numeric($post['rintegral']))||(!empty($post['all_rintegral'])&&!is_numeric($post['all_rintegral']))||(!empty($post['rfund'])&&!is_numeric($post['rfund']))||($post['rstatus']!=1&&$post['rstatus']!=-1)) {
            $message = array('status' => 2,'info' => '参数非法');
            echo json_encode($message);
            return '';
        }

        if ($post['num']!='') {
            $upreward = array(
                'reward_bonus' => $post['rbonus'],
                'reward_integral' => $post['rintegral'],
                'reward_all_integral' => $post['all_rintegral'],
                'reward_fund' => $post['rfund'],
                'reward_status' => $post['rstatus'],
                'reward_updatetime' => time()
            );
            $str = $this->taskreward_model->upreward($upreward,$post['num']);
            if ($str == false) {
                $message = array('status' => 8, 'info' => '更新出错');
                echo json_encode($message);
            }else{
                $message = array('status' => 7, 'info' => '更新成功');
                echo json_encode($message);
            }
        }else{
            $insert = array(
                'reward_bonus' => $post['rbonus'],
                'reward_integral' => $post['rintegral'],
                'reward_all_integral' => $post['all_rintegral'],
                'reward_fund' => $post['rfund'],
                'reward_status' => $post['rstatus'],
                'reward_jointime' => time()
            );
            $str = $this->taskreward_model->addtaskreward($insert);
            if ($str === false) {
                $message = array('status' => 4, 'info' => '添加出错');
                echo json_encode($message);
            }else{
                $message = array('status' => 5, 'info' => '添加成功');
                echo json_encode($message);
            }
        }

    }

    //删除奖励
    public function delectreward(){

        $this->checkauto();
        $this->load->helper('safe_helper');//载入安全函数

        $id = $this->input->post('id',true);
        $id = verify_id($id);     
        $data=$this->taskreward_model->delect_reward($id);

        if($data === false){
            $message = array('status' => 3000, 'info' => '禁用失败!');
        }else{
            $message = array('status' => 1000, 'info' => '禁用成功!');
        }
        echo json_encode($message);

    }

    //获取要修改的奖励的信息
    public function uptaskreward(){
        $this->checkauto();
        $this->load->helper('safe_helper');//载入安全函数

        $id = $this->input->post('id',true);
        $id = verify_id($id);

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

        $data = $this->taskreward_model->get_reward($id);
        if ($data === false) {
            $message = array('status' => 1, 'info' => '查询出错!');
        }elseif($data == ''){
            $message = array('status' => 3, 'info' => '查询为空!');
        }else{
            $message = array('status' => 2, 'info' => $data);
        }
        
        echo json_encode($message);

    }
    
}

?>