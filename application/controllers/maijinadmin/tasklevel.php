<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:微信粉丝层
 */
class Tasklevel extends RA_Controller {

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/tasklevel_model');
        $this->load->library('pagination');
        $this->load->helper('url');
    }

    //获取全部奖励
    public function getAllevel(){
        
        $this->checkauto();//判断是否登陆
        $data = array();
        $orderoption['page'] = $this->uri->segment(4,0);
        $orderoption['per_page'] = $this->config->item('PAGENUM_BACK');
        $data = $this->tasklevel_model->select_level_list($orderoption);
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
        $this->load->view('maijinadmin/tasklevel', $data);
        $this->load->view('maijinadmin/common/footer', $data);

    }

    public function add_editor_level(){

        $this->checkauto();//判断是否登陆
        $this->load->helper('safe_helper');
        $post = $this->input->post();

        post_check();//可能插入用户的信息
        if ($post['lname']=='' || $post['levelnum']=='' || $post['limg']==''||$post['lfund']=='') {
            $info = "信息不完整";
            $message = array('status'=>1,'info'=>$info);
            echo json_encode($message);
            exit();
        }

        
        $post['levelnum'] = verify_id($post['levelnum']);//查看等级是否整数。
        $post['lfund'] = verify_id($post['lfund']);
        $post['lstatus'] = verify_id($post['lstatus']);

        if ($post['num']=='') {
            $addlevels = array(
                'level_num' => $post['levelnum'],
                'level_name' => $post['lname'],
                'level_img' => $post['limg'],
                'level_integral' => $post['lfund'],
                'level_status' => $post['lstatus'],
                'level_jointime' => time()
            );
            $str = $this->tasklevel_model->addlevel($addlevels);
            if ($str == false) {
                $message = array('status' => 8, 'info' => '更新出错');
                echo json_encode($message);
            }else{
                $message = array('status' => 7, 'info' => '更新成功');
                echo json_encode($message);
            }
        }else{
            $post['num'] = verify_id($post['num']);
            $uplevel = array(
                'level_num' => $post['levelnum'],
                'level_name' => $post['lname'],
                'level_img' => $post['limg'],
                'level_integral' => $post['lfund'],
                'level_status' => $post['lstatus'],
                'level_updatetime' => time()
            );
            $str = $this->tasklevel_model->uplevel($uplevel,$post['num']);
            if ($str == false) {
                $message = array('status' => 8, 'info' => '更新出错');
                echo json_encode($message);
            }else{
                $message = array('status' => 7, 'info' => '更新成功');
                echo json_encode($message);
            }

        }

    }

    /**
     * 此处为文件上传函数，接收ajax传来的数据,返回已经上传文件的地址
     */
    public function upload(){

        $this->load->helper('safe_helper');
        $info = safe_upimg($_FILES['file'],2);//后一个参数是文件大小，单位为M。
                // var_dump($info);exit;
        if ($info==1) {
            //3,组合上传路径
            $dir = './static/task/images/levelimg/';
            //4,没有路径创建
            is_dir($dir) || mkdir($dir,0755,true);
            //5,组合上传文件 完整路径
            $filename = time().mt_rand(0,10000);
            $ext = strrchr($_FILES['file']['name'],'.');
            $fullname = $dir.$filename.$ext;
            //5,执行上传
            if(is_uploaded_file($_FILES['file']['tmp_name'])){
                move_uploaded_file($_FILES['file']['tmp_name'], $fullname);
            }else{
                    $data['error'] = $this->lang->line('up_error');
            }
            $fullname = ltrim($fullname,'.');
            $message = array('status' => 1,'info' => $fullname);
            echo json_encode($message);
        }else{
            // $data['error'] = $info;
            $message = array('status'=>2,'info'=>$info);
            echo json_encode($message);
        }
    }
    

    //删除奖励
    public function delectlevel(){

        $this->checkauto();
        $this->load->helper('safe_helper');//载入安全函数

        $id = $this->input->post('id',true);
        $id = verify_id($id);     
        $data=$this->tasklevel_model->delect_level($id);

        if($data === false){
            $message = array('status' => 3000, 'info' => '禁用失败!');
        }else{
            $message = array('status' => 1000, 'info' => '禁用成功!');
        }
        echo json_encode($message);

    }

    //获取要修改的等级的信息
    public function uptasklevel(){
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

        $data = $this->tasklevel_model->get_level($id);
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