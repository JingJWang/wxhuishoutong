var Gcontents =Array();

function Getgoods(code,id){
	var u ='/index.php/shop/integral/getList';
	var d ='code='+code+'&id='+id+'&type='+urltype;
	var f =function(res){
		var  response = eval(res);
		var types = Array();
		if(response['status'] == request_succ){
			AappId = response.data.signPackage.appId;
			Atimestamp = response.data.signPackage.timestamp;
			AnonceStr = response.data.signPackage.nonceStr;
			Asignature = response.data.signPackage.signature;
			window.shareData.timgUrl = 'http://wx.recytl.com/static/task/task_two/img/pic01.jpg';
			window.shareData.tContent = '【回收通】陪您过暑假，玩具礼包、女生礼包只需10元，还有更多超低秒杀哦';
			window.shareData.timeLineLink = response['data']['shareurl'];
			window.shareData.tTitle = '回收通-通花商城';
			sharetext();
			var list='';
			var tlist = '';
			var property = '';
			var viewsell = '';
			var prices = '',tong = '',pall = '',nclass='',over='',ismail = '',isj = '',sell = '';
			$.each(response['data']['list'],function(k,v){
                property = eval('(' + v['property']+')'); 
			    viewsell = '';
			    sell = '';
                over = v['num']<=0?'<div class="line"></div><div class="sold-out">已卖完</div>':'';//是否卖完
                if (property.viewsell==1) {
                	sell = '<span>销量 :</span><span style="padding-left: 5px;">'+v['selln']+'</span>'
                }
                viewsell = '<div class="sales">' +
							'<div class="arm">' +
								sell+
							'</div>'+
							over+
						'</div>';
                prices = v['ppri']>0?'<span class="rate">￥'+v['ppri']+'</span>':'';
                tong = v['integral']>0&&prices==''?'<span class="rate">'+v['integral']+'</span><span>通花</span>':'';
                pall = (prices!=''&&tong!='')?'<spam>+</spam>':'';
                ismail = property.m==1?'<div class="mail">包邮</div>':'';
                isj = property.j==1?'<div class="consign">寄售</div>':'';
				tlist = '<a class="dimension" onclick="tourl('+v['id']+')" href="javascript:void();">'
                                    +'<div class="graph">'
                                        +'<img src="'+v['img']+'"/>'
                                    +'</div>'
                                    +'<div class="solve">'
                                        +'<div class="above">'+v['name']+'</div>'
										+'<div class="welfare">'
											+ismail
											+isj
										+'</div>'
										+'<div class="prix">'
                                        	+prices+pall+tong
                                        +'</div>'
                                        +viewsell
                                    +'</div>'
                                +'</a>';
				if (property.type==1) {
					if (types[1]==undefined) {
						if (type==1) {nclass = 'sn'}else{nclass = ''};
		                types[1] = '<a href="javascript:;" class="rome '+nclass+'" data-id="1">热销商品</a>';
					};
					if (Gcontents[1]==undefined) {Gcontents[1] = tlist;}else{Gcontents[1] += tlist;}
				}else if(property.type==2){
					if (types[2]==undefined) {
						if (type==2) {nclass = 'sn'}else{nclass = ''};
		                types[2] = '<a href="javascript:;" class="rome '+nclass+'" data-id="2">国庆礼品</a>';
					};
					if (Gcontents[2]==undefined) {Gcontents[2] = tlist;}else{Gcontents[2] += tlist;}
				}
				if (types[v['sort']]==undefined) {
					if (type==v['sort']) {nclass = 'sn'}else{nclass = ''};
		    		types[v['sort']] = '<a href="javascript:;" class="rome '+nclass+'" data-id="'+v['sort']+'">'+v['tname']+'</a>';
				};
				if (Gcontents[v['sort']]==undefined) {Gcontents[v['sort']] = tlist;}else{Gcontents[v['sort']] += tlist;}
			});
			$.each(types,function(k,v){
				if (v==undefined) {return true;};
				list += types[k];
			})
			Gcontents[5] += '<div class="ons" align="center"><a class="words" href="/view/shop/digittype.html">没有您想要的商品？点击这里</a></div>';
			Gcontents[10] += '<div class="ons" align="center"><a class="words" href="https://jinshuju.net/f/nMT8fp">没有您想要的商品？点击这里</a></div>';
			$('.left').append(list);
			$("#inte").html(response['data']['intergral']);
			$(".right .val .na").html(Gcontents[type]);
			if (urltype!=undefined) {
				$('body,html').animate({'scrollTop': '400'}, 100);
				$('.right').scrollTop(postion);
			};
		}else{
			if (response['url']!='') {
				location.href = response['url'];
			};
		}
	}
	AjaxRequest(u,d,f);
}
function Getinfo(id){
	if(id == ''){
		UrlGoto('/view/shop/list.html');
	}
	var url=encodeURIComponent(location.href.split('#')[0]);
	var u ='/index.php/shop/integral/getInfo';
	var d ='id='+id+'&code='+getUrlParam('code')+'&url='+url;
	var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			var  banner='';
			var  toall='';
			var allneed = '';
			$.each(response['data']['0']['imgs'], function(key, val) {  
				banner =  banner + '<img src="'+val+'">';
				toall++;
			});
			var minprice = response['data']['0']['otprice']['0']['p'];
			var mininter = response['data']['0']['otprice']['0']['in'];
			var mincolor = response['data']['0']['otprice']['0']['color'];
			var pricelist ='';
			var	money = '';
			var intergral = '';
			var mincolor = '';
			var isfirst = '';
			$.each(response['data']['0']['otprice'], function(i, v) {
				if (v['p']<minprice&&v['p']>0) {minprice=v['p']};
				if (v['in']<mininter&&v['in']>0) {mininter=v['in']};
				if (v['p']>0) {money = '<div class="money">￥<span class="num">'+(v['p']/100)+'</div></span>';}else{money='';}
				if (v['in']>0) {intergral = '<div class="sum"><span>'+v['in']+'</span><span>通花</span></div>';}else{intergral='';}
				if (i==0) {isfirst='active';}else{isfirst='';}
				if(typeof(v['color']) != 'undefined'){ 
					var color='<div class="color" ><span>'+v['color']+'</span></div>';
				}else{
					var color='';
				}
				pricelist += '<div class="selection" style="cursor:pointer;">'
        						    +'<a class="dot '+isfirst+'" pid='+i+'></a>'
        						    +money+intergral+color+'</div>';
			});
			
			if (response['data']['reg']==false) {
				$('.shadow').css('display', 'block');
				$('.tips').css('display', 'block');
			};
			$('.ban').html(banner);
			if (parseInt(minprice)>parseInt(response['data']['0']['ppri'])) {minprice=response['data']['0']['ppri']};
			if (parseInt(mininter)>parseInt(response['data']['0']['integral'])) {minprice=response['data']['0']['integral']};
			if (minprice>0) {
				allneed+='&yen;'+(minprice/100)
			}else if (mininter>0) {
				allneed+='<span>'+mininter+'通花</span>'
			};
			$('.amount').html(pricelist);
			$('.commodity .details .picture img').attr('src', response['data']['0']['imgs']);
			$('.commodity .details .brief').html(response['data']['0']['name']);
			if(id>=944 && id<=947 ){
				allneed = "<span>优惠价</span>&nbsp;&nbsp;"+allneed;
			}
			$('#money').html(allneed);
			$('.conn').html(response['data']['0']['content']);
			if (response['data']['0']['fid']==0) {
				response['data']['0']['fid'] = response['data']['0']['tid'];
			};
			if(response['data']['0']['number'] < 1){
			        	$(".unBut").html('<button id="but-05">库存不足</button>');
			        	$(".footBut").html('<button style="margin:0 auto;" id="but-05">库存不足</button>');
			}else{
				if(id == '754'){
					$("head").append("<link>");
					css = $("head").children(":last");
					css.attr({
					    rel: "stylesheet",
					    type: "text/css",
					    href: "../../../static/shop/css/music.css"
					});
					$(".unBut").html('<button id="but-05" onclick="selectpricea();">立即订购</button>');
				    $(".footBut").html('<button style="margin:0 auto;" id="but-05" onclick="selectpricea();">立即订购</button>');
				    $('.commodity .submit').html('<a class="refer" onclick="suruprice('+response['data']['0']['id']+')" href="javascript:;">提交订单</a>');
				}else if(id >= 944 && id<= 947){
					$("head").append("<link>");
					css = $("head").children(":last");
					css.attr({
					    rel: "stylesheet",
					    type: "text/css",
					    href: "../../../static/shop/css/music.css"
					});
					$(".unBut").html('<button id="but-05" onclick="selectprice_zr();">立即订购</button>');
				    $(".footBut").html('<button style="margin:0 auto;" id="but-05"  onclick="selectprice_zr();">立即订购</button>');
				    $('.commodity .submit').html('<a class="refer" onclick="suruprice('+response['data']['0']['id']+')" href="javascript:;">提交订单</a>');
				}else{
					$(".unBut").html('<button id="but-05" onclick="selectprice();">立即订购</button>');
				    $(".footBut").html('<button style="margin:0 auto;" id="but-05"  onclick="selectprice();">立即订购</button>');
				    $('.commodity .submit').html('<a class="refer" onclick="suruprice('+response['data']['0']['id']+')" href="javascript:;">提交订单</a>');
				}
			        	
			}
			
		}
		if (response.data.signPackage!=null) {
			AappId = response.data.signPackage.appId;
			Atimestamp = response.data.signPackage.timestamp;
			AnonceStr = response.data.signPackage.nonceStr;
			Asignature = response.data.signPackage.signature;
			if (response['data']['0']['share']==null) {
			    window.shareData.tTitle = response['data']['0']['name'];
			    window.shareData.tContent = '【回收通】陪您过暑假，玩具礼包、女生礼包只需10元，还有更多超低秒杀哦';
			}else{
				if (response['data']['0']['share']['title']=='') {
			        window.shareData.tTitle = response['data']['0']['name'];
				}else{
					window.shareData.tTitle = response['data']['0']['share']['title'];
				}
				if (response['data']['0']['share']['des']=='') {
					window.shareData.tContent = '【回收通】陪您过暑假，玩具礼包、女生礼包只需10元，还有更多超低秒杀哦';
				}else{
					window.shareData.tContent = response['data']['0']['share']['des'];
				} 
			}
			window.shareData.timgUrl = response['data']['0']['imgs']['0'];
			window.shareData.timeLineLink = response['data']['shareurl'];
			sharetext(response['data']['0']['id']);
		}
	}
	AjaxRequest(u,d,f);
}
//音乐会购票
function goupiao(id,obj,price){
	var con = '<div class="musicBox">'
				+'<div class="music">'
				+'<p class="fontp">票价为：<font color="orangered"> '+price+'</font>元/张</p>'
				+'<div class="numP">'
					+'<p class="num">请选择要购买的数量：</p>'
					+'<div class="inp">'
						+'<p class="low numsize" onclick="addc(-1,'+price+')"></p>'
						+'<input type="text" class="number" id="adcNum" value="1" oninput="inpFun('+price+')" onfocus="cInpVal(this);" onblur="inpVal(this,2);" maxlength="3">'
						+'<p class="add numsize" onclick="addc(1,'+price+')"></p>'
					+'</div>'
					+'<p class="endNum">1张票</p>'
				+'</div>'
				+'<div class="allM">'
					+'<p>合计：</p>'
					+'<p class="payMoney">'+price+'</p>'
					+'<p>元</p>'
				+'</div>'
				+'<div class="btnBox">'
					+'<a href="javascript:;" class="sure btn" onclick="commitNum('+id+')">确定</a>'
					+'<a href="javascript:;" class="cancel btn"  onclick="cancelBox();">取消</a>'
				+'</div>'
				+'</div>'
			  +'</div>';
	$(obj).parent().append(con);
	var windowH = $(window).height();
	$('.musicBox').height(windowH);
	document.documentElement.style.overflow='hidden';
}
//音乐票 用输入值在value
function commitNum(id){
	var num=$('#adcNum').val();
	var u='/index.php/shop/integral/admStock';
	var d ='id='+id+'&num='+num;
	var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			location.href='http://www.wxmj.com/view/shop/orderInfo.html?id='+id+'&sprice=0&num='+parseInt(num);
		}else{
			alert("出票不足,请重新输入");
			return false;
		}
	 }
	AjaxRequest(u,d,f);
}
//猪肉专用
function zhurouNum(id){
	var num=$('#meatinp').val();
	var u='/index.php/shop/integral/admZhuStock';
	var d ='id='+id+'&num='+num;
	var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			location.href='http://www.wxmjj.com/view/shop/orderInfo.html?id='+id+'&sprice=0&num='+parseInt(num);
		}else{
			alert("库存不足");
			return false;
		}
	 }
	AjaxRequest(u,d,f);
}
//音乐会
function inpFun(price){
	var oneM =price;
	var test_value=$('.number').val();
	var patrn=/^([1-9]\d*|0)(\.\d*[1-9])?$/; 
	if (!patrn.exec(test_value)){
		$('.number').val(1);
	}else{
		var endMoney = oneM*test_value;
		$(".endNum").html(test_value+'张票');
		$('.payMoney').html(endMoney);
	}
}
//猪肉oninput事件
function inpFunM(){
	var price = $('#meatP').html();
	var test_value=$('.number').val();
	var patrn=/^([1-9]\d*|0)(\.\d*[1-9])?$/; 
	if (!patrn.exec(test_value)){
		$('#meatinp').val('');
	}else{
		var endMoney = price*test_value;
		$('#allmoney').html(endMoney);
		$('.endNum').html(test_value*2.5+'kg');
	}
}
//加减
function addc(type,price){
	var oneM = price;
	var weight='';
	weight = $('#adcNum').val();
	weight = Number(weight);
	if(type > 0){
		weight = weight + 1;
		$("#adcNum").val(weight);
		$(".endNum").html(weight+'张票');
		$('.payMoney').html(oneM*weight);
	}else {
		weight = weight - 1;
		if(weight < 1){
			weight = 1;
		}
		$("#adcNum").val(weight);
		$(".endNum").html(weight+'张票');
		$('.payMoney').html(oneM*weight);
	}
}
//猪肉-加减
function addmeat(obj,num){
	var inpNum = $(obj).parent().find('#meatinp').val();
	var price = $('#meatP').html();
	if(num ==  1){
		inpNum++;
	}else {
		if(inpNum == 1){
			inpNum == 1;
		}else{
			inpNum--;
		}
	}
	$(obj).parent().find('#meatinp').val(inpNum);
	$('.endNum').html(inpNum*2.5+'kg');
	$('#allmoney').html(inpNum*price);
	$(obj).parent().find('#meatinp').attr('data',inpNum);
}
//取消清除弹框
function cancelBox(){
	$('.musicBox').remove();
	document.documentElement.style.overflow='auto';
}
function closeShadow(){
	$('.musicBox').hide();
	$('body').css('overflow','auto');
}
function suruprice(id){
	var prid = $('.commodity .amount .selection .active').attr('pid');
	location.href='orderInfo.html?id='+id+'&sprice='+prid;
}
function getOrder(){
	if(id == '' || isNaN(id) || isNaN(sprice)){
		// UrlGoto('/view/shop/list.html');
	}
	var priceNum = $('.commodity .amount .selection .active').next('.money').find('.num').html();
	var paytype = $('.chosen .active').attr('type');
	var ua = window.navigator.userAgent.toLowerCase();
	var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
	if (priceNum>=16000&&paytype=='weixin') {
		alert("由于支付额度超出微信支付限制，请使用支付宝进行支付，或联系客服帮您支付：400-641-5080");
		return;
	};
	if ((priceNum==undefined||priceNum<=3000)&&iswx==1) {
		var u ='/index.php/shop/integral/HgetOrder';
		var d ='id='+id+'&limit=1&prid='+sprice+'&nums='+num;
		var f =function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				$("body").append(response['data']);
				callpay();
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
				UrlGoto(response['url']);
			}
		}
		AjaxRequest(u,d,f);
	}else{
		$('#goodsid').val(id);
		$('#prid').val(sprice);
		$('#limit').val('1');
		$('#paytype').val($('.chosen .active').attr('type'));
		$('#addressid').remove();//去除不用的input
		$('#name').remove();
		$('#mobile').remove();
		$('#address').remove();
		$('#theform').submit();
	}
}
function getRecord() {
	var id=getUrlParam('id');
	if(id == '' || isNaN(id)){
		UrlGoto('/view/shop/list.html');
	}
	var u ='/index.php/shop/integral/getRecord';
	var d ='id='+id;
	var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			if(response['data']['0']['goodsid'] =='646'){
				var hyContent = "<div class='hy'>"
						+"<div class='hyBox'>"
						+"<p class='hyP'>"+"<big>恭喜您成为回收通会员</big><br/>与回收通一起尊享收益一整年"+"</p>"
						+"<a href='/index.php/nonstandard/mybonus/mybonusList' class='seeSy'>查看收益</a>"
						+"</div>"
						+"</div>";
				$('.huiyBox').append(hyContent);
			}else{
				$('.huiyBox').css('display','none');
			}
			if (response['data']['isub']==false) {
				$('.shadow').css('display', 'block');
				$('.ceng').css('display', 'block');
			};
			$('.imgs').attr('src',response['data']['0']['img']);
			$('.title').html(response['data']['0']['name']);
			$('.money').html('￥'+response['data']['0']['price']/100);
			$('.flour').html('+'+response['data']['0']['aintegral']+'通花');
			$('.time .gray').html(formatDate(response['data']['0']['jointime']));
			if (response['data']['0']['aintegral']==0) {
			    $('.aflour').html('0通花');
			}else{
			    // var num = response['data']['0']['aintegral']/response['data']['0']['integral'];
			    $('.aflour').html('+'+response['data']['0']['aintegral']+'通花');
			}
			if (response['data']['0']['price']==0) {
			    $('.amoney').html('￥0');
			}else{
			    // var num = response['data']['0']['price']/response['data']['0']['ppri'];
				$('.amoney').html('￥'+response['data']['0']['price']/100);
			}
			$('.num').html('1');
			if (response['data']['0']['status']==1) {
                    $('.astatus').html('交易成功');
			};
			if (response['data']['0']['fid']==0) {
				response['data']['0']['fid'] = response['data']['0']['tid'];
			};
			if (response['data']['0']['fid']==4) {
			    if (response['data']['0']['status']==2) {
			    	$('.astatus').html('正在为您充值');
			    }
				var list = '<div class="expressInfo clearfix"><h3 class="tit fl">充值号码</h3>'+
				               '<p class="conP fr"><span class="gray">'+response['data']['0']['code'].substr(0,11)
				               +'</span><span class="gray"></span></p></div>';
				$('.state').after(list);
			}else if (response['data']['0']['fid']==2) {
			    if (response['data']['0']['status']==2) {
			    	$('.astatus').html('正在配送');
			    }
				if (response['data']['0']['code'] == '') {
					var list = '<div class="expressInfo clearfix"><h3 class="tit fl">配送信息</h3>'+
				               '<p class="conP fr"><span class="gray">正在为您发货</span></p></div>';
				}else{
					var info = response['data']['0']['code'].split(",");
				    var list = '<div class="expressInfo clearfix"><h3 class="tit fl">配送信息</h3>'+
				               '<p class="conP fr"><span class="gray">'+info['0']+
				               '</span><span class="gray">'+info['1']+'</span></p></div>';
				}
				$('.state').after(list);
			}else if(response['data']['0']['fid']==1){
				var list = '<div class="changeCode"><span>'+response['data']['0']['name']+
				           '兑换码:</span><p class="code">'+response['data']['0']['code']+
				           '</p><p class="term">有效期：<i>'+response['data']['0']['expire']+'</i></p></div>';
				if(response['data']['0']['id']=939){
					list +='<p style="text-align:center;height:30px;line-height:30px;"><a style="align:center" href="http://www.winnerbook.com.cn/mobile/ceo_user.php?act=pl_card_accept">点击兑换体验会员</a></p>';
				}
				$('.status').after(list)
			}
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);	
}
function getDetails(){
	var u='/index.php/shop/integral/getDetail';
	var d='';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			var content='';
			$.each(response['data'],function(k,v){
				content += '<div class="recordList">'+
								'<div class="con">'+
									'<a class="don" href="/view/shop/details.html?id='+v['id']+'">'+
										'<div class="picture">'+
											'<img src="'+v['img']+'"/>'+
										'</div>'+
										'<div class="reveal">' +
											'<div class="title">'+v['name']+'</div>' +
											'<div class="price">' +
												'<span class="money">￥'+v['pri']+'</span><span class="flour">+'+v['integral']+ ' 通花</span>'+
											'</div>'+
											'<div class="timer">'+formatDate(v['jointime'])+'</div>'+
										'</div>'+
									'</a>'+
								'</div>'+
							'</div>'
			});
			$(".record").html(content);
		}
		if(response['status'] == request_fall){
			$("body").html('<p style="text-align:center;margin-top:30%;">'+response['msg']+'</p>');
			if(response['url'] != ''){
				UrlGoto(response['url']);
			}
		}
	}	
	AjaxRequest(u,d,f);	
}
//时间转换  
function   formatDate(now)   {      
	var   now= new Date(now*1000);     
	var   year=now.getFullYear();     
	var   month=now.getMonth()+1;     
	var   date=now.getDate();     
	var   hour=now.getHours();      
	var   minute=now.getMinutes();     
	var   second=now.getSeconds();      
	return   year+"年"+fixZero(month,2)+"月"+fixZero(date,2)+"日    "+fixZero(hour,2)+":"+fixZero(minute,2)+":"+fixZero(second,2); 
}   
//时间如果为单位数补0  
function fixZero(num,length){     
	var str=""+num;      
	var len=str.length;     
	var s="";      
	for(var i=length;i-->len;){         
		s+="0";     
	}      
	return s+str; 
}
function Getshopinfo(id,num) {
	if(id == '' || isNaN(id)){
		UrlGoto('/view/shop/list.html');
	}
	if(adr==null || adr=='' || adr=='undefined'){
			adr=0;
	}
	if(num==null || num=='' || num=='undefined'){
			num=1;
	}
	var u ='/index.php/shop/realgood/orderinfo';
	var regist=$("#regist").val();
	var d ='id='+id+'&sprice='+sprice+'&adr='+adr+'&num='+num;
    var f =function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			if (response['data']['goodinfo']['fid']==2) {
				if(response['data']['address']=='noadress'){
	                var add = '<div class="noOrderAddress ifhasAddress letnext"><a href="/view/shop/addAddress.html?id='+id+'&sprice='+sprice+'&num='+num+'"><p><i></i><span>添加收货地址</span></p></a></div>';
	                $('.green').after(add);
				}else{
					selectadress = response['data']['address']['id'];
	                var mo = '';
	                if (response['data']['address']['status']==2) {
	                	mo = '<i class="default">默认</i>';
	                };
					var add = '<div class="orderAddress letnext"><a href="/view/shop/selectAddress.html?id='+id+'&sprice='+sprice+'&adr='+adr+'&num='+num+'" class="con"><div class="top"><span class="name">'+response['data']['address']['name']+'</span><span class="phoneNo">'+response['data']['address']['number']+'</span></div><div class="bot"><p>'+mo+'<span class="detail">'+response['data']['address']['city']+response['data']['address']['details']+'</span></p></div></a></div>';
	                $('.addnext').after(add);
				}
            	$('.subOrder').html('<a onclick="getrealOrder()">确认支付</a>');
            	$('#remarkf').css('display', 'block');
			}else if(response['data']['goodinfo']['fid']==4||response['data']['goodinfo']['tid']==4){
				var add = '<div class="newAddCon noOrderAddress" style="width:auto;">'
							+'<div class="name clearfix">'
								+'<p class="fl">手机号码<span></span></p>'
								+'<div class="fr text">'
									+'<input type="text" value="'+response['data']['usermobile']+'" class="input noInput" placeholder="请输入要充值的手机号码" onblur="checkPhone()"/>'
									+'<div id="addNum"></div>'
								+'</div>'
							+'</div>'
						+'</div>';
				$('.addnext').after(add);
            	$('.subOrder').html('<a onclick="getfloworder()">确认支付</a>');
			}else{
            	$('.subOrder').html('<a onclick="getOrder()">确认支付</a>');
			}
			var ua = window.navigator.userAgent.toLowerCase();
			var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
			if (iswx==1) {
				if (response['data']['goodinfo']['ppri']>0) {
					$('#weixin').css('display', 'block');
				};
			}else if(response['data']['goodinfo']['ppri']>0){
				$('#weixin').css('display', 'block');
				$('#zhifubao').css('display', 'block');
			}
			if(id>=755 && id<=761){
				var con='';
				var addgood = '<div class="goodsInfo" style=""><div class="con"><dl class="clearfix"><dt class="fl"><img src="'+response['data']['goodinfo']['imgs']+'" width="82px" height="65px"/></dt><dd class="fl"><h3 class="title">'+response['data']['goodinfo']['name']+'&nbsp;&nbsp;共出票<span class="nums">'+response['data']['nums']+'</span>张</h3></dd></dl></div>';
			}else{
				var addgood = '<div class="goodsInfo" style=""><div class="con"><dl class="clearfix"><dt class="fl"><img src="'+response['data']['goodinfo']['imgs']+'" width="82px" height="65px"/></dt><dd class="fl"><h3 class="title">'+response['data']['goodinfo']['name']+'</h3></dd></dl></div>';
			}
			var pricelist = money = intergral = isfirst = '';
			addgood += pricelist;
			var thua = '<span class="orange sum"><span class="side">'+response['data']['goodinfo']['integral']+'</span><span>通花</span></span>';		//通花
			if(id >= 944 && id<= 947){
				addgood += '<div class="number sum clearfix"><h3 class="tit fl">商品合计</h3><p class="conP fr"><span class="orange"><span class="much">'
						+response['data']['goodinfo']['ppri']/100*response['data']['nums']
						+'</span><span>元</span></span></p></div></div>';
			}else{
				addgood += '<div class="number sum clearfix"><h3 class="tit fl">商品合计</h3><p class="conP fr"><span class="orange"><span class="much">'
						+response['data']['goodinfo']['ppri']/100*response['data']['nums']
						+'</span><span>元</span></span>'+thua+'</p></div></div>';
			}
            $('.addnext').after(addgood);
            if(response['data']['temp'] == regist){
				regin();
			}
		}else{
			if (response['url']!='') {
				UrlGoto(response['url']);
			};
		}
	}
	AjaxRequest(u,d,f);
}
function putaddrinfo(){
	var type=getUrlParam('type');
	var sprice=getUrlParam('sprice');
	var num=getUrlParam('num');
	if (type==2) {
	    var oid=getUrlParam('oid');
	};
	var add = isAdd(this);
	var na = checkNameInput();
	var po = checkPosiInput();
	var ch = checkJieInput();
	var chp = checkPhone();
	if (!add || !na || !po || !ch || !chp) {
		return false;
	};
	var name = $('.nameInput').val();
	var phone = $('.noInput').val();
	var address = $('.posiInput').html();
	var detail = $('.jieInput').val();
	var isit = $('#moren').attr('myAttr');
    var u = '/index.php/shop/realgood/addadress';
    var d = 'name='+name+'&phone='+phone+'&address='+address+'&detail='+detail+'&isit='+isit;
    var f = function(res){
    	var response=eval(res);
    	if(response['status'] == request_succ){
    		var where = $('.greenBtn').attr('hou');
    		if (where == 1) {
    			if (id!='undefine') {
        	        if (type==2) {
        	            location.href = '/view/shop/selectAddress.html?id='+id+'&oid='+oid+'&type=2';
        	        }else{
    		            location.href = '/view/shop/selectAddress.html?id='+id+'&sprice='+sprice+'&num='+num;
        	        }
    			}else{
    			    location.reload();
    		    }
    		}else{
        	    if (type==2) {
        	        location.href = '/view/shop/selectAddress.html?id='+id+'&oid='+oid+'&type=2';
        	    }else{
    		        location.href = '/view/shop/selectAddress.html?id='+id+'&sprice='+sprice+'&num='+num;
        	    }
    		};
    	}else{
    		alert(response['msg']);
    	}
    }
    AjaxRequest(u,d,f);
}
function getalladdre(urltype){
    var u = '/index.php/shop/realgood/getaddress';
    var d = '';
    var f = function(res){
        var response = eval(res);
        var list = mo = sel ='';
    	if(response['status'] == request_succ){
    		$.each(response['data'], function(k, v) {
        	    mo = sel = '';
                if (v['status']==2) {
                	mo = '<i class="default">默认</i>';
                }
                if(v['sel']==1){
                    sel = 'selected';
                }
                if (type==2) {
                	selectHref = '/index.php/nonstandard/submitorder/address?fid='+id+'&oid='+oid+'&adr='+v['id'];
                	addHref = '/view/shop/addressManage.html?id='+id+'&oid='+oid+'&adr='+v['id']+'&type=2';
                }else{
                	if(urltype == null){
                		selectHref = '/view/shop/orderInfo.html?id='+id+'&sprice='+sprice+'&adr='+v['id']+'&num='+num;
                	}
                	if(urltype == 'metal'){	
                		selectHref = '/view/gold/paygoods.html?id='+id+'&addres='+v['id'];
                	}
                	if(urltype == 'stock'){
                		selectHref='/view/gold/metalextract.html?addres='+v['id'];
                	}
                	addHref = '/view/shop/addressManage.html?id='+id+'&sprice='+sprice+'&adr='+v['id'];
                }
        	    list += '<div class="selectAddress clearfix"><a href="'+selectHref+'" class="con '+sel+' fl"><div class="top"><span class="name">'+v['name']+'</span><span class="phoneNo">'+v['number']+'</span></div><div class="bot"><p>'+mo+v['city']+v['details']+'</p></div></a><div class="edit fr"><a href="'+addHref+'"></a></div></div>';
            });
            $('.content').html(list);
    	}else{
    		if ($response['msg']=='noadress') {
    			return;
    		};
    		alert(response['msg']);
    	}
    }
    AjaxRequest(u,d,f);
}
function getoneaddre(id){
    var u = '/index.php/shop/realgood/getanaddress';
    var d = 'id='+id;
    var f = function(res){
        var response = eval(res);
        if (response['status'] == request_succ) {
        	$('.nameInput').val(response['data']['name']);
        	$('.noInput').val(response['data']['number']);
	        $('.posiInput').html(response['data']['city']);
	        $('.jieInput').val(response['data']['details']);
	        if (response['data']['status']==2) {
	            $('#moren').trigger('click');
	        };
	        $('.isAdd').html('<a class="greenBtn" onclick="javascript:upaddress('+id+')">保存修改</a>');
        }else{
        	if (response['msg'] == '未找到您要的地址') {
        		$('.nameInput').val('');
        	    $('.noInput').val('');
	            $('.jieInput').val('');
        		return ;
        	};
    	    alert(response['msg']);
        }
    }
    AjaxRequest(u,d,f);
}
function upaddress(adr){
	var type=getUrlParam('type');
	var sprice=getUrlParam('sprice');
	if (type==2) {
	    var oid=getUrlParam('oid');
	};
	var add = isAdd(this);
	var na = checkNameInput();
	var po = checkPosiInput();
	var ch = checkJieInput();
	var chp = checkPhone();
	if (!add || !na || !po || !ch || !chp) {
		return false;
	};
	var name = $('.nameInput').val();
	var phone = $('.noInput').val();
	var address = $('.posiInput').html();
	var detail = $('.jieInput').val();
	var isit = $('#moren').attr('myAttr');
    var u = '/index.php/shop/realgood/upaddre';
    var d = 'name='+name+'&phone='+phone+'&address='+address+'&detail='+detail+'&isit='+isit+'&id='+adr;
    var f = function(res){
        var response = eval(res);
        if (response['status'] == request_succ) {
        	if (type==2) {
        	    location.href = '/view/shop/selectAddress.html?id='+id+'&oid='+oid+'&type=2';
        	}else{
        		location.href = '/view/shop/selectAddress.html?id='+id+'&sprice='+sprice;
        	}
        }else{
        	alert(response['msg']);
        }
    }
    AjaxRequest(u,d,f);
}
function deladdre(){
	var type=getUrlParam('type');
	var sprice=getUrlParam('sprice');
	if (type==2) {
	    var oid=getUrlParam('oid');
	};
	if(!confirm("确定要删除吗？")){
      return;
    }
	if(adr == '' || isNaN(adr)){
		UrlGoto('/view/shop/list.html');
	}
    var u = '/index.php/shop/realgood/deladdre';
    var d = 'id='+adr;
    var f = function(res){
        var response = eval(res);
        if (response['status'] == request_succ) {
        	if (type==2) {
        	    location.href = '/view/shop/selectAddress.html?id='+id+'&oid='+oid+'&type=2';
        	}else{
        		location.href = '/view/shop/selectAddress.html?id='+id+'&sprice='+sprice;
        	}
        }else{
        	alert(response['msg']);
        }
    }
    AjaxRequest(u,d,f);
}
function getrealOrder(){
	if($('.ifhasAddress').hasClass('noOrderAddress')){
		$('.grayBg,.noAddressTip').fadeIn();
		return ;
	}
	if (selectadress==0|| isNaN(selectadress) || isNaN(sprice)) {
		return ;

	};
	if(id == '' || isNaN(id)){
		UrlGoto('/view/shop/list.html');
	}
	var remark = $('#remark').val();
	var nums = $('.nums').html();
	var name = $('.orderAddress .con .name').html();
	var phone = $('.orderAddress .con .phoneNo').html();
	var detail = $('.orderAddress .con .detail').html();
	var priceNum = $('.orange .much').html();
	var paytype = $('.chosen .active').attr('type');
	var ua = window.navigator.userAgent.toLowerCase();
	var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
	if (priceNum>=16000&&paytype=='weixin') {
		alert("由于支付额度超出微信支付限制，请使用支付宝进行支付，或联系客服帮您支付：400-641-5080");
		return;
	};
	if ((priceNum==undefined||priceNum<=3000)&&iswx==1) {
		var u ='/index.php/shop/integral/HgetOrder';
		var d ='id='+id+'&limit=1&adress='+selectadress+'&remark='+remark+'&prid='+sprice
				+'&name='+name+'&phone='+phone+'&detail='+detail+'&nums='+num;
		var f =function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				$("body").append(response['data']);
				callpay();
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
				UrlGoto(response['url']);
			}
		}
		AjaxRequest(u,d,f);
	}else{
		$('#goodsid').val(id);
		$('#addressid').val(selectadress);
		$('#name').val(name);
		$('#nums').val(nums);
		$('#mobile').val(phone);
		$('#address').val(detail);
		$('#prid').val(sprice);
		$('#limit').val('1');
		$('#paytype').val($('.chosen .active').attr('type'));
		$('#theform').submit();
	}	
}
//分享次数添加
function addsharenum(id){
    var u = '/index.php/shop/flowgood/addsharenum';
    var d = 'id='+id;
    var f = function(res){
    }
	AjaxRequest(u,d,f);
}
function exchange(id){
	if (!confirm('确定兑换吗？')) {
		return ;
	};
	var u = '/index.php/shop/integral/exchangefund';
	var d = 'id='+id;
	var f = function(res){
		var response=eval(res);
		if (response['status']==request_succ) {
			alert(response['msg']);
		    UrlGoto(response['url']);
		}else{
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
$(document).keyup(function(e){ 
    var curkey = e.which; 
    if(curkey == 13){ 
        $(".greenBtn").click(); 
        return false; 
    } 
});
/**
 * 校验验证码
 * @param int  mobile
 * @param int  code
 */
function arCheckcode(){
	var  mobile=$("#mobile").val();
	var  Mreg=/^\d{11}$/; 
	if(!Mreg.test(mobile)){
		alert("手机号码为空或者格式不正确!");
		return false;
	}
	var  code=$("#code").val();
	var  Creg=/^\d{6}$/; 
	if(!Creg.test(code)){
		alert("验证码为空或者格式不正确!");
		return false;
	}
	var  invitation =  $("#invitation").val();
	var  password = $("#password").val();
	if(password == '' ){
		alert('密码为必填选项!');
		return false;
	}
    var u = '/index.php/nonstandard/system/binding_mobile';
    var d = "mobile="+mobile+"&code="+code+'&password='+password+'&invitation='+invitation;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			alert('注册成功');
            $(".shadow").css("display","none");
            $(".tips").css("display","none");
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
    }
	AjaxRequest(u,d,f);
}
//猪肉+音乐会 光标移入
function cInpVal(obj){
	if($(obj).val() == '1'){
		$(obj).val('');
	}
}
//光标移除
function inpVal(obj,num){
	if($(obj).val() == ''){
		$(obj).val('1');
		if(num == 1){
			$('.endNum').html('2.5'+'kg');
			$('#allmoney').html($('#meatP').html());
		}else{
			$('.endNum').html('1'+'张票');
			$('.payMoney').html($('.fontp font').html());
		}
	}
}
