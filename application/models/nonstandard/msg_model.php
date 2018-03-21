<?php
/**
 * 短信模块
 * 用于系统内  所有调用短信接口 
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Msg_model extends CI_Model {
    
    //验证码表    
    private   $verify_code  =  'h_verify_code';
    //短信类型
    public    $code_type    =  ''; 
    //发送类型
    public    $type         =  '';
    //加载数据库类
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 发送短信
     * @param   int     mobile    手机号码
     * @param   int     codetype  短信类型  
     * @param   array   content   发送内容
     * @param   string  type      发送类型   notice 通知类型    verify 验证类型
     * @param   int     template  短信模板id
     */
    function sendmsg(){
        switch ($this->type){
            case 'notice':
                return false;
                break;
            case 'verify':
                $result=$this->send_checkcode();
                return $result;
                break;
            case 'Voice':
                return false;
                break;
        }
    }
   /**
     * 发送短信验证码
     * @param   int     mobile    手机号码
     * @param   array   content   发送内容
     * @param   string  type      发送类型   notice 通知类型    verify 验证类型
     * @param   int     template  短信模板id
     * @return  boolean 发送成功返回 true | 失败返回  false
     */  
    function  send_checkcode(){        
        if(empty($this->mobile) || empty($this->content)){
            return  '手机号码或发送内容为空!'; 
        }
        if(!is_numeric($this->mobile) || isset($this->mobile{11})){
            return '手机号码类型格式正确!'; 
        }
        //校验当前的手机号码 是否已经被绑定过手机号码
        $check_sql='select wx_id from  h_wxuser  where  wx_mobile="'.$this->mobile.'"';
        $check_row=$this->db->query($check_sql);
        if($check_row->num_rows >0){
            return '该手机号码被占用!';
        }
        //校验是否已经存在同类型的手机验证码  是否超出 当天发送限制
        $sql='select code_id from '.$this->verify_code.' where code_jointime > '.
              strtotime("-1 day").' and code_jointime < "'.time().'" and code_moblie="'.
              $this->mobile.'" and code_type='.$this->code_type;
        $query=$this->db->query($sql);
        if($query->num_rows >= $this->code_limit){
               return '验证码发送次数超过限制!'; 
        }
        $this->load->library('message/shortmsg');
        $data=array(
                'code_type'=>$this->code_type,
                'code_moblie'=>$this->mobile,
                'code_number'=>$this->number,
                'code_jointime'=>time(),
        );
        //调用发送短信验证类库
        $info=$this->shortmsg->sendTemplateSMS($this->mobile,$this->content,$this->template);  
        if($info['status'] == $this->config->item('app_send_succ')){
            //发送成功
            $data['response_status']=$info['status'];
            $data['response_time']=$info['time'];
            $data['response_sid']=$info['sid'];
            $data['code_status']=1;
        }else{
            //发送失败
            $data['response_status']=$info['status'];
            $data['response_info']=$info['info'];
            $data['code_status']=0;
        }
        //保存发送内容
        $query=$this->db->insert($this->verify_code,$data);
        if($info['status'] != $this->config->item('app_send_succ')){
            return false;
        }
        if($query === false){
            return false;
        } 
        return true;
    }      
    /**
     * 用户模块-校验验证码是否正确  
     * @param      int     mobile   手机号码
     * @param      string  code     验证码
     * @param      int     type     类型  例如 注册 验证码  提现验证码
     * @param      int     invalid  失效时间
     * @return     成功返回 bool | true , 校验失败返回 bool | false
     */
    function check_code(){
        if(empty($this->mobile) || empty($this->code)){
            return  false ;
        }
        if(!is_numeric($this->mobile) || !is_numeric($this->code)){
            return  false;
        }
        $sql='select code_id,code_jointime,code_moblie from '.$this->verify_code.'
              where  code_moblie='.$this->mobile.' and code_number='.$this->code.' 
              and code_status=1  and response_status='.$this->config->item('app_send_succ').' 
              and  code_type='.$this->type;
        $query=$this->db->query($sql);
        if($query ===  false  || $query->num_rows() < 1){
            return false;
        }
        $result=$this->db->fetch_query($query);        
        if(time() - $result['0']['code_jointime'] > $this->invalid ){
            $this->db->update($this->verify_code,array('code_status'=>'-2'),
                array('code_id'=>$result['0']['code_id']),array());
            return  false;
        }
        //验证码校验成功
        $res=$this->edit_msgstatus($result['0']['code_id'], '-1');
        return $res;
    }
    
    /**
     * 生成随机字符串
     * @param   int      len      长度
     * @param   string   format   格式  ALL  CHAR  NUMBER
     * @return  string   返回生成的字符串
     */    
    function randStr($len=6,$format='ALL') {
        switch($format) {
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789'; 
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
                break;
            case 'NUMBER':
                $chars='0123456789'; 
                break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        mt_srand((double)microtime()*1000000*getmypid());
        $content="";
        while(strlen($content)<$len){
            $content.=substr($chars,(mt_rand()%strlen($chars)),1);
        }
        return $content;
    }
    /**
     * 更改信息状态
     * @param      int      id      信息id
     * @param      int      sttaus  状态
     * @return     bool             返回结果
     */
    function   edit_msgstatus($id,$status){
        $data=array('code_updatetime'=>time(),'code_status'=>$status);
        $where=array('code_id'=>$id);
        $result=$this->db->update($this->verify_code,$data,$where,$where,array());
        if($this->db->affected_rows() == 1 &&  $result){
            return true;
        }
        return false;
    }
    /**
     * 阿里大鱼  短信验证码  用户提现
     * @param    int        mobile      手机号码
     * @param    array      content     内容
     * @param    string     type        短信类型
     * @param    string     sign        短信签名
     * @param    string     extend      回传数据
     * @param    string     template    短信模板
     * @return  发送成功返回bool  true  | 发送失败 bool false
     */
    function  UserExtractCash(){
        $this->load->library('alidayu/alimsg');
        $this->alimsg=new Alimsg();
        $this->alimsg->mobile=$this->mobile;
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->sign=$this->config->item('alidayu_signname');
        $this->alimsg->template=$this->config->item('alidayu_templte_extractcash');
        $this->number=$this->randStr(6,'NUMBER');
        $this->alimsg->content="{\"code\":\"".$this->number."\",\"minute\":\"5\"}";
        $response=$this->alimsg->SendVerifyCode();
        $res=$this->SaveSms($response);
        return $res;
    }
    /**
     * 阿里大鱼  校验当前的验证码是否正确
     * @param   int     mobile  手机号码
     * @param   int     minute  超时条件(分钟)
     * @param   int     type    验证码分类
     * @param   string  code    验证码
     * @return  校验成功 返回 bool  true | 校验失败返回  false
     */
    function CheckVerify(){
        if(empty($this->mobile) || empty($this->code)){
            return  false ;
        }
        if(!is_numeric($this->mobile) || !is_numeric($this->code) 
                || !is_numeric($this->minute)){
            return  false;
        }
        $sql='select code_id,code_jointime,code_moblie from '.$this->verify_code.'
              where  code_moblie='.$this->mobile.' and code_number='.$this->code.'
              and code_status=1  and response_status='.$this->config->item('alidayu_sendsucc').'
              and  code_type='.$this->type;
        $query=$this->db->query($sql);
        if($query ===  false  || $query->num_rows() < 1){
            return false;
        }
        if(time() - $result['0']['code_jointime'] > $this->minute*60 ){
            $data=array('code_status'=>'-2');
            $where=array('code_id'=>$result['0']['code_id']);
            $query=$this->db->update($this->verify_code,$data,$where,array());
            return  false;
        }
        //验证码校验成功
        $res=$this->edit_msgstatus($result['0']['code_id'], '-1');
        return $res;
    }
    /**
     * 阿里大鱼  发送语音验证码  用户注册
     * @param   string   appkey  应用id
     * @param   string   secret  秘钥
     * @param   string   extend  回传数据
     * @param   int      mobile  手机号码
     * @param   string   shownum 去电号码
     * @param   int      templte 模板id
     * @return  发送成功返回bool  true  | 发送失败 bool false
     */
    function SendVoiceCode(){  
        $this->load->library('alidayu/alimsg'); 
        $this->alimsg=new Alimsg();
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');      
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->extend=$this->config->item('alidayu_extend');
        $this->alimsg->mobile=$this->mobile;
        $this->alimsg->shownum=$this->config->item('alidayu_shownum');
        $this->alimsg->templte=$this->config->item('alidayu_templte_voicecode');
        $this->number=$this->msg_model->randStr(6,'NUMBER');
        $this->alimsg->content=json_encode(array('product'=>'回收通','code'=>$this->number)); 
        $response=$this->alimsg->SendVoiceCode();
        $res=$this->SaveSms($response);
        return $res;
    }
    /**
     * 阿里大鱼  发送注册短信验证码  用户注册
     * @param   string   appkey  应用id
     * @param   string   secret  秘钥
     * @param   string   extend  回传数据
     * @param   int      mobile  手机号码
     * @param   int      templte 模板id
     * @return  发送成功返回bool  true  | 发送失败 bool false
     */
    function SendSmsCode(){  
        $this->load->library('alidayu/alimsg'); 
        $this->alimsg=new Alimsg();
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');      
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->sign=$this->config->item('alidayu_signname');
        $this->alimsg->mobile=$this->mobile;
        $this->alimsg->template=$this->templte;
        $this->number=$this->msg_model->randStr(6,'NUMBER');
        $this->alimsg->content=json_encode(array('product'=>'回收通','code'=>$this->number)); 
        $response=$this->alimsg->SendNotice();
        $res=$this->SaveSms($response);
        return $res;
    }
    /**
     * 保存当前短信验证码发送记录
     * @param   string  content  短信内容
     * @return  发送成功返回bool  true  | 发送失败 bool false
     */
    function  SaveSms($response){
        if($response === true){
            $data=array('code_type'=>$this->code_type,'code_moblie'=>$this->mobile,
                    'code_number'=>$this->number,'code_jointime'=>time(),
                    'response_status'=>$this->alimsg->code,
                    'response_sid'=>$this->alimsg->msg,'code_status'=>1);
            //保存发送内容
            $query=$this->db->insert($this->verify_code,$data);
            if($query){
                return  true;
            }
            return false;
        }
        if($response === false){
            $data=array('code_type'=>$this->code_type,'code_moblie'=>$this->mobile,
                    'code_number'=>$this->number,'code_jointime'=>time(),
                    'response_status'=>$this->alimsg->code,
                    'response_info'=>$this->alimsg->msg,'code_status'=>0);
            $query=$this->db->insert($this->verify_code,$data);
            if($query){
                return  false;
            }
            return false;
        }
    }
    /**
     * 校验当前手机号码是否已经存在
     * @param    int  mobile  手机号码
     * @return   存在返回false | 不存在返回false
     */
    function CheckMobile(){
        $check_sql='select wx_id from  h_wxuser  where  wx_mobile="'.$this->mobile.'"';
        $check_row=$this->db->query($check_sql);
        if($check_row->num_rows >0){
           return false;
        }
        return true;
    }
    /**
     * 校验当前手机号码是否超过当前发送上限
     * @param   int  mobile     手机号码
     * @param   int  code_limit 校验次数
     * @param   int  code_type  校验类型
     * @return  超过上限返回 bool false | 没有超过上限返回bool true
     */
    function CheckNum(){        
        $sql='select code_id,code_jointime from '.$this->verify_code.' where code_jointime > '.
                strtotime("-1 day").' and code_jointime < "'.time().'" and code_moblie="'.
                $this->mobile.'" and code_type='.$this->code_type;
        $query=$this->db->query($sql);
        if($query->num_rows >= $this->code_limit){
           return false;
        }
        $data=$query->result_array();
        $content=end($data);
        if(time() - $content['code_jointime'] < 60 ){
           return false;
        }
        return true;
    }
    /**
     * 注册校验-校验同一个IP注册的账号数量
     * @param    string  ip  当前注册的ip
     * @return  bool  返回校验的结果
     */
    function checkRegister(){
            $sql='select wx_id from h_wxuser where wx_loginip="'.$this->ip.'"';            
            $query=$this->db->query($sql);
            if($query === false || $query->num_rows > 3){
                return false;
            }
            return true;
    }
}