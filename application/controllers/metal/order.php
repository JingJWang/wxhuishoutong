<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type:text/html;charset=utf-8;');
class Order extends CI_Controller {
    /**
     * 获取贵金属类型
     */
    function metalType(){
        $this->load->model('metal/metal_model');
        $type=$this->metal_model->metalType(); 
        if($type == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到贵金属产品信息!');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$type);
        }
    }
    /**
     * 获取贵金属产品选项
     */
    function metalOption(){
        $id=$this->input->post('id',true);
        if(is_numeric($id) == false){
            Universal::Output($this->config->item('request_fall'),'产品校验失败!');
        }
        $this->load->model('metal/metal_model');
        $this->metal_model->id=$id;
        $attr=$this->metal_model->metalOption();
        if($attr != false){
            $option=$this->metalInfo($attr);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有获取到产品选项信息!');
        }
        if($option == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到选项详细信息!');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$option);
        }
    }
    /**
     * 获取产品参数详细信息
     * 
     */
    function metalInfo($attr){
        $rbinfo=$this->metalData();
        $option=json_decode($attr['attr'],true);
        $title=array();
        $para=array();
        foreach ($option as $key=>$val){
            $para[$key]['name']=$rbinfo[$key];
            foreach ($val as $i=>$n){    
                $n=str_replace(array('[',']'),array('',''),$n);;
                if(array_key_exists($n,$rbinfo)){
                     $para[$key]['option'][$n]=$rbinfo[$n];
                }
            }
        }  
       return $para;
    }
    /**
     * 读取配置的选项信息
	 */
    function metalData(){
        $this->load->model('metal/metal_model');
        $info=$this->metal_model->metalInfo();
        if($info == false){
            return  false;
        }
        $rbinfo=array();
        foreach ($info as $k=>$v){
            $rbinfo[$v['alias']]=$v['name'];
            $rbinfo[$v['id']]=$v['info'];
        }       
        return $rbinfo;
    }
    /**
     * 保存贵金属订单
     * metal=5160&purity=178&metaltype=186&weight=0.001
     * @param  metal   int 产品
     * @param  purity  int 纯度
     * @param  metaltype int 产品分类
     * @param  weight  float 重量
     * @param  type  int  订单类型
     * @return json
     */
    function  submitMetal(){
        if(isset($_SESSION['metaldata'])){
            $option=json_decode($_SESSION['metaldata'],true);
        }else{
            $option=$this->checkData();
        }
        
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION,true);
        if($online === false){
            $_SESSION['metaldata']=json_encode($option);;
            $_SESSION['LoginBackUrl']='/index.php/metal/order/submitMetal';
            $url='/index.php/nonstandard/system/Login';
            Universal::Output($this->config->item('request_fall'),'您还没有登录正在跳转到登录页面!',$url);
        }
        $orderinfo=$this->metalOrderInfo($option['orderdata']);
        //获取产品内容
        $metalid=$option['option']['metal'];
        $this->load->model('metal/metal_model');
        $this->metal_model->id=$metalid;
        $type=$this->metal_model->metalOption();
        if($type == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到产品类型信息!');
        }
        //获取报价方案
        $this->metal_model->id=$type['id'];
        $plan=$this->metal_model->metalQuote();
        if($plan == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到产品价格信息!');
        }
        //计算订单价格
        $total=$this->orderPri($option,$plan);
        $option['ctype']=$this->ctype;
        if(is_numeric($total) === false){           
            Universal::Output($this->config->item('request_fall'));
        }
        $this->saveMetalOrder($option,$type,$total);
    }
    /**
     * 保存订单
     * 表h_order_nonstandard 增加一个字段标识 订单类型 1 库存  2 现金
     */
    function saveMetalOrder($option,$type,$total){
        $orderinfo=$this->metalOrderInfo($option['orderdata']);    
        $orderinfo['product']=$type['name'];
        $orderinfo['weight']=$this->weight;
        $orderinfo['original']=$this->original;
        $dealtype=$option['option']['type'];
        $orderinfo['type']= $option['option']['type'] == 1 ? '库存' : '现金';
        $orderinfo['total']=$total.'元';
        $number=Universal::create_ordrenumber();
        $option=array('number'=>$number,'name'=>$type['name'],
                'type'=>$dealtype,'orderinfo'=>$orderinfo,'ctype'=>$option['ctype'],'mobile'=>$option['option']['tel']);
        $this->load->model('metal/metal_model');
       
        $orderres=$this->metal_model->metalOrder($option);
        if($orderres == false){
             Universal::Output($this->config->item('request_fall'),'保存订单出现异常!');
        }               
        $offerred=$this->Metalquote($type['name'],$number,$total);
        if($orderres == false){
            Universal::Output($this->config->item('request_fall'),'保存订单内容出现异常!');
        }
        $userred=$this->metal_model->userMetal();
        if($userred == false){
             Universal::Output($this->config->item('request_fall'),'保存个人信息出现异常!');
        }
        if(isset($_SESSION['metaldata'])){
            unset($_SESSION['metaldata']);
        }
        $method=$this->input->is_ajax_request();
        if(!$method){              
              unset($_SESSION['LoginBackUrl']);
              $url='/view/gold/subsucc.html?id='.$number;
              header("Location:".$url);
        }
        Universal::Output($this->config->item('request_succ'),'','/view/gold/subsucc.html?id='.$number);
    }
    /**
     * 保存贵金属报价信息
     */
    function Metalquote($name,$number,$total){
        //获取回收商详情
        $this->load->model('nonstandard/quote_model');
        $this->quote_model->coopid='151215593588660';
        $coopinfo=$this->quote_model->GetCoopInfo();       
        $this->quote_model->order_name=$name;
        $this->quote_model->done_times=$coopinfo['0']['offer'];
        $this->quote_model->coop_name=$coopinfo['0']['coop_name'];
        $this->quote_model->coop_class=$coopinfo['0']['coop_class'];
        $this->quote_model->coop_auth=$coopinfo['0']['coop_auth'];
        $this->quote_model->user_id=$coopinfo['0']['coop_number'];
        $this->quote_model->coop_addr=$coopinfo['0']['coop_address'];
        $this->quote_model->order_id=$number;
        $this->quote_model->price=$total;
        $this->quote_model->status=2;
        $this->quote_model->service='快递回收';
        $this->quote_model->remark='';
        $this->quote_model->distance=0;
        $this->quote_model->lng=0;
        $this->quote_model->lat=0;
        $this->quote_model->plan_id='';
        $res=$this->quote_model->savePlanQuote();
        return $res;
    }
   
    /**
     * 校验用户提交的订单信息
     */
    function checkData() {
        $option=array();
        $metal=$this->input->post('metal',true);
        if(is_numeric($metal) == false){
            Universal::Output($this->config->item('request_fall'),'请选择产品信息!');
        }
        $option['metal']=$metal;
        $purity=$this->input->post('purity',true);
        if(is_numeric($purity) == false){
            Universal::Output($this->config->item('request_fall'),'请选择产品纯度信息!');
        }else{
            $orderdata['purity']=$purity;
        }
        $option['purity']=$purity;
        $metaltype=$this->input->post('metaltype',true);
        if(is_numeric($metaltype) == false){
            Universal::Output($this->config->item('request_fall'),'请选择产品分类信息!');
        }else {
            $orderdata['metaltype']=$metaltype;
        }
        $option['metaltype']=$metaltype;
        $weight=$this->input->post('weight',true);
        if(is_numeric($metal) == false){
            Universal::Output($this->config->item('request_fall'),'请选择产品重量信息!');
        }
        $option['weight']=$weight;
        $option['original']=$weight;
        $type=$this->input->post('type',true);
        if(is_numeric($type) == false){
            Universal::Output($this->config->item('request_fall'),'请选择提交订单类型信息!');
        }
        $option['type']=$type;
        $tel=$this->input->post('tel');
        if((isset($tel{11}) || !is_numeric($tel)) && !empty($tel)){
        	Universal::Output($this->config->item('request_fall'),'请输入正确的手机号码!');
        }
        $option['tel']=$tel; 
        return array('option'=>$option,'orderdata'=>$orderdata);
    }
    /**
     * 获取订单内容
     * @param  array $option 订单参数
     * @return  array $ordre 订单内容
     */
    function metalOrderInfo($option){
        $info=$this->metalData();
        foreach ($option as $key=>$val){
            if(array_key_exists($key,$info)){
                $ordre[$key]=$info[$val];
            }
        }
        return $ordre;
    }
    /**
     * 计算当前订单的价格
     */
    function reckonPri() {
        $option=$this->checkData();               
        //获取产品内容
        $metalid=$option['option']['metal'];
        $this->load->model('metal/metal_model');
        $this->metal_model->id=$metalid;
        $type=$this->metal_model->metalOption();
        if($type == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到产品类型信息!');
        }
        //获取报价方案
        $this->metal_model->id=$type['id'];
        $plan=$this->metal_model->metalQuote();
        if($plan == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到产品价格信息!');
        }
        //计算订单价格
        $total=$this->orderPri($option,$plan);
        if(is_numeric($total) === false){
            Universal::Output($this->config->item('request_fall'));
        }
        Universal::Output($this->config->item('request_succ'),'','',$total);
    }
    /**
     * 根据报价订单参数 报价方案计算价格
     * @param  array $option  订单参数
     * @param  array $plan  报价参数
     * @return int 价格参数
     */
    function orderPri($option,$plan){
        $metalid=$option['option']['metal'];
        $content=json_decode($plan['content'],true);
        //获取产品运算基础价格
        $metalpri=$this->goldData();
        //产品基础价格
        $this->pri=$metalpri[$metalid]['sellpri'];
        switch ($metalid){
            case '5292':
                $this->pri=$this->pri-5;
                $this->weightpri=$this->pri;
                $this->ctype=37;
                break;
            case '5294':
                $this->pri=$this->pri-0.8;
                $this->ctype=40;
                break;
            case '5293':
                $this->ctype=38;
                $this->pri=$this->pri-5;
                $this->weightpri=$this->pri;
                break;
        }
        //根据报价参数获取计算后的价格
        foreach ($option['orderdata'] as $key=>$val){            
            if($content[$val] != 0 || empty($content[$val]) === false){
                $this->reckonRule($key,$content[$val]);
            }
        }
        //重量千克转化为克
        $weight=$option['option']['weight'];
        //计算总价格 根据每克的价格
        $total=$this->pri*$weight;
        $this->original=$weight;
        if($metalid == 5292 ||$metalid == 5293){
            //换算黄金比例
            $this->weight=$total/$this->weightpri-0.005;
            $this->weight=round($this->weight,2);
        }else{
            $this->weight=$weight;
        }
        //价格保留2位小数点
        $total=round($total,2);
        return $total;
    }
    /**
     * 计算规则
     * @param  string  $key   报价参数key
     * @param  string  $info  报价参数value
     */
    function reckonRule($key,$info) {
        $rule=$this->getRole($info);
        switch ($rule){
            case '-1':
                if(strpos($info,'%')){
                     $ratio=str_replace(array('-','+','%'),array('','',''),$info);
                    if($key == 'purity'){
                          $this->pri=$this->pri*$ratio/100;
                    }else{
                          $this->pri=$this->pri-$this->pri*$ratio/100;
                    }
                }else{
                    $ratio=str_replace(array('-','+','%'),array('','',''),$info);
                    $this->pri=$this->pri-$ratio;
                }
                break;
            case '1':
                if(strpos($info,'%')){
                    $ratio=str_replace(array('-','+','%'),array('','',''),$info);
                    $this->pri=$this->pri+$this->pri*$ratio/100;
                }else{
                    $ratio=str_replace(array('-','+','%'),array('','',''),$info);
                    $this->pri=$this->pri+$ratio;
                }
                break;
        }
    }
    /**
     * 获取运算方式
     * @param  string $rule 报价参数
     * @return  int  返回-1 || 1   返回值1 按照加法运算  -1 按照减法运算
     */
    function getRole($rule) {
        if(strpos($rule,'-') !== false){
            return '-1';   
        }
        if(strpos($rule,'+') !== false){
            return '1';
        }
    }
    /**
     * 获取当前金价
     */
    function matelPri(){
       $id=$this->input->post('id',true);
        //获取产品类型  
       $data=$this->goldData();       
       if($data !== false){
           $id === false ? $info='' :$info=$data[$id];
           Universal::Output($this->config->item('request_succ'),'','',$info);
       }else{
           Universal::Output($this->config->item('request_fall'),'没有获取到黄金价格');
       }
    }    
    /**
     * 订单提交成功查看订单信息
     */
    function orderInfo(){
        $id=$this->input->post('id',true);
        if(is_numeric($id) == false){
            Universal::Output($this->config->item('request_fall'),'没有成功获取到订单编号!');
        }
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->id=$id;
        $info=$this->metal_model->orderInfo();
        if($info === false){
            Universal::Output($this->config->item('request_fall'),'没有获取到订单详情!');
        }
        $offer=$this->metal_model->metalOffer($id);
        if($offer === false){
            Universal::Output($this->config->item('request_fall'),'没有获取订单报价详情!');
        }
        $option=json_decode($info['content'],true);
        $info['time']=date('Y-m-d H:i:s',$info['time']);
        $option['uppri']=$offer['uppri'];
        $info['isagree']=empty($offer['isagree']) ? '未确认' : '已确认'; 
        $info['confirm']=empty($offer['uppri'])  ? 0 : 1; 
        $info['cancel']=empty($offer['isagree'])  ? 0 : 1;
        $info['dealtype']= $info['dealtype'] == 1 ? '库存' : '现金';
        switch ($info['status']){
            case '2': 
                $info['type']='等待预支付';
                $info['permit']='1';
                $info['addres']='0';
                break;
            case '3':
                $info['type']='等待交易';
                $info['permit']='0';
                $info['addres']='1';
                break;
            case '10':
                $info['type']='订单已成交';
                $info['addres']='0';
                break;
            case '-1':
                $info['type']='订单已取消';
                $info['addres']='0';
                break;
        }
        $info['content']=$option;
        Universal::Output($this->config->item('request_succ'),'','',$info);
    }    
    /**
     * 贵金属交易价格列表
     * 
     */
    function metalPriceList(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        //读取用户贵金属交易详情
        $this->load->model('metal/metal_model'); 
        //读取用户贵金属库存列表
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $respmetal=$this->metal_model->metalStocKInfo();
        if($respmetal == false){
            Universal::Output($this->config->item('request_fall'),'没有读取到贵金属交易列表!');
        }       
        //读取贵金属交易价格列表        
        $resp=$this->metal_model->priceList();
        if($resp == false){
            Universal::Output($this->config->item('request_fall'),'没有读取到贵金属交易列表!');
        }
        $resp=$this->delaInfoList($resp);
        Universal::Output($this->config->item('request_succ'),'','',$resp);        
    }    
    /**
     * 贵金属交易列表数据获取
     */
    function delaInfoList($stock) {
        $info=$this->userMetalInfo();
        $usermetal=array('gold'=>0,'platinum'=>0,'silver'=>0);
        foreach ($stock as $key=>$val){
            switch ($val['alias']){
                case 'gold':
                    $stock=$info['gold']['buy']['weight']-$info['gold']['sold']['weight'];
                    $stock=round($stock,2);
                    if($info['gold']['buy']['pri'] != 0 && $info['gold']['buy']['weight'] !=0){
                        $average=$info['gold']['buy']['pri']/$info['gold']['buy']['weight'];
                    }else{
                        $average=0;
                    }
                    if($average !=0 ){
                        $rofit=$val['sold']-$average;
                    }else{
                        $rofit=0;
                    }
                    $loss=($val['sold']-$average)*$stock;
                    if($stock == 0){
                        $average=0;
                        $rofit=0;
                        $loss=0;
                    }
                    $usermetal['gold']=array(
                            'id'=>$val['id'],
                            'stock'=>$stock,
                            'average'=>round($average,2),
                            'rofit'=>round($rofit,2),
                            'loss'=>round($loss,2),
                            'buy'=>$val['buy'],
                            'sold'=>$val['sold']
                    );
                    break;
                case 'platinum':
                    $stock=$info['platinum']['buy']['weight']-$info['platinum']['sold']['weight'];
                    $stock=round($stock,2);
                    if($info['platinum']['buy']['pri'] != 0 && $info['platinum']['buy']['weight'] !=0){
                        $average=$info['platinum']['buy']['pri']/$info['platinum']['buy']['weight'];
                    }else{
                        $average=0;
                    }
                    if($average !=0 ){
                         $rofit=$val['sold']-$average;
                    }else{
                        $rofit=0;
                    }
                    $loss=($val['sold']-$average)*$stock;
                    if($stock == 0){
                        $average=0;
                        $rofit=0;
                        $loss=0;
                    }
                    $usermetal['platinum']=array(
                            'id'=>$val['id'],
                            'stock'=>$stock,
                            'average'=>round($average,2),
                            'rofit'=>round($rofit,2),
                            'loss'=>round($loss,2),
                            'buy'=>$val['buy'],
                            'sold'=>$val['sold']
                    );
                    break;
                case 'silver':                  
                    $stock=$info['silver']['buy']['weight']-$info['silver']['sold']['weight']; 
                    $stock=round($stock,2);
                    if($info['silver']['buy']['pri'] != 0 && $info['silver']['buy']['weight'] !=0){
                        $average=$info['silver']['buy']['pri']/$info['silver']['buy']['weight'];
                    }else{
                        $average=0;
                    }
                    if($average !=0 ){
                        $rofit=$val['sold']-$average;
                    }else{
                        $rofit=0;
                    }
                    $loss=($val['sold']-$average)*$stock;
                    if($stock == 0){
                         $average=0;
                         $rofit=0;
                         $loss=0;
                    }
                    $usermetal['silver']=array(
                            'id'=>$val['id'],
                            'stock'=>$stock,                            
                            'average'=>round($average,2),
                            'rofit'=>round($rofit,2),
                            'loss'=>round($loss,2),
                            'buy'=>$val['buy'],
                            'sold'=>$val['sold']
                    );
                    break;
            }
        }  
       return $usermetal;  
    }
    /**
     * 计算用户当前贵金属买进 卖出交易详情
     */
    function userMetalInfo(){
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $respinfo=$this->metal_model->dealInfo();
        $gold=array('buy'=>array('weight'=>0,'pri'=>0),'sold'=>array('weight'=>0,'pri'=>0));
        $platinum=array('buy'=>array('weight'=>0,'pri'=>0),'sold'=>array('weight'=>0,'pri'=>0));
        $silver=array('buy'=>array('weight'=>0,'pri'=>0),'sold'=>array('weight'=>0,'pri'=>0));        
        $usermetal=array('gold'=>$gold,'platinum'=>$platinum,'silver'=>$silver);
        if($respinfo == false){
            return $usermetal;
        }
        foreach ($respinfo as $k=>$v){
            $content=json_decode($v['record_content'],true);
            switch ($v['record_type']){
                case '1':
                    if($v['record_dealtype'] == 1){
                        $gold['buy']=array(
                                'weight'=>$gold['buy']['weight']+$content['weight'],
                                'pri'=>$gold['buy']['pri']+$content['total'],
                        );
                    }else{
                        $gold['sold']=array(
                                'weight'=>$gold['sold']['weight']+$content['weight'],
                                'pri'=>$gold['sold']['pri']+$content['total'],
                        );
                    }
                    break;
                case '2':
                    if($v['record_dealtype'] == 1){
                        $platinum['buy']=array(
                                'weight'=>$platinum['buy']['weight']+$content['weight'],
                                'pri'=>$platinum['buy']['pri']+$content['total'],
                        );
                    }else{
                        $platinum['sold']=array(
                                'weight'=>$platinum['sold']['weight']+$content['weight'],
                                'pri'=>$platinum['sold']['pri']+$content['total'],
                        );
        
                    }
                    break;
                case '3':
                    if($v['record_dealtype'] == 1){
                        $silver['buy']=array(
                                'weight'=>$silver['buy']['weight']+$content['weight'],
                                'pri'=>$silver['buy']['pri']+$content['total'],
                        );
                    }else{
                        $silver['sold']=array(
                                'weight'=>$silver['sold']['weight']+$content['weight'],
                                'pri'=>$silver['sold']['pri']+$content['total'],
                        );
                    }
                    break;
            }
        }
        $usermetal=array('gold'=>$gold,'platinum'=>$platinum,'silver'=>$silver);
        return $usermetal;
    }
    /**
     * 
     * 贵金属卖出交易
     */
    function metalSellout(){
        //校验当前是否是交易时间
        if( date('w') == 6  || date('w') == 0 ){
                Universal::Output($this->config->item('request_fall'),'当前时间不能交易!');
        }
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        //校验提交数据
        $reqinfo=$this->checkSelloutData();
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->reqinfo=$reqinfo;
        $res=$this->metal_model->metalSellout();
        if($res === false){
            if(isset($this->metal_model->errcode) && $this->metal_model->errcode == 1){
                $msg='库存不足!';
            }else{
                $msg='出现异常!';
            }
            Universal::Output($this->config->item('request_fall'),$msg);
        
        }else{
            Universal::Output($this->config->item('request_succ'),'已卖出','/index.php/nonstandard/center/ViewCenter');
        }
    }    
    /**
     * 贵金属交易卖出 校验数据
     */
    function checkSelloutData(){
        $option=array();
        $id=$this->input->post('id',true);
        if(is_numeric($id) == false){
            Universal::Output($this->config->item('request_fall'),'没有获取到卖出卖出的产品分类!');
        }
        $option['id']=$id;
        $weight=$this->input->post('weight',true);
        if(is_numeric($weight) == false || $weight < 0){
            Universal::Output($this->config->item('request_fall'),'买有获取到卖出的重量!');
        }        
        if(preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $weight) === false){
            Universal::Output($this->config->item('request_fall'),'买有获取到卖出的重量!');
        }
        $option['weight']=$weight;
        return $option;
    }
    /**
     * 贵金属交易列表获取用户余额和详情
     */
    function dealInfo(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        $id=$this->input->post('id',true);        
        if(!is_numeric($id)){
            Universal::Output($this->config->item('fall'),'没有获取到贵金属分类!');
        }
        $dealtype=$this->input->post('dealtype',true);
        if(!is_numeric($dealtype)){
            Universal::Output($this->config->item('fall'),'没有获取到贵金属分类!');
        }
        //查询用户的余额
        $this->load->model('nonstandard/wxuser_model');
        $userinfo=$this->wxuser_model->GetBalance($_SESSION['userinfo']['user_id']);
        if($userinfo == false){
            Universal::Output($this->config->item('fall'),'没有获取到用户详细信息!');
        }
        $resp['balance']=$userinfo['balance'];
        //读取用户库存
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $stock=$this->metal_model->metalStocKInfo();
        if($stock == false){
            Universal::Output($this->config->item('fall'),'没有获取到用户贵金属库存信息!');
        }
        //读取贵金属交易价格列表       
        $priinfo=$this->metal_model->priceList(); 
        foreach ($priinfo as $key=>$val){
            if($id == $val['id']){
                  $key=$val['id'];
                  $stock=$stock[$val['alias']];
                  $metal=$dealtype == 1 ? $val['buy'] : $val['sold'];
                  break;
            }else{
                  $key=false;
            }   
        }
        if($key === false){
            Universal::Output($this->config->item('request_fall'),'没有获取到贵金属分类!');
        }
        $resp['stock']=$stock;
        $resp['price']=$metal;
        Universal::Output($this->config->item('request_succ'),'','',$resp);
    }
    /**
     * 贵金属提货 库存查询
     */
    function metalStockInfo(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        //读取用户库存
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $stock=$this->metal_model->metalStocKInfo();
        if($stock == false){
            Universal::Output($this->config->item('fall'),'没有获取到用户贵金属库存信息!');
        }
        Universal::Output($this->config->item('request_succ'),'','',$stock);
    }    
    /**
     * 贵金属提货 获取地址
     */
    function addresStock(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        $id=$this->input->post('id',true);
        if(!is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'没有获取到地址信息!');
        }
        //读取用户地址
        $this->load->model('shop/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $this->metal_model->addres=$id;
        $addres=$this->metal_model->useraddres();
        if($addres == false){
            Universal::Output($this->config->item('request_fall'),'');
        }
        Universal::Output($this->config->item('request_succ'),'','',$addres);
    }
    /**
     * 贵金属交易提货订单
     */
    function submitStock() {
       $data=$this->checkStockData();
       //校验登录权限
       $this->load->model('auto/userauth_model');       
       $online=$this->userauth_model->UserCheck(2,$_SESSION);
       //读取用户贵金属库存
       $this->load->model('metal/metal_model');
       $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
       $stock=$this->metal_model->metalStocKInfo();
       if($stock == false){
           Universal::Output($this->config->item('request_fall'),'读取库存出现异常');
       }
       foreach ($stock as $key=>$val){
           if($data['data'][$key] > $val){
               Universal::Output($this->config->item('request_fall'),'库存出现异常');
           }
       }
       //添加订单
       $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
       $this->metal_model->content=$data['content'];
       $this->metal_model->stock=$stock;
       $this->metal_model->metal=$data['metal'];
       $this->metal_model->addres=$data['addres'];
       $res=$this->metal_model->submitStock();
        if($res == false){
            Universal::Output($this->config->item('request_fall'),'提交提货订单出现异常!');
        }
        Universal::Output($this->config->item('request_succ'),'','/view/gold/record.html');
       
    }
    /**
     * 贵金属交易 检验提货订单提交数据
     */
    function checkStockData(){
        $orderinfo=array();
        $data=$this->input->post();
        if(empty($data['gold']) && empty($data['platinum']) && empty($data['silver'])){
            Universal::Output($this->config->item('request_fall'),'内容不能为空!');
        }
        if($data['gold'] < 10 && $data['platinum'] < 10 && $data['silver'] < 10){
            Universal::Output($this->config->item('request_fall'),'提货重量不能小于10g');
        }
        $data['gold']= $data['gold'] < 10 ? 0 : $data['gold'];
        $data['platinum']= $data['platinum'] < 10 ? 0 : $data['platinum'];
        $data['silver']= $data['silver'] < 10 ? 0 : $data['silver'];
        foreach ($data as $k=>$v){           
           if(!is_numeric($v)){
               Universal::Output($this->config->item('request_fall'),'非法请求!');
           }else{       
               $option=array('gold'=>'黄金','platinum'=>'铂金','silver'=>'白银');        
               if(array_key_exists($k,$option)){
                   $orderinfo[$option[$k]]=$v.'克';
               };
           }
        }
        $option=array('gold'=>'黄金','platinum'=>'铂金','silver'=>'白银');
        $resp=array();
        $resp['data']=$data;
        $resp['content']=json_encode($orderinfo);
        $resp['metal']=array('gold'=>$data['gold'],'platinum'=>$data['platinum'],'silver'=>$data['silver']);
        $resp['addres']=$data['addres'];
        return $resp;
    }    
    /**
     * 读取提货记录
     */
    function stockRecord(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        //读取用户贵金属库存
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $stock=$this->metal_model->stockRecord();       
        if($stock == false){
            Universal::Output($this->config->item('request_fall'),'读取提货记录出现异常');
        }
        foreach ($stock as $key=>$val){
            switch ($val['state']){
                case '1':
                    $stock[$key]['state']='未发货';
                    break;
                case '2':
                    $stock[$key]['state']='已发货';
                    break;
                case '3':
                    $stock[$key]['state']='已签收';
                    break;
            }
           $stock[$key]['content']=json_decode($val['content'],true);
           $stock[$key]['time']=date('m-d H:i:s',$val['jointime']);
        }
        Universal::Output($this->config->item('request_succ'),'','',$stock);
    }
    /**
     * 贵金属交易记录
     */
    function dealRecord(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $online=$this->userauth_model->UserCheck(2,$_SESSION);
        //读取用户贵金属库存
        $this->load->model('metal/metal_model');
        $this->metal_model->userid=$_SESSION['userinfo']['user_id'];
        $record=$this->metal_model->dealRecord();
        if($record == false){
            Universal::Output($this->config->item('request_fall'),'读取交易记录出现异常');
        }  
        foreach ($record as $key=>$val){
            $record[$key]['content']=json_decode($val['content'],true);
            $record[$key]['jointime']=date('Y-m-d H:i:s',$val['jointime']);
            $record[$key]['dealtype']= $val['dealtype'] == 1 ? '买进':'卖出';
            $record[$key]['style']= $val['dealtype'] == 1 ? 'tradingBuy':'tradingSell';
            $record[$key]['sold']=$this->getPrice($val['type']);
            $record[$key]['url']=$this->getDelaUrl($val['type']);
        }
        Universal::Output($this->config->item('request_succ'),'','',$record);
        
    }
    function getPrice($type){
        //读取贵金属交易价格列表
        $this->load->model('metal/metal_model');
        $resp=$this->metal_model->priceList();
        if($resp == false){
            Universal::Output($this->config->item('request_fall'),'读取交易价格出现异常');
        }
        switch ($type){
            case '1':
                 $price=$resp['0']['sold'];
                break;
            case '2':
                 $price=$resp['1']['sold'];
                break;
            case '3':
                 $price=$resp['2']['sold'];
                break;
        }
        return $price;
    }
    function getDelaUrl($type){
       $url='/view/gold/dealinfo.html?id=';
       switch ($type){
           case '1':
               $url=$url.'5160';
               break;
           case '2':
               $url=$url.'5161';
               break;
           case '3':
               $url=$url.'5162';
               break;
       }
       return $url;
    }
    /**
     * 请求聚合数据获取当前黄金单价
     */
    function goldData(){     
        //聚合数据请求地址
        $url = "http://web.juhe.cn:8080/finance/gold/bankgold";
        $params = array(
                "key" =>'e915db1a4dbacc02b0161ef946ca5901',
                "v" => "",
        );
        $paramstring = http_build_query($params);
        $content = $this->curlRequest($url,$paramstring);
        $result = json_decode($content,true);
        if(is_array($result)){
            if($result['resultcode'] !='200'){
               return  false;
            }else{               
               foreach ($result['result']['0'] as $key=>$val){
                   switch ($key){
                       case '5':
                           $metal['5292']=array('variety'=>'黄金','sellpri'=>$val['sellpri'],'time'=>$val['time']);
                           break;
                       case '6':
                           $metal['5294']=array('variety'=>'白银','sellpri'=>$val['sellpri'],'time'=>$val['time']);
                           break;
                       case '7':
                           $metal['5293']=array('variety'=>'铂金','sellpri'=>$val['sellpri'],'time'=>$val['time']);
                           break;
                   }  
               }
               $this->upMetalPri($metal);
               return $metal;
            }
        }else{
            return false;
        }
    }
    /**
     * 更新贵金属交易价格
     */
    function upMetalPri($metal){
        $gold=array(
                'price_buy'=>$metal['5292']['sellpri'],
                'price_sold'=>$metal['5292']['sellpri']-5,
                'price_uptime'=>time());
        $gold_goods=array(
                'goods_ppri'=>$metal['5292']['sellpri']*100
        );
        $platinum=array(
                'price_buy'=>$metal['5293']['sellpri'],
                'price_sold'=>$metal['5293']['sellpri']-5,
                'price_uptime'=>time()                
        );
        $platinum_goods=array(
                'goods_ppri'=>$metal['5293']['sellpri']*100
        );
        $silver=array(
                'price_buy'=>$metal['5294']['sellpri'],
                'price_sold'=>$metal['5294']['sellpri']-0.8,
                'price_uptime'=>time()                
        );
        $silver_goods=array(
                'goods_ppri'=>$metal['5294']['sellpri']*100
        );
        //修改贵金属交易价格
        $this->load->model('metal/metal_model');        
        $this->metal_model->gold=$gold;
        $this->metal_model->platinum=$platinum;
        $this->metal_model->silver=$silver;
        $this->metal_model->goldgoods=$gold_goods;
        $this->metal_model->platinumgoods=$platinum_goods;
        $this->metal_model->silvergoods=$silver_goods;
        $resp=$this->metal_model->upMetalPrice(); 
    }    
    /**
     * curl 请求
     */
    function curlRequest($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if( $ispost ){
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }else{
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
    
}