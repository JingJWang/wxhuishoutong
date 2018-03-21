////点击切换成小类页面
//function navigate(obj){
//  $(".embody").css("display","none");
//  $(".comprise").css("display","block");
//  $("#brandpage").css('display','none');
//  $("#typepage").css('display','block');
//  var id = $("#brandselect").val();
//  $("#brand_"+id).attr('selected','selected');
//}
////点击切换成大类页面
//function toggle(){
//  $(".comprise").css("display","none");
//  $(".embody").css("display","block");
//  $("#typepage").css('display','none');
//  $("#brandpage").css('display','block');
//}
////删除
//function strike(el){
//  $(el).parents(".breed").css("display","none");
//}
////品牌里的操作
//var lastValue = '';
//var lastValue1 = "";
////点击修改
//function revise(el){
//  $(el).parents(".breed").find(".caddy").addClass("active");
//  $(el).parents(".breed").find(".chart").css("display","block");
//  $(el).parents(".breed").find(".field").removeAttr("readonly");
//  lastValue = $(el).parents(".breed").find(".field").val();
//  lastValue1 = $(".breed .item .caddy .bong .field.pag1").val();
//}
////取消按钮
//function cancle(el){
//  $(el).parents(".breed").find(".caddy").removeClass("active");
//  $(el).parents(".breed").find(".chart").css("display","none");
//  $(el).parents(".breed").find(".field").attr("readonly","readonly");
//  $(el).parents(".breed").find(".field").val(lastValue);
//  $(".breed .item .caddy .bong .field.pag1").val(lastValue1);
//}
////保存
//function conserve(el){
//  $(el).parents(".breed").find(".caddy").removeClass("active");
//  $(el).parents(".breed").find(".chart").css("display","none");
//  $(el).parents(".breed").find(".field").attr("readonly","readonly");
//}
////点击添加，出现添加内容
//function showBig(){
//	$('.form_big').show();
//}
//分页
function pagess(one_pag,now,num,state){
	var page = Math.ceil(num/one_pag);//可以分的页数
	var pages = '';
	if (num<=one_pag) {
		 pages='';
	}
	if (now>=one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="menu('+state+',0)">上一页</a></li>';
	};
	if (page<=5) {
	    for (var i = 0; i < page; i++) {
	        pages += '<li class="active"><a href="javascript:;" id="'+(i*one_pag)+'" onclick="menu('+state+','+i*one_pag+')">'+(i+1)+'</a></li>';
	    };
	}else{
	    if ((now/one_pag)<3) {
	    	for (var i = 1; i <= 5; i++) {
	        	pages += '<li class="active"><a href="javascript:; " id="'+((i-1)*one_pag)+'" onclick="menu('+state+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else if (now/one_pag>=(page-3)) {
	        for (var i = (page-4); i <= page; i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="menu('+state+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else{
            for (var i = (now/one_pag-1); i < (now/one_pag+4); i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="menu('+state+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }
	}
	if (now<(page-1)*one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="menu('+state+','+(now+one_pag)+')">下一页</a></li>';
	};
	pages += '<li class="active"><a>共'+page+'页&nbsp;&nbsp;共'+num+'条</a></li>';
	$('.pagination').html(pages);
	$('#'+now+'').css({ background: '#337ab7',color: '#fff'});
}
//添加内容切换
function showAdd(obj,num){
	if(num == 0){
		$(obj).next('.form_big').toggle();
	}else if(num == 1){
		$(obj).next().next('.form_big').show().find('.sousuo_Small').hide().siblings().show();
	}else if(num == 2){
		$(obj).next('.form_big').show().find('.add_save,add_cancel,.inp_small_add').hide().siblings().show();
		$(obj).next('.form_big').show().find('.inp_small_add,.add_save,.add_cancel').hide();
	}else if(num == 4){
		$(obj).next('.form_big').toggle();
	}
}
//点击取消添加信息部分隐藏
function cancelcont(obj){
	$(obj).parent().hide();
}
//点击修改，出来修改的input
function changeform(obj){
	$(obj).parent().next('.change_box').show();
}
//点击关闭修改内容
function close_change(obj){
	$(obj).parent().hide();
}
//点击分类添加效果
$('.list li').click(function(){
	$(this).addClass('hover_list_li').siblings().removeClass('hover_list_li');
})






















