$(function(){
	//弹出修改密码成功
	var pwdHeight = $(window).height(); //浏览器当前窗口可视区域高度 
	$('.mSuccess_box').css('height',pwdHeight);
	//点击确认支付按钮，弹出输密码
	$('.affirmBtn').click(function(){
		$('.mSuccess_box').css('display','block');
	})
	$('.mSuccess .affirmModification').click(function(){
		$('.mSuccess_box').css('display','none');
	})
	
})