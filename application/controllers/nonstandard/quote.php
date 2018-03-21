<?php

/**
 * 报价模块
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class Quote extends CI_Controller {
    
    private  $pri  = 0 ; //自动报价初始价格
    
    private  $base = 0 ; //自动报价基础价格

    private  $garbage = 0 ;// 自动报价垃圾价格
    
    private  $displayPri = 0 ;// 自动报价屏幕价格
    //初始化公共类
    function __construct(){
        parent::__construct();
        $this->load->library('common/universal');
    }
    /**
     * 订单报价----显示报价列表
     */
    function  ViewQuote(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array('id');
        $option=elements($coulms, $this->input->get(), '');
        //校验订单的状态  确认订单是否 在等待报价状态
        $this->load->model('nonstandard/order_model');
        $status=$this->order_model->checkOrder($option['id']);
        if($status === false){
             header('Location: /index.php/nonstandard/system/welcome');
        }
        if(empty($option['id']) || !is_numeric($option['id'])){
             exit();
        }
        $_SESSION['quoteId']=$option['id'];
        $view['type']=$status['order_ctype'];
        $view['option']=$this->config->item('user_quote_option');
        $view['options']=$this->config->item('cooperator_offer_service');        
        $this->load->view('nonstandard/quote',$view);  
    }
    /**
     * 订单报价----筛选报价
     * @param   int     price        价格
     * @param   int     distance     距离
     * @param   int     evaluation   评价
     * @param   int     transaction  成交单数
     * @param   string  option       服务
     * @param   int     auto         是否认证回收商
     * @return  array                结果集
     */
    function GetScreening(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);       
        $this->load->helper('array');
        $coulms=array('price','distance','evaluation','transaction','option','auto');
        $option=elements($coulms, $this->input->post(), '');
        $this->load->model('nonstandard/quote_model');
        $data['option']=$this->quote_model->GetScreening($option);  
        
        if(empty($data['option'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'','url'=>'','data'=>'');
        }else{
        	//获取当前用户登录手机号
        	$mobile = $_SESSION['userinfo']['user_mobile'];
        	//效验传参手机号  $mobile 为用户报单的手机号
        	if(!is_numeric($mobile)){
        		Universal::Output($this->config->item('request_fall'),'参数错误','','');
        	}
        	//加载优惠券模块
        	$this->load->model('coupon/couponuser_model');
        	$this->couponuser_model->mobile = $mobile;
        	$result = $this->couponuser_model->getCouponUser();
        	if($result != null){
        		$data['coupon'] = $result;
        	}
            $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>'','url'=>'','data'=>$data);
        }
        echo json_encode($response);exit;
    }
    /**
     * 订单报价----查看报价详情
     * @param     int    type   详情id
     */
    function  transactions(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $offerid=$this->input->get('type',true);     
        if(empty($offerid) || !is_numeric($offerid)){
            Universal::Output($this->config->item('request_fall'),'异常请求!');
        }   
        //从用户中心里面获取当前用户的用户券信息
        //获取当前用户登录手机号
        $mobile = $_SESSION['userinfo']['user_mobile'];
        //效验传参手机号  $mobile 为用户报单的手机号
        if(!is_numeric($mobile)){
        	Universal::Output($this->config->item('request_fall'),'参数错误','','');
        }
        //加载优惠券模块
        $this->load->model('coupon/couponuser_model');
        $this->couponuser_model->mobile = $mobile;
        $this->load->model('nonstandard/quote_model');
        $view['offerid']=$offerid;
        $this->quote_model->offerid=$offerid;
        $view['offer'] = $this->quote_model->Gettransactions();
        //订单号
        $numId=$view['offer'][0]['order_id'];
        //获取回收商信息
        $view['recover']=$this->quote_model->GetRecover($numId);
        $view['auto']=$this->config->item('cooperator_auth_type');
        //获取回收商记录
        $view['deal']=$this->quote_model->GetDealOrder($view['offer']['0']['cooperator_number']);
        $view['evalua']=$this->quote_model->GetEvaluation($view['offer']['0']['cooperator_number']);
        $result= $this->couponuser_model->getCouponUser();
        if($result != false  && in_array($view['offer']['0']['number'],$this->config->item('js_cooplist')) == false){
            $coup=array();
            foreach ($result as $key=>$val){
                if($view['offer']['0']['offer_price'] > $val['ranges']){
                    $coup[]=$val['amount'];
                }
            }
			if(!empty($coup)){
				$flag=max($coup);
			}else{
				$flag=0;
			}  
            switch ($flag){
                case '50':
                   $view['coup']='usable fl';
                   break;
                case '100':
                    $view['coup']='usable fl large';
                   break;
				default:
					$view['coup']='';
            }
        }else{
            $view['coup']='';
        }        
        $this->load->view('nonstandard/transactions',$view);
    }
    /**
     * 订单报价----选定报价
     * @param     int    oid  订单id
     * @param     int    qid  报价id
     * @param     string city 地址信息
     * @param     string quarters 详细地址
     * @return    成功返回 json  跳转地址 | 失败返回 json 原因
     */
    function  ChoiceQuote(){
    //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验参数是否为空
        $order=$this->input->post('oid',true);
        if(empty($order) || !is_numeric($order)){
            Universal::Output($this->config->item('request_fall'),
            $this->lang->line('order_optionnull'));
        }
        $offer=$this->input->post('qid',true);
        if(empty($offer) || !is_numeric($offer)){
            Universal::Output($this->config->item('request_fall'),
            $this->lang->line('order_optiontypenull'));
        }
        //校验地址信息
       // $addres=$this->input->post('quarters',true);
        $mobile=$this->input->post('mobile',true);
       /*  if(empty($city) || empty($addres) || empty($mobile) || !is_numeric($mobile)){
            Universal::Output($this->conifg->item('request_fall'),'地址信息和电话号码为必填选项');
        } */
        if(empty($mobile) || !is_numeric($mobile)){
            Universal::Output($this->conifg->item('request_fall'),'地址信息和电话号码为必填选项');
        }
        //更改订单 报价信息状态
        $this->load->model('nonstandard/quote_model');
        $this->quote_model->offerid=$offer;
        $this->quote_model->orderid=$order;
        $this->quote_model->mobile=$mobile;
       // $this->quote_model->city=Universal::safe_replace($city);
       // $this->quote_model->addres=Universal::safe_replace($addres);
        //补充地址信息
        $up=$this->quote_model->saveAddres();
        if(!$up){
            Universal::Output($this->config->item('request_fall'),'保存地址信息出现异常');
        }
        $res=$this->quote_model->ChoiceQuote();       
        if($res){
            $url='/index.php/nonstandard/order/ViewOrderInfo?id='.$order;
            Universal::Output($this->config->item('request_succ'),'',$url);
        }else{
            Universal::Output($this->config->item('request_fall'));
        }
    }
    /**
     * 订单报价---查看报价详情
     * @param    int   oid   报价oid
     */
    function  QuoteInfo(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->helper('array');
        $coulms=array("qid",'oid');
        $option=elements($coulms, $this->input->get(), '');
        //校验参数是否为空
        if(empty($option['qid']) ){
            Universal::Output($this->config->item('request_fall'),
            $this->lang->line('order_optionnull'));
        }
        //校验参数格式不对
        if( !is_numeric($option['qid'])){
            Universal::Output($this->config->item('request_fall'),
            $this->lang->line('order_optiontypenull'));
        }
        //更改订单 报价信息状态
        $this->load->model('nonstandard/quote_model');
        $view['coop']=$this->quote_model->GetCoppInfo($option['qid']); 
        $view['oid'] =$option['oid'];
        $this->load->view('nonstandard/choice',$view);
    }
    /**
     * 服务端任务接口 查询没有报价的订单 处理订单     * 
     */
    function planQuote(){
        //查询等待报价的订单
        $this->load->model('nonstandard/quote_model');
        $order=$this->quote_model->GetOrder();
        if($order  === false || empty($order)){
            echo '没有任务';exit();
        }		
        //保存任务信息
        $this->plan_id=$order['0']['plan_id'];//任务ID
        $this->user_openid=$order['0']['plan_openid'];//用户openid
        $this->type_name=$order['0']['type_name'];//型号名称
        $this->order_number=$order['0']['order_number'];//订单编号
        $this->order_lat=$order['0']['plan_lat'];//经度
        $this->order_lng=$order['0']['plan_lng'];//纬度
        $this->user_mobile=$order['0']['plan_mobile'];//用户手机号码
        //获取报价方案
        $this->quote_model->typeid=$order['0']['type_id'];
        $plan=$this->quote_model->GetPlan();
        //订单属性
        $attr=json_decode($order['0']['plan_content'],true);
        //自动报价回收商列表
        $coopdefault=$this->config->item('quote_plan_coop');
        //循环自动报价方案
        $this->coop='';
		$coop_number='';
		$quote=array();
        foreach ($plan as $k=>$v){
           //报价方案
           $this->quote=json_decode($v['plan_content'],true);
           //订单参数
           $this->attr=$attr;           
           //市价
           $this->lock=$plan['0']['plan_base_price']; 
           //调用运算规则
           $pri=$this->reckonRules(); 
		 
           //报价列表
           $coop_key=array_rand($coopdefault,1);
           $coop_number=$coopdefault[$coop_key];
           $quote[$coop_number]=$pri;
           //回收商编号
           $this->coop .= '"'.$v['coop_number'].'",';
        }          
        //获取标识寄售商列表
        $js_cooplist=$this->config->item('js_cooplist');
        //计算其他回收商的价格
        if($coopdefault !== false ){
            $base_pri=$quote[$coop_number];
            foreach ($coopdefault as $k=>$v){ 
                    if($v != $coop_number){
                        $coefficient=rand(1,8);
                        $temp_pri=$base_pri-$base_pri*$coefficient/100;
                        $quote[$v]=round($temp_pri);
                    }
            }
			//当基础价格大于100的时候 增加寄售商报价
            if($base_pri > 400){
                $temp_pri=$base_pri+$base_pri*10/100;
                $quote[$js_cooplist['0']]=round($temp_pri);
            }else{
				unset($quote[$js_cooplist['0']]);
			}
        }        
        //重新校验报价 价格 是否小于0
        foreach ($quote as $key=>$val){
            if($val <= 2){
               $quote[$key]=2;
            }
        } 
        $this->coop = array_keys($quote);
        $this->load->library('common/Location');
        //获取回收商的信息
        $this->quote_model->coopid=implode($this->coop,',');
        $coopinfo=$this->quote_model->GetCoopInfo();
        if(empty($coopinfo) || $coopinfo === false){
            $this->quote_model->status=-2;
            $response=$this->quote_model->editPlan();
            echo  '没有查询到回收商的信息';exit();
        }  
        foreach ($coopinfo as $key=>$val){
            $this->quote_model->order_name=$this->type_name;
            $this->quote_model->done_times=$val['offer'];
            $this->quote_model->coop_name=$val['coop_name'];
            $this->quote_model->coop_class=$val['coop_class'];
            $this->quote_model->coop_auth=$val['coop_auth'];
            $this->quote_model->user_id=$val['coop_number'];
            $this->quote_model->coop_addr=$val['coop_address'];
            $this->quote_model->order_id=$this->order_number;
            $this->quote_model->price=$quote[$val['coop_number']];
            $this->quote_model->service='快递回收';
            $this->quote_model->remark='';
            $this->quote_model->distance=0;
            $this->quote_model->status=1;
            //Location::getDistance($this->order_lat,$this->order_lng,$val['lat'],$val['lng']);
            $this->quote_model->lng=$val['lng'];
            $this->quote_model->lat=$val['lat'];
            $this->quote_model->plan_id=$this->plan_id;
            $res=$this->quote_model->savePlanQuote();
        }
		
        //修改自动报价任务的状态
        $this->quote_model->status=1;
        $response=$this->quote_model->editPlan();
        $this->checkTask($this->user_mobile);
        $this->SendNotice();        
    }    
    /**
     *自动报价运算规则
     *
     */
    function reckonRules(){
        //获取自动报价参数 分类
        $model=$this->quote_model->optionModel();
        //价格参数
        $plan=$this->quote;
        //订单内容
        $attr=$this->attr;
        $quote=array();
		$basics=array();
		$unive=array();
		$special=array();
        foreach ($model as $key=>$val){
            switch ($val['type']){
                case '0':
                    array_key_exists($val['alias'],$attr) == true ? $basics[$key]=$attr[$val['alias']] : '';
                    break;
                case '1':
                    array_key_exists($val['alias'],$attr) == true ? $unive[$key]=$attr[$val['alias']] : '';
                    break;
                case '2':
                    array_key_exists($val['alias'],$attr) == true ? $special=$attr[$val['alias']] : '';
                    break;
            }
        }
        $basics=$this->position($basics,$plan,1);
        $unive=$this->position($unive,$plan,2);
		$special=$this->position($special,$plan,2);
		$pri=$this->reckon($this->lock, $basics, $unive, $special);
        return $pri;
    }
    /**
     * 计算价格
     */
    function reckon($pri,$basics,$unive,$special){
		if(!is_array($basics)){
			 $pri=$pri-$basics;
		}else{
			foreach ($basics as $key=>$val){
            //echo $pri.'-'.$val,'=';
               $pri=$pri-$val;
			}
		}
        //echo $pri.'-'.$pri.'*'.$unive.'/100','=';
        $pri=$pri-$pri*$unive/100;
        //echo $pri.'-'.$pri.'*'.$special.'/100','=';
        $pri=$pri-$pri*$special/100;
        $pri=round($pri);
        return $pri;
    }
    /**
     *得到扣款中同类型百分比最多的一项
     *@param array  $option  订单参数
     *@param array  $plan    报价参数
     *@param int    $type    类型   值为1 不返回最大值 2返回最大值
     */
    function position($option,$plan,$type){
    	$ratio=array();
		if(!is_array($option)){
			return 0;
		}else{
			foreach ($option as $key=>$val){
				$ratio[]=str_replace(array('-','+','%'),array('','',''),$plan[$val]);
			}
			switch ($type){
				case 1:
					$result=$ratio;
					break;
				case 2:
					$result=max($ratio);
					break;
			}
			return  $result;
		}
    }
    /**
     * 获取报价方案价格
     */
    function planPri($quote){
         
    }
    /**
     * 自动报价完成后发送通知
     */
    function  SendNotice(){
        // 给微信用户发送微信通知
        $this->load->model('common/wxcode_model');
        $temp = sprintf($this->config->item('coop_wxuser_offer_url'),$this->order_number);
        $this->load->helper('url');
        $temp_url =  base_url($temp);
        $content = sprintf($this->config->item('coop_wxuser_offer_info'),
                $this->user_openid,$temp_url);
        $this->load->database();
        $response_wx=$this->wxcode_model->sendmessage($content);
        // 阿里大鱼短信
        $this->load->library('alidayu/alimsg');
        $this->alimsg=new Alimsg();
        $this->alimsg->mobile=$this->user_mobile;
        $this->alimsg->appkey=$this->config->item('alidayu_appkey');
        $this->alimsg->secret=$this->config->item('alidayu_secretKey');
        $this->alimsg->sign=$this->config->item('alidayu_signname');
        $this->alimsg->template=$this->config->item('APP_alidayu_offer_msg');
        $this->alimsg->content="{\"content\":\"".$this->type_name."\"}";
        $response=$this->alimsg->SendNotice();
    }
    /**
     * 校验用户是否存在 体验报单任务
     */
    function  checkTask($mobile){
        //校验用户是否存在任务        
        $this->quote_model->mobile=$mobile;
        $userid=$this->quote_model->checkUser();
        if($userid === false){
            return false;
        }
        //校验用户是否领取过任务
        $this->quote_model->userid=$userid;
        $taskid=$this->quote_model->checkTask();
    }
    
    
}
