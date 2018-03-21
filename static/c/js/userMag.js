//=== 弹出层显示隐藏 ===
//添加用户
function getInAdd(obj){
	$('.grayBg,.addAlert').slideDown(150);
}
function getOutAdd(){
	$('.grayBg,.addAlert').slideUp(150);
}
function addGetout(){
	if($('#noInput').html()==""&&$('#nameP').html()==""&&$('#emailTip').html()==""&&$('#passwordTip').html()==""){
		$('.grayBg,.addAlert').slideUp(150);
	}
}
//修改用户信息
function getOutRevise(){
	$('.grayBg,.reviseAlert').slideUp(150);
}
function saveRGetout(){
	if($('#namePR').html()==""&&$('#passwordTipR').html()==""){
		$('.grayBg,.reviseAlert').slideUp(150);
	}
}

//表单验证--手机号
function checkPhone(){
	var phoneLength =$(".noInput").val().length;
	if(phoneLength=="")
	{
		$('#noInput').html("手机号码不能为空");
		$('#noInput').css('display','block');
		return false;
	}
	else if (!$(".noInput").val().match(/^(1[3|4|5|7|8][0-9]{9})$/))
	{
		$('#noInput').html("手机号码格式不正确");
		$('#noInput').css('display','block');
		return false;
	}
	else
	{
		$('#noInput').html("");
		return true;
	}
}
//姓名
function checkName(obj){
	if($(obj).val()=="")
	{
		$('.addcon #nameP').html("请输入姓名");	
		$('.addcon #nameP').css('display','block');
		return false;
	}
	else if ($(obj).val().match(/[^\u4e00-\u9fa5]/g)) 
	{
		$('.addcon #nameP').html("请输入正确的姓名格式");
		$('.addcon #nameP').css('display','block');
		return false;
	}
	else
	{
		$('.addcon #nameP').html("");
		return true;
	}
}
function checkNameR(obj){
	if($(obj).val()=="")
	{
		$('.revisecon #namePR').html("请输入姓名");	
		$('.revisecon #namePR').css('display','block');
		return false;
	}
	else if ($(obj).val().match(/[^\u4e00-\u9fa5]/g)) 
	{
		$('.revisecon #namePR').html("请输入正确的姓名格式");
		$('.revisecon #namePR').css('display','block');
		return false;
	}
	else
	{
		$('.revisecon #namePR').html("");
		return true;
	}
}
//邮箱
function checkEmail(){
	var account = $(".emailInput").val();
	var length = $(".emailInput").val().length;
	var email = /^\w+[@]\w+[.][\w.]+$/;
	if(length ==""){
		$('#emailTip').html("邮箱不能为空");
		$('#emailTip').css('display','block');
		return false;
	}else if(email.test(account)){
		$("#emailTip").html("");
		return true;
	}else{
			$('#emailTip').css('display','block');
			$('#emailTip').html("请输入正确邮箱");
			return false;
	}
}
//密码验证
function checkpassword(){
	var length =$('.addcon .pwInput').val().length;	
	if(length ==""){
		$('.addcon #passwordTip').html("密码不能为空");
		$('.addcon #passwordTip').css('display','block');
		return false;
	}		
	else if(length < 6){
		$('.addcon #passwordTip').html("密码位数不能小于6位");
		$('.addcon #passwordTip').css('display','block');
		return false;
	}else if(length > 32)
	{
		$('.addcon #passwordTip').html("密码位数不能超过32位");
		$('.addcon #passwordTip').css('display','block');
		return false;
	}else{
		$('.addcon #passwordTip').html("");
		return true;
	}
}
function checkpasswordR(){
	var length =$('.revisecon .pwInput').val().length;	
	if(length ==""){
		$('.revisecon #passwordTipR').html("密码不能为空");
		$('.revisecon #passwordTipR').css('display','block');
		return false;
	}		
	else if(length < 6){
		$('.revisecon #passwordTipR').html("密码位数不能小于6位");
		$('.revisecon #passwordTipR').css('display','block');
		return false;
	}else if(length > 32)
	{
		$('.revisecon #passwordTipR').html("密码位数不能超过32位");
		$('.revisecon #passwordTipR').css('display','block');
		return false;
	}else{
		$('.revisecon #passwordTipR').html("");
		return true;
	}
}
$(function(){
    //=== 用户管理界面添加用户弹窗 自定义下拉菜单  ===
	//点击让ul显示
	$('.addcon .roleselected,.addcon .downArr').click(function(e) {
		$('.addcon .roleUl').show();
    });
	$('.addcon .roleSelect .roleUl a').click(function(e) {
		$(this).closest('.addcon .roleUl').prev().html($(this).html());
		$(this).closest('.addcon .roleUl').prev().attr('num',$(this).attr('num'));
		$(this).closest('.addcon .roleUl').hide();
		$(this).parent().addClass('current').siblings().removeClass('current');
    });
	$('.addcon .roleSelect').mouseleave(function(e) {
        $(this).children('.roleUl').hide();
    });
//=== 用户管理界面修改用户信息 弹窗 自定义下拉菜单  ===
	//点击让ul显示
	$('.revisecon .roleselected,.revisecon .downArr').click(function(e) {
		$('.revisecon .roleUl').show();
    });
	$('.revisecon .roleSelect .roleUl a').click(function(e) {
		$(this).closest('.revisecon .roleUl').prev().html($(this).html());
		$(this).closest('.revisecon .roleUl').prev().attr('num',$(this).attr('num'));
		$(this).closest('.revisecon .roleUl').hide();
		$(this).parent().addClass('current').siblings().removeClass('current');
    });
	$('.revisecon .roleSelect').mouseleave(function(e) {
        $(this).children('.roleUl').hide();
    });
})