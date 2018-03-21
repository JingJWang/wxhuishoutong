<?php 
/**
 * @brief   Curl工具类定义文件。
 * @author  赵一
 * @date    2016/06/25
 * @version 0.1
 */

/**
 * @class  CurlTools
 * @brief  Curl工具类。
 * @author 赵一
 */
class CurlTools {
    /**
     * @brief  构造函数。
     * @author 赵一
     */
    public function __construct() {
    }

    /**
     * @brief  析构函数。
     * @author 赵一
     */
    public function __destruct(){
    }

    /**
     * @brief     post函数。
     * @author    赵一
     * @param[in] $url  地址。
     * @param[in] $data post数据。
     * @return    响应数据。
     */
    public function post($url, $data) {
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
     * @brief     get函数。
     * @author    赵一
     * @param[in] $url 地址。
     * @return    响应数据。
     */
    public function get($url) {
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    	curl_setopt($curl, CURLOPT_HEADER, false);
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
}

?>
