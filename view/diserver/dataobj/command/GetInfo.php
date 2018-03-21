<?php 
/**
 * @brief   取得信息命令类定义文件。
 * @author  赵一
 * @date    2016/06/02
 * @version 0.1
 */

/**
 * @class  GetInfo
 * @brief  取得信息命令类。
 * @author 赵一
 */
class GetInfo extends BaseCommand {
    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
    	$res = self::CMD_RES_OK;

    	//检查参数信息
    	if (!isset($params['openid'])) {
    		//参数错误
    		$res = self::CMD_RES_ERR_PARAMS;
    	}
    	else {
    		$openid = $params['openid'];

    		include_once PATH_DATAOBJ_MODULE_USER . 'User.php';
    		$userMgr = new User();

    		$userData = $userMgr->get($openid);
    		if (isset($userData) && !empty($userData) && is_array($userData) && (count($userData) > 0)) {
    			/*
    			include_once PATH_DATAOBJ_MODULE_ORDER . 'OrderCache.php';
    			$orderCacheMgr = new OrderCache();
    			$datas = $orderCacheMgr->get($openid);

    			foreach ($datas as $data) {
    				$amount = $data['amount'];
//    				$amount = $data['amount'];

    				//TODO:同步钻石处理
    				
    				$orderCacheMgr->del($data['id']);
    			}
    			*/

    			//数据存在
    			$userData = $userData[0];
    		}
    		else {
    			//数据不存在
                 $userData = array('openid'  => 0,
                					'stage'   => self::DEFAULT_VALUE_STAGE + "",
                					'gold' => 0,
                					'frag'   => self::DEFAULT_VALUE_FRAG + "",
                					'bomb' => self::DEFAULT_VALUE_BOMB + "",
                					'skill' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0));

    			$userMgr->add($openid, self::DEFAULT_VALUE_STAGE, 0, self::DEFAULT_VALUE_FRAG, self::DEFAULT_VALUE_BOMB, array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0));
    		}
    	}

    	//返回值设置
    	$result['userData'] = $userData;
    	$result['result'] = $res;

    	return $result;
    }
}

?>
