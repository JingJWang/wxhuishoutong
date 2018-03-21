<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>勋章</title>
     <link href="/static/task/css/both.css?v=10000" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css?v=10000" type="text/css" rel="stylesheet"/>
     <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
     <style>
     input{border-width:0;border-color:white;border-style:none;background:none; border:none; outline:medium;alpha:0;appearance:none;-webkit-appearance:none;-webkit-tap-highlight-color: transparent;-webkit-user-modify:read-write-plaintext-only;}

     /* 2016-01-06 111*/
     .shadow{width:100%; height:100%; position:fixed; left:0; top:0; background: rgba(0,0,0,0.7); display: none;}
     .sharefx{width:228px;height:131px; font-size: 0;}
     .sharefx img{width:228px;height:121px;  display: block; position:absolute; right:25px; top:10px;}
     .treefx{margin-top:84px; width:100%; text-align: center;}
     .treefx img{width:203px;height:121px;  display: block; margin:0 auto;}
     .hstfx{}
     .hstfx img{ width:59px;height:28px;  display: block; position:absolute; right:30px; bottom:18px;}
     /* 2016-01-06 1111*/
     </style>
<script>
 
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
      title: '回收通-我的称号',
      link: '<?php echo $taskshareurl;?>',
      imgUrl:"<?php echo $this->config->item('webhost');?>static/task/images/t4.png" ,
      trigger: function (res) {     
           //点击分享是查询粉丝的分享记录，一周内只能领取一次       
      },
      success: function (res) {
            alert('成功分享到朋友');
            WeixinJSBridge.call('closeWindow');
      },
      cancel: function (res) {
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });
    wx.onMenuShareAppMessage({
        title: '回收通-我的称号', // 分享标题
        link: '<?php echo $taskshareurl;?>', // 分享链接
        imgUrl: '<?php echo $this->config->item('webhost');?>static/task/images/t4.png', // 分享图标
        success: function () {
            alert('成功分享到朋友');
            WeixinJSBridge.call('closeWindow');
        },
        cancel: function () { 
            // 用户取消分享后执行的回调函数
        }
    });
  });
    wx.error(function(res){

   });   
</script>
</head>

<body class="bg_color">

	<!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">我的称号</a>
         		 
         </div>
    	
    
    </div>
    <!--ggggggggggggggggggggggggg 222-->
<!-- <button class="btn">点我</button> -->

<!--这是弹出页面 开始-->
   <div class="shadow">
        <div class="sharefx"><img src="/static/task/images/sharefx.png" alt=""/></div>
        <div class="treefx"><img src="/static/task/images/treefx.png" alt=""/></div>
        <div class="hstfx"><img src="/static/task/images/hstfx.png" alt=""/></div>
   </div>
<!--这是弹出页面 结束-->






<!--ggggggggggggggggggggggggg 222222-->
    <div class="both">
    <?php if (isset($userinfo['can_select'])) { ?>
            <h3 class="rankwap bg_color"> 您已经升级 请选择新的称号</h3>
            <div class="mergist_one bor_bot1">
            
            <form action="<?php echo site_url('task/usercenter/select_level_title'); ?>" method="post">         
            <div class="diy_select">
                <input type="hidden" name="select_title" class="diy_select_input">
                <input value="" name="" class="diy_select_txt" placeholder="请选择等级称谓" readonly="readonly">
                <div class="diy_select_btn"></div>
               <ul class="diy_select_list">
               <?php foreach ($userinfo['can_select'] as $k => $v) { ?>
                  <li value='<?php echo $v['level_id']; ?>'><?php echo $v['level_name']; ?></li>
               <?php } ?>
                     
                 </ul>
              </div>
              <input type="submit" value="确定"  class="determine"/>
            </form>            
            </div>
          <?php } ?>
    </div>
    <!--head end-->
    
    <div class="themedal_one bg_colorw">
       <p class="fname">称号：<?php if ($num>0) { echo $levels['0']['level_name']; }else{echo '新手';} ?></p>
         <p  class="fnum">称号数量：<span class="colorlv"><?php echo $num; ?></span>个</p>

    </div>    
    
    <div class="themedal_two bg_colorw mt20">
      <?php if ($num>0) { ?>
        <div class="themedal_one bg_colorw">
          <div class="fshoubox">

          <?php foreach ($levels as $k => $v) { ?>
          <a href="<?php echo $v['level_share_url']; ?>">
            <div class="xunBox">   
                <div class="xunImg">
                    <span class="ji<?php echo $v['level_num']; ?>"><?php echo $v['level_num']; ?>级</span>
                    
                    <?php if (!empty($v['level_img'])) { ?>
                      <img src="<?php echo $v['level_img'] ?>" alt=""/>
                    <?php }else{ ?>
                      <img src="" alt=""/>
                    <?php } ?>
                    <p><?php echo $v['level_name']; ?></p>
                </div>
                <p class="xunName">需要成长值:<?php echo $v['level_integral']; ?></p>
            </div>
          </a>
          <?php } ?>
          </div>
        </div>
      <?php }else{ ?>
        <p class="themedal_one">您当前没有任何称号，快去领福利，还能升级！</p>
      <?php } ?>
    </div>
    
    <?php if ($num>0) { ?>
      <div class="botBox">
         <p class="botImg"><img src="/static/task/images/fnews.png" alt=""/></p>
         <p class="botTxt">您可以选择小动物了解它，分享到朋友圈<br />让大家保护它，还可以领福利</p>
      </div>
    <?php }else{ ?>
      <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="all_go bg_c"  id="one1">领取福利</a>
    <?php } ?>

     <script type="text/javascript" src="/static/task/js/meregist.js"></script>
     <!--gggggggggggggggggggggggggggggggggggggg  3333-->
      <script src="/static/home/js/jquery-1.9.1.min.js"></script>
      <script>
          var oBtn = $(".btn");
          var oShadow = $(".shadow");
          oBtn.on("click",function(){
              oShadow.show();
          })
          oShadow.on("click",function(){
              $(this).hide();
          })
      </script>
<!--gggggggggggggggggggggggggggggggggggggg  3333-->
</body>
</html>
