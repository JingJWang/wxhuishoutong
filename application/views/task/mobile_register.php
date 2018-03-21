
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>注册界面</title>
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<link rel="stylesheet" href="/static/task/taskgame/css/login.css?v=10000">
<link rel="stylesheet" href="/static/task/taskgame/css/reset.css">
<script src="/static/task/taskgame/js/zepto.min.js"></script>
<!-- <script src="js/touch3.js"></script> -->
<script>
$(document).ready(function(e) {
		//阻止手机默认触摸操作事件
		//$(document).bind("touchstart",function(e){ e.preventDefault(); });//阻止默认触摸效果
		//$(document).bind("touchmove",function(e){ e.preventDefault(); });//阻止默认触摸效果
		//$(document).bind("touchend",function(e){ e.preventDefault(); });//阻止默认触摸效果
		var winH = $(window).height();
		var winW = $(window).width();
		$(".content").width(winW);
		$(".content").height(winH);
});
</script>

</head>
<body>
<div class="content" id="login">
		<div class="loginBu"></div>
		<p class="loginTil" style="background:url(/static/task/taskgame/img/game_time_bg.png) no-repeat 50% 0%;height:100px;font-size:16px;padding-top:10px;"><span style="color:#000; padding-top:20px;">恭喜您<br/>获得<span class="loginMoney"><?php echo $center_fund; ?></span>元环保基金</span></p>
		<h3 class="loginTxt" style="color:#fff;font-family:'兰亭黑-简';">快来注册领取吧~</h3>
		<div id="loginForm" style="border-radius:5px;">
				<ul>
					<li class="clear">
						<input type="text" style="border-radius:5px;background:#9E8357;border:1px solid #B9B9B9;" class="loginPhone" id="mobile" value="" placeholder="手机号" />
						<input type="button" class="getCode" style="text-indent:0rem;" onclick="Getcode(this);" value="获取验证码">
						<input type="hidden" id="params" value="<?php echo $params; ?>">
        				<input type="hidden" id="invitation" value="<?php echo isset($_SESSION['userinfo']['extendnum'])?$_SESSION['userinfo']['extendnum']:''; ?>">
					</li>
					<li >
						<input type="" name="" id="imgcode" value="" placeholder="请输入图形验证码" class="fl enter" onblur="codelength()" onclick="vanish();"/>
						<span class="fr" style="margin:10.5px 0px;"><img src="/codeimg/code_char.php" width="80px" onclick="this.src='/codeimg/code_char.php?name=2&d='+Math.random();" alt=""></span>
					</li>
					<li>
						<input type="text" style="border-radius:5px;background:#9E8357;border:1px solid #B9B9B9;" class="loginCode" id="code" placeholder="验证码" />
					</li>
					<li>
						<input type="password" style="border-radius:5px;background:#9E8357;border:1px solid #B9B9B9;" class="loginPhone" id="password" placeholder="密码" />
						<button type="button" class="getCode" onclick="lookpwd();">查看</button>
					</li>
					<!-- <li>
						<input class="loginPass" id="invitation" placeholder="邀请码（选填）" />
					</li> -->
					<li><button type="button" class="loginSubmit" style="margin:0 auto;" onclick="Checkcode(this);">确认注册并领取</button></li>
				</ul>
		</div>
</div>

<script src="/static/task/js/jquery-1.9.1.min.js"></script>
<script src="/static/task/js/f_js.js"></script>
<script src="/static/task/js/ajax_common.js"></script>
<script src="/static/task/js/ajax_mobile.js"></script>
<script type="text/javascript">
function lookpwd(){
    var type=$("#password").attr("type")
    if(type == 'password'){
        $("#password").attr("type","text");
    }else{
        $("#password").attr("type","password");
    }
}
</script>
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