<?php
/**
 * 支付回调模块
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');
class Callback extends CI_Controller {
    
    /**
     * 微信支付----sdk支付回调接口
     * @param    string    string  传递内容
     * @param    strng     签名
     */
    function  WxSdKNotice(){
        //获取回调传递的数据
        $string=$GLOBALS['HTTP_RAW_POST_DATA'];
        //解析数据为xml 对象
        $str_recharge=simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        //json 编码为数组
        $arr_recharge=json_decode(json_encode($str_recharge),true);
        //开发中记录请求日志
        $logcontent='请求时间'.date('Y-m-d H:i:s').'请求IP'.$_SERVER['REMOTE_ADDR'].'请求方式'.$_SERVER['REQUEST_METHOD'].
        '请求地址'.$_SERVER['REQUEST_URI']."\r\n".var_export($arr_recharge,true)."\r\n";
        $this->PayLog($this->config->item('log_wxpay_filename'),$logcontent);
        //通信标示
        if($arr_recharge['return_code'] == 'SUCCESS'){
            $this->load->database();
            //查看此笔订单是否存在
            $checksql='select recharge_id  from  h_cooperator_recharge  where 
                       recharge_attach="'.$arr_recharge['attach'].'"';
            //执行
            $query=$this->db->query($checksql);
            if($query->num_rows() > 0){
                echo $arr_recharge['sign'];exit;
            }
            //添加返回信息记录
            $data=array(
                    'recharge_return_code'=>$arr_recharge['return_code'],//返回状态码
                    'recharge_return_msg'=>$arr_recharge['return_msg'],//返回信息
                    'recharge_device_info'=>$arr_recharge['device_info'],//设备号
                    'recharge_result_code'=>$arr_recharge['result_code'],//业务结果
                    'recharge_err_code'=>$arr_recharge['err_code'],//错误返回的信息描述
                    'recharge_err_code_des'=>$arr_recharge['err_code_des'],//错误返回的信息描述
                    'recharge_user'=>$arr_recharge['openid'],//用户标示
                    'recharge_is_subscribe'=>$arr_recharge['is_subscribe'],//用户是否关注公众号
                    'recharge_trade_type'=>$arr_recharge['trade_type'],//交易类型
                    'recharge_bank_type'=>$arr_recharge['bank_type'],//付款银行
                    'recharge_total_fee'=>$arr_recharge['total_fee'],//总金额
                    'recharge_transaction_id'=>$arr_recharge['transaction_id'],//微信支付订单号
                    'recharge_out_trade_no'=>$arr_recharge['out_trade_no'],//商户订单号
                    'recharge_attach'=>$arr_recharge['attach'],//商家数据包
                    'recharge_time_end'=>$arr_recharge['time_end']//支付完成时间
            );
            //添加记录
            $res=$this->db->insert('h_account_recharge',$data);
            if($res){
                echo $recharge->sign;exit;
            }
        }
    }
    function test(){
        $this->load->model('account/account_model');
        /* $query=array(
                     'method'=>1, //支付方式
                     'out_trade_no'=>'123989010220151109151240', 商户流水号
                     'ordernumber'=>'20151116495756504227',//订单编号
                     'coop_id'=>'1447299307'//回收商编号
                
        );
        $info=$this->account_model->orderQuery($query);
        var_dump($info); */
        /* $order=array(
                'name'=>'三星 GALAXYS3',  //订单名称
                'attch'=>'20151116495756504226',//订单编号
                'pri'=>1,//支付金额
        );
        $info=$this->account_model->GetOrderInfo($order);
        var_dump($info); */
    }
    
    /**
     * 日志记录
     * @param   string     filename  日志名称
     * @param   string     content   日志内容
     */
    function  PayLog($filename,$content){
          file_put_contents($filename,$content,FILE_APPEND);
    }
}