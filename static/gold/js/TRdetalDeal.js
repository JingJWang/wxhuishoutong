$(function(){
	/*//tab切换
	$('.metalDeal ul li').click(function(){
		$(this).addClass('myHover').siblings().removeClass('myHover');
		var index = $(this).index();
		$('.buySale .buy').eq(index).show().siblings().hide();
	});*/
	//弹出支付密码
	var pasHeight = $(window).height(); //浏览器当前窗口可视区域高度 
	$('.payPassword_box').css('height',pasHeight);
	$('.aboutPayment').css('height',pasHeight);	//弹出支付方式
	//买入
	$('.buyPaymentBtn').click(function(){		//点击按钮弹出支付方式
		$('.aboutPayment').css('display','block');
	});
	//.paymentClose关闭弹框
	$('.paymentClose').click(function(){		//点击按钮关闭支付方式
		$('.aboutPayment').css('display','none');
	});
	//选择支付方式（余额  微信...）
	$('.Payment_box ul li').click(function(){
		$(this).find('span').addClass('aboutPaymentSpan');
		$(this).siblings().find('span').removeClass('aboutPaymentSpan');
	});
	//点击余额支付输入密码
	$('.Payment_box ul li').eq(0).click(function(){
		if($(this).attr('id')=='IdaboutPayYe'){
			$('.aboutPayment').css('display','none');
			$('.passwordSet').css('display','block');
		}
		$('.payBtn .confirmPay').click(function(){
		$('.passwordSet').css('display','none');
			window.location.href = "../../../view/gold/TRBuySuccess.html";
		});
		$('.cancelPay').click(function(){
			$('.payPassword_box').css('display','none');
		});
	});
	
	//卖出部分
	$('.sellPaymentBtn').click(function(){
		$('.passwordSet').css('display','block');
		$('.payPassword').css('height','8.325rem')
		$('.passwordSet p').css('display','none');
		$('.payPwd').css('margin-top','30px');
		//支付密码框点击确认按钮
		$('.payBtn .confirmPay').click(function(){
		$('.passwordSet').css('display','none');
			window.location.href = "../../../view/gold/TRBuySuccess.html";
		});
	});
	
});
