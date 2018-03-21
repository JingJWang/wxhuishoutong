<?php
class option extends CI_Controller{
    /**
     * 获取品牌列表
     * @return  json
     */
    function getBrand(){
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
     * 获取产品型号列表
     * @param   int    id  型号id
     * @return  json
     */
    function getTypes(){
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
     * 获取参数信息
     */
    function  getOption(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        //校验传递参数是否合法
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        //校验是否已经配置过参数信息
        $this->load->model('center/quote_model');
        $this->quote_model->id=$id;
        //获取配置的信息
        $attr=$this->quote_model->getAtrr();
        if($attr !== false){
            $attr=json_decode($attr['0']['types_attr'],true);
        }else{
            $attr='';
        }     
        $data=$this->quote_model->getOption();
        //重组数组格式
        $temp_model=array();
        foreach ($data['model'] as $key=>$val){
            $info = empty($val['content']) ? '0' : explode(',',$val['content']);
            $content=array();
            if(empty($attr)){
                foreach ($info as $i=>$n){
                    $n=str_replace(array('[',']'),array('',''),$n);
                    $content[]=array('id'=>$n,'sign'=>0);
                }
            }else{
                if(array_key_exists($val['alias'],$attr)){
                    foreach ($info as $i=>$n){
                        $n='['.$n.']';
                        $sign=in_array($n,$attr[$val['alias']]) ? 1 : 0;
                        $n=str_replace(array('[',']'),array('',''),$n);
                        $content[]=array('id'=>$n,'sign'=>$sign);
                    }
                }else{
                    foreach ($info as $i=>$n){
                        $n=str_replace(array('[',']'),array('',''),$n);
                        $content[]=array('id'=>$n,'sign'=>0);
                    }
                } 
            }
            $data['model'][$key]['content']=$content;
            $content=array();
        }
        foreach ($data['option'] as $key=>$val){
            $option[$val['id']]=$val['info'];
        }
        $data['option']=$option;
        if(!$data){
            Universal::Output($this->config->item('request_fall'),'未能获取到相关的结果');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }
    }
    /**
     * 保存新增加的型号信息
     */
    function saveOption(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        $data=$this->input->post();        
        if(empty($data)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
        }else{
            $data=array_filter($data);
        }
        $attr=array();
        foreach ($data as $k=>$v){
            $val=explode(',',trim($v,','));
            $attr[$k]=Universal::safe_replace($val);
        }
        $this->load->model('center/quote_model');
        $this->quote_model->option=$attr;
        $res=$this->quote_model->saveOption();
        if($data){
            Universal::Output($this->config->item('request_succ'),'内容新增成功!');
        }else{
            Universal::Output($this->config->item('request_fall'),'内容新增失败');
        }
    }
    /**
     * 保存选中的型号属性信息
     * @param  string  info  属性信息
     * @return  json
     */
    function  optionSave(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数
        $info=$this->input->post('info');
        $id=$this->input->post('attrid');
        if(empty($info) || empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
        }else{
            $attr=explode('|',$info);
            $attr=array_filter($attr);
        }
        $attrinfo=array();
        foreach ($attr as $k=>$v){
            $temp=explode(':',$v);
            if(!is_numeric($temp['1'])){
                Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
            }
            $attrinfo[$temp['0']][]='['.$temp['1'].']';
        }
        $this->load->model('center/quote_model');
        $this->quote_model->attr=json_encode($attrinfo);
        $this->quote_model->id=$id;
        $data=$this->quote_model->saveAttr();
        if($data){
            Universal::Output($this->config->item('request_succ'),'保存属性成功');
        }else{
            Universal::Output($this->config->item('request_fall'),'保存属性失败');
        }
    }
    /**
     * 搜素品牌信息
     * @param   string   key   关键词
     * @return  json
     */
    function searchBrand(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验传递参数是否合法
        $key=$this->input->post('key');
        if(empty($key)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
        }
        $this->load->model('center/quote_model');
        $this->quote_model->key=Universal::safe_replace($key);
        $this->quote_model->type=5;
        $data=$this->quote_model->brandSearch();
        if(!$data){
            Universal::Output($this->config->item('request_fall'),'未能获取到相关的结果');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }
    }
    /**
     * 搜素某一个品牌下的型号
     * @param   string  key    关键词
     * @param   int     type   品牌ID
     * @return  json
      */
    function searchType(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();//校验当前用户是否登录
        
        //校验参数是否合法
        $key=$this->input->post('key',true);
        $id=$this->input->post('type',true);
        if(empty($key) || empty($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
        }
        if(!is_numeric($id) || isset($id{5}) || isset($key{20})){
            Universal::Output($this->config->item('request_fall'),'请求无效');
        }
        $this->load->model('center/quote_model');
        $this->quote_model->key=Universal::safe_replace($key);
        $this->quote_model->id=$id;
        $data=$this->quote_model->typeSearch();
        if(!$data){
            Universal::Output($this->config->item('request_fall'),'未能获取到相关的结果');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }
    }
    /**
     * 根据属性id删除
     * @param  int   id  属性id
     * @return  json
     */
    function delAttr(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        //校验参数是否正确
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
        }
        $this->load->model('center/quote_model');
        $this->quote_model->id=$id;
        //删除以前校验是否正在使用当中
        $res=$this->quote_model->checkAttr();
        if(!$res){
            $resopse=$this->quote_model->msg;
            if(is_array($resopse)){
                $msg='';
                foreach($resopse as $k=>$v){
                    $msg .= '['.$v['name'].'],';
                }
                $msg='型号为:'.trim($msg,',').':正在使用使用该属性,不能删除!';
            }else{
                $msg='属性正在使用不能删除!';
            }
            Universal::Output($this->config->item('request_fall'),$msg);
        }
        $response=$this->quote_model->delAttr();
        if(!$response){
            Universal::Output($this->config->item('request_fall'),'删除属性失败!');
        }else{
            Universal::Output($this->config->item('request_succ'),'删除属性成功!');
        }
    }
}