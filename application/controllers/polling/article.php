<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Article extends CI_Controller {
    /**
     * 定时任务 每天下午一点刷新知识库查看是否有文章
     * 更新知识库
     */
    function articleRefresh(){
         //校验请求的地址
        /*  $serverIp='182.92.214.25';
         $requestIp=$this->input->ip_address();
          if($serverIp != $requestIp){
            exit;
         }  */
         $this->load->model('polling/article_model');
         //查询超过24小时没有进入交易状态的订单
         $orderid=$this->article_model->getArticleInfo();
         var_dump($orderid);exit;
         //更新订单状态为等待提交  更新对应报价为失效
         $this->order_model->orderid=$orderid;
         $orderid=$this->order_model->editOrder();
    }
}