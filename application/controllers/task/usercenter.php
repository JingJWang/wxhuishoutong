<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Usercenter extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('task/user_model');
	}

	/**
	*
	*任务中心主页
	*
	*/
	public function taskcenter(){
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserPort('/index.php/task/usercenter/taskcenter','www.recytl.com');//判断终端

        if($this->userauth_model->UserIsLogin()){//校验用户是否登录，登录则获取用户信息
			$wx_id = $_SESSION['userinfo']['user_id'];
			if (!is_numeric($wx_id)) {
				exit();
			}
			$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
			if ($str === false ) {
				return false;
			}
			$userinfo = $this->user_model->userandlevel($wx_id);
			$data['userinfo'] = $userinfo;
			$levels = $this->user_model->level_medal($wx_id);//获取勋章个数
			$data['medal_num'] = count($levels);
			$data['wx_id'] = $wx_id;
        }
        else{
			$_SESSION['LoginBackUrl'] = '/index.php/task/usercenter/taskcenter';
        }
        if (isset($_SESSION['userinfo']['Login_openid'])) {
			$this->load->model('common/wxcode_model');
			$userwxinfo = $this->wxcode_model->userinfo($_SESSION['userinfo']['Login_openid']);//判断用户是否关注
			if ($userwxinfo['subscribe']==0) {
				$is_attention = 1;
		    }else{
		    	$is_attention = -1;
		    }
        }else{
		    $is_attention = -1;
        }

        $all_user = $this->user_model->get_Auser();
		$data['all_user'] = $all_user+$this->config->item('task_number');
		$this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
		$data['taskinscr']  = array();
		if ($this->zredis->link === true) {
			$taskinscr = $this->zredis->_redis->zrange('taskIndexScroll',0,-1);//轮播
			$data['taskinscr'] = $taskinscr;
		}
		$this->config->load('task',true);//配置项加载
		$allreward = $this->config->item('task');
		$data['announ'] = $allreward['announcements'];
		
		$data['news'] = 0;
		if(!isset($_SESSION['userinfo']['news'])){
			$data['news'] = 1;
			// $data['is_attention'] = 1;
			$data['is_attention'] = $is_attention;
			$_SESSION['userinfo']['news'] = 1;
		}
		$this->load->view('task/usercenter',$data);
	}

	/**
	*
	*用户的等级信息以及升级后可以选择称号
	*
	*/
	public function levelselect(){ 
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

		$userinfo=$this->user_model->userandlevel($wx_id);
		$data['userinfo'] = $userinfo;

	  	$levels = $this->user_model->level_medal($wx_id);
		$data['levels'] = $levels;

	  	$this->load->view('task/userlevel',$data);

	}

	/**
	*
	*选择称号后的逻辑
	*
	*/
	public function select_level_title(){
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserIsLoginJump('/index.php/task/usercenter/taskcenter');
	    
		$this->load->helper('safe_helper');//载入安全函数
		post_check();

		$wx_id = $_SESSION['userinfo']['user_id'];
		$wx_id = verify_id($wx_id);
		$wx_id = safe_str($wx_id);//转义wx_id，保证合法。
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}
		$this->load->language('task','chinese');//载入文字信息
		
		// 获取用户信息
		$post = $this->input->post();
		if (!isset($post['select_title']) || empty($post['select_title'])) {
			$data['error'] = $this->lang->line('no_select');
		}else{
			$level_id = $post['select_title'];
			$level_id = verify_id($level_id);//判断此变量是否为数字，不是则出错。
	
			$data = $this->user_model->getlevel($level_id,$wx_id);
			
			if ($data['levelinfo']['level_id']==$data['userinfo']['level_id'] || $data['level_num']['level_num']>0) {//	后面的是如果此等级的已经有了，侧不能再选择里。
				$data['error'] = $this->lang->line('have_select');

			}elseif ($data['levelinfo']['level_num']>$data['userinfo']['level_num']) {
				$data['error'] = $this->lang->line('cannotselect');
			}else{
				$this->user_model->up_level($level_id,$wx_id);
				$data['success'] = $this->lang->line('success_select');
			}
		}
		$this->load->view('task/have_get_title',$data);

	}

	/**
	 * 获取用户勋章
	 */
	public function my_medal(){
		exit();
        $this->load->model('common/wxcode_model');
        $this->load->model('task/tasks_model');

		if(!empty($_GET['nums'])){
            $wx_id=$num_id=$_GET['nums'];
        }else{
        	$wx_id = $_SESSION['userinfo']['user_id'];
        }

		$data['appid'] = $this->config->item('APPID');

        $center_info = $this->tasks_model->getextentnum($wx_id);

		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$levels = $this->user_model->level_medal($wx_id);
		if (!empty($levels)) {
			foreach ($levels as $k => $v) {
				if (empty($v['level_content_url'])) {
					$levels[$k]['level_share_url'] = '';
				}else{
					$levels[$k]['level_share_url'] = urlencode(site_url('task/otherget/getothersay')).'?extendnum='.$center_info[0]['center_extend_num'].'_'.$v['level_content_url'];
					$levels[$k]['level_share_url'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$data['appid'].'&redirect_uri='.$levels[$k]['level_share_url'].'&response_type=code&scope=snsapi_base#wechat_redirect';
				}
			}

		}
		if (isset($num_id) && $num_id!='') {
			
			$data['levels'] = $levels;
			$data['num'] = count($levels);

			// $data['appid'] = $this->config->item('APPID');
	    	$data['taskshareurl']=site_url('task/usercenter/my_medal').'/'.$wx_id;
	    	$data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置

			$this->load->view('task/themedal',$data);
			return '';

		}elseif(!isset($_SESSION['userinfo']['userlogin']) || $_SESSION['userinfo']['userlogin'] != 'ok' || !isset($_SESSION['userinfo']['user_id']) || empty($_SESSION['userinfo']['user_id'])){//没有session则跳转界面
			$url=site_url("nonstandard/system/welcome");
			header("Location: $url");
	        return '';
	    }

		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}

		// $levels = $this->user_model->level_medal($wx_id);
		$data['levels'] = $levels;
		$data['num'] = count($levels);
		$userinfo=$this->user_model->userandlevel($wx_id);
		$data['userinfo'] = $userinfo;

	    // $data['wxid'] = $wx_id;//用户的wxid。
	    $data['taskshareurl']=site_url('task/usercenter/my_medal').'?nums='.$wx_id;
	   	$data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
	   	
		$this->load->view('task/themedal',$data);

	}

	/**
	 * 用户排名
	 */
	public function user_rank(){
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

		$userinfo = $this->user_model->userandlevel($wx_id);
		$data['userinfo'] = $userinfo;

		$all_user = $this->user_model->userrank();
		foreach ($all_user as $k => $v) {
			if ($v['wx_id']==$wx_id) {
				$theUserRank = $k+1;
				break;
			}
		}
		if (!isset($theUserRank)) {
			$theUserRank = 0;
		}
		
		$data['theUserRank'] = $theUserRank;
		$data['all_user'] = $all_user;
		$data['userinfo'] = $userinfo;
		
		$this->load->view('task/ranking',$data);

	}
	/**
	 * 查看用户是否注册
	 */
	public function isreg(){
        // $this->load->model('auto/userauth_model');
        //校验是否已经登录
        // $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }
        return '';
	}
}