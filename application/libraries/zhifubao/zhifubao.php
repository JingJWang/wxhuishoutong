<?php
header("Content-type:text/html;charset=utf-8");
require 'AopSdk.php';
class zhifubao {
    /**
     * 支付宝mobile端支付方法
     * @param int     $out_trade_no   订单号 唯一 必填
     * @param string  $subject    订单名称 必填
     * @param int     $total_amount  金额
     * @param string  $body      描述可空
     * @param string  $timeout_express 超时时间        
     */
    function  pay(){        
        if(empty($this->config)){
            return  false;
        }
        $config=$this->config;
        if(!is_numeric($this->out_trade_no)){
            return false;
        }
        if(empty($this->total_amount)){
            return false;
        }
        if(empty($this->subject)){
            return false;
        }
        if(empty($this->timeout_express)){
            return  false;
        }
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKeyFilePath = $config['merchant_private_key'];
        $aop->alipayPublicKey=$config['alipay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayTradeWapPayRequest ();
        $request->setNotifyUrl($config['notify_url']);
        $request->setReturnUrl($config['return_url']);
        $request->setBizContent("{" .
                "    \"body\":\"".$this->body."\"," .
                "    \"subject\":\"".$this->subject."\"," .
                "    \"out_trade_no\":\"".$this->out_trade_no."\"," .
                "    \"timeout_express\":\"1m\"," .
                "    \"total_amount\":".$this->total_amount."," .
                "    \"product_code\":\"QUICK_WAP_PAY\"" .
                "  }");
        $result = $aop->pageExecute ($request);
        if(isset($this->return) && $this->return == 1){
            return  $result;
        }
        echo $result;
        
        
    }
    /**
     * 校验支付宝的公钥
     * @param  string  $sign  支付宝传递签名 
     */
    function checksign(){
        include 'wappay/service/AlipayTradeService.php';
        if(empty($this->config)){
            return  false;
        }
        if(empty($this->sign)){
            return  false;
        }
        $AlipayTradeService = new AlipayTradeService($this->config);
        $AlipayTradeService->alipay_public_key=$this->config['alipay_public_key'];
        $res=$AlipayTradeService->check($this->sign);
        return $res;
    }
    /**
     * 查询订单支付状态
     * @param   int    $out_trade_no 商户订单编号
     * @param   int    $trade_no 支付宝订单号
     * @param   array  支付宝配置文件
     * @return  查询结果 array
     */
    function queryPay(){        
        //商户订单号和支付宝交易号不能同时为空。 trade_no、  out_trade_no如果同时存在优先取trade_no
        if(empty($this->out_trade_no) && empty($this->trade_no)){
            return false;
        }
        $config=$this->config;
        //商户订单号，和支付宝交易号二选一        
        $out_trade_no = trim($this->out_trade_no);        
        //支付宝交易号，和商户订单号二选一
        $trade_no = trim($this->trade_no); 
        include 'aop/AopClient.php';        
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKeyFilePath = $config['merchant_private_key']; 
        $aop->alipayPublicKey=$config['alipay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayTradeQueryRequest ();
        $request->setBizContent("{" .
                "    \"out_trade_no\":\"".$out_trade_no."\"," .
                "    \"trade_no\":\"".$trade_no."\"" .
                "  }");
        $result = $aop->execute ($request);
        return $result;
    }
    /**
     * 转账接口
     * @param   int         $out_trade_no   商户订单编号
     * @param   string      $userinfo       支付宝账户
     * @param   int         $total_amount   转账金额
     * @param   string      $userRealname   支付宝真实姓名
     * @return  查询结果 array
     */
    function transfer(){
        if(empty($this->config)){
            return  false;
        }
        $config=$this->config;
        include 'aop/AopClient.php';
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $config['app_id'];
        $aop->rsaPrivateKeyFilePath = $config['merchant_private_key'];
        $aop->alipayPublicKey=$config['alipay_public_key'];
        $aop->apiVersion = '1.0';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayFundTransToaccountTransferRequest ();
        $request->setBizContent("{" .
            "\"out_biz_no\":\"".$this->out_trade_no."\"," .
            "\"payee_type\":\"ALIPAY_LOGONID\"," .
            "\"payee_account\":\"".$this->userinfo."\"," .
            "\"amount\":\"".$this->total_amount."\"," .
            "\"payer_real_name\":\"北京知通科技有限公司\"," .
            "\"payer_show_name\":\"北京知通科技有限公司转账\"," .
            "\"payee_real_name\":\"".$this->userRealname."\"," .
            "\"remark\":\"用户提现\"," .
            "\"ext_param\":\"{\\\"order_title\\\":\\\"知通科技提现\\\"}\"" .
        "}");
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        return $result->$responseNode;
    }
}