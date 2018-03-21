<?php 

global $config;
$config = array();

//数据库配置定义
$config['db']['user'] = array('host' => 'rdsajqyit46nx6b2cw6h.mysql.rds.aliyuncs.com',
		                      'dbname' => 'di',
		                      'username' => 'huishoutong',
		                      'password' => 'xiaotao_2016_shell',
		                      'pconnect' => false,
		                      'encoding' => 'UTF8',
		                      'dbprefix' => '');

//
//$config['url']['image'] = 'http://54.169.206.102/SmartPark/';

//cache配置定义
$config['memcache']['park'] = array('127.0.0.1:11211');

//日志配置定义
$config['log']['user'] = dirname(__FILE__) . '/log/user';
$config['log']['wx'] = dirname(__FILE__) . '/log/wx';

$config['version'] = '1.0.0';

//test
/*
$config['key']['wx'] = array('id' => 'wxe06f4b9e2ec72545',
		                     'secret' => '66cc3d81458a961e5a7262d9ccbac1cc');
*/

//happybluefin
$config['key']['wx'] = array('id' => 'wx29a596b5eac42c22',
		                     'secret' => '2129003c8d29a592d8f39e4cf0ebf8fe');
?>