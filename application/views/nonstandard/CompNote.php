<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>我的评价</title>
    <script src="../../../static/user/js/f_js.js"></script>
    <link rel="stylesheet" href="../../../static/user/css/f_style.css"/>
    <link rel="stylesheet" href="../../../static/user/css/fn_wopingjia.css"/>
</head>
<body class="SayBody">
<div class="SayBox">
    <header class="XDheader">
        <div class="main tc oh">
            <a href="javascript:history.go(-1)" class="fl aBack"></a>
            <span class="fcou">我的评价</span>
            <a href="javascript:;" class="fr">主页</a>
        </div>
    </header>
    <div class="main tabnav">
        <span class="fxActive">收到的评价</span>
        <span>发出的评价</span>
    </div>
    <div class="tab tabShou">
        <div class="renSay oh">
            <div class="sayImg fl">
                <img src="images/say1.png" alt=""/>
            </div>
            <div class="txtBox fl">
                <p class="sayNT oh">
                    <span class="saYName fl">SSSSSSS</span>
                    <span class="saYTime fr">2015-10-08</span>
                </p>
                <p class="sayTxt">这款App太方便了，把我那些物品都处理了，再也不用烦恼了，一下好轻松。</p>
            </div>
        </div>
        <div class="renSay oh">
            <div class="sayImg fl">
                <img src="images/say1.png" alt=""/>
            </div>
            <div class="txtBox fl">
                <p class="sayNT oh">
                    <span class="saYName fl">波波先生</span>
                    <span class="saYTime fr">2015-10-08</span>
                </p>
                <p class="sayTxt">这款App太方便了，把我那些物品都处理了，再也不用烦恼了，一下好轻松。</p>
            </div>
        </div>
        <div class="renSay oh">
            <div class="sayImg fl">
                <img src="images/say1.png" alt=""/>
            </div>
            <div class="txtBox fl">
                <p class="sayNT oh">
                    <span class="saYName fl">波波先生</span>
                    <span class="saYTime fr">2015-10-08</span>
                </p>
                <p class="sayTxt">这款App太方便了，把我那些物品都处理了，再也不用烦恼了，一下好轻松。</p>
            </div>
        </div>
    </div>
    <div class="tab">
        <div class="renSay oh">
            <div class="sayImg fl">
                <img src="images/say1.png" alt=""/>
            </div>
            <div class="txtBox fl">
                <p class="sayNT oh">
                    <span class="saYName fl">波波先生</span>
                    <span class="saYTime fr">2015-10-08</span>
                </p>
                <p class="sayTxt">这款App太方便了，把我那些物品都处理了，再也不用烦恼了，一下好轻松。</p>
            </div>
        </div>
        <div class="renSay oh">
            <div class="sayImg fl">
                <img src="images/say1.png" alt=""/>
            </div>
            <div class="txtBox fl">
                <p class="sayNT oh">
                    <span class="saYName fl">波波先生</span>
                    <span class="saYTime fr">2015-10-08</span>
                </p>
                <p class="sayTxt">这款App太方便了，把我那些物品都处理了，再也不用烦恼了，一下好轻松。</p>
            </div>
        </div>
        <div class="renSay oh">
            <div class="sayImg fl">
                <img src="images/say1.png" alt=""/>
            </div>
            <div class="txtBox fl">
                <p class="sayNT oh">
                    <span class="saYName fl">波波先生</span>
                    <span class="saYTime fr">2015-10-08</span>
                </p>
                <p class="sayTxt">这款App太方便了，把我那些物品都处理了，再也不用烦恼了，一下好轻松。</p>
            </div>
        </div>
    </div>
</div>
<script src="../../../static/home/js/jquery-1.9.1.min.js"></script>
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