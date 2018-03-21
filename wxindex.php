<?php
include 'weixindo/config/config.php';
define('TOKEN','tgxiaotao');
 $wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
} 

class wechatCallbackapiTest{
    
	public function valid(){
        $echoStr = $_GET["echostr"];       
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }
    /*
     * 功能描述:校验接受请求类型
     */
    public function responseMsg(){
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($postStr)){
			$this->logger('Request',$postStr);
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$RX_TYPE = trim($postObj->MsgType);
			$result = "";
			switch ($RX_TYPE){
			    //事件类型
				case "event":
					$result = $this->receiveEvent($postObj);
					break;
				//文本类型
				case "text":
					$result = $this->receiveText($postObj);
					break;
			}
			$this->logger('Response',$result);
			echo $result;
		}else {
			echo "";
			exit;
		}
    }
    /*
     * 功能描述:请求类型为事件类型
     */
     private function receiveEvent($object){
        echo $object->Event;
		$content="";
		$type="";
        switch ($object->Event){
            //关注事件
            case "subscribe":
                $type='1';
                include 'weixindo/control/UserControl.class.php';
                $wxuser=new UserControl();
                $content=$wxuser->check_user($object->FromUserName,$object->EventKey);
                break;
            //扫描场景二维码事件
            case "SCAN":
				$type="2";
				include 'weixindo/control/UserControl.class.php';
				$wxuser=new UserControl();
				$content=$wxuser->user_scan($object->FromUserName,$object->EventKey);
				break;
		    //取消事件
			case "unsubscribe":
				include 'weixindo/control/UserControl.class.php';
				$openid=$object->FromUserName;
                $wxuser=new UserControl();
                $content=$wxuser->user_cancel($openid);
				break;
		    //上报地理位置
		    case "LOCATION":
				    include 'weixindo/control/UserControl.class.php';
				    $data['openid']=$object->FromUserName;
				    $data['ctime']=$object->CreateTime;
				    $data['latitude']=$object->Latitude;
				    $data['longitude']=$object->Longitude;
				    $data['precision']=$object->Precision;
				    $wxuser=new UserControl();
				    $content=$wxuser->SaveLocation($data);
				    break; 
        }
		if($type == '1'){
			$result = $this->transmitTWNews($object, $content);			
		}else{
			$result = $this->transmitText($object, $content);
		}
        return $result;
    }
    /*
     * 功能描述:关键词处理
     */
    public function receiveText($object){
        $keyword=preg_match('/^[a-zA-Z]+/s',trim($object->Content),$data);
        $key=strtolower($data['0']);
        switch ($key) {
            case 'task':
                $url=urlencode('http://test.recytl.com/');
                //$content = '<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.
                //'index.php/nonstandard/system/welcome&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect">首页(测试入口)</a>';
                $content = '<a href="http://wx.recytl.com/index.php/task/lists/alltask?code=a&openid='.$object->FromUserName.'">任务(测试入口)</a>';
                $result = $this->transmitText($object, $content);
                break;
             //测试功能
            case 'superstar':
                $url=urlencode('http://test.recytl.com/');
                //$content = '<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.
                //'index.php/nonstandard/system/welcome&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect">首页(测试入口)</a>';
				$content = '<a href="http://test.recytl.com/index.php/nonstandard/system/welcome?code=a&openid='.$object->FromUserName.'">首页(测试入口)</a>';
                $result = $this->transmitText($object, $content);
                break;                
            /*
            case 'welcom':
                    $url=urlencode('http://wx.recytl.com/');
                    $content = '<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.
                    'index.php/nonstandard/system/welcome&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect">首页</a>';
                    //$content = '<a href="http://test.recytl.com/index.php/nonstandard/system/welcome?code=a&openid='.$object->FromUserName.'">首页(测试入口)</a>';
                    $result = $this->transmitText($object, $content);
                    break; */
            //检测回收商
            case 'c':
                include 'weixindo/control/UserControl.class.php';
                $wxuser=new UserControl();
                $user=str_replace($key,'', $object->Content);
                $content = $wxuser->CheckAdminNumber($user);
                $result  = $this->transmitText($object, $content);
                break;
            /* //非标准化回收商 临时
            case  'shou':
                $url=urlencode('http://wx.recytl.com/');
                $content ='<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.
                'index.php/cooperation/coop/ViewSearch&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect">搜索订单</a>';
                $result  = $this->transmitText($object, $content);
                break; */
            //默认处理
            default:
				if(strpos($object->Content,'回收通') !== false){
					$content='欢迎咨询回收通，回收通热线：400-6415080，小通妹会为您耐心解答哦~';					
				}	
				if(strpos($object->Content,'红包')  !== false){
				    $content='回收通福利站里有红包福利哦~<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/task/lists/alltask&response_type=code&scope=snsapi_base&state=#wechat_redirect">快去看看吧</a>';
				}
				if(strpos($object->Content,'加盟')  !== false){
				    $content='您想咨询加盟相关的问题吗？可以拨打我们的热线电话：400-6415080，咨询详情哦~';
				}
				if(strpos($object->Content,'通花')  !== false){
				    $content='通花是回收通推出的虚拟积分。通花用处多多，可以兑换宝贝哦~<a href="http://wx.recytl.com/view/shop/list.html">点此逛一逛通花商城</a>';
				}
                if($object->Content == '地点' || $object->Content == '地点列表'){
                    $url=urlencode('http://wx.recytl.com/');
                    $content = '<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.'index.php/weixin/wxmap/addresslist&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect">地点列表</a>
                    ';
                }                
                if (!isset($content)){
                    $content='回收通 价值驱动环保 高价回收二手数码，还有好玩的福利系统等您参与哦~<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/nonstandard/system/welcome&response_type=code&scope=snsapi_base&state=#wechat_redirect">快去瞧一瞧</a>';
                }
                $result = $this->transmitText($object, $content);
            break;
        }
        return $result;
    }
    /**
     * 功能描述：接通客服
     */
    private function transService($object){
        $textTpl = '<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                    </xml>';
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    /*
     * 功能描述:返回文本信息
     */
    private function transmitText($object, $content)    {
        if (!isset($content) || empty($content)){
            return "";
        }
        $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[%s]]></Content>
					</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    /*
     * 功能描述:图文信息
     */
    private function transmitBZNews($object, $newsArray){
        $newsTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<Content><![CDATA[]]></Content>
						<ArticleCount>%s</ArticleCount>
						<Articles>
							<item>
                                <Title><![CDATA[主界面]]></Title> 
                                <Description><![CDATA[回复数字1即可进入主界面]]></Description>
                                <PicUrl><![CDATA[http://xiaolin888.sinaapp.com/images/zhujiem1.jpg]]></PicUrl>
                                <Url><![CDATA[http://xiaolin888.sinaapp.com/main.php/?openid=".$object->FromUserName."]]></Url>
                            </item>                                                        
                        </Articles>
					</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), 1);
        return $result;
    }
    /*
     * 功能描述:多条图文
     */
	private function transmitTWNews($object, $newsArray){
		if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
	/*
	 * 功能描述:返回单条信息
	 */
    private function transmitNews($object, $newsArray){
        if(!is_array($newsArray)){
            return "";
        }
        $itemTpl = "<item>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[%s]]></Description>
						<PicUrl><![CDATA[%s]]></PicUrl>
						<Url><![CDATA[%s]]></Url>
					</item>";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[news]]></MsgType>
						<Content><![CDATA[]]></Content>
						<ArticleCount>%s</ArticleCount>
						<Articles>
						$item_str</Articles>
					</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }
    /*
     * 功能描述:记录请求  响应日志信息
     */
    private function logger($type,$log_content){
       switch ($type){
           case 'Request':
               file_put_contents('logs/'.date('Y-m-d').'request.log',date('Y-m-d H:i:s').'Request类型'."\r\n".$log_content."\r\n",FILE_APPEND);
           break;
           case 'Response':
               file_put_contents('logs/'.date('Y-m-d').'Response.log',date('Y-m-d H:i:s').'Response类型'."\r\n".$log_content."\r\n",FILE_APPEND);
           break;
           default:
           break;
       }       
    }
    /*
     * 功能描述:校验TOKEN
     */
	private function checkSignature(){
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}


?>