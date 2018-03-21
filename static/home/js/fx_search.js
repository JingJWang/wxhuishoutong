function selectSearch(){
    //    选择提示框
    var oSchoose =$(".Schoose");
    var oSelectBox = $(".selectBox");
    var aP = $(".selectBox p");
    var oPinName = $(".Schoose .pinName")
    oSchoose.on("click",function(){
        oSelectBox.slideToggle();
    });
    aP.on("click",function(){
        oSelectBox.slideToggle();
        oPinName.text($(this).text());
       $ (".Schoose .pinName").attr('data-key',$(this).attr('data-key'));
    });

}

function TiSHiSearch(){
    //    搜索提示框
    var oInput = $(".inputbox");
    var oTishi = $(".TiShiBox");
    var aTip = $(".TiShiBox p");
    oInput.focus(function(){
        oTishi.slideDown();
    });
    oInput.blur(function(){
        oTishi.slideUp();
    });
    aTip.on("click",function(){
        oTishi.slideUp();
        oInput.attr("value",$(this).text());
    });
}