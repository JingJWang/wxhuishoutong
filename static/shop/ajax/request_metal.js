/**
 * 
 */
var shopStyle={
		choiceModel:function(obj,type){
			$(obj).addClass('button_hover').siblings().removeClass('button_hover');
			$("#typeData").val(type);
			$('.mannerCont span').css('display','none');
			if(type==1){
				$('.mannerCont .cont_kc').css('display','block');
			}else{
				$('.mannerCont .cont_money').css('display','block');
			}
		},
		reckonWeight:function(type){
			var weight='';
			weight = $("#weight").val();
			weight =Number(weight);
			if(type > 0){
				weight = weight + 1;
				$("#weight").val(weight);
				shopStyle.reckonMetalPri(weight);
				weight = weight +'g';
				$(".asNum_p").html(weight);
			}else {
				weight = weight - 1;
				if(weight < 0){
					weight = 0;
				}
				$("#weight").val(weight);
				shopStyle.reckonMetalPri(weight);
				weight = weight +'g';
				$(".asNum_p").html(weight);
			}
			
		},
		checkWeight:function(obj){
			var weight=$(obj).val();
			metalShop.reckonMetalPri(weight);
			weight = weight +'g';
			$(".asNum_p").html(weight);
		},
		reckonMetalPri:function(weight){
			var pri=$("#metalPri").val();
			pri = Number(pri)*100;
			weight = Number(weight);
			var total = weight * pri/100;
			$('#total').html('合计： ¥'+total);
		},
		choicePay:function(obj,type) {
			$("#paytype").val(type);
			$(obj).addClass('myLi').siblings().removeClass('myLi');			
			if(type == 1){
				metalShop.orderWxPay();
			}
			if(type == 3){
				$(".affirmPay").attr('onclick','metalShop.orderZfbPay();');
			}		
		},
		payType:function(){
			
		}
}
var metalShop={
		goodsInfo:function(){
			var u='/index.php/shop/metal/goodsGold';
			var d='';
			var f=function(res){
				if(res.status == request_succ){
					$(".shopImg").html('<img src="'+res.data.img+'">');
					$(".goods_h1").html(res.data.name);
					$("#goodsId").val(res.data.id);
					$(".buyGoods_span").html('<p>下单提示</p><p>¥'+res.data.pri+'/克</p>'+
							'<input type="hidden" name="pri" id="metalPri" value="'+res.data.pri+'">');
					$(".shopNews").html(res.data.content);
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
		},
		submitOrder:function(){
			var weight=$("#weight").val();
			if(weight < 1){
				alert('请选择购买的商品重量!');return false;
			}			
			var data=$("#orderinfo").serialize();
			var u='/index.php/shop/metal/subGoodsOrder';
			var d=data;
			var f=function(res){
				if(res.status == request_succ){
					UrlGoto(res.url);
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
			
		},
		recordInfo:function(id){
			var u='/index.php/shop/metal/recordInfo';
			var d='id='+id;
			var f=function(res){
				if(res.status == request_succ){
			     	var	goods='<img src="'+res.data.goods.img+'">'+
						'<span>'+res.data.goods.name+'</span><div>'+
						'<p>¥'+res.data.goods.price+'</p><p>'+res.data.goods.content.info+'</p></div>';
					$(".particulars").html(goods);
					var sign='';
					$(".defaultAddress").attr('href','../../view/shop/selectAddress.html?id='+id+'&sign=metal');
					if(res.data.addres != 0){
						if(res.data.addres.status == 2){
							sign='<b>默认</b>';
						}else{
							sign='';
						}
						var addres='<span>'+res.data.addres.name+res.data.addres.number+'</span><div>'+sign+
						'<p>'+res.data.addres.city+res.data.addres.province+res.data.addres.details+'</p>'+
						'<input type="hidden" name="id" value="'+res.data.goods.id+'">'+
						'<input type="hidden" name="addres" value="'+res.data.addres.id+'"></div>';
						$(".defaultAddName").html(addres);
					}else{
						$(".defaultAddName").html('<p style="text-align:center;font-size:16px;">添加地址</p>');
					}
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
		},
		orderWxPay:function(){
			var paydata=$("#paydata").serialize();
			var u='/index.php/shop/metal/orderPay';
			var d=paydata;
			var f=function(res){
				if(res.status == request_succ){
						$("body").append(res.data);
						$(".affirmPay").attr('onclick','callpay();');
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);		
		},
		orderZfbPay:function(){
			var paydata=$("#paydata").serialize();
			var u='/index.php/shop/metal/orderPay';
			var d=paydata;
			var f=function(res){
				if(res.status == request_succ){
						$("body").append(res.data);
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);		
		},
		orderBalance:function() {
			var paydata=$("#paydata").serialize();
			var u='/index.php/shop/metal/orderPay';
			var d=paydata;
			var f=function(res){
				if(res.status == request_succ){
					alert(res.msg);
					UrlGoto(res.url);	
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
		},
		userinfo:function(id){
			var u='/index.php/shop/metal/userInfo';
			var d='id='+id;
			var f=function(res){
				if(res.status == request_succ){
					var content='';
					if(res.data !=0 ){
						content = '<li id="my1" class="myLi" onclick="shopStyle.choicePay(this,4);">'+
						'<img src="../../static/gold/img/recharge (4).png"><p>余额 支付</p><b>账户余额：¥'+res.data+'</b></li>';
						$(".affirmPay").attr('onclick','metalShop.orderBalance();');
					}
					var ua = navigator.userAgent.toLowerCase();
		            if (ua.match(/MicroMessenger/i) == "micromessenger") {
		                content = content + '<li id="my2" onclick="shopStyle.choicePay(this,1);">'+
		    			'<img src="../../static/gold/img/recharge (1).png"><p>微信支付</p></li>';
		            } else {
		                content = content + '<li onclick="shopStyle.choicePay(this,3);">'+
		    			'<img src="../../static/gold/img/recharge (2).png"><p>支付宝支付</p></li>';
		            }
		            $("#paytmethod").append(content);
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);	
			
		}
		
}