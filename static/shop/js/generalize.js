$(document).bind('scroll', function (e){
    var t = $("body").scrollTop();  //获取滚动距离
    var s = parseInt($('.handle').offset().top);
    if ( (s - t) < 2) {
        $(".full-bottom , .operate").show();
    } else {
        $(".full-bottom , .operate").hide();
    }
});
