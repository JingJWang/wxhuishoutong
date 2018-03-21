<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>回收通</title>
    <script src="../../../static/user/js/f_js.js"></script>
    <link rel="stylesheet" href="../../../static/user/css/f_style.css"/>
    <link rel="stylesheet" href="../../../static/user/css/fn_wopingjia.css"/>
</head>
<body class="SayBody">
<div class="SayBox">
    <header class="XDheader">
        <div class="main tc oh">
            <a href="javascript:history.go(-1)" class="fl aBack" ></a>
            <span class="fcou" >我的评价</span>
            <a href="/index.php/nonstandard/system/welcome" class="fr">主页</a>
        </div>
    </header>
    <div class="main tabnav">
        <span class="fxActive" onclick="GetMyEvaluation('g');">收到的评价</span>
        <span onclick="GetMyEvaluation('s');">发出的评价</span>
    </div>
    <div class="tab tabShou" id="list_g"></div>
    <div class="tab" id="list_s"></div>
</div>
<script type="text/javascript"  src="../../../static/home/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript"  src="../../../static/home/js/ajax_common.js"></script>
<script type="text/javascript"  src="../../../static/user/ajax/evaluation.js"></script>
<script>
    var aSpan = $(".tabnav span");
    var aTab = $(".tab");
    aSpan.on("click",function(){
        $(this).addClass("fxActive").siblings().removeClass("fxActive");
        var i = $(this).index();
        aTab.eq(i).show().siblings(".tab").hide();
    })
</script>
</body>
</html>