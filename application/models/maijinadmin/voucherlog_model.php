<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 *@author m
 *@description 现金卷领取记录
 */
class voucherlog_model extends CI_Model {
        
       private  $voucher_log ='h_voucher_log'; //现金卷发送日志表
    
       private  $wxuser      ='h_wxuser';    //y用户表
       
       /**
        * @description 现金券列表
        * @param int $option['page'] 当前页
        * @param int $option['pagenumber'] 每页数量
        * @return array $data['number'] 总记录数 $option['list'] 记录内容
        */
       function voucher_list($option){
           $number='select a.log_joindate,a.log_lastdate,a.log_type,a.voucher_pic,a.log_exceed,a.log_voucher_status from
                    h_voucher_log a left join  h_wxuser b on a.openid=b.wx_openid  order by a.log_joindate desc   limit 0,30';
           $number=$this->db->query($number);
           if(!$number){
               return false;
           }
           if($number->num_rows == '0'){
               return -1;
           }
           $data['number']=$number->num_rows;
           $contentsql='select a.log_joindate,a.log_lastdate,a.log_type,a.voucher_pic,a.log_exceed,a.log_voucher_status from
                        h_voucher_log a left join  h_wxuser b on a.openid=b.wx_openid  order by a.log_joindate desc 
                        limit '.$option['page'].','.$option['pagenumber'];
           $data['list']=$this->db->customize_query($contentsql);
           if($data['list'] === false){
               return false;
           }else{
               return $data;
           }
       }
       /**
        * @description 搜索现金卷
        */
       function searchvoucher($option){
           $number='select a.log_joindate,a.log_lastdate,a.log_type,a.voucher_pic,a.log_exceed,a.log_voucher_status,a.user_id from
                    h_voucher_log a left join  h_wxuser b on a.openid=b.wx_openid  where (a.openid="'.$option['keyword'].'" 
                    or a.user_id="'.$option['keyword'].'" )  order by a.log_joindate desc';
           $number=$this->db->query($number);
           if(!$number){
               return false;
           }
           if($number->num_rows == '0'){
               return -1;
           }
           $data['number']=$number->num_rows;
           $contentsql='select a.log_joindate,a.log_lastdate,a.log_type,a.voucher_pic,a.log_exceed,a.log_voucher_status,a.user_id from
                        h_voucher_log a left join  h_wxuser b on a.openid=b.wx_openid where (a.openid="'.$option['keyword'].'" 
                        or a.user_id="'.$option['keyword'].'")  order by a.log_joindate desc
                        limit '.$option['page'].','.$option['pagenumber'];
           $data['list']=$this->db->customize_query($contentsql);
           if($data['list'] === false){
               return false;
           }else{
               return $data;
           }
           
       }       
       
       /**
        * @description查询最近一周每天的数量
        */
       function voucherstatistics_week(){
           $sql='select date(log_joindate) as joindate, count(id) as num,sum(voucher_pic) as sum from '.$this->voucher_log.' where
	          DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(log_joindate) GROUP BY joindate';
           $data=$this->db->customize_query($sql);
           if($data === false){
               return false;
           }else{
               return $data;
           }
       }
       /**
        * @description 获取成交订单  未成交订单总数
        */
       function vouchertatistics_num(){
           $sql='select count(id) as num,log_type,sum(voucher_pic) sum from '.$this->voucher_log.' group by log_type';
           $data=$this->db->customize_query($sql);
           if($data === false){
               return false;
           }else{
               return $data;
           }
       }
       
}