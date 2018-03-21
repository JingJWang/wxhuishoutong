$(function(){
	//弹出支付密码
	var pasHeight = $(window).height(); //浏览器当前窗口可视区域高度 
	$('.payPassword_box').css('height',pasHeight);
	//点击确认支付$(this).addClass('myLi') == 按钮，弹出输密码
	$('.affirmPay').click(function(){
		if($('.payment ul li.myLi').attr('id')=='my1'){
			$('.passwordSet').css('display','block');
		}else if($('.payment ul li.myLi').attr('id')=='my2'){
			//window.location.href='changePassword.html'
		}
	});
	//选择支付方式
	$('.payment ul li').click(function(){
		$(this).addClass('myLi').siblings().removeClass('myLi');
	});
	//点击按钮,关闭弹框
	$('.payBtn button').click(function(){
		$('.passwordSet').css('display','none');
	});
});
