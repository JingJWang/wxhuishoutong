<?php
/*
 * 订单模块
 * function cancel_order   订单模块-取消订单
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class Order extends CI_Controller {
    
    /**
     * 订单模块-显示取消订单页面
     */
    function Viewcancel(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('oid');
        $reponse=elements($coulms, $this->input->get(), '');
        $view['orderid']=$reponse['oid'];
        $view['cancel']=$this->config->item('user_cancel_order');
        $this->load->view('nonstandard/ordercancel',$view);
    }
    /**
     * 我的订单 -- 查询当前订单的 状态
     */
    function GetOrderStatus() {
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('oid');
        $request=elements($coulms, $this->input->post(), '');
        //校验传递的参数是否
        if(empty($request['oid'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optionnull'));
            echo json_encode($response);exit;
        }
        //校验是否存在于该参数
        if(!is_numeric($request['oid'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optiontypenull'));
            echo json_encode($response);exit;
        }
        $this->load->model('nonstandard/order_model');
        $res=$this->order_model->GetOrderStatus('trading',$request['oid']);
        if($res){
            $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>'', 'url'=>$this->config->item('url_cancelorder_succ').'?status=e',
                    'data'=>'');
            echo  json_encode($response);exit;
        }
        $response=array('status'=>$this->config->item('request_fall'),
                'msg'=>$this->lang->line('ordertrading_fall'),
                'url'=>$this->config->item('url_cancelorder_succ').'?status=s',
                'data'=>'');
        echo  json_encode($response);exit;
    }    
    /**
     * 订单结果--取消订单检查订单状态
     */
    function CheckStatus(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
         $this->load->helper('array');
         $coulms=array('oid');
         $request=elements($coulms, $this->input->post(), '');
         //校验传递的参数是否 
         if(empty($request['oid'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optionnull'));
            echo json_encode($response);exit;
         }
         //校验是否存在于该参数
         if(!is_numeric($request['oid'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optiontypenull'));
            echo json_encode($response);exit;
         }
         $this->load->model('nonstandard/order_model');
         $res=$this->order_model->GetOrderStatus('cancel',$request['oid']);
         if($res){
             $response=array('status'=>$this->config->item('request_succ'),
                     'msg'=>'','url'=>$this->config->item('url_cancelorder').'?oid='.$request['oid'],
                     'data'=>'');
             echo  json_encode($response);exit;
         }
         $response=array('status'=>$this->config->item('request_fall'),
                 'msg'=>$this->lang->line('orderstatus_fall_cancal'),
                 'url'=>$this->config->item('url_cancelorder_succ').'?status=e',
                 'data'=>'');
         echo  json_encode($response);exit;
    }
    /**
     * 订单模块-取消订单
     * @param      int       orderid   订单id
     * @param      string    content   取消订原因
     * @param      string    make      补充描述
     * @return     json              返回结果   
     */
    function  cancel_order(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION); 
        //校验参数是否合法
        $orderid=$this->input->post('orderid',true);
        if(empty($orderid) || !is_numeric($orderid)){
            Universal::Output($this->config->item('request_fall'),
                              $this->lang->line('order_optionnull'));           
        }
        $content=$this->input->post('content',true);
        if($content == 'undefined' ||  empty($content)){
            Universal::Output($this->config->item('request_fall'),
            $this->lang->line('order_optionnull'));
        }
        //校验当前的订单状态
        $this->load->model('nonstandard/order_model');
        $res=$this->order_model->GetOrderStatus('cancel',$this->input->post('orderid',true));        
        if(!$res){
            Universal::Output($this->config->item('request_fall'),'当前订单不允许取消');
        }        
        $this->load->model('nonstandard/order_model');
        $this->order_model->userid=$_SESSION['userinfo']['user_id'];
        $this->order_model->orderid=$this->input->post('orderid',true);
        $this->order_model->content=$this->input->post('content',true);
        $this->order_model->make=$this->input->post('make',true);
        $response=$this->order_model->save_cancelorder();
        if($response){
            if($this->order_model->msg == 1){
                $this->load->library('vendor/notice');
                $this->notice->JPush('alias',array($this->order_model->coopnumber),'您有报价已经被用户取消!');
            }
            Universal::Output($this->config->item('request_succ'),'订单已经被取消',
            $this->config->item('url_cancelorder_succ').'?status=q');
        }else{
            Universal::Output($this->config->item('request_fall'),'订单取消失败');
        }
    }
    function coopMoneyBack($backdata){
                  
    }
    /**
     * 订单列表   订单列表
     */
    function ViewOrder(){ 
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserIsLoginJump('/index.php/nonstandard/order/ViewOrder?status=n');
        //校验是否绑定手机号码
       
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('nonstandard/mobile');
            return false;
        }
        $this->load->helper('array');
        $coulms=array('status');
        $request=elements($coulms, $this->input->get(), '');
        $view['status']=empty($request['status']) ? 'd' :$request['status'] ;
        $this->load->view('nonstandard/order',$view);
    }
    /**
     * 我的订单 ----获取订单列表
     * @param    int   status  订单状态
     * @return   成功返回 string 订单列表  | 结果为空 或者 失败返回  原因
     */
    function  getOrderList(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);        
        $sign=$this->input->post('sign',true);
        if(!in_array($sign,array('all','electron','metal','deal'))){
            Universal::Output($this->config->item('request_fall'),'非法请求!');
        } 
        $this->load->model('nonstandard/order_model');
        $this->order_model->userid=$_SESSION['userinfo']['user_id'];
        $this->order_model->sign=$sign;
        $list=$this->order_model->GetOrderLsit();
        if($list  !== false){
            Universal::Output($this->config->item('request_succ'),'','',$list);
        }else{
            Universal::Output($this->config->item('request_fall'),'目前还没有订单!');
        }
        
    }
    /**
     * 我的订单----删除订单
     * @param  int     id    订单编号
     * @return array          处理结果
     */
    function  DelOrder(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('id');
        $request=elements($coulms, $this->input->post(), '');
        if(empty($request['id'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optionnull'));
            echo json_encode($response);exit;
        }
        if(!is_numeric($request['id'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optiontypenull'));
            echo json_encode($response);exit;
        }
        $this->load->model('nonstandard/order_model');
        $this->order_model->DelOrder($request['id']);
    }
    /**
     * 我的订单----修改订单 修改物品属性信息
     * @param
     */
    function  EditOrderAttr(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('id');
        $request=elements($coulms, $this->input->get(), '');
        $this->load->database();        
        $sql='select a.order_province,a.order_city,a.order_name as name,a.order_county,a.order_residential_quarters,
              a.order_isused,b.electronic_oather,b.electronic_img,a.order_ctype from  h_order_nonstandard  
              as a left join h_order_content  as b on a.order_number=b.order_id 
              where a.order_number="'.$request['id'].'" and a.wx_id='.$_SESSION['userinfo']['user_id'];
        $query=$this->db->query($sql);
        $view['order']=$query->row_array();
        $view['attrinfo']=json_decode($view['order']['electronic_oather'],true);
        $option['key']=$this->config->item('electronic_attribute_key');
        $option['val']=$this->config->item('electronic_attribute_val');
        $view['id']=$request['id'];
        $view['attribute']=array('key'=>$option['key'][$view['order']['order_ctype']],
                'val'=>$option['val'][$view['order']['order_ctype']]);
        $this->load->view('nonstandard/editattr',$view);
    }
    /**
     * 我的订单----修改订单  详细信息
     */
    function EditOrder(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('id');
        $request=elements($coulms, $this->input->get(), '');
        $this->load->database();
        $this->load->model('common/wxcode_model');
        $sql='select a.order_province,a.order_latitude,a.order_longitude,
              a.order_city,a.order_county,a.order_residential_quarters,
              a.order_isused,b.electronic_oather,b.electronic_img,a.order_ctype 
              from  h_order_nonstandard  as a left join h_order_content  
              as b on a.order_number=b.order_id where a.order_number="'.$request['id'].
              '" and a.wx_id='.$_SESSION['userinfo']['user_id'];
        $query=$this->db->query($sql);
        if($query->num_rows() == 0){
            $response=json_encode(array('ststus'=>$this->config->item('request_illegal_access')));
            exit($response);
        }
        $view['oid']=$request['id'];
        $view['order']=$query->row_array();
        $view['attrinfo']=json_decode($view['order']['electronic_oather'],true);       
        $view['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        $this->load->view('nonstandard/editorder',$view);
    }
    /**
     * 我的订单----修改订单 保存修改信息
     * @param    string   sfdq_tj   //省
     * @param    string   csdq_tj   //市
     * @param    string   qydq_tj   //区
     * @param    string   latitude  //纬度
     * @param    string   longitude //经度
     * @param    string   quarters  //小区名称
     * @param    int      oid       //订单编号
     * @param    int      status    //状态
     * @param    int      isused    //是否可用
     */
    function SaveOrder(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('sfdq_tj','csdq_tj','qydq_tj','latitude','longitude','quarters','oid','status','isused','orname');
        $request=elements($coulms, $this->input->post(), '');
        if(empty($request['sfdq_tj'] )   || empty($request['csdq_tj']) ||
           empty($request['qydq_tj'])    || empty($request['latitude']) ||
           empty($request['longitude'] ) || empty($request['oid']) || 
           empty($request['status'])     || empty($request['isused']) ||
                empty($request['quarters'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optionnull'));
            echo json_encode($response);exit;
        }
        if(!is_numeric($request['oid']) || !is_numeric($request['status'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optiontypenull'));
            echo json_encode($response);exit;
        }
        $this->load->model('weixin/wxinformation_model');
        $latlon=$request['longitude'].','.$request['latitude'];
        $baidu_loc=$this->wxinformation_model->conversion_gps($latlon);
        if($baidu_loc->status ==0){
            $baidulocalhost=$baidu_loc->result['0']->y.','.$baidu_loc->result['0']->x;
            $request['latitude']=$baidu_loc->result['0']->y;
            $request['longitude']=$baidu_loc->result['0']->x;
        }
        $this->load->database();
        $res=$this->db->update('h_order_nonstandard',array('order_province'=>$request['sfdq_tj'],
                'order_city'=>$request['csdq_tj'],'order_county'=>$request['qydq_tj'],
                'order_latitude'=>$request['latitude'],'order_longitude'=>$request['longitude'],
                'order_residential_quarters'=>$request['quarters'],
                'order_orderstatus'=>$request['status'],
                'order_updatetime'=>time(),
                'order_submittime'=>time()
        ),array('order_number'=>$request['oid'],'wx_id'=>$_SESSION['userinfo']['user_id']));
        if($res === false){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_addclothes_fall'));
            echo json_encode($response);exit;
        }
        if ($request['status'] == 1) {
          $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
          $relist = $request['orname'].','.$request['oid'].','.$request['longitude'].','.$request['latitude'];
          $status = $this->zredis->_redis->rpush('orderlist',$relist);
        }        
        $response=array('status'=>$this->config->item('request_succ'),
                'msg'=>'','url'=>$this->config->item('url_cancelorder_succ').'?status=n');
        echo json_encode($response);exit;
    }
    /**
     * 我的订单----显示订单详情
     * @param    int  　订单id
     * @return   查询成功返回当前订单的详细信息
     */
    function ViewOrderInfo(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $orderid=$this->input->get('id',true);
        //校验订单编号
        if(empty($orderid)){
            Universal::Output($this->config->item('request_fall'),'出现异常!');
        } 
        if(isset($orderid{20}) || !is_numeric($orderid) ){
            Universal::Output($this->config->item('request_fall'),'出现异常!');
        }
        $this->load->model('nonstandard/order_model');
        $this->order_model->userid=$_SESSION['userinfo']['user_id'];
        $this->order_model->orderid=$orderid;
        $view=$this->order_model->GetOrderInfo();
        if(!$view){
            Universal::Output($this->config->item('request_fall'),'不存在该订单编号!');
        } 
        if (isset($view['offer'])&&!in_array($view['offer']['0']['number'],$this->config->item('js_cooplist'))) {//如果是寄售通，不给优惠券
            $this->load->model('coupon/couponuser_model');
            $comparePrice = $view['offer']['0']['second']>0?$view['offer']['0']['second']:$view['offer']['0']['price'];
            $this->couponuser_model->ranges = $comparePrice;
            $this->couponuser_model->mobile = $_SESSION['userinfo']['user_mobile'];
            $coupon = $this->couponuser_model->getCouponUser();
            if ($coupon!==false) {
                $maxcop = 0;
                foreach ($coupon as $k => $v) {
                    if ($comparePrice>=$v['ranges']&&$maxcop<$v['amount']&&($v['jointime']+$v['contime'])>time()) {
                        $maxcop = $v['amount'];
                        $view['couponInfo'] = $v;
                    }
                }
            }
        }
        $this->load->view('nonstandard/vieworder',$view);
    }
    /**
     * 用户确认第二次报价
     * @param    int   id  订单id
     * @return   json 返回结果  
     */
    function ConfirmQuote(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $id=$this->input->post('id',true);
        //校验参数
        if(empty($id)){
            Universal::Output($this->config->item('request_fall'),'出现异常!');
        }        
        if(isset($id{20}) || !is_numeric($id) ){
            Universal::Output($this->config->item('request_fall'),'出现异常!');
        }
        $this->load->model('nonstandard/order_model');
        $this->order_model->userid=$_SESSION['userinfo']['user_id'];
        $this->order_model->orderid=$id;
        $res=$this->order_model->ConfirmQuote();
        if($res){ 
            $coop=$this->order_model->getCoopNumber();
            if($coop !== false){
                $this->load->library('vendor/notice');
                $user[]=$coop;
                $this->notice->JPush('alias',$user,'您有修改的报价已经被用户确认,请及时处理');
            }
            Universal::Output($this->config->item('request_succ'),'已经成功确认,等待回收商完成订单',
            '/index.php/nonstandard/order/ViewOrderInfo?id='.$id);
        }else{
            Universal::Output($this->config->item('request_fall'),'确认报价出现异常!');
        }
    }
    
}
