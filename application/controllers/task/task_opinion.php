<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Task_opinion extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
	}

	/**
	 *	用户提意见 
	 */	
	public function task_get_opinion(){
        exit();
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }

        $this->load->view('task/opinion');
		// echo 1;

	}

	/**
	 * 用户完成评论提交
	 */
	public function obtain_opinion(){
        exit();
		$this->load->model('auto/userauth_model');
		$this->load->model('task/user_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }


		$wx_id = $_SESSION['userinfo']['user_id'];
		$this->load->helper('safe_helper');
		$this->load->language('task','chinese');//载入文字信息
		$wx_id = verify_id($wx_id);
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}

		post_check();
        $post = $this->input->post();

        $this->load->model('task/opinion_model');
        $str = $this->opinion_model->addopinion($wx_id,$post);
      	if (!$str) {
      		return false;
      	}

		$data['success'] = $this->lang->line('up_help_success');
		$this->load->view('task/have_get_help',$data);
	}

}