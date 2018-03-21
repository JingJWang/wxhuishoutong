<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title><?php echo $taskIntroduction['data']['instruction_name']; ?></title>
     <script src="/static/home/js/jquery-1.9.1.min.js"></script>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
     <script type="text/javascript">
     $(function(){
        var htmlDecode = function(str) {
        return str.replace(/&#(x)?([^&]{1,5});?/g,function($,$1,$2) {
        return String.fromCharCode(parseInt($2 , $1 ? 16:10));
         });
        };
     $('.service_both').html(htmlDecode("<?php echo $taskIntroduction['data']['instruction_content']; ?>"));
     })
</script>
</head>

<body>


	<!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <a href="javascript:history.back()" class="iall_left"></a> 
         	     <a class="headdis_cen"><?php echo $taskIntroduction['data']['instruction_name']; ?></a>
         		 
         </div>
    	
    
    </div>
    <!--head end-->
    
     
     
      <div class="service_both">
    	 <?php echo $taskIntroduction['data']['instruction_content']; ?>
    </div>
    
    <?php if ($taskIntroduction['data']['id']==6) { ?>
    <div class="sercive_bot" style=" margin-top:40px;">
         <a href="javascript:history.back()" class="ser_back">上一步</a>
         <a href="<?php echo site_url('task/userprofile/addgoodpreinfo'); ?>" class="ser_agree">同意</a>
    </div>    
    <?php } ?>
    

    
</body>
</html>
