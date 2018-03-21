//全部提现
$(".distill").click(function(){
    var price = $("#balance").html();
    $(".feed").val(price);
});
//支付宝
$(".braised.alipay").click(function(){
    if($(this).hasClass("active")){
        $(this).removeClass("active");
        $(".information").hide();
    }else{
        $(this).addClass("active");
        $(".information").show();
    }
});
//点击提现-提现成功
$(".confirm").click(function(){
	var boxH = $(window).height();
	$(".show_box").css({"display":"block","height":boxH});
	$(".ws_Ok").click(function(){
		$(".show_box").css("display","none");
	})
});
