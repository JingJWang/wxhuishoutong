<?php

class wxconfig_model extends CI_Model {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * 更新微信config配置参数 token jsapi ticket
     */
    function getwxconfig(){
        $resp=$this->getAccessToken();
        return  $resp;
    }
    /**
     * 
     * 获取wx token
     */
    function getAccessToken() {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx29a596b5eac42c22&secret=2129003c8d29a592d8f39e4cf0ebf8fe';
        $result=$this->httpGet($url);
        $res = json_decode($result);
        $data=array(
                'token_times'=>time(),
                'token_value'=>$res->access_token,//$res->access_token
                'token_lastdate'=>date('Y-m-d H:i:s')
        );
        $query=$this->db->update('h_token',$data,array('id'=>1));
        $row=$this->db->affected_rows();
        if($query === true && $row === 1){
            $resp=$this->getJsApiTicket($res->access_token);
            return $resp;
        }else{
            return false;
        }        
    }
    /**
     * 
     * 获取 ticke
     */
    function getJsApiTicket($accessToken){
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $result=$this->httpGet($url);
        $res = json_decode($result);
        $data=array(
                'token_times'=>time(),
                'token_value'=>$res->ticket,//$res->ticket
                'token_lastdate'=>date('Y-m-d H:i:s')
        );
        $query=$this->db->update('h_token',$data,array('id'=>2));
        $row=$this->db->affected_rows();
        if($query === true && $row === 1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 
     * @param string   $url  请求地址
     * @return  结果
     */
    function httpGet($url){
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