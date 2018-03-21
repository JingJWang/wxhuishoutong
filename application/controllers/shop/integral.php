<?php
header("Content-type:text/html;charset=utf-8");
/**
 * 积分商城
 * @author Administrator
 * 
 */
class  integral extends  CI_Controller{
    
    /**luck draw
     * 获取商品列表
     * @return  成功返回json 商品详细信息 | 失败返回 json  原因
     */
    function  getList(){ 
        $this->load->model('shop/flow_model');
        $this->flow_model->isfromSpread();
        $this->load->model('auto/userauth_model');
        $code = $this->input->post('code',true);
        $type = $this->input->post('type',true);
        if ($type=='null') {
            $type = '';
        }else{
            $type = '?type='.$type;
        }
        $this->load->model('common/wxcode_model','',TRUE);
        if ($code != 'null') {
            $this->load->model('auto/userauth_model','',TRUE);
            $this->userauth_model->UserCheck(2,$_SESSION,true);
            if(isset($this->userauth_model->url)){
                $result = $this->userauth_model->wxLogin($code);//用微信登录
            }
            $type = ($type==''?'?':$type.'&');
            $url = 'http://wx.recytl.com/view/shop/list.html'.$type.'code='.$code.'&state=';
        }else{
            $this->load->library('user_agent');
            $user_agent= $this->agent->agent_string();
            if (strpos($user_agent, 'MicroMessenger')&&!isset($_SESSION['userinfo']['user_openid'])) {
                $type = ($type==''?'?':$type.'&');
                Universal::Output($this->config->item('request_fall'),'','https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Fshop%2Flist.html'
                    .$type.'response_type=code&scope=snsapi_base&state=#wechat_redirect','');
            }
            $url = 'http://wx.recytl.com/view/shop/list.html'.$type;
        }
        $data['signPackage']=$this->wxcode_model->getSignPackageAjax($url);//分享的信息
        $data['shareurl'] = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Fshop%2Flist.html'.$type.'&response_type=code&scope=snsapi_base&state=#wechat_redirect';
        $this->load->model('shop/goods_model');
        $list=$this->goods_model->goodsList();
        if($list === false){
            Universal::Output($this->config->item('request_fall'),$this->goods_model->msg,'','');
        }
        $data['list']=$list;
        if($this->userauth_model->UserIsLogin()){
            $this->goods_model->userid=$_SESSION['userinfo']['user_id'];
            if (!is_numeric($this->goods_model->userid)) {
                Universal::Output($this->config->item('request_fall'),'请退出重新进入','','');
            }
            $this->load->model('task/user_model');
            $str = $this->user_model->is_have_user($this->goods_model->userid,',center_integral');//判断用户是否登入过任务中心，不是侧插入。
            if ($str === false) {
                Universal::Output($this->config->item('request_fall'),'获取用户信息失败','','');
            }elseif ($str === true) {
                $data['intergral'] = 0;
            }else{
                $data['intergral'] = $str['0']['center_integral'];
            }
        }else{
            $data['intergral'] = 0;
        }
        Universal::Output($this->config->item('request_succ'),'','',$data);
    }
    /**
     * 获取商品详情
     * @param   int  id  商品id
     * @return  成功返回json 商品详细信息 | 失败返回 json 跳转地址
     */
    function getInfo(){
        $this->load->model('shop/flow_model');
        $this->flow_model->isfromSpread();
        $id=$this->input->post('id',true);  
        //$id=2; 
        $url=$this->input->post('url',true);//'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] ;
        if(empty($id)){
            Universal::Output($this->config->item('request_fall'),'非法请求!','','');
        }
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        if (strpos($user_agent, 'MicroMessenger')) {
            $code = $this->input->post('code',true);
            $this->load->model('common/wxcode_model','',TRUE);
            $result = $this->extendeal($id,$code);//处理id，获取相应的分享链接和邀请码
            if (!is_numeric($result['sid'])) {
                Universal::Output($this->config->item('request_fall'),'','','');
            }
            $id = $result['sid'];
            $shareurl = $result['shareurl'];
            $signPackage = $this->wxcode_model->getSignPackageAjax($url);
            $_SESSION['LoginBackUrl'] = $result['url'];
        }else{
            if (!is_numeric($id)) {
                Universal::Output($this->config->item('request_fall'),'','','');
            }
            $shareurl = $signPackage = NULL;
            $_SESSION['LoginBackUrl'] = '/view/shop/info.html?id='.$id;
        }
        $this->load->model('shop/goods_model');
        $this->goods_model->goods_id=$id;
        $info=$this->goods_model->goodsInfo();
        if($info === false){
            Universal::Output($this->config->item('request_fall'),$this->goods_model->msg,'','');
        }
        $info['0']['share']=json_decode($info['0']['share'],true);
        $info['0']['imgs']=array_filter(explode('|',$info['0']['imgs']));
        $info['shareurl'] = $url;
        $info['signPackage']=$signPackage;//分享的信息
        empty($_SESSION['userinfo']['user_mobile'])?$info['reg']=false:$info['reg']=true;
        Universal::Output($this->config->item('request_succ'),'','',$info);
    }
    /**
     * 获得邀请码和分享的信息
     * @param        int/string        id         物品id/用户要求码加物品id
     * @param        string            code       微信传过来的code
     * @return       array             result     物品id 分享链接  邀请码和物品的id
     */
    private function extendeal($id,$code){
    	$urls='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'] ;
        if ($code != 'null') {
            $arr = explode('_',$id);
            if (count($arr)==1) {
                $extendnum = null;
                $sid = $id;
            }else{
                $extendnum = substr($id,0,6);
                $sid = end($arr);
            }
            //$url = 'http://wx.recytl.com/view/shop/info.html?
            $url=$urls.'&code='.$code.'&state='; ;
            $this->load->model('auto/userauth_model','',TRUE);
            $result = $this->userauth_model->wxLogin($code);//有code用微信自动登录
            if (isset($_SESSION['userinfo']['wx_mobile'])&&is_numeric($wx_id = $_SESSION['userinfo']['user_id'])) {//如果登录成功
                $this->load->model('task/user_model');
                $str = $this->user_model->is_have_user($wx_id,',center_extend_num');//判断用户是否登入过任务中心，不是侧插入。
                if ($str === false) {
                    Universal::Output($this->config->item('request_fall'),'获取用户信息失败','','');
                }
                $extends_num = ($str===true)?$this->user_model->extends:$str['0']['center_extend_num'];
                if ($extends_num!=$extendnum) {//确定不是用户本人
                    $this->load->model('task/otherget_model');
                    $this->otherget_model->shopinvite($extendnum,$sid);//购买商品提成任务 插入邀请码
                }
            }
            $shareurl = $urls;
        }else{
            if(isset($_SESSION['userinfo']['wx_mobile']) && is_numeric($wx_id = $_SESSION['userinfo']['user_id'])){//已经登入
                $this->load->model('task/user_model');
                $str = $this->user_model->is_have_user($wx_id,',center_extend_num');//判断用户是否登入过任务中心，不是侧插入。
                if ($str === false) {
                    Universal::Output($this->config->item('request_fall'),'获取用户信息失败','','');
                }
                $extends_num = ($str===true)?$this->user_model->extends:$str['0']['center_extend_num'];
                $shareurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?id='.$extends_num.'_'.$id;
            }else{
                $shareurl = $urls.'?id='.$id;
            }
            //获取邀请码
            $sid = $id;
            $shareurl = $urls.'?id='.$id;
        }
        $result = array(
            'sid' => $sid,
            'url' => $urls,
            'shareurl' => $shareurl,
        );
        return $result;
    }
    /**
     * 获取统一下单详细信息
     * @param    int  id 商品 id
     * @return   成功返回统一下单信息 | 失败 返回原因
     */
    function getOrder(){
        $result = $this->chcekOrder();
        if ($result===false) {
            $this->load->view('shop/errors',array('info'=>$this->msg));return ;
        }
        //校验商品的库存是否足够
        $this->load->model('shop/goods_model');
        $this->goods_model->goods_id=$this->input->post('id',true);
        $this->goods_model->goods_limit=$this->input->post('limit',true);
        $this->goods_model->goods_prid=$this->input->post('prid',true);//用户选择的奖励
        $this->goods_model->nums = $this->input->post('nums',true);//购买数量
        $stock=$this->goods_model->checkStock();
        if(!$stock){
            $this->load->view('shop/errors',array('info'=>$this->goods_model->msg));return ;
        }
        $this->goods_model->userid=$_SESSION['userinfo']['user_id'];
        $integral=$this->goods_model->checkIntegral();        
        if(!$integral){
            $this->load->view('shop/errors',array('info'=>$this->goods_model->msg));return ;
        }
        $showinfo = $this->createOrder();//获取支付信息->创建订单 
    	if( $this->goods_model->nums>0 && ($this->goods_model->goods_id>=755 && $this->goods_model->goods_id<=761)){
    		$showinfo['goods_name']=$showinfo['goods_name'].'  共'.$this->goods_model->nums.'张';
        }
        if ($showinfo===false) {
            $this->load->view('shop/errors',array('info'=>$this->goods_model->msg));return ;
        }
        $this->load->view('shop/payment',$showinfo);
    }
    /**
     * 获取统一下单详细信息
     * @param    int  id 商品 id
     * @return   成功返回统一下单信息 | 失败 返回原因
     */
    function HgetOrder(){
        $result = $this->chcekOrder();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),$this->msg,'','');

        }	
        //校验商品的库存是否足够
        $this->load->model('shop/goods_model');
        $this->goods_model->goods_id=$this->input->post('id',true);
        $this->goods_model->goods_limit=$this->input->post('limit',true);
        $this->goods_model->goods_prid=$this->input->post('prid',true);//用户选择的奖励
		$numss=(int)$this->input->post('nums');//购买数量
		//校验只能购买一次
		$id= $this->goods_model->goods_id;
		if($id==933){
		    $this->goods_model->userid=$_SESSION['userinfo']['user_id'];
		    $resu=$this->goods_model->gamebuyone($id);
		    if($resu==false){
		        Universal::Output($this->config->item('request_fall'),'您已经购买过此商品，不能再次购买！','','');
		    }
		}
	    if(isset($numss) && $numss<1){
	        $this->goods_model->nums=1;
	    }else{
	        if($numss==0){
	            $this->goods_model->nums=1;
	        }else if($numss<0){
	            $this->goods_model->nums = abs($numss);
	        }else{
	            $this->goods_model->nums = $numss;
	        }
	    }
	    $stock=$this->goods_model->checkStock();
	    if(!$stock){
	        Universal::Output($this->config->item('request_fall'),$this->goods_model->msg,'','');
	    }
	    $this->goods_model->userid=$_SESSION['userinfo']['user_id'];
	    $integral=$this->goods_model->checkIntegral();
	    if(!$integral){
	        Universal::Output($this->config->item('request_fall'),$this->goods_model->msg,'','');
	    }
	    $this->HcreateOrder();
		
		
    }
    /**
     * 用户下单前校验下单是否合法
     * @return 失败返回  json 原因  | 校验成功 继续执行
     */
    function chcekOrder(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION,true);
        if(isset($this->userauth_model->url)){
            $_SESSION['callback']='/view/shop/info.html?id=1';
            Universal::Output($this->config->item('request_fall'),'您还没有登录',$this->userauth_model->url,'');
        }
        //校验下单参数是否为空
        $id=$this->input->post('id',true);  
        $limit=$this->input->post('limit',true); 
        $prid=$this->input->post('prid',true); 
        $param=(empty($id) || 
                empty($limit) ||
                !is_numeric($prid));
        if($param === true){
            $this->msg = '必填选项不可为空!';
            return false;
        }
        //校验下单参数格式是否正确
        $this->remark = $this->input->post('remark',true);
        if ($this->remark===false || $this->remark=='') {
            $this->remark = '';
        }else{
            if (strlen($this->remark)>90) {
                $this->msg = '备注字数过长!';
                return false;
            }
            $this->remark = Universal::filter($this->remark);
        }
        $this->adress = '';
        if ($this->input->post('adress',true)===false || $this->input->post('adress',true)=='') {
            $_POST['adress'] = 0;
        }else{
            $name = Universal::filter($this->input->post('name',true));
            $phone = $this->input->post('phone',true);
            $detail = Universal::filter($this->input->post('detail',true));
            if(isset($phone{11}) || !is_numeric($phone)){
                $this->msg = '手机格式不对!';
                return false;
            };
            $this->adress = $name.','.$phone.','.$detail;
        }
        $format=(!is_numeric($this->input->post('id',true)) ||
                !is_numeric($this->input->post('limit',true)) || !is_numeric($this->input->post('adress',true)));
        if($format === true){
            $this->msg = '请正确输入!';
            return false;
        }
    }
    /**
     * 生成订单信息
     * @return  成功返回json  订单信息  | 失败 返回 json 失败原因
     */
    private function  createOrder(){
        $ordernum = $this->create_ordrenumber();
         $this->goods_model->nums;
         if($this->goods_model->nums==0 || $this->goods_model->nums==''){
         	$this->goods_model->nums=1;
         }
        $showinfo = array(
            'goods_name'=>$this->goods_model->goods_name,
            'ordernum'=>$ordernum,
            'pri'=>$this->goods_model->goods_pri*$this->goods_model->nums,
            'integral'=>$this->goods_model->goods_integral,
        );
        if ($this->goods_model->goods_pri>0) {
            if ($this->input->post('paytype')=='zhifubao') {//支付宝支付
                $this->paytype = 3;//支付宝支付
                $result = $this->addorder($ordernum,$this->goods_model->nums);
                if ($result===false) {
                    return false;
                }
                $this->load->library('zhifubao/zhifubao.php');
                $this->zhifubao->out_trade_no=$ordernum;
                $this->zhifubao->subject='回收通-'.$this->goods_model->goods_name;
                $this->zhifubao->total_amount=($this->goods_model->goods_pri)/100*$this->goods_model->nums;
                $this->zhifubao->body='goodid:'.$this->goods_model->goods_id;
                $this->zhifubao->timeout_express='1m';
                $this->zhifubao->config=$this->config->item('zhifubao_attr');
                $this->zhifubao->pay();
                exit;
            }elseif($this->input->post('paytype')=='weixin'){//微信支付
                $this->load->library('wxsdk/wxpay');
                $info=array(
                    'body'=>$this->goods_model->goods_name,
                    'orderid'=>$ordernum,
                    'moeny'=>($this->goods_model->goods_pri)/100*$this->goods_model->nums,
                    'pro_id'=>$this->goods_model->goods_id,
                    'type'=>'NATIVE',
                    'notifyurl'=>'http://test.recytl.com/callback/pay.php'    //要回调的地址
                );
                $orderImgName = $ordernum;
                $result = $this->wxpay->code($orderImgName,$info);
                if ($result==false) {
                    $this->msg = '无法生产订单！';
                    return false;
                }
            }
        }else{
            $config='<script>function sureorder(){if (confirm("确定消耗'.$this->goods_model->goods_integral.'通花购买")) { Checkpay();};
                    function Checkpay(){$.ajax({type: "POST",url:"/index.php/shop/integral/queryOrder",
                    data:"number='.$ordernum.'",dataType:"json",
                    beforeSend: function(){$("#turn_gif_box").css("display","block");},success: function(data){
                    var response=eval(data);if(response["status"]==request_succ){UrlGoto(response["url"]);}else{alert(response["msg"])}},
                    complete :function(XMLHttpRequest, textStatus){
                    $("#turn_gif_box").css("display","none");},error:function(XMLHttpRequest,textStatus,errorThrown){
                    }});}}</script>';
        }
        if (isset($config)) {
            $showinfo['config'] = $config;
        }
        if (isset($orderImgName)) {
            $showinfo['code_img']='qrcode/goods/'.$orderImgName.'.jpg';
        }
        if (!empty($this->adress)) {
            $showinfo['adressinfo'] = explode(',', $this->adress);
        }
        $this->paytype = 2;//微信寄售通支付
        $result = $this->addorder($ordernum,$this->goods_model->nums);
        if ($result===false) {
            return false;
        }
        return $showinfo;
    }
    /**
     * 生成订单信息(html5支付界面)
     * @return  成功返回json  订单信息  | 失败 返回 json 失败原因
     */
    private function HcreateOrder(){
        $pristatus=$this->goods_model->goods_prid;
		$goodsid=$this->goods_model->goods_id;
		//校验当亲订单是否合法
        $this->load->model('shop/goods_model');
		$resu=$this->goods_model->goodsInfo();
		switch ($pristatus){
		    case 0:
		        $pri=(int)$resu[0]['ppri'];//价格
		        break;
		    case 1:
		        $pri=(int)$resu[0]['otprice']['1']['p'];//价格
		        break;
		    default:break;
		}
		$in=(int)$resu[0]['number'];//库存
        $ordernum = $this->create_ordrenumber();
        $nums=(int)$this->goods_model->nums;//购买数量
		if($nums==0){
			$nums=1;
		}else if($nums<0){
			$nums = abs($nums);
		}else{
			if($nums>$in){
				Universal::Output($this->config->item('request_fall'),'库存不足');
			}else{
				$nums = $nums;
			}
		}
        if ($this->goods_model->goods_pri>0) {
            $this->load->library('wxpay/jspay');
            $jspay=new jspay();
            $jspay->openid=$_SESSION['userinfo']['Login_openid'];
            $jspay->orderid=$ordernum;
            $jspay->pri=$this->goods_model->goods_pri*$nums;
            $jspay->body=$this->goods_model->goods_name;
            $jspay->url=$this->config->item('url_pay_callback');
            $jspay->attach=$this->create_ordrenumber();
            $info=$jspay->getJsApiInfo();
            $url='window.history.back();';
            if(!$info){
                Universal::Output($this->config->item('request_fall'),'下单失败!',$url,'');
            }
            $config='<script>function jsApiCall(){WeixinJSBridge.invoke("getBrandWCPayRequest",'.$info.
            ',function(res){Checkpay();});}function Checkpay(){$.ajax({type: "POST",url:"/index.php/shop/integral/queryOrder",
                    data:"number='.$ordernum.'&nums='.$this->goods_model->nums.'",dataType:"json",
                    beforeSend: function(){$("#turn_gif_box").css("display","block");},success: function(data){
                    var response=eval(data);if(response["status"]==request_succ){UrlGoto(response["url"]);}else{alert(response["msg"])}},
                    complete :function(XMLHttpRequest, textStatus){
                    $("#turn_gif_box").css("display","none");},error:function(XMLHttpRequest,textStatus,errorThrown){
                    }});}</script>';
        }else{
            $config='<script>if (confirm("确定消耗'.$this->goods_model->goods_integral.'通花购买")) { Checkpay();};
                    function Checkpay(){$.ajax({type: "POST",url:"/index.php/shop/integral/queryOrder",
                    data:"number='.$ordernum.'",dataType:"json",
                    beforeSend: function(){$("#turn_gif_box").css("display","block");},success: function(data){
                    var response=eval(data);if(response["status"]==request_succ){UrlGoto(response["url"]);}else{alert(response["msg"])}},
                    complete :function(XMLHttpRequest, textStatus){
                    $("#turn_gif_box").css("display","none");},error:function(XMLHttpRequest,textStatus,errorThrown){
                    }});}</script>';
        }
        //生成订单记录
        $this->paytype = 1;//微信回收通支付
        $result = $this->addorder($ordernum,$this->goods_model->nums);
        $url='window.history.back();';
        if(!$result){
            Universal::Output($this->config->item('request_fall'),'下单失败!',$url,'');
        }
        Universal::Output($this->config->item('request_succ'),'','',$config); 
    }
    /**
     * 添加订单
     * @param   int     ordernum        订单编号
     */
    private function addorder($ordernum,$nums){
        $pristatus=$this->goods_model->goods_prid;
        //校验当亲订单是否合法
        $this->load->model('shop/goods_model');
    	if($_SESSION['userinfo']['user_invitation'] =="" ||!isset($_SESSION['userinfo']['user_invitation']) || 
    	    $_SESSION['userinfo']['user_invitation']==false ){
    	    $this->goods_model->uid=$this->goods_model->userid;
    	    $res=$this->goods_model->getInvitation();
    		$invitation = $res['0']['inv'];
    	}else{
    		$invitation = $_SESSION['userinfo']['user_invitation'];
    	}
		$goodsid=$this->goods_model->goods_id;
		$resu=$this->goods_model->goodsInfo();
        switch ($pristatus){
            case 0:
                $pri=(int)$resu[0]['ppri'];//价格
                break;
            case 1:
                $pri=(int)$resu[0]['otprice']['1']['p'];//价格
                break;
            default:break;
        }
		$in=(int)$resu[0]['number'];//库存
		if(!isset($nums)){
			$nums=1;
		}else{
			$nums=(int)$nums;//购买数量
			if($nums==0){
				$nums=1;
			}else if($nums<0){
				$nums = abs($nums);
			}else{
				if($nums>$in){
					Universal::Output($this->config->item('request_fall'),'库存不足');
				}else{
					$nums = $nums;
				}
			}
		}
        //生成订单记录
        $name=$this->goods_model->goods_name.'  '.$nums;
        $this->goods_model->record=array('record_userid'=>$_SESSION['userinfo']['user_id'],
                'record_goodid'=>$this->input->post('id',true),
        		'record_name'=>$name,
                'record_adressid'=>$this->input->post('adress',true),
                'record_integral'=>$this->goods_model->goods_integral,
                'record_content'=>$this->goods_model->record_content,
        		'record_invitation'=>$invitation,
                'record_type'=>$this->paytype,'record_payid'=>$ordernum,
                'record_price'=>$pri*$nums,
                'record_divide'=>$this->goods_model->goods_divide,
                'record_adress'=>$this->adress,
                'record_remark'=>$this->remark,
                'record_jointime'=>time(),'record_status'=>0);
        $addrecord=$this->goods_model->createOrder();
        if(!$addrecord){
            $this->msg = '无法生产订单！';
            return false;
        }
        return true;
    }
    /**
     * 查询订单是否支付成功 并系统记录收入 同时更新用户订单状态  减去对应的通花
     * @param   int  number   订单编号
     * return   成功返回 json 跳转地址  | 失败 返回原因
     */
    function queryOrder(){
        $number=$this->input->post('number',true);
		$nums=(int)$this->input->post('nums');//购买数量
		if($nums==0){
			$nums=1;
		}else if($nums<0){
			$nums = abs($nums);
		}else{
			$nums = $nums;
		}
        if(empty($number) || !is_numeric($number)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'请求非法!','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
		//校验当亲订单是否合法
		$this->load->model('shop/goods_model');
        $this->goods_model->number=$number;
		$this->goods_model->nums=$nums;
        $record=$this->goods_model->getRecord();
        if(!$record){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->goods_model->extendnum = '';
        if($this->goods_model->price>0&&$this->goods_model->divide>0){//查看是否有邀请码
            $this->load->model('task/otherget_model');
            $this->goods_model->extendnum = $this->otherget_model->shopgetextend($this->goods_model->goods_id,$this->goods_model->userid);
            if ($this->goods_model->extendnum==false) {
                $this->goods_model->extendnum = '';
            }
        }
        $res=$this->goods_model->transaction();
        if($res){
            if ($this->goods_model->extendnum != '') {
                $this->goods_model->sendNotice();
            } 
            //购买会员大礼包成功后  把大礼包的内容添加到会员个人中心
            if($this->goods_model->goods_id==646){
            	$fund=$this->config->item('fund');
            	$integral=$this->config->item('integral');
            	$userid=$record[0]['uid'];
            	//查询该用户的体验会员到期时间 ,如果还未到期 用户购买了会员大礼包,会员到期时间为体验会员加上一年的时间 ,如果体验时间到期了 会员购买 则到期时间为现在时间加上一年
            	$this->goods_model->fund=$fund;
            	$this->goods_model->integral=$integral;
            	$ins_result=$this->goods_model->updatePerson();
            	if($ins_result){
            		$response=array('status'=>$this->config->item('request_succ'),
            				'msg'=>'','url'=>'/view/shop/details.html?id='.
            				$this->goods_model->recordid,'data'=>array('recordid'=>$this->goods_model->recordid));
            		echo json_encode($response);exit();
            	}else{
            		$response=array('status'=>$this->config->item('request_fall'),
            					'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
            		echo json_encode($response);exit();
            	}
            }else{
            	$this->load->model('shop/flow_model');
            	$bonus_result = $this->flow_model->getBonus($record[0]['record_goodid']);
            	if($bonus_result){
            	   /*  //验证当前邀请码的用户是否是会员
            	    $mem_result=$this->flow_model->selectMem($record[0]['invita']);
            	    if($mem_result==0){
            	        $response=array('status'=>$this->config->item('request_fall'),
            	            'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
            	        echo json_encode($response);exit();
            	    } */
            		$this->flow_model->goodsid=$record[0]['record_goodid'];//物品id
            		$this->flow_model->goodname=$record[0]['name'];//物品名字
            		$this->flow_model->goodid=$record[0]['payid'];//交易订单号
            		$this->flow_model->invitation=$record[0]['invita'];//邀请码
            		$this->flow_model->fixed=$bonus_result[0]['type'];//奖金提取方式
            		$this->flow_model->value=$bonus_result[0]['value'];//奖金提取方式设定的值
            		$this->flow_model->userid=$record[0]['uid'];//购买人id
            		if($record['0']['price']>0 ){
            			$this->flow_model->price=$record['0']['price'];//成交的价格
            		}else{
            			$this->flow_model->price=0;
            		}
            		$ins_result=$this->flow_model->addGoodDeal();
            		 if($ins_result){
            			$response=array('status'=>$this->config->item('request_succ'),
            					'msg'=>'','url'=>'/view/shop/details.html?id='.
            					$this->goods_model->recordid.'&nums='.$nums,
            					'data'=>array('recordid'=>$this->goods_model->recordid,'nums'=>$nums));
            			echo json_encode($response);exit();
            		}else{
            			$response=array('status'=>$this->config->item('request_fall'),
            					'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
            			echo json_encode($response);exit();
            		}
            	}else{
            		 $response=array('status'=>$this->config->item('request_succ'),
            				'msg'=>'购买成功','url'=>'','data'=>'');
            		echo json_encode($response);exit(); 
            	}
            }
        }else{
            $response=array('status'=>$this->config->item('request_fall'),
                        'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
           echo json_encode($response);exit();
        }
    }
    /**
     * 查询交易交易记录详情
     * @param    int  number 交易编号
     * @return   成功返回交易详情 json |失败返回原因 json
     */
    function getRecord(){
        $id=$this->input->post('id',true);  
        if(empty($id) ||
            !is_numeric($this->input->post('id',true))){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'查看当前交易详情出现异常','url'=>'','data'=>'');
            echo json_encode($response);exit();
        }
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        //查询当前交易记录详情
        $this->load->model('shop/goods_model');
        $this->goods_model->number=$this->input->post('id',true);
        $this->goods_model->userid=$_SESSION['userinfo']['user_id'];        
        $info=$this->goods_model->recordInfo();
        if($info === false || $info['0']['status']==0){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit();
        }
        if($info !== false){
            $info['isub'] = true;
            // ($_SESSION['userinfo']['subscribe']==1)?$info['isub'] = true:$info['isub'] = false;
            $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>'','url'=>'','data'=>$info);
            echo json_encode($response);exit();
        }
        
    }
    /**
     *获取交易记录
     *@return  成功json 返回当前用户的交易记录| 失败json 返回是原因
     */
    function getDetail(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(1,$_SESSION,true);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录！如果您在微信且与微信绑定，将自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $this->load->model('shop/goods_model');
        $this->goods_model->userid=$_SESSION['userinfo']['user_id'];
        $info=$this->goods_model->getDetail();
        if($info !== false){
            $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>'','url'=>'','data'=>$info);
            echo json_encode($response);exit();
        }
        $response=array('status'=>$this->config->item('request_fall'),
                'msg'=>$this->goods_model->msg,'url'=>'','data'=>'');
        echo json_encode($response);exit();
       
    }
    /**
     * 通话兑换基金兑换
     * @param     int     id      物品id
     */
    function exchangefund(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(1,$_SESSION);
        if(isset($this->userauth_model->url)){
            Universal::Output($this->config->item('request_fall'),'您还没有登录,正在跳转到首页自动登录',$this->userauth_model->url,'');
        }
        if (!is_numeric($this->input->post('id',true))||!is_numeric($_SESSION['userinfo']['user_id'])) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('shop/goods_model');
        $this->goods_model->goods_id=$this->input->post('id',true);
        $stock=$this->goods_model->getgoodinfo();
        if ($stock['0']['type']!=3) {
            Universal::Output($this->config->item('request_fall'),'此商品不能这样购买','','');
        }
        $this->goods_model->userid=$_SESSION['userinfo']['user_id'];
        $recordInfo = $this->goods_model->getorderinfo();
        if (is_array($recordInfo)) {
            Universal::Output($this->config->item('request_fall'),'您已经购买过此商品，不能再次购买！','','');
        }
        $this->goods_model->goods_integral = $stock['0']['integral'];
        $integral=$this->goods_model->checkIntegral();
        if ($integral===false) {
            Universal::Output($this->config->item('request_fall'),$this->goods_model->msg,'','');
        }
        $this->goods_model->ordernumber = $this->create_ordrenumber();
        $result = $this->goods_model->exchangefunds();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'提交订单失败','','');
        }
        Universal::Output($this->config->item('request_succ'),'提交订单成功','/view/shop/details.html?id='.
                    $this->goods_model->recordid,'');
    }
    /**
     *  生成订单订单编号
     * @return string
     */
    function create_ordrenumber(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
    /**
     * 检查输入的票数与当前的库存量相比
     */
    function admStock(){
    	//$data=$this->input->post();
    	$data=$this->input->post();
    	if(empty($data['num'])){
    		Universal::Output($this->config->item('request_fall'),'非法请求!','','');
    	}
    	//获取商品的库存liang
    	$this->load->model('shop/goods_model');
    	//$data['num']; //获取输入的票张数
    	$this->goods_model->goods_id=$data['id']; //获取输入的票张数
    	$result=$this->goods_model->getgoodinfo();
    	if($result === false){
    		Universal::Output($this->config->item('request_fall'),'','','');
    	}
    	$result['num']=$data['num'];
    	if($result['0']['number']>$data['num']){
    		
    		Universal::Output($this->config->item('request_succ'),'','',$result);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'','',$result);
    	}
    }
    /**
     * 检查输入的猪肉个数数与当前的库存量相比
     */
    function admZhuStock(){
        //$data=$this->input->post();
        $data=$this->input->post();
        if(empty($data['num'])){
            Universal::Output($this->config->item('request_fall'),'非法请求!','','');
        }
        //获取商品的库存liang
        $this->load->model('shop/goods_model');
        //$data['num']; //获取输入的票张数
        $this->goods_model->goods_id=$data['id']; //获取输入的票张数
        $result=$this->goods_model->getgoodinfo();
        if($result === false){
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $result['num']=$data['num'];
        if($result['0']['number']>$data['num']){
    
            Universal::Output($this->config->item('request_succ'),'','',$result);
        }else{
            Universal::Output($this->config->item('request_fall'),'','',$result);
        }
    }
}