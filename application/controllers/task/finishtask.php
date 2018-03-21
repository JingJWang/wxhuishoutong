<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Finishtask extends CI_Controller {

	function __construct(){
		parent::__construct();
        $this->load->database();
        $this->load->model('task/taskfinish_model');
        $this->load->model('task/user_model');
	}

	/**
	 * 分享后跳转的页面
	 */
	function finishshare(){
		if(!isset($_SESSION['userinfo']['userlogin']) || $_SESSION['userinfo']['userlogin'] != 'ok' || !isset($_SESSION['userinfo']['user_id']) || empty($_SESSION['userinfo']['user_id'])){//没有session则跳转界面
	        exit;
	    }
        $this->load->model('common/wxcode_model');
        $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],107);//设置微信分组 分享组
		$post=$this->input->post();

		$wx_id = $post['wxid'];
		$this->load->helper('safe_helper');
		$wx_id = verify_id($wx_id);
		$add_share = 1;//分享增加的次数

		$task_type = 3;//任务类型为3

		$userinfo = $this->user_model->usertaskinfo($wx_id);//获取用户信息
		if (empty($userinfo)) {
			return false;
		}

		$share_limit_time = $this->config->item('share_limit_time');//得到可以分享的时间
		$share_time = $userinfo[0]['center_laster_share'] + $share_limit_time*3600;
		if ($share_time<time()) {
			
		$new_center_share = $userinfo[0]['center_share'] + $add_share;//得到用户新的分享量
		$new_cycle_share = $userinfo[0]['this_cycle']['center_share'] + $add_share;//循环中的分享量(新)
		$str = $this->taskfinish_model->updatauserinfo($wx_id,$task_type,$new_center_share);
		if (!$str) {
			return false;
		}
		$str = $this->taskfinish_model->upadtacycleinfo($wx_id,$task_type,$userinfo[0],$new_cycle_share);//(新)

		$gettasks = $this->taskfinish_model->getonetypetask($wx_id,$task_type);//分享后看是否有任务完成
		if (!empty($gettasks)) {
			foreach ($gettasks as $k => $v) {
				if (!empty($v['task_overtime']) && $v['task_overtime']<time()) {//判断任务是否过期，过期的话更新数据
					$str=$this->taskfinish_model->uptaskprocess($v['wx_id'],$v['task_id'],5);
					if (!$str) {
        				return false;
       				 }
       				 break;
				}else{
					if ($new_cycle_share>=$v['task_share'] && $userinfo[0]['this_cycle']['center_sign']>=$v['task_sign'] && $userinfo[0]['this_cycle']['center_turnover']>=$v['task_turnover'] && $userinfo[0]['this_cycle']['center_invite_u']>=$v['task_invite_u'] && $userinfo[0]['this_cycle']['center_invite_m']>=$v['task_invite_m']) {//判断是否任务已经完成(新)

						if ($v['reward_num']>0) {//判断任务是否是否给奖励
							$process_level = 3;
						}else{
							$process_level = 4;
						}
						$str=$this->taskfinish_model->uptaskprocess($v['wx_id'],$v['task_id'],$process_level);
						if (!$str) {
        					return false;
       					 }
       					 $task = 'have_finish';//(新)
       					 // echo '你有任务已经完成';
					}
				}
			}
		}
		if (isset($task) && $task == 'have_finish') {//(新)
			if ($process_level == 4) {
				$str = $this->taskfinish_model->is_finish_this_cycle($wx_id,$task_type);
				if (!$str) {
					return false;
				}
			}else{
				// echo '你有任务已经完成';
				echo 1;
			}
		}else{
			// echo "分享成功";//前段界面未加
			echo 0;
		}
		}else{
			echo 2; //分享时间未到
		}
		
	}
	/**
	 * 签到界面
	 * @param 	int 	wx_id 	用户的id
	 */
	function usersign(){
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        $this->load->helper('url');
        if(empty($_SESSION['userinfo']['user_mobile'])){
        	$response =  array('status'=>$this->config->item('request_fall'),'msg'=>'您未注册。无法签到',
        					'url'=>'/index.php/task/usercenter/isreg','data'=>'');
        	echo json_encode($response);
            exit();
        }
        //判断wx_id
		$wx_id=$this->input->post('wx_id',true);
		if (!is_numeric($wx_id) || $wx_id!=$_SESSION['userinfo']['user_id']) {
			$response =  array('status'=>$this->config->item('request_fall'),'msg'=>'出错',
        					'url'=>'','data'=>'');
        	echo json_encode($response);
            exit();
		}
		$userinfo = $this->user_model->usertaskinfo($wx_id);//获取用户信息
		// 检查用户今天是否签到
		if ($userinfo['0']['center_laster_sign']>=strtotime(date('Y-m-d'))) {
			$response =  array('status'=>$this->config->item('request_fall'),
				'msg'=>'已签到，请明天继续！','url'=>'',
				'data'=>array(
				    'conday'=>$userinfo['0']['center_conum'],
				    'img'=>$_SESSION['userinfo']['user_img']
				));
        	echo json_encode($response);
            exit();
		}
		$update = $userhave = $rewards = array();//定义初始值
		$result = $this->signrwd($userinfo['0']['center_sign'],$userinfo['0']['center_laster_sign'],$userinfo['0']['center_conum']);//取到奖励，以及更新连续次数
       /*  if($userinfo['0']['center_sign']<=0){
             $rewards=100;
             $balance=1;
        }else{
            $rewards = $result['rewards'];
            $balance=0;
        } */
		$rewards = $result['rewards'];
		$balance=0;
		$update['conum'] = $result['conum'];
        $this->load->model('task/reward_model');
		if ($rewards['all_integral']>0) {//检查是否可以升级
			$now_integral = $userinfo['0']['center_all_integral']+$rewards['all_integral'];
        	$result = $this->reward_model->checklevel($now_integral,$userinfo['0']['level_num']);
        	if ($result===false) {
        		$response =  array('status'=>$this->config->item('request_fall'),'msg'=>$this->reward_model->msg,
        					'url'=>'','data'=>'');
        		echo json_encode($response);
            	exit();
        	}
        	if ($result>0) {
        		$update['newlevel'] = $result;
        	}
		}
		$update['newnum'] = $userinfo['0']['center_sign'] + 1;//计算得到用户新的回收值
		$userhave = array(//用户拥有的奖励
			'center_fund'=>$userinfo['0']['center_fund'],
			'center_bonus'=>$userinfo['0']['center_bonus'],
			'center_integral'=>$userinfo['0']['center_integral'],
			'center_all_integral'=>$userinfo['0']['center_all_integral']
		);
        $this->db->trans_begin();//事务开启
        $this->reward_model->task_num = $update;
        $this->reward_model->reward = $rewards;
        $this->reward_model->balance =$balance;
        $this->reward_model->user_have = $userhave;
        $result = $this->reward_model->getreward($wx_id,1);
        $userinfo['0']['center_sign']+=1;
        $userinfo['0']['center_laster_sign']=time();
        $return = $this->reward_model->Chinviter($wx_id,$userinfo['0']);//查看是否第一个任务。  
        if ($rewards['all_integral']>0) {
        	$this->reward_model->thlog($wx_id,$rewards['all_integral'],'做签到任务');	
        }
        if ($this->db->trans_status() === false || $result===false || $return===false){
            $this->db->trans_rollback();
            $response =  array('status'=>$this->config->item('request_fall'),'msg'=>$this->reward_model->msg,
        					'url'=>'','data'=>'');
        	echo json_encode($response);
            exit();
        }
		$this->db->trans_commit();
        $this->load->model('common/wxcode_model');
        $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],108);//设置微信分组 任务完成，转盘，报单组
    	$response =  array('status'=>$this->config->item('request_succ'),'msg'=>'签到成功',
        					'url'=>'','data'=>array(
        					    'rewards'=>$rewards,
        					    'conday'=>$update['conum'],
        					    'img'=>$_SESSION['userinfo']['user_img'],
        					    'sign'=>$userinfo['0']['center_sign'],
        					    'lxsign'=>$userinfo['0']['center_conum']
        					));
        echo json_encode($response);
        exit();
	}
	/**
	 * 根据签到时间和连续次数，确定奖励
	 * @param 	int 	signum 		已经签到的次数
	 * @param 	int 	lastsign 	最后签到时间
	 * @param 	int 	conum 		已经连续签到的次数
	 * @return 	bool 	false		错误返回
	 * @return 	array 	return 		正确返回奖励和新的连续签到次数	
	 */
	private function signrwd($signum,$lastsign,$conum){
		$this->config->load('task',true);//配置项加载
		$allreward = $this->config->item('task');
		if ($signum == 0 && $lastsign <= 0 && $conum == 0) {//查看是否第一次签到
			$rewards = $allreward['sign_reward']['first'];
			$return['conum'] = 1;
		}elseif($lastsign >= strtotime(date('Y-m-d',strtotime('-1 day')))){//是否连续签到
			if ($conum == 0) {
				$rewards = $allreward['sign_reward']['cononeday'];
				$conum = 1;
			}elseif ($conum == 1) {//连续2天
				$rewards = $allreward['sign_reward']['cononeday'];
			}elseif($conum >= 2){//连续3天及3天以上签到
				$rewards = $allreward['sign_reward']['contwoday'];
			}
			$return['conum'] = $conum+1;
		}else{
			$rewards = $allreward['sign_reward']['nocon'];//段片了
			$return['conum'] = 1;
		}
		$return['rewards'] = $rewards;
		return $return;
	}
    /**
     * 获取提成
     * @param        int        wx_id        用户id
     * @return       json       返回json字符串
     */
    function getdivide(){
		$this->load->model('auto/userauth_model');
        //校验是否已经登录
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
			Universal::Output($this->config->item('request_fall'),'','','');
        }
        if (!is_numeric($wx_id = $_SESSION['userinfo']['user_id'])) {
			Universal::Output($this->config->item('request_fall'),'','','');
        }
    	$this->load->model('task/tasks_model');
    	$this->load->model('task/otherget_model');
        $result = $this->otherget_model->getshopinfo($wx_id);//获取订单信息
        if ($result===false) {
			Universal::Output($this->config->item('request_fall'),'您没有可以获取的提成！','','');
        }
        $data['cantake'] = 0;
        $time = time();
        $ids = '';
        $num = 0;
        foreach ($result as $k => $v) {
            if ($v['record_userid']==$wx_id) {
                return false;
            }
            if (($v['record_time']+604800)<$time) {
            	$ids .= $v['record_id'].',';
                $data['cantake'] += intval($v['record_divide']*$v['record_price']);//能提现的钱数
                $num++;
            }
        }
        $ids=trim($ids,',');
        if ($ids=='') {
			Universal::Output($this->config->item('request_fall'),'您没有可以获取的提成！','','');
        }
        $result = $this->otherget_model->getdivide($ids,$wx_id,$data['cantake'],$num);
        if ($result==false) {
			Universal::Output($this->config->item('request_fall'),'提取失败','','');
        }
		Universal::Output($this->config->item('request_succ'),'提取成功','',$data);
    }
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}