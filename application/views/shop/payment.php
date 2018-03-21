<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>确认订单</title>
    <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
    <link rel="stylesheet" type="text/css" href="/static/shop/css/cssReset.css"/>
    <link rel="stylesheet" type="text/css" href="/static/shop/css/payment.css"/>
</head>
<body>
<div class="satnav">
    <a class="back" href="javascript:history.go(-1);">返回</a>
    <span>确认订单</span>
</div>
<div class="full-top"></div>

<div class="content">
    <div class="dimension">
        <p class="brief">
            <span>商品名称：</span>
            <span class="name"><?php echo $goods_name; ?></span>
        </p>
        <p class="brief">
            <span>订单编号：</span>
            <span class="name ordernumber"><?php echo $ordernum; ?></span>
        </p>
        <?php if (isset($adressinfo)) { ?>
        <p class="brief">
            <span>收货地址：</span>
            <span class="name"><?php echo($adressinfo['2']) ?></span>
        </p>
        <p class="brief">
            <span>收&nbsp货&nbsp人：</span>
            <span class="name"><?php echo($adressinfo['0']) ?></span>&nbsp
            <span class="name"><?php echo($adressinfo['1']) ?></span>
        </p>
        <?php } ?>
        <p class="brief">
            <span>商品金额：</span>
            <span class="price"><?php if ($pri>0&&$integral>0) {echo '￥'.($pri/100).'+'.$integral.'通花';}elseif($pri>0){echo '￥'.$pri/100;}else{echo $integral.'通花';} ?></span>
        </p>
    </div>
</div>
<div class="defray">
<?php if(isset($code_img)){ ?>
    <div class="tips">
        扫码付款
        <div class="line-left"></div>
        <div class="line-right"></div>
    </div>
    <div class="code" align="center">
        <div class="print">
            <img src="<?php echo '/'.$code_img; ?>"/>
        </div>
        <p class="brief">
            <span class="name"></span>
        </p>
    </div>
<?php } ?>
</div>
<?php if (isset($config)) { ?>
    <div class="isAdd"><a class="greenBtn" onclick="javascript:sureorder()">确认订单</a></div>
<?php }else{ ?>
    <div class="isAdd" style="margin-top:20px"><a class="greenBtn" onclick="sureoder()">确认已支付</a></div>
<?php } ?>
</body>
</html>
<script type="text/javascript" src="/static/shop/js/jquery-1.11.1.min.js"></script>
<script src="/static/home/js/request_common.js"></script>
<script src="/static/shop/ajax/request_flow.js?v=1001"></script>
<?php if (isset($config)) { ?>
    <?php echo $config; ?>
<?php } ?>
<script type="text/javascript">
    var ua = window.navigator.userAgent.toLowerCase();
    var check = setInterval('checkorder()',5000);
    var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
    if (iswx==1) {
        $('.defray .brief .name').html('长按识别二维码支付');
    }else{
        $('.defray .brief .name').html('下载二维码进行支付');
    }
</script>