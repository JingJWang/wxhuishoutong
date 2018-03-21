$(function(){
	//绑定银行卡弹出验证码
	var bindHeight = $(window).height(); //浏览器当前窗口可视区域高度 
	$('.bind_box').css('height',bindHeight);
	//点击确认支付按钮，弹出输密码
	$('.bindBank').click(function(){
		$('.bind_box').css('display','block');
	})
	$('.bindBtn button').click(function(){
		$('.bind_box').css('display','none');
	})
})
