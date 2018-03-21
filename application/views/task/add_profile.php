<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 	 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>开启公益档案</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
     <!--时间css-->
    <link href="/static/task/css/mobiscroll_002.css" rel="stylesheet" type="text/css">
    <link href="/static/task/css/mobiscroll_001.css" rel="stylesheet" type="text/css">
    <link href="/static/task/css/mobiscroll_003.css" rel="stylesheet" type="text/css">
 </head>

<body class="bg_color">
	<!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">开启公益档案</a>
         		 
         </div>
    	
    
    </div>
    <!--head end-->
    
     <!--content start-->
     <div class="both">
           <div class="openimg"><img src="/static/task/images/a.png" alt=""/></div>
           
     </div>
     
     <form action="<?php echo site_url('task/userprofile/add_profile'); ?>" method='post' name='form1'>
      <div class="both bg_colorw">
    
    	 <div class="good_nav ptb15 bor_bot1">
         	   <span class="god_navall good_left">姓名</span>
               
               <input type="text" value="" name="name" placeholder="请输入姓名" class="god_navall good_right"/>         
         </div>
         
         <div class="good_nav ptb15 bor_bot1">
         	   <span class="god_navall good_left">性别</span>
         	   <div class="diy_select" style="float:right">
               		<select name="sex" id="" class="open_navall">
               			<option value="1">男</option>
               			<option value="2">女</option>
              		</select>
               </div>
               <!-- <input type="text" value="" placeholder="" />       -->
         </div>
         
         <div class="good_nav ptb15">
         	   <span class="god_navall good_left">出生日期</span>
               
               <input id="appDateTime" name="birthday" type="text" value="" placeholder="请选择出生日期" class="open_navall good_right" readonly="readonly"/>         
         </div>
         
        
    </div>
    
    
    <div class="both bg_colorw mt20">
    
    	
         
         <div class="good_nav ptb15 bor_bot1">
         	   <span class="god_navall good_left">省/市</span>
               
               		<select name="selectc" class="select_txt">
                        <option value="1">选择城市</option>
                  </select>
               
                   <select name="selectp" onChange="selectcityarea('selectp','selectc','form1');" class="select_txt">
                        <option value="2">选择省份</option>
                   </select>     
                
         </div>
         
    </div>   
    </form>   
         
     <p class="open_con">请真实填写公益档案资料您所做的公益越多得到的特权和优惠将会更多</p>
     <div class="good_bot"><a class="both_bota">提交</a></div>    
     <!--content end-->
          
<script src="/static/task/js/jquery-1.9.1.min.js"></script>  
<script src="/static/task/js/mobiscroll_002.js" type="text/javascript"></script>
<script src="/static/task/js/mobiscroll_004.js" type="text/javascript"></script>
<script src="/static/task/js/mobiscroll_003.js" type="text/javascript"></script>
<script src="/static/task/js/mobiscroll.js" type="text/javascript"></script>
<script language="javascript" src="/static/task/js/city.js"></script>
<script language="javascript">
first("selectp","selectc","form1",0,0);
</script>
<script>	

//时间
 $(function () {
 		$('.both_bota').click(function() {
 			document.form1.submit(function() {
 				if ($("input[name='selectc']").value=='' || $("input[name='selectp']").value=='') {
 					return false;
 				}else{
 					return true;
 				};
 			});
 		});
		var currYear = (new Date()).getFullYear();	//获取今年年份
		var opt={};
		opt.date = {preset : 'date'};
		opt.datetime = {preset : 'datetime'};
		opt.time = {preset : 'time'};
		opt.default = {
			theme: 'android-ics light', //皮肤样式
			display: 'modal', //显示方式 
			mode: 'scroller', //日期选择模式
			dateFormat: 'yyyy-mm-dd',
			lang: 'zh',
			showNow: true,
			nowText: "今天",
			startYear: currYear-150 , //开始年份
			endYear: currYear  //结束年份
		};
		$("#appDate").mobiscroll($.extend(opt['date'], opt['default']));
		var optDateTime = $.extend(opt['datetime'], opt['default']);
		var optTime = $.extend(opt['time'], opt['default']);
		$("#appDateTime").mobiscroll(optDateTime).datetime(optDateTime);
		$("#appTime").mobiscroll(optTime).time(optTime);
	});
	
	
	
</script>

</body>
</html>
