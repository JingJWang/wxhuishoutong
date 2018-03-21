<?php 
/**
 * @brief   登陆（通过微信code）命令类定义文件。
 * @author  赵一
 * @date    2016/04/07
 * @version 0.1
 */

/**
 * @class  LoginByWX
 * @brief  登陆（通过微信code）命令类。
 * @author 赵一
 */
class LoginByWX extends BaseCommand {
    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
        $res = self::CMD_RES_OK;

        $isNew = false;
        
        //检查参数信息
        if (!isset($params['code']) || !isset($params['url'])) {
            //参数错误
            $res = self::CMD_RES_ERR_PARAMS;
        }
        else {
            $code = $params['code'];
            $url = $params['url'];

            $wxBaseInfo = $this->_getBaseInfoByWeiXin($GLOBALS['config']['key']['wx']['id'], $GLOBALS['config']['key']['wx']['secret'], $code);
            $openid = $wxBaseInfo['openid'];
            $accessToken = $wxBaseInfo['access_token'];
            if (isset($openid) && !empty($openid)) {
                if (isset($wxBaseInfo['scope']) && !empty($wxBaseInfo['scope'])) {
                    if ($wxBaseInfo['scope'] == 'snsapi_base') {
                        //基础信息
                    }
                    else if ($wxBaseInfo['scope'] == 'snsapi_userinfo') {
                        //全信息
                        $wxUserInfo = $this->_getUserInfoByWeiXin($accessToken, $openid);

                        $result['wxUserInfo'] = $wxUserInfo;
                    }
                    else {

                    }
                }

                include_once PATH_DATAOBJ_MODULE_USER . 'User.php';
                $userMgr = new User();

                $userData = $userMgr->get($openid);
                if (isset($userData) && !empty($userData) && is_array($userData) && (count($userData) > 0)) {
                	//数据存在
                	$userData = $userData[0];
                }
                else {
                	$userMgr->add($openid, self::DEFAULT_VALUE_STAGE, 0, self::DEFAULT_VALUE_FRAG, self::DEFAULT_VALUE_BOMB, array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0));
                	//数据不存在
                	$userData = array('openid'  => 0,
                			'stage'   => self::DEFAULT_VALUE_STAGE + "",
                			'gold' => 0,
                			'frag'   => self::DEFAULT_VALUE_FRAG + "",
                			'bomb' => self::DEFAULT_VALUE_BOMB + "",
                			'skill' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0));
                }

                $url = $params['url'];
                include_once PATH_DATAOBJ_SDK_WX . 'jssdk.php';
                $jssdk = new JSSDK($GLOBALS['config']['key']['wx']['id'], $GLOBALS['config']['key']['wx']['secret']);
                $signPackage = $jssdk->getSignPackage($url);

                $result['signPackage'] = $signPackage;

                $result['userData'] = $userData;
                $result['wxBaseInfo'] = $wxBaseInfo;
            }
            else {
            	//微信登录失败
            	$res = self::CMD_RES_ERR_LOGIN_BY_WX;
            }
        }

        //返回值设置
        $result['result'] = $res;

        return $result;
    }

    /**
     * @brief     取得微信基本信息函数。
     * @author    赵一
     * @param[in] $appId     应用id。
     * @param[in] $appSecret 应用秘钥。
     * @param[in] $code      code。
     * @return    基本信息。
     */
    private function _getBaseInfoByWeiXin($appId, $appSecret, $code) {
        $baseInfo = null;
        $endpoint = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $endpoint .= '?appid=' . $appId;
        $endpoint .= '&secret=' . $appSecret;
        $endpoint .= '&code='.$code;
        $endpoint .= '&grant_type=authorization_code';

        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec( $curl );
        if (empty($response)) {
            curl_close($curl); // close cURL handler
        }
        else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if($info['http_code'] != 200 && $info['http_code'] != 201 ) {
                //error
            }
            else {
                $jsonResponse = json_decode($response, true);
                if (isset($jsonResponse['openid']) && !empty($jsonResponse['openid'])) {
                    $baseInfo = $jsonResponse;
                }
            }
        }

        return $baseInfo;
    }

    /**
     * @brief     取得微信用户信息函数。
     * @author    赵一
     * @param[in] $appId     应用id。
     * @param[in] $appSecret 应用秘钥。
     * @param[in] $code      code。
     * @return    基本信息。
     */
    private function _getUserInfoByWeiXin($accessToken, $openid) {
        $userInfo = null;
        $endpoint = 'https://api.weixin.qq.com/sns/userinfo';
        $endpoint .= '?access_token=' . $accessToken;
        $endpoint .= '&openid=' . $openid;
        $endpoint .= '&lang=zh_CN';

        $curl = curl_init($endpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec( $curl );
        if (empty($response)) {
            curl_close($curl); // close cURL handler
        }
        else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if($info['http_code'] != 200 && $info['http_code'] != 201 ) {
                //error
            }
            else {
                $jsonResponse = json_decode($response, true);
                if (isset($jsonResponse['openid']) && !empty($jsonResponse['openid'])) {
                    $userInfo = $jsonResponse;
                }
            }
        }
    
        return $userInfo;
    }

    /**
     * @brief 命令逻辑处理结果（微信登陆失败）
     */
    const CMD_RES_ERR_LOGIN_BY_WX = 101;

    /**
     * @brief 渠道名称。
     */
    const APP_CHANNEL = "wx";
}

?>
