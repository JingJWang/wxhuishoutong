<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通-个人资料</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/address.css"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript"src="../../../static/m/js/Popt.js"></script>
		<script type="text/javascript"src="../../../static/m/js/cityJson.js"></script>
		<script type="text/javascript"src="../../../static/m/js/citySet.js"></script>
	</head>
	<body>
		<!--  green start      -->
		<div class="green">
			<div class="back">
				<p>个人资料</p>
				<a href="JavaScript:;" onclick="javascript:history.back(-1);" class="backBtn">返回</a>
				<a href="/index.php/nonstandard/system/welcome" class="home">主页</a>
			</div>
		</div>
		<!--  green end      -->
		<!--  phoneNo start  -->
		<div class="phoneNo clearfix">
			<p class="title fl">手机号</p>
			<p class="number fr"><?php echo $mobile; ?></p>
		</div>
		<!--  phoneNo end   -->
		<!--  address start  -->
		<div class="address">
			<h3 class="title">地址</h3>
			<div class="addressIn">
				<input type="text" id="city" readonly="readonly" value="<?php echo $address; ?>"/>
			</div>
		</div>
		<!--  address start  -->
		<!--  address start  -->
		<div class="address house">
			<h3 class="title">小区名称</h3>
			<div class="addressIn">
				<input type="text" id="address" value="<?php echo $info; ?>"/>
			</div>
		</div>		
		<!--  house start  -->
		<div class="revise">
			<a href="javascript:;" onclick="editUserData();">修&nbsp;改</a>
		</div>
	</body>
	<script type="text/javascript" src="../../../static/home/js/request_common.js"></script>
	<script type="text/javascript" src="../../../static/m/ajax/r_center.js"></script>
	<script type="text/javascript">
		$("#city").click(function (e) {
			SelCity(this,e);
		});
	</script>
</html>
