/**
 * Created by Administrator on 2016/7/21 0021.
 */
 function voteclick(){
    $(".group").click(function(){
        if( $(this).hasClass("active") ){
            $(this).removeClass("active on");
            $(this).parents(".casket").find(".square").removeClass("active");
            $(this).parents(".casket").find(".sn").removeClass("active");
        }else{
            $(this).addClass("active on");
            $(this).parents(".casket").find(".square").addClass("active");
            $(this).parents(".casket").find(".sn").addClass("active");
            $(this).parents(".casket").siblings().find(".group.active").removeClass("active on");
            $(this).parents(".casket").siblings().find(".square").removeClass("active");
            $(this).parents(".casket").siblings().find(".sn").removeClass("active");
        }
    });
}

$(".lump").click(function(){
    if( $(this).hasClass("active") ){
        $(this).siblings().removeClass("active");
    }else{
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
    }
});

$(".women").click(function(){
    if( $(this).hasClass("active") ){
        $(this).removeClass("active");
        $(this).parents(".case").siblings().find(".women").removeClass("active");

    }else{
        $(this).addClass("active");
        $(this).parents(".case").siblings().find(".women").removeClass("active");

    }
});


function rwdSucClose(){
   $('.rwdSuccess').slideUp(200);
}