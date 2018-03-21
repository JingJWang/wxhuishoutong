<?php
class OrderModel extends MySQL{
    //修改订单 到现场管理员名下
    public function update_order_user($openid,$uerid){
        if($openid != '' && $uerid != ''){
            $sql='update h_order set user_id="'.$uerid.'" where weixin_id="'.$openid.'" and order_status=1';
            if(!$this->Query($sql)){
                return FALSE; 
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }
    
}