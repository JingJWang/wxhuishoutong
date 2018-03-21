//获取买流量订单信息
function getflowinfo(id){
	if(id == '' || isNaN(id)){
		UrlGoto('/view/shop/list.html');
	}
	var u ='/index.php/shop/flowgood/orderinfo';
	var d ='id='+id+'&num=1';
    var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$('.newAddCon .clearfix .noInput').val(response['data']['usermobile']);
		}
        var addgood = '<div class="goodsInfo"><div class="con"><dl class="clearfix"><dt class="fl"><img src="'+response['data']['goodinfo']['imgs']+'" width="82px" height="65px"/></dt><dd class="fl"><h3 class="title" style="height:36px;line-height:36px;">'+response['data']['goodinfo']['name']+'</h3><div class="price"><span class="money">￥<span class="num">'+(response['data']['goodinfo']['ppri']/100)+'</span></span><span class="flour">+'+response['data']['goodinfo']['integral']+' 通花</span></div></dd></dl></div><div class="number sum clearfix"><h3 class="tit fl">商品合计</h3><p class="conP fr"><span class="orange">'+(response['data']['goodinfo']['allPpri']/100)+'元</span><span class="orange">+ '+(response['data']['goodinfo']['allIntegral'])+' 通花</span></p></div></div>';
        $('.newAddCon').after(addgood);
    }
	AjaxRequest(u,d,f);
}
//提交流量订单
function getfloworder(){
	if(id == '' || isNaN(id)){
		UrlGoto('/view/shop/list.html');
	}
	var phone = checkPhone();
	if (phone==false) {
		return false;
	};
	var mobile = $(".noInput").val();
	var priceNum = $('.goodsInfo .price .money .num').html();
	if (priceNum>=16000) {
		alert("此价格属大额支付，请联系客服帮您支付：400-641-5080");
		return;
	};
	var ua = window.navigator.userAgent.toLowerCase();
	var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
	if ((priceNum==undefined||priceNum<=3000)&&iswx==1) {
		var u ='/index.php/shop/integral/HgetOrder';
		var d ='id='+id+'&limit=1&phone='+mobile+'&prid=0';
	    var f =function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				$("body").append(response['data']);
				callpay();
			}else{
				alert(response['msg']);
				UrlGoto(response['url']);
			}
	    }
		AjaxRequest(u,d,f);
	}else{
		$('#addressid').remove();
		$('#address').remove();
		$('#goodsid').val(id);
		$('#name').val($('.orderAddress .con .name').html());
		$('#prid').val('0');
		$('#limit').val('1');
		$("#mobile").val(mobile);
		$('#paytype').val($('.chosen .active').attr('type'));
		$('#theform').submit();
	}
}
//获取热销商品
function hotshop(){
	var u = '/index.php/shop/flowgood/hotshop';
	var d ='';
    var f =function(res){
		var response=eval(res);
		if (response['status'] == request_succ) {
			var list = '<div class="motif">热门商品</div><div style="padding:0px 20px;height:auto;">';
			$.each(response['data'], function(i, v) {
				list += '<a class="apellation" href="/view/shop/info.html?id='+v['id']+'">'+v['name']+'</a>';
			});
			list += '</div>';
			$('.item').html(list);
		};
	}
	AjaxRequest(u,d,f);
}
//搜索商品
function searchshop(){
	var text = $('.entry').val();
	if (text == '') {
		return false;
	};
	var u = '/index.php/shop/flowgood/seachshop';
	var d = 'text='+text;
    var f =function(res){
    	var response=eval(res);
    	var list = '';
		if(response['status'] == request_succ){
			$.each(response['data'], function(i, v) {
				list += '<a class="place" href="/view/shop/info.html?id='+v['id']+'">'
                        +'<div class="print">'
                            +'<img src="'+v['img']+'"/>'
                        +'</div>'
                        +'<div class="instruction">'
                            +'<div class="insider">'+v['name']+'</div>'
                            +'<div class="price">'
                                +'<span style="color:#F85E26">￥'+(v['ppri']/100)+'</span>'
                                +'<span>+</span>'
                                +'<span style="color:#F85E26">'+v['integral']+'</span>'
                                +'<span>通花</span>'
                            +'</div>'
                            +'<div class="volume">'
                                +'<span>销量 :</span>'
                                +'<span style="padding-left: 5px;">'+v['selln']+'</span>'
                            +'</div>'
                        +'</div>'
                    +'</a>';
			});
            $('.resemble').html(list);
		}else{
			if (response['msg']!='') {
				alert(response['msg']);
			};
			if (response['url']!='') {
		        UrlGoto(response['url']);
			};
		}
    }
	AjaxRequest(u,d,f);
}

var turnnumber = 0;
var id;
function checkorder(){
	if (turnnumber<10) {
		var number = $('.ordernumber').html();
		var u = '/index.php/shop/flowgood/checkorder';
		var d ='number='+number;
    	var f =function(res){
			var response=eval(res);
			if (response['status'] == request_succ) {
				UrlGoto('/view/shop/details.html?id='+response['data']['id']);
				clearInterval(check);
			};
			id = response['data']['id'];
		}
		AjaxRequest(u,d,f);
	}else{
		// $('.defray').after('<div class="isAdd" style="margin-top:20px"><a class="greenBtn" onclick="sureoder()">点击刷新订单</a></div>');
		clearInterval(check);
	}
	turnnumber++;
}

function sureoder(){
	var number = $('.ordernumber').html();
	var u = '/index.php/shop/flowgood/checkorder';
	var d ='number='+number;
    var f =function(res){
		var response=eval(res);
		if (response['status'] == request_succ) {
			UrlGoto('/view/shop/details.html?id='+response['data']['id']);
			clearInterval(check);
		}else{
			alert("您的订单还未处理成功！");
		}
	}
	AjaxRequest(u,d,f);
}

function getProGoods(){
	var host = window.location.pathname;
	var addfrom = '';
	var sfrom = '';
	if (from != 'undefined'&&from!=null) {
	    addfrom = '&from='+from;
		sfrom = '&spreadFrom='+from;
	};
	var u = '/index.php/shop/flowgood/getProGoods';
	var d ='fromUrl='+host+sfrom;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			AappId = response.data.signPackage.appId;
			Atimestamp = response.data.signPackage.timestamp;
			AnonceStr = response.data.signPackage.nonceStr;
			Asignature = response.data.signPackage.signature;
			window.shareData.timgUrl = '';
			window.shareData.tContent = '';
			window.shareData.timeLineLink = '';
			window.shareData.tTitle = '';

			var list = leftlist = rightlist ='';
			$.each(response['data']['goods'], function(i, v) {
				list = '<a class="shopDetail" href="/view/shop/info.html?id='+v['id']+addfrom+'">'
                			+'<div class="contain" align="center">'
                			    +'<div class="trade-name">'
                			        +'<span>'+v['name']+'</span>'
                			    +'</div>'
                			    +'<div class="print">'
                			        +'<div class="pic">'
                			            +'<img src="'+v['img']+'"/>'
                			        +'</div>'
                			    +'</div>'
                			    +'<div class="rates">'
                			        +'<div class="price fl">'
                			            +'<span>原价</span>'
                			            +'<span>￥'+(v['opri']/100)+'</span>'
                			            +'<div class="line"></div>'
                			        +'</div>'
                			        +'<div class="price fr">'
                			            +'<span>现价</span>'
                			            +'<span>￥'+(v['ppri']/100)+'</span>'
                			        +'</div>'
                			    +'</div>'
                			+'</div>'
            			+'</a>';
            	if (v['tid']==7) {
            		leftlist += list;
            	}else if(v['tid']==10){
            		rightlist += list;
            	}
			});
			$('.detail .shopList .dimension .phones').html(leftlist);
			$('.detail .shopList .dimension .luxury').html(rightlist);
		}else{
			$('.detail .shopList .dimension').html('没有商品了！');
		}
	}
	AjaxRequest(u,d,f);
}
function upUrl(from){
	if (from == 'undefined'||from==null) {
	    return ;
	};
	$('.mostly .handle .fl').attr('href', 'http://wx.recytl.com/index.php/nonstandard/submitorder/ViewBrand?id=5&from='+from);
	var hre = $('.recover').attr('href');
	$('.recover').attr('href',hre+'?from='+from);
}

function upmUrl(from){
	if (from == 'undefined'||from==null) {
	    return ;
	};
	$('.mostly .handle .fl').attr('href', '/view/shop/list.html?type=5&from='+from);
	$('.mostly .handle .fr').attr('href', '/view/shop/list.html?type=10&from='+from);
	var hre = $('.recover').attr('href');
	$('.recover').attr('href',hre+'?from='+from);
	$('.operate .master .fl').attr('href', '/view/shop/list.html?type=5&from='+from);
	$('.operate .master .fr').attr('href', '/view/shop/list.html?type=10&from='+from);
}