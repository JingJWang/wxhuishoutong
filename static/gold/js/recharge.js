$(function(){
	//选择支付方式
	$('.payment ul li').click(function(){
		$(this).addClass('myLi').siblings().removeClass('myLi');
	});
})
