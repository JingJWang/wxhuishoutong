<?php 

$url = urlencode("http://mrzh.happybluefin.com/H5Game/xpg/getScore.html");

$smartparking = array('data' => array('cmd' => 'GetKeyByWX',
                                      'params' => array('url' => $url)),
                      'type' => CMD_TYPE_CS,
                      'version' => '1.0.0',
                      'sign' => 'sign');

$_REQUEST['smartparking'] = json_encode($smartparking);

include_once '../json/Gateway.php';



//{"smartparking":{"data":"业务数据","type":"类型","version":"版本号","sign":"签名"}}
//{"smartparking":{"data":{"cmd":"命令名称", "params":"命令参数"},"type":"类型","version":"版本号","sign":"签名"}}

//gateway();

?>
