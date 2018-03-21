/**
 * Created by Administrator on 2016/6/16 0016.
 */
//切换订单状态的颜色
function statusBj(obj){
	    $(obj).siblings("span.pattern").removeClass("pattern");
	    $(obj).addClass("pattern");
}
//切换提交时间的颜色
$(".date span").click(function (e) {
    $(this).siblings("span.pattern").removeClass("pattern");
    $(this).addClass("pattern");
});
//切换页数的颜色
function orderPageBJ(obj){
	    $(obj).siblings("span.figure.dig").removeClass("dig");
	    $(obj).addClass("dig");
}
//判断订单状态，如果是待交易可以取消交易
/*function state(){
    var arr = $(".layer .status").length;
    for(var i = 0;i<arr;i++){
       if(($(".layer .status").eq(i).text()) == "待交易"){
           $(".layer .check .cancle").eq(i).css("display","block");
       }
    }
}*/
/*state();*/
//查看显示订单详情
function lookup(el){
    $(el).parents(".layer").addClass("active");
    $(".full").css("display","block");
    $(".detail").css("display","block");
}

//取消订单
function cancleorder(){
    $(".layer.active").removeClass("active");
    $(".full").css("display","block");
    $(".farme").css("display","block");
}

//订单详情里取消订单
function orderform(el){
    $(el).parents(".layer").addClass("active");
    $(".full").css("display","block");
    $(".frame").css("display","block");
    $(".detail").css("background-color","#EBECEC");
    $(".title-btn").css("background-color","#EBECEC");
}

//订单详情里的关闭按钮
function shut(){
    $(".full").css("display","none");
    $(".detail").css("display","none");
    $(".layer.active").removeClass("active");
}

//取消订单里的按钮
function forgo(){
    if($(".detail").css("display") == "block"){
        $(".frame").css("display","none");
        $(".detail").css("background-color","#ffffff");
        $(".layer.active").removeClass("active");
    }else{
        $(".frame").css("display","none");
        $(".full").css("display","none");
        $(".detail").css("display","none");
        $(".layer.active").css("display","none");
    }
}
//确认删除订单
function del(){
    $(".full").css("display","none");
    $(".frame").css("display","none");
    $(".detail").css("display","none");
    $(".layer.active").css("display","none");
}

/*//取消订单里的关闭和取消按钮
$(".reason .close-btn , .reason .abolish-btn").click(function(){
    $(".shadow , .reason").hide();
});


//取消订单里的确认按钮
$(".reason .sure-btn").click(function(){
    var res = $(".entry").val();
    if(res == ""){
        alert("请输入取消原因");
    }else{
        $(".shadow , .reason").hide();
    }
});

//报价订单里的取消关闭按钮
$(".frames .close-btn ,.frames .abolish").click(function(){
    $(".frames").hide();
});


$(".frames .affirm").click(function(){
    var price = $(".money").val();
    if(price == ""){
        alert("请输入报价金额");
    }else{
        $(".frames").hide();
        $(".nese.active").hide();
    }
});*/





