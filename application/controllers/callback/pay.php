<?php
/**
 * 第三方回调模块
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Pay extends CI_Controller {    
    /**
     * 微信支付 支付结果回调处理
     */
    function  WxPayCall(){
        //接受传递数据
      
        //验证码是否存在相同的订单信息
        
        file_put_contents('wxjspay.log',var_export($_REQUEST),FILE_APPEND);
        //保存订单支付信息
       
        //请求微信服务器 校验当前的订单支付结果是否合法
        
        //处理当前的订单  验证订单的状态 操作 用户的余额 
        
        //通知用户 和回收商 订单成交详情
        echo  '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }
    /**
     * 查询订单是否支付成功 并系统记录收入 同时更新用户订单状态  减去对应的通花   与 index.php/shop/integral/queryOrder 逻辑一样
     * @param   int  number   订单编号
     * return   成功返回 json 跳转地址  | 失败 返回原因
     */
    function zfbCall(){
        $str = '';        
        foreach ($_POST as $k => $v) {
            $str .= $k.':'.$v."; ";
        }
        file_put_contents('callback/zhifubao.log',$str."\r\n",FILE_APPEND);
        $number=$_POST['out_trade_no'];
        if(empty($number) || !is_numeric($number)){
            return 'FAILL';
            exit();
        }
        //校验当亲订单是否合法
        $this->load->model('shop/goods_model');
        $this->goods_model->number=$number;
        $record=$this->goods_model->getRecord();
        if(!$record){
            return 'FAILL';
            exit();
        }
        $this->goods_model->extendnum = '';
        if($this->goods_model->price>0&&$this->goods_model->divide>0){//查看是否有邀请码
            $this->load->model('task/otherget_model');
            $this->goods_model->extendnum = $this->otherget_model->shopgetextend($this->goods_model->goods_id,$this->goods_model->userid);
            if ($this->goods_model->extendnum==false) {
                $this->goods_model->extendnum = '';
            }
        }
        $res=$this->goods_model->transaction();
        if($res){
            if ($this->goods_model->extendnum != '') {
                $this->goods_model->sendNotice();
            }
            return 'SUCCESS';
            exit();
        }else{
            return 'FAILL';
            exit();
        }
    }
}