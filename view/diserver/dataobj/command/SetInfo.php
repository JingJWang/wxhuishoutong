<?php 
/**
 * @brief   设置信息命令类定义文件。
 * @author  赵一
 * @date    2016/06/02
 * @version 0.1
 */

/**
 * @class  SetInfo
 * @brief  设置信息命令类。
 * @author 赵一
 */
class SetInfo extends BaseCommand {
    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
    	$res = self::CMD_RES_OK;
    	//检查参数信息
    	if (!isset($params['openid']) || !isset($params['stage']) || !isset($params['gold']) || !isset($params['frag']) || !isset($params['bomb']) || !isset($params['skill'])) {
    		//参数错误
    		$res = self::CMD_RES_ERR_PARAMS;
    	}
    	else {
    		$openid = $params['openid'];

    		$stage = $params['stage'];
    		$gold = $params['gold'];
    		$frag = $params['frag'];
    		$bomb = $params['bomb'];
    		$skill = $params['skill'];

    		include_once PATH_DATAOBJ_MODULE_USER . 'User.php';
    		$userMgr = new User();
    		
    		$userData = $userMgr->get($openid);
    		if (isset($userData) && !empty($userData) && is_array($userData) && (count($userData) > 0)) {
    			//数据存在
    			$userData = $userData[0];

    			$isOk = true;
    			if ($stage < $userData['stage']) {
    				$isOk = false;
    			}
    			
    			if ($isOk == true) {
    				$userData['stage'] = $stage;
    				$userData['gold'] = $gold;
    				$userData['frag'] = $frag;
    				$userData['bomb'] = $bomb;
    				$userData['skill'] = $skill;

    				$userMgr->set($openid, $stage, $gold, $frag, $bomb, $skill);
    			}
    			else {
    				$res = self::CMD_RES_ERR_CHECK_DATA;
    			}
    		}
    		else {
    			//数据不存在
    			$userData['openid'] = $openid;
    			$userData['stage'] = $stage;
    			$userData['gold'] = $gold;
    			$userData['frag'] = $frag;
    			$userData['bomb'] = $bomb;
    			$userData['skill'] = $skill;

    			$userMgr->add($openid, $stage, $gold, $frag, $bomb, $skill);
    		}
    	}

    	//完成任务
    	$this->_accessGameTask(self::GAME_TASK_SERVER_ADDR, $openid, self::GAME_TASK_SERVER_KEY);
    	
    	//返回值设置
    	$result['userData'] = $userData;
    	$result['result'] = $res;

    	return $result;
    }

    /**
     * @brief     访问任务服务器函数。
     * @author    赵一
     * @param[in] $url    任务服务器地址。
     * @param[in] $openid open id。
     * @param[in] $key    秘钥。
     * @return    执行结果
     */
    private function _accessGameTask($url, $openid, $key) {
    	$res = null;
    
    	if (isset($url) && isset($openid) && isset($key)) {
    		try {
		    	$postData = array('openid' => $openid,
		    			          'key' => $key);
		    	include PATH_DATAOBJ_MODULE_TOOLS . 'CurlTools.php';
		    	$curlTools = new CurlTools();
		    	$response = $curlTools->post($url, $postData);
		    	if (isset($response) && !empty($response)) {
					//TODO:
		    	}
    		}
    		catch (Exception $e) {
    			//错误处理（忽略）
    		}
    	}

    	return $res;
    }

    /**
     * @brief 命令逻辑处理结果（上传数据检查出错）
     */
    const CMD_RES_ERR_CHECK_DATA = 201;

    /**
     * @brief 任务服务器地址
     */
    const GAME_TASK_SERVER_ADDR = "http://test.recytl.com/index.php/activity/games/gametask";

    /**
     * @brief 任务服务器地址
     */
    const GAME_TASK_SERVER_KEY = "ksueh78PI";
}

?>
