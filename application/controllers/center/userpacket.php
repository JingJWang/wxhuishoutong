<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 用户分组
 * @author wang    2016-05-25
 *
 */
class Userpacket extends CI_Controller {
	/**
	 * 移动到订单成交过的分组
	 * @param      int        mobile        用户电话号
	 * @return     json      返回json字符串
	 */
	function moveprice(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
		if (!preg_match("/^1[34578]\d{9}$/",$this->input->post('mobile',true))
			||!is_numeric($this->input->post('type',true))) {
            Universal::Output($this->config->item('request_fall'),'请输入正确的手机号码','','');
		}
        $this->load->model('center/userpacket_model');
        $userinfo = $this->userpacket_model->getuser();
        if ($userinfo === false) {
            Universal::Output($this->config->item('request_fall'),'未查到此用户','','');
        }
        if ($userinfo['0']['status']==-1) {
            Universal::Output($this->config->item('request_fall'),'此用户未关注公众号','','');
        }
        switch ($this->input->post('type',true)) {
        	case '6'://交易
                $result = $this->userpacket_model->checktransa($userinfo['0']['id']);
                if ($result===false) {
                    Universal::Output($this->config->item('request_fall'),'此用户未交易成功过','','');
                }
                $this->load->model('common/wxcode_model');
                $result = $this->wxcode_model->setPacket($userinfo['0']['openid'],105);//设置微信分组 注册组
        		break;
        	case '5'://通花商城
                $result = $this->userpacket_model->checkshop($userinfo['0']['id']);
                if ($result===false) {
                    Universal::Output($this->config->item('request_fall'),'此用户在通花商城未交易过','','');
                }
                $this->load->model('common/wxcode_model');
                $result = $this->wxcode_model->setPacket($userinfo['0']['openid'],106);//设置微信分组 注册组
        		break;
            case '4':
                $result = $this->userpacket_model->checkshare($userinfo['0']['id']);
                if ($result===false) {
                    Universal::Output($this->config->item('request_fall'),'此用户任务，转盘，报单都未行动过','','');
                }
                $this->load->model('common/wxcode_model');
                $result = $this->wxcode_model->setPacket($userinfo['0']['openid'],107);//设置微信分组 注册组
        		break;
            case '3'://任务，转盘，报过单
                $result = $this->userpacket_model->checkconduct($userinfo['0']['id']);
                if ($result===false) {
                    Universal::Output($this->config->item('request_fall'),'此用户任务，转盘，报单都未行动过','','');
                }
                $this->load->model('common/wxcode_model');
                $result = $this->wxcode_model->setPacket($userinfo['0']['openid'],108);//设置微信分组 注册组
        		break;
            case '2':
                $this->load->model('common/wxcode_model');
                $result = $this->wxcode_model->setPacket($userinfo['0']['openid'],109);//设置微信分组 注册组
                break;
        	default:
        		break;
        }
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'移组失败','/view/control/login.html','');
        }
        Universal::Output($this->config->item('request_succ'),'成功','','');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */