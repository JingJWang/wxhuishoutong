//星星评分
function XingScore(){
    var oFen =$(".XingFen");
    var aLi = $(".ulList li");
    var iScore = 1;
    aLi.on("click",function(){
        var i = $(this).index();
        aLi.each(function(index){
            if(index<= i){
                aLi.eq(index).addClass("active");
            }else{
                aLi.eq(index).removeClass("active");
            }
        });
        sCore = (i+1)*iScore;
        oFen.text(sCore)
    })
};

//评价原因选择
function SlectReason(){
    var aSpan = $(".reasonBox span");
    aSpan.on("click",function(){
    	var style=$(this).attr('class');
        if(style == 'active'){
        	$(this).removeClass("active");
        }else{
        	$(this).addClass("active");
        }
    })
}