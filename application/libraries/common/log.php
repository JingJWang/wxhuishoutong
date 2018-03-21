<?php
/*
 * 系统错误日志 ,报警,扩展
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Log extends  CI_Model{
    
    
    /**
     * 记录系统级错误日志
     * @param   string     $filename  日志文件名称
     * @param   string     $content   记录内容
     */
    function  Record($filename,$content){
        //date('Y-m-d H:i:s').'类型'."\r\n".$log_content."\r\n"        
        file_put_contents($filename,'发生日期:'.date('Y-m-d H:i:s').$content,FILE_APPEND);        
    }
    /**
     * 发送报警通知 短信方式
     * @param      string     mobile   手机号码 端个一逗号隔开
     * @param      string     content  手机号码 端个一逗号隔开
     */
    function  SendWarningMsg(){
        
        
    }
    
}