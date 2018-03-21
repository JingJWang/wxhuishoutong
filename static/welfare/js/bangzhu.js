function loadFun(){
	$('header').css({'display':'none','z-index': -1});
	var hei = $(window).height();
	$('.posiDiv').css({'display':'block','height':hei+'px','position':'fixed','top':'0','left':'0'});
	var bz_hei = $(window).height()-$('header').height()-50;
	$('.bz_cont').css({'height':bz_hei+'px'});
	$('body').height($(window).height()).css('overflow','hidden');
}
function closefun(){
	$('header').css({'display':'block','z-index': 99});
	$('.posiDiv').css({'display':'none'});
	$('body').height($(window).height()).css('overflow','auto');
}
//滚动条事件
$(window).scroll(function(){
	if($(window).scrollTop() > $('.first').height()){
		$('header').css({'background':'#81d3fc','position':'fixed'})
	}else{
		$('header').css({'background':'none','position':'absolute'})
	}
});
