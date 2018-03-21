<?php
/**
 * 微信支付模块
 */
header("Content-type: text/html; charset=utf-8");
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Account_model extends CI_Model {
    //非标准化订单
    private   $table_order_nonstandard = 'h_order_nonstandard'; 
    //支付记录表 
    private   $table_wxuser_payment    = 'h_wxuser_payment';
    /**
     * 微信订单支付对账  付款
     * @param   int   out_trade_no      商户订单编号
     * @param   int   ordernumber       订单编号
     * @param   int   coop_id           回收商编号
     * @return  bool                    支付结果
     */
    function orderQuery($option){
        switch ($option['method']){
            //微信
            case 1 : 
                $response=$this->wxQuery($option);
                break;
            //支付宝
            case 2 :
                break;
            //百度钱包    
            case 3 :
                
                break;
            //余额
            case 4 :
                
                break;
        }
        return $response;
    }
    /** 订单查账 -----微信
     * @param   int   out_trade_no      商户订单编号
     * @param   int   ordernumber       订单编号
     * @param   int   coop_id           订单编号
     * @return  bool                    支付结果
     */
    
    //报价  订单   记录成交金额     统计信息 增加1 
    function wxQuery($option){
        //校验商户支付订单编号
        if(!is_numeric($option['out_trade_no']) || !is_numeric($option['ordernumber'])){
            return false;
        }
        //加载微信sdk
        $this->load->library('wxsdk/lib/WxPayApi');
        $input = new WxPayOrderQuery();
        $input->SetOut_trade_no($option['out_trade_no']);
        $response=WxPayApi::orderQuery($input);
        //校验 请求状态  业务状态
        if($response['result_code'] == 'SUCCESS' && $response['return_code'] == 'SUCCESS'){
            $option['cash_fee']=100;//$response['result_code'];//支付金额
            return $this->WxPay($option);
        }else{
            //查账失败
            return array('status'=>8010,'msg'=>$response['return_msg']);
        }
    }
    /**
     * 微信支付   支付给用户订单费用
     * @param    int   ordernumber   订单编号
     * @param    int   cash_fee      支付金额
     * @return   bool                结果 
     */
    function WxPay($pay){
        //给用户支付 订单费用
        $this->load->library('hongbao/packet');
        //根据订单编号查询用户openid
        $this->load->database();
        $openid=$this->OrderInfo($pay['ordernumber']);
        if($openid == false){
            exit();
        } 
        //支付订单费用
        $payinfo=$this->packet->_route('transfers',array('openid'=>$openid,'money'=>$pay['cash_fee']));
        //支付成功
        if($payinfo->result_code == 'SUCCESS' && $payinfo->return_code == 'SUCCESS'){
                $log=array(
                        'cooperator_number'=>$pay['coop_id'],
                        'order_number'=>$pay['ordernumber'],
                        'payment_sums'=>$pay['cash_fee'],
                        'payment_number'=>$payinfo->partner_trade_no,
                        'payment_openid'=>$openid
                );
                $res_log=$this->Paylog($log);
                return array('status'=>8080,'msg'=>$this->lang->line('common_orderpay_succ'));
        }else{
            return array('status'=>8020,'msg'=>$payinfo->err_code_des);
        }
    }
    /**
     * 订单支付---查询订单信息
     * @param    int    ordernumber  订单编号
     */
    function  OrderInfo($ordernumber){
        $sql='select wx_openid as openid  from '.$this->table_order_nonstandard.' where order_number="'.$ordernumber.'"';
        $query=$this->db->query($sql);
        if($query->num_rows() == 1){
            $orderinfo=$query->result_array();
            return $orderinfo['0']['openid'];
        }
        return false;
    }
    /**
     * 订单支付---记录支付信息
     */
    function  Paylog($log){
        //记录支付信息
        $data=array(
                'cooperator_number'=>$log['cooperator_number'],
                'order_number'=>$log['order_number'],
                'payment_sums'=>$log['payment_sums'],
                'payment_number'=>$log['payment_number'],
                'payment_openid'=>$log['payment_openid'],
                'payment_jointime'=>time(),
                'payment_status'=>1,
        );
        //插入支付记录
        $query=$this->db->insert($this->table_wxuser_payment,$data);
        if($query){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 微信支付生成 统一下单信息
     * @param    string   name    订单名称
     * @param    string   attch   商家数据包 (填写订单编号)
     * @param    int      pri     金额
     * @param    
     */
    function  GetOrderInfo($order){
        $this->load->library('wxsdk/lib/WxPayApi');;
        $this->load->library('wxsdk/unit/log');
        //初始化日志
        $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'wxpay.log');
        $log = Log::Init($logHandler, 15);
        //统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($order['name']);
        $input->SetAttach($order['attch']);
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($order['pri']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis",time() + 600));
        $input->SetNotify_url("http://test.recytl.com/index.php/callback/callback/WxPayCall");
        $input->SetTrade_type("APP");
        try {
            //获取统一下单信息
            $orderinfo = WxPayApi::unifiedOrder($input);
            return  array('status'=>8070,'data'=>$orderinfo);
        } catch (Exception $e) {
             return  array('status'=>8030,'msg'=>$e);
        }
    }
    
}