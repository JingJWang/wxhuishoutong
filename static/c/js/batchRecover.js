function sysTitle(obj){
    $(obj).addClass('current').siblings().removeClass('current');
}

function title01(){
    $("#noQuote").show();
    $("#hasQuote").hide();
}

function title02(){
    $("#noQuote").hide();
    $("#hasQuote").show();
}

//切换页数的颜色
$(".page a.figure").click(function (e) {
    $(this).siblings("a.figure.dig").removeClass("dig");
    $(this).addClass("dig");
});

//报价按钮
$(".quote").click(function(){
    $(this).parents(".nese").addClass("active");
    $(".shade , .frame").show();
    $(".close-btn ,.abolish").click(function(){
        $(".shade , .frame").hide();
    });
    $(".affirm").click(function(){
        var price = $(".money").val();
        if(price == ""){
            alert("请输入报价金额");
        }else{
            $(".shade , .frame").hide();
            $(".nese.active").hide();
        }
    })
});
