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

          <?php if (isset($error) && $error != '') { ?>
            <div class="smiletxt pb30"><?php echo $error; ?></div>
          <?php }elseif(isset($not_full)){ ?>
            <div class="smiletxt pb30"><?php echo $not_full; ?></div>
          <?php }else{ ?>
            <div class="smile_nav"></div>
            <div class="smiletxt pb30"><?php echo $success ?></div>
          <?php } ?>
          
     </div>
     <!--content end-->
</body>
</html>
