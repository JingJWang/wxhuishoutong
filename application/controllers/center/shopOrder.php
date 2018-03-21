<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 通花商城订单管理
 * @author   wangrenjie    2016-05-15
 */
class ShopOrder extends CI_Controller {
	/**
	 * 查询订单界面
	 * @param      int        page(get)             从第几个开始
	 * @param      int        orderStatus(post)     选择此状态的订单
	 * @return     array      返回订单信息和目录信息
	 */
	function queryorder(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
	    $orderStatus = $this->input->post('orderStatus',true);
        if (!is_numeric($orderStatus)) {
            Universal::Output($this->config->item('request_fall'),'请输入正确的数字','','');
        }
		$page = $this->input->post('page',true);
        if (!is_numeric($page)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
		$page_per = $page+10;
	    $this->load->model('center/shoporder_model');
	    $result = $this->shoporder_model->Mqueryorder($orderStatus,$page,$page_per);
	    if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'没有此类型信息','','');
	    }
        $result['name'] = $_SESSION['user']['name'];
        $result['num']['now'] = $page;
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
    /**
     * 查询订单界面（某一个）
     * @param      int        id                 具体的id
     * @param      int        orderStatus(post)     选择此状态的订单
     * @return     array      返回订单信息和目录信息
     */
    function onequery(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $lid = $this->input->post('id',true);
        if (!is_numeric($lid)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('center/shoporder_model');
        $result = $this->shoporder_model->Monequery($lid);
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'查找错误或没有添加类型信息','','');
        }
        if ($result['typeid']==4) {
            $result['company'] = 0;
            $result['number'] = $result['text'];
        }else{
            if (empty($result['text'])) {
                $arr['0'] = $arr['1'] = '';
            }else{
                $arr = explode(',',$result['text']);
            }
            $result['company'] = $arr['0'];
            $result['number'] = $arr['1'];
            unset($result['text']);
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 查询订单界面（搜索功能）
     * @param      int        id                 具体的id
     * @param      int        orderNum(post)     选择此状态的订单
     * @return     array      返回订单信息和目录信息
     */
    function numquery(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($this->input->post('orderNum',true))) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('center/shoporder_model');
        $result = $this->shoporder_model->Mseachorder();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'没有此订单','','');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
	/**
	 * 修改订单
	 */
	function editeorder(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($this->input->post('orderNum',true)) 
        	|| !is_numeric($this->input->post('orderStatus',true))
        	|| !is_numeric($this->input->post('orderId',true))
            || (empty($this->input->post('company',true))&&$this->input->post('company',true)!=0)) {
            Universal::Output($this->config->item('request_fall'),'请正确输入或把信息填写完整','','');
        }
        $express = Universal::filter($this->input->post('company',true)).','.$this->input->post('orderNum',true);
        $this->load->model('center/shoporder_model');
        $result = $this->shoporder_model->Mediteorder($express);//修改信息
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'修改失败','','');
        }
        Universal::Output($this->config->item('request_succ'),'','/view/control/tongMage.html','');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */