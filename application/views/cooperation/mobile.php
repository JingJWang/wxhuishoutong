<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>绑定手机号</title>
    <link rel="stylesheet" href="/static/home/css/f_style.css"/>
    <link rel="stylesheet" href="/static/home/css/biaodan.css"/>
</head>
<body class="BunBody">
<div class="BPbox">
    <div class="phoneNum">
        <span>手机号</span>
        <input type="text" id="mobile" placeholder="请输入手机号"/>
        <input type="hidden" id="openid" placeholder="请输入手机号"/ value="<?php echo $openid; ?>">
        <input type="button" onclick="Getcode(this);"  class="newbtn" value="获取验证码" />
    </div>
    <div class="code">
        <span>验证码</span>
        <input type="text" id="code" placeholder="请输入验证码"/>
    </div>
    <div class="btn">
        <button onclick="Checkcode(this);">提 交</button>
    </div>
</div>
<div id="turn_gif_box">
    <div id="turn_gif">
        <span>
            <img src="/static/home/images/loading.gif" alt=" "/>
        </span>
    </div>
</div>
<script src="/static/home/js/jquery-1.9.1.min.js"></script>
<script src="/static/home/js/f_js.js"></script>
<script src="/static/home/js/ajax_common.js"></script>
<script> 
var GetcodeUrl="/index.php/nonstandard/system/send_checkmobile";
var CheckcodeUrl="/index.php/cooperation/coop/RegCoop";
/**
 * @param  int  mobile
 */
var wait=60;
function time(o) {
        if (wait == 0) {
            o.removeAttribute("disabled");           
            o.value="获取验证码";
            wait = 60;
        } else {
            o.setAttribute("disabled", true);
            o.value="重新发送(" + wait + ")";
            wait--;
            setTimeout(function() {
                time(o)
            },
            1000)
        }
    }

function Getcode(obj) {
	var  mobile=$("#mobile").val();
	var  reg=/^\d{11}$/;   
	if(typeof mobile == "undefined" || !reg.test(mobile)){
		alert("手机号码格式不正确!");
		return false;
	}
	//获取验证码
	$.ajax({
		   type: "POST",
		   url:  GetcodeUrl,
		   data: "mobile="+mobile,
		   dataType:"json",
		   beforeSend: function(){
			     time(obj);
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if (data.status == request_succ) {
				 alert(data.msg);
			 }
			 if(data.status != request_succ){
				 alert(data.msg);
			 }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ 
			   
		   }
	}); 
}
/**
 * 校验验证码
 * @param int  mobile
 * @param int  code
 */
function Checkcode(){
	var  mobile=$("#mobile").val();
	var  Mreg=/^\d{11}$/; 
	if(!Mreg.test(mobile)){
		alert("手机号码为空或者格式不正确!");
		return false;
	}
	var  code=$("#code").val();
	var  Creg=/^\d{6}$/; 
	if(!Creg.test(code)){
		alert("验证码为空或者格式不正确!");
		return false;
	}
	var openid = $("#openid").val();
	if(openid == '' ){
		alert("请重新进入!");
		return false;
	}
	//校验验证码
	$.ajax({
		   type: "POST",
		   url:  CheckcodeUrl,
		   data: "mobile="+mobile+"&code="+code+"&openid="+openid,
		   dataType:"json",
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if (data.status == request_succ) {
				 alert(data.msg);
				 WeixinJSBridge.call('closeWindow');
			 }
			 if(data.status == request_fall){
				 alert(data.msg);
			 }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){
			   
		   }
	}); 
}


</script>
</body>
</html>