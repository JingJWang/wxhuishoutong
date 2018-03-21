<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");

class Turntable extends CI_Controller {

	function __construct(){
		parent::__construct();
	}
	/**
	 * 加载奖励
	 * @return 		json 	正确错误都返回json并告诉用户
	 */
	function getarticle(){
		$this->load->database();
		$this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
		$this->load->model('activity/turntable_model');
		$result = $this->turntable_model->getrew();
		if ($result === false) {
			$response = array('status'=>$this->config->item('request_fall'),
                        'msg'=>$this->turntable_model->msg,'url'=>'','data'=>'');
			echo json_encode($response);exit;
		}
	    $data = array('result' => $result);
        if(isset($this->userauth_model->url)){
        	$data['free'] = 1;
    		$response = array('status'=>$this->config->item('request_succ'),
                        'msg'=>$this->turntable_model->msg,'url'=>'','data'=>$data);
	    	echo json_encode($response);exit;
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($wx_id)) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'出错','url'=>'','data'=>''
            );
            echo json_encode($response);exit;
        }
        //判断用户是否第一次
		$cnum = $this->turntable_model->cnum($wx_id);
        if ($cnum===false) {
           	$response=array('status'=>$this->config->item('request_fall'),
               	    'msg'=>'出错','url'=>'','data'=>''
           	);
    	    echo json_encode($response);exit;
	    }
	    if ($cnum>=1) {
	    	$result = $this->turntable_model->getfree($wx_id);
            if ($result!=0) {//有免费机会
                $data['free'] = 1;
            }else{
            	$data['free'] = 0;
            }
	    }else{
	    	$data['free'] = 1;
	    }
		$response = array('status'=>$this->config->item('request_succ'),
                        'msg'=>$this->turntable_model->msg,'url'=>'','data'=>$data);
		echo json_encode($response);
	}
	/**
	 * 开启转盘
	 * @param 	int 	wx_id 	用户的id
	 * @return 	json 	返回json数据
	 */
	function bigwel(){ 
	    $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录,正在跳转到首页自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        if(empty($_SESSION['userinfo']['user_mobile'])){

            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有注册,注册后再抽奖','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($wx_id)) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'出错','url'=>'','data'=>''
            );
            echo json_encode($response);exit;
        }
		$this->load->database();
		$this->load->model('activity/turntable_model');
        //判断用户是否第一次
		$cnum = $this->turntable_model->cnum($wx_id);
        if ($cnum===false) {
           	$response=array('status'=>$this->config->item('request_fall'),
               	    'msg'=>'出错','url'=>'','data'=>''
           	);
    	    echo json_encode($response);exit;
	    }	
        $obinfo = $this->lottery();
        if ($cnum>=1) {
        	$this->turntable_model->cnum = $cnum;
        }
        $result = $this->turntable_model->upinfo($wx_id,$obinfo);
        if ($result===false) {
        	$response=array('status'=>$this->config->item('request_fall'),
               	    'msg'=>$this->turntable_model->msg,'url'=>'','data'=>''
           	);
    	    echo json_encode($response);exit;
        }
        $response=array('status'=>$this->config->item('request_succ'),
              	'msg'=>$this->turntable_model->msg,'url'=>'','data'=>$obinfo
        );
        $this->load->model('common/wxcode_model');
        $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],108);//设置微信分组 任务完成，转盘，报单组
    	echo json_encode($response);exit;
	}
	/**
	 * 设置概率问题
	 * @return 		array 		result 		返回数组
	 */
	private function lottery(){
		$rewards = $this->turntable_model->rewardinfo();
		if ($rewards=== false) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'出错','url'=>'','data'=>''
            );
            echo json_encode($response);exit;
		}
		$all_pro = 0;
		$area = array();//记录每个奖励的范围（开始值）
		foreach ($rewards as $k => $v) {
			if ($v['type']==2 && $v['number']<=0) {
				unset($rewards[$k]);//类型为2，但数量为0的去掉
				continue;
			}
			$area[$k] = $all_pro;
			$all_pro += $v['probity'];
		}
		$pro = mt_rand(1,$all_pro);
		foreach ($rewards as $k => $v) {
			if ($pro>$area[$k] && $pro<=$area[$k]+$v['probity']) {
				$result = $v;
				break;
			}
		}
		if (!isset($result)) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'出错','url'=>'','data'=>''
            );
            echo json_encode($response);exit;
		}
		return $result;
	} 
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */