<!DOCTYPE html>
<html>
<head>
<title>团收报名</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link   rel="stylesheet" type="text/css" href="<?php echo $this->config->item('CSSPATH'); ?>common.css">
<script src="<?php echo $this->config->item('JSPATH'); ?>cxselect/jquery.min.js"></script>
<script src="<?php echo $this->config->item('JSPATH'); ?>cxselect/jquery.cxselect.min.js"></script>
<script>
$(document).ready(function(){
	$.cxSelect.defaults.url = '/static/weixin/public/js/cxselect/cityData.min.json';
	$('#city_china').cxSelect({
		selects: ['province', 'city', 'area']
	});
})
</script>
</head>
<body>
<div class="top t_c">
回收通
</div>
<div class="wrap" style="padding-top:20px;">
    <p class="size16 red">
    * 每次活动，每人最多可卖30kg</br>* 卖产品时带报名手机去现场，可拿补贴
    </p>
    <form action="/index.php/weixin/order/addorder" method="post" onsubmit="submit.disabled=1">
        <!--openid-->
        <input type="hidden" name="openid"  id="openid" value="<?php  echo $weixin_id; ?>">
        <p class="tab">
        <span class="tit">手机号&nbsp;<span class="red">*</span></span>
        <input class="kong"  name="phone" id="h_tel"  type="text" value="<?php  echo $order_mobile; ?>"/>
        </p>
        <div class="tab">
        <span class="tit">地址&nbsp;<span class="red">*</span></span>
        <div id="city_china">
        <?php if($order_province == ''){?>
        <select class="province" disabled="disabled" name="province"></select></p>
        <select class="city" disabled="disabled" name="city"></select></p>
        <select class="area" disabled="disabled" name="area"></select></p>
        <?php }else{?>
        <select class="province" name="province" data-value="<?php echo $order_province; ?>" data-first-title="选择省" disabled="disabled"></select>
        <select class="city"     name="city"   data-value="<?php echo $order_city; ?>" data-first-title="选择市" disabled="disabled"></select>
        <select class="area"     name="county" data-value="<?php echo $order_county; ?>" data-first-title="选择地区" disabled="disabled"></select>
        <?php } ?>
        </div>
    <p class="tab">
        <span class="tit">小区名称&nbsp;<span class="red">*</span></span>
        <input class="kong"  name="address" id="h_addinfo" type="text" value="<?php echo $order_address;?>"/>
    </p>
    <div class="tab">
        <span class="tit">产品类型&nbsp;<span class="red">*</span></span>
        <select class="selectbg" id="order_type" name="order_type">
            <option value="1">请选择类型</option>
            <?php 
                foreach ($standard_product as $key=>$product){
                    echo '<option value="'.$key.'">'.$product.'</option>';
                } 
            ?>           
        </select>
    <div class="tab">
        <span class="tit">产品数量&nbsp;<span class="red">*</span></span>
        <select class="selectbg" id="number" name="order_num">
            <option value="0">请选择数量</option>
            <option value="1">10以下</option>
            <option value="2">10-40</option>
            <option value="3">40-80</option>
            <option value="4">80以上</option>
    </select>
    </div>
        <p id="message" style="text-align: center;color:red;margin-top:10px;"></p>
        <p class="t_c">
        <input class="guanzhu true_btn" value="" type="submit" "/>
        </p>
    </form>
    </div>
</div>
</body>
</html>
