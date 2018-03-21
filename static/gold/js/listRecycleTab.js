$(function(){
	$('.AllList .accomplishList').eq(2).show().siblings().hide();	//初始化内容
	$('.indent a').click(function(){
		$(this).addClass('indent_hover').siblings().removeClass('indent_hover');
		index = $(this).index();
		$('.AllList .accomplishList').eq(index).show().siblings().hide();
	})
})
