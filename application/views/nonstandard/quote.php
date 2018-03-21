<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通报价列表</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetails.css"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
	</head>
	<body style="padding: 106px 0 0px 0;background-color:#ffffff;">
		<div class="bidListgrag"></div>
		<!--  green start  -->
		<div class="green">
			<div class="back">
				<p>报价列表</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end  -->
		<!--  tip start  -->
		<div class="tip tip02">
			<p>成交价50元以上包邮。钱先到账、快递回收覆盖全国</p>
		</div>
		<!--  tip start  -->
		<!--  bidListTop start  -->
		<div class="bidListTop sort">
			<i class="leftLine"></i>
			<i class="centerLine"></i>
			<i class="rightLine"></i>
			<ul class="clearfix">
			     <li onclick="sorted(this),Getoption('price');"><input type="hidden" id="price" value="0"><span>价格</span></li>
				<li onclick="sorted(this),Getoption('distance');"><input type="hidden" id="distance" value="0"><span>距离</span></li>
				<li onclick="sorted(this),Getoption('evaluation');"><input type="hidden" id="evaluation" value="0"><span>评价</span></li>
				<li onclick="sorted(this),Getoption('transaction');"><input type="hidden" id="transaction" value="0"><span>成交单数</span></li>
			</ul>
		</div>
		<div class="bidListTop topBorder">
			<ul class="clearfix">
				<li onclick="selected(this),Option('s');">上门回收</li>
				<li onclick="selected(this),Option('o');">快递回收</li>
				<li onclick="selected(this),Option('d');"><input type="hidden" id="option" value="0">到我小区</li>
				<li>
					<div class="confirm">
						<p onclick="confirms(this),Auto();"><input type="hidden" id="auto" value="0">认证回收商</p>
					</div>
				</li>
			</ul>
		</div>		
		<div class="eachbidLine"></div>
		<!--  bidLine end  -->
		<!--  bidNone start  无报价列表  -->
		<?php if($type == 10){ ?>
		 <!--  bidListTop end  -->
		    <div id="batch">
		    <div class="content">
		    <div class="print fl"></div>
		    <p class="hint">您的批量回收订单已提交成功<br />稍后会有工作人员联系您...</p>
		    </div>
		    <div class="handle">
		    <a class="return" href="/index.php/nonstandard/system/welcome">返回首页</a>
		    </div>
		    </div>
		<!--  bidLine start  有报价列表  -->
		<?php }else{ ?>		   
		<div class="bidNone fl" style="display: none;">
		<p class="planeBg">我们正在把您的订单推送给回收商!</br>如有大量手机要卖,请联系客服!</p>
		<div class="tip01">
		<p><span class=nums>1</span><span class="tm">1&nbsp分钟</span>左右会收到手机报价，报价会以短信和微信通知您，请注意查收</p>
		<p><span class=nums>2</span>报价后24小时内，如未选定任何回收商，订单将自动返回待发送状态</p>
		<p><span class=nums>3</span>选定回收商后，回收商会打款到您的回收通账户，再寄手机</p>
		</div>
		<a href="javascript:;" onclick="RefreshQuote();" class="refresh">刷新</a>
		</div>
		<?php } ?>
		<!--  bidNone end  -->
    <script src="../../../static/home/js/ajax_common.js"></script>
    <script src="../../../static/home/js/ajax_quote.js"></script>
	</body>
</html>
