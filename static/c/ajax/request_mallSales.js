var Sales={
		submit:function(){
			var data=$("#datainfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=22';
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					$('#datainfo')[0].reset();
					Sales.list(1);
				}else{
					alert(response['msg']);
				}
			}
			AjaxRequest(u,d,f);
		},
		list:function(id){
				$("#luxurySale .listing").html(''); 
				var u='/index.php/center/settlement/settlementList';
				var d='type=22&page='+id;
				var f=function(res){
					var response=eval(res);
					if(response['status'] == request_succ){
						var content='';
						$.each(response['data']['list'],function(key,val){
							content = content +'<div class="nese">'+
					            '<div class="num fl">'+val['oid']+'</div>'+
					            '<div class="tel fl">'+val['mobile']+'</div>'+
					            '<div class="name fl">'+val['gname']+'</div>'+
					            '<div class="price fl">'+val['nprice']+'</div>'+
					            '<div class="sprice fl">'+val['price']+'</div>'+
					            '<div class="other fl">'+val['oprice']+'</div>'+
					            '<div class="htime fl">'+$.myTime.UnixToDate(val['rtime'])+'</div></div>';
						}); 
					}
					$("#luxurySale .listing").html(content); 
					 //下面是分页
			        var one_pag = 10;
			        var page='';
			        var now = Number(response['data']['now']);//当前开始数字
			        var num = response['data']['num'];//总条数
			        if( num > 1){
			        	for(var i=1;i<=num;i++){
			        		if(id==i){
			        			page = page + '<a class="figure dig" onclick="Sales.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
			        		}else{
								page = page + '<a class="figure" onclick="Sales.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
							}
			        	}
			        	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
						 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
			        }else{
						page='<a class="figure dig" onclick="Sales.list(1)" href="javascript:;">1</a>';
					}
			        $("#luxurySale .pagination").html(page);
				}
				AjaxRequest(u,d,f);
			}
}
Sales.list(1);
/****************获取推广商城手机模块**********/
var phonesaleinfo={
		submit:function(){
			var data=$("#phonesaleinfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=21';
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					$('#phonesaleinfo')[0].reset();
					phonesaleinfo.list(1);
				}else{
					alert(response['msg']);
				}
			}
			AjaxRequest(u,d,f);
		},
		list:function(id){
			$("#phoneSale .listing").html(''); 
			var u='/index.php/center/settlement/settlementList';
			var d='type=21&page='+id;
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					var content='';
					$.each(response['data']['list'],function(key,val){
						content = content +'<div class="nese">'+
			            '<div class="num fl">'+val['oid']+'</div>'+
			            '<div class="tel fl">'+val['mobile']+'</div>'+
			            '<div class="name fl">'+val['gname']+'</div>'+
			            '<div class="price fl">'+val['nprice']+'</div>'+
			            '<div class="sprice fl">'+val['price']+'</div>'+
			            '<div class="other fl">'+val['oprice']+'</div>'+
			            '<div class="htime fl">'+$.myTime.UnixToDate(val['rtime'])+'</div></div>';
					}); 
				}
				$("#phoneSale .listing").html(content); 
				 //下面是分页
		       var one_pag = 10;
		       var page='';
		       var now = Number(response['data']['now']);//当前开始数字
		       var num = response['data']['num'];//总条数
		       var numpage = response['data']['num'];//总页数
		       if( num > 1){
		       	for(var i=1;i<=num;i++){
		       		if(id==i){
		       			page = page + '<a class="figure dig" onclick="phonesaleinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
		       		}else{
							page = page + '<a class="figure" onclick="phonesaleinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
						}
		       	}
		       	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
					 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
		       }else{
					page='<a class="figure dig" onclick="phonesaleinfo.list(1)" href="javascript:;">1</a>';
				}
		       $("#phoneSale .pagination").html(page);
			}
			AjaxRequest(u,d,f);
		}
}
phonesaleinfo.list(1);
/****************获取推广商城其他模块**********/
var shopSaleinfo={
		submit:function(){
			var data=$("#shopSaleinfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=23';
			var f=function(res){
				var response=eval(res);
					if(response['status'] == request_succ){
						$('#shopSaleinfo')[0].reset();
						shopSaleinfo.list(1);
					}else{
						alert(response['msg']);
					}
				}
			AjaxRequest(u,d,f);
		},
		list:function(id){
			$("#shopSale .listing").html(''); 
			var u='/index.php/center/settlement/settlementList';
			var d='type=23&page='+id;
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					var content='';
					$.each(response['data']['list'],function(key,val){
						content = content +'<div class="nese">'+
			            '<div class="num fl">'+val['oid']+'</div>'+
			            '<div class="tel fl">'+val['mobile']+'</div>'+
			            '<div class="name fl">'+val['gname']+'</div>'+
			            '<div class="price fl">'+val['nprice']+'</div>'+
			            '<div class="sprice fl">'+val['price']+'</div>'+
			            '<div class="other fl">'+val['oprice']+'</div>'+
			            '<div class="htime fl">'+$.myTime.UnixToDate(val['rtime'])+'</div></div>';
					}); 
				}
				$("#shopSale .listing").html(content); 
				 //下面是分页
		       var page='';
		       var now = Number(response['data']['now']);//当前开始数字
		       var num = response['data']['num'];//总页数
		       if( num > 1){
		       	for(var i=1;i<=num;i++){
		       		if(id==i){
		       			page = page + '<a class="figure dig" onclick="shopSaleinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
		       		}else{
							page = page + '<a class="figure" onclick="shopSaleinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
						}
		       	}
		       	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
					 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
		       }else{
					page='<a class="figure dig" onclick="shopSaleinfo.list(1)" href="javascript:;">1</a>';
				}
		       $("#shopSale .pagination").html(page);
			}
			AjaxRequest(u,d,f);
		}
}
shopSaleinfo.list(1);
/****************获取商城其他列表**********/

/******分页********/
function nextpage(){
	var page=$(".dig").html();
	var total=$("#total").attr('data-val');
	if(page >= total){
		alert('当前已经是最后一页!');
	}else{
		page = Number(page) + 1; 
		Sales.list(page);
		phonesaleinfo.list(page);
		shopSaleinfo.list(page);
	}
}