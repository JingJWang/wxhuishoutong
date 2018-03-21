/****************获取推广回收奢侈品模块**********/
var recoverinfo ={
		submit:function(){
			var data=$("#recoverinfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=12';
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					$('#recoverinfo')[0].reset();
					recoverinfo.list(1);
				}else{
					alert(response['msg']);
				}
			}
			AjaxRequest(u,d,f);
		},
		list:function(id){
			$("#luxuryRecover .listing").html(''); 
			var u='/index.php/center/settlement/settlementList';
			var d='type=12&page='+id;
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
				$("#luxuryRecover .listing").html(content); 
				 //下面是分页
		        var one_pag = 10;
		        var page='';
		        var now = Number(response['data']['now']);//当前开始数字
		        var num = response['data']['num'];//总条数
		        if( num > 1){
		        	for(var i=1;i<=num;i++){
		        		if(id==i){
		        			page = page + '<a class="figure dig" onclick="recoverinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
		        		}else{
							page = page + '<a class="figure" onclick="recoverinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
						}
		        	}
		        	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
					 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
		        }else{
					page='<a class="figure dig" onclick="recoverinfo.list(1)" href="javascript:;">1</a>';
				}
		        $("#luxuryRecover .pagination").html(page);
			}
			AjaxRequest(u,d,f);
		}
}
recoverinfo.list(1);
/****************获取推广回收手机模块**********/
var phoneinfo={
		submit:function(){
			var data=$("#phoneinfo").serialize();
			var u='/index.php/center/settlement/settlementAdd';
			var d=data+'&type=11';
			var f=function(res){
				var response=eval(res);
					if(response['status'] == request_succ){
						$('#phoneinfo')[0].reset();
						phoneinfo.list(1);
					}else{
						alert(response['msg']);
					}
				}
			AjaxRequest(u,d,f);	
		},
		list:function(id){
			$("#phoneRecover .listing").html(''); 
			var u='/index.php/center/settlement/settlementList';
			var d='type=11&page='+id;
			var f=function(res){
				var response=eval(res);
				if(response['status'] == request_succ){
					var content='';
					$.each(response['data']['list'],function(key,val){
						content = content +'<div class="nese">'+
			            '<div class="num fl">'+val['oid']+'</div>'+
			            '<div class="tel fl">'+val['mobile']+'</div>'+
			            '<div class="name fl">'+val['gname']+'</div>'+
			            '<div class="sprice fl">'+val['price']+'</div>'+
			            '<div class="price fl">'+val['nprice']+'</div>'+
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
		       if( num > 1){
		       	for(var i=1;i<=num;i++){
		       		if(id==i){
		       			page = page + '<a class="figure dig" onclick="phoneinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
		       		}else{
							page = page + '<a class="figure" onclick="phoneinfo.list('+i+')" href="javascript:;">'+i+'</a>&nbsp;';
						}
		       	}
		       	page = page +'<a class="figure" onclick="nextpage();" href="javascript:;">下一页</a>&nbsp;'+
					 '&nbsp;<a class="figure" id="total" data-val="'+num+'" href="javascript:;">共'+num+'页</a>&nbsp;';
		       }else{
					page='<a class="figure dig" onclick="phoneinfo.list(1)" href="javascript:;">1</a>';
				}
		       $("#phoneRecover .pagination").html(page);
			}
			AjaxRequest(u,d,f);	
		}
}
phoneinfo.list(1);
/******分页********/
function nextpage(){
	var page=$(".dig").html();
	var total=$("#total").attr('data-val');
	if(page >= total){
		alert('当前已经是最后一页!');
	}else{
		page = Number(page) + 1; 
		recoverinfo.list(page);
		phoneinfo.list(page);
	}
}