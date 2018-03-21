<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('Asia/Shanghai');
/**********运营商手机头几位区别****************/
$config['mobile_operators'] = array(
							'corporation'=>array(134,135,136,137,138,139,147,150,151,152,157,158,159,178,182,183,184,187,188),//移动
							'unicom'=>array(130,131,132,155,156,186,185,145),//联通
							'telecom'=>array(133,153,177,180,181,189,171,170),//电信
						);
/**********比特峰配置****************/
$config['bitefeng'] = array(
	'toke' => 'be8f691ff5712fe165c062846823176d',
	'corpid' => 'bitfeng1',
	'url' => 'http://api.bitfeng.cn:11086/bitfeng_test/charge_std_test/index',
);
/* End of file config.php */
/* Location: ./application/config/common.php */
