<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
		<title></title>
		<link rel="stylesheet" type="text/css" href="../../../static/home/css/evaluate.css">
		<link rel="stylesheet" media="screen" href="../../../static/home/css/cssreset.css" type="text/css" />
		<script type="text/javascript" src="../../../static/home/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/m/js/attr.js"></script>
	</head>
	<body>
		<div class="wrap">
			<header>
				<div class="title">
					<a href="javascript:;" class="TextOverflow"><span>手机型号</span>&nbsp;&nbsp;<samp><?php echo $typename; ?></samp></a>
				</div>
			</header>
			<article>
				<form action="#" method="post" id="attr">
					<div id="property_list" class="property_list">
						<!-- 属性内容 -->
					</div>
					<input id="latitude" name="latitude"  type="hidden" value="" />
                    <input id="longitude" name="longitude" type="hidden" value="" />
				</form>
				<div class="chakan_price" onclick="subOrder()">下一步</div>
			</article>
			<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
			<script type="text/javascript" src="../../../static/home/js/request_common.js"></script>
			<script type="text/javascript" src="../../../static/m/ajax/r_option.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					$("#step1 dl dd").find(".pinggu_other").each(function(i){if(i==0)$(this).css('display','block')})
					$("#step1 input[name='desc_id[]']").each(function(){$(this).val(0);})
				})
			</script>
			<script type="text/javascript">
			 wx.ready(function () {
			    wx.getLocation({
		  		    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
		  		    success: function (res) {
		  		        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
		  		        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
		  		        var speed = res.speed; // 速度，以米/每秒计
		  		        var accuracy = res.accuracy; // 位置精度
		  		        $("#latitude").val(latitude);
		  		        $("#longitude").val(longitude);
		  		   }
			    });
			    });
		    //config 信息配置出错后 出现
			    wx.error(function(res){
		  	  });
			</script>
		</div>
	</body>
</html>