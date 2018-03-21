<html>
<head>
<title>回收通-门店地址修改</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('JSPATH'); ?>jquery-1.7.2.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>common.css?v=2000102"/>
<link   rel="stylesheet" type="text/css" href="/static/weixin/public/css/order.css">
<style>
 #loading{display:none;position:absolute;width:100%;top:30%;left:50%;margin-left:-150px;
    text-align:center;padding:7px 0 0 0;font:bold 11px Arial, Helvetica, sans-serif;}
 #subjects{display:none;}
 .login{margin-top:10%;}
 
</style>
</head>
<body>
<div class="form_ctrl page_head">登陆</div>
<div class="login">
            <label class="ctrl_title">用户名</label>
            <input class="input_user" type="text"  id="username"   name="username"  value="<?php echo empty($username)? '' :$username; ?>" />
            <label class="ctrl_title">密码</label>
			<input class="input_pwd"  type="password" id="password" name="password"  value="" />
			<p id="info"></p>
			<input type="button"   name="password" id="sub_login" onclick="Login();" value="确定" />
</div>
<div id="loading">
<img src="../../../static/weixin/public/img/loading.gif" mce_src="../../../static/weixin/public/img/loading.gif" alt="loading.." />
</div>
</body>
<script>
  /**
   * 检测是否存在用户 
   */
  function Login(){
	   var username=$("#username").val();
	   var password=$("#password").val();
	   $.ajax({
	       type: "POST",
	       url: "/index.php/userlogin/login/Checklogin",
	       data:{username:username,password:password},
	       dataType: "json",
  	       beforeSend: function () {
  	  	   // 禁用按钮防止重复提交loading.gif
  	  	   $("#sub_login").attr({ disabled: "disabled" });
       	   $("#loading").show();  
  	       },
	       success: function(data){ 
		      $("#loading").hide(); 
		      $("#username").val(""); 
		      $("#password").val("") ;
		      var reponse = eval(data);
		      if(reponse['status'] == 0){
		    	     alert(reponse['info']);
			  }
		      if(reponse['status'] == 1000){
			         $(".login").css("display",'none');
			         //alert(reponse['url']);
			         location.href=reponse.url;
			  }
 	  	   },
  	       complete: function () {
    	        $("#sub_login").removeAttr("disabled");
    	   },
    	   error: function (data) {
        	    alert('System encountered trouble!');
      	        $("#loading").hide(); 
    	  }
	   });
	   return false;
  }   
</script>
</html>
