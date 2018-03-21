<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 奢侈品订单管理
 * @author   wangrenjie    2016-04-19
 */
class Luxurys extends CI_Controller {
	/**
	 * 加入订单
	 */
	function addorder(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($this->input->post('articleId',true)) 
        	|| !is_numeric($this->input->post('shopId',true)) 
        	|| !ctype_alnum($this->input->post('orderNum',true)) ) {
            Universal::Output($this->config->item('request_fall'),'请正确输入','','');
        }
        $this->load->model('center/luxurys_model');
        $result = $this->luxurys_model->Maddorder();//添加信息
        if ($result === false) {
            Universal::Output($this->config->item('request_fall'),'添加失败，请重新添加','','');
        }
        Universal::Output($this->config->item('request_succ'),'添加成功','/view/control/luxuryMage.html','');
	}
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
	    if (is_numeric($orderStatus) && $orderStatus!=-1) {
            $where = 'luxury_status='.$orderStatus;
            $where_ = 'a.luxury_status='.$orderStatus;
	    }else{
	    	$where = '(luxury_status=1 or luxury_status=2 or luxury_status=4)';
            $where_ = '(a.luxury_status=1 or a.luxury_status=2 or a.luxury_status=4)';
	    }
        $this->load->helper('url');
		$page = $this->input->post('page',true);
        if (!is_numeric($page)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
		$page_per = $page+10;
	    $this->load->model('center/luxurys_model');
	    $result = $this->luxurys_model->Mqueryorder($where,$where_,$page,$page_per);
	    if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'没有此类型信息','','');
	    }
        $result['name'] = $_SESSION['user']['name'];
        $result['num']['now'] = $page;
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
    /**
     * 查询订单界面
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
        $where = 'a.luxury_id='.$lid;
        $this->load->model('center/luxurys_model');
        $result['order'] = $this->luxurys_model->Monequery($where);
        if ($result['order']===false) {
            Universal::Output($this->config->item('request_fall'),'查找错误或没有添加类型信息','','');
        }
        $result['type'] = $this->luxurys_model->Mgetyeinfo();//获取类型
        $result['branch'] = $this->luxurys_model->Mgetbranch();//门店信息
        if ($result['order']['list']['0']['pid']=='') {
            $result['order']['list']['0']['pid']=0;
        }
        $result['brand'] = $this->luxurys_model->Mgetbrands($result['order']['list']['0']['pid']);//品牌信息
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 查询订单界面
     * @param      int        id                 具体的id
     * @param      int        orderNum(post)     选择此状态的订单
     * @return     array      返回订单信息和目录信息
     */
    function numquery(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!ctype_alnum($this->input->post('orderNum',true))) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $where_ = 'a.luxury_number="'.$this->input->post('orderNum',true).'"';
        $where = 'luxury_number="'.$this->input->post('orderNum',true).'"';
        $this->load->model('center/luxurys_model');
        $result = $this->luxurys_model->Mqueryorder($where,$where_,$page=0,$page_per=10);
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
        if (!is_numeric($this->input->post('articleId',true)) 
        	|| !is_numeric($this->input->post('shopId',true)) 
        	|| !ctype_alnum($this->input->post('orderNum',true)) 
        	|| !is_numeric($this->input->post('orderStatus',true))
        	|| !is_numeric($this->input->post('orderId',true))) {
            Universal::Output($this->config->item('request_fall'),'请正确输入','','');
        }
        $this->load->model('center/luxurys_model');
        $result = $this->luxurys_model->Mediteorder();//修改信息
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'修改失败','','');
        }
        Universal::Output($this->config->item('request_succ'),'','/view/control/luxuryMage.html','');
	}
	/**
	 * 查找物品类型和门店信息  此处找类型为三的类型
	 */
	function getyeinfo(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $this->load->model('center/luxurys_model');
        $result['type'] = $this->luxurys_model->Mgetyeinfo();
        $result['branch'] = $this->luxurys_model->Mgetbranch();//门店信息
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
	/**
	 * 查找品牌
	 */
	function getbrands(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($this->input->post('brandId',true))){
            Universal::Output($this->config->item('request_fall'),'请正确输入','','');
        }
        $this->load->model('center/luxurys_model');
        $result = $this->luxurys_model->Mgetbrands($this->input->post('brandId',true));
        Universal::Output($this->config->item('request_succ'),'','',$result);
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */