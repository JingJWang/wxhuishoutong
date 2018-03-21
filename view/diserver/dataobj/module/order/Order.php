<?php 
/**
 * @brief   订单类定义文件。
 * @author  赵一
 * @date    2016/06/08
 * @version 0.1
 */

include_once PATH_DATAOBJ_API_DATABASE . 'MySQL.php';

/**
 * @class  Order
 * @brief  订单类。
 * @author 赵一
 */
class Order {
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
	 * @brief     增加订单信息函数。
	 * @author    赵一
     * @param[in] $openid  用户id。
     * @param[in] $amount  总金额。
     * @param[in] $free    现金券金额。
     * @param[in] $type    货币种类。
     * @param[in] $orderId 支付订单号。
     * @param[in] $tradeId 商户订单号。
     * @param[in] $channel 渠道。
     * @param[in] $time    支付完成时间。
	 * @return    插入结果(true:成功;false:失败)。
	 */
	public function add($openid, $amount, $free, $type, $orderId, $tradeId, $channel, $time) {
		if (isset($openid) && !empty($openid)) {
			//image json字符串化
			if (isset($skill) && !empty($skill) && is_array($skill)) {
				$skill = json_encode($skill);
			}
			else {
				$skill = '';
			}
			
			//将数据插入缓冲表
			$sql = sprintf('insert into `%s` (`openid`, `amount`, `free`, `type`, `orderId`, `tradeId`, `channel`, `time`) values(\'%s\', %d, %d, \'%s\', \'%s\', \'%s\', \'%s\', %d);',
					self::TABLE_NAME,
					$openid, 
					$amount, 
					$free, 
					$type, 
					$orderId, 
					$tradeId, 
					$channel, 
					$time);
			$queryRes = $this->mDb->query($sql);
		}

		return $queryRes;
	}

    /**
     * @brief     取得订单信息（通过微信openid）函数。
     * @author    赵一
     * @param[in] $openid 微信openid。
     * @return    用户信息。
     */
    public function get($openid) {
    	$sql = sprintf('select ' . $this->_getField(). ' from `%s` where `openid`=\'%s\'',
    			self::TABLE_NAME,
    			$openid,
				$game);
    	$queryRes = $this->mDb->query($sql);
    	if ($queryRes) {
    		$queryData = $this->mDb->get($queryRes);
    		$this->_convertFromDB($queryData);
    	}

    	return $queryData;
    }

    /**
     * @brief     删除订单信息函数。
     * @author    赵一
	 * @param[in] $id 订单索引id。
     * @return    设置结果(true:成功;false:失败)。
     */
    public function del($id) {
    	if (isset($id) && !empty($id)) {
	    	$sql = sprintf('delete from `%s` where `id`=%d',
	    			self::TABLE_NAME,
	    			$id);
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
    	return '`id`, `openid`, `amount`, `free`, `type`, `orderId`, `tradeId`, `channel`, `time`, `ext`';
    }

    /**
     * @brief         数据转换函数（将数据库数据转换为php数据）。
     * @author        赵一
	 * @param[in/out] $datas 数据。
     */
    private function _convertFromDB(&$datas) {
    	if (isset($datas) && !empty($datas) && is_array($datas)) {
    		
    	}
    }

    /**
     * @brief 用户信息表名称。
     */
    const TABLE_NAME = 'order';

    /**
     * @brief 数据库实例。
     */
    private $mDb;
}

?>
