<?php
/**
 * 极光推送APP 通知
 *  $this->load->library('vendor/notice');
 *  $this->notice->JPush('alias',array('1447299307','1447494159'),'测试推送接口');
 */
require_once("jpush/jpush/src/JPush/JPush.php");

class Notice {
    
    //极光推送AppKey
    private   $app_key  =  '';    
    //极光推送Master Secret
    private   $master_secret   = '';
    //日志文件
    private   $log_jpush = '';        //极光推送日志';
    //初始化
    function  __construct(){
        $this->app_key='3290c77e3338296715491ebe';
        $this->master_secret='726f15bee3061c7d23bb755f';        
    }
    /**
     * 极光推送
     * @param    array     user         推送列表
     * @param    string    content      内容
     * @param    string    method       推送方式    all 发送系统通知  通知所有人, alias 通知列表内的终端   
     * @return   bool                   结果bool值
     */
    function  JPush($method,$user,$content,$extras=null){        
        //实例化
        $client = new JPush($this->app_key, $this->master_secret);  
        switch ($method){
                case  'all':
                   $result = $client->push()
                        ->setPlatform('all')
                        ->addAllAudience()
                        ->setNotificationAlert($content)
                        ->send();
                    break;               
                case  'alias':
                    $result = $client->push()
                        ->setPlatform('all')
                        ->addAlias($user)
                        ->setNotificationAlert($content)
                        ->send();
                    break;
                case  'voice':
                    $result = $client->push()
                        ->setPlatform('all')
                        ->addAlias($user)
                        ->setMessage($content, '回收通', '1', $extras)
                        ->send();
                        break;
            }    
            return $result;
          }
        
   
    
}