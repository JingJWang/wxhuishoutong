/* *
fsx  built in 2015.9.23
 */
//订单列表
function slide(){
        var  mySwiper = new Swiper ('.swiper-container', {
            direction: 'horizontal', //水平方向
            grabCursor : true, //指针形状
            freeMode : false,//自动粘合
            slidesPerView : 'auto',//按设置的宽高自由分配显示的个数
            preventClicks : false
        });
        var aSpan = $(".navBox .swiper-slide");
        aSpan.on("click",function(){
            $(this).addClass("active").siblings().removeClass("active");
        })
    };
    
//报价列表 BaojiaList.html  js
function BJ(){
    $aSpan = $(".BLhead span");
    $aImg = $(".BLhead img");

    $aSpan.each(function(){
        $(this).click(function(){
            var i = $(this).index();
            var aa=$aImg.eq(i).attr("src");
            var bb="/static/home/images/down2.png";
            if(aa == bb){
                $aImg.eq(i).attr("src","/static/home/images/up2.png");
            }else{
                $aImg.eq(i).attr("src","/static/home/images/down2.png");
            }

        })
    })
}
//取消交易 Cannel.html js 开始
function Cannel(){
    var $aP = $(".CselectBox p");

    $aP.each(function(){
        $(this).click(function(){
            $(this).addClass("active").siblings().removeClass("active");
        })

    })
}

//数码产品 digit.html js 开始
function Dxiala(){
    //    下拉菜单  js 开始
    var $aP = $(".fx_options p");
    var $fx_value = $(".fx_value");
    var $fx_btn = $(".fx_btn");
    var $fx_bg = $(".fx_bg");
    var $fx_options = $(".fx_options");
    var $selects = $(".selects");
    $(function(){$selects.each(function(){
        var $aP = $(this).find(".fx_options p");
        var $fx_value = $(this).find(".fx_value");
        var $fx_btn = $(this).find(".fx_btn");
        var $fx_bg = $(this).find(".fx_bg");
        var $fx_options = $(this).find(".fx_options");
        var $selects = $(this).find(".selects");

        function fxSlideBg(obj1,obj2,obj3,bgname){
            obj1.click(function(){
                obj3.stop().toggleClass(bgname);
                obj2.stop().slideToggle();

            })
        }
        fxSlideBg($fx_btn,$fx_options,$fx_bg,"bg2");
        function fxQuZhi(objs,obj0,obj1,obj2){
            objs.each(function(){
                $(this).click(function(){
                    obj0.text($(this).text());
//                obj1.attr("value",$(this).text());
                    obj2.slideUp();
                    $(this).addClass("selected").siblings().removeClass("selected");
                    obj0.css("color","#000")
                    obj1.removeClass("bg2").addClass("bg1");
                })
            })
        };
        fxQuZhi($aP,$fx_value,$fx_bg,$fx_options);
    })})
//    下拉菜单  js 结束
    //是否 选择  js  开始
    var $Bao = $("#Bao");
    var $Ture = $("#Ture");
    var $aBao = $("#Bao .choose");
    var $aTure = $("#Ture .choose");

    $aBao.each(function(){
        $(this).click(function(){
            $(this).addClass("active").siblings().removeClass("active");
        })
    })
    $aTure.each(function(){
        $(this).click(function(){
            $(this).addClass("active").siblings().removeClass("active");
        })
    })
    //是否 选择  js  结束
    var $Duo = $("#Duo");
    var $aDuo = $("#Duo div");

    $aDuo.each(function(){
        $(this).click(function(){
            $(this).toggleClass("active");
        })
    })
}
//我的物品 myGoods.html  js  开始
function MG(){
    var $aMGheader = $(".MGheader span");
    var $aMode = $(".MGcontent .mode");

    $aMGheader.each(function(){
        $(this).click(function(){
            var i = $(this).index();
            $(this).addClass("active11").siblings().removeClass("active11");
            $aMode.eq(i).addClass("active22").siblings().removeClass("active22");
        })

    })
}
//旧衣产品 OldCloth.html js 开始
function OC(){
    //    下拉菜单  js 开始
    var $aP = $(".fx_options p");
    var $fx_value = $(".fx_value");
    var $fx_btn = $(".fx_btn");
    var $fx_bg = $(".fx_bg");
    var $fx_options = $(".fx_options");
    var $selects = $(".selects");

    $(function(){$selects.each(function(){
        var $aP = $(this).find(".fx_options p");
        var $fx_value = $(this).find(".fx_value");
        var $fx_btn = $(this).find(".fx_btn");
        var $fx_bg = $(this).find(".fx_bg");
        var $fx_options = $(this).find(".fx_options");
        var $selects = $(this).find(".selects");

        function fxSlideBg(obj1,obj2,obj3,bgname){
            obj1.click(function(){
                obj3.stop().toggleClass(bgname);
                obj2.stop().slideToggle();

            })
        }
        fxSlideBg($fx_btn,$fx_options,$fx_bg,"bg2");
        function fxQuZhi(objs,obj0,obj1,obj2){
            objs.each(function(){
                $(this).click(function(){
                    obj0.text($(this).text());
//                obj1.attr("value",$(this).text());
                    obj2.slideUp();
                    $(this).addClass("selected").siblings().removeClass("selected");
                    obj0.css("color","#000")
                    obj1.removeClass("bg2").addClass("bg1");
                })
            })
        };
        fxQuZhi($aP,$fx_value,$fx_bg,$fx_options);
    })})
//    下拉菜单  js 结束
//是否 选择  js  开始
    var $Bao = $("#Bao");
    var $Ture = $("#Ture");
    var $aBao = $("#Bao .choose");
    var $aTure = $("#Ture .choose");

    $aBao.each(function(){
        $(this).click(function(){
            $(this).addClass("active").siblings().removeClass("active");
        })
    })
    $aTure.each(function(){
        $(this).click(function(){
            $(this).addClass("active").siblings().removeClass("active");
        })
    })
    //是否 选择  js  结束
    var $Duo = $("#Duo");
    var $aDuo = $("#Duo label");

    $aDuo.each(function(){
        $(this).click(function(){
            $(this).parent().toggleClass("active");
        })
    })
}

//个人资料 PerData.html js 开始
function PD(){
    //    下拉菜单  js 开始
    var $aP = $(".fx_options p");
    var $fx_value = $(".fx_value");
    var $fx_btn = $(".fx_btn");
    var $fx_bg = $(".fx_bg");
    var $fx_options = $(".fx_options");
    var $selects = $(".selects");

    $(function(){$selects.each(function(){
        var $aP = $(this).find(".fx_options p");
        var $fx_value = $(this).find(".fx_value");
        var $fx_btn = $(this).find(".fx_btn");
        var $fx_bg = $(this).find(".fx_bg");
        var $fx_options = $(this).find(".fx_options");
        var $option = $(this).find("input:not('.ss')");

        function fxSlideBg(obj1,obj2,obj3,bgname){
            obj1.click(function(){
                obj3.stop().toggleClass(bgname);
                obj2.stop().slideToggle();

            })
        }
        fxSlideBg($fx_btn,$fx_options,$fx_bg,"bg2");
        function fxQuZhi(objs,obj0,obj1,obj2){
            objs.each(function(){
                $(this).click(function(){
                    obj0.text($(this).text());
//                obj1.attr("value",$(this).text());
                    obj2.slideUp();
                    $(this).addClass("selected").siblings().removeClass("selected");
                    obj0.css("color","#000")
                    obj1.removeClass("bg2").addClass("bg1");
                })
            })
        };
        fxQuZhi($aP,$fx_value,$fx_bg,$fx_options);
    })})
    //    下拉菜单  js 结束
}
//手机品牌 phoneKinds.html js 开始
function PK(){
    var $aDiv = $(".boxLeft .swiper-slide");
    var $aMode = $(".mode");
    var aN = $(".n");
    aN.each(function(index){
        $(this).text(index+1);
    })
    $aDiv.each(function(){
        $(this).click(function(){
            var i = $(this).index()-1;
            $(this).addClass("active").siblings().removeClass("active");
            $aMode.eq(i).addClass("current").siblings().removeClass("current");
        })
    })
    var oKindTitle = $(".KindTitle");
    var oKinds = $(".kinds");
    var aP = $(".kinds p");
    var oImg =$(".jiaobiao img");
    var oKindName =$(".Kindname");
    oKindTitle.click(function(){
        oKinds.slideDown();
        oImg.attr("src","/static/home/images/up.png");
        aP.each(function(){
            $(this).click(function(){
                oKinds.slideUp();
                oKindName.text($(this).text());
                oImg.attr("src","/static/home/images/down.png");
            })
        })
    })
    var L = ($(".header").width()- oKindTitle.width())/2;
    var T = ($(".header").height()- oKindTitle.height())/2;
    oKindTitle.css({"left":L,"top":T,"display":"block"});
}

function qqq(){
        var aP = $("#kkk p");
        aP.each(function(){
            $(this).click(function(){
                var i = $(this).index();
                alert(i);
            })
        })
}
qqq();

function shenFen(){
    $("#sjld").sjld("#shenfen","#chengshi","#quyu");
    $(".m_zlxg").click(function(){
    	$(this).siblings(".m_zlxg").find(".m_zlxg2").slideUp();
        $(this).siblings(".m_zlxg").removeClass("bg4");
        $(this).find(".m_zlxg2").slideToggle();
        $(this).toggleClass("bg4");
    })

}

function mySwiperOC(){
    var  mySwiper = new Swiper ('.swiper-container', {
        direction: 'horizontal',
        grabCursor : true,
        freeMode : false,
        slidesPerView : 'auto',
        prevButton:'.swiper-button-prev',
        nextButton:'.swiper-button-next'
    });
}
function mySwiperPKA(){
    var H = $(document.body).height()-$(".header").height()-$(".TitLeft").height();
    $(".swiper-container1").css("height", H);
    var  mySwiper = new Swiper ('.swiper-container1', {
        direction: 'vertical',
        initialSlide :0,
        grabCursor : true,
        freeMode : false,
        slidesPerView : 'auto',
        observer:true,
        observeParents:true,
        preventClicks : false

    });
}
function mySwiperPKB(){
    var H = $(document.body).height()-$(".header").height()-$(".TitLeft").height()
    $(".swiper-container2 ").css("height", H);
    var  mySwiper = new Swiper ('.swiper-container2', {
        direction: 'vertical',
        grabCursor : true,
        initialSlide :0,
        freeMode : false,
        observer:true,
        observeParents:true,
        slidesPerView : 'auto',
        preventClicks : false,
        /*onReachEnd: function(swiper){
            alert('到头了');
        }*/
    });
}
