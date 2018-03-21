<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
 /**
  * 后台奖金会员入口
  * @author sun
  * 
  */
class Homebonus extends CI_Controller { 
    /**
     * 奖金审核
     */
    function bonusAudit(){
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->bonustatus=$data['bonustatus'];
    	$this->homebonus_model->page=$data['page'];
    	$this->homebonus_model->num=10;
    	$response=$this->homebonus_model->bonusAudit();
    	$response['num']['now'] = $data['page'];
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取需要修改的单条奖金数据
     */
    function bonusAuditEdit(){
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$id=$this->input->post('id',true);
    	if(empty($id) || !is_numeric($id) || !isset($id)){
             Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
         }
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$id;
    	$response=$this->homebonus_model->bonusAuditEdit();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存修改的单条奖金数据
     */
    function bonusAuditSave(){
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$data=$this->input->post();
    	if(empty($data['bonustatus']) || !is_numeric($data['bonustatus']) || !isset($data['bonustatus'])){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	if(empty($data['id']) || !is_numeric($data['id']) || !isset($data['id'])){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	if(empty($data['wxuid']) || !isset($data['wxuid'])){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->wxuid=$data['wxuid'];
    	$this->homebonus_model->bonustatus=$data['bonustatus'];
    	$response=$this->homebonus_model->bonusAuditSave();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取奖励增加模块信息列表
     */
    function bonusIncreaseList(){
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->page=$data['page'];
    	$this->homebonus_model->summoney=$data['summoney'];
    	$this->homebonus_model->status=$data['status'];
    	$this->homebonus_model->start=$data['start'];
    	$this->homebonus_model->end=$data['end'];
    	$response=$this->homebonus_model->bonusIncreaseList();
    	$response['num']['now'] = $data['page'];
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取排行榜审核列表
     */
    function rankingAuditList(){
    	//$status=$this->input->post('status',true);
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	//$this->homebonus_model->status=$status;
    	$response=$this->homebonus_model->rankingAuditList();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取奖金比例审核商城列表
     */
    function bonusSetShop(){
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$name = $this->input->post('name',true);
    	$page = $this->input->post('page',true);
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->name=$name;
    	$this->homebonus_model->page=$page;
    	$response=$this->homebonus_model->bonusSetShop();
    	$response['num']['now'] = $page;
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取奖金比例设置商城单条数据
     */
    function updateShopBonus(){
    	$id = $this->input->post('id',true);
    	if(empty($id) || !is_numeric($id) || !isset($id)){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$id;
    	$response=$this->homebonus_model->selectShopBouns();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存奖金比例设置商城单条数据
     */
    function updateShopSave(){
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->goodid=$data['goodid'];
    	$this->homebonus_model->name=$data['name'];
    	$this->homebonus_model->type=$data['radiobutton'];
    	if($data['radiobutton']==1){
    		$this->homebonus_model->value=$data['value1'];
    	}else if($data['radiobutton']==2){
    		$this->homebonus_model->value=$data['value'];
    	}
    	$response=$this->homebonus_model->updateShopSave();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取奖金比例审核回收列表
     */
    function bonusSetOrder(){
    	//判断用户是否已经登录
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->goodid=$data['goodid'];
    	$this->homebonus_model->page=$data['page'];
    	$this->homebonus_model->start=$data['start'];
    	$this->homebonus_model->end=$data['end'];
    	$response=$this->homebonus_model->bonusSetOrder();
    	$response['num']['now'] =$data['page'];
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取商城产品列表
     */
    function bonusSetShopList(){
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$response=$this->homebonus_model->shoplist();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存商城奖金比例设置
     */
    function bonusSetSaveShop(){
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	if(!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])){
    	    Universal::Output($this->config->item('request_fall'),'数据异常');
    	}
    	if(!isset($data['name']) || empty($data['name'])){
    	    Universal::Output($this->config->item('request_fall'),'数据异常');
    	}
    	if(!isset($data['addvalue']) || empty($data['addvalue'])){
    	    Universal::Output($this->config->item('request_fall'),'数据异常');
    	}
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->name=$data['name'];
    	$this->homebonus_model->ordervalue=$data['addvalue'];
    	$this->homebonus_model->type=$data['type'];
    	$response=$this->homebonus_model->saveShopBonus();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'该商品设置已经存在');
    	}
    }
    /**
     * 获取回收产品列表
     */
    function bonusSetOrderList(){
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$response=$this->homebonus_model->orderlist();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存回收奖金比例设置
     */
    function bonusSetSaveOrder(){
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->goodname=$data['name'];
    	$this->homebonus_model->type=$data['type'];
    	$start = explode('-',$data['qujian']);
    	if(isset($data['ordervalue']) && ($data['ordervalue']!=null || !empty($data['ordervalue']))){
    		$this->homebonus_model->ordervalue=$data['ordervalue'];
    	}else{
    		$this->homebonus_model->ordervalue='';
    	}
    	if(isset($start['0']) && ($start['0']!=null || !empty($start['0']))){
    		$this->homebonus_model->start=$start['0'];
    	}else{
    		$this->homebonus_model->start='';
    	}
    	if(isset($start['1']) && ($start['1']!=null || !empty($start['1']))){
    		$this->homebonus_model->end=$start['1'];
    	}else{
    		$this->homebonus_model->end='';
    	}
    	$response=$this->homebonus_model->saveOrderBonus();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'该数据已经存在');
    	}
    }
    /**
     * 获取奖金比例设置商城单条数据
     */
    function updateOrderBonus(){
    	$goodid= $this->input->post('id',true);
    	if(empty($goodid) || !is_numeric($goodid) || !isset($goodid)){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->goodid=$goodid;
    	$response=$this->homebonus_model->selectOrderBouns();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存奖金比例设置商城单条数据
     */
    function updateOrderSave(){
    	$data=$this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	if(!isset($data['goodid']) || empty($data['goodid']) || !is_numeric($data['goodid'])){
    	    Universal::Output($this->config->item('request_fall'),'数据异常');
    	}
    	$this->homebonus_model->goodid=$data['goodid'];
    	$this->homebonus_model->start=$data['start'];
    	$this->homebonus_model->end=$data['end'];
    	$this->homebonus_model->radiobutton=$data['radiobutton'];
    	if($data['radiobutton']==2){
    		$this->homebonus_model->value=$data['value'];
    	}else if($data['radiobutton']==1){
    		$this->homebonus_model->value=$data['value1'];
    	}
    	$response=$this->homebonus_model->updateOrderSave();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 随机发放奖励
     */
    function addBonusRand(){
    	$data = $this->input->post();
    	$this->load->model('nonstandard/homebonus_model');
    	if(!isset($data['id']) || empty($data['id']) || !is_numeric($data['id'])){
    	    Universal::Output($this->config->item('request_fall'),'数据异常');
    	}
    	$this->homebonus_model->start=$data['start'];
    	$this->homebonus_model->end=$data['end'];
    	$this->homebonus_model->startM=$data['startM'];
    	$this->homebonus_model->endM=$data['endM'];
    	$this->homebonus_model->summoney=$data['summoney'];
    	$this->homebonus_model->id=$data['id'];
    	$this->homebonus_model->status=$data['status'];
    	$response=$this->homebonus_model->addBonusRand();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'参数错误');
    	}
    }
    /**
     * 批量结算奖金审核
     */
    function bonusAuditUpdate(){
    	$data=$this->input->post();
    	if(empty($data['bonustatus']) || !is_numeric($data['bonustatus']) || !isset($data['bonustatus'])){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->bonustatus=$data['bonustatus'];
    	$response=$this->homebonus_model->bonusAuditUpdate();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'参数错误');
    	}
    }
    /**
     * 排行榜审核 发放奖励
     */
    function saveRandingaudit(){
    	$data=$this->input->post();
    	if(empty($data['phone']) || !is_numeric($data['phone']) || !isset($data['phone'])){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	if(empty($data['value']) || !is_numeric($data['value']) || !isset($data['value'])){
    		Universal::Output($this->config->item('request_fall'),'参数类型不正确!');
    	}
    	$this->load->model('nonstandard/homebonus_model');
    	$this->homebonus_model->phone=$data['phone'];
    	$this->homebonus_model->value=$data['value'];
    	$response=$this->homebonus_model->saveRandingaudit();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'参数错误');
    	}
    }
}