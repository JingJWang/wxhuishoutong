$(function(){
	$('.mannerStyle li').click(function(){
		$(this).addClass('button_hover').siblings().removeClass('button_hover');
		var index = $(this).index();
		$('.mannerCont span').eq(index).show().siblings().hide();
	});
	//input增减,精确到小数点
	var oldNum = $('.num').val();
	$('.asNum_p').html(oldNum/1000 + 'kg');
	$('.add').click(function(){	
		oldNum++;
		$('.num').val(oldNum);
		$('.asNum_p').html(oldNum/1000 + 'kg');
	});
	$('.subtract').click(function(){
		if(oldNum < 2){
			return false;
		}
		oldNum--;
		$('.num').val(oldNum);
		$('.asNum_p').html(oldNum/1000 + 'kg');
	});
	$('.num').focus(function(){ 		//focus 获取焦点
		$(document).keyup(function () {
		var	newNum = $('.num').val();
		if(newNum<1 || !newNum){
			$('.asNum_p').html('0.00kg');
				oldNum=0;
			}else{
				$('.asNum_p').html(newNum/1000 + 'kg');
				oldNum=newNum;
			}	
		});
	});
})