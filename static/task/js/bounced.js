function locate(){
    var sm = parseInt($(".framed").height())/2;
    $(".framed").css("margin-top",'-'+ sm + 'px');
}
$(".framed .graphic img").load(function(){
    locate();
});
$(".framed .graphic img").attr("src","/static/task/task_two/img/reward.png");

$(".close-btn").click(function(){
    $(".blighted , .framed").hide();
});
//签到弹框
function closeqd(){
	$('#back').css('display','none');
	$('.box').css('display','none');
}
$(window).scroll(function(){
	var scrollTop = $(this).scrollTop();
	var scrollHeight = $(document).height();
	var windowHeight = $(this).height();
	if(scrollTop + windowHeight == scrollHeight){
		$(".inList .tasklist li").removeClass("btnhid");
		$(".classbtn").hide();
	}else if($(".tasklist li:last-child").is(":visible")){
		$('.seefinal').show();
	}
})
