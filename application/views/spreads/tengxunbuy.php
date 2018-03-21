<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1" >
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <title></title>
    <link rel="stylesheet" type="text/css" href="/static/shop/css/cssReset.css" />
    <link rel="stylesheet" type="text/css" href="/static/shop/css/generalize.css" />
    <script charset="utf-8" type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
    <script type="text/javascript">
        var AappId = '';
        var Atimestamp = '';
        var AnonceStr = '';
        var Asignature = '';
        window.shareData = {
            "timgUrl": "",
            "timeLineLink": "",
            "tTitle": "",
            "tContent": ""
        };
        function sharetext(){
            wx.config({
                debug: false,
                appId: AappId,
                timestamp: Atimestamp,
                nonceStr: AnonceStr,
                signature: Asignature,
                jsApiList: [ // 所有要调用的 API 都要加到这个列表中       
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage'   
                ]
            });
            wx.ready(function () {
            //监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
                wx.onMenuShareTimeline({
                    title: window.shareData.tTitle,
                    desc: window.shareData.tContent,
                    link: window.shareData.timeLineLink,
                    imgUrl:window.shareData.timgUrl,
                    trigger: function (res) {     
                     //点击分享是查询粉丝的分享记录，一周内只能领取一次       
                    },
                    success: function (res) {
                        alert('分享成功');
                          //分享成功后提示粉丝
                          // WeixinJSBridge.call('closeWindow');
                    },
                    cancel: function (res) {
                    },
                    fail: function (res) {
                      alert(JSON.stringify(res));
                    }
                });
                wx.onMenuShareAppMessage({
                    title: window.shareData.tTitle,
                    desc: window.shareData.tContent,
                    link: window.shareData.timeLineLink,
                    imgUrl:window.shareData.timgUrl,
                    success: function () {
                        alert('分享成功');
                        // 用户确认分享后执行的回调函数
                      //分享成功后提示粉丝
                        // alert('成功分享到朋友');
                        // WeixinJSBridge.call('closeWindow');
                    },
                    cancel: function () { 
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
            wx.error(function(res){
            //config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新    签名。
            });
        }
    </script>
</head>
<body>
<div class="title">
    高价回收、寄售手机&nbsp|&nbsp奢侈品
    <a class="back" href="javascript:;"></a>
</div>
<a class="advert" href="javascript:;">
    <img src="/static/shop/images/adv.jpg"/>
</a>
<div class="mostly">
    <div class="deal">
        <span>已经成交了</span>
        <span class="sum">40937</span>
        <span>单</span>
    </div>
    <div class="handle">
        <a class="bought phone fl" href="/view/shop/list.html?type=5?from=<?php echo $from ?>">买手机</a>
        <a class="bought fr" href="/view/shop/list.html?type=10?from=<?php echo $from ?>">买奢侈品</a>
    </div>
</div>
<div class="detail">
    <div class="name">高价回收、寄售手机&nbsp|&nbsp奢侈品</div>
    <div class="shopList">
        <div class="dimension">
            <div class="phones">
                <?php foreach ($phone as $k => $v) { ?>
                    <a class="shopDetail" href="/view/shop/info.html?id=<?php echo $v['id'] ?>">
                    <div class="contain" align="center">
                        <div class="trade-name">
                            <span><?php echo $v['name'] ?></span>
                        </div>
                        <div class="print">
                            <div class="pic"><img src="<?php echo $v['img'] ?>"></div>
                        </div>
                        <div class="rates">
                            <div class="price fl"><span>原价</span><span>￥<?php echo $v['opri']/100 ?></span><div class="line"></div></div>
                            <div class="price fr"><span>现价</span><span>￥<?php echo $v['ppri']/100 ?></span></div>
                        </div>
                    </div>
                </a>
                <?php } ?>
            </div>
            <div class="luxury">
                <?php foreach ($luxury as $k => $v) { ?>
                    <a class="shopDetail" href="/view/shop/info.html?id=<?php echo $v['id'] ?>">
                    <div class="contain" align="center">
                        <div class="trade-name">
                            <span><?php echo $v['name'] ?></span>
                        </div>
                        <div class="print">
                            <div class="pic"><img src="<?php echo $v['img'] ?>"></div>
                        </div>
                        <div class="rates">
                            <div class="price fl"><span>原价</span><span>￥<?php echo $v['opri']/100 ?></span><div class="line"></div></div>
                            <div class="price fr"><span>现价</span><span>￥<?php echo $v['ppri']/100 ?></span></div>
                        </div>
                    </div>
                </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="depict">
    <p class="brief">以上均是回收或寄售的商品</p>
    <p class="brief">您也可以寄售您的手机或奢侈品，想卖多少钱您说了算</p>
</div>
<div class="further fl" align="center">
    <a class="more-btn" href="/view/shop/list.html">更多商品请访问商城</a>
</div>
<div class="footer fl" align="center">
    <div class="covers">
        <div class="profiles">
            <div class="code fl">
                <img src="/static/shop/images/code.jpg"/>
            </div>
            <div class="anenst">
                <div class="palette">
                    <a class="site phone fl" href="/index.php/nonstandard/system/welcome">手机版主页</a>
                    <a class="site fl" href="http://www.recytl.com/index.php/home/home/index/1">电脑版主页</a>
                </div>
                <div class="number">客服电话：400-641-5080</div>
            </div>
        </div>
        <div class="firm">
            <div class="names fl">北京知通科技有限公司</div>
            <a class="route fl" href="http://www.recytl.com">www.recytl.com</a>
        </div>
    </div>
</div>
<a class="recover" href="/index.php/shop/flowgood/spread/<?php echo $from; ?>"></a>
<div class="full-bottom"></div>
<div class="operate">
    <div class="master">
        <a class="bought phone fl" href="/view/shop/list.html?type=5?from=<?php echo $from ?>">买手机</a>
        <a class="bought fr" href="/view/shop/list.html?type=10?from=<?php echo $from ?>">买奢侈品</a>
    </div>
</div>

<script type="text/javascript" charset='utf-8' src="/static/shop/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" charset='utf-8' src="/static/shop/js/generalize.js"></script>

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