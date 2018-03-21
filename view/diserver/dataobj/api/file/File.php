<?php 
/**
 * @brief   文件类定义文件。
 * @author  赵一
 * @date    2016/02/19
 * @version 0.1
 */

/**
 * @class  File
 * @brief  文件类。
 * @author 赵一
 */
class File {
	/**
	 * @brief  构造函数。
	 * @author 赵一
	 */
	public function __construct() {
	}

    /**
     * @brief     写文件函数。
     * @author    赵一
     * @param[in] $filename 文件名。
     * @param[in] $log      日志内容。
     * @return    写入成功时返回写入字节数，否则返回false。
     */
    public function write($filename, $log) {
        $result = false;

        $date = date('Ymd', time());
        $path = PATH_LOG . $date .'/';
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }

        $file = $path . $filename;

        $result = file_put_contents($file,
                                    print_r($log, TRUE),
                                    FILE_APPEND);

        return $result;
    }
}

?>
