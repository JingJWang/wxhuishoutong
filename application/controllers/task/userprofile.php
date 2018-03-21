<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Userprofile extends CI_Controller {

	function __construct(){
		exit();
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('task/profile_model');
        $this->load->model('task/user_model');
	}

	/**
	 *打开用户档案 
	 */	
	public function open(){
		exit();
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }
	    
		$wx_id = $_SESSION['userinfo']['user_id'];
		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}
		$userinfo = $this->profile_model->getuserpro($wx_id);
		
		if ($userinfo) {
			$usertaskinfo = $this->user_model->usertaskinfo($wx_id);
			$profiles = $this->profile_model->taskprofileinfo($wx_id);
			$data['userinfo'] = $userinfo[0];
			$data['usertaskinfo'] = $usertaskinfo[0];
			$data['profiles'] = $profiles;
			$this->load->view('task/userprofile',$data);
			// var_dump($data);
		}else{
			$this->load->view('task/add_profile');
		}
	}

	/**
	 * 档案未打开则让用户注册
	 */
	public function add_profile(){
		exit();
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }

		$wx_id = $_SESSION['userinfo']['user_id'];
		$data = $this->input->post();
		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}
		post_check();


		$this->load->language('task','chinese');//载入文字信息
		
		if ($data['birthday'] == '') {
			$error['error'] = $this->lang->line('error_nodate');
			$this->load->view('task/have_get_help',$error);
			return '';
		}else{
			list($y,$m,$d)=explode('-',$data['birthday']);
			$y = intval($y); $m = intval($m); $d = intval($d);//转换数字
		}
		if (checkdate($m,$d,$y)==false){
			$error['error'] = $this->lang->line('error_date');
			$this->load->view('task/have_get_help',$error);
			return '';
		}elseif ($data['name'] == ''){
			$error['error'] = $this->lang->line('info_no_full');
			$this->load->view('task/have_get_help',$error);
			return '';
		}elseif($data['selectc']==1 || $data['selectc']==2){
			$error['error'] = $this->lang->line('error_where');
			$this->load->view('task/have_get_help',$error);
			return '';
		}
		

		$str = $this->profile_model->adduserpro($wx_id,$data);
		if ($str) {
			$url = site_url('task/userprofile/open');
			echo "<script>location.href='$url';</script>";
		}else{
			echo '此用户已经注册了！';
		}
	}


	/**
	 * 编辑需要帮助中心
	 */
	public function addgoodpreinfo(){
		exit();
		$this->load->model('auto/userauth_model');
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

		if (!empty($_POST)) {

			$post = $this->input->post();
			post_check();

			$data['error'] = '';
			if (empty($_FILES) || count($_FILES)>1 || $post['name']=='' || $post['idcard']=='' || $post['iphone']=='' || $post['address']=='' || $post['why_help']=='' || $post['date']=='') {
				// die('要上传一张图片。');
				$data['error'] = $this->lang->line('need_img');
				$data['not_full'] = $this->lang->line('info_no_full');
			}elseif(!preg_match('/^((13[0-9])|145|147|(15[0-35-9])|(18[0-9]))[0-9]{8}$$/', $post['iphone'])){
				$data['error'] = $this->lang->line('not_iphone');
			}elseif(!preg_match('(^\d{15}$)',$post['idcard']) && !preg_match('(^\d{17}([0-9]|X)$)',$post['idcard'])){
				$data['error'] = $this->lang->line('not_idcard');
			}else{
				$info = safe_upimg($_FILES['imgs'],2);//后一个参数是文件大小，单位为M。
				// var_dump($info);exit;
				if ($info==1) {
					//3,组合上传路径
					$dir = './public/task/needhelpimg/';
					//4,没有路径创建
					is_dir($dir) || mkdir($dir,0755,true);
					//5,组合上传文件 完整路径
					$filename = time().mt_rand(0,10000);
					$ext = strrchr($_FILES['imgs']['name'],'.');
					$fullname = $dir.$filename.$ext;
					//5,执行上传
					if(is_uploaded_file($_FILES['imgs']['tmp_name'])){
						move_uploaded_file($_FILES['imgs']['tmp_name'], $fullname);
						$post['help_imgs'] = $fullname;
						$str = $this->profile_model->addneedhelp($post,$wx_id);
						if (!$str) {
							return false;
						}else{
							$data['success'] = $this->lang->line('up_help_success');
						}
					}else{
						$data['error'] = $this->lang->line('up_error');
					}

				}else{
					$data['error'] = $info;
				}
			}

			$this->load->view('task/have_get_help',$data);
		}else{
			$this->load->view('task/needhelpinfo');
		}
	}


	/**
	 * 好人好报
	 */
	public function goodperson(){
		exit();
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }

		$wx_id = $_SESSION['userinfo']['user_id'];
		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}

		$userinfo = $this->user_model->usertaskinfo($wx_id);

		//得到说明内容
		$this->load->model('maijinadmin/instruction_model');
		$instruction = 7;
		$data['taskIntroduction'] = $this->instruction_model->get_instruction($instruction);

		$data['userinfo'] = $userinfo[0];
		$this->load->view('task/goodperson',$data);
	}


	/**
	 * 得到合同内容
	 */
	public function constract(){
		exit();

		$this->load->model('maijinadmin/instruction_model');
		$instruction = 6;
		$data['taskIntroduction'] = $this->instruction_model->get_instruction($instruction);
		$this->load->view('task/needhelpcontract',$data);

	}

	/**
	 * 任务系统介绍
	 */
	public function taskIntroduction(){
		exit();

		$this->load->model('maijinadmin/instruction_model');
		$instruction = 5;
		$data['taskIntroduction'] = $this->instruction_model->get_instruction($instruction);
		$this->load->view('task/needhelpcontract',$data);

	}
}