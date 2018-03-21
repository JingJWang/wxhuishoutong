<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <link rel="stylesheet" type="text/css" href="/static/m/css/cssReset.css"/>
    <link rel="stylesheet" type="text/css" href="/static/m/css/withdrawCash.css"/>
</head>
<body>
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
<div class="entryBar">
    <div class="words fl">提现金额</div>
    <a class="distill fr" href="javascript:;">全部提现</a>
    <div class="entry">
        <input class="feed" id="cashIput" type="text" placeholder="请输入提现金额"/>
    </div>
</div>
<div class="chosen">
    <a class="braised" href="/view/m/prompt.html">
        <div class="mode fl">微信</div>
        <div class="right fr"></div>
    </a>
    <a class="braised alipay" href="javascript:;">
        <div class="mode fl">支付宝</div>
        <div class="right fr"></div>
    </a>
    <div class="information">
        <div class="substance">
            <div class="cue fl">账号</div>
            <div class="import">
                <input class="info" id="account" type="text" placeholder="请输入您的支付宝账号"/>
            </div>
        </div>
        <div class="substance">
            <div class="cue fl">姓名</div>
            <div class="import">
                <input class="info" id="nameInput" type="text" placeholder="请输入支付宝实名认证的姓名"/>
            </div>
        </div>
        <div class="substance last">
            <div class="cue fl">账号</div>
            <div class="import">
                <input class="info" id="noteCode" type="text" placeholder="请输入图中验证码"/>
                <a class="code" href="javascript:;">
                    <img src="/codeimg/code_char.php?name=1" id="getcode_char"/>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="handle">
    <a class="confirm" href="javascript:;" onclick="zfbextract()">确认提现</a>
</div>
<div class="show_box" style="display: none;">
	<div class="withSuccess">
		<div class="WStop">
			<p class="WStop_first">提现成功</p>
			<p class="WStop_second">请在微信钱包余额中查看</p>
		</div>
		<a href="javascript:;" class="ws_Ok">好的</a>
		<div class="giftbag">
			<a href="javascript:;" class="runawayBuy"></a>
			<a href="javascript:;" class="afterBuy"></a>
		</div>
	</div>
</div>
<div class="hints">提现失败请联系客服解决：400-641-5080</div>

<div class="advertisement">
	<a href="javascript:;" class="huiyuanBag" onclick="tourl(646)">
		<img src="../../../static/welfare/img/rukou.png" class="bagImg">
	</a>
	<a href="javascript:;" class="advTwo">
		<img src="../../../static/welfare/img/advImg.png" class="advImg">
			<p class="advP">支付由联动优势提供支持</p>
	</a>
</div>

<script type="text/javascript" src="/static/m/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="/static/m/js/withdrawCash.js"></script>
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
<script>
	window.onload=window.onresize=function(){
		document.documentElement.style.fontSize=window.innerWidth/16+'px';
	};
    document.getElementsByTagName('body')[0].style.height = window.innerHeight+'px';  
</script>
</body>
</html>