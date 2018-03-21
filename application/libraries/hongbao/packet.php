<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @description 微信红包支付 调用
 */
require_once "oauth2.php";

class Packet{    
    
	private $wxapi; //微信wxapi
	/**
	 * @description 调用微信红包
	 * @param string $fun 调用方法类型
	 * @param string $param 参数
	 * @return SimpleXMLElement
	 */
    function _route($fun,$param = ''){
		$this->wxapi = new Wxapi();
		switch ($fun){
			case 'transfers':
				return $this->transfers($param);
				break;
			case 'wxpacket':
				return $this->wxpacket($param);
				break;			
			default:
				exit("Error_fun");
		}		
    }
    /**
     * @description 调用微信红包
     * @param string $param['openid'] opendid
     * @param string $param['money'] 数额
     */		
	private function wxpacket($param){
		return $this->wxapi->pay_micro_letter_envelopes($param['openid'],$param['money']);
	}
	/**
	 * @description 调用微信转账
	 * @param string $param['openid'] opendid
	 * @param string $param['money'] 数额
	 */
	private function transfers($param){
	    return $this->wxapi->pay_account_balance($param);
	}
}
/* End of file pay.php */