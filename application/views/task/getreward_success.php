<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 	 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>奖励情况</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
     <script src="/static/task/js/jquery-1.9.1.min.js"></script>
     <script type="text/javascript">
        $(function(){
            var oShadowBox = $(".shadowBox");
            var oConBox = $(".conBox");
            var oChaBox = $(".chaBox");
            var aLabel = $(".radioBox  label");
            var mt = ($(window).height()-oConBox.height())/2;
            oConBox.css({marginTop:mt});
            aLabel.on("click",function(){
                $(this).find("p").addClass("active").siblings().find("p").removeClass("active");
                $(this).siblings().find("p").removeClass("active");
                $(this).siblings().find("#one").prop('checked',false);
                $(this).find('#one').prop('checked',true);
            })
            oChaBox.on("click",function(){
                oShadowBox.hide()
            })
        })
     </script>
     <style>
      input{border-width:0;border-color:white;border-style:none;background:none; border:none; outline:medium;alpha:0;appearance:none;-webkit-appearance:none;-webkit-tap-highlight-color: transparent;-webkit-user-modify:read-write-plaintext-only;}
      /*弹框*/
     .shadowBox{width:100%; height:100%; position: fixed; left:0; top:0; background: rgba(0,0,0,0.7); z-index: 10;}
     .conBox{width:92%; margin: 0 auto;background: #fff; font-size: 14px; border-radius: 4px; position: relative;}
     .chaBox{position:absolute; right:-6px; top:-6px; width:22px; height:22px;}
     .conBox h1{background: #fff; font-size: 16px;}
     .mode1{text-align: center; padding:20px 0;}
     .fbtn{text-align: center; height:44px;}
     .fbtn input{width:90%; height:100%; background: #fff; color:#017aff; font-size: 16px;  }
     .radioBox input{display: none;}
     .btp{ border-top: 1px solid #dfdfdf;}
     .radioBox p{height:44px; line-height: 44px; background: #f8f8f8; padding-left:18px; border-bottom: 1px solid #dfdfdf;}
     .radioBox .active{ background: #f8f8f8 url("/static/task/images/chooseBg.png") no-repeat 90% center; background-size:24px 20px; }
     .imgLogo{width:30px;height:30px; padding-right:10px;}
     .fsBtn{ display:block; width:auto; height:40px; line-height:40px; overflow:hidden; text-align:center; font-size:18px; border-radius:2px;background:#fff; color:#58ab22; border: 1px solid #58ab22; margin:20px 10px 0 10px;}
     .bg_c{margin-top:6px;}
      .tongBg{width:90%; margin:0 auto; height:6px;}
      .smiletxt{padding:20px 0; }
      .smiletxt span{line-height: 26px;}
      .fTxt{width:90%; margin:0 auto; font-size: 14px; background: #f3f3f3; padding:10px 0; margin-top:20px;}
      .fTxt p{line-height:26px; color:#FFAA0A;}
     /*弹框结束*/
     </style>
 </head>

<body>

<!-- 升级提示框 start -->
<?php if (isset($userinfo['can_select'])) { ?>
<div class="shadowBox">
    <div class="conBox">
        <div class="chaBox"><img src="/static/task/images/chacha.png" alt=""/></div>
        <div class="mode1">
            <h1>您已经升级，请选择新的称号</h1>
            <p>（也可以去我的等级中去选择）</p>
        </div>
        <form action="<?php echo site_url('task/usercenter/select_level_title'); ?>" method="post">
        <div class="radioBox">
            <?php foreach ($userinfo['can_select'] as $k => $v) { ?>
                <label for="two"><p class="btp"><img class="imgLogo" src="<?php echo $v['level_img']; ?>" alt=""/><?php echo $v['level_name']; ?><input id="one" value="<?php echo $v['level_id']; ?>" name="select_title" type="radio"/><span></span></p></label>
            <?php } ?>
        </div>
        <p class="fbtn"><input type="submit" class="determine" value="确 定" /></p>
        </form>
    </div>
</div>
<?php } ?>
<!-- 升级提示框 end -->


    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">奖励情况</a>
         		 <!-- <a href="#" class="head_bright share_topimg"></a> -->
         </div>
    	
    
    </div>
  
     <div class="both">
           
           
          <!-- <p class="receive_txt bor_bot1">森林是地球的肥，我们要保护森林，共有8463人参与了活动，你也来吧</p> -->

          <?php if (isset($str['error']) && $str['error']!='') { ?>
            <div class="smiletxt pb30" style="font-size:14px;padding-bottom:0px;"><?php echo $str['error']; ?></div>
          <?php }else{ ?>
          <?php if (isset($result)) { ?>
            <div class="smiletxt pb30" style="font-size:14px;padding-bottom:0px;"><?php echo $this->lang->line($result); ?></div>
          <?php } ?>
          <div class="smiletxt pb30" style="font-size:14px;padding-bottom:0px;">奖金可点击福利站的【提现】按钮进入【个人中心】查看或提现哦~</div>
          <div class="smiletxt pb30" style="font-size:14px;padding-top:0px;"><?php
          if (((isset($str['add_all_intergral'])&&$str['add_all_intergral']>0) || (isset($str['add_fund'])&&$str['add_fund']>0) || (isset($str['add_bonus'])&&$str['add_bonus']>0) || (isset($str['add_integral'])&&$str['add_integral']>0))) {
              echo '您获得了：';
          }
          echo (isset($str['add_integral'])&&$str['add_integral']>0)? $str['add_integral'].'个通花 ':'';
          echo (isset($str['add_all_intergral'])&&$str['add_all_intergral']>0)? $str['add_all_intergral'].'个成长值 ':''; 
          echo (isset($str['add_bonus'])&&$str['add_bonus']>0)? $str['add_bonus'].'元奖金 ':''; 
          echo (isset($str['add_fund'])&&$str['add_fund']>0)? $str['add_fund'].'元基金':'';?>
          <?php } ?>
          <br><span style="color:#9554CE;">每天最多可领取8个红包，您也可以领取通花~</span>
          <br><span style="color:#FFAA0A;">后续会有神秘任务持续上线，请保持关注~</span>
          </div>

         <div class="fTxt">
             <p class="size12">【通花抵现】维达纸巾6包16元，还包邮！不买真亏</p>
         </div>


         <a href="/view/shop/list.html" class="fsBtn" >通花商城</a>
          <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="all_go bg_c"  id="one1">返回福利站</a>
          
     </div>

</body>
</html>
