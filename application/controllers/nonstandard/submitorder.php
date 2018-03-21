<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
header('content-type:text/html;charset=utf-8');
class Submitorder extends CI_Controller{
    /**
     * 加载db类 提交订单model
     */
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('array');
        $this->load->model('nonstandard/wxuser_model');
        $this->load->model('nonstandard/submitorder_model');
    }  
    /**
     * 搜索型号
     */
    function BrandSearch(){
        //校验是否已经绑定手机号码
        $this->load->view('nonstandard/brasearch');
    }
    /**
     * 获取搜索数据
     */
    function GetSearchType(){
        $coulms=array('keyword','type','page');
        $request=elements($coulms, $this->input->post(), '');
        if(empty($request['keyword'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if(!is_numeric($request['type'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if($request['page'] != 0 && $request['page'] != 1){
           $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if(empty($request['keyword'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if($request['page'] == 0){
            $page='limit 0,10';
        }
        if($request['page'] == 1){
            $page='';
        }
        $str_key=trim($request['keyword'],' ');
        //!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$str) utf 下的数组  汉子 字母
        $where=''; 
        /* $arr_key=$this->SplitWord($str_key);
        $where='';
        foreach ($arr_key  as $key=>$val){
            $where .= ' and a.types_name like "%'.$val.'%" ';
        } */
        if(strpos($str_key,' ')){
            $str_key=str_replace(' ',',',$str_key);
            $arr_key=explode(',',$str_key);
            foreach ($arr_key  as $key=>$val){
                $where .= ' and a.types_name like "%'.$val.'%" ';
            }
        }else{
            $arr_key=$this->SplitWord($str_key);
            foreach ($arr_key  as $key=>$val){
                $where .= ' and a.types_name like "%'.$val.'%" ';
            }
        }
        $sql='select a.types_id as id ,a.types_name as name from h_electronic_types 
              as a left join h_brand as b on  a.brand_id=b.brand_id  where a.types_status=1 and
              b.brand_classification='.$request['type'].$where.$page;
        $query=$this->db->query($sql);
        if($query->num_rows() < 1){
            $response=array('status'=>'3000','msg'=>'','url'=>'','data'=>0);
            echo json_encode($response);exit;
        }
        $data=$query->result_array();
        $response=array('status'=>'1000','msg'=>'','url'=>'','data'=>$data);
            echo json_encode($response);exit;
    } 
    /**
     * 中文二元分词 编码utf-8
     */
    function SplitWord($str){
        $cstr = array();
        $search = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`",
                "^", "(", ")", "?", "-", "\t", "\n", "'", "<", ">",
                "\r", "\r\n", "{1}quot;", "&", "%", "#", "@", "+",
                "=", "{", "}", "[", "]", "：", "）", "（", "．", "。",
                "，", "！", "；", "“", "”", "‘", "’", "［", "］", "、",
                "—", "　", "《", "》", "－", "…", "【", "】",);
        $str = str_replace($search, " ", $str);
        preg_match_all("/[a-zA-Z]+/", $str, $estr);
        preg_match_all("/[0-9]+/", $str, $nstr);
        $str = preg_replace("/[0-9a-zA-Z]+/", " ", $str);
        $str = preg_replace("/\s{2,}/", " ", $str);
        $str = explode(" ", trim($str));
        foreach ($str as $s) {
            $l = strlen($s);
            $bf = null;
            for ($i= 0; $i< $l; $i=$i+3) {
                $ns1 = $s{$i}.$s{$i+1}.$s{$i+2};
                if (isset($s{$i+3})) {
                    $ns2 = $s{$i+3}.$s{$i+4}.$s{$i+5};
                    if (preg_match("/[\x80-\xff]{3}/",$ns2)) $cstr[] = $ns1.$ns2;
                } else if ($i == 0) {
                    $cstr[] = $ns1;
                }
            }
        }
        $estr = isset($estr[0])?$estr[0]:array();
        $nstr = isset($nstr[0])?$nstr[0]:array();
        return array_merge($nstr,$estr,$cstr);
    }
    /**
     * 显示电子产品 分类 品牌 型号表单
     */
    function  ViewBrand(){ 
        $this->load->model('shop/flow_model');
        $this->flow_model->isfromSpread();
        $proid=$this->input->get('id',true);  
        if(empty($proid) || !is_numeric($proid)){
            Universal::Output($this->config->item('request_fall'),'本次请求不符合规范!');
        }
        switch ($proid){
            case '5':
                $view=array('proid'=>'5','proname'=>'手机');
                break;
            case '7':
                $view=array('proid'=>'7','proname'=>'平板电脑');
                break;
            default:
                $view=array('proid'=>'5','proname'=>'手机');
                break;
        }        
        $this->load->view('nonstandard/digittype',$view);
    }
    /**
     * 通过搜索电子产品
     */
    function GetTypeName(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $coulms=array('typeid');
        $request=elements($coulms, $this->input->get(), '');
        $sql='select a.types_id as typeid,a.types_name as typename,b.brand_id as 
              braid,b.brand_name as braname,c.product_name as proname,c.product_id 
              as proid from h_electronic_types as a left join h_brand as b on 
              a.brand_id=b.brand_id left join h_order_product as c on 
              b.brand_classification=c.product_id  where a.types_id='.$request['typeid'];
        $query=$this->db->query($sql);
        $brand=$query->row_array();
        $View['proname']=$brand['proname'];
        $View['braname']=$brand['braname'];
        $View['typename']=$brand['typename'];
        $_SESSION['submitorder']['brand']=$brand;
        //数码属性
        $attrkey=$this->config->item('electronic_attribute_key');
        $attrval=$this->config->item('electronic_attribute_val');
        $attrstyle=$this->config->item('electronic_attribute_style');
        $View['attribute']=array('key'=>$attrkey[$brand['proid']],
                                 'val'=>$attrval[$brand['proid']],
                                 'style'=>$attrstyle[$brand['proid']]);
        $this->load->view('nonstandard/digitattr',$View);
    }
    /**
     * 显示 电子产品属性选项  保存选择的 分类 品牌 型号
     * @param   int     proid        产品分类
     * @param   string  proname      产品名称
     * @param   int     braid        品牌id
     * @param   string  braname      品牌名称
     * @param   int     typeid       型号id     
     * @param   string  typename     型号名称  
     */ 
    function  ViewAttr(){
        //校验传递的参数
        $typeid=$this->input->get('id',true);
        if(empty($typeid) || !is_numeric($typeid) || isset($typeid{10})){
            Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
        }
        //获取js sdk 配置
        $this->load->model('common/wxcode_model');
        $view['signPackage']=$this->wxcode_model->GetSignPackage();   
        //获取当前的品牌  型号 分类信息
        $this->load->model('nonstandard/option_model');
        $this->option_model->typeid=$typeid;
        $proinfo=$this->option_model->getProInfo();
        if($proinfo === false){
            Universal::Output($this->config->item('request_fall'),'没有获取到产品型号信息!');
        }else{
            $view['typename']=$proinfo['0']['cname'];
        }
        //校验当前的信号是否存在报价方案
        $this->load->model('nonstandard/quote_model');
        $this->quote_model->typeid=$typeid;
        $plan=$this->quote_model->checkQuote();
        if($plan){
            $this->load->view('nonstandard/attrplan',$view);
            return '';
        }  
        //校验产品类型是否是笔记本电脑
        if($proinfo['0']['pid'] ==6 ){
            $list=$this->submitorder_model->Getcpu($brand['typename']);
            if(empty($list)){
                $view['cpu']='';
            }else{
                $view['cpu']=explode(',', $list['0']['configure_cpu']);
            }
        }
        //分类 品牌 型号信息
        $view['proname']=$proinfo['0']['pname'];
        $view['braname']=$proinfo['0']['bname'];
        $view['typename']=$proinfo['0']['cname'];
        $view['proid']=$proinfo['0']['pid'];
        //数码属性
        $attrkey=$this->config->item('electronic_attribute_key');
        $attrval=$this->config->item('electronic_attribute_val');
        $attrstyle=$this->config->item('electronic_attribute_style');
        $view['attribute']=array('key'=>$attrkey[$proinfo['0']['pid']],
                                 'val'=>$attrval[$proinfo['0']['pid']],
                                 'style'=>$attrstyle[$proinfo['0']['pid']]
        );       
        $this->load->view('nonstandard/digitattr',$view);
    }
    /**
     * 当某一型号存在自动报价方案
     * @param  int  id   型号id
     * @return  array  参数列表
     * 
     */
    function quotePlan(){
        //校验传递的参数
        $typeid=$this->input->post('id',true);
        if(empty($typeid) || !is_numeric($typeid) || isset($typeid{10})){
            Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
        }
        //获取js sdk 配置
        $this->load->model('common/wxcode_model');
        $view['signPackage']=$this->wxcode_model->GetSignPackage();
        //获取保存的参数信息
        $this->load->model('nonstandard/quote_model');
        $this->quote_model->typeid=$typeid;
        //查询型号参数配置
        $result=$this->quote_model->getOption();
        $attr=json_decode($result['0']['types_attr'],true);
        //查询参数信息
        $option=$this->quote_model->getTypeAttr();
        foreach ($attr as $k=>$v){
            //$temp=json_decode($v,true);
            foreach ($v as $i=>$n){
                $attr[$k][$i]=str_replace(array('[',']'),array('',''),$n);
            }
        }
        $model=array();
        foreach ($option['model'] as $key=>$val){
            $model[$val['alias']]=array('model'=>$val['model'],'type'=>$val['type'],
                    'name'=>$val['name'],'logic'=>$val['logic']);
        }
        $info=array();
        foreach ($option['info'] as $key=>$val){
            $info[$val['id']]=$val['info'];
        }
        $response=array('attr'=>$attr,'model'=>$model,'info'=>$info);
        
        Universal::Output($this->config->item('request_succ'),'','',$response);
    }
    /**
     * 提交自动订单
     * @param   int      id       型号id
     * @param   array    data     参数属性    
     * @return  json  返回添加订单结果
      */
    function planOrder(){     
		 //获取参数
        $data=$this->input->post(); 
		$callback='';
        if (isset($data['select'])) {
            $callback=$data['select'];
            unset($data['select']);
        }
        if(empty($data) || !is_array($data)){
            Universal::Output($this->config->item('request_fall'),'没有接受到您的选择项');
        }
        $result=$this->handleData($data);
        //校验登录权限
        $this->load->model('auto/userauth_model');
        if (!$this->userauth_model->UserIsLoginJump('/index.php/nonstandard/submitorder/ViewAttr?id='.$data['id'].'&select='.$callback,true)) {
            Universal::Output($this->config->item('request_fall'),'请登录后再次报单','/index.php/nonstandard/system/Login');
        }
        //校验当前用户本周报单是否超过限制
        /* $this->submitorder_model->mobile=$_SESSION['userinfo']['user_mobile'];
        $this->submitorder_model->userid=$_SESSION['userinfo']['user_id'];
        $num=$this->submitorder_model->checkNum();       
        if($num){           
            Universal::Output($this->config->item('request_fall'),'本周报单数量已经超过限制!');
        } */
        //获取当前的品牌  型号 分类信息
        $this->load->model('nonstandard/option_model');
        $this->option_model->typeid=$data['id'];
        $proinfo=$this->option_model->getProInfo();
        if($proinfo === false){
            Universal::Output($this->config->item('request_fall'),'获取参数出现异常!');
        }
		/* //校验接口权限
        $this->load->model('auto/crequest_model');
        $this->crequest_model->userid=$_SESSION['userinfo']['user_id'];
        $this->crequest_model->fname='planOrder';
        $this->crequest_model->requestAuth();  */       
        //添加订单
        $this->load->model('shop/goods_model');
        if(!isset($_SESSION['userinfo']['user_invitation']) || $_SESSION['userinfo']['user_invitation'] =="" ||
            $_SESSION['userinfo']['user_invitation']==false ){
            $this->goods_model->uid=$_SESSION['userinfo']['user_id'];
            $res=$this->goods_model->getInvitation();
            $invitation = $res['0']['inv'];
        }else{
            $invitation = $_SESSION['userinfo']['user_invitation'];
        }
        $this->submitorder_model->id=$data['id'];
        $this->submitorder_model->typename=$proinfo['0']['cname'];
        $this->submitorder_model->pid=$proinfo['0']['pid'];
        $this->submitorder_model->mobile=$_SESSION['userinfo']['user_mobile'];
        $this->submitorder_model->openid=$_SESSION['userinfo']['user_openid'];
        $this->submitorder_model->userid=$_SESSION['userinfo']['user_id'];
		$this->submitorder_model->invitation=$invitation;
        $this->submitorder_model->attr=json_encode($result['order']);
        $this->submitorder_model->plan=json_encode($result['plan']);
        $this->submitorder_model->ordertype=0;
        $res=$this->submitorder_model->savePlanOrder();
        if($res){
            $orderid=$this->submitorder_model->number; 
            //校验接口权限
            $this->load->model('auto/crequest_model');
            $this->crequest_model->userid=$_SESSION['userinfo']['user_id'];
            $this->crequest_model->fname='planOrder';
            $this->crequest_model->unLock();
            $url=$this->config->item('url_quotelist_succ').'?id='.$orderid;
            Universal::Output($this->config->item('request_succ'),'',$url);
        }else{
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('edit_order_fall'));
            echo json_encode($response);exit;
        }
    }
    /**
     * 处理自动报价的数据
     * @param   array  data 报价数据
     * @return  成功返回array | 失败输出 json  失败原因 
     */
    function handleData($data){
        //去除数组中为空的选项
        array_filter($data);
        //校验是否存在 多选
        if(array_key_exists('other',$data)){
            $other=$data['other'];
            unset($data['other']);
        }
        if(empty($data)){
            Universal::Output($this->config->item('request_fall'),'本次选择的属性不符合规范');
        }
        if(empty($data['longitude']) || empty($data['latitude'])){
            unset($data['longitude']);
            unset($data['latitude']);
        }
        //校验参数
        $attr=array();
        //校验请求的参数是否整数
        foreach ($data as $k=>$v){
            if(!is_numeric($v)){
                Universal::Output($this->config->item('request_fall'),'您还没有选择您手机的属性信息');
            }else{
                $attr[$k]=$v;
            }
        }
        //校验时候存在多选参数 存在转换成数组
		$other_temp=Universal::safe_replace($other);
        if(isset($other_temp) && empty($other_temp) === false){
            $sre_other=Universal::safe_replace($other_temp);
            if(!is_numeric($other_temp)){
                Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
            }
            $other=str_replace(array(' '),array(''),$other);
            $other=trim($other,',');
            $other=explode(',',$other);
        }
        $this->submitorder_model->latitude='0';
        $this->submitorder_model->longitude='0';
        //删除不用于订单内容中属性
        unset($attr['longitude']);
        unset($attr['latitude']);
        unset($attr['id']);
        //获取参数内容
        $this->load->model('nonstandard/quote_model');
        $info=$this->quote_model->GetOptionInfo();
        if(!$info){            
            Universal::Output($this->config->item('request_fall'),'获取参数内容详情出现异常!');
        }
        //转换参数内容数据格式
        $content=array();
        foreach ($info as $k=>$v){
            $content[$v['id']]=$v['info'];
        }
        //获取用于自动报价的参数
        $attrinfo=array();
        foreach ($attr as $k=>$v){
            if(!array_key_exists($v,$content)){
                Universal::Output($this->config->item('request_fall'),'获取参数详情出现异常!');
            }
            $attrinfo[$k]=$content[$v];
        }
        $attr['other']=$other;
        $temp_str='';
        //获取用于订单详情的内容
        if(is_array($other)){
            foreach ($other as $k=>$v){
                if(!array_key_exists($v,$content)){
                    Universal::Output($this->config->item('request_fall'),'获取订单参数详情出现异常!');
                }
                $temp_str.= $content[$v].',';
            }
            $attrinfo['other']=trim($temp_str,',');
        }else{
            $attrinfo['other']='';
        }
        $response=array('order'=>$attrinfo,'plan'=>$attr);
        return $response;
    }
    /**
     * 显示电子产品订单选项   保存选择的产品属性
     * @param  int   product   分类id 
     */    
    function  ViewDigit(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $this->load->model('common/wxcode_model');
        $view['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        //当不存在品牌型号信息的时候 返回品牌 型号列表
        if(!isset($_SESSION['submitorder']['brand']['proname']) || 
           !isset($_SESSION['submitorder']['brand']['braname']) ||
           !isset($_SESSION['submitorder']['brand']['typename'])){
           header("Location:/index.php/nonstandard/submitorder/ViewBrand"); 
        }
        //分类 品牌 型号信息
        $view['proname']=$_SESSION['submitorder']['brand']['proname'];
        $view['braname']=$_SESSION['submitorder']['brand']['braname'];
        $view['typename']=$_SESSION['submitorder']['brand']['typename'];
        $view['userdata']=$this->wxuser_model->get_userinfo();
        //加载电子表单
        $this->load->view('nonstandard/digit',$view);
    }   
    /** 
     * 提交订单   修改订单属性
     */
    function  CheckOrderAttr(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        $attr=$this->input->post();
        $res_attr=$this->submitorder_model->electronic_attr(2,$attr['typeid'],$attr);
        if($res_attr == false){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('order_optionnull'));
            echo json_encode($response);exit;
        }
        $this->load->database();
        $query=$this->db->update('h_order_content',array('electronic_oather'=>
                $_SESSION['submitorder']['attr'],'electronic_updatetime'=>time()),
                array('order_id'=>$attr['number']));
        if($query){
            $response=array('status'=>$this->config->item('request_succ'),
                    'url'=>$this->config->item('url_editorderattr_succ').'?id='.$attr['number']);
            echo json_encode($response);exit;
        }else{
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->lang->line('edit_order_fall'));
            echo json_encode($response);exit;
        }
    }
    /**
     * 提交订单----校验属性
     */
    function  CheckAtrr(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验参数
        $attr=$this->input->post();
        foreach ($attr as $key=>$value){            
            switch ($key){
                 case 'latitude':
                     $attr['latitude']='39.994309';
                    break;
                 case 'longitude':
                     $attr['longitude']='116.424639';
                    break;
                 case 'oather':
                    if(empty($value)){
                        $attr[$key]='';
                    }else{
                        $value=trim($value,',');
                        $value=str_replace(',','[1]',$value);
                        $value=Universal::safe_replace($value);
                        $attr[$key]=$value=str_replace('[1]',',',$value);
                    }
                    break;
                 default:
                     if(empty($value) || isset($value{50})){                         
                         Universal::Output($this->config->item('request_fall'),'请求参数不符合规范!');
                     }
                     $attr[$key]=Universal::safe_replace($value);
                     break;
            }
        }
		
		 //添加订单
		$this->load->model('shop/goods_model');
        if(!isset($_SESSION['userinfo']['user_invitation']) ||$_SESSION['userinfo']['user_invitation'] =="" ||
            $_SESSION['userinfo']['user_invitation']==false ){
            $this->goods_model->uid=$_SESSION['userinfo']['user_id'];
            $res=$this->goods_model->getInvitation();
            $invitation = $res['0']['inv'];
        }else{
            $invitation = $_SESSION['userinfo']['user_invitation'];
        }
		
		
        $this->load->model('nonstandard/submitorder_model');
        $this->submitorder_model->attr=$attr;
        $this->submitorder_model->type=2;
        $this->submitorder_model->userid=$_SESSION['userinfo']['user_id'];
        $this->submitorder_model->openid=$_SESSION['userinfo']['user_openid'];
		$this->submitorder_model->invitation=$invitation;
        $this->submitorder_model->latitude=$attr['latitude'];
        $this->submitorder_model->longitude=$attr['longitude'];
        $res=$this->submitorder_model->checkorder_data();
        if($res){
            $this->load->model('common/wxcode_model');
            $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],108);//设置微信分组 任务完成，转盘，报单组
            Universal::Output($this->config->item("request_succ"),'','
                               /index.php/nonstandard/quote/ViewQuote?id='.
                               $this->submitorder_model->number);
        }
        Universal::Output($this->config->item("request_fall"),'订单提交失败');
    }
    /**
     * 选择回收商前 填写详细地址
     * @param   array   electronic
     * @return  string  respone   返回结果
     */
    function  address(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        //校验参数
        $number=$this->input->get('oid',true);
        $adr = $this->input->get('adr',true);
        $userid = $_SESSION['userinfo']['user_id'];
        if(empty($number) || !is_numeric($number) || isset($number{20}) || !is_numeric($userid)
            || (!is_numeric($adr)&&$adr!=false)){
            Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
        }
        $offid=$this->input->get('fid',true);
        if(empty($number) || !is_numeric($number) || isset($number{20})){
            Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
        }
        //获取订单的详细
        $this->load->model('nonstandard/order_model');
        $this->order_model->number=$number;
        $order=$this->order_model->getProInfo();
        if($order === false){
            Universal::Output($this->config->item('request_fall'),'没有找到该笔订单');
        }
        if($order['0']['types_id'] == 0){
            $attr=$this->order_model->getProInfoAttr();
            if($attr === false){
                Universal::Output($this->config->item('request_fall'),'没有找到该笔订单');
            }
            $info=json_decode($attr['0']['attr'],true);
            $view=array('proname'=>$info['proname'],'typename'=>$info['typename'],
                    'offerid'=>$offid,'orderid'=>$number);
        }else{
            $view=array('proname'=>$order['0']['brand_name'],'typename'=>$order['0']['types_name'],
                    'offerid'=>$offid,'orderid'=>$number);
        }
        //获取当前用户填写的地址
        $this->load->model('shop/reals_model');
        $addresult = $this->reals_model->getaddress($userid);
        if ($adr==false && $addresult!=false) {
            foreach ($addresult as $k => $v) {
                if ($v['status']==2) {//获取默认地址
                    $view['address'] = $v;
                    break;
                }
            }
        }elseif($addresult!=false){
            foreach ($addresult as $k => $v) {
                if ($v['id']==$adr) {//得到选中的地址
                    $view['address'] = $v;
                    break;
                }
            }
        }
        if (!isset($view['address'])&&$addresult!=false) {//当用户选中的地址不存在
            $view['address'] = $addresult['0'];
        }elseif(!isset($view['address'])){
            $view['address']['name'] = '';
            $view['address']['details'] = '';
            $view['address']['number'] = '';
            $view['address']['id'] = '';
            $view['address']['city'] = '';
        }
        $_SESSION['good']['selectadrid'] = $view['address']['id'];//存取当前选中的地址
        $this->load->view('nonstandard/digit',$view);   
    }
    /**
     * 显示批量回收表单
     */
    function more(){
        $this->load->view('nonstandard/more');
    }
    /**
     * 添加批量回收订单
     * @param amount    int       数量
     * @param oather    string    内容
     * @param name      string    姓名
     * @param cardid    int       银行卡号
     * @param cardname  string    开户行
     */
    function moreSubmit(){
       $data=$this->input->post();
       if(empty($data) && !isset($_SESSION['moredata'])){
           Universal::Output($this->config->item('request_fall'),'没有获取到数据');
       }
       if(isset($_SESSION['moredata'])){
           $data=json_decode($_SESSION['moredata'],true);
       }
       $this->load->model('auto/userauth_model');
       if (!$this->userauth_model->UserIsLoginJump('/index.php/nonstandard/submitorder/moreSubmit',true)) {
           $_SESSION['moredata']=json_encode($data);
           Universal::Output($this->config->item('request_fall'),'请登录后再次报单','/index.php/nonstandard/system/Login');
       }
       if(!isset($data['amount']) || !is_numeric($data['amount'])){
           Universal::Output($this->config->item('request_fall'),'手机数量不可为空或数值不正确!');
       }
       if(!isset($data['oather']) || empty($data['oather'])){
           Universal::Output($this->config->item('request_fall'),'备注信息为必填选项!');
       }
       if(!isset($data['name']) || empty($data['name'])){
           Universal::Output($this->config->item('request_fall'),'姓名选项为必填选项!');
       }
       if(!isset($data['cardid']) || !is_numeric($data['cardid'])){
           Universal::Output($this->config->item('request_fall'),'银行卡号不能为空或卡号不正确!');
       }
        if(!isset($data['cardname']) || empty($data['cardname'])){
           Universal::Output($this->config->item('request_fall'),'开户行不能为空!');
       }
       $data['proname']='批量回收';
       $data['typename']='批量回收';
       //添加订单
       $this->submitorder_model->id=0;
       $this->submitorder_model->typename='批量回收';
       $this->submitorder_model->pid=10;
       $this->submitorder_model->mobile=$_SESSION['userinfo']['user_mobile'];
       $this->submitorder_model->openid=$_SESSION['userinfo']['user_openid'];
	   $this->submitorder_model->invitation=$_SESSION['userinfo']['user_invitation'];
       $this->submitorder_model->userid=$_SESSION['userinfo']['user_id'];
       $this->submitorder_model->attr=json_encode($data);
       $this->submitorder_model->ordertype=1;
       $this->submitorder_model->latitude='0';
       $this->submitorder_model->longitude='0';
       $res=$this->submitorder_model->savePlanOrder();
       if($res){
           $orderid=$this->submitorder_model->number;
           $url=$this->config->item('url_quotelist_succ').'?id='.$orderid;
           if(isset($_SESSION['moredata'])){
               unset($_SESSION['moredata']);
               header('location:'.$url);
           }else{
               Universal::Output($this->config->item('request_succ'),'',$url);
           }    
       }else{
           $response=array('status'=>$this->config->item('request_fall'),
                   'msg'=>$this->lang->line('edit_order_fall'));
           echo json_encode($response);exit;
       }
    }
    
}
/* End of file Submitorder.php */
/* Location: ./application/controllers/nonstandard/Submitorder.php */