<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Wxorder_model extends CI_Model {
    
    private  $order='h_order';//订单表
    
    /*
     *功能描述:根据openid 查询订单 
     */
    public function  getlastorder($openid){        
        $sql='select weixin_id,order_mobile,order_province,order_city,order_county,order_address from '.
        $this->order.' where weixin_id="'.$openid.'"';
        $data=$this->db->customize_query($sql);
        return $data;
    }
    /**
     * @description 生成订单编号
     * @return int 16位数字
     */
    function ordernumber(){
            return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
    /*
     * 功能描述:添加订单
     */    
    public function  addorder(){
        $order_data['weixin_id']=$this->input->post('openid',TRUE);/*微信id*/ 
        $order_data['order_mobile']=$this->input->post('phone',TRUE);/*订单 手机*/
        $order_data['order_province']=$this->input->post('province',TRUE);/*订单 省份*/ 
        $order_data['order_city']=$this->input->post('city',TRUE); /*订单 市区*/
        $order_data['order_county']=$this->input->post('county',TRUE); /*订单 县*/ 
        $order_data['order_address']=$this->input->post('address',TRUE);/*订单  地址*/
        $order_data['order_num']=$this->input->post('order_num',TRUE);/*订单 数量*/
        $order_data['order_type']=$this->input->post('order_type',TRUE);/*订单 数量*/
        $order_data['order_randid']=$this->ordernumber();//订单编号
        $order_data['order_joindate']=date("Y-m-d H:i:s");
        //判断所提交的内容不为空
        $res=$this->db->insert($this->order,$order_data);
        if(!$res){
                return false;
            }else{
                $this->load->model('weixin/wxvoucher_model');
                $info=$this->wxvoucher_model->one_submit_order(2,$this->input->post('openid'),TRUE);
                if($info !== false){
                    return $info;
                }else{
                    return false;
                }
            }
        
    }
    /*
     * 功能描述:根据openid查询所有未删除的订单、
     */
    public function get_orderlist($weixin_id){
        $sql0='select * from h_order where order_status=1 and  weixin_id="'.$weixin_id.'" order by order_joindate desc';
        $odrer_status1=$this->db->customize_query($sql0);
        $sql1='select * from h_order where order_status=0 and  weixin_id="'.$weixin_id.'" order by order_joindate desc';
        $odrer_status0=$this->db->customize_query($sql1);
        if($odrer_status1 == '0' && $odrer_status0 == '0'){
            return false;
        }else{
            $data['deal']=$odrer_status0;
            $data['nodeal']=$odrer_status1;
            return $data;
        }
        	
    }
    /*
     * 功能描述:查看订单
     */
    public function getorderinfo($id){
        $ordersql='select * from h_order where id='.$id;
        $res_order=$this->db->customize_query($ordersql);
        if($res_order !== false || $res_order !='0'){
             $orderdata['order']=$res_order;             
        }else{
            return  false;
        }
        if($res_order['0']['voucher_id'] != ''){
            $vouchersql='select * from h_voucher_log where id in('.$res_order['0']['voucher_id'].')';
            $res_voucher=$this->db->customize_query($vouchersql);
            if($res_order !==false || $res_voucher != '0'){
                $orderdata['voucher']=$res_voucher;
                return $orderdata;
            }else{
                return false;
            }
        }else{
            return $orderdata;
        }
    }
   /*
    * 功能描述:查看是否存在 订单分享记录  不存在 添加记录返回id  存在返回ID
    */
    public function checkshareorder($id){
        $sql='select order_id from h_voucher_shareorder where order_shareid='.$id;
        $shareorder=$this->db->customize_query($sql);
        if($shareorder !== false){
            if($shareorder== '0'){
                $addsql='insert into h_voucher_shareorder(order_shareid,order_jointime)values('.$id.',"'.date('Y-m-d H:i:s').'")';
                $res_add=$this->db->query($addsql);
                if($res_add){
                    return $this->db->insert_id();
                }else{
                    return false;
                }
            }else{
                return $shareorder['0']['order_id'];
            }
        }else{
            return false;
        }
    }
}
/* End of file wxorder_model.php */
/* Location: ./controllers/model/weixin/wxorder_model.php */