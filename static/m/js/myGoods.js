//评价页面 选中标签改变样式
function label(obj) {
	if (!$(obj).hasClass('selected')) {
		$(obj).addClass('selected');
	} else {
		$(obj).removeClass('selected');
	}
}
//取消原因页面  取消原因多选
function selReason(obj) {
	if (!$(obj).hasClass('selReason')) {
		$(obj).addClass('selReason');
		$(obj).siblings().removeClass('selReason');
	}
}
//订单详情页面
function twoBidAlert() {
	$('.odGray').show();
}
function twoBidHide() {
	$('.odGray').hide();
}
//我的物品页面关闭广告
function closeAdv(obj) {
	$(obj).parent().hide();
	$('.goodsCon').css('margin-top', '90px');
}
//提交订单页面
function getOutAlert() {
	var inputNumber = $('.addressIn input').size();
	var addressval = $('#address').val();
	if (inputNumber !== 4 || addressval == "") {
		$('.grayBg,.ifGetOut01').fadeIn();
		return false;
	} else {
		$('.grayBg,.ifGetOut02').fadeIn();
	}
}
function noGetOut() {
	$('.grayBg,.ifGetOut').fadeOut();
}
//search页面
function searchselect() {
	$('.downSlide').fadeIn(200);
}
//手机品牌型号选择页面
function swiperheight() {
	//获取手机屏幕高度
	var screenHeight = $(window).height();
	var top = $('.conLeft ').offset().top;
	var zhi = screenHeight - top;
	$('.conRight,.conLeft').height(zhi);
}
$(function() {
	//-------------------  myGoods页面js  ---------------------------------
	$('.goodsNav a').click(function() {
		$(this).addClass('current').siblings().removeClass('current');
	})
	//-------------------  bidList页面js  ---------------------------------
	
	//-------------------  phonePadSelect页面js  ---------------------------------
	//下拉菜单
	$('.bmselected').click(function(e) {
		$(this).next().slideDown(200);
		$('.backselect .mySelect .gray').show();
	});
	//给型号列表加数字
	Number();
	//-------------------  search页面js  ---------------------------------
	//选择手机还是平板
	$('.searchBox .downSlide ul li').click(function() {
		var con = $(this).html();
		$('.searchBox .search p').html(con);
		$('.downSlide').fadeOut(200);
		if($("#value").html() == "手机"){
			$('.bmselected').html("手机");
			$(".msUl li.current").removeClass("current");
			$(".msUl li[data-id='5']").addClass("current");
		}else{
			$('.bmselected').html("平板电脑");
			$(".msUl li.current").removeClass("current");
			$(".msUl li[data-id='7']").addClass("current");
		}
	});
	if($(".bmselected").html() == "手机"){
		$("#value").html("手机");
	}else if($(".bmselected").html() == "平板电脑"){
		$("#value").html("平板");
	}
});
function clickType(obj) {
	$(obj).closest('.msUl').prev().html($(obj).html());
	$(obj).closest('.msUl').hide();
	$('.backselect .mySelect .gray').hide();
	$(obj).parent().addClass('current').siblings().removeClass('current');
	if($(".bmselected").html() == "手机"){
		$("#value").html("手机");
	}else if($(".bmselected").html() == "平板电脑"){
		$("#value").html("平板");
	}
}
function Number() {
	$('.con .conRight ul li').each(function(index, element) {
		var number = index + 1;
		$(element).find('span').html(number);
	});
}
function brandSign(obj) {
	$(obj).addClass('current').siblings().removeClass('current');
}
function sorted(obj) {
	if ($(obj).hasClass("sorted")) {
		$(obj).removeClass('sorted');
	} else {
		$(obj).addClass('sorted').siblings().removeClass('sorted');
	}
}
function selected(obj) {
	if ($(obj).hasClass("selected")) {
		$(obj).removeClass('selected');
	} else {
		$(obj).addClass('selected').siblings().removeClass('selected');
	}
}
function confirms(obj) {
	if (!$(obj).hasClass('current')) {
		$(obj).addClass('current');
	} else {
		$(obj).removeClass('current');
	}
}
function  coopclass(){
	//判断分数显示星星
	$('.eachbidLine .bidLine').each(function(index, element) {
		var scores = $(element).find('.scoreStar .scores').attr('data-class');
		var number = scores * 2;
		//console.info(number);
		$(element).find('.starPic').addClass('starPic' + number);
	});
}

function jump(){
	if($("#value").html() == "平板"){
		window.location = '/index.php/nonstandard/submitorder/BrandSearch?id=1';
	}else{
		window.location = '/index.php/nonstandard/submitorder/BrandSearch?id=2';
	}
}

//免邮规则
$(".rule,.anquan").click(function(){
	$(".shade , .regulation").show();
	$(".closeBtn , .realize").click(function(){
		$(".shade , .regulation").hide();
	});
});

//添加地址弹框
$(".addSite").click(function(){
	$(".shadow , .frame").show();
	$(".frame .close-btn").click(function(){
		$(".shadow , .frame").hide();
	});
	$(".frame .add-btn").click(function(){
		var name=$("#names").val();
		var mobile=$("#tel").val();
		var city=$("#city1").val();
		var quarters=$("#desc").val();
		if(name == ''){
			alert('姓名不能为空');return false;
		}else if(mobile == ""){
			alert("电话号码不能为空");
		}else if(!mobile.match(/^(1[3|4|5|7|8][0-9]{9})$/)){
			alert('电话格式不正确');return false;
		}else if(city == ''){
			alert('地址信息必填!');return false;
		}else if(quarters == ''){
			alert('详细信息不能为空');return false;
		}else{
			$(".shadow , .frame").hide();
		}
	})
});
