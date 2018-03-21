<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>公益档案</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
</head>

<body>
	<!--head start-->
        <div class="main head">
             <div class="head_nav pos_re">
                     <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
                     <a class="headdis_cen">公益档案</a>
                     
             </div>
            
        
        </div>
    <!--head end-->
    
    
    <!--content start-->
     <div class="both">
     	  <div class="public_one">
          	   <p class="pone_title">通花：<span class="colorlv"><?php echo $userinfo['center_integral']; ?></span></p>
          	   <a href="<?php echo site_url('task/userprofile/goodperson') ?>" class="public_onebot">点击进入好人好报活动</a>
          </div>
     
     		<div class="h25"></div>


            <?php foreach ($profiles as $k => $v) { ?>
            <!--循环此处 start-->
            <div class="public_nav ptb10 bor_bot1">
                    <div class="public_right"></div>
                   
                    <div class="public_left">
                            <div class="pl_top">
                                   <span class="public_title">公益方式</span>
                                   
                                   <span class="public_liebie"><?php echo $v['info_name']; ?></span>
                            </div>
                            
                            
                             <p class="public_cen">获得通花:<?php echo $v['get_integral']; ?></p>
                             
                             <p class="public_time"><?php echo date('Y-m-d',$v['task_finishtime']); ?></p>
                    
                    </div>
                
            </div>
            <!--循环此处 end-->
            <?php } ?>
            <!--循环此处 start-->
     		<!-- <div class="public_nav ptb10 bor_bot1">
            		<div class="public_right"></div>
                    
            		<div class="public_left">
                    		<div class="pl_top">
                                   <span class="public_title">公益方式</span>
                                   
                                   <span class="public_liebie">为公益分享朋友圈100次 </span>
                            </div>
                            
                            
                             <p class="public_cen">公 斤 数：10kg 获得树苗:100</p>
                             
                             <p class="public_time">2015-09-09</p>
                    
                    </div>
          
            </div> -->
            <!--循环此处 end-->
            
     </div>
    <!--content end-->
      
</body>
</html>
