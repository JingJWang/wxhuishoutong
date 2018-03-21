<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('Asia/Shanghai');
/**********签到奖励****************/
$config['sign_reward'] = array(
							'first'=>array('integral'=>10,'all_integral'=>10,'bonus'=>0,'fund'=>0),//签到第一次
							'nocon'=>array('integral'=>10,'all_integral'=>10,'bonus'=>0,'fund'=>0),//签到未连续
							'cononeday'=>array('integral'=>15,'all_integral'=>15,'bonus'=>0,'fund'=>0),//签到连续两天
							'contwoday'=>array('integral'=>20,'all_integral'=>20,'bonus'=>0,'fund'=>0),//连续三天及以上
						);
/**********签到奖励****************/
$config['announcements'] = array(
	'通话商城又上新东西，又好玩',
);
$config['click_tasks'] = array(
								'67' => array('img'=>'task_types9','icon'=>'去答题'),
								'70' => array('img'=>'gamepac','icon'=>'去领取'),
								'73' => array('img'=>'sousoushenbian','icon'=>'去注册'),
								'75' => array('img'=>'quanmama','icon'=>'去注册'),
								'76' => array('img'=>'liujianfang','icon'=>'看直播'),
								'77' => array('img'=>'youpiaole','icon'=>'去注册'),
								'78' => array('img'=>'telegou','icon'=>'去注册'),
								'79' => array('img'=>'','icon'=>'去关注'),
								'80' => array('img'=>'','icon'=>'去领取'),
								'81' => array('img'=>'kankan','icon'=>'去看看'),
								'82' => array('img'=>'eleplay','icon'=>'去注册'),
                                '83' => array('img'=>'dushu','icon'=>'去领取'),
                                '84' => array('img'=>'lingyuangou','icon'=>'去领取'),
                                '85' => array('img'=>'kaola','icon'=>'去领取'),
                                '86' => array('img'=>'yanxuan','icon'=>'去领取'),
                                '87' => array('img'=>'dushu','icon'=>'去领取'),
);
/* End of file config.php */
/* Location: ./application/config/common.php */
