<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Shareorder_model extends CI_Model {
    
    private $ordervoucher='h_voucher_shareorder';
    /*
     * 功能描述:校验是否已经领取过每周分享卷
     */
    public function check_weekshare($id){
        $sql='select order_ascription,order_number from '.$this->ordervoucher.'  where order_id='.$id;
        $arr_data=$this->db->customize_query($sql);
        return $arr_data;
    }
    /*
     * 功能描述:更新剩余名额 并且添加 领取记录
     */
    public function update_ordershare($openid,$id){
        $sql='update '.$this->ordervoucher.' set order_number=order_number+1,order_ascription=concat(order_ascription,"'.$openid.',") where order_id='.$id;
        $arr_data=$this->db->query($sql);
        if($arr_data){
            return true;
        }else{
            return false;
        }
    }
    
    
}