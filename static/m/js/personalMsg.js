/******************************personalMessage页面js***********************************/
//模态窗口函数
function getOutAlert(){
	$('.grayBg,.ifGetOut').fadeIn();
}
function noGetOut(){
	$('.grayBg,.ifGetOut').fadeOut();
}
//电话号码特殊符号****替换函数
function phoneNumber(){
	var number = $('.phoneNumber').text();
	var four = number.substr(3,4);
	var re = new RegExp(four,"g");
	var end = number.replace(re,"****")
	$('.phoneNumber').html(end);
}
/******************************drawingCash页面    js***********************************/
function emptyCashInput(){
	$('#cashIput').val("");
}
//点击全部提现时把剩余金额加载到input中
function allCash(){
	var defaltCash = $('.nowCash .money .cur').html();
	if( defaltCash <= 18000 ){
		$('.mInput .mEnter').val(defaltCash);
		$('.cashTip').html("");
	}else{
		$('.grayBg,.ifGetOut').fadeIn();
		$('.ifGetOut .conP').html("今日提现上限为18000！");
	}
}
function writeCash(){
	var defaltCash = $('.nowCash .money .cur').html();
	var cash = $('#cashIput').val();	
	//alert(defaltCash);
	//alert(cash);
	//alert(Number(defaltCash));
	if( Number(cash) > 18000 ){
		$('.grayBg,.ifGetOut').fadeIn();
		$('.ifGetOut .conP').html("今日提现上限为18000！");
	}
	else if( Number(cash) > Number(defaltCash) )
	{
		$('.grayBg,.ifGetOut').fadeIn();
		$('.ifGetOut .conP').html("您目前的余额没有这么多哟！");
	}
}
//表单验证
	//提现金额
	function checkCash(obj){
		if($(obj).val()=="")
		{
			$('.cashTip').html("* 请输入提现金额！");	
			$('#isCash').css('backgroundColor','#acacac');
		}
		else if ($(obj).val().match(/[^\d/.]/g)) 
		{
			$('.cashTip').html("* 请输入正确的数值！");
			$('#isCash').css('backgroundColor','#acacac');	
		}
		else
		{
			$('.cashTip').html("");
			$('#isCash').css('backgroundColor','#58ab23');
		}
	}
	//姓名
	function checkName(obj){
		if($(obj).val()=="")
		{
			$('#nameP').html("* 请输入姓名！");	
			$('#isCash').css('backgroundColor','#acacac');
		}
		else if ($(obj).val().match(/[^\u4e00-\u9fa5]/g)) 
		{
			$('#nameP').html("* 请输入正确的姓名格式！");
			$('#isCash').css('backgroundColor','#acacac');	
		}
		else
		{
			$('#nameP').html("");
			$('#isCash').css('backgroundColor','#58ab23');
		}
	}
	//验证码输入验证
	function codeNo(obj){
		var codeLength = $('#noteCode').val().length;
		if($(obj).val()=="")
		{
			$('#codeP').html("* 请输入验证码！");	
			$('#isCash').css('backgroundColor','#acacac');
		}
		else if ($(obj).val().match(/[^\w]/g)) 
		{
			$('#codeP').html("* 请输入数字或字母验证码！");
			$('#isCash').css('backgroundColor','#acacac');	
		}
		else if (codeLength !== 4) 
		{
			$('#codeP').html("* 请输入4位验证码！");
			$('#isCash').css('backgroundColor','#acacac');	
		}
		else
		{
			$('#codeP').html("");
			$('#isCash').css('backgroundColor','#58ab23');
		}
	}
//点击确认提交按钮时判断是否可运行
function isCash(obj){
	if($('.cashTip').html()!==""||$('.codeP').html()!==""||$('.nameP').html()!=="")
	{
		return false;
	}
}

//广告随机
function board(){
	var number = parseInt(Math.random() * 3 + 1);
	if(number == 1){
		$(".print img").attr("src", "../../../static/task/task_two/img/guangao.jpg");
		$(".print").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}else if(number == 2){
		$(".print img").attr("src", "../../../static/task/task_two/img/guangao.jpg");
		$(".print").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}else{
		$(".print img").attr("src", "../../../static/task/task_two/img/guangao.jpg");
		$(".print").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}
}
board();

