/******************************confirmOrder页面  js***********************************/
//切换支付方式
$(".chosen .braised").click(function(){
	$(".chosen .braised.active").removeClass("active");
	$(this).addClass("active");
});
//模态窗口函数
function noGetOut(){
	$('.grayBg,.noAddressTip').fadeOut();
}
//新增地址页面addAddress  函数声明
	//表单验证--姓名  
	function checkNameInput(){
		var nameLength =$('.newAddCon .nameInput').val().length;	
		var re = /[^\u4e00-\u9fa5]/g;		
		if(nameLength==""){
			$('#addName').html("* 姓名不能为空！");
			$('#addName').css('display','block');
			return false;
		}
		else if(re.test($('.nameInput').val())){
			$('#addName').html("* 请使用合法的姓名！");
			$('#addName').css('display','block');	
			return false;		
		}
		else{
			$('#addName').html("");
			return true;		
		}
	}
	//表单验证--地区
	function checkPosiInput(){
		var nameLength =$('.posiInput').html().length;	
		if(nameLength==""){
			$('#addPosi').html("* 地区不能为空！");
			$('#addPosi').css('display','block');
			return false;
		}
		else{
			$('#addPosi').html("");	
			return true;					
		}
	}
	//表单验证--详细地址
	function checkJieInput(){
		var nameLength =$('.jieInput').val().length;	
		if(nameLength==""){
			$('#addJie').html("* 地区不能为空！");
			$('#addJie').css('display','block');
			return false;
		}
		else{
			$('#addJie').html("");	
			return true;					
		}
	}
	//表单验证--手机号
	function checkPhone(){
		var phoneLength =$(".noInput").val().length
		if(phoneLength=="")
		{
			$('#addNum').html("* 手机号码不能为空!");
			$('#addNum').css('display','block');
			return false;
		}
		else if (!$(".noInput").val().match(/^(1[3|4|5|7|8][0-9]{9})$/))
		{
			$('#addNum').html("* 手机号码格式不正确！");
			$('#addNum').css('display','block');
			return false;
		}
		else
		{
			$('#addNum').html("");
			return true;		
		}
	}
	//点击确认添加按钮时判断是否可运行
	function isAdd(obj){
		if($('#addName').html()!==""||$('#addNum').html()!==""||$('#addPosi').html()!==""||$('#addJie').html()!==""){
			return false;
		}
		return true;		
	}
/******************************selectAddress页面  js***********************************/
//模态窗口函数
function getOutAddress(){
	$('.grayBg,.newAddress').slideDown(200);
	$('.alladress').css('display', 'none');
	$('.newAddress').css('display', 'block');
}
function closeOutAddress(){
	$('.alladress').css('display', 'block');
	$('.newAddress').css('display', 'none');
}
function getInAddress(){
	if($('#addName').html()==""&&$('#addNum').html()==""&&$('#addPosi').html()==""&&$('#addJie').html()==""){
		$('.grayBg,.newAddress').slideUp(200);
	}
	
}
function fadeOut(){
	$('.grayBg,.newAddress').slideUp(200);
}
$(function(){
	$('.content .selectAddress .con').click(function(){
		if(!$(this).hasClass('selected'))
		{
			$(this).addClass('selected');
			$(this).parent('.selectAddress').siblings().children('.con').removeClass('selected');
		}
	})
})
/******************************addToAddress页面  js***********************************/
/*设为默认地址按钮  myAttr为0时是不设为默认   myAttr为1时是设为默认地址*/
function defaultAddress(obj){
	if($(obj).attr("myAttr")==0){
		$(obj).attr('myAttr','1');
	}else{
		$(obj).attr('myAttr','0');
	}
}
/******************************addressMange页面  js***********************************/
//== 控制点击input页面跳转 ==	
function pageJump(){
	window.location.href='selectCity.html';/*链接地址*/
}
//城市选择--点击确定按钮--判断是否已经选择城市
function backBtn(){
	var select = $('#gr_zone_ids').html();
	$('.addare').css('display', 'block');
	$('.area').css('display', 'none');
	if (select=='') {
		return;
	}
    $('.posiInput').html(select);
	$('#addPosi').html("");	
}
//== 控制点击input页面跳转 ==	
function opned(){
	$('.addare').css('display', 'none');
	$('.area').css('display', 'block');
	var text = $("#gr_zone_ids").html();
	if (text=='') {
	    getLocation();
	};
}
function closed(){
	$('.addare').css('display', 'block');
	$('.area').css('display', 'none');
}

//交易详情里的广告随机
function board(){
	var number = parseInt(Math.random() * 3 + 1);
	if(number == 1){
		$(".print img").attr("src", "../../static/shop/images/gug1.png");
		$(".print").attr("href", "/view/shop/list.html?hmsr=details&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}else if(number == 2){
		$(".print img").attr("src", "../../static/shop/images/gug2.png");
		$(".print").attr("href", "/view/shop/list.html?hmsr=details&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}else{
		$(".print img").attr("src", "../../static/shop/images/gug3.png");
		$(".print").attr("href", "/view/shop/list.html?hmsr=details&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}
}

//兑换记录里的广告随机
function boardmap(){
	var number = parseInt(Math.random() * 3 + 1);
	if(number == 1){
		$(".graphs img").attr("src", "../../static/shop/images/grap1.png");
		$(".graphs").attr("href", "/view/shop/list.html?hmsr=shoprecord&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}else if(number == 2){
		$(".graphs img").attr("src", "../../static/shop/images/grap2.png");
		$(".graphs").attr("href", "/view/shop/list.html?hmsr=shoprecord&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}else{
		$(".graphs img").attr("src", "../../static/shop/images/grap3.png");
		$(".graphs").attr("href", "/view/shop/list.html?hmsr=shoprecord&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}
}

//关闭弹框
function close(){
	$(".shadow").css("display","none");
	$(".ceng").css("display","none");
}



$(document).on('click', '.goodsInfo .option', function(event) {
	$(".goodsInfo .active").removeClass("active");
	$(this).find('.chose').addClass("active");
	var an = $(this).find(".count").html();
	var am = $(this).find(".much").html();
	if(am == undefined){
		$(".number .much").html(an);
		$(".number.sum").addClass("not");
	}else{
		$(".number .much").html(an);
		$(".number .side").html('+'+ am);
		$(".number.sum").removeClass("not");
	}
});

