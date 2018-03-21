	$(function(){
		/*自定义下拉菜单-----------------------*/
		//点击让ul显示
		$('.selected').click(function(e) {
            //$('.msUl').show();
			$(this).next().show();
        });
		
		$('.mySelect .msUl a').click(function(e) {
            //alert($(this).html());
			//$('.selected').html($(this).html());
			
			$(this).closest('.msUl').prev().html($(this).html());
			
			//1、让ul消失 2、给当前点击的父亲添加特殊current
			//$('.msUl').hide();
			$(this).closest('.msUl').hide();
			$(this).parent().addClass('current').siblings().removeClass('current');
        });
		
		//给大盒子添加鼠标离开 让ul隐藏
		$('.mySelect').mouseleave(function(e) {
            $(this).children('.msUl').hide();
        });
        /*皮包 腰带 衣服 手机 一排点击选中后的样式变化*/
        $('.picList').on('click','#ty24', function(event) {
       		$(this).addClass('types current01 brandname').siblings().removeClass();
        });
        $('.picList').on('click','#ty25', function(event) {
       		$(this).addClass('types current02 brandname').siblings().removeClass();
        });
        $('.picList').on('click','#ty26', function(event) {
       		$(this).addClass('types current03 brandname').siblings().removeClass();
        });
        $('.picList').on('click','#ty27', function(event) {
       		$(this).addClass('types current04 brandname').siblings().removeClass();
        });
	});
