<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>好人好报</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
    
</head>

<body class="bg_color">

	<!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">好人好报</a>
         		 
         </div>
    	
    
    </div>
    <!--head end-->



 	<div class="both">
           <a href="openfile.html" class="openimg"><img src="/static/task/images/a.png" alt=""/></a>
           
           <div class="needhelp_nav"></div>
    </div>
    
    
    <div class=" bg_colorw mt20 pos_re" style=" height:40px;">
    		<div class="goo2_one">通花：<?php echo $userinfo['center_integral']; ?></div>
    		
    </div>
     <div class="both bg_colorw ">
    		 <p class="goo2_con">
                <?php echo $taskIntroduction['data']['instruction_content']; ?>
             </p>
    		
    </div>
   
        
     
     <a href="<?php echo site_url('task/userprofile/constract'); ?>" class="all_go bg_c">我需要帮助</a>
     
</body>
</html>
