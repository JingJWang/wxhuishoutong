var GetcodeUrl="/index.php/task/taskshare/send_checkmobile";
var CheckcodeUrl="/index.php/task/taskshare/binding_mobile";
var CheckcodeUrlTwo="/index.php/nonstandard/system/binding_mobile";
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
	var imgcode = $("#imgcode").val();
	var  reg=/^\d{11}$/;   
	if(typeof mobile == "undefined" || !reg.test(mobile)){
		alert("手机号码格式不正确!");
		return false;
	}
	//获取验证码
	$.ajax({
		   type: "POST",
		   url:  GetcodeUrl,
		   data: "mobile="+mobile+"&imgcode="+imgcode,
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
				$('.password .fr img').attr('src','/codeimg/code_char.php?name=2&d='+Math.random());
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
 * 校验验证码 用于游戏
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
	var  params=$("#params").val();
	var  invitation =  $("#invitation").val();
	var  password = $("#password").val();
	if(password == '' ){
		alert('密码为必填选项!');
		return false;
	}
	//获取验证码
	$.ajax({
		   type: "POST",
		   url:  CheckcodeUrl,
		   data: "mobile="+mobile+"&code="+code+'&password='+password+'&invitation='+invitation+"&params="+params,
		   dataType:"json",
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if (data.status == request_succ) {
			 	alert('注册成功');
				UrlGoto(data.url);
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

/**
 * 校验验证码 用于任务中心
 * @param int  mobile
 * @param int  code
 */
function CheckcodeTwo(){
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
	var  invitation =  $("#invitation").val();
	var  password = $("#password").val();
	if(password == '' ){
		alert('密码为必填选项!');
		return false;
	}
	//获取验证码
	$.ajax({
		   type: "POST",
		   url:  CheckcodeUrlTwo,
		   data: "mobile="+mobile+"&code="+code+'&password='+password+'&invitation='+invitation,
		   dataType:"json",
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if (data.status == request_succ) {
				 UrlGoto(app+'/task/usercenter/taskcenter');
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
