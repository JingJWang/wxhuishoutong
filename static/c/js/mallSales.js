function sysTitle(obj){
    $(obj).addClass('current').siblings().removeClass('current');
}

function title01(){
    $("#phoneSale , #shopSale").hide();
    $("#luxurySale").show();
}

function title02(){
    $("#luxurySale , #shopSale").hide();
    $("#phoneSale").show();
}

function title03(){
    $("#luxurySale , #phoneSale").hide();
    $("#shopSale").show();
}

//切换页数的颜色
$(".page span.figure").click(function (e) {
    $(this).siblings("span.figure.dig").removeClass("dig");
    $(this).addClass("dig");
});