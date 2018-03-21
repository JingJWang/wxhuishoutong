<html>
<head>
<title>订单修改</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link   rel="stylesheet" type="text/css" href="/static/weixin/public/css/order.css"  >
<script type="text/javascript" src="/static/weixin/public/js/jquery.1.4.2-min.js"></script>
<script type="text/javascript" src="/static/weixin/public/js/jquery.cityselect.js"></script>
<script type="text/javascript" src="/static/weixin/public/js/common.js"></script>
<script type="text/javascript" src="/static/weixin/public/js/edit_order.js?v=2012"></script>
</head>
<body>
<div id="subjects">
		<input type="hidden" id="openid" />
		<div class="form_ctrl page_head"><h2>订单修改</h2></div>
		<div class="form_ctrl page_text">
			<p>请对订单信息完善</p>
		</div>
		<div class="form_ctrl input_text">
			手机:<span id="h_tel"></span>
		</div>
		<div class="form_ctrl form_select">
			地区:<span id="city"></span>
		</div>
		<div class="form_ctrl input_text">
			详细地址:<span id="address"></span>
		</div>
		<div class="form_ctrl input_text">
			旧衣件数:<span id="num"></span>
		</div>
		<div class="form_ctrl input_text">
			报单日期:<span id="date"></span>
		</div>
		<div class="form_ctrl input_text">
			<label class="ctrl_title">重量(KG)<span class="req">*</span></label>
			<input type="text" name="order_weight" id="order_weight" value="" placeholder="请输入重量">
		</div>
		<div class="form_ctrl input_text">
			<label class="ctrl_title">价格<span class="req">*</span></label>
			<input type="text" name="h_price" id="h_price" value="" placeholder="请输入价格">
		</div>
		<div class="form_ctrl form_checkbox" id="voucher1">
			
		</div>
		<div class="form_ctrl form_checkbox" id="voucher2">
			
		</div>
		<div class="form_ctrl form_checkbox" id="voucher3">
			
		</div>		
		<div class="form_ctrl input_text">
			<label class="ctrl_title">备注</label>
			<input type="text" name="h_remark" id="h_remark" value="" placeholder="请输入备注">
		</div>		
		<p id="info" style="text-align:center;margin-top:10px;color:red;"></p>
		<div class="form_ctrl form_submit">			
			<input type="button" value="提交" onclick="update_order();">
		</div>
	
</div>
</body>
</html>
