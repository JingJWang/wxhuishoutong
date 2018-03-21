<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>回收通</title>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link type="text/css" rel="stylesheet" href="../public/css/common.css" >
</head>
<body class="bgh">
<div class="top t_c">
回收通
</div>
<?php 
$id=$_GET['id'];
!empty($id)?:exit();
include('config/config.php');
include('common/mysql.class.php');
include('do/modelclass/OrderModel.class.php');
$wx_order=new OrderModel();
$data=$wx_order->getorderlist($id,'1');
?>
<div class="wrap jiaoyi">
<p class="size18">每人每天最多可出售30kg旧衣服！</p>
<p class="paddingl10">手机号：<?php echo $data['info'][0]['order_mobile'];?></p>
<p class="paddingl10">地址：<?php echo $data['info'][0]['order_province'].$data['info'][0]['order_city'].$data['info'][0]['order_county'];?></p>
<p class="paddingl10">小区名称：<?php echo $data['info'][0]['order_address'];?></p>
<p class="paddingl10">旧衣件数：<?php 
									switch($data['info'][0]['order_num']){
										case '1':
											echo '10件以下';
											break;
										case '2':
											echo '10-40件';
											break;
										case '3':
											echo '40-80件';
											break;
										case '4':
											echo '80件以上';
											break;
									} ;
								?></p>
<p class="paddingl10">报单时间：<?php echo $data['info'][0]['order_joindate'];?></p>
<p class="paddingl10 margint20">状态：未成交</p>
<p class="jiaoyiwei">
<input class="guanzhu" value="修    改" type="button" />
<input class="guanzhu" value="删    除" type="button" />
</p>
</div>
</body>
</html>
