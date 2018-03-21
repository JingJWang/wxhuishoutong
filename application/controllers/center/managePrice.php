<?php
/**
 * 后台管理
 * 手机品牌 型号管理
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class  managePrice extends  CI_Controller{

    /**
     * 型号管理 获取品牌列表
     * @return  json  型号列表
     */
    function typeBrandlist(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $this->load->model('center/manageprice_model');
        $response=$this->manageprice_model->typeBrandlist();
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
        $this->load->model('center/manageprice_model');
        $this->manageprice_model->id=$id;
        $this->manageprice_model->page=$page;
        $this->manageprice_model->num=10;
        $response=$this->manageprice_model->typeList();
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
    function editTypePrice(){
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
        $price=$this->input->post('price',true);
        if(empty($id) || !is_numeric($price)){
            Universal::Output($this->config->item('request_fall'),'没有获取到价格');
        }    
        $this->load->model('center/manageprice_model');
        $this->manageprice_model->id=$id;
        $this->manageprice_model->brand=$brandid;
        $this->manageprice_model->price=$price;
        $response=$this->manageprice_model->editTypePrice();
        if( $response !== false){
            Universal::Output($this->config->item('request_succ'),'修改型号市价成功');
        }else{
            Universal::Output($this->config->item('request_fall'),'修改型号市价成功');
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
        $this->load->model('center/manageprice_model');        
        $this->manageprice_model->brand=$id;
        $this->manageprice_model->page=$page;
        $this->manageprice_model->num=7;
        $this->manageprice_model->keyword=$keyword;
        $response=$this->manageprice_model->searchType();
        if( $response !== false){
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有搜索到相关的结果');
        }
    }
    /**
     * 批量修改手机回收最高价 通过品牌修改
     */
     function upcountprice(){
       $data=$this->input->post();
       if(count(array_filter($data))!=4){
           Universal::Output($this->config->item('request_fall'),'获取的参数有误');
       }
       if(!is_numeric($data['radck']) && !is_numeric($data['hideRadio'])&& !is_numeric($data['hoverinp'])){
           Universal::Output($this->config->item('request_fall'),'获取的参数有误');
       }
      
       $this->load->model('center/manageprice_model');
       $this->manageprice_model->radck=$data['radck'];
       $this->manageprice_model->hideRadio=$data['hideRadio'];
       $this->manageprice_model->hoverinp=$data['hoverinp'];
       $this->manageprice_model->dataid=substr($data['dataid'],0,strlen($data['dataid'])-1);
       $response=$this->manageprice_model->upcountprice();
       if( $response !== false){
           Universal::Output($this->config->item('request_succ'),'修改成功','',$response);
       }else{
           Universal::Output($this->config->item('request_fall'),'修改失败');
       }
     }
     /**
      * 批量修改手机市价管理 通过价格区间修改
      */
     function uprangeprice(){
         $data=$this->input->post();
         $this->load->model('center/manageprice_model');
         if(isset($data['status']) && is_numeric($data['status']) && !empty($data['status'])){
             if($data['status']==1){//取价格区间的最大值
                 if(!is_numeric($data['lastPrice']) || empty($data['lastPrice']) || !isset($data['lastPrice'])){
                     Universal::Output($this->config->item('request_fall'),'获取的参数有误');
                 }
                  $this->manageprice_model->lastPrice = $data['lastPrice'];
             }else if($data['status']==2){//取价格区间
                 if(!is_numeric($data['start']) || empty($data['end']) || !isset($data['start'])){
                     Universal::Output($this->config->item('request_fall'),'获取的参数有误');
                 }
                 $this->manageprice_model->startPirce = $data['start'];
                 if(!is_numeric($data['end']) || empty($data['end']) || !isset($data['end'])){
                     Universal::Output($this->config->item('request_fall'),'获取的参数有误');
                 }
                 $this->manageprice_model->endPirce = $data['end'];
             }
         }
         if(empty($data['radck']) && !isset($data['radck'])&& !is_numeric($data['radck'])){
             Universal::Output($this->config->item('request_fall'),'获取的参数有误');
         }
         if(empty($data['hoverinp']) && !isset($data['hoverinp'])&& !is_numeric($data['hoverinp'])){
             Universal::Output($this->config->item('request_fall'),'获取的参数有误');
         }
         $this->manageprice_model->status=$data['status'];
         $this->manageprice_model->radck=$data['radck'];
         $this->manageprice_model->hoverinp=$data['hoverinp'];
         $this->manageprice_model->hideRadio=$data['hideRadio'];
         $response=$this->manageprice_model->uprangeprice();
         if( $response !== false){
             Universal::Output($this->config->item('request_succ'),'修改成功','',$response);
         }else{
             Universal::Output($this->config->item('request_fall'),'修改失败');
         }
         
     }
}
