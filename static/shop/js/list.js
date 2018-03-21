var w = document.documentElement.clientWidth;
var h = document.documentElement.clientHeight;
var t1=new TouchScroll({id:'wrapper','width':5,'opacity':0.7,color:'#555',minLength:20});
$("#pagenavi2").css("display","block");
	var active=0,
	as=document.getElementById('pagenavi2').getElementsByTagName('a');	
	for(var i=0;i<as.length;i++){
		(function(){
			var j=i;
			as[i].onclick=function(){
				t2.slide(j);
				return false;
			}
		})();
	}	
	$("#blist").css("display","block");
	var t2=new TouchSlider({id:'blist', speed:600, timeout:3000, before:function(index){
	as[active].className='';
	active=index;
	as[active].className='active';
}});
var urltype = getUrlParam('type');
if (urltype==null) {
	type = 1;
}else{
	var arr = urltype.split("_");
	type = arr[0];
	postion = arr[1];
}
var code=getUrlParam('code');
var id=getUrlParam('openid');
Getgoods(code,id);




//跳转到搜索页面
$(".rise").click(function(){
	location.href = "./trawled.html";
});

//判断选中了哪个分类
var an = $(".rome.sn").attr("data-id");
$(".right .val[data-id = '" + an + "']").show();

$('.mission').css('height', $(window).height() - 50 - 50);

$(document).bind('scroll', function (e){
	var t = $("body").scrollTop();  //获取滚动距离
	var s = $('.locate').offset().top;
	if (s - t < 2) {
		$('#product_list .right, #product_list .left').css('overflow', 'auto');
	} else {
		$('#product_list .right, #product_list .left').css('overflow', 'hidden');
	}
});

$(window).resize(function () {
	$('.mission').css('height', $(window).height() - 50 - 50);
});

$(document).on('click', '.rome', function(event) {
	var an = $(this).attr("data-id");
	$(".right .val .na").html(Gcontents[an]);
	// $(".right .val").hide();
	// $(".right .val[data-id='"+ an + "']").show();
	$(".rome.sn").removeClass("sn");
	$(this).addClass("sn");
	$('#product_list .right').scrollTop(0);
});

function tourl(id){
	var an = $('.left .sn').attr("data-id");
	var a = $('.right').scrollTop();
	window.history.pushState(null, null, '/view/shop/list.html?type='+an+'_'+a);//修改url
	if(id == 657){
		location.href = '/view/shop/goldshop.html?id='+id;
	}else{
		location.href = '/view/shop/info.html?id='+id;		
	}
}

function locate(){
	var sm = parseInt($(".inform").height())/2;
	$(".inform").css("margin-top",'-'+ sm + 'px');
}
//图片加载后更改弹框的大小
$(".inform img").load(function(){
	locate();
});
$(".inform img").attr("src","/static/shop/images/note.png");

$(".realize").click(function(){
	$(".shade , .inform").hide();
});