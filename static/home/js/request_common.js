var   request_succ   =   1000;
var   request_fall	 =	 3000;
var   msg_request_fall = '出现异常,请稍后';
function BrandSearch(){
	var url = '/index.php/nonstandard/submitorder/BrandSearch';
	UrlGoto(url);
}
function offer(){
	var url = '/index.php/nonstandard/quote/ViewQuote';
	UrlGoto(url);
}
function myorder(){
	var url = '/index.php/nonstandard/order/ViewOrder';
	UrlGoto(url);
}
function userdata(){
	var url = '/index.php/nonstandard/wxuser/ViewUser';
	UrlGoto(url);
}
function  Editorder(obj){
	var number=$(obj).attr('data-key')
	var url='/index.php/nonstandard/order/EditOrderAttr?id='+number;
	UrlGoto(url);
}
function  EditSkipAttr(){
	var number=$("#number").val();
	var url='/index.php/nonstandard/order/EditOrder?id='+number;
	UrlGoto(url);
}
function ViewQuote(obj){
	var number=$(obj).attr('data-key')
	var url='/index.php/nonstandard/quote/ViewQuote?id='+number;
	UrlGoto(url);
}
function ViewOrder(obj){
	var number=$(obj).attr('data-key')
	var url='/index.php/nonstandard/order/ViewOrderInfo?id='+number;
	UrlGoto(url);
}
function CancalOrder(obj){
	var number=$(obj).attr('data-key')
	var url='/index.php/nonstandard/order/Viewcancel?oid='+number;
	UrlGoto(url);
} 
function  ViewEvaluation(obj){
	var number=$(obj).attr('data-key')
	var type=$(obj).attr("data-val");
	var url='/index.php/nonstandard/wxuser/ViewEvaluation?oid='+number+'&type='+type;
	UrlGoto(url);
}
function transactions(obj) {
	var  orderid=$(obj).attr('data-key');
	location.href="/index.php/nonstandard/quote/transactions?type="+orderid
}
function EvaluationList(){
	var url = '/index.php/nonstandard/wxuser/EvalLIst';
	UrlGoto(url);
}
function  ViewOrderDeal(oid){
	if(oid == ''){
		alert('出现异常');return false;
	}
    var u='/index.php/nonstandard/order/GetOrderStatus';
    var d='oid='+oid
    var f=function(data){
    	var response=eval(data);
    	if(response['status'] ==request_succ){
    		UrlGoto(response.url);
    	}
    	if(response['status'] ==request_fall){
    		alert(response.msg);
    		UrlGoto(response.url);
    	}
    }
    AjaxRequest(u, d, f);
}
function  EditOrderAttr(){
	var u ='/index.php/nonstandard/submitorder/CheckOrderAttr';
	var d = $("#request").serialize();
	var f = function(data){
		var response=eval(data);
		if(response['status'] == request_succ){
			UrlGoto(response.url);
		}
		if(response['status'] == request_fall){
			alert(response.msg);
		}
	}
	AjaxRequest(u,d,f);
}
function GetOrderList(obj,status){
	$(".active11").attr('class','swiper-slide');
	$(obj).attr('class','swiper-slide');
	OrderList(status);
}
function OrderCancel(oid){
	var u='/index.php/nonstandard/order/CheckStatus';
	var d='oid='+oid;
	var f=function(data){
		var  response=eval(data);
		if(response['status'] == request_succ){
			UrlGoto(response['url']);
		}
		if(response['status'] == request_fall){
				alert(response['msg']);
				UrlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
function OrderList(status){
	var  u='/index.php/nonstandard/order/getOrderList';
	var  d='status='+status;
	var content='';
	var f=function(res){
		  var response=eval(res);
		  if(response['status'] == request_succ){
			  $("#list").html('');
			   var list=OrderCssList(status,response['data'],content);
			  $("#list").html(list);
		  }
		  if(response['status'] == request_fall){
			  $("#list").html('');
			  var list=OrderCssList(status,response['data'],content);
			  $("#list").html(list);
			  $(".modeA").append('<p style="text-align:center;">还没有内容!</p>');
		  }
	  }
	  AjaxRequest(u,d,f);
}
function   OrderCssList(key,list,content){
	switch (key) {
			case 'd':
				content='<div class="modeA"><div class="Tit">未完成,等待补充</div>';
				if(list != 0){
					$.each(list,function(n,val){
					    content = content + '<div class="modeBox">'+
	                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
	                    '<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
	                    '<div style="clear: both;"></div><div class="btnBox">'+
	                    '<span class="btnS1" data-key="'+val['number']+'" onclick="Editorder(this);">继续填写</span>'+
	                    '<span class="btnS1" data-key="'+val['number']+'" onclick="Delorder(this);">删除物品</span>'+
	                    '</div></div></div>';
					});
				}
				content = content + '</div>';
				break;
			case 'z':
				content='<div class="modeA"><div class="Tit">已发送报价中</div>';
				if(list != 0){
					$.each(list,function(n,val){
						content = content + '<div class="modeBox ">'+
	                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
	                    '<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
	                    '<div style="clear: both;"></div><div class="btnBox">'+
	                    '<span class="btnS1" data-key="'+val['number']+'" onclick="ViewOrder(this);">订单详情</span>'+
	                    '<span class="btnS1" data-key="'+val['number']+'" onclick="ViewQuote(this);">查看报价</span>'+
	                    '</div></div></div><div class="time">'+
	                    '<div class="timeL"><img src="../../../static/home/images/ben.png"  alt=""/>'+val['time']+'</div>'+
	                    '<div class="timeR">已报<span> '+val['offer']+'</span>人</div></div>';
						});
				}
				content = content + '</div>';
				break;
			case 'x':
				content='<div class="modeA"><div class="Tit">报价结束</div>';
				if(list != 0){
					$.each(list,function(n,val){
						content = content + '<div class="modeBox ">'+
	                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
	                    '<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
	                    '<div style="clear: both;"></div><div class="btnBox">'+
	                    '<span class="btnS1" data-key="'+val['number']+'" onclick="ViewOrder(this);">订单详情</span>'+
	                    '<span class="btnS1" data-key="'+val['number']+'" onclick="ViewQuote(this);">查看报价</span>'+
	                    '</div></div></div><div class="time">'+
	                    '<div class="timeL"><img src="../../../static/home/images/ben.png"  alt=""/>'+val['time']+'</div>'+
	                    '<div class="timeR">已报<span> '+val['offer']+'</span>人</div></div>';
					});
				}
				content = content + '</div>';
				break;
			case 'w':
				content='<div class="modeA"><div class="Tit">等待回收商确认</div>';
				if(list != 0){
					$.each(list,function(n,val){
						content = content + '<div class="modeBox bb">'+
	                    	'<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
	                    	'<div class="conBox2"><div class="phone">iPhone5S</div>'+
	                    	'<div style="clear: both;"></div><div class="WP">'+
	                    	'<span class="WSF">'+val['coopname']+'</span><span>'+val['coopmobile']+'</span>'+
	                    	'</div><div class="btnBox" data-key="'+val['number']+'" onclick="ViewOrder(this);"><span class="btnS1">订单详情</span>'+
	                    	'<span class="btnS2" data-key="'+val['number']+'" onclick="CancalOrder(this)">取消交易</span></div></div>'+
	                    	'</div><div class="time"><div class="timeL">'+
	                    	'<img src="../../../static/home/images/ben.png"  alt=""/>报单时间: '+val['time']+'</div></div>';
					});
				}
				content = content + '</div>';
				break;
			case 's':
				content='<div class="modeA"><div class="Tit">等待交易</div>';
				if(list != 0){
					$.each(list,function(n,val){
						content = content + '<div class="modeBox">'+
		                    '<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
		                    '<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
		                    '<div style="clear: both;"></div><div class="btnBox">'+
		                    '<span class="btnS1" data-key="'+val['number']+'" onclick="ViewOrder(this);">订单详情</span>'+
		                    '<span class="btnS1" data-key="'+val['number']+'" onclick="CancalOrder(this)">取消交易</span>'+
		                    '</div></div></div><div class="time">'+
		                    '<div class="timeL"><img src="../../../static/home/images/ben.png"  alt=""/>'+
		                    '报单时间: '+val['time']+'</div><div class="timeR">已报<span>'+val['offer']+'</span>人</div></div>';
					});
				}
				content = content + '</div>';
				break;
			case 'e':
				content='<div class="modeA bt"><div class="Tit">已成交</div>';
				if(list != 0){
					$.each(list,function(n,val){
						if(val['evaluation'] == 0){
							var p='<span class="btnW btnS4 " id="sloid" data-val="e"  data-key="'+val['number']+'" onclick="ViewEvaluation(this);">去评价</span>';
						}else{
							var p='<span class="btnW btnS4">已评价</span>';
						}
						content = content + '<div class="modeBox bb">'+
	                    		'<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
	                    		'<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
	                    		'<div style="clear: both;"></div><div class="WP">'+
	                            '<span class="WSF">'+val['coopname']+'</span><span>'+val['coopmobile']+'</span>'+
	                            '</div><div style="clear: both;"></div><div class="btnBox">RMB :'+
	                             val['price']+'</div></div></div><div class="time">'+
	                            '<div class="timeL"><img src="../../../static/home/images/time.png"  alt=""/>成交时间: '+val['time']+'</div>'+
	                            '<div class="timeR">'+p+'</div></div>';
					});
				}
				content = content + '</div>';
				break;
			case 'q':
				content='<div class="modeA"><div class="Tit">已取消</div>';
				if(list != 0){
					$.each(list['list'],function(n,val){
						if(val['coopname'] != '' && val['coopmobile'] != '' ){
							if(val['evaluation'] == 0){
								var p='<span class="btnW btnS4" id="sloid" data-val="q" data-key="'+val['number']+'" onclick="ViewEvaluation(this);">去评价</span>';
							}else{
								var p='<span class="btnW btnS4">已评价</span>';
							}
						}else{
							var p='';
						}
						 content = content + '<div class="modeBox bb">'+
						 	'<div class="imgBox"><img src="../../../static/home/images/iphone.png" alt=""/></div>'+
						 	'<div class="conBox2"><div class="phone">'+val['name']+'</div>'+
						 	'<div style="clear: both;"></div><div class="WP">'+
						 	''+val['remark'].replace(',','')+'<span></span>'+
						 	'</div><div style="clear: both;"></div></div></div><div class="time">'+
						 	'<div class="timeL"><img src="../../../static/home/images/time.png"  alt=""/>取消时间: '+val['cantime']+'</div>'+
						 	'<div class="timeR">'+p+'</div></div>';
					});
				}
				content = content + '</div>';
				break;
	}
	return content;
}
function SubmitEvaluation(){
	var fraction=$(".XingFen").html();
	var reason='';
	$(".reasonBox .active").each(function(){
		reason = reason + $(this).html()+',';
	});
	reason = reason.substring(0, reason.length - 1)
	var make=$("#textarea").val();	
	var type=$("#type").val();
	var oid=$("#oid").val();
	var u='/index.php/nonstandard/wxuser/SubmitEvaluation';
	var d='oid='+oid+'&make='+make+'&reason='+reason+'&fraction='+fraction+'&type='+type;
	var f=function(data){
		reponse= eval(data);
		if(reponse['status'] == request_succ){
			UrlGoto(data['url']);
		}
		if(reponse['status'] == request_fall){
			alert(reponse['msg']);
		}
	}
	AjaxRequest(u, d, f);
	
}
function  Delorder(obj){
	var check=confirm('确定删除该订单吗?');
	if(check){		
		var u='/index.php/nonstandard/order/DelOrder';
		var d='id='+$(obj).attr('data-key');
		var f=function(data){
			reponse= eval(data);
			if(reponse['status'] == request_succ){
				OrderList('d');
			}
			if(reponse['status'] == request_fall){
				alert(reponse['msg']);
			}
		}
		AjaxRequest(u, d, f);
	}else{
		return false;
	}
	
}
function EditOrder(status){
	var sfdq_tj=$("#sfdq_tj").val();
	if(sfdq_tj == '' ){
		alert('省份属于必填选项!');return  false;
	}
	var csdq_tj=$("#csdq_tj").val();
	if(csdq_tj == '' ){
		alert('市属于必填选项!');return  false;
	}
	var qydq_tj=$("#qydq_tj").val();
	if(qydq_tj == '' ){
		alert('区属于必填选项!');return  false;
	}
	//校验地址信息不是默认选项
	if(sfdq_tj == '选择省份' || csdq_tj == '选择城市' || qydq_tj == '选择区域'){
		alert('地址信息属于必填选项!');
		return false;
	}
	var latitude=$("#latitude").val();
	if(sfdq_tj == '' ){
		alert('您所在地区没有获取到,请稍等2秒提交!');return  false;
	}
	var longitude=$("#longitude").val();
	if(sfdq_tj == '' ){
		alert('您所在地区没有获取到,请稍等2秒提交!!');return  false;
	}
	var quarters=$("#quarters").val();
	if(quarters == ''){
		lert('小区名称为必填选项!');return  false;
	}
	var oid=$("#oid").val();
	if(oid == ''){
		alert('出现异常,请刷新页面!');return  false;
	}
	var isused = $("input[name='danbao']:checked").val();
	if(isused != 1 && isused != -1){
		alert('是否可用,是必填选项!');
		return false;
	}
	var  name=$("#proname").val();
	var  u='/index.php/nonstandard/order/saveOrder';
	var  d='sfdq_tj='+sfdq_tj+'&csdq_tj='+csdq_tj+'&qydq_tj='+qydq_tj+
			'&latitude='+latitude+'&longitude='+longitude+'&quarters='+quarters+
			'&oid='+oid+'&isused='+isused+'&status='+status+'&orname='+name
    var  f=function(data){
		reponse=eval(data);
		if(reponse['status'] == request_succ){
			UrlGoto(data.url);
		}
		if(reponse['status'] == request_fall){
			alert(data.msg);
		}
	}
	AjaxRequest(u,d,f);
}
function TypeSearch(option){
	  $("#list").css('display','none');
	  var keyword=$("#keyword").val();
	  var u='/index.php/nonstandard/submitorder/GetSearchType';
	  var d='keyword='+keyword+'&type='+$(".pinName").attr('data-key')+'&page='+option;
	  var content='';
	  var f=function(res){
		  var response=eval(res);
		  if(response['status'] == request_succ){
			  if(option == 0){
				  $.each(response['data'],function(n,val){
					  content = content +'<p onclick="TypeChoice(this);"'+
					  'data-key="'+val['id']+'">'+val['name']+'</p>';
				  })
				  $("#result").html(content);
			  }
			  if(option == 1){
				  $.each(response['data'],function(n,val){
					  content = content +'<li onclick="TypeChoice(this);"'+
					  'data-key="'+val['id']+'">'+val['name']+'</li>';
				  })
				  $("#resultlsit").html('');
				  $("#resultlsit").html(content);
			  }
		  }
		  if(response['status'] == request_fall){
			  $("#history").css('display','none');
			  $("#hot").css('display','none');
			  if(option ==1){
				  $("#resultlsit").html('小通没有找到符合条件的机型');
			  }else{
				  $("#list").html('小通没有找到符合条件的机型');
				  $("#list").css('display','block');
				  $("#result").html('');
			  }
			  
		  }
	  }
	  AjaxRequest(u,d,f);
}
function  TypeChoice(obj){
	var typename=$(obj).text();
	var typeid=$(obj).attr('data-key');
	$("#keyword").val(typename);
	var url='/index.php/nonstandard/submitorder/GetTypeName?typeid='+typeid;
	UrlGoto(url);
}
function checkdata(status) {
	var order = '';
	var latitude	  = $("#latitude").val(); //纬度
	var longitude	  = $("#longitude").val();//经度
	//校验是否获取到当前用户的坐标
	if(latitude == '' || longitude == ''){
		alert('没有获取到您的坐标!');
		return false;
	}
	//校验地址
	var province = $("#sfdq_tj").val();
	var city     = $("#csdq_tj").val();
	var county   = $("#qydq_tj").val();
	if( province == '' || city == '' || county == ''){
		alert('地址信息属于必填选项!');
		return false;
	}
	//校验地址信息不是默认选项
	if(province == '选择省份' || city == '选择城市' || county == '选择区域'){
		alert('地址信息属于必填选项!');
		return false;
	}
	//校验详细地址
	var quarters = $("#quarters").val() ;
	if(quarters == ' '){
		alert('小区名称为必填选项!');
		return false;
	}
	//校验是否可用
	var isused = $("input[name='danbao']:checked").val();
	if(isused != 1 && isused != -1){
		alert('是否可用,是必填选项!');
		return false;
	}
	var  d = 'latitude='+latitude+'&longitude='+longitude+'&orderstatus='+
			  status+'&province='+province+'&city='+city+'&county='+county+
		     '&residential_quarters='+quarters+'&isused='+isused;
	var  u = '/index.php/nonstandard/submitorder/submitorder_electronic';
	var  f = function(data){
		 if(data.status == request_succ) {
			 UrlGoto(data.url);
		 }
		 if(data.status  !=  request_succ){
			 alert(data.msg);
		 }
	}
	var check=confirm('请确认您的信息是否填写无误？');
	if(check){	
		AjaxRequest(u,d,f);
	}else{
		return false;
	}
}
function editUserData(){
	var u = '/index.php/nonstandard/wxuser/update_wxuserinfo';
	var residential = $("#residential").val();
	var house_number= $("#house_number").val();
	var province	= $("#sfdq_tj").val();
	var city		= $("#csdq_tj").val();
	var county 		= $("#qydq_tj").val();
	var tytename	= $("#tytename").val();
	if(province == ''){
		alert('为了更好的服务您,省名必填!');
		return false;
	}
	if(city == ''){
		alert('为了更好的服务您,市名必填!');
		return false;
	}
	if(county == ''){
		alert('为了更好的服务您,区名必填!');
		return false;
	}
	/*if(residential == ''){
		alert('为了更好的服务您,小区名称必填!');
		return false;
	}*/
	var d ='residential='+residential+'&house_number='+house_number+'&province='+
	province+'&city='+city+'&county='+county+'&tytename='+tytename;
	var f=function(res){
		var response =eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			UrlGoto(response['url']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
} 
function Choice(qid,oid){
	var u="/index.php/nonstandard/quote/ChoiceQuote";
	var d="qid="+qid+'&oid='+oid;
    var f=function(data){
		response=eval(data);
		if(response['status'] == request_succ){
			
			UrlGoto(response['url']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
    
	AjaxRequest(u,d,f);
} 
function cancel(obj){
	var content =  $(obj).html();
	$(".active").removeClass("active");
	$(obj).addClass("active");
}
function subcancel(orderid){
	var u = '/index.php/nonstandard/order/cancel_order';
	var content = $(".active").html();
	var make=$("#make").val();
	var d = 'orderid='+orderid+'&content='+content+'&make='+make;
	var f=function(res){
			var reponse=eval(res);
			if(reponse['status'] == request_succ){
				UrlGoto(reponse['url']);
			}
			if(reponse['status'] == request_fall){
				alert(reponse['msg']);
			}
		}
	AjaxRequest(u,d,f);
}
var UrlGoto=function(url) {
	if(url != ''){
		location.href=url;
	}else{
		return '路径为空!';	
	}
}
var  AjaxRequest=function(u,d,f){	
	var result= '';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	$("#turn_gif_box").css('display','block');
        },
        success:function(res){
        	  f(res);
        },
        complete: function(res){
       	 	$("#turn_gif_box").css('display','none'); 
       	 	return result;
       	},
        error:function(msg){
           
        }
    });
	
}
function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}
