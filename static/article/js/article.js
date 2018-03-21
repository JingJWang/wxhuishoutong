/**
 * Created by Administrator on 2016/6/15 0015.
 */
//点击关闭按钮弹出层消失
function thclose(){
    $(".shadow").css("display","none");
    $(".follow").css("display","none");
    $(".ceng").css("display","none");
}
function share(){
    var ua = window.navigator.userAgent.toLowerCase();
    var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
    if (iswx==1) {
        $(".shadow").css("display","block");
        $(".ceng").css("display","block");
    }else{
        $('.fuzhi').css('display', 'block');
    }
}
$('.fuzhi').click(function() {
    $('.fuzhi').css('display', 'none');
});
function closelo(){
    $(".shadow").css("display","none");
    $(".tips").css("display","none");
    $(".ceng").css("display","none");
}
//更改密码状态
function mima(){
    if($(".pane.don").attr("type") == "password"){
        $(".logo").addClass("active");
        $(".pane.don").attr("type","text");
    }else{
        $(".logo").removeClass("active");
        $(".pane.don").attr("type","password");
    }
}