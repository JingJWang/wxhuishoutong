function sysTitle(obj){
    $(obj).addClass('current').siblings().removeClass('current');
}

function title01(){
    $("#luxuryRecover").show();
    $("#phoneRecover").hide();
}

function title02(){
    $("#luxuryRecover").hide();
    $("#phoneRecover").show();
}

//切换页数的颜色
$(".page span.figure").click(function (e) {
    $(this).siblings("span.figure.dig").removeClass("dig");
    $(this).addClass("dig");
});