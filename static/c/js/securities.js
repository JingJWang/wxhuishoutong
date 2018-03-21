$(".compile").html("1.数码回收成交金额大于1000时可以使用优惠券\n2.选择也大于1000的回收商时提示是否使用优惠券选完后优惠券后预支付的钱只是本金\n3.回收价1000元+50元优惠券本次成交获得1050元余额里面直接激活那么多钱")

//添加优惠券
$("#append").click(function(){
    $(".stockList , .useRule").hide();
    $(".interpolate").show();
    //添加优惠券里面的保存
    $(".hold").click(function(){
        var price = $("#price").val();
        var qx = $("#power").val();
        var xz = $("#term").val();
        var sd = $("#start").val();
        var ed = $("#end").val();
        var fw = $("#range").val();
        var bz = $("#remark").val();
        if( (price != "") && (qx != "") && (xz != "") && (sd != "") && (ed != "") && (fw != "") && (bz != "") ){
            var html = '<div class="dimension">'+
                '<div class="worth fl">'+ price + '</div>'+
                '<div class="limit fl">'+ qx + '</div>'+
                '<div class="term fl">'+ xz + '</div>'+
                '<div class="stime fl">'+ sd + '</div>'+
                '<div class="etime fl">'+ ed + '</div>'+
                '<div class="range fl">'+ fw + '</div>'+
                '<div class="remark fl">'+ bz + '</div>'+
                '</div>';

            $(".interpolate , .useRule").hide();
            $(".stockList").show();
            $(".stockList .listing").append(html);
        }else{
            alert("信息填写不完整");
        }

    })

});

//回收增值劵
$(".motif").click(function(){
    $(".useRule , .interpolate").hide();
    $(".stockList").show();
});


//编辑优惠券规则
$("#compile").click(function(){
    $(".stockList , .interpolate").hide();
    $(".useRule").show();
    //保存按钮
    $(".conserve").click(function(){
        $(".useRule , .interpolate").hide();
        $(".stockList").show();
    })
});
