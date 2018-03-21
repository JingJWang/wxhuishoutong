<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sharevoucher_model extends CI_Model {
    
    private $weekvoucher='h_voucher_shareweek';
    
    /*
     * 功能描述:生成每周分享卷
     */
    public function addshareweek($type,$openid){
        $nowdate=date('Y-m-d H:i:s');
        $nowweek=7-date('w');
        $nowtime=strtotime("+$nowweek day");
        $expiredate=date('Y-m-d 23:59:59',$nowtime);
        $selsql='select week_id from '.$this->weekvoucher.' where week_openid="'.$openid.
                '" and week_type='.$type.' and week_expiredate >= "'.$nowdate.'"';
        $arr_id=$this->db->customize_query($selsql);
        if($arr_id !== false && $arr_id == '0'){
            $insertsql='insert into '.$this->weekvoucher.'(week_openid,week_type,week_expiredate,week_jointime)values(
            "'.$openid.'",'.$type.',"'.$expiredate.'","'.$nowdate.'")';
            $addres=$this->db->query($insertsql);
            if($addres){
                return $this->db->insert_id();
            }else{
                return false;
            }
        }else{
           return $arr_id['0']['week_id'];
        }
    }
    /*
     * 功能描述:校验是否已经领取过每周分享卷
     */
    public function check_weekshare($id){
        $sql='select week_ascription,week_number from '.$this->weekvoucher.'  where week_id='.$id;
        $arr_data=$this->db->customize_query($sql);
        return $arr_data;
    }
    /*
     * 功能描述:更新剩余名额 并且添加 领取记录
     */
    public function update_weekshare($openid,$id){
        $sql='update '.$this->weekvoucher.' set week_number=week_number+1,week_ascription=concat(week_ascription,"'.$openid.',") where week_id='.$id;
        $arr_data=$this->db->query($sql);
        if($arr_data){
            return true;
        }else{
            return false;
        }
    }
    
}