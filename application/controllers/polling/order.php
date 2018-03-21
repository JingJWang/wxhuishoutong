<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Order extends CI_Controller {
    
    /**
     * 定时任务 间隔20秒
     * 新的订单 通知符合条件的回收商
     */
    function  orderNotice(){     
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $info=$this->zredis->_redis->lpop('orderlist');
        if(!$info){
            Universal::SystemLog('orderNotice.log',date('Y-m-d H:i:s').'当前没有等待通知的订单'."\r\n");
            exit;
        }
        //获取当前所有正常状态的回收商        
        $arr_order=explode(',',$info);       
        $this->load->model('polling/order_model');
        $coop=$this->order_model->getCoopInfo(); 
        //筛选出每个符合条件的回收商
        $notice=array();
        $this->load->library('common/Location');
        foreach ($coop as $v){
             $distance=Location::getDistance($arr_order['3'],$arr_order['2'],$v['cooperator_lat'],$v['cooperator_lng']);
             $distance/100 < $v['cooperator_distance'] ? $notice[]=$v['cooperator_number'] : '';
        }  
        //发送通知给符合条件的回收商
        if(is_array($notice) && !empty($notice)){
            $this->load->library('vendor/notice');
            $response=$this->notice->JPush('voice',$notice,'您有新的订单,请及时处理',array("voice"=>"1", "content"=>"21"));            
            $content=date('Y-m-d H:i:s').'|sendno:'.$response->data->sendno.'|msg_id:'.$response->data->msg_id."\r\n";
            Universal::SystemLog('orderNotice.log',$content);
            exit;
        } 
    } 
    /**
     * 定时任务  间隔5分钟
     * 更新订单状态    
     * 1.订单提交24小时后 没有报价  订单状态更新为等待提交
     * 2.报价12小时后  订单状态更新为等待提交
     * 3.订单处在待交易状态  超过24小时   更新订单状态为等待提交
     */
    function orderRefresh(){
         //校验请求的地址
         $serverIp='182.92.214.25';
         $requestIp=$this->input->ip_address();
        /*  if($serverIp != $requestIp){
            exit;
         } */
         $this->load->model('polling/order_model');
         //查询超过24小时没有进入交易状态的订单
         $orderid=$this->order_model->getOrderList();
         
         //更新订单状态为等待提交  更新对应报价为失效
         $this->order_model->orderid=$orderid;
         $orderid=$this->order_model->editOrder();
    }
    
}