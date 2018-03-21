
$(function() {
	//到首页
	$('.FirstA').click(function(){
		
	});
	//选择支付方式
	$('.SortExchange ul li').click(function(){
		$(this).addClass('hover_li').siblings().removeClass('hover_li');
		//让它相对应的出现（现金-库存）
		var index = $(this).index();
		$('.way div').eq(index).show().siblings().hide();
	});
	//input增减
	var oldNum = parseInt($('.num').val());
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
		var	newNum = parseInt($('.num').val());
		if(newNum<1 || !newNum){
			$('.asNum_p').html('0.00kg');
				oldNum=0;
			}else{
				$('.asNum_p').html(newNum/1000 + 'kg');
				oldNum=newNum;
			}	
		});
	});	
	//点击金属效果
	$('.bigSort .purity_box .purity').eq(0).show().siblings().hide();	//初始化纯度
	$('.bigSort .purity_box .purity .purity_span').eq(0).removeClass('hover_span');
	$('.bigClassify .purity_box .purity').eq(0).show().siblings().hide();		//初始化分类
	$('.recycleSort li').eq(0).removeClass('recycleSortLi');					//默认第一个没有加class	
	$('.recycleSort li').click(function(){		
		$(this).addClass('recycleSortLi').siblings().removeClass('recycleSortLi');		
		var index = $(this).index();		
		$('.bigSort .purity_box .purity').eq(index).show().siblings().hide();		
		$('.bigClassify .purity_box .purity').eq(index).show().siblings().hide();		
	});
	$('.purity span').click(function(){		
		$(this).addClass('hover_span').siblings().removeClass('hover_span');		
	});
	
	
});