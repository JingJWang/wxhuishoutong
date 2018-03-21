<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>团收报名</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link   rel="stylesheet" type="text/css" href="../public/css/common.css">
<script type="text/javascript" src="../public/js/jquery.1.4.2-min.js"></script>
<script type="text/javascript" src="../public/js/jquery.cityselect.js?v=2018"></script>
<script type="text/javascript" src="../public/js/common.js"></script>
<script type="text/javascript" src="../public/js/updateorder.js"></script>
<?php
	$id=$_GET['id'];
	!empty($id)?:exit();
	include('config/config.php');
	include('common/mysql.class.php');
	include('do/modelclass/OrderModel.class.php');
	$wx_order=new OrderModel();
	$data=$wx_order->getorderinfo($id);
	?>	
<script>
$(document).ready(function(){
	//默认城市
	$("#city_1").citySelect({
		prov:"<?php echo $data[0]['order_province']; ?>", //省份 
		city:"<?php echo $data[0]['order_city']; ?>", //城市 
		dist:"<?php echo $data[0]['order_county']; ?>", //区县
	});	
	
});
</script>
<body>
<div class="top t_c">
回收通
</div>
<div class="wrap" style="padding-top:20px;">
<p class="size16 red">
每次回收活动，每人最多可出售30kg旧衣服
</p>			
		<p class="tab">
		<input type="hidden" id='orderid' value="<?php echo $data[0]['id']; ?>" />
		<span class="tit">手机号&nbsp;<span class="red">*</span></span>
		<input class="kong"  name="phone" id="h_tel"  type="text" value="<?php echo $data[0]['order_mobile']; ?>" />
		</p>
		<div class="tab">
		<span class="tit">地址&nbsp;<span class="red">*</span></span>
		<div class="form_ctrl form_select" id="city_1">
			<select name="h_province" id="h_province" class="prov selectbg margint10"></select>
			<div></div>
			<select name="h_city" id="h_city" class="city selectbg margint10" disabled="disabled"></select>
			<div></div>
			<select name="h_dist" id="h_dist" class="dist selectbg margint10" disabled="disabled"></select>
			<div></div>
		</div>
		</div>
		<p class="tab">
		<span class="tit">小区名称&nbsp;<span class="red">*</span></span>
		<input class="kong"  name="phone" id="h_addinfo" type="text" value="<?php echo $data[0]['order_address']; ?>" />
		</p>
		<div class="tab">
		<span class="tit">旧衣件数&nbsp;<span class="red">*</span></span>
		<select class="selectbg" id="number">
		<option value="1">请选择</option>
		<option value="1" <?php echo  $data[0]['order_num']==1? 'selected ="selected"' :''; ?>>10件以下</option>
		<option value="2" <?php echo  $data[0]['order_num']==2? 'selected ="selected"' :''; ?>>10-40件</option>
		<option value="3" <?php echo  $data[0]['order_num']==3? 'selected ="selected"' :''; ?>>40-80件</option>
		<option value="4" <?php echo  $data[0]['order_num']==4? 'selected ="selected"' :''; ?>>80件以上</option>
		</select>
		<input type="hidden" name="h_number" id="h_number" value="1">
		</div>
		<p class="t_c">
		<input class="guanzhu" value="修  改" type="button" onclick="updateorder();"/>
		</p>
		</div>
</body>
</html>
