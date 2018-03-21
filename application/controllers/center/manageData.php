<?php
/**
 *系统数据管理
 *首页交易额  成交单数   成交记录数据
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class  manageData  extends  CI_Controller{ 
    
    
    function __construct(){
       parent::__construct();
       //校验用户是否在线
       $this->load->model('center/login_model');
       $this->login_model->isOnine();
    }
    /**
     * 读取交易额记录
     * @param  int  p  页码
     * @return  json  返回页码内的内容
     */
    function  getVolume(){
        //校验传递的页码是否为整数
       $page=$this->input->post('p',true);
       if(!is_numeric($page)){
           Universal::Output($this->config->item('request_fall'),'接收到的页码格式不正确');
       }
       //读取记录结果集
       $this->load->model('center/managedata_model');
       $result=$this->managedata_model->getVolume();
       if(!$result){
           Universal::Output($this->config->item('request_fall'),'没有获取到相关的结果');
       }
       //读取结果集中的  p 页中的内容
       if($page >  $result['total']){
           Universal::Output($this->config->item('request_fall'),'当前已经最后一页');
       }
       $start=($page-1)*10;
       $list['total']=ceil($result['total']/10);
       $list['list']=array_slice($result['list'],$start,10);
       //改变交易额成交单数显示格式  添加状态
       $status=array('1'=>'正在使用','0'=>'未使用','-1'=>'已过期');
       foreach ($list['list'] as $key=>$val){
           $temp=json_decode($val['content'],true);
           $list['list'][$key]['start']=date('Y-m-d',$val['start']);
           $list['list'][$key]['volume']=number_format($temp['volume']);
           $list['list'][$key]['number']=$temp['number'];
           $list['list'][$key]['status']=$status[$val['status']];
           unset($list['list']['content']);
       }
       Universal::Output($this->config->item('request_succ'),'','',$list);
    }    
    /**
     * 添加交易额 成交单数
     * @param   string data     时间
     * @param   int    volume   交易额
     * @param   int    number   成交单数
     * @return  json 返回插入数据的结果
     */
    function addVolume(){             
        //校验传递参数是否正确
        $data=$this->input->post('data',true);
        //判断日期格式是否正确
        if(!strtotime($data)){
            Universal::Output($this->config->item('request_fall'),'日期格式不正确');
        }
        $volume=$this->input->post('volume',true);
        $number=$this->input->post('number',true);
        //校验成交额于成交单数是否正确
        if(!is_numeric($volume) || !is_numeric($number)){
            Universal::Output($this->config->item('request_fall'),'交易额,成交单数不正确');
        }
        $start=date('Y-m-d 11:00:01',strtotime($data));
        $stop=date('Y-m-d 11:00:00',strtotime($start)+86400);
        $content=json_encode(array('volume'=>$volume,'number'=>$number));
        //加载model
        $this->load->model('center/managedata_model');
        $this->managedata_model->start=strtotime($start);
        //校验是否存在重复的记录
        $check=$this->managedata_model->checkVolume();
        if(!$check){
            Universal::Output($this->config->item('request_fall'),'已经存在同一天的记录');
        }
        $this->managedata_model->stop=strtotime($stop);
        $this->managedata_model->content=$content;
        $result=$this->managedata_model->saveVolume();
        if($content){
            Universal::Output($this->config->item('request_succ'),'已成功写入一条交易额记录');
        }else{
            Universal::Output($this->config->item('request_fall'),'写入交易额记录出现异常');
        }
    }
    /**
     * 删除交易额记录
     * @param   int   id  记录id
     * @return  json 删除结果
     */
    function delVolume(){
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'传递的当前记录ID不正确');
        }
        //加载model
        $this->load->model('center/managedata_model');
        $this->managedata_model->id=$id;
        $result=$this->managedata_model->delVolume();
        if($result){
            Universal::Output($this->config->item('request_succ'),'当前记录已经成功删除');
        }else{
            Universal::Output($this->config->item('request_fall'),'删除当前记录出现异常');
        }
    }
    /**
     * 读取交易额记录
     * @param   int   id  记录id
     * @return json 结果
     */
    function seeVolume(){
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'传递的当前记录ID不正确');
        }
        //加载model
        $this->load->model('center/managedata_model');
        $this->managedata_model->id=$id;
        $result=$this->managedata_model->seeVolume();
        if($result){
            Universal::Output($this->config->item('request_succ'),'','',$result);
        }else{
            Universal::Output($this->config->item('request_fall'),'删除当前记录出现异常');
        }
    }
    /**
     * 修改交易额与成交单数记录
     * @param  int  id   记录id
     * @param  int  volume  交易额
     * @param  int  number  单数
     * @return  json  结果
     */
    function upVolume(){
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'传递的当前记录ID不正确');
        }
        $volume=$this->input->post('volume',true);
        $number=$this->input->post('number',true);
        if(!is_numeric($volume) || !is_numeric($number)){
            Universal::Output($this->config->item('request_fall'),'交易额于成交记录格式不正确');
        }
        $date=$this->input->post('data',true);
        if(!strtotime($date)){
            Universal::Output($this->config->item('request_fall'),'展示时间格式不正确');
        }
        $start=date('Y-m-d 11:00:01',strtotime($date));
        $stop=date('Y-m-d 11:00:00',strtotime($start)+86400);
        //加载model
        $this->load->model('center/managedata_model');
        $this->managedata_model->id=$id;
        $this->managedata_model->content=json_encode(array('volume'=>$volume,'number'=>$number));
        $this->managedata_model->start=strtotime($start);
        $this->managedata_model->stop=strtotime($stop);
        $result=$this->managedata_model->upVolume();
        if($result){
            Universal::Output($this->config->item('request_succ'),'当前记录修改成功');
        }else{
            Universal::Output($this->config->item('request_fall'),'修改当前记录出现异常');
        }
        
    }
    /**
     * 添加成交记录
     * @param   string   date  展示的日期
     * @param   string   start 开始的时间
     * @param   string   stop  结束的时间
     */
    function addRecord(){
        //校验日期是否正确
        $date=$this->input->post('date',true);
        if(!strtotime($date)){
            Universal::Output($this->config->item('request_fall'),'日期为空或者格式不正确');
        }
        $start=$this->input->post('start',true);
        if(!is_numeric(str_replace(':','',$start))){
            Universal::Output($this->config->item('request_fall'),'开始时间为空或者格式不正确');
        }
        $stop=$this->input->post('stop',true);
        if(!is_numeric(str_replace(':','',$stop))){
            Universal::Output($this->config->item('request_fall'),'结束时间为空或者格式不正确');
        }
        //校验交易记录
        $user=$this->input->post('user',true);
        $mobile=$this->input->post('mobile',true);
        $time=$this->input->post('time',true);
        $type=$this->input->post('type',true);
        $moeny=$this->input->post('moeny',true);
        $content=$this->input->post('content',true);
        if(count($user) != 5 || count($mobile) != 5 || count($time) != 5|| count($type) 
                != 5 || count($moeny) != 5){
            Universal::Output($this->config->item('request_fall'),'少于5条记录!');
        }
        $list=array();
        foreach ($user as $key=>$val){
            $row=$key+1;
            $temp_name=Universal::safe_replace($val);
            if(empty($temp_name)){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录用户名为空');
            }
            $temp_mobile=Universal::safe_replace($mobile[$key]);
            if(!is_numeric($temp_mobile)){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录手机号码为空或者格式不正确');
            }
            $temp_time=Universal::safe_replace($time[$key]);
            if(!is_numeric($temp_time)){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录评论时间为空或者格式不正确');
            }
            $temp_type=Universal::safe_replace($type[$key]);
            if(empty($temp_type)){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录手机型号为空');
            }
            $temp_moeny=Universal::safe_replace($moeny[$key]);
            if(empty($temp_moeny)){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录成交金额为空');
            }
            $list[]=array('name'=>$temp_name,'mobile'=>$temp_mobile,'time'=>$temp_time,
                    'type'=>$temp_type,'moeny'=>$temp_moeny,'content'=>Universal::safe_replace($content[$key]));
        }
        $this->load->model('center/managedata_model');
        $this->managedata_model->start=strtotime($date.$start);
        $this->managedata_model->stop=strtotime($date.$stop);
        $this->managedata_model->list=json_encode($list);
        $result=$this->managedata_model->addRecord();
        if($result){
            Universal::Output($this->config->item('request_succ'),'已经成功写入一条记录');
        }else{
            Universal::Output($this->config->item('request_fall'),'写入记录出现异常');
        }
    }
    /**
     * 读取成交记录列表
     * @param  int   p  分页
     * @return  结果集
     */
    function getRecord(){
       $page=$this->input->post('p',true);
       if(!is_numeric($page)){
           Universal::Output($this->config->item('request_fall'),'没有获取到当前页码');
       }
       $this->load->model('center/managedata_model');
       $this->managedata_model->page=$page;
       $result=$this->managedata_model->getRecord();
       if(!$result){
           Universal::Output($this->config->item('request_fall'),'没有获取到相关的结果集');
       }
       $start=($page-1)*10;
       $list['total']=ceil($result['total']/10);
       $temp=array_slice($result['list'],$start,10);
       $status=array('1'=>'正在使用','0'=>'未使用','-1'=>'已使用');
       foreach ($temp as $key=>$val){
           $list['list'][$key]['id']=$val['data_id'];
           $list['list'][$key]['time']=date('Y-m-d H:i:s',$val['data_starttime']).'至'.
           date('H:i',$val['data_stoptime']);
           $list['list'][$key]['content']=json_decode($val['data_content'],true);
           $list['list'][$key]['status']=$status[$val['data_status']];
       }
       Universal::Output($this->config->item('request_succ'),'','',$list);
    }
    /**
     * 根据id读取记录内容
     * @param  int  id  记录id
     * @return  json  返回记录详细内容
     */
    function recordInfo(){
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到详情记录');
        }
        $this->load->model('center/managedata_model');
        $this->managedata_model->id=$id;
        $result=$this->managedata_model->recordInfo();
        if(!$result){
            Universal::Output($this->config->item('request_fall'),'没有获取到相关的结果集');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$result);
        }
    }
    /**
     * 根据id修改记录内容
     * @param   int  id  记录id
     * @param   string  name  用户名
     * @param   int  mobile  手机号码
     * @param   string  time 时间
     * @param   string   type 型号
     * @param   string  moeny 金额
     * @param   string  content 内容
     * @return  json  返回修改结果
     */
    function upRecord(){
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到要修改的记录');
        }
        //校验交易记录
        $user=$this->input->post('name',true);
        $mobile=$this->input->post('mobile',true);
        $time=$this->input->post('time',true);
        $type=$this->input->post('type',true);
        $moeny=$this->input->post('moeny',true);
        $content=$this->input->post('content',true);
        if(count($user) != 5 || count($mobile) != 5 || count($time) != 5|| count($type) 
                != 5 || count($moeny) != 5){
            Universal::Output($this->config->item('request_fall'),'少于5条记录!');
        }
        $list=array();
        foreach ($user as $key=>$val){
            $row=$key+1;
            if(empty($val)){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录用户名为空');
            }
            if(!is_numeric($mobile[$key])){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录手机号码为空或者格式不正确');
            }
            if(!is_numeric($time[$key])){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录时间为空或者格式不正确');
            }
            if(empty($type[$key])){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录手机型号为空');
            }
            if(empty($moeny[$key])){
                Universal::Output($this->config->item('request_fall'),'第'.$row.'条记录成交金额为空');
            }
            $list[]=array('name'=>$val,'mobile'=>$mobile[$key],'time'=>$time[$key],
                    'type'=>$type[$key],'moeny'=>$moeny[$key],'content'=>$content[$key]);
        }
        $this->load->model('center/managedata_model');
        $this->managedata_model->id=$id;
        $this->managedata_model->content=json_encode($list);
        $result=$this->managedata_model->upRecord();
        if($result){
            Universal::Output($this->config->item('request_succ'),'当前记录已经更新');
        }else{
            Universal::Output($this->config->item('request_fall'),'修改当前记录出现异常');
        }
    }
    /**
     * 根据id删除记录
     * @param  int  id   记录id
     * @return  json 删除结果
     */
    function delRecord(){
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到要删除的记录');
        }
        $this->load->model('center/managedata_model');
        $this->managedata_model->id=$id;
        $result=$this->managedata_model->delRecord();
        if($result){
            Universal::Output($this->config->item('request_succ'),'当前记录已经删除');
        }else{
            Universal::Output($this->config->item('request_fall'),'删除当前记录出现异常');
        }
    }
    
    
}