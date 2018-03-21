<?php
class  repair {
    function  wxcodepay(){
        //校验来源
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; 
   
        file_put_contents('wxjspaysj.log','请求接受到的时间'.date('Y-m-d H:i:s').$postStr."\r\n" ,FILE_APPEND);
        //解析xml
        
        $result = json_decode(json_encode(simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        //拿到订单编号
        if ($result['result_code'] != 'SUCCESS' || $result['return_code'] != 'SUCCESS') {
            return false;
        }
        //调用查询接口 确认这笔订单成功
        $orderNumber = $result['out_trade_no']; 
        $url = 'http://wx.recytl.com/repair/repair/wxPayCallback';
        $data = array(
                'number'=>$result['out_trade_no'],
        );
        $curl = curl_init ();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $return = curl_exec ( $curl );
        curl_close ( $curl );
        $return = json_decode($return,true);
        if ($return['status']==1000) {
            echo 'SUCCESS';
        }
    }
}
$repair = new repair();
$repair->wxcodepay();