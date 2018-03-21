<?php 
/**
 * @brief   MySQL数据库类定义文件。
 * @author  赵一
 * @date    2016/02/19
 * @version 0.1
 */

/**
 * @class  MySQL
 * @brief  MySQL数据库类。
 * @author 赵一
 */
class MySQL {
    /**
     * @brief     构造函数。
     * @author    赵一
     * @param[in] $config 数据库配置。
     */
    public function __construct($config) {
        $this->mHost = $config['host'];
        $this->mUser = $config['username'];
        $this->mPassword = $config['password'];
        $this->mCharset = $config['encoding'];
    }

    /**
     * @brief  连接数据库函数。
     * @author 赵一
     * @return true  成功。
     * @return false 失败。
     */
    public function connect() {
        $result = false;
        $this->mCon = mysql_connect($this->mHost, $this->mUser, $this->mPassword);
        if ($this->mCon) {
            mysql_set_charset($this->mCharset, $this->mCon);
            $result = true;
        }

        return $result;
    }

    /**
     * @brief     选择数据库函数。
     * @author    赵一
     * @param[in] $database 数据库名称。
     * @return    true      成功。
     * @return    false     失败。
     */
    public function selectDB($database) {
        $result = false;
        if (mysql_select_db($database, $this->mCon)) {
            $result = true;
        }

        return $result;
    }

    /**
     * @brief     查询函数。
     * @author    赵一
     * @param[in] $sql 查询语句。
     * @return    混合查询结果。(false为失败)
     */
    public function query($sql) {
        $result = mysql_query($sql, $this->mCon);
        if (!$result) {
            if (mysql_errno($this->mCon) == 2006) {
                // try to reconnect
                if ($this->connect()) {
                    $result = mysql_query($sql, $this->mCon);
                    if (!$result) {
                        //TODO: error!!!
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @brief     数据取得函数。
     * @author    赵一
     * @param[in] $result 查询结果。
     * @return    数据。
     */
    public function get($result) {
        $rows = array();

        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @brief  关闭数据库函数。
     * @author 赵一
     */
    public function close() {
        mysql_close($this->mCon);
    }

    /**
     * @brief 数据库地址。
     */
    protected $mHost;

    /**
     * @brief 用户名。
     */
    protected $mUser;

    /**
     * @brief 用户密码。
     */
    protected $mPassword;

    /**
     * @brief 字符集。
     */
    protected $mCharset;

    /**
     * @brief 数据库连接句柄。
     */
    protected $mCon;
}

?>
