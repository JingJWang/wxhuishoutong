//分页样式
function typeNextPage(page){alert(page);
	if(page == "+1"){
		var select=$("#typepage .dig").html();
		var next = Number(select) - 1;
		if( $("#type_"+next).length > 0 ){
			$("#type_"+select).removeClass("dig");
			$("#type_"+next).addClass("dig");
			orderList(next);
		}else{
			alert('当前页已经是第一页');
			return false;
		}
		if(next > 3){
			var none = next -4;
			var diplay = next + 1;
			$("#type_"+next).css("display","inline");
			$("#type_"+diplay).css("display","inline");
			$("#type_"+none).css("display","none");
		}
	}
	if(page == "-1"){		
		var select=$("#typepage .dig").html();
		var next = Number(select) + 1;
		if( $("#type_"+next).length > 0 ){
			$("#type_"+select).removeClass("dig");
			$("#type_"+next).addClass("dig");
			orderList(next);
		}else{
			alert('当前页已经是最后一页');
			return false;
		}
		if(next > 3){
			var none = next -4;
			var diplay = next + 1;
			$("#type_"+next).css("display","inline");
			$("#type_"+diplay).css("display","inline");
			$("#type_"+none).css("display","none");
		}
	}
	if(page  != '-1' && page !='+1'){
		var next=page+1;
		if( $("#type_"+next).length > 0 ){
			orderList(page);
		}else{
		   alert('当前页已经是最后一页');
		   return false;
		}
		if(page > 3){
			var none = next -4;
			$("#type_"+next).css("display","inline");
			$("#type_"+none).css("display","none");
		}
	}
}
//切换页数的颜色
function orderPageBJ(obj){
	    $(obj).siblings("span.figure.dig").removeClass("dig");
	    $(obj).addClass("dig");
}