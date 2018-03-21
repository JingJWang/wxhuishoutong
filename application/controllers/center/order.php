<?php
/**
 * 订单模块  数码订单
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class order extends CI_Controller{
    
    /**
     * 获取订单列表
     * @param   int    status 状态
     * @param   int    page  页码
     * @return  json   返回订单列表
     */
    function orderList(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $status=$this->input->post('status',true);
        $time=$this->input->post('time',true);
        $page=$this->input->post('page',true);
        $start=$this->input->post('start',true);
        $end=$this->input->post('end',true);
        //校验是否参数是否为空
        if(empty($status) || empty($time) ||empty($page)){
            Universal::Output($this->config->item('request_fall'),'订单状态与日期不能为空');
        }
        if(!in_array($status,array('1','3','10','2','-2','-1','4','3','all'))){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
        }
        if(!is_numeric($page)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
        }
        if(!empty($start) && !empty($end)){
            $start=strtotime($start);
            $end=c($end);
            if($start == false && $end == false ){
                Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
            }
        }else{
            $start=0;
            $end=0;
        }
        $keyword=$this->input->post('keyword',true);
        $this->load->model('center/digitaorder_model');
        //当存在关键词 并且符合规范 则按照关键词检索列表
        if(!empty($keyword) && isset($keyword{10})){
            if(!is_numeric($keyword)){
                Universal::Output($this->config->item('request_fall'),'输入的关键词不符合规范');
            }
            $this->digitaorder_model->keyword=$keyword;
            $response=$this->digitaorder_model->searchUser();
            if(!$response){
                Universal::Output($this->config->item('request_fall'),'没有找到包含关键词的内容');
            }else{
                $this->digitaorder_model->userid=$response['0']['id'];
            }            
        }else{
            $this->digitaorder_model->userid=0;//用户id
            $this->digitaorder_model->number=Universal::safe_replace($keyword);//订单编号
        }        
        $this->digitaorder_model->status=$status;
        $this->digitaorder_model->time=$time;
        $this->digitaorder_model->page=$page;
        $this->digitaorder_model->num=9;
        $this->digitaorder_model->start=$start;
        $this->digitaorder_model->end=$end;
        $response=$this->digitaorder_model->orderList();
        if($response !== false){
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有获取到结果');
        }
    }
    /**
     * 根据订单id 查询订单内容
     * @param  int  id  订单id
     * @return  json  查询成功返回订单详情 | 查询失败 错误原因
     */
    function orderInfo(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单的id');
        }
        $this->load->model('center/digitaorder_model');
        $this->digitaorder_model->id=$id;
        $response=$this->digitaorder_model->orderInfo();
        if($response === false){
            Universal::Output($this->config->item('request_fall'),'获取订单详情出现异常');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$response);
        }
    }
    /**
     * 根据订单id 完成订单的预支付操作
     * @param  int  id  订单id
     * @return  json  返回预支付结果
     */
    function prePayment(){ 
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数类型
        $orderid=$this->input->post('id',true);
        if(empty($orderid) || !is_numeric($orderid)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单的id');
        }
        //校验当前订单是否是预支付订单
        $this->load->model('center/digitaorder_model');
        $this->digitaorder_model->orderid=$orderid;
        $order=$this->digitaorder_model->getOrderInfo();
        if(!$order){
            Universal::Output($this->config->item('request_fall'),'当前订单不是预支付订单');
        }
        //读取用户信息
        $this->digitaorder_model->userid=$order['0']['wx_id'];
        $user=$this->digitaorder_model->getUser();
        if(!$user){
            Universal::Output($this->config->item('request_fall'),'没有获取到用户信息');
        }
        //获取订单的报价信息
        $this->digitaorder_model->number=$order['0']['order_number'];
        $offer=$this->digitaorder_model->getOrderOffer();
        if(!$offer){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单报价信息');
        }        
        //完成订单的预支付操作
        $this->digitaorder_model->offerid=$offer['0']['offerid'];
        $this->digitaorder_model->price=$offer['0']['price']*100;
        $this->digitaorder_model->userid=$order['0']['wx_id'];
        $pay=$this->digitaorder_model->prePayment();
        if($pay === false){
            Universal::Output($this->config->item('request_fall'),'预支付操作失败!');
        }else{
            $wxmsg=$this->config->item('coop_wxuser_prepay_done_info');
            $message=$this->config->item('APP_alidayu_prepay_msg');
            $url=$this->config->item('coop_wxuser_done_url');
            $this->advanceNotice(1,$order['0']['order_number'],$wxmsg,$message,$url);
            Universal::Output($this->config->item('request_succ'),'预支付操作完成!');
        }
    }
    /**
     * 通知用户当前订单的状态
     */
    function advanceNotice($type,$orderid,$wxmsg,$message,$url,$msginfo=1){
        $this->load->model('appsunny/order_model');
        $get_wxuser_info = $this->order_model->get_wxuser_info($orderid);
        // 给用户发送微信通知
        $this->load->model('common/wxcode_model');
        $this->load->helper('url');
        $temp_url=base_url($url);
        switch ($type){
            case '1':
                $content = sprintf($wxmsg,$get_wxuser_info['openid'],$get_wxuser_info['title'],
                        $get_wxuser_info['money'],$temp_url);
                break;
            case '2':
                $content = sprintf($wxmsg,$get_wxuser_info['openid'],$get_wxuser_info['title'],
                        $temp_url);
                break;
            case '3':
                $content = sprintf($wxmsg,$get_wxuser_info['openid'],$get_wxuser_info['title'],
                        $get_wxuser_info['money'],$temp_url);
                break;
        }        
        $resp=$this->wxcode_model->sendmessage($content);
        // 给微信用户发送短信通知
        $this->load->library('alidayu/alimsg');
        $this->alimsg=new Alimsg();
        $this->alimsg->mobile=$get_wxuser_info['mobile'];
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->sign=$this->config->item('alidayu_signname');
        $this->alimsg->template=$message;
        if($msginfo == 1){
            $this->alimsg->content="{\"name\":\"".$get_wxuser_info['title']."\",\"moeny\":\"".$get_wxuser_info['money']."\"}";
        }else{
            $this->alimsg->content="{\"name\":\"".$get_wxuser_info['title']."\",\"money\":\"".$get_wxuser_info['money']."\"}";
         }
        $response = $this->alimsg->SendNotice();
    }
    /**
     * 修改报价
     */
    function upQuote() {
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //获取订单编号与修改过后的价格
       $number=$this->input->post('number',true);
       if(!is_numeric($number)){
           Universal::Output($this->config->item('request_fall'),'没有获取到订单编号');
       } 
       $upprice=$this->input->post('upprice',true);
       if(!is_numeric($upprice)){
           Universal::Output($this->config->item('request_fall'),'没有获取到修改的报价');
       }
       $price=$this->input->post('price',true);
       if(!is_numeric($upprice)){
           Universal::Output($this->config->item('request_fall'),'没有获取到第一次报价');
       }
       //修改报价
       $this->load->model('center/digitaorder_model');
       $this->digitaorder_model->price=$price;
       $this->digitaorder_model->upprice=$upprice;
       $this->digitaorder_model->number=$number;
       $order=$this->digitaorder_model->upQuote();
       if($order){
           $wxmsg=$this->config->item('coop_wxuser_modify_info');
           $message=$this->config->item('APP_alidayu_modify_price');
           $url=sprintf($this->config->item('coop_wxuser_modify_url'),$number);
           $this->advanceNotice(2,$number,$wxmsg,$message,$url);
           Universal::Output($this->config->item('request_succ'));
       }else{
           Universal::Output($this->config->item('request_fall'),'修改报价出现异常!');
       }
    }
    /**
     * 订单支付
     */
    function  orderpay(){  
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数类型
        $orderid=$this->input->post('id',true);
        if(empty($orderid) || !is_numeric($orderid)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单的id');
        }
        //校验当前支付订单是否包含现金券
        $vouchid=$this->input->post('vouch',true);
        $this->load->model('center/digitaorder_model');
        //读取订单
        $this->digitaorder_model->id=$orderid;
        $order=$this->digitaorder_model->orderstatus();
        if($order === false){
            Universal::Output($this->config->item('request_fall'),'获取订单内容出现异常');
        }        
        //读取报价信息
        $this->digitaorder_model->number=$order['number'];
        $price=$this->digitaorder_model->orderquote();
        if($price === false){
            Universal::Output($this->config->item('request_fall'),'获取报价内容出现异常');
        }
        //校验是否存在现金劵的ID
        if(!empty($vouchid)){
            $vouchres=$this->digitaorder_model->vouchinfo($vouchid);
            $vouchpri=0;
            if($vouchres == false){
                Universal::Output($this->config->item('request_fall'),'现金劵失效或者被使用!');
            }else{
                $vouchpri=$vouchres['info_amount'];
            }
            //校验现金劵是否符合使用规则
            if($price < $vouchres['info_range']){
                $vouchpri=0;
            }
        }else{
            $vouchres['coupon_id']=0;
            $vouchpri=0;
        }
        //完成订单支付
        $this->digitaorder_model->vouchid=$vouchres['coupon_id'];
        $this->digitaorder_model->userid=$order['wx_id'];
        $this->digitaorder_model->orderid=$order['number'];
		$this->digitaorder_model->invitation=$order['invi'];
        $this->digitaorder_model->moeny=$price;
        $this->digitaorder_model->vouchpri=$vouchpri;
        $pay=$this->digitaorder_model->orderpay();
        if($pay === false){
            Universal::Output($this->config->item('request_fall'),'订单支付过程中出现异常');
        }else{
            $wxmsg=$this->config->item('coop_wxuser_done_info');
            $message=$this->config->item('APP_alidayu_order_success');
            $url=$this->config->item('coop_wxuser_done_url');
            $this->advanceNotice(1,$order['number'],$wxmsg,$message,$url,$msginfo=2);
            Universal::Output($this->config->item('request_succ'),'订单已经支付完成');
        }
    }    
    /**
     * 取消订单
     * @param  int  id  订单id
     * @return  json  返回处理结果
     */
    function ordercall(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单ID!');
        }
        $content=$this->input->post('content',true);
        //读取订单
        $this->load->model('center/digitaorder_model');
        //获取订单记录
        $order=$this->digitaorder_model->ordercont($id);
        if($order === false){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单内容!');
        }
        //读取用户记录
        $user=$this->digitaorder_model->userinfo($order['userid']);
        if($order === false){
            Universal::Output($this->config->item('request_fall'),'没有获取到用户内容!');
        }
        //读取订单报价信息
        $offer=$this->digitaorder_model->offerinfo($order['number']);
        if($order === false){
           Universal::Output($this->config->item('request_fall'),'没有获取到报价内容!');
        }
        if($user['freeze'] < $offer['price'] ){
            Universal::Output($this->config->item('request_fall'),'用户冻结余额出现异常!'); 
        }
        $call=array('content'=>$content);
        $option=array_merge($order,$user,$offer,$call);
        $resp=$this->digitaorder_model->ordercall($option);
        if ($resp){
            $option=array('mobile'=>$user['mobile'],'name'=>$order['name'],'phone'=>$this->config->item('system_service_phone'));
            $this->cacelOrderMsg($option);
            Universal::Output($this->config->item('request_succ'));
        }else {
            Universal::Output($this->config->item('request_fall'));
        }
    }
    /**
     * 订单取消时候通知用户
     * @param  int 		mobile 手机号码
     * @param  string 	name  订单名称
     * @param  int 		phone 客服电话 
	 *
     */
    function cacelOrderMsg($option){
        // 给微信用户发送短信通知
        $this->load->library('alidayu/alimsg');
        $this->alimsg=new Alimsg();
        $this->alimsg->mobile=$option['mobile'];
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->sign=$this->config->item('alidayu_signname');
        $this->alimsg->template=$this->config->item('alidayu_templte_cacelorder');
        $this->alimsg->content="{\"name\":\"".$option['name']."\",\"phone\":\"".$option['phone']."\"}";
        $response = $this->alimsg->SendNotice();
        
    }
    /**
     * 返回列表数据的操作
     */
    function operation($status){
            //订单未提交 已成交
            if( $status == -2 || $status == 10 || $status == -1 || $status == 4){
                $operation['1']=array('look'=>'查看');
            }
            //订单报价中 
            if( $status == 1 ){
                $operation=array('look'=>'查看','offer'=>'查看报价','canel'=>'取消订单');
            }
            //等待预支付  待交易  
            if ($status == 2 ){
                $operation=array('look'=>'查看','prePayment'=>'预支付');
            }
            if( $status == 3 ){
                $operation=array('look'=>'查看','offer'=>'查看报价','cacel'=>'取消订单');
            }
            return $operation;
    }
    /**
     * 获取当前订单的用户以及用户的代金券
     * @param int id 订单id 
     * @return json 订单金额以及代金券列表
     */
    function getVouchers(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);    
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'订单ID校验不正确!');
        }
        //获取订单需要支付金额
        $this->load->model('center/digitaorder_model');
        $info=$this->digitaorder_model->getVouchers($id);
        if($info === false){
            Universal::Output($this->config->item('request_fall'),'获取代金券列表出现异常!','',array('price'=>$this->digitaorder_model->price));
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$info);
        }
    }
    /**
     * 获取批量回收订单的内容
     * @param  id  int  订单内容
     * @return json
     */
    function info(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);        
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单ID');
        }
        //校验订单是否是批量回收
        $this->load->model('center/digitaorder_model');
        $this->digitaorder_model->id=$id;        
        $info=$this->digitaorder_model->info();
        if($info == false){
            Universal::Output($this->config->item('request_fall'),'查询不到当前订单或不是批量回收类型');
        }
        //获取批量回收内容
        $this->digitaorder_model->number=$info['number'];
        $batchinfo=$this->digitaorder_model->batchinfo();
        if($batchinfo == false){
            Universal::Output($this->config->item('request_fall'),'查询不到当前订单内容');
        }
        $batchinfo=json_decode($batchinfo['content'],true);
        $response=array('amount'=>$batchinfo['amount'],'oather'=>$batchinfo['oather'],'number'=>$info['number']);
        Universal::Output($this->config->item('request_succ'),'','',$response);
    }
    /**
     * 批量回收报价
     * @param  int number 订单编号
     * @param json
     */
    function batchQuote() {
        //校验用户是否在线 
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $number=$this->input->post('number',true);
        if(!is_numeric($number)){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单编号');
        }
        $pri=$this->input->post('pri',true);
        if(!is_numeric($pri)){
            Universal::Output($this->config->item('request_fall'),'没有获取到价格');
        }
        $coop=1447299307;
        //回去回收商信息
        $this->load->model('nonstandard/quote_model');
        $this->quote_model->coopid=$coop;
        $this->quote_model->number=$number;
        $res=$this->quote_model->isQuote();
        if($res === false){
            Universal::Output($this->config->item('request_fall'),'当前回收商已经报过价');
        }        
        $this->quote_model->coopid=$coop;
        $coopinfo=$this->quote_model->GetCoopInfo();
        if($coopinfo === false){
            Universal::Output($this->config->item('request_fall'),'没有获取到回收商报价信息');
        }
        $coopinfo['number']=$number;
        $coopinfo['pri']=$pri;
        //添加批量回收报价
        $resp=$this->saveBatch($coopinfo);
        if($resp){
            Universal::Output($this->config->item('request_fall'),'报价成功');
        }else{
            Universal::Output($this->config->item('request_fall'),'报价成功');
        }
        
    }
    /**
     * 添加批量回收报价
     * @param  array  $option
     * @return bool
     */
    function saveBatch($option){
        $this->quote_model->order_name='批量回收';
        $this->quote_model->done_times=$option['0']['offer'];
        $this->quote_model->coop_name=$option['0']['coop_name'];
        $this->quote_model->coop_class=$option['0']['coop_class'];
        $this->quote_model->coop_auth=$option['0']['coop_auth'];
        $this->quote_model->user_id=$option['0']['coop_number'];
        $this->quote_model->coop_addr=$option['0']['coop_address'];
        $this->quote_model->order_id=$option['number'];
        $this->quote_model->price=$option['pri'];
        $this->quote_model->service='快递回收';
        $this->quote_model->remark='';
        $this->quote_model->distance=0;
        $this->quote_model->lng=$option['0']['lng'];
        $this->quote_model->lat=$option['0']['lat'];
        $resp=$this->quote_model->savePlanQuote();
        return $resp;
    }
    
    
    
    
    
    
}