/**
 * Created by 雪晴 on 2016/8/2.
 */
//显示各种订单类型
function droplist(){
    if($(".drop-box").css("display") == "none"){
        $(".drop-box").css("display","block");
    }else{
        $(".drop-box").css("display","none");
    }
}

//将选中的状态显示到上边
function chosen(el){
    $(".drop-box").css("display","none");
    $(".digitalorder").html($(el).html());
    $(el).siblings(".details.active").removeClass("active");
    $(el).addClass("active");
}

function afteran(){
//切换页数的颜色
$(".page .sum").click(function (e) {
    $(this).siblings(".sum.active").removeClass("active");
    $(this).addClass("active");
});

//选中
$(".graph .case").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active");
    }else{
        $(this).addClass("active");
    }
});

//全选
$(".numa .all").click(function(){
    if($(".message .case").length == $(".message .case.active").length){
        $(".case.active").removeClass("active");
        $(".all").removeClass("active");
    }else{
        $(".case").addClass("active");
        $(".all").addClass("active");
    }
});

//全部通过
$(".through").click(function(){
    $(".case.active").parents(".group").find(".adopted").addClass("active checkok");
    $(".case.active").parents(".group").find(".adopted").siblings(".refer").removeClass("active nocheckok");

});

//全部未通过
$(".failed").click(function(){
    $(".case.active").parents(".group").find(".refer").addClass("active nocheckok");
    $(".case.active").parents(".group").find(".refer").siblings(".adopted").removeClass("active checkok");
});

//通过按钮
$(".state .adopted").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active checkok");
    }else{
        $(this).addClass("active checkok");
        $(this).siblings(".refer").removeClass("active nocheckok");
    }
});

//未通过
$(".state .refer").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active nocheckok");
    }else{
        $(this).addClass("active nocheckok");
        $(this).siblings(".adopted").removeClass("active checkok");
    }
});
}