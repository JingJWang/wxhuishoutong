<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收取消交易</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
	</head>
	<body>
		<!--  green start  -->
		<div class="green">
			<div class="back">
				<p>取消交易</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end  -->
		<!--  green start  -->
		<!--  reason end  -->
		<div class="reason">
			<h3>取消原因</h3>
			<ul>
			<?php foreach($cancel as $key=>$val){ ?>
				<li onclick="selReason(this)"><?php echo $val; ?></li>
		    <?php } ?>
			</ul>
		</div>
		<!--  reason start  -->
		<div class="opinion writeIn">
			<textarea class="title"  name="" rows="4" cols="30" id="make" ></textarea>
		</div>
		<!--  opinion end  -->
		<div class="btn">
			<a href="javascript:;" class="submitBtn" onclick="subcancel('<?php echo $orderid; ?>');">提交</a>
		</div>
	</body>
<script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
</html>
