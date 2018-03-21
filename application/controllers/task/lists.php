<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Lists extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('task/list_model');
        $this->load->model('task/user_model');
	}
	/**
	 * 读取用户任务
	 * @param 	int 	wx_id 	用户的id
	 * @return 	成功返回json，任务列表 | 失败返回json
	 */
	public function tasklist(){
		$this->load->model('auto/userauth_model');
		if(!$this->userauth_model->UserIsLogin()){//校验是否绑定手机号码
			$allfinishlist 	  = $this->list_model->AlltaskInfo();
			$this->config->load('task',true);//配置项加载
			$C_takes = $this->config->item('task');
			$usertasktype = array(//将用户的任务类型数量放到一个数组，重新命名用于任务得到对应的数据
				'task_sign' => 0,
				'task_turnover' => 0,
				'task_share' => 0,
				'task_invite_u' => 0,
				'task_invite_m'=>0
			);
			// 检查用户回收金额和邀请用户变化 -结束
			$usertaskinfo['0']['center_klegdtime'] = 0;
			$usertaskinfo['0']['center_plgametime'] = 0;
			$data = array(
				'finishlist' => array(),
				'nofinishlist' => $allfinishlist['allnofinishlist'],
				'usertaskinfo' => $usertaskinfo,
				'usertasktype' => $usertasktype,
				'task_process' => $this->config->item('task_process'),
				'task_types' => $this->config->item('task_types'),
				'task_types_data' => $this->config->item('task_types_data'),
				'reward_type' => $this->config->item('reward_type'),
				'c_task' => $C_takes['click_tasks'],
			);
			$data['sign_task'] = -1;
        	Universal::Output($this->config->item('request_succ'),'','',$data);
		}
        $wx_id = $_SESSION['userinfo']['user_id'];
		$this->load->language('task','chinese');//加载任务语言包
		if (!is_numeric($wx_id)) {
			$response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optiontypenull'),'url'=>'','data'=>'');
            echo json_encode($response);exit();
		}
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}
		$usertaskinfo			 = $this->user_model->usertaskinfo($wx_id);
		$usertaskinfo['0']['strtotime'] = strtotime(date('Y-m-d'));
		$sign_task = ($usertaskinfo['0']['center_laster_sign']>$usertaskinfo['0']['strtotime'])?1:-1;
		$allfinishlist 	  = $this->list_model->tasknofinishlist($wx_id);//获取未完成的任务 以及过期的任务类型
		$allnofinishlist  = $allfinishlist['allnofinishlist'];
		$finishlist		  = $allfinishlist['finishlist'];
		if (isset($allnofinishlist['over_task_types']) && $allnofinishlist['over_task_types']!='') {//如果有任务过期，判断循环是否结束（5种类型的）。
			$allnofinishlist['over_task_types']=rtrim($allnofinishlist['over_task_types'],',');
			$voer_type = array_unique(explode(',', $allnofinishlist['over_task_types']));//去除可能重复的元素
			$this->load->model('task/taskfinish_model');
			foreach ($voer_type as $k => $v) {//最多5次循环
				$this->taskfinish_model->is_finish_this_cycle($wx_id,$v);
			}
		}
		$nofinishlist = array();
		if(isset($allnofinishlist['main'])){
			foreach ($allnofinishlist['main'] as $k => $v) {//对比每个主线任务，如果主线任务没做完，无法做其它任务
				if ($v['task_limit_other'] == -2) {
					$nofinishlist['main'][] = $v;
				}
			}
		}
		if (empty($nofinishlist)) {//确保要进行限制的主线任务已经完成
			if (!empty($allnofinishlist['votes'])) {
				$nofinishlist['votes']  = $allnofinishlist['votes'];
			}
			if (!empty($allnofinishlist['advs'])) {
				$nofinishlist['advs']  = $allnofinishlist['advs'];
			}
			if (!empty($allnofinishlist['main'])) {
				$nofinishlist['main']  = $allnofinishlist['main'];
			}
			if (!empty($allnofinishlist['active'])) {
				$nofinishlist['active']  = $allnofinishlist['active'];
			}
			if (!empty($allnofinishlist['jinghua'])) {
				if ($allnofinishlist['jinghua'][0]['task_limit_other']>=1) {
					foreach ($finishlist as $k => $v) {
						if ($v['task_id']==$allnofinishlist['jinghua'][0]['task_limit_other']) {
							$nofinishlist['jinghua'] = $allnofinishlist['jinghua'];
							break;
						}
					}
				}else{
					$nofinishlist['jinghua'] = $allnofinishlist['jinghua'];
				}
			}
			if (!empty($allnofinishlist['other'])) {
				$nofinishlist['other'] = $allnofinishlist['other'];
			}	
		}
		$usertasktype = array(//将用户的任务类型数量放到一个数组，重新命名用于任务得到对应的数据
			'task_sign' => $usertaskinfo[0]['this_cycle']['center_sign'],
			'task_turnover' => $usertaskinfo[0]['this_cycle']['center_turnover'],
			'task_share' => $usertaskinfo[0]['this_cycle']['center_share'],
			'task_invite_u' => $usertaskinfo[0]['this_cycle']['center_invite_u'],
			'task_invite_m'=>$usertaskinfo[0]['this_cycle']['center_invite_m']
		);
		// 以下为检查用户回收金额和邀请用户变化
		$all_turnover   = $this->list_model->get_user_order($wx_id);//查看用户回收金额。
		if ($all_turnover['all_turnover'] > $usertaskinfo[0]['center_turnover']) {//如果有回收金额增加，更新表单
        	$usertasktype['task_turnover'] = $this->upaddnum($wx_id,$all_turnover['all_turnover'],$usertaskinfo['0']['center_turnover'],$usertasktype['task_turnover'],'2',$usertaskinfo);
        	if ($usertasktype['task_turnover']===false) {
        		$response=array('status'=>$this->config->item('request_fall'),'msg'=>$this->lang->line('order_optiontypenull'),'url'=>'','data'=>'');
            	echo json_encode($response);exit();
        	}
		}
		$this->config->load('task',true);//配置项加载
		$C_takes = $this->config->item('task');
		// 检查用户回收金额和邀请用户变化 -结束
		$data = array(
			'finishlist' => $finishlist,
			'nofinishlist' => $nofinishlist,
			'usertaskinfo' => $usertaskinfo,
			'usertasktype' => $usertasktype,
			'task_process' => $this->config->item('task_process'),
			'task_types' => $this->config->item('task_types'),
			'task_types_data' => $this->config->item('task_types_data'),
			'reward_type' => $this->config->item('reward_type'),
			'c_task' => $C_takes['click_tasks'],
		);
		$data['sign_task'] = $sign_task;
		$response=array('status'=>$this->config->item('success'),'msg'=>'','url'=>'','data'=>$data);
        echo json_encode($response);exit();
	}
	/**
	 * 更新邀请人数，回收金额
	 * @param 	int 	wx_id 		 用户的id
	 * @param 	int 	allnum 		 用户此类型实际的数值
	 * @param 	int 	nownum	 	 用户此类型任务现在的数值
	 * @param 	int 	nowcynum	 用户此轮的数值
	 * @param 	int 	type 		 要更新的任务类型
	 * @param 	array   usertaskinfo 用户的任务信息
	 * @return 	int 	nowcynum 	 返回次轮更新好的数值
	 */
	private function upaddnum($wx_id,$allnum,$nownum,$nowcynum,$type,$usertaskinfo){
		$this->load->model('task/taskfinish_model');
		$nowcynum = $allnum-$nownum+$nowcynum;
		$str = $this->taskfinish_model->updatauserinfo($wx_id,$type,$allnum);
		if (!$str) {
			return false;
		}
		$str = $this->taskfinish_model->upadtacycleinfo($wx_id,$type,$usertaskinfo[0],$nowcynum);
		if (!$str) {
			return false;
		}
		return $nowcynum;
	}
	/**
	 * 我的任务界面
	 */
	public function mytask(){

		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('task/mobile');
            return false;
        }
	    
	    $wx_id = $_SESSION['userinfo']['user_id'];

		$this->load->language('task','chinese');//加载任务语言包
		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
		if ($str === false ) {
			return false;
		}

		$usertaskinfo			 = $this->user_model->usertaskinfo($wx_id);


		$usertasktype = array(//将用户的任务类型数量放到一个数组，重新命名用于任务得到对应的数据
			'task_sign' => $usertaskinfo[0]['this_cycle']['center_sign'],
			'task_turnover' => $usertaskinfo[0]['this_cycle']['center_turnover'],
			'task_share' => $usertaskinfo[0]['this_cycle']['center_share'],
			'task_invite_u' => $usertaskinfo[0]['this_cycle']['center_invite_u'],
			'task_invite_m'=>$usertaskinfo[0]['this_cycle']['center_invite_m']
		);

		$data = $this->list_model->goingtask($wx_id);
		
		
		$data = array(
			'mytask' 	   => $data['mytask'],
			'usertaskinfo' => $usertaskinfo,
			'usertasktype' => $usertasktype,
			'task_process' => $this->config->item('task_process'),
			'task_types'   => $this->config->item('task_types'),
			'task_types_data' => $this->config->item('task_types_data')
		);

		$this->load->view('task/list_going_task',$data);
	}
}