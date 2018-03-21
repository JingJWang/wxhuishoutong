$(function(){
	$('.selectPay li').click(function(){
		$(this).addClass('payLi').siblings().removeClass('payLi');
	})
})
