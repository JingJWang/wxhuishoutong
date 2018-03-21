<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
		<title>回收通-订单列表</title>
		<link rel="stylesheet" href="../../../static/gold/css/public.css" />
		<link rel="stylesheet" href="../../../static/gold/css/recycleHead.css" />
		<link rel="stylesheet/less" href="../../../static/gold/css/listRecycleAll.less">
	</head>
	<body>
		<header>
			<a href="../../../index.php/nonstandard/center/ViewCenter" class="FirstA">
				<img src="../../../static/gold/img/header-img1.png">
				<p>返回</p>
			</a>
			<h1>订单详情</h1>
		</header>
		<div class="indent">
			<a class="indent_hover" onclick='orderList.list(this,"all");'>全部订单</a>
			<a onclick='orderList.list(this,"electron");'>数码回收</a>
			<a onclick='orderList.list(this,"metal");'>贵金属回收</a>
			<a onclick='orderList.list(this,"deal");'>已完成订单</a>
		</div>
		<div class="AllList">
			<!--贵金属回收-->
			<div class="accomplishList">
			 
		    </div>
		</div>
	</body>
	<script type="text/javascript" src="../../../static/gold/js/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="../../../static/gold/js/less.min.js"></script>
	<script type="text/javascript" src="../../../static/gold/js/listRecycleTab.js"></script>
	<script type="text/javascript" src="../../../static/m/ajax/r_common.js"></script>
	<script type="text/javascript" src="../../../static/m/ajax/r_order.js?v=2017042001"></script>
	<script type="text/javascript">
    	window.onload=window.onresize=function(){
    		document.documentElement.style.fontSize=window.innerWidth/16+'px';
    	}
	</script>
</html>
