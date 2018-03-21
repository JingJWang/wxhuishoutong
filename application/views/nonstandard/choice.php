<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>开始交易</title>
    <script type="text/javascript" src="../../../static/home/js/f_js.js"></script>
    <script type="text/javascript" src="../../../static/home/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
    <link rel="stylesheet" href="../../../static/home/css/f_style.css"/>
    <link rel="stylesheet" href="../../../static/home/css/biaodan.css"/>
</head>
<body>
<div class="Bbox">
    <div class="wait bb">等待商家确认</div>
    <div class="Bcontent">
        <div class="bb">
            <span class="name">联系电话</span>
            <span class="f38"><?php  echo $coop['0']['cooperator_mobile']; ?></span>
            <button><a href="<?php  echo 'tel:'.$coop['0']['cooperator_mobile']; ?>">致电回收商</a></button>
        </div>
        <div class="bb">
            <span class="name">微&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp信</span>
            <span class="f38"><?php echo $coop['0']['cooperator_wx'];?></span>
        </div>
        <div>
            <span class="name">常用地址</span>
            <span class="f38"><?php  echo $coop['0']['cooperator_address']; ?></span>
        </div>
    </div>
    <div class="flower">
       
    </div>
    <div class="Bbot">
        <span onclick="OrderCancel('<?php echo $oid; ?>');">取消交易</span>
        <span onclick="ViewOrderDeal('<?php echo $oid; ?>');" class="active">交易完成</span>
    </div>
</div>
</body>
</html>