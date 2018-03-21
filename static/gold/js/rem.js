window.onload=window.onresize=function(){
	document.documentElement.style.fontSize=window.innerWidth/16+'px';
}
var metalOrdre={
		//获取贵金属产品列表	
		getType:function(){
			var u='/index.php/metal/order/metalType';
			var d='';
			var f=function(res){
				var content='';
				var fristid='';
				$.each(res.data,function(k,v){
					if(k==0){
						fristid=v['id'];
						content = content +'<li onclick="metalStyle.clickType(this,'+v['id']+');"'+
											'class="recycleSortLi">'+v['name']+'</li>';
					}else{
						content = content +'<li onclick="metalStyle.clickType(this,'+v['id']+');" >'+v['name']+'</li>';
					}
				});
				content = content +'<input type="hidden" name="metal" id="metalData" value="'+fristid+'">';	
				metalOrdre.getOption(fristid);	
				$(".recycleSort").html(content);
				//$(".recycleSort li").eq(0).removeClass('recycleSortLi');
			}
			AjaxRequest(u,d,f);
		},
		//获取产品详细信息
		getOption:function(id){
			var u='/index.php/metal/order/metalOption';
			var d='id='+id;
			var f=function(res){
				var content='';
				var mark='';
				var style='';
				var num=1;
				if(res.status == request_succ){
					$.each(res.data,	function(k,v){
						if(num == 1){
							style= 'pinggu_title_one';
							mark = 'pinggu_outhera';
							display='';
							num = 0;
						}else{
							style= 'pinggu_title_two';
							mark = 'pinggu_outherb';
							display='display: none;';
						}
						content = content +'<dd>'+
						'<div class="property_title  '+style+'">'+
						'<span class="fl">'+v.name+'</span>'+
						'<p class="conTxt fl TextOverflow"></p>'+
						'<a class="xiugai_btn fr" href="javascript:;" '+
						'onclick="metalStyle.modifyOption(this);" style="'+display+'">修改</a>'+																					
						'</div>'+metalOrdre.optionInfo(v.option,mark,k)+'</dd>';
					});			
					$("#metalData").val(id);
					content = '<dl>'+content+'</dl>';
					$("#weight").val('');
					metalOrdre.metalPri(id)
					$("#property_list").html(content);
				}
			}
			AjaxRequest(u,d,f);
		},
		optionInfo:function(data,mark,key){
			var content='';
			var info='';
			var frist='';
			var num=1
			$.each(data,function(k,v){
					info = info + '<li onclick="metalStyle.nextOption(this),'+
					'metalStyle.clickOption(this,'+k+');" class="purity_span">'+v+'</li>';
			});
			content ='<ul class="pinggu_other '+mark+'">'+info+'<div class="clear">&nbsp;</div></ul>'+
			'<input type="hidden" name="'+key+'" id="'+key+'Data" value="'+frist+'">';
			return content;
		},		
		submitData:function(){
			var data=$("#request").serialize();
			var u='/index.php/metal/order/submitMetal';
			var d=data;
			var phone = $('.teltext').val();
			if(phone == ""){
		        alert("手机号不能为空");
		        return false;
		    }else if(!(/^1[34578]\d{9}$/.test(phone))){
		        alert("请输入正确的手机号码");
		        return false;
		    };
			var weight='';
			weight = $("#weight").val();
			weight =Number(weight);
			if(weight=="" || weight=="0" || weight<1){
				alert("请输入1以上克数且不能为0哦!");
				return false;
			};
			var f=function(res){
				if(res.status == request_succ){
					UrlGoto(res.url);
				}
				if(res.status == request_fall){
					alert(res.msg);
					if(res.url != ''){
						UrlGoto(res.url);
					}
				}
			}
			AjaxRequest(u,d,f);
		},
		metalPri:function(id){			
			var u='/index.php/metal/order/matelPri';
			var d='id='+id;
			var f=function(res){
				if(res.status == request_succ){
					$(".con_p").html('回收品类（今日'+res.data.variety+'出售价格：'+res.data.sellpri+'/克）');
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
		},
		reckonMetalPri:function(){
			var data=$("#request").serialize();
			var u='/index.php/metal/order/reckonPri';
			var d=data;
			var f=function(res){
				if(res.status == request_succ){
					$("#pri").html(res.data);
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
		},
		orderInfo:function(page,id){
			var u='/index.php/metal/order/orderInfo';
			var d='id='+id;;
			if(page == 1){
				var f=function(res){
					if(res.status == request_succ){
						var content='<p>订单时间：  <font>'+res.data.time+'</font></p>'+
						'<P>交易方式：  <font>'+res.data.content.type+'</font></P>'+
						'<p>回收品类：  <font>'+res.data.content.product+'/'+res.data.content.purity+'/'+res.data.content.metaltype+'</font></p>'+
						'<p>克数：  <font>'+res.data.content.weight+'</font></p>'+
						'<p>回收总额：  <font>￥'+res.data.content.total+'</font></p>';
						$(".content").html(content);
						$('.footer-left').attr('href','/view/gold/metalinfo.html?id='+res.data.id)
					}
				}
				
			}
			if(page == 2){
				var f=function(res){
					if(res.status == request_succ){
						$('.alreadyPay').html('<p class="alP">订单编号： '+res.data.id+'</p><input type="hidden" id="number" value="'+res.data.id+'"><div class="firstAlreadiv">'+
						'<h1>'+res.data.content.purity+res.data.content.product+
						'</h1><h2>'+res.data.type+'</h2></div>'+
						'<div class="alreadyPayDiv"><p>'+res.data.content.weight+'/回收估价 ¥'+res.data.content.total+'</p><span class="timerSpan">'+res.data.time+'<s>（最终以我们接收到的货物为准）</s></span><div>');
						$('.product').html('<p>回收品类： '+res.data.content.product+'</p><p>交易方式： '+res.data.dealtype+'</p>');
						$('.message').html('<p>纯度： '+res.data.content.purity+'</p><p>分类： '+res.data.content.metaltype+'</p><p>克数： '+res.data.content.weight+'</p>');
						if(res.data.dealtype == '库存'){
							$('.pinventoryMoney').html('<span class="PMgoods">将入库'+res.data.content.purity+'黄金：'+res.data.content.weight+'g</span><span class="PMgoods">商家预支付： ¥'+res.data.content.total+'</span>');
						}else{
							$('.pinventoryMoney').html('<span class="PMgoods">商家预支付： ¥'+res.data.content.total+'</span>');
						}	
						if(res.data.confirm == 1){
							$(".PMmoney").html('<p>回收商修改报价为</p><p>¥'+res.data.content.uppri+'元</p><p>'+res.data.isagree+'</p>');
						}
						if(res.data.addres == 0){
							$('.productMessage_h1').css('display','none');
							$('.RInformation').css('display','none');
						}	
						var permit='';
						if(res.data.content.uppri != 0){
							if(res.data.cancel == 0){
								permit='<button class="cancelBtn" onclick="metalOrdre.confirmOffer();">确认改价</button>';
							}else{
								permit='';
							}
						}else{
							permit='';
						}
						if(res.data.permit == 1){
							permit= permit + '<button class="cancelBtn" onclick="metalOrdre.orderCancel();">取消交易</button>';
						}
						$(".footer").html(permit);
					}					
				}
			}			
			AjaxRequest(u,d,f);
		},
		confirmOffer:function(){
			var res=confirm('确认本次修改的价格吗?');
			if(res == false){
				return false;
			}
			var u='/index.php/nonstandard/order/ConfirmQuote';
			var d='id='+$("#number").val();
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					  location.reload();
				}
				if(response['status'] == request_fall){
					alert(response['msg']);
				}
			}
			AjaxRequest(u,d,f);
		},
		orderCancel:function(){
			var url='/index.php/nonstandard/order/Viewcancel?oid='+$("#number").val();
			window.location.href = url;
		},
		metalPriceList:function(){
			var u='/index.php/metal/order/metalPriceList';
			var d='';
			var f=function(res){				
				if(res.status == request_succ){
					$('#goldStock').html(res.data.gold.stock);
					$('#goldAverage').html(res.data.gold.average);
					if(res.data.gold.rofit > 0){
						$('#goldRofit').html('<p class="claMetP">单克盈亏</p><span class="orangeJ">+</span><p class="claMetPcRed" >'+Math.abs(res.data.gold.rofit)+'</p>');
					}else if(res.data.gold.rofit < 0){
						$('#goldRofit').html('<p class="claMetP">单克盈亏</p><span class="greenJ">-</span><p class="claMetPcGreen" >'+Math.abs(res.data.gold.rofit)+'</p>');
					}else{
						$('#goldRofit').html('<p class="claMetP">单克盈亏</p><p class="claMetPcBlack" >0</p><span class=""></span>');
					}					
					if(res.data.gold.loss > 0){
						$('#goldLoss').html('<p class="claMetP">总盈亏</p><span class="orangeJ">+</span><p class="claMetPcRed" >'+Math.abs(res.data.gold.loss)+'</p>');
					}else if( res.data.gold.loss < 0){
						$('#goldLoss').html('<p class="claMetP">总盈亏</p><span class="greenJ">-</span><p class="claMetPcGreen" >'+Math.abs(res.data.gold.loss)+'</p>');
					}else{
						$('#goldLoss').html('<p class="claMetP">总盈亏</p><p class="claMetPc" >0</p><span class=""></span>');
					}
					$('#goldDeal').html(res.data.gold.buy+'/'+res.data.gold.sold);
					$('#platinumStock').html(res.data.platinum.stock);
					$('#platinumAverage').html(res.data.platinum.average);
					if(res.data.platinum.rofit > 0){
						$('#platinumRofit').html('<p class="claMetP">单克盈亏</p><span class="orangeJ">+</span><p class="claMetPcRed" >'+Math.abs(res.data.platinum.rofit)+'</p>');
					}else if(res.data.platinum.rofit < 0){
						$('#platinumRofit').html('<p class="claMetP">单克盈亏</p><span class="greenJ">-</span><p class="claMetPcGreen" >'+Math.abs(res.data.platinum.rofit)+'</p>');
					}else{
						$('#platinumRofit').html('<p class="claMetP">单克盈亏</p><p class="claMetPc" >0</p><span class=""></span>');
					}			
					if(res.data.platinum.loss > 0){
						$('#platinumLoss').html('<p class="claMetP">总盈亏</p><span class="orangeJ">+</span><p class="claMetPcRed" >'+Math.abs(res.data.platinum.loss)+'</p>');
					}else if( res.data.platinum.loss < 0){
						$('#platinumLoss').html('<p class="claMetP">总盈亏</p><span class="greenJ">-</span><p class="claMetPcGreen" >'+Math.abs(res.data.platinum.loss)+'</p>');
					}else{
						$('#platinumLoss').html('<p class="claMetP">总盈亏</p><p class="claMetPc" >0</p><span class=""></span>');
					}
					$('#platinumDeal').html(res.data.platinum.buy+'/'+res.data.platinum.sold);					
					$('#silverStock').html(res.data.silver.stock);					
					$('#silverAverage').html(res.data.silver.average);
					if(res.data.silver.rofit > 0){
						$('#silverRofit').html('<p class="claMetP">单克盈亏</p><span class="orangeJ">+</span><p class="claMetPcRed" >'+Math.abs(res.data.silver.rofit)+'</p>');
					}else if(res.data.silver.rofit < 0){
						$('#silverRofit').html('<p class="claMetP">单克盈亏</p><span class="greenJ">-</span><p class="claMetPcGreen" >'+Math.abs(res.data.silver.rofit)+'</p>');
					}else{
						$('#silverRofit').html('<p class="claMetP">单克盈亏</p><p class="claMetPc" >0</p><span class=""></span>');
					}					
					if(res.data.silver.loss > 0){
						$('#silverLoss').html('<p class="claMetP">总盈亏</p><span class="orangeJ">+</span><p class="claMetPcRed" >'+Math.abs(res.data.silver.loss)+'</p>');
					}else if( res.data.silver.loss < 0){
						$('#silverLoss').html('<p class="claMetP">总盈亏</p><span class="greenJ">-</span><p class="claMetPcGreen" >'+Math.abs(res.data.silver.loss)+'</p>');
					}else{
						$('#silverLoss').html('<p class="claMetP">总盈亏</p><p class="claMetPc" >0</p><span class=""></span>');
					}
					$('#silverDeal').html(res.data.silver.buy+'/'+res.data.silver.sold);
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
		},
		dealinfo:function(id,dealtype){
			var u='/index.php/metal/order/dealInfo';
			var d='id='+id+'&dealtype='+dealtype;
			var f=function(res){
				if(res.status == request_succ){
					if(dealtype == 1){
						$(".buy_money").html('<p class="nowMoney">账户余额：¥<span>'+res.data.balance+'</span></p>'+
								'<p class="nowPrice">参考价格：¥<span id="price">'+res.data.price+'</span>/克</p>');
					}
					if(dealtype == -1){
						$(".buy_money").html('<p class="nowMoney">库存：<span>'+res.data.stock+'</span>克</p>'+
								'<p class="nowPrice">参考价格：¥<span id="price">'+res.data.price+'</span>/克</p>');
					}
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);			
		},
		metalSellout:function(){
			var data=$("#orderinfo").serialize();
			var u='/index.php/metal/order/metalSellout';
			var d=data;
			var f=function(res){
				if(res.status == request_succ){
					$('.kucunDiv').css('display','none');
					//alert(res.msg);
					if(res.url != ''){
						UrlGoto(res.url);
					}
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);	
		},
		metalstockinfo:function() {			
			var u='/index.php/metal/order/metalStockInfo';
			var d='';
			var f=function(res){
				if(res.status == request_succ){
					$("#gold").html(res.data.gold);
					$("#platinum").html(res.data.platinum);
					$("#silver").html(res.data.silver);					
				}
				if(res.status == request_fall){
					alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);	
		},
		metalstockaddres:function(id){			
			var u='/index.php/metal/order/addresStock';
			var d='id='+id;
			var f=function(res){
				if(res.status == request_succ){
					$(".nameAddress").html('<span>'+res.data.name+' '+res.data.number+'</span>'+
					'<p>'+res.data.province+res.data.city+res.data.details+'</p><div>'+
					'<input type="hidden" name="addres" id="addres" value="'+res.data.id+'"></div>');
				}
				if(res.status == request_fall){
					$(".nameAddress").html('<span style="text-align:center">去添加</span><p></p><div>'+
							'<input type="hidden" id="addres" name="addres" value="0"></div>');
				}
			}
			AjaxRequest(u,d,f);	
		},
		submitStock:function(){
			var gold=$("#goldData").val();
			var platinum=$("#platinumData").val();
			var silver=$("#silverData").val();
			var addres=$("#addres").val();
			var u='/index.php/metal/order/submitStock';
			var d='gold='+gold+'&platinum='+platinum+'&silver='+silver+'&addres='+addres;
			var f=function(res){
				if(res.status == request_succ){
					UrlGoto(res.url);
				}
				if(res.status == request_fall){
					alert(res.msg);
					return false;
				}
			}
			AjaxRequest(u,d,f);				
		},
		stockRecord:function(){
			var u='/index.php/metal/order/stockRecord';
			var d='';
			var f=function(res){
				var content='';
				var info='';
				if(res.status == request_succ){
					$.each(res.data,function(k,v){
						$.each(v.content,function(i,n){
							info = info +'<span>'+i+n+'</span>';
						});
						content = content + '<div class="pickGoodFinish">'+
						'<div class="stateTime fl"><p>订单编号：'+v.number+'</p>'+
						'<h1 class="stateTime_finish">'+v.state+'</h1>'+
						'</div><div class="goldCategory fl">'+info+'</div>'+
						'<div class="nameAdd fl"><p>'+v.name+v.phone+'</p>'+
					    '<p>'+v.province+v.city+v.area+v.details+'</p></div>'+
						'<div class="stateTime fl">'+
							'<p>下单时间：'+v.time+'</p>'+
							'<span>'+v.express+'</span>'+
						'</div>'+
					'</div>';
						info='';
					});
					$(".pickGoodNull").after(content);
				}
				if(res.status == request_fall){
					//alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);	
		},
		dealRecord:function(){
			var u='/index.php/metal/order/dealRecord';
			var d='';
			var f=function(res){
				if(res.status == request_succ){
					var content = '';
					$.each(res.data,function(k,v){
						var rose=(v.sold-v.content.upri)/v.content.upri;
						if(rose > 0){
							var info='<span class="zhangdie">'+'<b class="addPRed">'+rose.toFixed(2)+'%</b>'+'<b class="adRed">↑</b>'+'</span>';
						}else{
							var info='<span class="zhangdie">'+'<b class="addPGreen">'+rose.toFixed(2)+'%</b>'+'<b class="adGreen">↓</b>'+'</span>';
						}
						content = content + '<a href="'+v.url+'"><div class="tradingRecord"><div class="tr_box fl">'+
							'<span class="'+v.style+'">'+v.dealtype+'</span><p>'+v.content.type+'</p>'+
							'<p></p><p>纯度99.99%</p><p>'+v.content.weight+'g</p></div>'+
							'<div class="addRecord">'+
							'<p class="addR_Pa">成交单价 :</p>'+
							'<span class="price">¥'+v.content.upri+'/克</span>'+
							'</div>'+
							'<div class="addRecord">'+
							'<p class="addR_Pa">今日卖出单价 :</p>'+
							'<span class="price">¥'+v.sold+'/克</span>'+
							'</div>'+
							'<div class="addRecord">'+
							'<p class="addR_Pa">涨跌 :</p>'+info+'</div>'+
							'<div class="bargain"><span>成交金额 :</span>'+
							'<span>¥'+v.content.total+'</span><p>'+v.jointime+'</p></div></div></a>';
					});
					$(".No").after(content);
				}
				if(res.status == request_fall){
					//alert(res.msg);
				}
			}
			AjaxRequest(u,d,f);
			
		}
}
var metalStyle={		
		clickType:function(obj,id){
			$(obj).addClass('recycleSortLi').siblings().removeClass('recycleSortLi');
			metalOrdre.getOption(id);
		},		
		optiondisplay:function(){
			$('form .property_list dd ul ').css('display','none');
		},
		modifyOption:function(obj){
			$(obj).parent().siblings('.pinggu_other').css('display','block');
		},
		nextOption:function(obj){
			var content=$(obj).text();
			$(obj).parent().siblings('.property_title').find('.conTxt').html(content);
			$(obj).parent().css('display','none');
			$(obj).parent().parent().next('dd').find('ul').css('display','block');
			$(obj).parent().parent().next('dd').find('.xiugai_btn').css('display','block');
		},
		clickOption:function(obj,id){
			$(obj).addClass('hover_span').siblings().removeClass('hover_span');
			$(obj).parent().siblings('input').val(id);
		},
		reckonWeight:function(type){
			var weight='';
			weight = $("#weight").val();
			weight =Number(weight);
			if(type > 0){
				weight = weight + 1;
				$("#weight").val(weight)
				weight = weight +'g';
				$(".asNum_p").html(weight);
			}else {
				weight = weight - 1;
				if(weight < 0){
					weight = 0;
				}
				$("#weight").val(weight)
				weight = weight +'g';
				$(".asNum_p").html(weight);
			}
			metalOrdre.reckonMetalPri();
		},
		choiceModel:function(obj,type){
			$(obj).addClass('hover_li').siblings().removeClass('hover_li');
			$("#typeData").val(type);
		},
		checkWeight:function(obj){
			var weight=$(obj).val();
			weight = Math.round(parseFloat(weight)*100)/100;
			weight = weight+'g';
			$(".asNum_p").html(weight);
		},
		checkPrice:function(obj){
			var weight = $(obj).val();
			weight = $("#weight").val();
			weight = Number(weight);
			weight = Math.round(weight*100)/100;
			var price=$("#price").html();
			price=Number(price)*100;
			var total=Math.round(parseFloat(price*weight))/100;
			$(".buy_lastP").html('合计：¥<span>'+total+'</span>');
			weight = weight +'g';
			$(".asNum_p").html(weight);
			$('.kuCNum').html(weight);
			$('.kuPrice').html(total);
		},
		reckonPrice:function(type){
			var weight='';
			weight = $("#weight").val();
			weight = Number(weight);
			weight = Math.round(weight*100)/100;
			if(type > 0){
				weight = weight + 1;
				if(weight.toString().length > 12){
					weighta = weight.toString().split(".")[0];
					weightb = weight.toString().split(".")[1];
					weight = Number(weighta+'.'+weightb);
					weight = Math.round(Math.round(weight*100))/100;
				}
				$("#weight").val(weight)
				var price=$("#price").html();
				price=Number(price)*100;
				var total=Math.round(parseFloat(price*weight))/100;
				$(".buy_lastP").html('合计：¥<span>'+total+'</span>');
				weight = weight +'g';
				$(".asNum_p").html(weight);
				$(".kuCNum").html(weight+'克');
				$(".kuPrice").html(total);
			}else {
				weight = weight - 1;
				if(weight < 0){
					weight = 0;
				}
				if(weight.toString().length > 12){
					weighta = weight.toString().split(".")[0];
					weightb = weight.toString().split(".")[1];
					weight = Number(weighta+'.'+weightb);
					weight = Math.round(Math.round(weight*100))/100;
				}
				$("#weight").val(weight);
				var price=$("#price").html();
				price=Number(price)*100;
				var total=Math.round(parseFloat(price*weight))/100;
				$(".buy_lastP").html('合计：¥<span>'+total+'</span>');
				weight = weight +'g';
				$(".asNum_p").html(weight);
				$(".kuCNum").html(weight+'克');
				$(".kuPrice").html(total);
			}
		},
		choisDeal:function(obj,id,dealtype){
			$(obj).addClass('myHover').siblings().removeClass('myHover');
			$("#weight").val('')
			$(".buy_lastP").html('')
			if(dealtype == -1){
				$(".buyPaymentBtn").attr('onclick','metalStyle.kucunSure(),metalStyle.checkPrice();');
			}else{
				$(".buyPaymentBtn").attr('onclick','metalShop.submitOrder();');
			}			
			metalOrdre.dealinfo(id,dealtype);
		},	
		reckonExtract:function(obj,type,product){
			var weight='';			
			weight =$(obj).parent().find('.num').val();
			weight =Number(weight);
			var stock=$(obj).parent().parent().parent().parent().find('.inventory_num').html();
			function stylea(){
				$(obj).parent().parent().find('.asNum_p').html('')
				$(obj).parent().find('.num').val(weight).css('color','red');
				$('.subbtn').css('background','#B7B7B6');
				$(obj).parent().find('.num').val(0);
				alert('库存不足');
			};
			function styleSub(){
				weighta = weight.toString().split(".")[0];
				weightb = weight.toString().split(".")[1];
				weight = Number(weighta+'.'+weightb);
				weight = Math.round(Math.round(weight*100))/100;
			};
			if(type == 1){
				if(weight > stock){
					stylea();
					return false;
				};
				weight = weight + 1;
				if(weight.toString().length > 12){
					styleSub();
				};
				if(weight > stock){
					stylea();
					return false;
				};
				$(obj).parent().find('.num').val(weight).css('color','#4f4f4f');
				$('.subbtn').css('background','#58ab22');
				weight = weight +'g';
				$(obj).parent().parent().find('.asNum_p').html(weight);
			};
			if(type == -1){
				weight = weight - 1;
				if(weight.toString().length > 12){
					styleSub();
				};
				if(weight < 0){
					weight = 0;
				};
				if(weight > stock){
					$(obj).parent().parent().find('.asNum_p').html('');
					$(obj).parent().find('.num').val(weight).css('color','red');
					$('.subbtn').css('background','#B7B7B6');
					alert('库存不足');
					return false;
				}
				$(obj).parent().find('.num').val(weight).css('color','#4f4f4f');
				$('.subbtn').css('background','#58ab22');
				weight = weight +'g';
				$(obj).parent().parent().find('.asNum_p').html(weight);
			};
			if(type == 0){
				var stock=$(obj).parent().parent().parent().parent().find('.inventory_num').html();
				if(weight > stock){
					$(obj).parent().parent().find('.asNum_p').html('');
					$(obj).parent().find('.num').val(weight).css('color','red');
					$('.subbtn').css('background','#B7B7B6');
					alert('库存不足');
					return false;
				};
				$(obj).parent().find('.num').val(weight).css('color','#4f4f4f');
				$('.subbtn').css('background','#58ab22');
				weight = weight +'g';
				$(obj).parent().parent().find('.asNum_p').html(weight);
			};
		},
		reckonGoldJ:function(obj){
			$(obj).parent().next('.zsImg').css('display','block');
		},
		reckonGoldJClose:function(obj){
			$(obj).parent('.zsImg').css('display','none');
		},
		kucunCancel:function(){
			$('.kucunDiv').css('display','none');
		},
		kucunSure:function(){
			var kuCHeight = $(window).height();
			$('.kucunDiv').height(kuCHeight);
			var weiNum = $('.num').val();
			if(weiNum !== ''){
				$('.kucunDiv').css('display','block');
			}else{
				alert('请输入您要卖出的数量！')
			}
		}
}
