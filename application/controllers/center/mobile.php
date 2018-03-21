<?php
/**
 * 后台管理
 * 手机品牌 型号管理
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class  mobile extends  CI_Controller{
    
    /**
     * 获取手机品牌列表
     * @return json 成功返回 品牌列表
     */
    function brandLsit() {
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $page=$this->input->post('page',true);
        if(!is_numeric($page)){
                Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->page=$page;
        $this->mobile_model->num=7;
        $response=$this->mobile_model->getBrandList();
        if( $response === false ){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌列表!');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }
    }
    /**
     * 品牌管理 修改品牌名称
     * @param   int    id     品牌id
     * @param   string name   品牌名称
     * @return  json 成功返回结果 | 失败返回原因  
     */
    function editBrand(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $name=$this->input->post('name',true);
        if(empty($name)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌名称');
        }else{
            $name=str_replace(array(' ',''),array('[A]','[B]'),$name);
            $name=Universal::safe_replace($name);
            $name=str_replace(array('[A]','[B]'),array(' ',''),$name);
        }
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->id=$id;
        $this->mobile_model->name=$name;
        $response=$this->mobile_model->editBrand();
        if( $response ){
            Universal::Output($this->config->item('request_succ'),'修改品牌名称成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'修改品牌名称失败!');
        }
    }
    /**
     * 品牌管理   删除品牌
     * @param  int  id  品牌id
     * @return  json 删除成功返回提示原因 |删除失败返回 原因 
     */
    function delbrand(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->id=$id;
        $response=$this->mobile_model->delbrand();
        if( $response ){
            Universal::Output($this->config->item('request_succ'),'删除品牌名称成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'删除品牌名称失败!');
        }
    }
    /**
     * 品牌管理 批量删除
     * @param  int  id  品牌id
     * @return  json  删除成功 返回提示信息 | 删除失败返回原因  
     */
    function delMultiBrand(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);
        $check=Universal::safe_replace($id);
        if(empty($check) || !is_numeric($check)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        if(strpos($id,',')){
            $id=trim($id,',');
        }else{
            Universal::Output($this->config->item('request_fall'),'品牌编号不符合规范');
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->id=$id;
        $response=$this->mobile_model->delMultiBrand();
        if( $response ){
            Universal::Output($this->config->item('request_succ'),'删除品牌名称成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'删除品牌名称失败!');
        }
    }
    /**
     * 品牌管理 添加品牌
     * @param   string  品牌名称
     * @return  json 添加成功返回提示|添加失败返回原因
     */
    function addBrnad(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $name=$this->input->post('name',true);
        if(empty($name)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌名称');
        }else{
            $name=str_replace(array(' ','',),array('[A]','[B]'),$name);
            $name=Universal::safe_replace($name);
            $name=str_replace(array('[A]','[B]'),array(' ',''),$name);
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->name=$name;
        $response=$this->mobile_model->addBrnad();
        if( $response ){
            Universal::Output($this->config->item('request_succ'),'添加品牌名称成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'添加品牌名称失败!');
        }
    }
    /**
     * 型号管理 获取品牌列表
     * @return  json  型号列表
     */
    function typeBrandlist(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $this->load->model('center/mobile_model');
        $response=$this->mobile_model->typeBrandlist();
        if( $response ){
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }else{
            Universal::Output($this->config->item('request_fall'),'获取品牌列表失败');
        }
    }
    /**
     * 型号管理 获取型号列表
     * @param   int  id  品牌id
     * @return  json 成功返回品牌列表 | 失败返回原因
     */
    function typeList(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        $page=$this->input->post('page',true);
        if(empty($id) || !is_numeric($page) ){
            Universal::Output($this->config->item('request_fall'),'没有获取到当前页码');
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->id=$id;
        $this->mobile_model->page=$page;
        $this->mobile_model->num=7;
        $response=$this->mobile_model->typeList();
        if( $response !== false){
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }else{
            Universal::Output($this->config->item('request_fall'),'获取型号列表失败');
        }
    }
    /**
     * 型号管理  修改型号名称
     * @param  int   id      型号id
     * @param  int   brand   品牌id
     * @param  string  name   型号名称
     * @return  json 返回修改结果
     */
    function editTypeName(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号编号');
        }
        $brandid=$this->input->post('brand',true);
        if(empty($brandid) || !is_numeric($brandid)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        $name=$this->input->post('name',true);
        if(empty($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到修改的型号名称');
        }else{
            $name=str_replace(array(' ',''),array('[A]','[B]'),$name);
            $name=Universal::safe_replace($name);
            $name=str_replace(array('[A]','[B]','KEYA1'),array(' ','','+'),$name);
        }        
        $this->load->model('center/mobile_model');
        $this->mobile_model->id=$id;
        $this->mobile_model->brand=$brandid;
        $this->mobile_model->name=$name;
        $response=$this->mobile_model->editTypeName();
        if( $response !== false){
            Universal::Output($this->config->item('request_succ'),'修改型号名称成功');
        }else{
            Universal::Output($this->config->item('request_fall'),'修改型号名称成功');
        }
    }
    /**
     * 型号管理 搜索型号
     * @param   int      brand   品牌id
     * @param   string   name    搜索关键词
     * @return  json  返回搜素结果
     */
    function searchType(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $page=$this->input->post('page',true);
        if(empty($page) || !is_numeric($page)){
            Universal::Output($this->config->item('request_fall'),'没有获取到当前页码');
        }
        $id=$this->input->post('brand',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        $keyword=$this->input->post('keyword',true);
        if(empty($keyword)){
            Universal::Output($this->config->item('request_fall'),'没有获取到搜素关键词');
        }else{
            $keyword=Universal::safe_replace($keyword);
        }
        $this->load->model('center/mobile_model');        
        $this->mobile_model->brand=$id;
        $this->mobile_model->page=$page;
        $this->mobile_model->num=7;
        $this->mobile_model->keyword=$keyword;
        $response=$this->mobile_model->searchType();
        if( $response !== false){
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有搜索到相关的结果');
        }
    }
    /**
     * 型号管理  根据型号id删除
     * @param   int   typeid    型号id
     * @param   int   brandid   品牌
     * @return  json  删除结果
     */
    function deltype(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $typeid=$this->input->post('typeid',true);
        if(empty($typeid) || !is_numeric($typeid)){
            Universal::Output($this->config->item('request_fall'),'没有获取到型号编号');
        }
        $brand=$this->input->post('brandid',true);
        if(empty($brand) || !is_numeric($brand)){
            Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
        }
        $this->load->model('center/mobile_model');
        $this->mobile_model->typeid=$typeid;
        $this->mobile_model->brandid=$brand;
        $response=$this->mobile_model->deltype();
        if( $response ){
            Universal::Output($this->config->item('request_succ'),'删除型号名称成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'删除型号名称失败!');
        }
    }
    /**
     *型号管理  增加新的型号
     *@param   int     brand  品牌id
     *@param   string  type  型号名称
     *@return  json  添加的结果
     */
     function addtype(){
         //校验用户是否在线
         $this->load->model('center/login_model');
         $this->login_model->isOnine();
         
         $type=$this->input->post('type',true);
         if(empty($type)){
             Universal::Output($this->config->item('request_fall'),'没有获取到型号内容');
         }else{
             $type=str_replace(array(' ',''),array('[A]','[B]'),$type);
             $type=Universal::safe_replace($type);
             $type=str_replace(array('[A]','[B]','KEYA1'),array(' ','','+'),$type);
         }
         $brand=$this->input->post('brand',true);
         if(empty($brand) || !is_numeric($brand)){
             Universal::Output($this->config->item('request_fall'),'没有获取到品牌编号');
         }
         $this->load->model('center/mobile_model');
         $this->mobile_model->type=$type;
         $this->mobile_model->brand=$brand;
         $response=$this->mobile_model->addtype();
         if( $response ){
             Universal::Output($this->config->item('request_succ'),'新增型号名称成功!');
         }else{
             Universal::Output($this->config->item('request_fall'),'新增型号名称失败!');
         }
         
     }
    
}
