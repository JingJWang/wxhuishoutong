<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 	 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>任务详情</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
 </head>

<body>
	<!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">任务详情</a>
         		 <!-- <a href="#" class="head_bright share_topimg"></a> -->
         </div>
    	
    
    </div>
    <!--head end-->
    
     <!--content start-->
     <div class="both">
           
           
          <!-- <p class="receive_txt bor_bot1">森林是地球的肥，我们要保护森林，共有8463人参与了活动，你也来吧</p> -->

          <?php if (isset($error)) { ?>
            <div class="smiletxt pb30"><?php echo $this->lang->line($error); ?></div>
          <?php }else{ ?>
          <div class="smile_nav"></div>
          
          <div class="smiletxt pb30"><?php echo $getonetask['task_content']; ?></div>
          <?php if ($getonetask['task_id'] == 5) { ?>
            <div class="smiletxt pb30"><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid;?>&redirect_uri=<?php echo $open_share_url;?>&response_type=code&scope=snsapi_base#wechat_redirect">点击链接开始游戏</a></div>
          <?php }elseif(isset($open_share_url)!='') { ?>
            <div class="smiletxt pb30"><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo $appid;?>&redirect_uri=<?php echo $open_share_url;?>&response_type=code&scope=snsapi_base#wechat_redirect">在此页面分享</a></div>
          <?php }elseif (!empty($getonetask['task_url'])) { ?>
          	<div class="smiletxt pb30"><a href="<?php echo $getonetask['task_url'] ?>">点击此链接领福利</a></div>
          <?php } ?>
        	<?php } ?>
          <?php if (isset($getonetask['task_type']) && $getonetask['task_type']==1) { ?>
            <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="all_go bg_c"  id="one1">福利中心签到</a>
          <?php }else{ ?>
            <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="all_go bg_c"  id="one1">返回福利站</a>
          <?php } ?>
     </div>
     <!--content end-->
</body>
</html>
