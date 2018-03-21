<?php
/*
 * 个人中心
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class Wxuser extends CI_Controller {  

    /**
     * 个人中心  查看个人资料
     */
    function  ViewUser(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->model('nonstandard/wxuser_model');
        $addres=$this->wxuser_model->get_userinfo();
        $view['address']=$addres['0']['province'].'-'.$addres['0']['city'].'-'.$addres['0']['county'];
        $view['info']=$addres['0']['residential'];
        $view['mobile']=$addres['0']['mobile'];
        $this->load->view('nonstandard/userdata',$view);
    }
    /**
     * 个人中心-更改用户资料
     */
    function update_wxuserinfo(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $city=$this->input->post('city',true);
        $info=$this->input->post('address',true);
        if(empty($city) || empty($info)){
            Universal::Output($this->config->item('request_fall'),'没有获取到地址信息');
        }
        $this->load->model('nonstandard/wxuser_model');
        $this->wxuser_model->city=Universal::filter($city);
        $this->wxuser_model->info=Universal::filter($info);;
        $this->wxuser_model->userid=$_SESSION['userinfo']['user_id'];
        $response=$this->wxuser_model->update_userinfo();
        if($response){
            Universal::Output($this->config->item('request_succ'),'地址修改完成','/index.php/nonstandard/center/ViewCenter');
        }else{
            Universal::Output($this->config->item('request_fall'),'地址修改失败!');
        }
    }
    /**
     * 评价模块-显示评价页面
     * @param  int   oid  评价的订单id
     */
    function  ViewEvaluation(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('oid','type');
        $request=elements($coulms, $this->input->get(), '');
        if(empty($request['oid']) || !is_numeric($request['oid'])){
            exit();
        }
        if($request['type'] != 'e' && $request['type'] != 'q'){
            exit();
        }
        $request['type'] == 'e' ? $view['option'] = $this->config->item('order_deal_option'): 
        $view['option'] =$this->config->item('order_deal_cancel');
        $view['oid']=$request['oid'];
        $view['type']=$request['type'];
        $this->load->view('nonstandard/evaluation',$view);
    }
    /**
     * 评价模块-对回收商进行评价
     * @param    int      oid         订单id
     * @param    int      fraction    评分
     * @param    string   reason      评价原因
     * @param    string   make        备注       
     * @return   json
     */
    function SubmitEvaluation(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $orderid=$this->input->post('oid',true);        
        if(empty($orderid) || !is_numeric($orderid)){
           Universal::Output($this->config->item('request_fall'),'订单编号不能为空!');
        }        
        $fraction=$this->input->post('fraction',true);
        if(empty($fraction) || !is_numeric($fraction)){
           Universal::Output($this->config->item('request_fall'),'评分不能为空!');
        }
        $type=$this->input->post('type',true);
        if($type != 'e' && $type !='q'){
            Universal::Output($this->config->item('request_fall'),'评价类型不能为空!');
        }
        $reason=$this->input->post('reason',true);   
        if(empty($reason) || isset($reason{30})){
            Universal::Output($this->config->item('request_fall'),'评价内容不能为空!');
        }     
        $make=$this->input->post('make',true);  
        $type == 'e' ? $comment_type='1' : $comment_type='-1';       
        $this->load->model('nonstandard/order_model');
        $this->order_model->orderid=$orderid;
        $this->order_model->userid=$_SESSION['userinfo']['user_id'];
        $this->order_model->fraction=$fraction;
        $this->order_model->reason=Universal::safe_replace($reason);
        $this->order_model->type=$comment_type;
        $this->order_model->make=Universal::safe_replace($make);
        $res=$this->order_model->orderReason();
        if(!$res){
            Universal::Output($this->config->item('request_fall'),'订单评价失败');
        }
        Universal::Output($this->config->item('request_succ'),'',$this->config->item('url_cancelorder_succ').'?status=y');
    }
   /**  
    * 我的评价----评价列表
    */
    function  EvalLIst(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->view('nonstandard/evaluationlist');
    }
    /**
     * 我的评价 ---获取评价列表
     */
    function GetEveluation(){
        //检验用户是否在线
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验参数
        if($this->input->post('type',true) != 's' && 
                $this->input->post('type',true) !== 'g'){
            $response=array('status'=>$this->config->item('request_succ'),
                    'url'=>'','msg'=>$this->lang->line('order_optiontypenull'),'data'=>'');
            echo json_encode($response);exit;
        }
        $this->load->database();
        //h_cooperator_comment
        if($this->input->post('type',true) == 'g'){
             $sql='select b.comment_reason as content ,FROM_UNIXTIME(
                        b.comment_jointime, "%Y-%m-%d ") as time,
                        c.cooperator_name as name,c.cooperator_pic as img ,
                        b.comment_remark as remark from h_order_nonstandard a
                        left join h_cooperator_comment b on a.order_number=
                        b.order_id left join h_cooperator_info c on b.cooperator_number=
                        c.cooperator_number where a.wx_id='.$_SESSION['userinfo']['user_id'].'
                        and (order_orderstatus=10 or order_orderstatus= -1)  and  comment_id !=" "  order by  time desc';
        }
        //h_wxuser_comment
        if($this->input->post('type',true) == 's'){
             $sql='select b.comment_reason as content ,FROM_UNIXTIME(b.comment_jointime,
                  "%Y-%m-%d ") as time,b.comment_remark,b.comment_remark as remark,
                   c.wx_name as name,c.wx_img as img from  h_order_nonstandard  a left join 
                   h_wxuser_comment b on a.order_number=b.order_id   left join  h_wxuser 
                   c on b.wx_id=c.wx_id   where a.wx_id='.$_SESSION['userinfo']['user_id'].'
                   and (order_orderstatus=10 or order_orderstatus= -1)  and  comment_id !=" "   order by  time desc';
        }
        $resul=$this->db->query($sql);
        if($resul->num_rows < 1){
            $data=0;
        }else{
            $data=$resul->result_array();
            if($this->input->post('type',true) == 'g'){
                foreach ($data as $k=>$v){
                    $data[$k]['name']=mb_substr($v['name'],0,1).'师傅';
                    $data[$k]['img']=substr($v['img'],1);
                }
            }
        }
        $response=array(
              'status'=>$this->config->item('request_succ'),
               'msg'=>'','url'=>'','data'=>$data
        );
        echo json_encode($response);exit;
    }
    /**
     * 个人中心 校验当前用户 是否有可提现的金额
     */    
    function ExtractView(){
        //校验当前用户是否登录
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);        
        $this->load->view('nonstandard/extract');
    }
    /**
     * 个人中心 校验当前用户 是否有可提现的金额
     */    
    function zfbExtractView(){
        //校验当前用户是否登录
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);        
        $this->load->view('nonstandard/zfbextract');
    }
    /**
     * 用户绑定微信号
     */
    function userbindwx(){
        if(!isset($_GET['code'])||empty($_GET['code'])){//获取code
            $this->load->view('exception/bindwx');
            return;
        }else{
           $code=$_GET['code'];
        }
        $this->load->model('auto/userauth_model');
        if (!$this->userauth_model->UserIsLogin()||$_SESSION['userinfo']['user_openid']!='') {//未登录或者已经绑定wx
            $this->load->view('exception/bindwx',$data['return']='您已经绑定过微信号了！');
            return;
        }
        $this->load->model('common/wxcode_model','',TRUE);
        $message = $this->wxcode_model->getOpenid_token($code);//获取openid和token
        if (!isset($message['access_token'])||empty($message['access_token'])||empty($message['openid'])) {
            exit();
        }
        $message['openid']=Universal::safe_replace($message['openid']);
        $_SESSION['userid']['Login_openid'] = $message['openid'];
        $this->load->model('nonstandard/wxuser_model');
        //校验用户是否存在
        $userid=$this->wxuser_model->check_user(array('openid'=>$message['openid']));
        if($userid === false || ($userid!==0&&$userid['0']['wx_mobile']!='')){//此微信已经被绑定并且不正常或者此微信号已经被绑定
            $this->load->view('exception/bindwx',$data['return']='此微信已经被绑定或者此微信号异常！');
            return;
        }
        $delop = 0;//是否删除以前记录的openid
        if ($userid!==0&&$userid['0']['wx_mobile']=='') {//未注册的用户
            $delop = 1;
        }
        $info = $this->wxcode_model->get_snsapi_userinfo($message['access_token'],$message['openid']);//获取微信用户的最新消息
        $result = $this->wxuser_model->bindwx($message['openid'],$info,$delop);
        $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],109);//设置微信分组 注册组
        if ($result===false) {
            $data['return']='绑定失败！';
            $this->load->view('exception/bindwx',$data);
            return;
        }
        if (isset($_SESSION['LoginBackUrl'])) {
            header('Location:http://'.$_SERVER['HTTP_HOST'].$_SESSION['LoginBackUrl']);
            exit;
        }else{
            $data['return']='绑定成功';
            $this->load->view('exception/bindwx',$data);
        }
    }                                                                                                                        
}