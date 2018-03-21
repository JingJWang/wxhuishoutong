<?php
/**
 * 系统管理 自动报价管理
 */
class quote extends CI_Controller{
    
    /**
     * 品牌列表
     * @return  json
     */
    function brandList(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $this->load->model('center/quote_model');
        $this->quote_model->typeid=5;
        $data=$this->quote_model->getBrandList();
        if($data !== false){
           Universal::Output($this->config->item('request_succ'),'','',$data);
        }else{
           Universal::Output($this->config->item('request_fall'),'没有找到分类下的品牌列表!');
        }
    }
    /**
     * 搜索品牌
     * @param   string  key  品牌关键词
     * @return  json
     */
    function searchBrand(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $key=$this->input->post('key');
        if(empty($key)){
            Universal::Output($this->config->item('request_fall'),'请输入关键词字');
        }
        $key=Universal::safe_replace($key);
        $this->load->model('center/quote_model');
        $this->quote_model->type=5;
        $this->quote_model->key=$key;
        $data=$this->quote_model->brandSearch();
        if($data !== false){
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有找到分类下的品牌列表!');
        }
    }
    /**
     * 型号列表
     * @return  json
     */
    function typeList(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        $this->load->model('center/quote_model');
        $this->quote_model->brandid=$id;
        $this->quote_model->coop=empty($_SESSION['user']['coop']) ? 1447299307 :$_SESSION['user']['coop'];
        $data=$this->quote_model->getTypeList();
        if($data !== false){
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有找到品牌下的型号列表!');
        }
    }
    /**
     * 搜素型号
     * @param  string  key  型号列表
     * @return  json
     */
    function searchType() {
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数
        $key=$this->input->post('key',true);
        if(empty($key)){
            Universal::Output($this->config->item('request_fall'),'关键词不能为空!');
        }
        $key=Universal::safe_replace($key);
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        $this->load->model('center/quote_model');
        $this->quote_model->id=$id;
        $this->quote_model->key=$key;
        $data=$this->quote_model->typeSearch();
        if($data !== false){
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有找到品牌下的型号列表!');
        }
    }
    /**
     * 获取参数信息
     */
    function optionInfo(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
         //校验传递的参数是否合法
         $id=$this->input->post('id');
         if(empty($id) || !is_numeric($id) || isset($id{9})){
             Universal::Output($this->config->item('request_fall'),'本次请求不合法');
         }
         //获取当前型号的配置
         $this->load->model('center/quote_model');         
         //获取参数列表
         $this->quote_model->id=$id;
         $this->quote_model->userid=empty($_SESSION['user']['coop']) ? 1447299307 :$_SESSION['user']['coop'];
         $attr=$this->quote_model->GetOptionInfo();
         if($attr !== false){
             foreach ($attr['attr'] as $key=>$val){
                 foreach ($val as $i=>$n){
                     $attr['attr'][$key][$i]=str_replace(array('[',']'),array('',''),$n);
                 }
             }   
             $temp=array();
             foreach ($attr['info'] as $k=>$n){
                 $temp[$n['id']]=$n['info'];
             }
             $attr['info']=$temp;
             $model=array();
             foreach ($attr['model'] as $key=>$val){
                 $model[$val['alias']]=array('name'=>$val['name'],'type'=>$val['type']);
             }
             $attr['model']=$model;
             if(!empty($attr['plan'])){
                 $attr['plan']=array('content'=>json_decode($attr['plan']['0']['content'],true),
                         'base'=>$attr['plan']['0']['base'],'garbage'=>$attr['plan']['0']['garbage']
                 );
             }else{
                 $attr['plan']='';
             }
             Universal::Output($this->config->item('request_succ'),'','',$attr);
         }else{
             Universal::Output($this->config->item('request_fall'),'没有找到该型号的参数配置信息!');
         }
    }    
    /**
     * 保存报价方案
     * 
     */
    function saveQuote(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数
        $quote=$this->input->post();       
        if(empty($quote['typeid']) || !is_numeric($quote['typeid'])){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号ID!');
        }
        $typeid=$quote['typeid'];
        unset($quote['typeid']);
        if(empty($quote['garbage']) || !is_numeric($quote['garbage'])){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号基价!');
        }
        $garbage=$quote['garbage'];
        unset($quote['garbage']);
        if(empty($quote['base']) || !is_numeric($quote['base'])){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号市价!');
        } 
        $base=$quote['base'];
        unset($quote['base']);
        foreach ($quote as $key=>$val){            
            $val=str_replace(array('-','+','%','/'),array('','','',''),$val);
            if(!is_numeric($key) || !is_numeric($val)){
                Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
            }
        }
        $this->load->model('center/quote_model');
        //获取参数列表
        $this->quote_model->id=$typeid;
        $this->quote_model->garbage=$garbage;
        $this->quote_model->base=$base;
        $this->quote_model->plan=json_encode($quote);
        $this->quote_model->coop=empty($_SESSION['user']['coop']) ? 1447299307 :$_SESSION['user']['coop'];
        $res=$this->quote_model->savePlan();
        if($res !== false){
            Universal::Output($this->config->item('request_succ'),'保存报价方案成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'保存报价方案失败!');
        }
    }
    
    /* function saveTypePri(){
        //校验参数
        $typeid=$this->input->post('typeid',true);
        if(empty($typeid) || !is_numeric($typeid)){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号ID!');
        }
        $garbage=$this->input->post('garbage',true);
        if(empty($garbage) || !is_numeric($garbage)){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号基价!');
        }
        $base=$this->input->post('base',true);
        if(empty($base) || !is_numeric($base)){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号市价!');
        }
       
    } */
    
}