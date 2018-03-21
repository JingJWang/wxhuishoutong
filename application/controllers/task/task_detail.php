<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Task_detail extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('task/tasks_model');
        $this->load->model('task/user_model');
	}
	/**
	 * 获取奖励
	 * 暂不用
	 */
	public function getaward(){
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
		$task_id = $this->uri->segment(4);
		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$task_id = verify_id($task_id);

		$thislog=$this->tasks_model->getonetasklog($wx_id,$task_id);//判断用户是否满足领取条件
		if (empty($thislog) || $thislog[0]['task_process']!=3) {
			$view['messageinfo'] = '您的任务没完成或已经领取了奖励';
			$this->load->view('exception/busy',$view);
			return '';
		}

		$getonetask = $this->tasks_model->getonetask($task_id);//获取任务信息
		
		$data=array(
			'getonetask' => $getonetask[0],
		);

		if (count($data['getonetask']['rewards'])==1) {

			$putstr = '<form action="'.site_url('task/task_detail/obainaward').'/'.$data['getonetask']['task_id'].'" method="post" name="theform">
					<input type="hidden" value="on" name="'.$data['getonetask']['rewards'][0]['reward_id'].'" />
          		</form>
				<script type="text/javascript">
					function load_submit(){
						document.theform.submit()
					}
					load_submit();
				</script>
          		';
			echo $putstr;exit();
		}

		$this->load->view('task/reward_select',$data);

	}
	/**
	 * 获取奖励信息
	 * @param   int(post)     task_id      任务id
	 */
	function rewardInfo(){
        $this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            return false;
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
		$task_id = $this->input->post('tid',true);
		if (!is_numeric($wx_id)||!is_numeric($task_id)) {
		    Universal::Output($this->config->item('request_fall'));
		}
        $thislog=$this->tasks_model->getonetasklog($wx_id,$task_id);//判断用户是否满足领取条件
		if (empty($thislog) || $thislog[0]['task_process']!=3) {
		    Universal::Output($this->config->item('request_fall'),'您的任务没完成或已经领取了奖励','','');
		}
		$getonetask = $this->tasks_model->getonetask($task_id);//获取任务信息
		$result['rewards'] = $getonetask['0']['rewards'];
		$result['num'] = $getonetask['0']['reward_num'];
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
	/**
	 * 领取奖励 
	 * 已经不用
	 */
	public function obaward(){
		exit();
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }

		$this->load->model('task/taskfinish_model');
		// $wx_id = 5;
		$wx_id = $_SESSION['userinfo']['user_id'];
		$task_id = $this->uri->segment(4);
		$reward_id=$this->input->post();
		$this->load->helper('safe_helper');
		$task_id = verify_id($task_id);
		$wx_id = verify_id($wx_id);
		post_check();

		$this->load->language('task','chinese');

		$thistask=$this->tasks_model->getonetask($task_id);
		if (empty($thistask)) {//判断用户是否有次任务
			$data['result'] = 'no_the_task';
		}elseif (empty($reward_id)) {//判断是否传来了数据
			$data['result'] = 'reward_num_error';
		}elseif (count($reward_id)!=$thistask[0]['reward_num']) {//判断是否选择数量是否正确。
			$data['result'] = 'reward_num_error';
		}else{
			$reward_ids = explode(' ',$thistask[0]['reward_id']);//判断用户选择的奖励是否与任务给的匹配
			foreach ($reward_id as $k => $v) {
				$is_right = 0;
				foreach ($reward_ids as $ke => $va) {
					if ($k == $va) {
						$is_right = 1;
						break;
					}
				}
			}
			if ($is_right == 0) {
				$data['result'] = 'no_match';
			}else{
				$thislog=$this->tasks_model->getonetasklog($wx_id,$task_id);//判断用户是否满足领取条件。
				if (empty($thislog) || $thislog[0]['task_process']!=3) {
					$data['result'] = 'no_finish';
				}else{
					$str=$this->tasks_model->obtainreward($wx_id,$reward_id,$task_id,$thistask['0']['info_name']);
					if ($str == 'get_fail') {
						$data['result'] = 'get_fail';
					}elseif ($str == 'no_fund') {//基金不够了
						$data['result'] = 'no_fund';
					}elseif($str == 'red_error'){
						$data['result'] = 'red_error';
					}elseif ($str) {
						$data['str'] = $str;
						if (isset($str['is_level'])==1) {
							$userinfo=$this->user_model->userandlevel($wx_id);
							$data['userinfo'] = $userinfo;
							$data['result'] = 'success_and_up';
						}else{
							$data['result'] = 'get_success';
						}
						$data['get_reward_bonus']=$data['get_reward_integral']=$data['get_reward_all_integral']=$data['get_reward_fund']=0;
						foreach ($thistask[0]['rewards'] as $k => $v) {
							$data['get_reward_bonus']+=$v['reward_bonus'];
							$data['get_reward_integral']+=$v['reward_integral'];
							$data['get_reward_all_integral']+=$v['reward_all_integral'];
							$data['get_reward_fund']+=$v['reward_fund'];
						}
						$str = $this->taskfinish_model->is_finish_this_cycle($wx_id,$thistask[0]['task_type']);//更新循环任务
						if (!$str) {
							return false;
						}elseif($str == 1){
							$data['result'] = 'cycle_finish';
						}					
    	    		}else{
    	    			return false;
    	    		}
	    	    }
    	    }
    	}

		$this->load->view('task/getreward_success',$data);
		
	}
	/**
	 * 领取奖励
	 * @param 	int(session) 	wx_id 		用户的id
	 * @param 	int(get)		task_id 	任务的id
	 * @param 	array(post) 	reward_id 	奖励的id 	
	 */
	function obainaward(){
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            exit();
        }
		$this->load->language('task','chinese');
		$wx_id = $_SESSION['userinfo']['user_id'];
		$task_id = $this->input->post('id',true);
		if (!is_numeric($wx_id) || !is_numeric($task_id)) {
			exit();
		}
		$rewards=$this->input->post('award',true);
		$rewards = rtrim($rewards, ",");
		$reward_id = explode(',',$rewards);
		$this->load->model('task/reward_model');
		$result = $this->reward_model->checktask($wx_id,$task_id,$reward_id);//检查任务与奖励，并用户，奖励信息
		if ($result === false) {
		    Universal::Output($this->config->item('request_fall'),'没有可领取的奖励','','');
		}
		$thislog=$this->tasks_model->getonetasklog($wx_id,$task_id);//判断用户任务是否完成。
		if (empty($thislog) || $thislog[0]['task_process']!=3) {
		    Universal::Output($this->config->item('request_fall'),'任务未完成','','');
		}
		$rewards = $result['reward'];
		$userinfo = $result['userinfo'];
		$newlevel = '';//记录新等级
		if ($rewards['all_integral']>0) {//检查是否可以升级
			$now_integral = $userinfo['0']['center_all_integral']+$rewards['all_integral'];
        	$return = $this->reward_model->checklevel($now_integral,$userinfo['0']['level_num']);
        	if ($return===false) {
            	exit();
        	}
        	if ($return>0) {
        		$newlevel = $return;
        	}
		}
		if ($rewards['bonus']>0) {//如果有奖金，再次检测。
			$check = $this->tasks_model->VerifyInfo($wx_id,$task_id);
			if ($check === false) {
				exit();
			}
		}
		$this->db->trans_begin();//开启事务
        $this->reward_model->user_have = $userinfo['0'];
        $this->reward_model->reward = $result['reward'];
        $return = $this->reward_model->obtainward($wx_id,$result['tasktype'],$task_id,$newlevel,$reward_id);
        if ($return === false) {
            $this->db->trans_rollback();
        	if ($this->reward_model->msg!='') {
    		    Universal::Output($this->config->item('request_fall'),$this->lang->line($this->reward_model->msg),'','');
        	}
        }
		$this->load->model('task/taskfinish_model');
        $return = $this->taskfinish_model->is_finish_this_cycle($wx_id,$result['tasktype']);//判断循环
        if ($return === false) {
            $this->db->trans_rollback();
        	exit();
        }
        $return = $this->reward_model->Chinviter($wx_id,$userinfo['0']);//检查邀请
        if ($rewards['all_integral']>0) {//添加通花日志
        	$this->reward_model->thlog($wx_id,$rewards['all_integral'],'做'.$result['taskname'].'任务');	
        }
        if ($this->db->trans_status() === false){
            $this->db->trans_rollback();
        	$str = $this->zredis->_redis->set('taskinfouptime',time());
            exit();
        }
		$this->db->trans_commit();
		$this->reward_model->IndexScroll($rewards,$result['tasktype'],$result['taskname']);
		$data['str']['add_bonus']=$rewards['bonus'];//数据处理完，把要显示给用户的数据给调用出来
		$data['str']['add_integral']=$rewards['integral'];
		$data['str']['add_all_intergral']=$rewards['all_integral'];
		$data['str']['add_fund']=$rewards['fund'];
		if ($newlevel!='') {
			$userinfo=$this->user_model->userandlevel($wx_id);
			$data['userinfo'] = $userinfo;
		}
        $this->load->model('common/wxcode_model');
        $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],108);//设置微信分组 任务完成，转盘，报单组
		Universal::Output($this->config->item('request_succ'),'','',$data);
	}
}