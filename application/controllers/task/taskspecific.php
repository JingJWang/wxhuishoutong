<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Taskspecific extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('task/tasks_model');
        $this->load->model('task/user_model');
	}
	/**
	 * 检查任务进度是否完成
	 * @param        int     id       任务的id
	 */
	function checktaskplan(){
		$this->load->model('auto/userauth_model');
        $result = $this->userauth_model->UserCheck(2,$_SESSION);//校验是否已经登录
        $method=$this->input->is_ajax_request();
        if(empty($_SESSION['userinfo']['user_mobile'])||$result===false
           ||!is_numeric($task_id = $this->input->post('id',true))
           ||!is_numeric($wx_id=$_SESSION['userinfo']['user_id'])){//校验是否继续
			Universal::Output($this->config->item('request_fall'),'','','');
        }
        $result = $this->tasks_model->checktask($wx_id,$task_id);
        if ($result==false) {
		    Universal::Output($this->config->item('request_fall'),'','','');
        }
		Universal::Output($this->config->item('request_succ'),'','',$result);
	}
	/**
	 * 任务信息，可以领取任务。
	 * @param 	int 	task_id 	任务id
	 */
	function task_detail(){
        $task_id = $this->input->post('tid',true);
        $this->load->model('auto/userauth_model');
        if (!$this->userauth_model->UserIsLoginJump('/view/task/taskDetail.html?id='.$task_id,true)) {
        	if ($task_id==5) {//游戏任务
				$data['getonetask']		 = $this->tasks_model->getonetask($task_id);//此任务的信息
				$data = $this->judgtask($data,'-1');
				$data['task_type'] = $this->tasks_model->getypeinfo($data['getonetask']['0']['task_type'],
							$data['task_type_id']);
				Universal::Output($this->config->item('request_succ'),'','',$data);
        	}
            Universal::Output($this->config->item('request_fall'),'需要登录才可以做任务哦！','/index.php/nonstandard/system/Login');
        }
		$wx_id 		  = $_SESSION['userinfo']['user_id'];
		if (!is_numeric($task_id)||!is_numeric($wx_id)) {
			Universal::Output($this->config->item('request_fall'),'','','');
		}
		$data['getonetask']		 = $this->tasks_model->getonetask($task_id);//此任务的信息
		$data['getonetask']['0']['isShareAdd']=json_decode($data['getonetask']['0']['task_difcontent'],true)['shde'];
		unset($data['getonetask']['0']['task_difcontent']);
		$data['getonetask']['0']['extend_num']='';
		if ($data['getonetask']['0']['isShareAdd']==1) {
			$center_info = $this->tasks_model->getextentnum($wx_id);//获取用户的推广码
			$data['getonetask']['0']['extend_num']=$center_info['0']['center_extend_num'];
		}
		if (!isset($data['getonetask']['0']['task_id'])) {
			Universal::Output($this->config->item('request_fall'),'','','');
		}
		$thislog=$this->tasks_model->getonetasklog($wx_id,$task_id);//判断用户此任务进行过程
		if (empty($thislog)) {
			$task_data = $this->get_task($task_id,$wx_id,$data['getonetask']);//获取任务
			if (isset($task_data['error']) && $task_data['error']!='') {
			    Universal::Output($this->config->item('request_fall'),'','','');
			}
			if (isset($task_data['to_num']) && $task_data['to_num']==3) {
				$taskfinish = 1;
			}
		}elseif ($thislog['0']['task_process'] == 2) {
			$task_data = $this->check_task($task_id,$wx_id,$data['getonetask']);//检查任务是否完成
			if (isset($task_data['to_num']) && $task_data['to_num']==3) {
				$taskfinish = 1;
			}
		}
		$data = $this->judgtask($data,$wx_id);
		if ((isset($taskfinish) && $taskfinish == 1)||(!empty($thislog) && $thislog['0']['task_process'] == 3)) {
			$data['getonetask']['0']['get_rewards'] = '领取奖励';
			$data['getonetask']['0']['taskid'] = $task_id;
		}
		if (!empty($thislog) && $thislog['0']['task_process'] == 4) {
			$data['process'] = 4;
			Universal::Output($this->config->item('request_succ'),'','',$data);
		}
		$data['task_type'] = $this->tasks_model->getypeinfo($data['getonetask']['0']['task_type'],
						$data['task_type_id']);
		Universal::Output($this->config->item('request_succ'),'','',$data);
	}
	/**
	 * 根据任务类型和id判断获取用户的具体信息
	 * @param    int     data         任务信息
	 * @param    int     wx_id        用户id
	 * @return   int     data         任务更新后的信息
	 */
	private function judgtask($data,$wx_id){
		$data['task_type_id'] = '';
		switch ($data['getonetask']['0']['task_type']) {
			case 1:
				$data['getonetask']['0']['process_name'] = '去签到';
				$data['getonetask']['0']['url']		   = 'task/usercenter/taskcenter';
				$data['getonetask']['0']['url']		   = site_url($data['getonetask']['0']['url']);
				break;
			case 2:
				$data['getonetask']['0']['process_name'] = '卖东西！';
				$data['getonetask']['0']['url']		   = 'nonstandard/system/welcome';
				$data['getonetask']['0']['url']		   = site_url($data['getonetask']['0']['url']);
				break;
			case 3:
        		$limit = 2;
        		$data['taskIntroduction'] = $this->tasks_model->get_instruction($limit);
        		if (!$data['taskIntroduction']) {
        			return false;
        		}
				$appid = $this->config->item('APPID');
	    		$center_info = $this->tasks_model->getextentnum($wx_id);//获取用户的推广码
				foreach ($data['taskIntroduction'] as $k => $v) {
					$data['taskIntroduction'][$k]['share_url'] = urlencode($data['getonetask']['0']['task_share_url'])
			            .'?extendnum='.$center_info['0']['center_extend_num'].'_'.$v['id'];
					$data['taskIntroduction'][$k]['share_url'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
					    .$appid.'&redirect_uri='.$data['taskIntroduction'][$k]['share_url']
					    .'&response_type=code&scope=snsapi_base#wechat_redirect';
				}
				break;
        	case 5:
       			$this->load->model('maijinadmin/instruction_model');
				$appid = $this->config->item('APPID');
				if ($data['getonetask']['0']['extend_num']=='') {
					$center_info = $this->tasks_model->getextentnum($wx_id);//获取用户的推广码
				}else{
					$center_info['0']['center_extend_num'] = $data['getonetask']['0']['extend_num'];
				}
	    		if ($center_info) {
	    		    $data['open_share_url'] = urlencode($data['getonetask']['0']['task_share_url'])
	    		        .'?extendnum='.$center_info['0']['center_extend_num'].'_'.'10';	    		
	   			}else{
			        Universal::Output($this->config->item('request_fall'),'','您没有邀请码，请刷新界面或请和客服人员联系','');
	   			}
	   			$data['getonetask']['0']['process_name'] = '去邀请';
	   			$data['getonetask']['0']['url'] = '/index.php/task/otherget/getothersaytwo'.'?extendnum='.$center_info['0']['center_extend_num'].'_'.'10';
	   			$data['getonetask']['0']['urlT'] = '/index.php/task/otherget/getothersaytwo'.'?extendnum='.$center_info['0']['center_extend_num'].'_'.'11';
				// $data['getonetask']['0']['url']	= 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
				    // .$appid.'&redirect_uri='.$data['open_share_url'].'&response_type=code&scope=snsapi_base#wechat_redirect';
				break;
			case 7:
			    $data = $this->maintask($data,$wx_id);
				break;
			case 9:
			    $this->config->load('task',true);//配置项加载
				$C_takes = $this->config->item('task');
				if (!isset($C_takes['click_tasks'][$data['getonetask']['0']['task_id']])) {
					return false;
				}
				$data['getonetask']['0']['process_name'] = $C_takes['click_tasks'][$data['getonetask']['0']['task_id']]['icon'];
				$data['task_type_id'] = $data['getonetask']['0']['task_id'];
				$data['getonetask']['0']['icon_img'] = $C_takes['click_tasks'][$data['getonetask']['0']['task_id']]['img'];
			    break;
			case 10:
			    $data['getonetask']['0']['process_name'] = '去投票';
				$data['getonetask']['0']['url']		   = '/view/task/vote.html?id='.$data['getonetask']['0']['task_id'];
			    break;
			default:
				break;
		}
		return $data;
	}
    /**
     * 主线任务
     */
    private function maintask($data,$wx_id){
    	switch ($data['getonetask']['0']['task_id']) {
			case 6:
				$data['getonetask']['0']['process_name'] = '关注回收通';
				$data['task_type_id'] = '6';
				break;
			case 5:
				$appid = $this->config->item('APPID');
	    		$data['open_share_url'] = urlencode($data['getonetask']['0']['task_share_url']);
	    		$data['getonetask']['0']['process_name'] = '玩游戏';
				$data['getonetask']['0']['url']	= 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
				    .$appid.'&redirect_uri='.$data['open_share_url']
				    .'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
				$data['task_type_id'] = '5';
				break;
			case 7:
				$data['getonetask']['0']['process_name'] = '去攻略库';
		        $data['getonetask']['0']['url']		   = '/view/article/knowledgeIdx.html';
				$data['task_type_id'] = '7';
				break;
			case 8:
				$data['getonetask']['0']['process_name'] = '买商品';
		        $data['getonetask']['0']['url']		   = '/view/shop/list.html';
				$data['task_type_id'] = '8';
				break;
			case 9:
				$data['getonetask']['0']['process_name'] = '体验报单';
		        $data['getonetask']['0']['url']		   = '/index.php/nonstandard/system/welcome';
				$data['task_type_id'] = '9';
				break;
			case 15:
				$data['getonetask']['0']['process_name'] = '玩游戏';
		        $data['getonetask']['0']['url']		   = '/view/games/gameIndex.html';
				$data['task_type_id'] = '15';
				break;
			case 16:
				$data['getonetask']['0']['process_name'] = '去帮忙';
		        $data['getonetask']['0']['url']		   = '/view/shop/list.html';
		        $this->load->model('task/otherget_model');
		        $result = $this->otherget_model->checkshopinfo($wx_id);
		        $data['shoptake'] = $result;
				$data['task_type_id'] = '16';
				break;
			case 17://此任务未加正在进行时状态
				$data['getonetask']['0']['process_name'] = '去注册';
		        $data['getonetask']['0']['url']		   = $data['getonetask']['0']['task_url'];
				$data['task_type_id'] = '17';
				break;
			case 18://此任务未加正在进行时状态
				$data['getonetask']['0']['process_name'] = '去注册';
		        $data['getonetask']['0']['url']		   = $data['getonetask']['0']['task_url'];
				$data['task_type_id'] = '18';
				break;
			default:
				break;
		}
		return $data;
    }
	/**
	 * 用户领取任务
	 * @param    int     task_id         任务id
	 * @param    int     wx_id           用户id
	 * @param    array   thisTaskInfo	 此任务的信息
	 * @return 	 array 	 data 			 返回错误信息或任务进行的状态
	 */
	private function get_task($task_id,$wx_id,$thisTaskInfo){
		$getonetask = $thisTaskInfo;
		if (empty($getonetask)) {
			$data['error'] = 'no_this_task';//没有任务信息传输过来
			return $data;
		}
		$task_types_data = $this->config->item('task_types_data');
		$input = array(//得到要输入的基本信息
			'wx_id' => $wx_id,
			'task_id' => $task_id,
			'task_jointime' => time(),
		);
		if (!empty($getonetask[0]['task_limit_time'])) {//如果任务有时间限制，侧加入结束时间。
			$input['task_overtime'] = time()+$getonetask[0]['task_limit_time'];
		}
		//以下验证任务是否完成。
		$usertaskinfo = $this->user_model->usertaskinfo($wx_id);
		$usertasktype = array(//将用户的任务类型数量放到一个数组，重新命名用于任务得到对应的数据
			'task_sign' => $usertaskinfo[0]['this_cycle']['center_sign'],
			'task_turnover' => $usertaskinfo[0]['this_cycle']['center_turnover'],
			'task_share' => $usertaskinfo[0]['this_cycle']['center_share'],
			'task_invite_u' => $usertaskinfo[0]['this_cycle']['center_invite_u'],
			'task_invite_m'=>$usertaskinfo[0]['this_cycle']['center_invite_m']
		);
		switch ($getonetask[0]['task_type']) {
			case 10://活动任务
				$this->tasks_model->puttasklog($input);//插入信息
				break;
			case 9://活动任务
				$this->tasks_model->puttasklog($input);//插入信息
				break;
			case 7://主线任务
				switch ($getonetask[0]['task_id']) {
					case 6:
				        $this->load->model('common/wxcode_model');
				        if (!isset($_SESSION['userinfo']['user_openid'])) {
				        	exit();
					    }
				        $userwxinfo = $this->wxcode_model->userinfo($_SESSION['userinfo']['user_openid']);
				        if ($userwxinfo['subscribe']==1) {
				        	$input['task_finishtime'] = time();
					 		$input['task_process']  = 3;
					    	$this->tasks_model->puttasklog($input);//插入信息

				        	$data['to_num'] = 3;
				        	return $data;
						}else{
							$this->tasks_model->puttasklog($input);//插入信息
				        }
						break;
					case 5:
						$this->tasks_model->puttasklog($input);//插入信息
						break;
					case 7:
						$this->tasks_model->puttasklog($input);//插入信息
						break;
					case 8:
						$this->tasks_model->puttasklog($input);//插入信息
						break;
					case 9:
						$this->tasks_model->puttasklog($input);//插入信息
						break;
					case 15:
						$this->tasks_model->puttasklog($input);//插入信息
						break;
					case 18:
						$this->tasks_model->puttasklog($input);//插入信息
						break;
					default:
								
						break;
				}
				break;
			case 8://活动任务
				$this->tasks_model->puttasklog($input);//插入信息
				break;
			case 4://精华部分
				$is_finish = 1;//用于判断是否完成。1为是，0为否。
				foreach ($task_types_data as $k => $v) {
					if ($v!='jinghua' && $getonetask[0][$v]!=0) {
						if ($usertasktype[$v]<$getonetask[0][$v]) {
							$is_finish = 0;
							break;
						}
					}
				}
				if ($is_finish == 1 && $getonetask[0]['reward_num']>0) {//任务已经完成了，并且有任务奖励
					$input['task_finishtime'] = time();
			 		$input['task_process']  = 3;

					$this->tasks_model->puttasklog($input);//插入信息

					$data['to_num'] = 3;
				    return $data;
				}elseif($is_finish == 1){
					$input['task_finishtime'] = time();
					$input['task_process']  = 4;

					$this->tasks_model->puttasklog($input);//插入信息
					$data['result'] = 'task_finish';
					exit('此任务没奖励');
				}else{
				 	$this->tasks_model->puttasklog($input);//插入信息
				}
				break;
			default://剩余的5项日常任务
				$taskType=$task_types_data[$getonetask[0]['task_type']];
						
				switch ($getonetask[0]['task_type']) {
					case 1:
						$input['task_cycle']  = $usertaskinfo[0]['center_sign_num'];
						break;
					case 2:
						$input['task_cycle']  = $usertaskinfo[0]['center_turnover_num'];
						break;
					case 3:
						$input['task_cycle']  = $usertaskinfo[0]['center_share_num'];
						break;
					case 5:
						$input['task_cycle']  = $usertaskinfo[0]['center_invite_u_num'];
						break;
					case 6:
						$input['task_cycle']  = $usertaskinfo[0]['center_invite_m_num'];
						break;
					default:
						return false;
						break;
				}//此类型用户的循环次数
				if ($usertasktype[$taskType]>=$getonetask[0][$taskType]) {//任务已经完成了
					if ($getonetask[0]['reward_num']>0) {//判断任务是否是否给奖励
                        if ($getonetask[0]['task_type']==5) {
                            $result = $this->tasks_model->checktask($wx_id,$task_id);
                            if ($result['now']>=$result['need']) {
						        $input['task_finishtime'] = time();
				 		        $input['task_process']  = 3;
                            }
                        }else{
						    $input['task_finishtime'] = time();
				 		    $input['task_process']  = 3;
                        }
				 		$this->tasks_model->puttasklog($input);//插入信息
						$data['to_num'] = 3;
					    return $data;
					}else{
						$input['task_finishtime'] = time();
						$input['task_process']  = 4;
						$this->tasks_model->puttasklog($input);//插入信息
						$this->load->model('task/taskfinish_model');
						$str = $this->taskfinish_model->is_finish_this_cycle($wx_id,$getonetask[0]['task_type']);
						if (!$str) {
							return false;
						}elseif($str == 1){
							$data['result'] = 'cycle_finish';
						}else{
							$data['result'] = 'task_finish';
						}
						exit('此任务没奖励');
					}					 	
				}else{
					$this->tasks_model->puttasklog($input);//插入信息
				}
				break;
		}
		$data['to_num'] = 2;
		return $data;

	}
	/**
	 * 用户检查任务是否完成
	 * @param    int     task_id         任务id
	 * @param    int     wx_id           用户id
	 * @param    array   thisTaskInfo	 此任务的信息
	 * @return 	 array 	 data 			 返回错误信息或任务进行的状态
	 */
	private function check_task($task_id,$wx_id,$thisTaskInfo){
		$data['getonetask'] = $thisTaskInfo;
		$this->load->language('task','chinese');
		$usertaskinfo = $this->user_model->usertaskinfo($wx_id);
		$usertasktype = array(//将用户的任务类型数量放到一个数组，重新命名用于任务得到对应的数据
			'task_sign' => $usertaskinfo['0']['this_cycle']['center_sign'],
			'task_turnover' => $usertaskinfo['0']['this_cycle']['center_turnover'],
			'task_share' => $usertaskinfo['0']['this_cycle']['center_share'],
			'task_invite_u' => $usertaskinfo['0']['this_cycle']['center_invite_u'],
			'task_invite_m'=>$usertaskinfo['0']['this_cycle']['center_invite_m']
		);
		if ($data['getonetask']['0']['task_type'] == 7 && $data['getonetask']['0']['task_id'] == 6) {
			$this->load->model('common/wxcode_model');
			if (!isset($_SESSION['userinfo']['user_openid'])) {
	        	exit();
		    }
			$userwxinfo = $this->wxcode_model->userinfo($_SESSION['userinfo']['user_openid']);
			if ($userwxinfo['subscribe']==1) {
			    $update['task_process']  = 3;
				$this->load->model('task/taskfinish_model');
				$str=$this->taskfinish_model->uptaskprocess($wx_id,$task_id,$update['task_process']);
				if (!$str) {
        			return false;
       			}
       			$res['to_num'] = 3;
				return $res;
			}
		}elseif ($data['getonetask']['0']['task_type']==1 || $data['getonetask']['0']['task_type']==2 
			    || $data['getonetask']['0']['task_type']==3 || $data['getonetask']['0']['task_type']==5 
			    || $data['getonetask']['0']['task_type']==6) {
			$task_types_data = $this->config->item('task_types_data');
			$taskType=$task_types_data[$data['getonetask']['0']['task_type']];
			if ($usertasktype[$taskType]>=$data['getonetask']['0'][$taskType] && $data['getonetask']['0']['reward_num']>0) {//判断是否任务完成，判断任务是否是否给奖励	
			    if ($data['getonetask']['0']['task_type']==5) {
                    $result = $this->tasks_model->checktask($wx_id,$task_id);
                    if ($result['now']<$result['need']) {
                    	return false;
                    }
                }					
				$update['task_process']  = 3;
				$this->load->model('task/taskfinish_model');
				$str=$this->taskfinish_model->uptaskprocess($wx_id,$task_id,$update['task_process']);
				if (!$str) {
        			return false;
       			}
       			$res['to_num'] = 3;
       			return $res;
			}elseif($usertasktype[$taskType]>=$data['getonetask']['0'][$taskType]){
				$update['task_process']  = 4;
				$this->load->model('task/taskfinish_model');
				$str=$this->taskfinish_model->uptaskprocess($wx_id,$task_id,$update['task_process']);
				if (!$str) {
					return false;
				}
				$str = $this->taskfinish_model->is_finish_this_cycle($wx_id,$data['getonetask'][0]['task_type']);
				if (!$str) {
					return false;
				}elseif($str == 1){
					$data['result'] = 'cycle_finish';
				}else{
					$data['result'] = 'task_finish';
				}
				exit('此任务没奖励');
			}
		}elseif ($data['getonetask']['0']['task_type']==4) {
			$is_finish = 1;//用于判断是否完成。1为是，0为否。
			$task_types_data = $this->config->item('task_types_data');
			foreach ($task_types_data as $k => $v) {
				if ($v!='jinghua' && $data['getonetask']['0'][$v]!=0) {
					if ($usertasktype[$v]<$data['getonetask']['0'][$v]) {
						$is_finish = 0;
						break;
					}
				}
			}
			if ($is_finish==1 && $data['getonetask']['0']['reward_num']>0) {//任务已经完成了，并且有奖励要领取
				$update['task_process']  = 3;

				$this->load->model('task/taskfinish_model');
				$str=$this->taskfinish_model->uptaskprocess($wx_id,$task_id,$update['task_process']);
				if (!$str) {
        			return false;
       			}
       			$res['to_num'] == 3;
       			return $res;
			}elseif ($is_finish == 1) {
				$update['task_process']  = 4;
				$this->load->model('task/taskfinish_model');
				$str=$this->taskfinish_model->uptaskprocess($wx_id,$task_id,$update['task_process']);
				if (!$str) {
					return false;
				}
				$data['result'] = 'task_finish';
				exit('此任务没奖励');
			}

		}
	}
}