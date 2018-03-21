<!DOCTYPE html>
<html>
<head>
<title>回收通</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta name="format-detection" content="telephone=no"/>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<link type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>money.css">
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('JSPATH'); ?>jquery-1.7.2.min.js"></script>
<script>
var APP = '<?php echo site_url(); ?>';
var wxid = <?php echo $wxid; ?>;
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
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
  wx.ready(function () {
   // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
   
    //监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareTimeline({
      title: '任务分享',
      link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid;?>&redirect_uri=<?php echo $taskshareurl;?>&response_type=code&scope=snsapi_base#wechat_redirect',
      imgUrl:"<?php echo $this->config->item('webhost');?>/weixin/public/img/gz.png" ,
      trigger: function (res) {     
           //点击分享是查询粉丝的分享记录，一周内只能领取一次       
      },
      success: function (res) {
            $.post(APP+'/task/finishtask/finishshare', {wxid: wxid}, function(status) {
              if (!status) {
                alert("成功分享到朋友圈");
              }else{
                alert('分享成功，并且有任务已经完成。');
              };
            },'json');
           //分享成功后提示粉丝
            WeixinJSBridge.call('closeWindow');
      },
      cancel: function (res) {
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });
    wx.onMenuShareAppMessage({
        title: '任务分享', // 分享标题
        link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid;?>&redirect_uri=<?php echo $taskshareurl;?>&response_type=code&scope=snsapi_base#wechat_redirect', // 分享链接
        imgUrl: '<?php echo $this->config->item('webhost');?>/weixin/public/img/gz.png', // 分享图标
        success: function () {
            $.post(APP+'/task/finishtask/finishshare', {wxid: wxid}, function(status) {
              if (!status) {
                alert("成功分享到朋友圈");
              }else{
                alert('分享成功，并且有任务已经完成。');
              };
            },'json'); 
            // 用户确认分享后执行的回调函数
          //分享成功后提示粉丝
            alert('成功分享到朋友');
            WeixinJSBridge.call('closeWindow');
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
</head> 
<body>
     <p>分享界面，完成任务</p>
</body>
</html>
