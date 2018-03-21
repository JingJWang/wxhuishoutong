<?php

class WxcodeModel extends MySQL {
    //微信基础接口表    
    private $table_wxuser='h_token';  
        
    function __construct(){
        parent::__construct();
        
    }    
    /**
     * 功能描述:js SDK配置参数 获取
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }
    /**
     * 功能描述:获取随机字符串
     * 参数描述:$length 随机字符串长度
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /**
     * 功能描述:获得ApiTicket
     */
      public function getJsApiTicket() {
    	$sql=' select token_value from h_token where id=2';
    	$this->Query($sql);
    	$data=$this->RowArray();
        return  $data['token_value'];        
      }
	  /**
	   * 功能描述:获得AccessToken
	   */
      public function getAccessToken() {   
       $sql=' select token_value from h_token where id=1'; 
    	if($this->Query($sql) === false){
    	    return FALSE;
    	}else{
    	    $data=$this->RowArray();
    	    return  $data['token_value'];
    	}
    	
      }
      /**
       * 功能描述:获取生成二维码地址
       */
      public function https_post($id){
          $access_token=$this->getAccessToken();
          $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
          //QR_SCENE 临时  //QR_LIMIT_SCENE 永久
          $data='{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
          //$data='{"expire_seconds": 7200, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
          $curl=curl_init();
          curl_setopt($curl,CURLOPT_URL,$url);
          curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
          curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
          if(!empty($data)){
              curl_setopt($curl,CURLOPT_POST,1);
              curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
          }
          curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
          $info=curl_exec($curl);
          curl_close($curl);
          $jsoninfo=json_decode($info,true);
          return  $ticket=$jsoninfo["ticket"];;
      }
      /**
       * 功能描述:获取带参的二维码
       */
      public function img($id){
          $ticket=$this->https_post($id);
          return $url='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
      }
      /**
       * 功能描述:下载微信带参的二维码
       */
      public function dowload($id){
          $url=$this->img($id);
          $ch=curl_init($url);
          curl_setopt($ch,CURLOPT_HEADER,0);
          curl_setopt($ch,CURLOPT_NOBODY,0);
          curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
          curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
          $info=curl_exec($ch);
          $httpinfo=curl_getinfo($ch);
          curl_close($ch);
          return array_merge(array('body'=>$info),array('header'=>$httpinfo));
      }
      /**
       * 功能描述:根据openid获取用户详细信息
       */
      public function userinfo($openid){
          $access_token=$this->getAccessToken();
          $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
          $ch=curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $output = curl_exec($ch);
          curl_close($ch);
          $data = json_decode($output, true);
          return $data;
      }
      /*
       *	更新token
       */
      function update_token(){
          $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRETt;
          $curl=curl_init();
          curl_setopt($curl,CURLOPT_URL,$url);
          curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
          curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
          $info=curl_exec($curl);
          curl_close($curl);
          $jsoninfo=json_decode($info,true);
          $sql='update h_token set   token_value="'.$jsoninfo["access_token"].'",token_lastdate= "'.date('Y-m-d H:i:s').'" where id=1';
          $this->Query($sql);
      }
      
      /**
       * 功能描述:通过code 取得用户的openid
       */
      function getOpenid($code){
          $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".APPID."&secret=".APPSECRETt."&code=".$code."&grant_type=authorization_code";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$url);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $result = curl_exec($ch);
          $data=json_decode($result,true);
          if(array_key_exists('errcode', $data)){
              return $openid='';
          }
          $openid=$data['openid'];
          return $openid;
      }
      /**
       *功能描述:根据openid推送信息
       *参数描述:$openid 微信openid   $content内容
       */
      public function sendmessage($content){
          if(empty($content)){
              return false;
          }else{
              $data=$content;
          }
          $access_token=$this->getAccessToken();
          $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
          $curl=curl_init();
          curl_setopt($curl,CURLOPT_URL,$url);
          curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
          curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
          curl_setopt($curl,CURLOPT_POST,1);
          curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
          $info=curl_exec($curl);
          curl_close($curl);
          $info=json_decode($info,true);
          return  $info;
      }
      /**
       * 功能描述:snsapi_userinfo方式获取用户信息
       */
      public function get_snsapi_userinfo($token,$openid){
          $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$url);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $result = curl_exec($ch);
          $data=json_decode($result);
          $info['openid']=$data->openid;
          $info['nickname']=$data->nickname;
          $info['sex']=$data->sex;
          $info['headimgurl']=$data->headimgurl;
          return $info;
      }
      /**
       * 功能描述:curl get方法
       * 参数描述:$url 请求的地址
       * 返回数据:$res  请求结果
       */
      public function httpGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
      }
}