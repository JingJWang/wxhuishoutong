<?php
/*
 *  Copyright (c) 2014 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.yuntongxun.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */


include("CCPRestSmsSDK.php");

class Shortmsg extends CI_Model{

        //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        public $accountSid= '';
        
        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        public $accountToken= '';
        
        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
        //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        public $appId='';
        
        //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        public $serverIP='';        
        
        //请求端口，生产环境和沙盒环境一致
        public $serverPort='';
        
        //REST版本号，在官网文档REST介绍中获得。
        public $softVersion='';
        
        function __construct(){
            parent::__construct();
            // 初始化REST SDK
            $this->accountSid=$this->config->item('app_msg_accountsid');
            $this->accountToken=$this->config->item('app_msg_accounttoken');
            $this->appId=$this->config->item('app_msg_appid');
            $this->serverIP=$this->config->item('app_msg_serverip');
            $this->serverPort=$this->config->item('app_msg_serverport');
            $this->softVersion=$this->config->item('app_msg_softversion');
        }
        
        /**
          * 发送模板短信
          * @param to 手机号码集合,用英文逗号分开
          * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
          * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
          */       
        function sendTemplateSMS($to,$datas,$tempId){ 
             $rest = new REST($this->serverIP,$this->serverPort,$this->softVersion);
             $rest->setAccount($this->accountSid,$this->accountToken);
             $rest->setAppId($this->appId);            
             // 发送模板短信
             $result = $rest->sendTemplateSMS($to,$datas,$tempId);
             if($result == NULL ) {
                 return array('status'=>'error','info'=>'NULL');
             }
             
             if($result->statusCode!=0) {
                 return array('status'=>(String)$result->statusCode,'info'=>(String)$result->statusMsg);
             }else{
                 $smsmessage = $result->TemplateSMS;
                 return array('status'=>(String)$result->statusCode,'info'=>'','time'=>(String)$smsmessage->dateCreated,'sid'=>(String)$smsmessage->smsMessageSid);
             }
        }
        
        
}
        

