<?php
/**
 * @author ma  
 * 2016/02/15
 * 阿里云大鱼 短信服务
 * 包含  短信验证码  短信通知  语言验证码 语言通知
 */
include "TopSdk.php";	
class  Alimsg {
    /**
     * 短信验证码 
     * @param    int        mobile      手机号码
     * @param    array      content     内容
     * @param    string     sign        短信签名
     * @param    string     extend      回传数据
     * @param    string     template    短信模板
     * @return   成功返回true | 失败返回false
     */
    function  SendVerifyCode(){
        if(empty($this->mobile) || empty($this->content)
             || empty($this->sign) || empty($this->template)){
                return false;
        }
        if(isset($this->mobile{11}) || !is_numeric($this->mobile)){
                return false;
        }
        $c = new TopClient;
        $c->appkey = $this->appkey;                     //应用编号
        $c->secretKey = $this->secret;                  //应用秘钥
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($this->sign);
        $req->setSmsParam($this->content);
        $req->setRecNum($this->mobile);
        $req->setSmsTemplateCode($this->template);
        $resp = $c->execute($req); 
        if(isset($resp->result) && $resp->result->err_code == 0){
            $this->code=$resp->result->err_code;
            $this->msg=$resp->result->model;
            return  true;
        }
        if(isset($resp->code)){
            $this->code=$resp->code;
            $this->msg=$resp->msg.'|'.$resp->sub_code.'|'.$resp->sub_msg;
            return  false;
        }
        $this->code='3000';
        $this->msg='阿里大鱼短信服务返回未知信息!';
        return false;
    }
    
    /**
     * 短信通知
     * @param    int        mobile      手机号码
     * @param    array      content     内容
     * @param    string     sign        短信签名
     * @param    string     template    短信模板
     * @return   成功返回true | 失败返回false
     */
    function SendNotice(){
        $c = new TopClient;
        $c->appkey = $this->appkey;                     //应用编号
        $c->secretKey = $this->secret;                  //应用秘钥
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($this->sign);
        $req->setSmsParam($this->content);
        $req->setRecNum($this->mobile);
        $req->setSmsTemplateCode($this->template);
        $resp = $c->execute($req);
        if(isset($resp->result) && $resp->result->err_code == 0){
            $this->code=$resp->result->err_code;
            $this->msg=$resp->result->model;
            return  true;
        }
        if(isset($resp->code)){
            $this->code=$resp->code;
            $this->msg=$resp->msg.'|'.$resp->sub_code.'|'.$resp->sub_msg;
            return  false;
        }
        $this->code='3000';
        $this->msg='阿里大鱼短信服务返回未知信息!';
        return false;
    }
    /**
     * 语言验证码
     * @param   string    appkey   应用key
     * @param   string    secret   应用秘钥
     * @param   int       mobile   手机号码
     * @param   string    content  短信内容
     * @param   int       shownum  呼叫号码
     * @param   string    extend   回传数据
     * @param   string    templte  短信模板id
     * @return  发送成功返回  true 短信id | 发送失败发送 false 以及原因
     * 您正在注册成为${product}用户，验证码${code}，感谢您的支持！
     */
    function SendVoiceCode(){
        if(empty($this->appkey) || empty($this->secret)){            
            return false;
        }
        if(empty($this->mobile) || !is_numeric($this->mobile)){
            $this->msg='手机号码必填或格式不正确!';
            return false;
        }
        if(empty($this->content)){
            $this->msg='短信内容为空!';
            return false;
        }
        if(empty($this->shownum) || !is_numeric($this->shownum)){
            $this->msg='呼叫号码必填或格式不正确!';
            return false;
        }        
        if(empty($this->templte)){
            $this->msg='短信模板是必填选项!';
            return false;
        }
        //调用阿里大鱼的 短信服务 SDK
        $c = new TopClient;
        $c->appkey = $this->appkey;                     //应用编号
        $c->secretKey = $this->secret;                  //应用秘钥
        $req = new AlibabaAliqinFcTtsNumSinglecallRequest;
        $req->setExtend($this->extend);                 //回传数据
        $req->setTtsParam($this->content);              //短息内容
        $req->setCalledNum($this->mobile);              //手机号码
        $req->setCalledShowNum($this->shownum);         //呼叫号码
        $req->setTtsCode($this->templte);               //短信模板id
        $resp = $c->execute($req);
        if(isset($resp->result) && $resp->result->err_code == 0){
            $this->code=$resp->result->err_code;
            $this->msg=$resp->result->model;
            return  true;
        }
        if(isset($resp->code)){
            $this->code=$resp->code;
            $this->msg=$resp->msg.'|'.$resp->sub_code.'|'.$resp->sub_msg;
            return  false;
        }
        $this->code='3000';
        $this->msg='阿里大鱼短信服务返回未知信息!';
        return false;
    }
} 


