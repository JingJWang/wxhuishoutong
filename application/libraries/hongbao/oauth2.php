<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @description 微信红包参数
 * @author mxt
 *
 */

class Wxapi {    
    
    private $app_id = 'wx29a596b5eac42c22'; 
    
    private $app_secret = '4da9b5c2ae49bb6b37d4e07da78a25dd'; 
    
    private $app_mchid = '1239890102'; 
    
    /**
     * @description 微信红包
     * @param string $openid 用户openid
     * @param int $money number
     */
    public function pay_micro_letter_envelopes($re_openid,$money=0){ 
        include_once('WxHongBaoHelper.php');
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();
        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，丌长于 32 位
        $wxHongBaoHelper->setParameter("mch_billno", $this->app_mchid.date('YmdHis').rand(1000, 9999));//订单号
        $wxHongBaoHelper->setParameter("mch_id", $this->app_mchid);//商户号
        $wxHongBaoHelper->setParameter("wxappid", $this->app_id);
        $wxHongBaoHelper->setParameter("nick_name", '回收通');//提供方名称
        $wxHongBaoHelper->setParameter("send_name", '回收通');//红包发送者名称
        $wxHongBaoHelper->setParameter("re_openid", $re_openid);//接受收红包的用户,用户在wxappid下的openid
        $wxHongBaoHelper->setParameter("total_amount", $money);//付款金额，单位分
        $wxHongBaoHelper->setParameter("min_value", $money);//最小红包金额，单位分
        $wxHongBaoHelper->setParameter("max_value", $money);//最大红包金额，单位分（ 最小金额等于最大金额： min_value=max_value =total_amount）
        $wxHongBaoHelper->setParameter("total_num", 1);//红包发放总人数
        $wxHongBaoHelper->setParameter("wishing", '感谢您参与活动，回收通祝您生活愉快！');//红包祝福语
        $wxHongBaoHelper->setParameter("client_ip", '182.92.214.25');//调用接口的机器 Ip 地址
        $wxHongBaoHelper->setParameter("act_name", '回收通现场活动');//活劢名称
        $wxHongBaoHelper->setParameter("remark", '让周边小伙伴也来参与吧！');//备注信息
        //后来添加的参数
//        $wxHongBaoHelper->setParameter("share_imgurl", 'https://wx.gtimg.com/mch/img/ico-logo.png');//分享的图片url
//        $wxHongBaoHelper->setParameter("share_url", 'http://www.qq.com');//分享链接
//        $wxHongBaoHelper->setParameter("share_content", '快来参加猜灯谜活动');//分享文案
//        $wxHongBaoHelper->setParameter("logo_imgurl", 'https://wx.gtimg.com/mch/img/ico-logo.png');//商户logo的url
//        $wxHongBaoHelper->setParameter("sub_mch_id", '10000090');//微信支付分配的子商户号，受理模式下必填
//        $wxHongBaoHelper->setParameter("sign", 'C380BEC2BFD727A4B6845133519F3AD6');//详见签名生成算法
		$postXml = $wxHongBaoHelper->create_hongbao_xml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
		$responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;
    }
    /**
     * @description  微信 余额支付
     */
    public function  pay_account_balance($option){        
        include_once('WxHongBaoHelper.php');
        $commonUtil = new CommonUtil();
        $wxHongBaoHelper = new WxHongBaoHelper();
        //appid
        $wxHongBaoHelper->setParameter("mch_appid", $this->app_id);
        //商户号
        $wxHongBaoHelper->setParameter("mchid", $this->app_mchid);
        //设备号
        $wxHongBaoHelper->setParameter("device_info", $this->app_id);
        //随机数
        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());
        //订单号
        $wxHongBaoHelper->setParameter("partner_trade_no", $this->build_order_number());
        //openid
        $wxHongBaoHelper->setParameter("openid", $option['openid']);
        //校验身份 NO_CHECK不校验  FORCE_CHECK 强制校验   OPTION_CHECK 存在的时候校验 
        if(empty($option['name'])){
            $wxHongBaoHelper->setParameter("check_name",'NO_CHECK');
            $username='';
        }else{
            $wxHongBaoHelper->setParameter("check_name",'FORCE_CHECK');
            $username=$option['name'];
        }
        //收款用户姓名  check_name=NO_CHECK 必填
        $wxHongBaoHelper->setParameter("re_user_name", $username);
        //金额
        $wxHongBaoHelper->setParameter("amount", $option['money']);
        //企业付款操作说明信息。必填。
        $wxHongBaoHelper->setParameter("desc", '支付商品金额');
        //调用接口的机器IP 必填
        $wxHongBaoHelper->setParameter("spbill_create_ip", '182.92.214.25');
        $postXml = $wxHongBaoHelper->create_transfers_xml();
        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $responseObj;        
    }
    /**
     * @description 生成随机字符串
     * @return string 随机字符串
     */  
    public function great_rand(){
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $t1='';
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;    
    }
    //生成订单号
    private function  build_order_number(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
}
?>