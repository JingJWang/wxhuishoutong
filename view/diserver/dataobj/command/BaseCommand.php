<?php 
/**
 * @brief   命令基础类定义文件。
 * @author  赵一
 * @date    2016/02/19
 * @version 0.1
 */

/**
 * @class  Command
 * @brief  命令基础类。
 * @author 赵一
 */
class BaseCommand {
    /**
     * @brief  构造函数。
     * @author 赵一
     */
    public function __construct() {
    }

    /**
     * @brief     执行函数。
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    public function execute($params) {
        return $this->_execute($params);
    }

    /**
     * @brief     执行函数。(派生类实现)
     * @author    赵一
     * @param[in] $params 命令参数。
     * @return    执行结果
     */
    protected function _execute($params) {
    	return null;
    }

    /**
     * @brief 命令逻辑处理结果（成功）
     */
    const CMD_RES_OK = 0;

    /**
     * @brief 命令逻辑处理结果（参数错误）
     */
    const CMD_RES_ERR_PARAMS = 1;
    
    const DEFAULT_VALUE_STAGE = 1;
    const DEFAULT_VALUE_FRAG = 5;
    const DEFAULT_VALUE_BOMB = 1;
}

?>
