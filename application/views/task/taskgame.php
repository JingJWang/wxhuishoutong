<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>冲出雾霾</title>
    <meta name="viewport" content="width=device-width,initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="full-screen" content="true"/>
    <meta name="screen-orientation" content="portrait"/>
    <meta name="x5-fullscreen" content="true"/>
    <meta name="360-fullscreen" content="true"/>
    <style>
        html, body {
            -ms-touch-action: none;
            background: #888888;
            padding: 0;
            border: 0;
            margin: 0;
            height: 100%;
        }
    </style>

    <!--这个标签为通过egret提供的第三方库的方式生成的 javascript 文件。删除 modules_files 标签后，库文件加载列表将不会变化，请谨慎操作！-->
    <!--modules_files_start-->
	<script egret="lib" src="/static/task/task_game/libs/modules/egret/egret.min.js"></script>
	<script egret="lib" src="/static/task/task_game/libs/modules/egret/egret.web.min.js"></script>
	<script egret="lib" src="/static/task/task_game/libs/modules/game/game.min.js"></script>
	<script egret="lib" src="/static/task/task_game/libs/modules/game/game.web.min.js"></script>
	<script egret="lib" src="/static/task/task_game/libs/modules/res/res.min.js"></script>
	<script egret="lib" src="/static/task/task_game/libs/modules/gui/gui.min.js"></script>
	<script egret="lib" src="/static/task/task_game/libs/modules/tween/tween.min.js"></script>

	<!--modules_files_end-->

    <!--这个标签为不通过egret提供的第三方库的方式使用的 javascript 文件，请将这些文件放在libs下，但不要放在modules下面。-->
    <!--other_libs_files_start-->
    <!--other_libs_files_end-->

    <!--这个标签会被替换为项目中所有的 javascript 文件。删除 game_files 标签后，项目文件加载列表将不会变化，请谨慎操作！-->
    <!--game_files_start-->
	<script src="/static/task/task_game/main.min.js?v=10000"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">

    wx_appid = '<?php echo $signPackage["appId"];?>';
    wx_shareurl = '<?php echo $taskshareurl; ?>';

    wx.config({
    debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [ // 所有要调用的 API 都要加到这个列表中       
        'onMenuShareTimeline',
        'onMenuShareAppMessage'   
    ]
  });

wx.ready(function(){

    //监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareTimeline({
        title: '<?php echo $getitle ?>', // 分享标题
        desc: '', // 分享描述
        link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+wx_appid+'&redirect_uri='+wx_shareurl+'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect',
        imgUrl: 'http://wx.recytl.com/static/task/images/youxi.jpg', // 分享图标
        success: function () { 
            alert("成功分享到朋友圈");
            // WeixinJSBridge.call('closeWindow');
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });
    //监听“分享到朋友”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareAppMessage({
        title: '冲出雾霾得奖励', // 分享标题
        desc: '<?php echo $getitle ?>', // 分享描述
        link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+wx_appid+'&redirect_uri='+wx_shareurl+'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', // 分享链接
        imgUrl: 'http://wx.recytl.com/static/task/images/youxi.jpg', // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
          //分享成功后提示粉丝
            alert('成功分享到朋友');
            // WeixinJSBridge.call('closeWindow');
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });

});
wx.error(function(res){
    //config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
}); 
</script>
	<!--game_files_end-->
</head>
<body>

    <div style="margin: auto;width: 100%;height: 100%;background-color: #000000;" class="egret-player"
         data-entry-class="Main"
         data-orientation="auto"
         data-scale-mode="noBorder"
         data-frame-rate="30"
         data-content-width="640"
         data-content-height="960"
         data-show-paint-rect="false"
         data-multi-fingered="2"
         data-show-fps="false" data-show-log="false"
         data-log-filter="" data-show-fps-style="x:0,y:0,size:30,textColor:0x00c200,bgAlpha:0.9">
    </div>
    <script>
        //游戏配置
        var gameConfig = {
            "openId":"<?php echo $userinfo['wx_openid']; ?>",
            "userId":"<?php echo $userinfo['wx_id']; ?>",
            "nickName":"<?php echo $userinfo['wx_name'] ?>",
            "iconImg":"<?php echo $userinfo['wx_img']; ?>",
            "startDialog":"游戏结束别忘了领取 环保基金 哦！"
        };
        //领取基金
        function leadFunds(gotodataStr){
            gotodataStr = escape(gotodataStr);
            
            window.location.href = "http://wx.recytl.com/index.php/task/taskshare/get_score/"+gotodataStr;
        }
        function updateShareTitle(title){
            //监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
            wx.onMenuShareTimeline({
                title: title, // 分享标题
                desc: '', // 分享描述
                link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+wx_appid+'&redirect_uri='+wx_shareurl+'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect',
                imgUrl: 'http://wx.recytl.com/static/task/images/youxi.jpg', // 分享图标
                success: function () { 
                    alert("成功分享到朋友圈");
                    // WeixinJSBridge.call('closeWindow');
                },
                cancel: function () { 
                    // 用户取消分享后执行的回调函数
                }
            });
            //监听“分享到朋友”按钮点击、自定义分享内容及分享结果接口
            wx.onMenuShareAppMessage({
                title: '冲出雾霾得奖励', // 分享标题
                desc: title, // 分享描述
                link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='+wx_appid+'&redirect_uri='+wx_shareurl+'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect', // 分享链接
                imgUrl: 'http://wx.recytl.com/static/task/images/youxi.jpg', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                  //分享成功后提示粉丝
                    alert('成功分享到朋友');
                    // WeixinJSBridge.call('closeWindow');
                },
                cancel: function () { 
                    // 用户取消分享后执行的回调函数
                }
            });
        }
        egret.runEgret();
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
