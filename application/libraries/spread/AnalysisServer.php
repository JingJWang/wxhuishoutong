<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
define('CMD_TYPE_CS','CS');
/**
 * @brief   分析服务器类定义文件。
 * @author  赵一
 * @date    2017/01/11
 * @version 0.1
 */

/**
 * @class  AnalysisServer
 * @brief  分析服务器类。
 * @author 赵一
 */
class AnalysisServer {
    /**
     * @brief  构造函数。
     * @author 赵一
     * @param[in] $config   服务器配置。
     * @param[in] $dataName 数据名称。
     * @param[in] $key      秘钥。
     */
    public function __construct($configs) {
    	$this->mConfig = $configs['config'];
    	$this->mDataName = $configs['dataName'];
    	$this->mDataKey = $configs['key'];
    }

    /**
     * @brief  析构函数。
     * @author 赵一
     */
    public function __destruct(){
    }

    /**
     * @brief     追加打点命令函数。
     * @author    赵一
     * @param[in] $ip      打点设备ip。
     * @param[in] $mac     打点设备mac。
     * @param[in] $channel 打点设备渠道。
     * @return    返回结果，参数错误：null，成功：{'result':0, 'rand':123}，失败：{'result':非零}。
     */
    public function addTrack($ip, $mac, $channel) {
    	$res = null;

    	if (isset($ip) && !empty($ip) && isset($mac) && !empty($mac) && isset($channel) && !empty($channel)) {
	    	$params = array('cmd' => 'AddTrack',
			    			'params' => array('ip' => $ip,
						    				  'mac' => $mac,
						    				  'channel' => $channel),
			    			'type' => CMD_TYPE_CS,
			    			'version' => self::CMD_VERSION);
	    	$postData = json_encode($params);
    		$sign = crypt($postData, $this->mDataKey);
    		$params['sign'] = $sign;
    		$postData = array($this->mDataName => json_encode($params));

	    	$postResponse = $this->_post($postData);
	    	if (isset($postResponse) && !empty($postResponse)) {
	    		$cmd = $postResponse['cmd'];
	    		$result = $postResponse['result'];
	    		$params = $postResponse['params'];
	    		if ($cmd == 'AddTrack') {
	    			switch ($result) {
	    				case 0:
	    					//成功
	    					$rand = $params['rand'];
	    					$res = array('rand' => $rand,    //随机数，用于调用注册打点以及子功能打点
	    							     'result' => $result);
	    					break;
	    				default:
	    					//失败
	    					$res = array('result' => $result);
	    					break;
	    			}
	    		}
	    	}
    	}

    	return $res;
    }

    /**
     * @brief     首次进入打点命令函数。
     * @author    赵一
     * @param[in] $rand    随机数值，由AddTrack返回得出。
     * @param[in] $where   打点功能源名称。（寄售通:JST;回收通:HST）
     * @return    返回结果，参数错误：null，成功：{'result':0}，失败：{'result':非零}。
     */
    public function enterTrack($rand, $where) {
    	$res = null;
    
    	if (isset($rand) && !empty($rand) && isset($where) && !empty($where)) {
    		$params = array('cmd' => 'EnterTrack',
    				'params' => array('rand' => $rand,
    						'where' => $where),
    				'type' => CMD_TYPE_CS,
    				'version' => self::CMD_VERSION);
    		$postData = json_encode($params);
    		$sign = crypt($postData, $this->mDataKey);
    		$params['sign'] = $sign;
    		$postData = array($this->mDataName => json_encode($params));
    
    		$postResponse = $this->_post($postData);
    		if (isset($postResponse) && !empty($postResponse)) {
    			$cmd = $postResponse['cmd'];
    			$result = $postResponse['result'];
    			$params = $postResponse['params'];
    			if ($cmd == 'EnterTrack') {
    				switch ($result) {
    					case 0:
    						//成功
    						$res = array('result' => $result);
    						break;
    					default:
    						//失败
    						$res = array('result' => $result);
    						break;
    				}
    			}
    		}
    	}
    
    	return $res;
    }

    /**
     * @brief     注册打点命令函数。
     * @author    赵一
     * @param[in] $rand  随机数值，由AddTrack返回得出。
     * @param[in] $where 打点功能源名称。（寄售通:JST;回收通:HST）
     * @param[in] $id    注册后系统内的唯一标识id。
     * @return    返回结果，参数错误：null，成功：{'result':0}，失败：{'result':非零}。
     */
    public function registTrack($rand, $where, $id) {
    	$res = null;
    
    	if (isset($rand) && !empty($rand) && isset($where) && !empty($where)) {
    		$params = array('cmd' => 'RegistTrack',
    				'params' => array('rand' => $rand,
    						'where' => $where,
    				        'id' => $id),
    				'type' => CMD_TYPE_CS,
    				'version' => self::CMD_VERSION);
    		$postData = json_encode($params);
    		$sign = crypt($postData, $this->mDataKey);
    		$params['sign'] = $sign;
    		$postData = array($this->mDataName => json_encode($params));
    
    		$postResponse = $this->_post($postData);
    		if (isset($postResponse) && !empty($postResponse)) {
    			$cmd = $postResponse['cmd'];
    			$result = $postResponse['result'];
    			$params = $postResponse['params'];
    			if ($cmd == 'RegistTrack') {
    				switch ($result) {
    					case 0:
    						//成功
    						$res = array('result' => $result);
    						break;
    					default:
    						//失败
    						$res = array('result' => $result);
    						break;
    				}
    			}
    		}
    	}
    
    	return $res;
    }

    /**
     * @brief     post提交函数。
     * @author    赵一
     * @param[in] $postData post提交数据。
     * @return    执行结果
     */
    private function _post($postData) {
    	$res = null;

    	$response = $this->_curlPost($this->mConfig['url'], $postData);
    	if (isset($response) && !empty($response)) {
    		$data = json_decode($response, true);
    		if (isset($data) && !empty($data) 
    			&& isset($data[$this->mDataName]) && !empty($data[$this->mDataName])) {
    			$res = $data[$this->mDataName];
    		}
    	}

    	return $res;
    }

    /**
     * @brief     curl post函数。
     * @author    赵一
     * @param[in] $url  地址。
     * @param[in] $data post数据。
     * @return    响应数据。
     */
    public function _curlPost($url, $data) {

    	$postData = '';
    	foreach ($data as $k => $v) {
    		$postData .= "$k=" . $v. "&" ;
    	}
    	$postData = substr($postData, 0, -1);
    	 
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	curl_setopt($curl, CURLOPT_POST, true);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	$response = curl_exec($curl);
    	if (empty($response)) {
    		curl_close($curl); // close cURL handler
    	}
    	else {
    		$info = curl_getinfo($curl);
    		curl_close($curl); // close cURL handler
    		if($info['http_code'] != 200 && $info['http_code'] != 201 ) {
    			//error
    		}
    	}
    	
    	return $response;
    }
    
    /**
     * @brief 用户服务器配置。
     */
    protected $mConfig = null;

    /**
     * @brief 数据标识位。
     */
    protected $mDataName = null;

    /**
     * @brief 数据加秘钥。
     */
    protected $mDataKey = null;

    /**
     * @brief 命令版本号。
     */
    const CMD_VERSION = "1.0.0";
}

?>
