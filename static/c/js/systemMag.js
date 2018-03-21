//日期控件
	var start = {
		elem: '#start',
		format: 'YYYY-MM-DD',
		max: '2099-06-16', //最大日期
		istime: true,
		istoday: true,
	};
	laydate(start);

	var start1 = {
		elem: '#start1',
		format: 'YYYY-MM-DD',
		max: '2099-06-16', //最大日期
		istime: true,
		istoday: true,
	};
	laydate(start1);

	var start2 = {
		elem: '#start2',
		format: 'YYYY-MM-DD',
		max: '2099-06-16', //最大日期
		istime: true,
		istoday: true,
	};
	laydate(start2);
	
function sysTitle(obj){
	$(obj).addClass('current').siblings().removeClass('current');
}

function title01(){
	$(".volumes").css("display","block");
	$(".trades").css("display","none");
	$(".casket").css("display","block");
	$(".acceptable").css("display","none");
	$(".acceptable-first").css("display","none");
}

function title02(){
	$(".volumes").css("display","none");
	$(".trades").css("display","block");
	$(".whole").css("display","block");
	$(".appenddata").css("display","none");
	$(".figures .page").css("display","block");
	$(".raise .name").css("display","block");
	$(".raise .notes").css("display","none");

	$(".figures .data").css("display","none");
	$(".figures .owned").css("display","block");
	$(".nese").css("display","block");
	$(".whole .datalist").css("display","block");
	$(".whole .correct").css("display","none");
	$(".include").css("display","block");


}

//成交数据管理切换页数的状态
$(".casket .embody .number").click(function (e) {
	$(this).siblings(".number.active").removeClass("active");
	$(this).addClass("active");
});

//添加数据
$(".add-data").click(function(){
	$(".casket").css("display","none");
	$(".acceptable-first").css("display","block");
	$(".acceptable").css("display","none");
});

//点击修改按钮
function display_up(id){
	$(".casket").css("display","none");
	var sj = $(this).parents(".protect").find(".timedata").html();
	var ds = $(this).parents(".protect").find(".further .saith").html();
	var je = $(this).parents(".protect").find(".further .sum").html();
	$(".acceptable-first").css("display","none");
	$(".acceptable").css("display","block");
	$(".entry.time").val(sj);
	$(".entry.sum").val(ds);
	$(".entry.money").val(je);
	$(".refer .submit").attr('onclick','upVolume('+id+')');
}
//成交数据管理里的删除弹框
function displayVolume(id){
	$(this).parents(".protect").addClass("active");
	$(".shadow").css("display","block");
	$(".frame").css("display","block");
	$(".confirm").attr("onclick",'delVolume('+id+')')
}
//确定删除
function del(){
	$(".protect.active").css("display","none");
	$(".shadow").css("display","none");
	$(".frame").css("display","none");
}
//取消删除
function forgo(){
	$(".shadow").css("display","none");
	$(".frame").css("display","none");
	$(".protect.active").removeClass("active");
}


//交易记录管理里的修改
function displayUpRecord(obj) {
	$(obj).parents(".nese").find(".data").css("display","block");
	$(obj).parents(".nese").find(".owned").css("display","none");
	$(obj).parents(".nese").siblings().css("display","none");
	$(".whole .datalist").css("display","none");
	$(".whole .correct").css("display","block");
	$(obj).parents(".nese").find(".stuff").removeClass("active");
	$(obj).parents(".nese").find(".stuff input").removeAttr("readonly");
	$(obj).parents(".nese").find(".stuff input").addClass("active");
	$(".raise .name").css("display","none");
	$(".raise .notes").css("display","block");
	$(".include").css("display","block");
	$(".figures .page").css("display","none");
}



//进到修改页面，点击交易详情返回
function fase(){
	$(".figures .data").css("display","none");
	$(".figures .owned").css("display","block");
	$(".nese").css("display","block");
	$(".whole .datalist").css("display","block");
	$(".whole .correct").css("display","none");
	$(".nese .stuff input").attr("readonly","readonly");
	$(".nese .stuff input").removeClass("active");
	$(".raise .name").css("display","block");
	$(".raise .notes").css("display","none");
	$(".include").css("display","block");
	$(".figures .page").css("display","block");
}


/*//查看
$(".prot .look").click(function(){
	$(".nese .stuff").addClass("active");
	$(this).parents(".nese").find(".data").css("display","block");
	$(this).parents(".nese").find(".owned").css("display","none");
	$(this).parents(".nese").siblings().css("display","none");
	$(".whole .datalist").css("display","none");
	$(".whole .correct").css("display","block");
	$(".raise .name").css("display","none");
	$(".raise .notes").css("display","block");
	$(".include").css("display","none");
	$(".figures .page").css("display","none");
});*/

function displaySesRecord(obj) {
		$(".nese .stuff").addClass("active");
		$(obj).parents(".nese").find(".data").css("display","block");
		$(obj).parents(".nese").find(".owned").css("display","none");
		$(obj).parents(".nese").siblings().css("display","none");
		$(".whole .datalist").css("display","none");
		$(".whole .correct").css("display","block");
		$(".raise .name").css("display","none");
		$(".raise .notes").css("display","block");
		$(".include").css("display","none");
		$(".figures .page").css("display","none");
}

//添加数据
function tagged(){
	$(".whole").css("display","none");
	$(".appenddata").css("display","block");
}

//交易记录管理切换页数的状态
$(".figures .embody .number").click(function (e) {
	$(this).siblings(".number.active").removeClass("active");
	$(this).addClass("active");
});

//交易记录管理里的删除按钮
function displayDelRecord(obj,id){
	$(obj).parents(".nese").addClass("active");
	$(".shadow").css("display","block");
	$(".fraed").css("display","block");
	$("#delrecord").attr('onclick','delRecord('+id+')');
}

//取消删除
function away(){
	$(".nese.active").removeClass("active");
	$(".shadow").css("display","none");
	$(".fraed").css("display","none");
}
//确认删除
function sure(){
	$(".nese.active").css("display","none");
	$(".shadow").css("display","none");
	$(".fraed").css("display","none");
}

$(".an").change(function(){
	var curVal = parseInt($(this).val());
	if( (curVal + 3) >= 24 ){
		var rightVal = $(".am option[value='" + (Math.abs(curVal - 21)) + "']");
		$(rightVal)[0].selected = true;
	}else{
		var rightVal = $(".am option[value='" + ((curVal + 3)) + "']");
		$(rightVal)[0].selected = true;
	}

});

$(".am").change(function(){
	var curVal = parseInt($(this).val());
	if( (curVal - 3) <= -1 ){
		var rightVal = $(".an option[value='" + (curVal + 21) + "']");
		$(rightVal)[0].selected = true;
	}else{
		var rightVal = $(".an option[value='" + (curVal - 3) + "']");
		$(rightVal)[0].selected = true;
	}

});
