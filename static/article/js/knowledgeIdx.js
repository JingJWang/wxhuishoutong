$(document).ready(function(){
	//=== 轮播图 ===
	$dragBln = false;
	$(".timer").addClass("desc");
	$(".main_image").touchSlider({
		flexible : true,
		speed : 200,
		btn_prev : $("#btn_prev"),
		btn_next : $("#btn_next"),
		paging : $(".flicking_con a"),
		counter : function (e){
			$(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
		}
	});
	
	$(".main_image").bind("mousedown", function() {
		$dragBln = false;
	});
	
	$(".main_image").bind("dragstart", function() {
		$dragBln = true;
	});
	
	$(".main_image a").click(function(){
		if($dragBln) {
			return false;
		}
	});
	
	timer = setInterval(function(){
		$("#btn_next").click();
	}, 3000);
	
	$(".main_visual").hover(function(){
		clearInterval(timer);
	},function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		},3000);
	});
	
	$(".main_image").bind("touchstart",function(){
		clearInterval(timer);
	}).bind("touchend", function(){
		timer = setInterval(function(){
			$("#btn_next").click();
		}, 3000);
	});
})
//== 底部导航按钮选中状态 ==	
$('.bottomNav a').click(function(){
	var num = $(this).index()+1;
	//alert(num);
	$(this).addClass('select0'+num+'').siblings().removeClass();
})
function showList(obj){
	$('.listIconopen').slideToggle(300);
	if($(obj).hasClass('bgChange')){
		$(obj).css('background-image','url("/static/article/images/allOpenIcon.png")');
		$(obj).removeClass('bgChange');
	}else{
		$(obj).css('background-image','url("/static/article/images/allCloseIcon.png")');
		$(obj).addClass('bgChange');
	}
}