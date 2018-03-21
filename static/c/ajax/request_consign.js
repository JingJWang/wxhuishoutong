/****************获取推广寄售奢侈品模块**********/
var luxuryinfo={
		submit:function(){
			var data=$("#luxuryinfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=32';
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					$('#luxuryinfo')[0].reset();
					luxuryinfo.list(1);
				}else{
					alert(response['msg']);
				}
			}
			AjaxRequest(u,d,f);
		},
		list:function(id){
			$("#luxuryRecover .listing").html(''); 
			var u='/index.php/center/settlement/settlementList';
			var d='type=32&page='+id;
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					var content='';
					$.each(response['data']['list'],function(key,val){
						console.log(val);
						content = content +'<div class="nese">'+
				            '<div class="num fl">'+val['oid']+'</div>'+
				            '<div class="tel fl">'+val['mobile']+'</div>'+
				            '<div class="name fl">'+val['gname']+'</div>'+
				            '<div class="price fl">'+val['price']+'</div>'+
				            '<div class="sprice fl">'+val['nprice']+'</div>'+
				            '<div class="other fl">'+val['oprice']+'</div>'+
				            '<div class="htime fl">'+$.myTime.UnixToDate(val['rtime'])+'</div>'+
				            '<div class="dtime fl">'+$.myTime.UnixToDate(val['otime'])+'</div></div>';
					}); 
				}
				$("#luxuryRecover .listing").html(content); 
				 //下面是分页
		        var one_pag = 10;
		        var page='';
		        var now = Number(response['data']['now']);//当前开始数字
		        var num = response['data']['num'];//总条数
		        var numpage = response['data']['num'];//总页数
		       // page(one_pag,now,num);
		        
		        if( num > 1){
		        	for(var i=1;i<=num;i++){
		        		if(id==i){
		        			page = page + '<a class="figure dig" onclick="luxuryinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
		        		}else{
							page = page + '<a class="figure" onclick="luxuryinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
						}
		        	}
		        	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
					 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
		        }else{
					page='<a class="figure dig" onclick="luxuryinfo.list(1)" href="javascript:;">1</a>';
				}
		        $("#luxuryRecover .pagination").html(page);
			}
			AjaxRequest(u,d,f);
		}
}
luxuryinfo.list(1);
/****************获取推广寄售手机模块**********/
var consigninfo={
		submit:function(){
			var data=$("#consigninfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=31';
			var f=function(res){
				var response=eval(res);
					if(response['status'] == request_succ){
						$('#consigninfo')[0].reset();
						consigninfo.list(1);
					}else{
						alert(response['msg']);
					}
				}
			AjaxRequest(u,d,f);
		},
		list:function(id){
			$("#phoneRecover .listing").html(''); 
			var u='/index.php/center/settlement/settlementList';
			var d='type=31&page='+id;
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					var content='';
					$.each(response['data']['list'],function(key,val){
						content = content +'<div class="nese">'+
			            '<div class="num fl">'+val['oid']+'</div>'+
			            '<div class="tel fl">'+val['mobile']+'</div>'+
			            '<div class="name fl">'+val['gname']+'</div>'+
			            '<div class="price fl">'+val['price']+'</div>'+
			            '<div class="sprice fl">'+val['nprice']+'</div>'+
			            '<div class="other fl">'+val['oprice']+'</div>'+
			            '<div class="htime fl">'+$.myTime.UnixToDate(val['rtime'])+'</div>'+
			            '<div class="dtime fl">'+$.myTime.UnixToDate(val['otime'])+'</div></div>';
					}); 
				}
				$("#phoneRecover .listing").html(content); 
				 //下面是分页
		       var one_pag = 10;
		       var page='';
		       var now = Number(response['data']['now']);//当前开始数字
		       var num = response['data']['num'];//总条数
		       var numpage = response['data']['num'];//总页数
		       if( num > 1){
		       	for(var i=1;i<=num;i++){
		       		if(id==i){
		       			page = page + '<a class="figure dig" onclick="consigninfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
		       		}else{
							page = page + '<a class="figure" onclick="consigninfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
						}
		       	}
		       	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
					 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
		       }else{
					page='<a class="figure dig" onclick="consigninfo.list(1)" href="javascript:;">1</a>';
				}
		       $("#phoneRecover .pagination").html(page);
			}
			AjaxRequest(u,d,f);
		}
}
consigninfo.list(1);
/******分页********/
function nextpage(){
	var page=$(".dig").html();
	var total=$("#total").attr('data-val');
	if(page >= total){
		alert('当前已经是最后一页!');
	}else{
		page = Number(page) + 1; 
		luxuryinfo(page);
		consigninfo.list(page);
	}
}