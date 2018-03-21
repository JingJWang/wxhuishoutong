<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Coop extends CI_Controller {
    
    //搜索订单
    function ViewSearch(){
        exit();
        //校验进来的是不是回收商
        $code=$this->input->get('code');
        if(empty($code)){
            exit('请重新进入!');
        }
        $this->load->database();
        $this->load->model('common/wxcode_model');
        $openid=$this->wxcode_model->getOpenid($code);
        $checkuser='select coop_openid,coop_mobile,coop_status from h_temp_coop where coop_openid="'.$openid.'"';
        $result=$this->db->query($checkuser);
        if( $result->num_rows == 0){
            $view['openid']=$openid;
            $this->load->view('cooperation/mobile',$view);
            return '';
        }
        $coopinfo=$result->result_array();        
        if($coopinfo['0']['coop_status']  == '-1' ){
            exit();
        }
        if($coopinfo['0']['coop_status']  == 0){
            $msg['messageinfo']='请等待管理员审核!';
            $this->load->view('exception/busy',$msg);
            return '';
        }
        
        $_SESSION['coopinfo']['openid']=$coopinfo['0']['coop_openid'];
        $_SESSION['coopinfo']['mobile']=$coopinfo['0']['coop_mobile'];
        $this->load->view('cooperation/search');
    }
    //临时回收商 注册
    function RegCoop(){
        exit();
        $this->load->helper('array');
        $coulms=array('mobile','code','openid');
        $request=elements($coulms, $this->input->post(), '');
        $this->load->model('nonstandard/msg_model');
        $response=$this->msg_model->check_code($request['mobile'],$request['code']);
        if(array_key_exists('status', $response)){
            echo json_encode($response);
            exit();
        }
        $data=array(
                'coop_openid'=>$request['openid'],
                'coop_mobile'=>$request['mobile'],
                'coop_jointime'=>date('Y-m-d H:i:s')
        );
        $this->db->insert('h_temp_coop',$data);
        $this->msg_model->edit_msgstatus($response['0']['code_id'],'-1');
        $result=array('status'=>1000,'msg'=>'请等待审核!');
        echo json_encode($result);
        exit();
    }
    
    //根据手机号码 查询订单
    function SearchOrder(){
        exit();
        $this->load->helper('array');
        $coulms=array('mobile');
        $request=elements($coulms, $this->input->post(), '');
        //校验手机号码是否为空  格式是否正确
        if(empty($request['mobile']) || !is_numeric($request['mobile'])){
            $response=array('status'=>$this->config->item('request_fall'),'msg'=>'手机号码为空或者手机号码格式不正确!');
             echo json_encode($response);exit;
        }
        //验证用户是否绑定手机号码
        $this->load->database();
        $user_sql='select wx_id from h_wxuser where wx_status=1 and wx_mobile="'.$request['mobile'].'"';
        $user_query=$this->db->query($user_sql);
        if($user_query->num_rows() === 0){
            $response=array('status'=>$this->config->item('request_fall'),'msg'=>'该用户还没绑定手机号码,请先提示用户绑定手机号码!');
             echo json_encode($response);exit;
        }
        //获取用户id
        $userinfo=$user_query->result_array();//$userinfo['0']['wx_id'];
        //查询订单
         $order_sql='select a.order_name as name ,a.order_number as number
                    from h_order_nonstandard as a left join h_order_content as b on a.order_id=
                    b.order_id where a.wx_id='.$userinfo['0']['wx_id'].' and  a.order_orderstatus=1 
                    and  a.order_status=1 ';
        $order_query=$this->db->query($order_sql);
        if($order_query->num_rows() === 0){
            $response=array('status'=>$this->config->item('request_fall'),'msg'=>'没有找到该用户符合的订单!');
            echo json_encode($response);exit;
        }
        //获取所有类型的订单
        $orderinfo=$order_query->result_array();
        $response=array('status'=>$this->config->item('request_succ'),'msg'=>'','url'=>'','data'=>$orderinfo);
        echo json_encode($response);exit;
    }
    /*
     *  查看订单详细信息 
     */
    function ViewOrdre(){
        exit();
        $this->load->helper('array');
        $coulms=array('id');
        $request=elements($coulms, $this->input->get(), '');
        //校验传递的订单编号
        if(empty($request['id']) || !is_numeric($request['id']) ){
            exit('没有获取到订单号 或者 订单号格式不正确!');
        }
        $this->load->database();
        $ordersql=' select a.order_name,a.order_number,a.order_ctype,b.electronic_oather,
                    c.wx_name,c.wx_openid,c.wx_mobile  from h_order_nonstandard as a left 
                    join h_order_content as b on a.order_id= b.order_id left join  
                    h_wxuser as c on a.wx_id=c.wx_id  where a.order_number='.$request['id'].' 
                    and  a.order_orderstatus=1  and  a.order_status=1 ';
        $order_query=$this->db->query($ordersql);
        if($order_query->num_rows() === 0){
           exit('没有获取到该订单的详细信息!');
        }
        //获取所有类型的订单
        $view['orderinfo']=$order_query->result_array();
        $view['jspay']='/wxpay/JsPay.php';
        $attr=$this->config->item('electronic_attribute_key');
        $view['attr']=$attr[$view['orderinfo']['0']['order_ctype']];
        $this->load->view('cooperation/orderinfo',$view);
    }
    
    
   
}