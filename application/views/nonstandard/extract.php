<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/drawingCash.css"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
    </head>
	<body>
		<!--  grayBg start      -->
		<div class="grayBg"></div>
		<!--  grayBg end      -->
		<!--  ifGetOut start      -->
		<div class="ifGetOut">
			<p class="conP"></p>
			<div class="btn clearfix">
				<a href="javascript:;" class="no fl" onclick="noGetOut()">取消</a>
				<a href="javascript:;" class="yes fr" onclick="noGetOut(),emptyCashInput()">确认</a>
			</div>
		</div>
		<!--  ifGetOut end      -->
		<!--  green start      -->
		<div class="green">
			<div class="back">
				<a href="javascript:;" onclick="javascript:history.back(-1)">返回</a>
			</div>
			<div class="nowCash">
				<span class="title">当前余额</span>
				<p class="money">
					<span>￥</span>
					<span class="cur" id="balance"></span>
					<span>元</span>
				</p>
			</div>
		</div>
		<!--  green end      -->
		<!--  headerYellow start      -->
		<div class="headerYellow">
			<p>提现账户：您的微信钱包</p>
		</div>
		<!--  headerYellow end      -->		
		<!--  entry start      -->
		<div class="entry">
			<form action="" method="" name="">
				<div class="money clearfix">
					<span class="mSpan fl">提现金额</span>
					<div class="mInput fl clearfix">
						<input type="" name="" id="cashIput" value="" placeholder="" class="fl mEnter" onblur="checkCash(this)"/>
						<span class="fr">
							<a href="javascript:;" onClick="allCash()">全部提现</a>
						</span>
					</div>
				</div>
				<p class="cashTip"></p>
				<!--  <div class="money note clearfix" >
					<span class="mSpan fl">姓名</span>
					<div class="mInput fl clearfix">
						<input type="" name="" id="nameInput" value="" placeholder="请输入微信实名认证的姓名" class="fl enter name" onblur="checkName(this)"/>
					</div>
				</div>-->
				<p id="nameP"></p>				
				<div class="money note clearfix">
					<span class="mSpan fl">验证码</span>
					<div class="mInput fl clearfix">
						<input type="" name="" id="noteCode" value="" placeholder="请输入验证码" class="fl enter" onblur="codeNo(this)"/>
						<span class="fr"><img src="/codeimg/code_char.php?name=1" id="getcode_char" width="100%" height="90%"/></span>
					</div>
				</div>
				<p id="codeP"></p>				
			</form>
		</div>
		<!--  entry start      -->
		<!--  bottomTip start      -->
		<div class="bottomTip">			
			    <p>1.微信绑定银行卡才可提现，每日提现次数为3次</p>
                <p>2. 由于微信限制，提现金额单次最高为3000RMB</p>
                <p>3.由于微信限制，每次提现金额不小于1 RMB</p>
                <p>3.由于微信限制，每次提现需要间隔1分钟</p>
		</div>
		<!--  bottomTip end      -->		
		<!--  button start      -->
		<div class="isCash">
			<button type="button" id="isCash" onclick="checkCash('#cashIput'),codeNo('#noteCode'),isCash(this),writeCash(),extract()">确认提现</button>			
		</div>
		<!--  button start      -->
		<div class="billboard"></div>
		<a href="javascript:;" class="print"><img src="../../../static/m/images/advert1.png" /></a>
		<!-- --- copy start --- -->
		<div class="copy">
			<p>© 2014-2016 回收通 版权所有，并保留所有权利</p>
		</div>
		<!-- --- copy end --- -->
		<script type="text/javascript" src="../../../static/m/js/personalMsg.js"></script>
		<script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
        <script type="text/javascript" src="../../../static/home/js/ajax_extract.js"></script>
<!-- 百度统计 -->
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?a337a5249adb71bc3f563821242e0c34";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
	</body>
</html>
