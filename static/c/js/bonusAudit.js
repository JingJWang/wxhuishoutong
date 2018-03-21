$(function(){
	//=== 顶部自定义下拉菜单 ===
	//点击让ul显示
	$('.downSelect .selected,.downSelect .butn').click(function(e) {
		$('.myUl').show();
    });
	$('.downSelect .myUl a').click(function(e) {
		$(this).closest('.myUl').prev().html($(this).html());
		$(this).closest('.myUl').hide();
		$(this).parent().addClass('current').siblings().removeClass('current');
    });
	$('.downSelect').mouseleave(function(e) {
        $(this).children('.myUl').hide();
    });
//=== 订单添加页自定义下拉菜单 ===   
	//点击让ul显示
	$('.downSelectAdd .selectedAdd').click(function(e) {
		$(this).siblings('.myUlAdd').show();
    });
    $('.downSelectAdd .myUlAdd').on('click','li a',function() {
    	var id = $(this).attr('tid');
		$(this).closest('.myUlAdd').prev().html($(this).html());
		$(this).closest('.myUlAdd').prev().attr('tid', id);
		$(this).closest('.myUlAdd').hide();
		$(this).parent().addClass('current').siblings().removeClass('current');
    });
	$('.downSelectAdd').mouseleave(function(e) {
        $(this).children('.myUlAdd').hide();
    });
//=== 订单修改页自定义下拉菜单 ===   
	//点击让ul显示
	$('.downSelectRevise .selectedRevise').click(function(e) {
		$(this).siblings('.myUlRevise').show();
    });
    $('.downSelectRevise .myUlRevise').on('click','li a',function() {
    	var id = $(this).attr('tid');
		$(this).closest('.myUlRevise').prev().html($(this).html());
		$(this).closest('.myUlRevise').prev().attr('tid', id);
		$(this).closest('.myUlRevise').hide();
		$(this).parent().addClass('current').siblings().removeClass('current');
    });
	$('.downSelectRevise').mouseleave(function(e) {
        $(this).children('.myUlRevise').hide();
    });
})  
//全选
function checkall(){
    $(".embody .breed .icon .graph").addClass("active");
    $(".embody .breed .item .field").addClass("sign");
}
function checkInverse(){
    $(".embody .breed .icon .graph.active").removeClass("active");
    $(".embody .breed .item .field.sign").removeClass("sign");
}
//打开列表
function cance(){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
}
//关闭修改弹框
function closeLuxuryBoxRevise(){
	$('.luxuryBoxRevise').css('display','none');
	$('.luxuryBox').css('display','block');
}
//批量查询
function openShadow(){
	$(".shadow").css("display","block");
    $(".shadowDiv").css("display","block");
}
function eyebrow(){
    $(".shadow").css("display","none");
    $(".shadowDiv").css("display","none");
}