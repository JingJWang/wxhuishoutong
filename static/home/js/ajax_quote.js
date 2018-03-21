Screening();
/**
 * 获取选择的条件
 */
function Getoption(option){
		var order=$("#"+option).val();
		if(order == 1){
			$("#"+option).val('0');
		}else{
			$("#"+option).val('1');
		}
		switch (option) {
			case 'price':
				$("#distance").val('0');
				$("#evaluation").val('0');
				$("#transaction").val('0');
				break;
			case 'distance':
				$("#price").val('0');
				$("#evaluation").val('0');
				$("#transaction").val('0');
				break;
			case 'evaluation':
				$("#price").val('0');
				$("#distance").val('0');
				$("#transaction").val('0');
				break;
			case 'transaction':
				$("#price").val('0');
				$("#evaluation").val('0');
				$("#evaluation").val('0');
				break;
		}
		Screening();
}
/**
 * 获取选定的属性
 */
function Option(attr){
	var option = $("#option").val();
	if(option == 0){
		 $("#option").val(attr);
	}
	if(option != 0){
		$("#option").val(attr);
	}
	if(option == attr){
		$("#option").val('0');
	}
	Screening();
}
/**
 * 认证相关
 */
function Auto(){
	var auto=$("#auto").val();
	if(auto == 1){
		$("#auto").val('0');
	}else{
		$("#auto").val('1');
	}
	Screening();
}
/**
 *根据条件获取相关的报价信息
 */
function  Screening(){
	var option = '';
	//价格
	var price = $("#price").val();
	if(price != 0){
		option= option + 'price=1';
	}else{
		option= option + 'price=0';
	}
	//距离
	var distance = $("#distance").val();
	if(distance != 0){
		option= option + '&distance=1';
	}else{
		option= option + '&distance=0';
	}
	//评价
	var evaluation = $("#evaluation").val();
	if(evaluation != 0){
		option= option + '&evaluation=1';
	}else{
		option= option + '&evaluation=0';
	}
	//成家
	var transaction = $("#transaction").val();
	if(transaction != 0){
		option= option + '&transaction=1';
	}else{
		option= option + '&transaction=0';
	}
	//服务
	var  options = $("#option").val();	
	if(options != 0){
		option= option + '&option='+options;
	}else{
		option= option + '&option=0';
	}
	var  auto  = $("#auto").val();
	if(auto != 0){
		option= option + '&auto='+auto;
	}else{
		option= option + '&auto=0';
	}		
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/quote/GetScreening",
		   data: option,
		   dataType:"json",
		   beforeSend: function(){
				 $("#turn_gif_box").css('display','block');
		   },
		   success: function(data){
			   response=eval(data);
			   var title='<p style="font-size:13px;text-align:center;background:'+
				   		 '#f6f6f6;">部分选择<font style="font-style: italic;font-weight:bold;">"'+
				   		 '"快递方式</font>的用户会有【钱先到账】的惊喜哦~';
			   var content='';
			   var auroname='';
			   var coop='';
			   var consign ='';
			   var cooptitle='';
			   var hint = '';
			   var name = '';
			   var mold = '';
			   var adv = '<div class="goodness">'+
				   '<span class="stress">回收</span>'+
				   '<span>火速变现</span>'+
				   '</div>'+
				   '<div class="seize"></div>';
			   var point = '<div class="goodness con">'+
				   '<span class="stress">寄售</span>'+
				   '<span>自主定价</span>'+
				   '</div>';
			   var tip = '<div class="point">暂无更多报价' +
					   '<div class="left-line"></div>'+
					   '<div class="right-line"></div>'+
				   '</div>';
			   var coupons =new Array();
			   if(response['status'] == 1000){
				   $.each(response['data']['option'],function(n,data){
					   if(response['data']['coupon']!=null){
						   $.each(response['data']['coupon'],function(n,coupon){
							   if(parseInt(data['price'])>=parseInt(coupon['ranges'])){
								   coupons[n]=parseInt(coupon['amount']);
							   }else{
								   coupons[n] = null;
							   }

						   });
						   if(coupons.length>0){
							   var max=coupons[0];
							   for(var i=1;i<coupons.length;i++){
								   if(max<coupons[i]){
									   max=coupons[i];
								   }
							   }
						   }
						   if(data['ctype'] != 1 && max!=null){
							   hint = '<span class="cue">可用增值劵'+max+'元</span>';
							   name = 'cue';
						   }else{
							   hint = '';
						   }
					   }

					   var temp='';
					   $.each(data['service'],function(k,i){
						   temp = temp + '<li>'+i+'</li>';
					   });

					   if(data['ctype'] == 1){
						   //coop='<font color="#f3621f">寄售:</font>';
						   //coopinfo='<div class="information">我们帮您卖，此价为参考价，想卖多少您说了算</div>';
						   //cooptitle='点此寄售';
						   coop = '<div class="price">'+
								   	'<div class="sign fl">寄售</div>'+
							   			'<div class="reveal fr">'+
							   				'<div class="number fl">￥'+data['price']+'元</div>'+
							   				'</div>'+
							   			'</div>'+
							   			'<div class="bot">'+
							   				'<div class="des fl">按指导价寄售快递包邮</div>'+
							   					'<span class="sell fr">点此寄售</span>'+
						   				'</div>';

						   
						   mold = 'consign';
					   }else{
						   //coop='回收商:';
						   //coopinfo='';
						   //cooptitle='点此卖给他';
						   coop = '<div class="price">'+
							   '<div class="seller fl">回收商</div>'+
							   '<div class="authen fl"></div>'+
							   '<div class="reveal fr">'+
							   '<div class="number fl">'+
							   '￥'+data['price']+'元'+
							   hint+
							   '</div>'+
							   '<ul class="fl clearfix">'+
							   '<li>快递回收</li>'+
							   '</ul>'+
							   '</div>'+
							   '</div>'+
							   '<div class="bot">'+
							   '<div class="sell fr">选此回收商</div>'+
							   '<div class="left">'+
							   '<div class="deal">'+
							   '<span>成交</span>'+
							   '<span class="num">'+data['csum']+'</span>'+
							   '<span>单</span>'+
							   '</div>'+
							   '<div class="scoreStar fl clearfix">'+
							   '<span class="fl">'+
							   '<span class="scores" data-class="'+data['cclass']+'">评价'+data['cclass']+'分</span>'+
							   '</span>'+
							   '<span class="starPic fl"></span>'+
						   '</div>'+
						   '</div>'+
						   '</div>';
						   mold = 'recover';
					   }

					      /*
						   var list='<a class="bidLine '+ name + '"  href="/index.php/nonstandard/quote/transactions?type='+data['offerid']+'">'+
						   '<div class="price clearfix"><p class="number fl">￥'+data['price']+'元'+ hint + '</p><ul class="fl clearfix">'+
						   temp+'</ul><div class="phone-model"><span>'+data['name']+'</span></div></div>'+coopinfo+
						   '<div class="top clearfix">'+
						   '<p class="code fl">订单编号：<span>'+data['orderid']+'</span></p>'+
						   // '<p class="posi fr">'+data['distance']/1000+'km</p>'+
						   '</div>'+
						   '<div class="bot"><div class="cell">'+
						   '<span href="/index.php/nonstandard/quote/transactions?type='+data['offerid']+'" onclick="checkpri('+data['price']+')" class="cell-people">'+cooptitle+'</span></div>'+
						   '<div class="left"><div class="confirm clearfix theme">'+
						   '<p class="fl">'+coop+'<span class="name">'+data['cname']+'</span><span class="authen">'+data['cauth']+'</span></p>'+
						   '</div><div class="score clearfix"><div  class="deal fl"><span>成交 </span><span >'+data['csum']+'</span><span> 单</span>'+
						   '</div><div class="scoreStar fl clearfix">'+
						   '<span class="fl"><span style="display:none;">评价</span><span class="scores" data-class="'+data['cclass']+'"></span></span>'+
						   '<span class="starPic fl"></span></div></div></div></div></a>';
					       */
					   var list = '<a class="bidLine '+ name + ' '+ mold + '"  href="/index.php/nonstandard/quote/transactions?type='+data['offerid']+'">'+
							   coop +
							   '</a>';
					   if(data['ctype'] == 1){
						   consign=point + list;
					   }else{						   
						   content = content + list;
					   }
				   });
				   $(".bidListTop.sort").show();
				   $("body").css("background-color" , "#f0f1f2");
				   $("body").css("padding" , "106px 0px 0px 0px");
				   $(".bidNone").css('display','none');
				   $(".eachbidLine").html('');
				   $(".eachbidLine").html(consign+ adv + content);
				   $(".eachbidLine") .append(tip);
				   list();
				   coopclass();
			   }
			   if(response['status'] == 3000){
				   $(".eachbidLine").html('');
				   $(".bidListTop.sort").hide();
				   $("body").css("background-color" , "#ffffff");
				   $("body").css("padding" , "70px 0px 0px 0px");
				   $(".bidNone").css('display','block');
			   }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){
			   
		   }
	}); 
}
function RefreshQuote(){
	window.location.reload()
} 
function list(){
	$('.eachbidLine .bidLine').each(function(index,element){
		var scores = $(element).find('.scoreStar .scores').html();
		var number = scores * 2;
		$(element).find('.starPic').addClass('starPic'+number);
	})	
}
function checkpri(pri){
	if(pri <= 20){
		alert('回收价格较低，请多攒点手机一起卖');
	}
}
