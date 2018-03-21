<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cooporder_model extends CI_Model {
    /*
     * 功能描述:查询订单
     */
    public  function order_list($type){
        $sql='select a.*,b.wx_name from h_order as a left join h_wxuser as b on a.weixin_id=wx_openid  where user_id='.
             $_SESSION['id'].' and order_status='.$type.' order by a.order_joindate desc';
        $orderlist=$this->db->customize_query($sql);
        return $orderlist;
    }
    /*
     * 功能描述:获去订单数量
     */
    public function  order_num(){
        $sql='select count(id) as num  from h_order where user_id='.$_SESSION['id'].' GROUP BY order_status';
        $orderlist=$this->db->customize_query($sql);
        return $orderlist;
    }
    /*
     * 获取修改订单的信息
     */
    public function  edituserinfo($id){
        $sql='select * from  h_order where id='.$id.' limit 1';
        $data['order']=$this->db->customize_query($sql);
        if($data['order'] !== false && $data['order'] !=''){
            //根据微信id查询用户表单
            $wx='select * from h_wxuser where wx_openid="'.$data['order']['0']['weixin_id'].'" limit 1';
            $wx_data=$this->db->customize_query($wx);
            if($wx_data !== false && $data!= '' ){
                //根据用户的代金券id  查询金额
                if(date('Y-m-d H:i:s') < $wx_data['0']['wx_freezedate']){
                    $option=' and (log_type=1 or log_type=2)';
                }else{
                    $option='';
                }
                $voucher_sql='select * from h_voucher_log where openid = "'.$data['order']['0']['weixin_id'].'" and log_voucher_status=1 and log_status=1 '.$option.' order by voucher_pic desc';
                $data['voucher']=$this->db->customize_query($voucher_sql);
                return $data;
            }else{
                return false;
            }     
        }else{
            return false;
        }
        
    }
    /*
     * 功能描述:保存订单最新更新的信息
     */
    public function update_order($data){
        //校验存在订单 且未成交状态        
        $checkordersql='select id from h_order where order_status=0 and  id='.$data['orderid'];
        $res_check=$this->db->customize_query($checkordersql);
        if($res_check === false || $res_check != '0'){
            return array('status'=>'0','info'=>$this->lang->line('order_null'));
        }else{
            //当存在现金券的时校验现金券是否已经被使用 存在
            $voucher_log_id=trim($data['voucher'],",");
            if(!empty($voucher_log_id)){
               $checkvoucher='select id from h_voucher_log where  log_voucher_status=1 and  id in('.$voucher_log_id.')';
                $res_checkvoucher=$this->db->customize_query($checkvoucher);
                if($res_checkvoucher === false || $res_checkvoucher == '0'){
                    return array('status'=>'0','info'=>$this->lang->line('voucher_null'));
                }
            }
        } 
        //存在订单 或者存在现金券的时候处理订单
        $sql='update h_order set voucher_id="'.$voucher_log_id.'",order_lastdate="'.date('Y-m-d H:i:s').'",order_pic="'.$data['prirce'].'",order_weight="'.$data['weight'].'",user_id='.$_SESSION['id'].',order_make="'.$data['make'].'",user_address="'.$_SESSION['address'].'",order_status=0 where id='.$data['orderid'];
        $res=$this->db->query($sql);
        if(!$res){
           return array('status'=>'0','info'=>$this->lang->line('order_null'));
        }else{
            //当存在现金券的时候 调用红包接口            
            if(!empty($voucher_log_id) && $_SESSION['loginok']='1001'){                
                //调用红包接口
                $res_sendhb=$this->send_hongbao($data);
                if($res_sendhb){
					//给用户发送通知信息
					$content='{ "touser":"'.$data['openid'].'","msgtype":"news","news":{"articles": [ { "title":"交易完成，点我在得红包~", "description":"您的交易已完成，如得到微信红包，2小时内领取，否则红包会跑哦~点击查看交易详情，快邀请小伙伴们一起参与吧~","url":"http://wx.recytl.com/index.php/weixin/order/lookorder/'.$data['orderid'].'", "picurl":""}]}}';
					$this->order_send_message($content);
                    //修改现金券为已经使用状态
                    return $this->cancel_voucher($data['orderid'],$voucher_log_id,$data['openid']);
                }else{
                   return array('status'=>'0','info'=>$this->lang->line('sendredbagfall'));
                }
                
            }
            //给用户发送通知信息
            $content='{ "touser":"'.$data['openid'].'","msgtype":"news","news":{"articles": [ { "title":"交易完成，点我在得红包~", "description":"您的交易已完成，如得到微信红包，2小时内领取，否则红包会跑哦~点击查看交易详情，快邀请小伙伴们一起参与吧~","url":"http://wx.recytl.com/index.php/weixin/order/lookorder/'.$data['orderid'].'", "picurl":""}]}}';
            $this->order_send_message($content);
            return true;
        }
    }
    //更改代金券的状态
    private function cancel_voucher($odrerid,$id,$openid){
        $sql='update h_voucher_log set order_id='.$odrerid.',user_id='.$_SESSION['id'].',log_address="'.$_SESSION['address'].'",log_voucher_status=2  where id in('.$id.')';
        $res=$this->db->Query($sql);
        if($res){
            return $this->freeze_voucher($openid);
        }else{
            return array('status'=>'0','info'=>$this->lang->line('updatevoucherstatus'));
        }
       
    }
    /*
     * 功能描述:发送红包并记录红包的状态 和订单号
     * 现金券的状态
     */
    private function send_hongbao($data){
        if(empty($data['voucher'])){
            return $message=array('status'=>1,'info'=>'现金券不可为空');
        }else{
            $voucher_log_id=trim($data['voucher'],",");
            $sql='select id,openid,voucher_pic from h_voucher_log where id in('.$voucher_log_id.')';
        }
        $res_vooucher=$this->db->customize_query($sql);
        //执行成功并且结果不为空 执行
        if($res_vooucher && $res_vooucher != '0'){           
            $pic=0;
            foreach ($res_vooucher as $voucher){
                $pic+=$voucher['voucher_pic'];
            }
            //发送红包
            if(isset($_SESSION['pay_type']) && $_SESSION['pay_type']==1){
                //$this->packet->some_function(); 
                $this->load->library('hongbao/packet');
                $result=$this->packet->_route('wxpacket',array('openid'=>$voucher['openid'],'money'=>$pic*100));
                $result->return_code=='SUCCESS'? $send_listid= $result->send_listid : $send_listid=$result->return_msg;
                //记录发送日志
                $sendlogsql='insert into h_redbag_sendlog(send_order_id,send_money,send_openid,send_userid,
                             send_return_code,send_return_msg,send_result_code,send_err_code,send_err_code_des,
                             send_re_openid,send_total_amount,send_send_time,send_send_listid,send_jiontime)values(
                             "'.$data['orderid'].'","'.$pic.'","'.$voucher['openid'].'",'.$_SESSION['id'].',"'.$result->return_code.'"
                             ,"'.$result->return_msg.'","'.$result->result_code.'",0,0,"'.$result->re_openid.'","'.$result->total_amount.'"
                              ,"'.$result->send_time.'","'.$result->send_listid.'","'.date('Y-m-d H:i:s').'")';
                $res_addlog=$this->db->query($sendlogsql); 
                $vouchersql='update h_voucher_log set log_return_code="'.$result->return_code.'", log_send_listid="'.$send_listid.
                '" where id in ('.$voucher_log_id.')';
                $voucherres=$this->db->query($vouchersql);
                if($res_addlog && $voucherres ){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    /*
     *冻结改用户下代金券
     */
    private function freeze_voucher($openid){
        if($openid!=''){
            $freezedate=mktime('23','59','59',date('m'),date('d')+$this->config->item('FREEZEDAY'),date('Y'));//冻结时间
            $sql='update h_wxuser set wx_freezedate="'.date('Y-m-d H:i:s',$freezedate).'" where wx_openid="'.$openid.'"';
        }
        $res=$this->db->query($sql);
        if($res){			
            return true;
        }else{
            return false;
        }       
    }
    /*
     *给某一用户发送信息通知
     *$openid  用户openid  $content
     */
    private function order_send_message($content){
        if(empty($content)){
           return $message=array('status'=>'0','info'=>$this->lang->line('option_notnull'));
        }else{
           $this->load->model('common/wxcode_model');
           return  $message=$this->wxcode_model->sendmessage($content);
        }       
    }

}