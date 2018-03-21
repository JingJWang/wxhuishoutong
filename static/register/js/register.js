/******************************注册页面      js***********************************/
var InterValObj; //timer变量，控制时间
var count = 60; //间隔函数，1秒执行
var curCount;//当前剩余秒数
var code = ""; //验证码
var codeLength = 6;//验证码长度
function sendMessage() {
	curCount = count;
	var dealType; //验证方式
	var uid=$("#uid").val();//用户uid
	if ($("#phone").attr("checked") == true) {
		dealType = "phone";
	}
	else {
		dealType = "email";
	}
	//产生验证码
	for (var i = 0; i < codeLength; i++) {
		code += parseInt(Math.random() * 9).toString();
	}
	//设置button效果，开始计时
		$("#btnSendCode").attr("disabled", "true");
		$("#btnSendCode").val(  curCount + "s");
		$("#btnSendCode").css({'border-color':'#f85e26','color':'#f85e26'});
		InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
}
//timer处理函数
function SetRemainTime() {
	if (curCount == 0) {                
		window.clearInterval(InterValObj);//停止计时器
		$("#btnSendCode").removeAttr("disabled");//启用按钮
		$("#btnSendCode").val("获取验证码");
		if($('.mInput .enter').val()==""){
			$("#btnSendCode").css('background','#fff');
		}else{
			$('.mInput #btnSendCode').css('background','#fff');
		}
		code = ""; //清除验证码。如果不清除，过时间后，输入收到的验证码依然有效    
	}
	else {
		curCount--;
		$("#btnSendCode").val( + curCount + "s");
		$("#btnSendCode").css('background','#fff');
	}
}

//密码显示与隐藏图标改变
function eyePic(obj){
	if($(obj).hasClass('open')){
		$(obj).removeClass('open');
		$(obj).siblings('#password').attr('type','password');
		$(obj).siblings('#password1 , #password2').attr('type','password');
	}else{
		$(obj).addClass('open');
		$(obj).siblings('#password').attr('type','text');
		$(obj).siblings('#password1 , #password2').attr('type','text');
	}
}
//判断手机号码是否输入正确 获取验证码显示黄色
	//表单验证--手机号
	function checkPhone(obj){
		if ($(obj).val().match(/^(1[3|4|5|7|8][0-9]{9})$/)) // /^(((13[0-9]{1})|159|153)+\d{8})$/
		{
			$('.entry span input').css({'border-color':'#f85e26','color':'#f85e26'});
			return true;
		}else
		{
			$('.entry .tip').css("height" , "30px");
			$('.entry .tip').html("提示： 请输入正确的手机号码");
			$('.entry span input').css({'border-color':'#acacac','color':'#acacac'});
			return false;
		}
	}
//判断验证码的位数
/*function codelength(){
	var length = $('.code input').val().length;
	if(length!==6){
		$('.entry .tip').css("height" , "30px");
		$('.entry .tip').html("提示： 请输入正确的验证码");
	}else{
		$('.entry .tip').html("");
	}
}*/
//验证密码长度
function passwordlength(){
	var length = $('#password').val().length;
	if(length<6||length==0){
		$('.entry .tip').css("height" , "30px");
		$('.entry .tip').html("提示： 请输入6位以上密码");
	}else{
		$('.entry .tip').html("");
	}
}
//验证手机号是否输入
function phoneSuccess(){
	if ($('.entry .phone .enter').val()=="") 
	{
		return false;
	}else if(!$('.entry .phone .enter').val().match(/^(1[3|4|5|7|8][0-9]{9})$/))
	{
		return false;
	}else
	{
		sendMessage();
	}
}
//点击提交按钮 验证是否为空
function submitSuccess(){
	var length01 = $('.entry .phone .enter').val().length;
	var length02 = $('.entry .code .enter').val().length;
	var length03 = $('.entry .password #password').val().length;
	if(length01==0||length02==0||length03==0){
		return false;
	}
}


//广告消失
function vanish(){
	$(".advert").slideUp()
}

//如果有推广码
/*
 if(){
 $("#promoCode #mobile").attr("readonly","readonly");
 $("#promoCode #mobile").css("color" , '#575757');
 $("#promoCode #mobile").val("推广码：" );
 $("#promoCode .hint").hide();
 }
 */




