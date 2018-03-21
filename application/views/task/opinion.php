<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>任务详情</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
 </head>

<body class="bg_color">
	 <!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">任务详情</a>
         		 
         </div>
    	
    
    </div>
    <!--head end-->
    
     <!--content start-->
     <div class="both">
     	  <div class="task_one bor_bot1">提意见</div>
          
        <div class="task_two bg_colorw bor_bot1">
        	   <div class="all_nav">任务说明</div>
             <p class="fp18">如果您有意见和想法可以告诉我们，如果我们采纳将会给予您回报</p>
        </div>
     
     	 <div class="task_one bor_bot1" style=" height:auto;"><span class="task_pro"></span></div>
       <div class="task_two bg_colorw bor_bot1">
          <div class="all_nav">您的意见或想法：</div>
          <form action="<?php echo site_url('task/task_opinion/obtain_opinion'); ?>"  method="post" name='opinion_form'>
            <textarea placeholder="此处输入..." name="opinions" class="user_opinion"></textarea>
          </form>
       </div>
     	 <!-- <div class="task_two bg_colorw bor_bot1">
               <div class="task_nav">
               	    <a class="task_no">2元红包</a>
                    <a class="task_no">200棵树苗</a>
                    <a class="tast_on">任选其一</a>
               
               </div>
              
       </div> -->

       <div class="good_bot"><a href="#" class="both_bota">提交</a></div>

      </div> 
 
     <!--content end-->
<script type="text/javascript" src="/static/task/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
  $(function(){
    $('.both_bota').click(function() {
      if ($('.user_opinion').val()=='') {
        alert('请输入内容');
        return 0;
      };
      document.opinion_form.submit(function() {
        return true;
      });
    });

  })
</script>
</body>
</html>
