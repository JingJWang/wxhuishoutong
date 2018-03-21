<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>好人好报</title>
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
         		 <a href="javascript:history.back()" class="iall_left"></a> 
         	     <a class="headdis_cen">好人好报</a>
         		 
         </div>
    	
    
    </div>
    <!--head end-->



 	<div class="both">
           <a href="openfile.html" class="openimg"><img src="/static/task/images/a.png" alt=""/></a>
           
           <div class="needhelp_nav"></div>
  </div>
  <form action="" method="post" enctype="multipart/form-data" name='form1'>   
    <div class="both bg_colorw">
    
    	 <div class="good_nav ptb15 bor_bot1">
         	   <span class="god_navall good_left">姓名</span>
               
               <input type="text" value="" name="name" placeholder="请输入姓名" class="god_navall good_right"/>         
         </div>
         
         <div class="good_nav ptb15 bor_bot1">
         	   <span class="god_navall good_left">身份证号</span>
               
               <input type="text" value="" name="idcard" placeholder="请输入身份证号" class="god_navall good_right"/>         
         </div>
         
         <div class="good_nav ptb15">
         	   <span class="god_navall good_left">手机号</span>
               
               <input type="text" value="" name="iphone" placeholder="请输入手机号" class="god_navall good_right"/>
         </div>
         
        
    </div>
     
     <div class="both bg_colorw mt20">
           <div class="good_nav ptb15 bor_bot1">
               <span class="god_navall good_left">详细地址</span>
               
               <input type="text" value="" name="address" placeholder="请输入详细地址" class="god_navall good_right"/>         
           </div>
     </div>
     
     
     <div class="good_tishi">请详细填写您遇到的困难，我们将会进行审核</div>
    
     
     <div class="both bg_colorw ptb10">
              <textarea placeholder="详细说下需要怎样的帮助" name="why_help" class="print_txt"></textarea>
              
              <div class="add_nav">
                    
                  <input type="file" name="imgs" id="file1" class="yin_file" />
                                
                  <div  class="upload">
                    <img src="/static/task/images/addimg.png" id="img1" >
                               
                  </div>
                      
            </div>
     </div>
     
     
     
     <div class="both bg_colorw mt20">
     		<div class="good_nav ptb15 bor_bot1">
               <span class="god_navall good_left">需要日期</span>
               
               <input id="appDateTime" type="text" value="" name="date" placeholder="请选择日期" class="god_navall good_right good_bgr" readonly="readonly" />         
            </div>
           
           <div class="good_nav ptb15 bor_bot1">
               <span class="god_navall good_left">需要金额</span>
               
               <input type="text" value="" placeholder="请输入需要金额" name="need_money" class="god_navall good_right"/>         
           </div>
     </div>
     
     
     <div class="good_bot"><a href="#" class="both_bota">提交</a></div>
  </form>
     
     
<script src="/static/task/js/jquery-1.9.1.min.js"></script>  
<script src="/static/task/js/mobiscroll_002.js" type="text/javascript"></script>
<script src="/static/task/js/mobiscroll_004.js" type="text/javascript"></script>
<script src="/static/task/js/mobiscroll_003.js" type="text/javascript"></script>
<script src="/static/task/js/mobiscroll.js" type="text/javascript"></script>
<script>	
	$("#file1").change(function(){
		
		var objUrl = getObjectURL(this.files[0]) ;
		console.log("objUrl = "+objUrl) ;
		if (objUrl) {
			$("#img1").attr("src", objUrl) ;
		}
	}) ;
	//建立一個可存取到該file的url
	function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) { // basic
			url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) { // mozilla(firefox)
			url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) { // webkit or chrome
			url = window.webkitURL.createObjectURL(file) ;
		}
		return url ;
	}
	
	
	//时间
	 $(function () {
      $('.both_bota').click(function() {
        document.form1.submit(function() {
            return true;
        });
      });
			var currYear = (new Date()).getFullYear();	//获取今年年份
      var nowData=new Date();
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
        minDate: nowData, 
        // maxDate:new Date(nowData.getFullYear()+20,nowData.getMonth(),nowData.getDate()),
		    // startYear: currYear , //开始年份
		    endYear: currYear+20  //结束年份
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
