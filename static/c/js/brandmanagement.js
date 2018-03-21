/**
 * Created by Administrator on 2016/6/19 0019.
 */
//修改版本点击时的样式
$('#productType li').click(function (e) {
    $(this).siblings('li.active').removeClass("active");
    $(this).addClass("active");
});


//品牌里的操作
var lastValue = '';
var lastValue1 = "";
//点击修改
function revise(el){
    $(el).parents(".breed").find(".caddy").addClass("active");
    $(el).parents(".breed").find(".chart").css("display","block");
    $(el).parents(".breed").find(".field").removeAttr("readonly");
    lastValue = $(el).parents(".breed").find(".field").val();
    lastValue1 = $(".breed .item .caddy .bong .field.pag1").val();
}

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
//型号里的操作

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

//删除
function strike(el){
    $(el).parents(".breed").css("display","none");
}

//添加品牌
function addbrand(){
    $(".shadow").css("display","block");
    $(".gain").css("display","block");
}

//添加型号
function addmodel(){
    $(".shadow").css("display","block");
    $(".eject").css("display","block");
}

//关闭型号弹框
function reveal(){
    $(".shadow").css("display","none");
    $(".eject").css("display","none");
}

//关闭品牌弹框
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