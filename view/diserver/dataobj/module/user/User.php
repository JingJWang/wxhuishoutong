<?php 
/**
 * @brief   用户类定义文件。
 * @author  赵一
 * @date    2016/05/12
 * @version 0.1
 */

include_once PATH_DATAOBJ_API_DATABASE . 'MySQL.php';

/**
 * @class  User
 * @brief  用户类。
 * @author 赵一
 */
class User {
    /**
     * @brief  构造函数。
     * @author 赵一
     */
    public function __construct() {
    	$this->mDb = new MySQL($GLOBALS['config']['db']['user']);
    	$this->mDb->connect();
    	$this->mDb->selectDB($GLOBALS['config']['db']['user']['dbname']);
    }

    /**
     * @brief  析构函数。
     * @author 赵一
     */
	public function __destruct(){
	    $this->mDb->close();
	}

	/**
	 * @brief     增加用户信息函数。
	 * @author    赵一
	 * @param[in] $openid 微信openid。
	 * @param[in] $game   游戏名称。
	 * @param[in] $times  游戏次数。
	 * @param[in] $score  最高分。
	 * @return    插入结果(true:成功;false:失败)。
	 */
	public function add($openid, $stage, $gold, $frag, $bomb, $skill) {
		if (isset($openid) && !empty($openid)) {
			//image json字符串化
			if (isset($skill) && !empty($skill) && is_array($skill)) {
				$skill = json_encode($skill);
			}
			else {
				$skill = '';
			}
			
			//将数据插入缓冲表
			$sql = sprintf('insert into `%s` (`openid`, `stage`, `gold`, `frag`, `bomb`, `skill`) values(\'%s\', %d, %d, %d, %d, \'%s\');',
					self::TABLE_NAME,
					$openid,
					$stage,
					$gold,
					$frag,
					$bomb,
					$skill);
			$queryRes = $this->mDb->query($sql);
		}

		return $queryRes;
	}

    /**
     * @brief     取得用户信息（通过微信openid）函数。
     * @author    赵一
     * @param[in] $openid 微信openid。
	 * @param[in] $game   游戏名称。
     * @return    用户信息。
     */
    public function get($openid) {
    	$sql = sprintf('select ' . $this->_getField(). ' from `%s` where `openid`=\'%s\'',
    			self::TABLE_NAME,
    			$openid);
    	$queryRes = $this->mDb->query($sql);
    	if ($queryRes) {
    		$queryData = $this->mDb->get($queryRes);
    		$this->_convertFromDB($queryData);
    	}

    	return $queryData;
    }

    /**
     * @brief     设置用户信息函数。
     * @author    赵一
	 * @param[in] $openid 微信openid。
	 * @param[in] $game   游戏名称。
	 * @param[in] $times  游戏次数。
	 * @param[in] $score  最高分值。
     * @return    设置结果(true:成功;false:失败)。
     */
    public function set($openid, $stage, $gold, $frag, $bomb, $skill) {
    	if (isset($openid) && !empty($openid)) {

    		//image json字符串化
			if (isset($skill) && !empty($skill) && is_array($skill)) {
				$skill = json_encode($skill);
			}
			else {
				$skill = '';
			}

	    	$sql = sprintf('update `%s` set `stage`=%d, `gold`=%d, `frag`=%d, `bomb`=%d, `skill`=\'%s\' where `openid`=\'%s\'',
	    			self::TABLE_NAME,
	    			$stage,
	    			$gold,
	    			$frag,
	    			$bomb,
					$skill,
	    			$openid);
	    	$queryRes = $this->mDb->query($sql);
    	}

    	return $queryRes;
    }

    /**
     * @brief  取得数据表字段名函数。
     * @author 赵一
     * @return 数据表字段名。
     */
    private function _getField() {
    	return '`openid`, `stage`, `gold`, `frag`, `bomb`, `skill`';
    }

    /**
     * @brief         数据转换函数（将数据库数据转换为php数据）。
     * @author        赵一
	 * @param[in/out] $datas 数据。
     */
    private function _convertFromDB(&$datas) {
    	if (isset($datas) && !empty($datas) && is_array($datas)) {
    		foreach ($datas as &$data) {
    			if (isset($data['skill']) && !empty($data['skill'])) {
    				$data['skill'] = json_decode($data['skill'], true);
    			}
    		}
    	}
    }

    /**
     * @brief 用户信息表名称。
     */
    const TABLE_NAME = 'user';

    /**
     * @brief 数据库实例。
     */
    private $mDb;
}

?>
