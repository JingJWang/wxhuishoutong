/**
 * Created by Administrator on 2016/6/19 0019.
 */
//修改版本点击时的样式
$('#productType li').click(function (e) {
    $(this).siblings('li.active').removeClass("active");
    $(this).addClass("active");
});
//回收奖金设置-添加信息-弹框radio选中执行
function radioCli(){		//物品编号或区间
	var radios = document.getElementsByName("radionews");	
	for(var i=0;i<radios.length;i++){
	 	if(radios[1].checked){
	 		$('.modelMqu').eq(1).show().siblings().hide();
	 		$('.orderInpa').attr('id','');
	 		$('.orderInpb').attr('id','');
	 		$('.ordera').html('请选择');
	 	};
	 	if(radios[0].checked){
	 		$('.modelMqu').eq(0).show().siblings().hide();
	 		$('.orderInpa').attr('id','orderInpa');
	 		$('.orderInpb').attr('id','orderInpb');
	 		$('.orderInpa,.orderInpb').val('');
	 	}
	};
};
function radioClick(){		//物品信息值
	var radios = document.getElementsByName("radiomoney");	
	for(var i=0;i<radios.length;i++){
	 	if(radios[i].checked){
	 		$('.modelMqub').eq(i).show().siblings().hide();
	 		$('.modelMqub input').eq(i).attr('id','ordervalue').parent().siblings().find('.gubiNum').attr('id','');
	 	};
	};
};
//商城奖金设置修改
function radioChangex(){
	 var radios = document.getElementsByName("radiobutton");	
	 for(var i=0;i<radios.length;i++){
	 	if(radios[i].checked){
	 		$('.radionum').eq(i).show().siblings().hide();
	 	};
	 };
};
//商城奖金设置添加
function radioChange(){
	var radios = document.getElementsByName("type");
	for(var i=0;i<radios.length;i++){
	 	if(radios[i].checked){
	 		$('.radioNum').eq(i).show().siblings().hide();
	 		$('.radioNum').eq(i).find('input').attr('id','addvalue').parent().siblings().find('.radioNumM').attr('id','');
	 		$('.radioNum').eq(i).siblings().find('input').val('');
	 	};
	};	
};
//品牌里的操作
var lastValue = '';
var lastValue1 = "";

//取消按钮
function cancle(el){
    $(el).parents(".breed").find(".caddy").removeClass("active");
    $(el).parents(".breed").find(".chart").css("display","none");
    $(el).parents(".breed").find(".field").attr("readonly","readonly");
    $(el).parents(".breed").find(".field").val(lastValue);
    $(".breed .item .caddy .bong .field.pag1").val(lastValue1);
}
//保存
function conserve(el){
    $(el).parents(".breed").find(".caddy").removeClass("active");
    $(el).parents(".breed").find(".chart").css("display","none");
    $(el).parents(".breed").find(".field").attr("readonly","readonly");
}
//选中
function chosen(el){
    if($(el).hasClass("active")){
        $(el).removeClass("active");
        $(el).parents(".breed").find("input").removeClass("sign");
    }else{
        $(el).addClass("active");
        $(el).parents(".breed").find("input").addClass("sign");
    }
}
//全选
function checkall(){
    $(".embody .breed .icon .graph").addClass("active");
    $(".embody .breed .item .field").addClass("sign");
}
function checkInverse(){
    $(".embody .breed .icon .graph.active").removeClass("active");
    $(".embody .breed .item .field.sign").removeClass("sign");
}
//点击修改
function revise1(el){
    $(el).parents(".breed").find(".caddy").addClass("active");
    $(el).parents(".breed").find(".chart").css("display","block");
    $(el).parents(".breed").find(".field.pag1").removeAttr("readonly");
    lastValue1 = $(el).parents(".breed").find(".field.pag1").val();
    //$(".breed .item .caddy .bong .field.pag1").removeAttr("readonly");
    //lastValue1 = $(".breed .item .caddy .bong .field.pag1").val();
}

//取消按钮
function cancle1(el){
    $(el).parents(".breed").find(".caddy").removeClass("active");
    $(el).parents(".breed").find(".chart").css("display","none");
    $(el).parents(".breed").find(".field.pag1").attr("readonly","readonly");
    $(el).parents(".breed").find(".field.pag1").val(lastValue1);
    //$(".breed .item .caddy").removeClass("active");
    //$(".breed .item .caddy .chart").css("display","none");
    //$(".breed .item .caddy .bong .field.pag1").attr("readonly","readonly");
    //$(".breed .item .caddy .bong .field.pag1").val(lastValue1);
}

//保存
function conserve1(el){
    $(el).parents(".breed").find(".caddy").removeClass("active");
    $(el).parents(".breed").find(".chart").css("display","none");
    $(el).parents(".breed").find(".field.pag1").attr("readonly","readonly");
    //$(".breed .item .caddy").removeClass("active");
    //$(".breed .item .caddy .chart").css("display","none");
    //$(".item .caddy .bong .field.pag1").attr("readonly","readonly");
}

//选中
function chosen1(el){
    if($(el).hasClass("active")){
        $(el).removeClass("active");
    }else{
        $(el).addClass("active");
    }
}

//全选
function checkall1(){
    $(".comprise .breed .icon .graph").addClass("active");
}
function checkInverse1(){
    $(".comprise .breed .icon .graph.active").removeClass("active");

}
//切换页数的颜色
function pageBj(obj){
	$(obj).siblings(".fewpage.dig").removeClass("dig");
    $(obj).addClass("dig");
}
//批量删除
function batch(){
    $(".graph.active").parents(".breed").css("display","none");
}
//商城奖金删除-二次确认
function delFun(){
	$(".shadow").css("display","block");
    $('.delBox').css("display","block");
}
//二次确认--确定删除
function delBtnSure(el){
	$(".shadow").css("display","none");
    $('.delBox').css("display","none");
    //delCont = $(el).parents().find(".breed").remove();
}
//删除二次确认-取消删除
function delBtnNo(){
	$(".shadow").css("display","none");
    $('.delBox').css("display","none");
}
//商城-添加品牌
function addbrand(){
    $(".shadow").css("display","block");
    $(".addGain").css("display","block");
}
function closeAddGain(){
	$(".shadow").css("display","none");
    $(".addGain").css("display","none");
}
function closeEditGain(){
	$(".shadow").css("display","none");
	$(".amendGain").css("display","none");
	
}
//点击确认修改关闭弹框
function changeNews(){
	$(".shadow").css("display","none");
    $(".amendGain").css("display","none");
};
//回收奖金 添加
function addHuishou(){
    $(".shadow").css("display","block");
    $(".addeject").css("display","block");
};
//回收奖金添加-关闭弹框
function closeAddHuis(){
    $(".shadow").css("display","none");
    $(".addeject").css("display","none");
};
//回收奖金修改-关闭弹框
function closeEditHuis(){
    $(".shadow").css("display","none");
    $(".changeject").css("display","none");
};
//关闭型号弹框
function reveal(){
    $(".shadow").css("display","none");
    $(".eject").css("display","none");
}
//关闭弹框
function eyebrow(){
    $(".shadow").css("display","none");
    $(".gain").css("display","none");
}
//点击切换成型号页面
function navigate(){
    $(".embody").css("display","none");
    $(".comprise").css("display","block");
    $("#brandpage").css('display','none');
    $("#typepage").css('display','block');
    var id = $("#brandselect").val();
    $("#brand_"+id).attr('selected','selected');
}
//点击切换成品牌页面
function toggle(){
    $(".comprise").css("display","none");
    $(".embody").css("display","block");
    $("#typepage").css('display','none');
    $("#brandpage").css('display','block');
    seelctSave();
}
//select选择框记住默认选择
function seelctSave(){
	var id=$('#brandType option:selected').attr('data-id');
	$("#brandselect").val(id);
}