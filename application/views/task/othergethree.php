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
    <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
<style type="text/css">
  /*2016-01-06改*/
.btnBox{text-align: center; position: fixed; left:0; bottom:0; width: 100%; height:auto; background: #ff0c09; font-size: 0; border-top: 1px solid #fcad33;}
.btn,.btnBack{font-size:16px;  width:100%; height:40px; line-height: 40px;display: inline-block;  text-align: center;}
.btn{background: #fff; color:#58ab22;}
.btnBack{ background: #fcad33;  color:#fff; }
.btnBox a:link,.btnBox a:visited,.btnBox a:active{color:#fff;}
.shadow{width:100%; height:100%; position:fixed; left:0; top:0; background: rgba(0,0,0,0.7); display: none; z-index: 10;}
.sharefx{width:228px;height:131px; font-size: 0;}
.sharefx img{width:228px;height:121px;  display: block; position:absolute; right:20px; top:10px;}
.treefx{margin-top:84px; width:100%; text-align: center;}
.treefx img{width:203px;height:121px;  display: block; margin:0 auto;}
.hstfx{}
.hstfx img{ width:59px;height:28px;  display: block; position:absolute; right:30px; bottom:18px;}
/*2016-01-06改*/

/*2016-01-09 改*/
.shadow_two{display: none;}
.shadow_two{width:100%; height:100%; position:fixed; left:0; top:0; background: rgba(0,0,0,0.7); display: none;}
.BPbox{position: absolute;top:100px;}
.BPbox .btn{ background: #fff; padding:0.926rem 0;margin: 0;}
.bg_c{margin: 5px 28px;}
.register_text{font-size: 16px;padding: 15px 0 0 0;text-align: center;}
/*2016-01-09 改*/


</style>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="/static/home/js/jquery-1.9.1.min.js"></script>
<script>
var APP = '<?php echo site_url(); ?>';
<?php if (isset($wxid)&&$wxid!='') {; ?>
var wxid = <?php echo $wxid; ?>;
<?php }; ?>

var instruction = <?php echo $instruction; ?>

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
   
    //监听“分享到朋友圈”按钮点击、自定义分享内容及分享结果接口
    wx.onMenuShareTimeline({
      title: '<?php if(isset($tasksharetitle_pe)){ echo $tasksharetitle_pe; }else{echo $tasksharetitle;}?>',
      desc: '<?php echo $tasksharedes ?>',
      link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid;?>&redirect_uri=<?php echo $taskshareurl;?>&response_type=code&scope=snsapi_base#wechat_redirect',
      imgUrl:"<?php echo $taskshareimg ?>" ,
      trigger: function (res) {     
           //点击分享是查询粉丝的分享记录，一周内只能领取一次       
      },
      success: function (res) {
          if (typeof(wxid)!="undefined") {
            $.post(APP+'/task/finishtask/finishshare', {wxid: wxid}, function(status) {
              if(status==2||status==0){
                alert("成功宣传环保到朋友圈。");
              }else{
                alert('宣传环保成功，并且有福利可以领取。');
              };
              window.location.href = APP+'task/usercenter/taskcenter';
            },'json');
          }else{
            alert("成功宣传环保到朋友圈。");
          };
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
        title: '<?php if(isset($tasksharetitle_pe)){ echo $tasksharetitle_pe; }else{echo $tasksharetitle;}?>',
        desc: '<?php if(isset($tasksharedes_pe)){echo $tasksharedes_pe;}else{echo $tasksharedes;}  ?>',
        link: 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid;?>&redirect_uri=<?php echo $taskshareurl;?>&response_type=code&scope=snsapi_base#wechat_redirect', // 分享链接
        imgUrl: '<?php echo $taskshareimg ?>', // 分享图标
        success: function () {
          if (typeof(wxid)!="undefined") {
            $.post(APP+'/task/finishtask/finishshare', {wxid: wxid}, function(status) {
              if(status==2||status==0){
                alert("成功宣传环保到朋友。");
              }else{
                alert('宣传环保成功，并且有福利可以领取。');
              };
              window.location.href = APP+'task/usercenter/taskcenter';
            },'json'); 
          }else{
            alert("成功宣传环保到朋友。");
          };           
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
<script type="text/javascript">
$(function(){
  var htmlDecode = function(str) {
      return str.replace(/&#(x)?([^&]{1,5});?/g,function($,$1,$2) {
          return String.fromCharCode(parseInt($2 , $1 ? 16:10));
      });
  };
  $('.instruction_content').html(htmlDecode("<?php echo $taskIntroduction['data']['instruction_content']; ?>"));
})
</script>
</head> 
<body>
	<? echo instruction_content;?>
    <div style="margin:0px 0px 10px 0px; font-size:12px;padding-bottom: 30px;" class = "instruction_content">
        
    </div>

    <!--这是弹出页面 开始-->
    <div class="shadow">
        <div class="sharefx"><img src="/static/task/images/sharefx.png" alt=""/></div>
        <div class="treefx"><img src="/static/task/images/treefx.png" alt=""/></div>
        <div class="hstfx"><img src="/static/task/images/hstfx.png" alt=""/></div>
    </div>
    <div class="fuzhi" style="position:fixed;top:0;left:0;background:rgba(0,0,0,0.7);width:100%;height:100%;display:none;">
        <div style="text-align:center;background:#ffffff;margin:150px 0px 0px 0px;padding-top:10px;">长按复制下面链接分享到微信好友</div>
        <div style="text-align:center;background:#ffffff;margin:0px 0px 0px 0px;padding-top:10px;"><?php echo urldecode($taskshareurl);?></div>
    </div>    
    <!--这是弹出页面 结束-->
<!--底部固定  按钮 开始-->
      <div class="btnBox">
          <?php if (isset($next)&&$next==1) { ?>
            <a class="btn btnBack" href="/index.php/task/usercenter/taskcenter">做任务领现金</a>
          <?php }else{ ?>
          <a class="btn btn_get btnBack">
          <?php if ($instruction>=13) {
            echo '宣传公益环保';
          }elseif($instruction=10){
            echo '邀请';
          }else{
            echo '分享';
            } ?>
          </a>
          <?php } ?>
          
      </div>
<!--底部固定  按钮 结束-->
    
<script type="text/javascript">

// 2016-01-06
var oBtn = $(".btn_get");
var oShadow = $(".shadow");
var ua = window.navigator.userAgent.toLowerCase();
var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
oBtn.on("click",function(){
    if (iswx==1) {
        oShadow.show();
    }else{
        $(".fuzhi").show();
    }
})
oShadow.on("click",function(){
     $(this).hide();
})
$(".fuzhi").on("click",function(){
     $(this).hide();
})
// 2016-01-06

</script>
<script>
  var _hmt = _hmt || [];
  (function() {
    var hm = document.createElement("script");
    hm.src = "//hm.baidu.com/hm.js?a337a5249adb71bc3f563821242e0c34";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
  })();
  (function (doc, win) {
		var docEl = doc.documentElement,  
		resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',  
		recalc = function () {  
			var clientWidth = docEl.clientWidth; 
			if (!clientWidth) return;  
			docEl.style.fontSize = 20 * (clientWidth / 320) + 'px';  
		}; 
		if (!doc.addEventListener) return;  
		win.addEventListener(resizeEvt, recalc, false);  
		doc.addEventListener('DOMContentLoaded', recalc, false);  
	})(document, window);
</script>

</body>
</html>
