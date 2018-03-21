/****************获取订单列表**********/
function orderList(number){
	var status=$("#status .pattern").attr('data-status');
	if(typeof(status) == 'undefined' || status ==''){
		alert('您还没有选择订单状态');return false;
	}
	var time=$("#time .pattern").attr('data-time');	
	if(typeof(time) == 'undefined' || time ==''){
		alert('您还没有选择订单日期');return false;
	}
	var keyword=$("#keyword").val();
	if(typeof(keyword) == 'undefined'){
		alert('没有获取到关键词');return false;
	}
	var start=$('#start').val();
	var end=$('#end').val();
	if(typeof(start) == 'undefined' || typeof(end) == 'undefined'){
		alert('您还没有选择订单日期');return false;
	}
	var u='/index.php/center/order/orderList';
	var d='status='+status+'&time='+time+'&start='+start+
		  '&end='+end+'&page='+number+'&keyword='+keyword;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			var content='';
			$.each(response['data']['list'],function(key,val){
				content = content +'<div class="layer">'+
		            '<div class="digit">'+val['number']+'</div>'+
		            '<div class="model">'+val['name']+'</div>'+
		            '<div class="status"></div>'+
		            '<div class="cellphone"></div>'+
		            '<div class="shift"></div>'+
		            '<div class="date">'+$.myTime.UnixToDate(val['jointime'])+'</div>'+
		            '<div class="check"><table width="100%" height="48px" cellspacing="0" cellpadding="0" border="0"><tr width="100%" height="100%"><td width="100%" height="100%">'+operation(val['orderstatus'],val['id'])+
		            '</td></tr></table></div></div>';
			});
			$("#orderList").html(content);
			//读取分页
			if(number == 1){
				var total=response['data']['total'];
				var page='';
				if(total > 1){
					page ='<span class="figure" onclick="typeNextPage(+1);" href="javascript:;">上一页</span>';
					for(var i=1;i<=total;i++){
						if( i > 4 ){
							var display='style="display:none;"';
						}else{
							var display='';
						}
						if(number == i){
							page = page + '<span id="type_'+i+'" '+display+' class="figure dig" onclick="orderPageBJ(this),typeNextPage('+i+')">'+i+'</span>';
						}else{
							page = page + '<span id="type_'+i+'" '+display+' class="figure" onclick="orderPageBJ(this),typeNextPage('+i+')">'+i+'</span>';
						}
					}
					page = page +'<span class="figure" onclick="typeNextPage(-1);" href="javascript:;">下一页</span>'+
							 '<span class="figure" id="total" data-val="'+total+'" href="javascript:;">共'+total+'页</span>';
				}else{
					page='<span class="figure dig" onclick="typeNextPage(1)" href="javascript:;">1</span>';				
				}	
				$("#typepage").html(page);		
			}
		}
		if(response['status'] == request_fall){
			$("#orderList").html('<p style="font-size:14px;color:red;">没有获取到结果</p>');	
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/*********订单分页*********/
function typeNextPage(page){
	if(page == "+1"){
		var select=$("#typepage .dig").html();
		var next = Number(select) - 1;
		if( $("#type_"+next).length > 0 ){
			$("#type_"+select).removeClass("dig");
			$("#type_"+next).addClass("dig");
			orderList(next);
		}else{
			alert('当前页已经是第一页');
			return false;
		}
		if(next > 3){
			var none = next -4;
			var diplay = next + 1;
			$("#type_"+next).css("display","inline");
			$("#type_"+diplay).css("display","inline");
			$("#type_"+none).css("display","none");
		}
	}
	if(page == "-1"){		
		var select=$("#typepage .dig").html();
		var next = Number(select) + 1;
		if( $("#type_"+next).length > 0 ){
			$("#type_"+select).removeClass("dig");
			$("#type_"+next).addClass("dig");
			orderList(next);
		}else{
			alert('当前页已经是最后一页');
			return false;
		}
		if(next > 3){
			var none = next -4;
			var diplay = next + 1;
			$("#type_"+next).css("display","inline");
			$("#type_"+diplay).css("display","inline");
			$("#type_"+none).css("display","none");
		}
	}
	if(page  != '-1' && page !='+1'){
		var next=page+1;
		if( $("#type_"+next).length > 0 ){
			orderList(page);
		}else{
		   alert('当前页已经是最后一页');
		   return false;
		}
		if(page > 3){
			var none = next -4;
			$("#type_"+next).css("display","inline");
			$("#type_"+none).css("display","none");
		}
	}
}
/***********查看订单*************/
function orderInfo(obj,id){	
	var u='/index.php/center/order/orderInfo';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			orderDisplay(response['data']);
			lookup(obj);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/**************订单预支付*****************/
function prePayment(obj,id){	
	var perimt=confirm('确定预支付当前订单吗?');
	if(perimt == true){
		var u='/index.php/center/order/prePayment';
		var d='id='+id;
		var f=function(res){
			var response=eval(res);
			if(response['status'] == request_succ){
				alert(response['msg']);
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
			}
			if(typeof(response['url']) != 'undefined' && response['url'] !=''){
				urlGoto(response['url']);
			}
		}
		AjaxRequest(u,d,f);
	}else {
		alert('当前订单已经取消');
	}
}

/***********查看订单*************/
function orderDisplay(response){
	/**********订单部分************/
	//订单编号
	var number=response['order']['0']['number'];
	(typeof(number) == 'undefined'  || number == '' ) ? 
	$("#order_number").html('') : $("#order_number").html(number);
    //订单名称
	var name=response['order']['0']['name'];		
	(typeof(name) == 'undefined'  || name == '' ) ? 
	$("#order_name").html('') : $("#order_name").html(name);
	//提交时间
	var jointime=$.myTime.UnixToDate(response['order']['0']['jointime'],true);
	(typeof(jointime) == 'undefined'  || jointime == '' ) ? 
	$("#order_jointime").html('') : $("#order_jointime").html(jointime);
	//用户地址
	var address=response['order']['0']['province']+response['order']['0']['city']+
	response['order']['0']['county']+response['order']['0']['quarters'];
	(typeof(address) == 'undefined'  || address == '' ) ? 
	$("#order_address").html('') : $("#order_address").html(address);
	//成交价格
	var order_price=response['order']['0']['price'];
	$("#order_price").html(order_price)
	var order_mobile=response['order']['0']['mobile'];
	$("#order_mobile").html(order_mobile)
	var order_phone=response['order']['0']['phone'];
	$("#order_phone").html(order_phone)
	/************报价部分**************/
	var offer=response['offer'];
	if(typeof(offer) == 'object'){
		var content='';
		$.each(offer,function(k,v){
			content = content +'<div class="info">'+
		        '<div class="attribute">回 收 商:</div>'+
		    	'<div class="classify info-name">'+v['name']+'</div>'+
		    	'<div class="probate"></div>'+
		    	'<div class="amount">报价金额:<span><input type="text" style="width:40px;" '+
		    	'id="upprice" value="'+v['price']+'"></input>元</span><span id="priceQuote">'+
		    	'<a href="javascript:;" onclick="Price.quote('+v['price']+')">修改报价</a></span>'+
		    	'</div></div><div class="info">'+
		    	'<div class="attribute">回收地址:</div>'+
		    	'<div class="classify"></div>'+
		    	'</div><div class="info"><div class="attribute">联系方式:</div>'+
		    	'<div class="classify"><div class="info-phone">手机: </div>'+
		        '<div class="chat">微信: </div></div></div>'+
		        '<div class="info"><div class="attribute">报价金额:</div>'+
		        '<div class="classify"></div><div class="probate"></div></div>';
		});
		$("#offerinfo").html(content);
	}	
	/***********订单详情部分************/
	var model=response['attr'];
	var attr=response['order']['0']['oather'];
	if(typeof(attr) == 'object'){
		var content='';
		$.each(attr,function(k,v){
			content = content + '<div class="info">'+
            	'<div class="attribute">'+model[k]+'</div>'+
            	'<div class="classify">'+v+'</div></div>';
		});
		$("#attrinfo").html(content);
	}
	
}
function operation(status,id){
	var operation ={};
    //订单未提交 已成交
    if( status == -2 || status == 10 || status == -1 || status == 4){
        operation['orderInfo'] 	='查看订单';    }
    //订单报价中 
    if( status == 1 ){
    	operation['orderInfo'] 	='查看订单'; 
    	operation['orderQuote'] ='提交报价';
    }
    //等待预支付  待交易  
    if (status == 2 ){
    	operation['orderInfo']	='查看订单';
    	operation['prePayment']	='预支付';    	
    }
    //待交易状态
    if( status == 3 ){
    	operation['orderInfo']	='查看订单';
    	operation['orderpay']	='支付订单';
    	operation['ordercall']	='取消订单';
    }
    var content='';
    $.each(operation,function(k,v){
    	content = content +'<span class="view"'+
    	'value="" onclick="'+k+'(this,'+id+')"> '+v+' </span>';
    	//  '<input class="cancle" type="button" value="取消订单" onclick="orderform(this);"/>'
    });
    return content;
}
var orderpay=function(obj,id){
	defray.show();	
	getVouchers(id);
	$(".affirm").attr('onclick','submitpay('+id+',0)');
}
var submitpay=function(id,vouch){	
	if(vouch == '' || vouch == 0 ){
		alert('当前支付不包含现金劵');
	}else{
		var option=confirm('支付当前订单包含一张现金劵!');
	}	
	var u='/index.php/center/order/orderpay';
	var d='id='+id+'&vouch='+vouch;
	var f=function(res){
		if(request_fall == res['status']){
			alert('订单支付出现异常:'+res['msg']);
		}
		if(request_succ == res['status']){
			alert('订单支付已经完成');
			defray.cacel();
		}
	};			
	AjaxRequest(u,d,f);
}
/**************获取当前订单预支付金额 以及当前用户下的代金券*****************/
var getVouchers=function(id) {
	var u='/index.php/center/order/getVouchers';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);		
		if(response['status'] == request_succ){
			var price=response['data']['price']['price'];
			var content='';
			$(".usable .list").html('');
			$.each(response['data']['vouche'],function(k,v){
				if(k == 0){
					active='active';
					$(".affirm").attr('onclick','submitpay('+id+','+v['id']+')');
					amount=Number(price)+Number(v['amount']);
					if(Number(price) > Number(v['ranges'])){						
						$(".bulk .amount").html(amount);						
					}else{						
						$(".bulk .amount").html(price);
						active='';
					}
				}else{
					active='';
				}
				content= content + '<div class="circle '+active+'"'+
						'onclick="defray.change(this,'+v['amount']+','+id+','+v['id']+');">'+
	                    '<div class="worth">'+
						'<div class="reduce">￥<span>'+v['amount']+'</span></div>'+
						'<div class="act">回收增值劵</div></div>'+
						'<div class="term">成交价格大于'+v['ranges']+'元可使用</div></div>';
			});
			$(".usable .list").html(content);
			$(".asses .price").html(price);
			//$(".bulk .amount").html(price);
		}
		if(response['status'] == request_fall){
			var response=eval(res);		
			var price=response['data']['price']['price'];
			$(".asses .price").html(price);
			$(".bulk .amount").html(price);
			$(".usable .list").html('没有查询到当前用户的回收增值劵');
		}
	}
	AjaxRequest(u,d,f);
}
function ordercall(obj,id){
	orderCall.show(id);
}
var orderCall={
		number:'',
		show:function(id){
			number=id;
			$("#callinfo").val('');
			$(".shadow").show();
			$(".reason").show();
		},
		close:function(){
			$(".shadow").hide();
			$(".reason").hide();
		},
		submit:function(){
			var content=$("#callinfo").val();
			var u='/index.php/center/order/ordercall';
			var d='id='+number+'&content='+content;
			var f=function(res){
				if(request_fall == res['status']){
					$('.reason .erro').html('订单取消出现异常');
				}
				if(request_succ == res['status']){
					$('.reason .erro').html('订单取消已经完成');
					orderCall.close(0);
				}
			};			
			AjaxRequest(u,d,f);
		}
}
var priceQuote=function(res){
	if(request_fall == res['status']){
		$('#priceQuote').append('<font style="color:red;">异常</font>');
	}
	if(request_succ == res['status']){
		$('#priceQuote').append('<font style="color:red;">已修改</font>');
	}
}
var Price={
		quote:function(price){
			var number=$("#order_number").html();
			var upprice=$('#upprice').val();
			var u='/index.php/center/order/upQuote';
			var d='upprice='+upprice+'&number='+number+'&price='+price;
			var f=priceQuote;			
			AjaxRequest(u,d,f);
		}		
}
//显示支付增值劵弹框
var defray={	
		//显示支付订单弹框
		show:function(){
			 var sm = parseInt($(".defray").height())/2;
			 $(".defray").css("margin-top",'-'+ sm + 'px');
			 $(".shade").css('display','block');
			 $(".defray").css('display','block');
		},
		//支付增值劵弹框关闭和取消按钮
		cacel:function(){			
			$(".shade").css('display','none');
			 $(".defray").css('display','none');
		},
		//支付增值劵弹框确认支付
		submit:function(){
			$(".defray .affirm").click(function(){
			    //省略确认支付操作
			    $(".shade , .defray").hide();
			});
		},
		change:function(obj,price,id,vouvh){
			    $(".circle.active").removeClass("active");
			    $(obj).addClass("active");
			    var amount=Number($(".asses .price").html());
			    $(".bulk .amount").html(amount+price);
			    $(".affirm").attr('onclick','submitpay('+id+','+vouvh+')');
		}
		
}
function orderQuote(obj,id){
	Quote.show(obj,id);
	Quote.info(obj,id);
}
var Quote={
		number:'',
		show:function(){
			$('#amount').html('');
			$('#oather').html('');
			$('.shadow').show();
			$('.frames').show();
		},
		close:function(){
			$('#quote').val('');
			$('.frames').hide();
			$('.shadow').hide();
		},
		info:function(obj,id){			
			var u='/index.php/center/order/info';
			var d='id='+id;
			var f=function(res){
				var resp=eval(res);
				if(resp['status'] == request_fall){
					$('.error').html(resp['msg']);
				}
				if(resp['status'] == request_succ){
					number=resp['data']['number'];
					$('#amount').html(resp['data']['amount']+'台');
					$('#oather').html(resp['data']['oather']);
				}
			}
			AjaxRequest(u,d,f);
		},
		batch:function(){
			var pri=$('#quote').val();
			var u='/index.php/center/order/batchQuote';
			var d='number='+number+'&pri='+pri;
			var f=function(res){
				var resp=eval(res);
				if(resp['status'] == request_fall){
					$('.error').html(resp['msg']);
				}
				if(resp['status'] == request_succ){
					$('.error').html(resp['msg']);
					 Quote.close();
				}
			}
			AjaxRequest(u,d,f);
		}
}