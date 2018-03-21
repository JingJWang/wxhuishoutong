<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通-数码回收</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css?v=20160709"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>		
	</head>
	<body  onload="swiperheight()">
		<!--  green start  -->
		<div class="green">
			<div class="backselect">
				<a href="JavaScript:history.back(1);" class="backa">返回</a>
				<div class="mySelect">
			    	<a class="bmselected"><?php echo $proname; ?></a>
			        <ul class="msUl">
			        	
			        </ul>
			        <div class="gray"></div>
			    </div>
			    <!--<a class="search-btn" href="http://wx.recytl.com/index.php/nonstandard/submitorder/BrandSearch"></a>-->
			</div>
		</div>
		<!--  green end  -->
		<!--  brand start  -->
        <div class="afficle">
        	<div class="contet">如果宝贝是山寨、高仿或描述严重不符需自付往返邮费</div>
        </div>
        <div class="searchBox">
        	<div class="search clearfix" style="border-bottom: 1px solid #ddd;">
        		<p id="value" class="fl" onclick="searchselect()">手机</p>
        		<input type="text" name="" id="keyword" value="" placeholder="输入您的手机品牌型号查询" class="searchInput fl" onclick="jump();"/>
        		<a href="javascript:;" class="fr"></a>
        	</div>
        	<div class="downSlide">
        		<ul>
        			<li class="pinName" data-key="5" onclick="GetBrands(5);">手机</li>
        			<li onclick="GetBrands(7);">平板</li>
        		</ul>
        	</div>
        </div>
        <div style="width:100%;height:83px;"></div>
		<div class="box">
			<div class="title clearfix">
				<div class="titleLeft fl">品牌</div>
				<div class="titleRight fl">型号</div>
			</div>	
			<div class="con clearfix">
				<div class="conLeft fl">
					<ul id="brand">
						
					</ul>
				</div>
				<div class="conRight fl">
					<ul id="type">
						
					</ul>
				</div>
			</div>
		</div>
		<!--  brand end  -->
	</body>
<script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
<script type="text/javascript" src="../../../static/home/js/ajax_brandtype.js"></script>
<script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
</html>
