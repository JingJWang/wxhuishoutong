<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics extends CI_Controller {
	/**
	 * 今日界面的返回信息
	 * @param     day    star_d     开始时间
	 * @param     day    end_d     结束时间
	 */
	function backinfo(){
		if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'','/view/control/login.html','');
		}
        $this->star_d = date('Y-m-d');
        $this->end_d = date('Y-m-d',strtotime("+1 day"));//第二天的时间
		$result = $this->daycount();    
        $result['name'] = $_SESSION['user']['name'];
        $result['day'] = $this->star_d;
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
	/**
	 * 根据日期来给出相应的值
	 */
	function dateback(){
		if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
			Universal::Output($this->config->item('request_fall'),'','/view/control/login.html','');
		}
		switch ($this->input->post('date',true)) {
			case 'yes':
                $this->star_d = date('Y-m-d',strtotime("-1 day"));//第二天的时间
                $this->end_d = date('Y-m-d',strtotime("-1 day"));//第二天的时间
				break;
			case 'yyes':
                $this->star_d = date('Y-m-d',strtotime("-2 day"));//第二天的时间
                $this->end_d = date('Y-m-d',strtotime("-2 day"));//第二天的时间
				break;
			case 'week':
                $this->star_d = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'))); // 第一天
                $this->end_d = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+(6-date('w')),date('Y'))); // 最后一天
                $this->star_d = date('Y-m-d',strtotime($this->star_d)+86400);
                $this->end_d = date('Y-m-d',strtotime($this->end_d)+86400);
				break;
			case 'month':
                $this->star_d = date('Y-m-01', strtotime(date('Y-m-d')));//第二天的时间
                $this->end_d = date('Y-m-d', strtotime($this->star_d.' +1 month -1 day'));//第二天的时间
                // $this->end_d = date('Y-m-d',strtotime($this->end_d)+86400);
				break;
			default:
			    $patten = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
                if (preg_match ($patten, $this->input->post('star',true)) && preg_match ($patten, $this->input->post('end',true))) {
                	$this->star_d = $this->input->post('star',true);
                	$this->end_d = $this->input->post('end',true);
                }else{
                	Universal::Output($this->config->item('request_fall'),'请输入开始与结束日期','','');
                }
				break;
		}
		$result = $this->daycount();
	    $this->load->model('center/statistics_model');
	    $this->statistics_model->daycount($this->star_d,$this->end_d);
        $result['name'] = $_SESSION['user']['name'];
        $result['day'] = $this->star_d;
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
	/**
	 * 统计界面
	 */
	private function count(){
	    $star_d = $this->star_d;//今天的时间
	    $end_d = $this->end_d;//第二天的时间
	    $this->load->model('center/statistics_model');
	    $data['login_n'] = $this->statistics_model->userinfo($star_d,$end_d);//用户统计
	    $data['tong_order'] = $this->statistics_model->tonginfo($star_d,$end_d);//通化商城统计信息

        $data['task']['invite_u'] = $this->statistics_model->task_count(5,$star_d,$end_d);//邀请任务
        $data['task']['turnover'] = $this->statistics_model->task_count(2,$star_d,$end_d);//回收任务
        $data['task']['game'] = $this->statistics_model->task_count(7,$star_d,$end_d);//主线（游戏）任务
	    $data['order_n'] = $this->statistics_model->orderinfo($star_d,$end_d);//通化商城统计信息
	    $data['every'] = $this->statistics_model->everday($star_d,$end_d);
	    return $data;
	}
	/**
	 * 统计界面
	 * @param     day    star_d     开始时间
	 * @param     day    end_d     结束时间
	 */
	private function daycount(){
		$this->load->model('center/statistics_model');
	    $result = $this->statistics_model->daycount($this->star_d,$this->end_d);
	    $data['login_n']['loginum'] = $result['other']['login'];
	    $data['login_n']['joinum'] = $result['other']['join_num'];
	    $data['tong_order'] = $result['other']['record_count'];
	    $data['task']['invite_u'] = $result['other']['invite_u'];
	    $data['task']['turnover'] = $result['other']['turnover'];
	    $data['task']['game'] = $result['other']['game'];
	    $data['order_n']['order_count'] = $result['other']['order_count'];
	    $data['order_n']['order_sum'] = $result['other']['order_sum'];
	    $data['every']['login'] = $result['other']['login'];
	    $data['every']['sign'] = $result['other']['sign'];
	    $data['every']['share'] = $result['other']['share'];
	    return $data;
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */