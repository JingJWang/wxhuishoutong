$(document).ready(function(){
	//轮播图  
	var mySwiper = new Swiper('.swiper-container',{
		autoplay : 5000,
		autoplayDisableOnInteraction : false,
		loop : true,
		pagination : '.swiper-pagination',
		paginationClickable :true,
	})  
	//评价	
	var index=0;
	var oP=$('.spanIndex a');
	var oUl=$('.userRecord ul');
	var aLi = $('.userRecord ul li').length;
	var oLiw=$('.userRecord ul li').width();
	var ulWidth = oLiw*aLi;
	var num=0;
	var timer;
	oUl.width(ulWidth+'px');
	oP.click(function(){
		index=$(this).index();
		oUl.stop().animate({'left':-oLiw*(index)},'fast');
		oP.eq(index).addClass('onea').siblings().removeClass('onea');
	})
	$('.userRecord').hover(function(){
		clearInterval(timer)
	},function(){
		timer=setInterval(function(){
			num++;
			if(num==aLi){
				num=0;	
			};
			oP.eq(num).addClass('onea').siblings().removeClass('onea');
			oUl.stop().animate({left:-oLiw},500,function(){
				$('.userRecord ul li:first').appendTo(oUl)
				oUl.css({'left':0})
			})	
		},5000)
	}).trigger('mouseleave')
//== 底部导航按钮选中状态 ==	
	$('.bottomNav a').click(function(){
	var num = $(this).index()+1;
	//alert(num);
	$(this).addClass('select0'+num+'').siblings().removeClass();
	})
});
//== 控制点击input页面跳转 ==	
function pageJump(){
	window.location.href='/index.php/nonstandard/submitorder/BrandSearch';/*链接地址*/
}
function locate(){
	var sm = parseInt($(".inform").height())/2;
	$(".inform").css("margin-top",'-'+ sm + 'px');
}
//图片加载后更改弹框的大小
$(".inform img").load(function(){
	locate();
});
$(".inform img").attr("src","/static/m/images/notes.png");

$(".realize").click(function(){
	$(".shade , .inform").hide();
});



















