<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<title>回收通</title>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" href="../../../static/m/css/m_indexjj.css" />
		<link rel="stylesheet" href="../../../static/m/css/swiper.3.1.2.min.css" />
	</head>
	<body>
		<div class="swiper-container">
			<!--轮播-->
		    <div class="swiper-wrapper">
		        <div class="swiper-slide"><a href="javascript:;"><img src="../../../static/m/images/banner09091.jpg"></a></div>
		        <div class="swiper-slide"><a href="javascript:;"><img src="../../../static/m/images/banner09092.jpg"></a></div>
		        <div class="swiper-slide"><a href="http://wx.recytl.com/view/gold/metal.html"><img src="../../../static/m/images/banner09093.jpg"></a></div>
		        <div class="swiper-slide"><a href="http://wx.recytl.com/view/shop/list.html"><img src="../../../static/m/images/banner09094.jpg"></a></div>
		        <div class="swiper-slide"><a href="http://wx.recytl.com/view/article/knowledgeIdx.html"><img src="../../../static/m/images/banner09095.jpg"></a></div>
		        <div class="swiper-slide"><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2f/view/shop/digittype.html&response_type=code&scope=snsapi_base&state=#wechat_redirect"><img src="../../../static/m/images/banner09096.jpg"></a></div>
		    </div>
		    <!-- 分页器 -->
		    <div class="swiper-pagination"></div>
		    <div class="swiper-pagination"></div>
		    <div class="swiper-pagination"></div>
		    <div class="swiper-pagination"></div>
		    <!--搜索框-->
			<div class="positionss">
				<b class="ssB"></b>
				<input type="text" class="ssInput" placeholder="输入您的手机品牌型号查询" name="" id="" onclick="pageJump()">
			</div>
		</div>
		<!--banner结束-->
		<div class="aggregate">
			<div class="aggrDiv">
				<p class="aggDivP">成交单数</p>
				<p class="aggDivPN">
					<span class="aggDivNum"><?php echo $deal['number']; ?></span>
					<span class="aggDivP">单</span>
				</p>
			</div>
			<div class="aggrDiv">
				<p class="aggDivP">成交金额</p>
				<p class="aggDivPN">
					<span class="aggDivNum">¥<?php echo $deal['volume']; ?></span>
				</p>
			</div>
		</div>
		<div class="product_class">
			<a href="/index.php/nonstandard/submitorder/ViewBrand?id=5" class="productD">
				<span class="imga img"></span>	
				<p class="productP">手机回收</p>
			</a>
			<a class="productD" href="/index.php/nonstandard/submitorder/ViewBrand?id=7" >
				<span class="imgb img"></span>	
				<p class="productP">平板回收</p>
			</a>
			<a class="productD" href="../../../view/gold/metal.html">
				<span class="imgc img"></span>	
				<p class="productP">贵金属回收</p>
			</a>
			<a class="productD" href="http://mp.weixin.qq.com/s?__biz=MzIzOTE5NTMwMg==&mid=503564960&idx=1&sn=0d024e2283648ec165d9fd70bb9d6803&scene=1&srcid=0428kEmaXZG62yl2PZXth55I#wechat_redirect">
				<span class="imgd img"></span>	
				<p class="productP">奢侈品回收</p>
			</a>
			<!--<a class="productD" href="https://jinshuju.net/f/fISg4j">
				<span class="imge img"></span>	
				<p class="productP">高尔夫球回收</p>
			</a>-->
			<a class="productD" href="../../../view/repair/repIndex.html?id=5">
				<span class="imgg img"></span>	
				<p class="productP">手机维修</p>
			</a>
			<a class="productD" href="/index.php/nonstandard/order/ViewOrder?status=n">
				<span class="imgf img"></span>	
				<p class="productP">订单查询</p>
			</a>
		</div>
		<div class="buz">
			<a class="buzA" href="../../../view/newmobile/m_index.html">
				<span class="buzimga buzImg"></span>
				<p class="buzP">1.查找型号</p>
			</a>
			<a class="buzA" href="../../../view/quote/q_index.html">
				<span class="buzimgb buzImg"></span>
				<p class="buzP">2.物品描述</p>
			</a>
			<a class="buzA">
				<span class="buzimgc buzImg"></span>
				<p class="buzP">3.选择报价</p>
			</a>
			<a class="buzA">
				<span class="buzimgd buzImg"></span>
				<p class="buzP">4.货款提现</p>
			</a>
		</div>
		<div class="userRecord">
			<h1 class="userRH1">使用记录</h1>
			<div class="ulDiv">
			<ul>
				   <?php
            		    if(!empty($dynamic)){
            		     foreach ($dynamic as $k=>$v){
            		?>
				<li>
					<div class="firstNews">
						<p class="userP"><?php empty($v['name']) ? $name='微信用户': $name=$v['name'];echo $name;?></p>
						<p class="userP"><?php echo substr($v['mobile'],0,3).'*****'.substr($v['mobile'],8,11);  ?></p>
						<p class="userP">评价：</p>
						<p class="uTime">
						<?php
            				 $time=(time() - $v['time'])/60;
            				 $number=floor($time);
            				 if($number < 60 ){
            				     echo $number.'分钟以前';
            				 }
            				 if($number > 60 && $number < 1440 ){
            				     echo floor(($number/60)).'小时以前';
            				 }
            				 if($number > 1440){
            				     echo floor(($number/1440)).'天以前';
            				 }
            		     ?>
            		     </p>
					</div>
					<div class="pjcont">
						<?php empty($v['content']) ? $reason='回收通平台服务好，价格高': $reason=$v['content'];echo $reason; ?>
					</div>
					<div class="shopNews">
						<p class="shopNa">成功卖出<?php echo $v['type']; ?>,获得</p>
						<p class="shopNb"><?php echo $v['moeny']; ?></p>
						<p class="shopNa">奖励</p>
					</div>
				</li>
				    <?php } } ?>
				
			</ul>
			</div>
			<div class="spanIndex">
				<a class="onea"></a>
				<a></a>
				<a></a>
				<a></a>
				<a></a>
			</div>
		</div>
		<div class="banquan">
			© 2014  回收通 版权所有，并保留所有权利
		</div>		
		<div class="bottomNav">
			<div class="select">
				<a href="/index.php/nonstandard/system/welcome" class="select01">回收/寄售</a>
    			<a href="/index.php/task/usercenter/taskcenter">福利</a>
    			<a href="/view/shop/list.html">商城</a>
    			<a href="/index.php/nonstandard/center/ViewCenter">我的</a>
			</div>
		</div>        
        <script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../../../static/m/js/swiper-3.4.2.jquery.min.js"></script>
		<script type="text/javascript" src="../../../static/m/js/index.js"></script>
		<!-- 百度统计代码 -->
		<script>
			window.onload=window.onresize=function(){
				document.documentElement.style.fontSize=window.innerWidth/16+'px';
			};
            var _hmt = _hmt || [];
            (function() {
              var hm = document.createElement("script");
              hm.src = "//hm.baidu.com/hm.js?a337a5249adb71bc3f563821242e0c34";
              var s = document.getElementsByTagName("script")[0]; 
              s.parentNode.insertBefore(hm, s);
            })();
        </script>
        <script type="text/javascript">
		!function(win) {
		    function resize() {
		        var domWidth = domEle.getBoundingClientRect().width;
		        if(domWidth / v > 540){
		            domWidth = 540 * v;
		        }
		        win.rem = domWidth / 16;
		        domEle.style.fontSize = win.rem + "px";
		    }
		    var v, initial_scale, timeCode, dom = win.document, domEle = dom.documentElement, viewport = dom.querySelector('meta[name="viewport"]'), flexible = dom.querySelector('meta[name="flexible"]');
		    resize();
		}(window);
	</script>
	</body>
</html>
