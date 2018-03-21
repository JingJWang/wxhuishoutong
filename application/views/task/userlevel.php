<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 	 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>等级系统</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
     <style>
     input{border-width:0;border-color:white;border-style:none;background:none; border:none; outline:medium;alpha:0;appearance:none;-webkit-appearance:none;-webkit-tap-highlight-color: transparent;
	-webkit-user-modify:read-write-plaintext-only;}
     </style>
 </head>

<body>
	<!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">等级系统</a>
         		 
         </div>
    	
    
    </div>
    <!--head end-->
    
     <!--content start-->
     <div class="both">
     	  <div class="list_one bg_color">我的称号：<span class="font_color"><?php echo $userinfo['level_name']; ?></span><br>我的通花：<span class="font_color"><?php echo $userinfo['center_integral']; ?></span></div>
          

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

          <?php foreach ($levels as $k => $v) { ?>
              <!--循环 start-->
              <div class="rank_two bor_bot1 pos_re">
                    <div class="rank_left">
                        <span class="ltitle" style=" height:30px;"><?php echo $v['level_name']; ?></span>
                            <span class="lshum">需要成长值:<?php echo $v['level_integral']; ?></span>
                    
                    
                    </div>
                    <div class="ltwo_right"><img src="<?php if (empty($v['level_img'])) { echo '/static/task/images/listrbg.png'; }else{ echo $v['level_img']; } ?>"  alt="" /></div>
                   
              </div>
              <!--循环 end-->
            <?php } ?>
        
        <!--循环 start-->
      
                   
          <!-- <div class="rank_two bor_bot1 pos_re">
          		
          	    <div class="rank_left">
                		<span class="ltitle" style=" height:30px;">波波先先生波波先先生波波先先生</span>
                        <span class="lshum">需要树苗:500</span>
                
                
                </div>
                 <div class="ltwo_right"><img src="images/listrbg.png"  alt="" /></div>
               
          </div> -->
   		<!--循环 end-->
        
     </div>
     <!--content end-->
 <script type="text/javascript" src="/static/task/js/meregist.js"></script>
</body>
</html>
