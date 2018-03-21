var GetcodeUrl="/index.php/nonstandard/system/mobilecode";
var CheckcodeUrl="/index.php/nonstandard/system/binding_mobile";
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
	var mobile=$("#mobile").val();
	var imgcode = $("#imgcode").val();
	var  reg=/^\d{11}$/;   
	if(typeof mobile == "undefined" || !reg.test(mobile)){
		$(".tip").html("手机号码格式不正确!");
		return false;
	}
	if (imgcode=='') {
		$(".tip").html("请输入图形验证码！");
		return false;
	};
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
				 $(".tip").html(data.msg);
			 }
			 if(data.status != request_succ){
				 alert(data.msg);
				 $('.password .fr img').attr('src','/codeimg/code_char.php?name=2&d='+Math.random());
				 if(data.url != ' '){
					 UrlGoto(data.url);
				 }
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
	var  promoCode=$("#promoCodes").val();
	var  Mreg=/^\d{11}$/; 
	if(!Mreg.test(mobile)){
		alert("手机号码为空或者格式不正确!");
		return false;
	}
	var  code=$("#code").val();
	var  Creg=/^\d{6}$/; 
	/*if(!Creg.test(code)){
		alert("验证码为空或者格式不正确!");
		return false;
	}*/
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
		   data: "mobile="+mobile+"&code="+code+'&password='+password+'&invitation='+invitation+'&promoCode='+promoCode,
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

function login(){
	var mobile = $('#mobile').val();
	var pwd = $('#password').val();
	var code = $('#code').val();
	if (mobile=='') {
		$('.tip').html('请填入手机号码');
		return;
	};
	if (pwd=='') {
		$('.tip').html('请填入密码');
		return;
	};
	var u = '/index.php/nonstandard/system/userlogin';
	var d = 'code='+code+'&name='+mobile+'&pwd='+pwd;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			var ua = window.navigator.userAgent.toLowerCase();
			var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
			if (iswx==1&&response['data']['isbrand']==0) {
				if (confirm("您未绑定微信号，您是否愿意与此微信绑定？")) {
					location.href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Findex.php%2Fnonstandard%2Fwxuser%2Fuserbindwx&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
					return ;
				}else{
					location.href=response['url'];
					return;
				}
			};
			if (response['url']!='') {
				location.href=response['url'];
			};
		}else{
			if (response['msg']!='') {
				$('.tip').html(response['msg']);
			};
			if (response['data']['code']!=undefined&&response['data']['code']==1) {
				$('.password').css('display', 'block');
			};
			$('.password .fr img').attr('src','/codeimg/code_char.php?name=2&d='+Math.random());
		}
	}
	AjaxRequest(u,d,f);
}

function Getchacode() {
	var  mobile=$("#mobile").val();
	var imgcode = $("#imgcode").val();
	var  reg=/^\d{11}$/;   
	if(typeof mobile == "undefined" || !reg.test(mobile)){
		$('.tip').html('手机号码格式不正确');
		return false;
	}
	var u = '/index.php/nonstandard/system/send_changemobile';
	var d = "mobile="+mobile+"&imgcode="+imgcode;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			if (response['msg']!='') {
				alert(response['msg']);				
			};
		}
		if (response['status']!=request_succ) {
			alert(response['msg']);	
			$('.password .fr img').attr('src','/codeimg/code_char.php?name=2&d='+Math.random());
		};
	}
	AjaxRequest(u,d,f);
}

function submitChangeInfo(){
	var  mobile=$("#mobile").val();
	var  reg=/^\d{11}$/;   
	if(typeof mobile == "undefined" || !reg.test(mobile)){
		$('.tip').html('手机号码格式不正确');
		return false;
	}
	var code = $("#code").val();
	var pwd1 = $("#password1").val();
	var pwd2 = $("#password2").val();
	if (code=='') {
		$('.tip').html('请填写验证码');
		return;
	};
	if (pwd1==''||pwd2=='') {
		$('.tip').html('请填完密码');
		return ;
	};
	var u = '/index.php/nonstandard/system/changepwd';
	var d = "mobile="+mobile+'&code='+code+'&pwd1='+pwd1+'&pwd2='+pwd2;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			if (response['msg']!='') {
				alert(response['msg']);				
			};
			UrlGoto('/index.php/nonstandard/system/Login');
		}else{
			if (response['msg']!='') {
				alert(response['msg']);				
			};
		}
	}
	AjaxRequest(u,d,f);
}