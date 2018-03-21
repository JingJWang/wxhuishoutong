//调用成交交易额列表
getVolume('1');
//添加交易数据
function addVolume(){
	var time=$("#start").val();
	if(time == '' || typeof(time) == 'undefined'){
		alert('没有获取到时间');
		return false;
	}
	var volume=$("#addvolume").val();
	if(volume == '' || typeof(volume) == 'undefined'){
		alert('没有获取到交易额');
		return false;
	}
	var number=$("#addnumber").val();
	if(number =='' ||  typeof(number) == 'undefined'){
		alert('没有获取到成交单数');
		return false;
	}
	var u='/index.php/center/manageData/addVolume';
	var d='data='+time+'&volume='+volume+'&number='+number;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//读取交易记录
function getVolume(p){
	if(p == '' || typeof(p) ==  'undefined'){
		alert('没有获取到当前页码');
	}
	var u='/index.php/center/manageData/getVolume';
	var d='p='+p;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			if($.isArray(response['data']['list'])){
				var content='';
				$.each(response['data']['list'],function(k,v){
					content = content +'<div class="protect">'+
					'<div class="timedata">'+v['start']+'</div>'+
					'<div class="further"><span>成交单数</span>'+
					'<span class="saith">'+v['number']+'</span>'+
					'<span>单，成交金额</span><span class="sum">'+v['volume']+'元</span>'+
					'</div><div class="estus">'+v['status']+'</div>'+
					'<div class="pick"><span class="amend" onclick="seeVolume('+v['id']+');">修改</span>'+
				    '<span class="delete" onclick="displayVolume('+v['id']+');">删除</span></div></div>';
				});
				$("#volume").html(content);
			}
			/*if( p == 1){
				var page='';
				for(var i=response['data']['total'];i > 0;i--){
					content='';
					 if( p == i){
						 content = content + '<a class="number active" href="javascript:;">'+i+'</a>';
					 }else{
						 content = content + '<a class="number " href="javascript:;">'+i+'</a>';
					 }
				}
				page='<div class="embody">'+content+'</div>';
				$("#volumepage").html(page);
			}*/
			if(response['data']['total'] == 1){
				var page = '<a class="number active" onclick="volumeNextPage(this,1)" href="javascript:;">1</a>';
				$("#volumepage").html(page);
			}
			if(p == 1 && response['data']['total'] > 1){
				var page='';
				var total='<a class="number" id="totalVolume" href="javascript:;" data-total="'+response['data']['total']+'">共'+response['data']['total']+'页</a>';
				for(var i=1;i <= response['data']['total'];i++){
					if(p == i){
						page = page + '<a id="V_page_'+i+'" data-page="'+i+'" class="number active" href="javascript:;" onclick="volumeNextPage(this,'+i+')">'+i+'</a>';
					}else{
						if(i <= 4){
							page = page + '<a id="V_page_'+i+'" data-page="'+i+'" style="display:block;" class="number " onclick="volumeNextPage(this,'+i+')" href="javascript:;">'+i+'</a>';
						}else{
							page = page + '<a id="V_page_'+i+'" data-page="'+i+'" style="display:none;" class="number " onclick="volumeNextPage(this,'+i+')" href="javascript:;">'+i+'</a>';
						}
					}
				}
				page= page + total;
				$("#volumepage").html(page);
			}
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//分页
function volumeNextPage(obj,page){
	var  option=$("#volumepage .active").attr('data-page');
	var total=$("#totalVolume").attr('data-total');
	//向前
	if(page > option){
		if(page > total){
			alert('当前已经是最后一页!');
		}else{
			if(page >= 4){
				start =page -3;
				$("#V_page_"+start).css('display','none');
				stop =page+1
				$("#V_page_"+stop).css('display','block');
				$(obj).addClass('active').siblings().removeClass('active');
			}else{
				$(obj).addClass('active').siblings().removeClass('active');
			}
			getVolume(page);
		}
	}else{
			start =page - 1;
			$("#V_page_"+start).css('display','block');
			stop =page+3
			$("#V_page_"+stop).css('display','none');
			$(obj).addClass('active').siblings().removeClass('active');
			getVolume(page);
	}
}
//删除当前的成交记录
function delVolume(id){
	if(id =='' || typeof(id) == 'undefied'){
		alert('没有获取到当前记录的id');
	}
	var u='/index.php/center/manageData/delVolume';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			location.reload();
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//根据id读取单条记录
function seeVolume(id) {
	if(id =='' || typeof(id) == 'undefied'){
		alert('没有获取到当前记录的id');
	}
	var u='/index.php/center/manageData/seeVolume';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$('.stuff #start1').val(response['data']['start']);
			$('#upvolume').val(response['data']['volume']);
			$('#upnumber').val(response['data']['number']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	display_up(id);
	AjaxRequest(u,d,f);
}
//根据id 修改
function upVolume(id){
	if(id =='' || typeof(id) == 'undefied'){
		alert('没有获取到当前记录的id');
	}
	var time=$("#start1").val();
	if(time == '' || typeof(time) == 'undefined'){
		alert('没有获取到时间');
		return false;
	}
	var volume=$("#upvolume").val();
	if(volume == '' || typeof(volume) == 'undefined'){
		alert('没有获取到交易额');
		return false;
	}
	var number=$("#upnumber").val();
	if(number =='' ||  typeof(number) == 'undefined'){
		alert('没有获取到成交单数');
		return false;
	}
	var u='/index.php/center/manageData/upVolume';
	var d='data='+time+'&volume='+volume+'&number='+number+'&id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			location.reload();
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}


//添加成交记录
function addRecord(){
	var date=$("#start2").val();
	if(date == '' || typeof(date) == 'undefined'){
		alert('没有获取到日期');
		return false;
	}
	var start=$("#startRecord").find("option:selected").text();
	if(start == '' || typeof(start) == 'undefined'){
		alert('没有获取到开始时间');
		return false;
	}
	var stop=$("#stopRecord").find("option:selected").text();
	if(stop == '' || typeof(stop) == 'undefined'){
		alert('没有获取到结束时间');
		return false;
	}
	var content=$("#Recordinfo").serialize();
	var u='/index.php/center/manageData/addRecord';
	var d='date='+date+'&start='+start+'&stop='+stop+'&'+$("#Recordinfo").serialize();
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			location.reload();
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}

//读取成交记录
function getRecord(p) {
	var u='/index.php/center/manageData/getRecord';
	var d='p='+p;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			var content='';
			if($.isArray(response['data']['list'])){
				$.each(response['data']['list'],function(k,v){
					content =  content + '<div class="nese"><div class="owned">'+
					'<div class="prot"><div class="timedata">'+v['time']+'</div>'+
					'<div class="further"><span>交易记录</span>'+
					'</div><div class="estus">'+v['status']+'</div>'+
					'<div class="pick"><span class="look" onclick="seeRecord(this,'+v['id']+',1)">查看</span>'+
					'<span class="amend" onclick="seeRecord(this,'+v['id']+',2)">修改</span>'+
					'<span class="delete" onclick="displayDelRecord(this,'+v['id']+')">删除</span>'+
					'</div></div></div><div class="data" id="infoRecord_'+v['id']+'"></div></div>';
				});
				$("#ListRecord").html(content);
			}
			if(response['data']['total'] == 1){
				var page = '<a class="number active" onclick="recordNextPage(this,1)" href="javascript:;">1</a>';
				$("#pageRecord").html(page);
			}
			if(p == 1 && response['data']['total'] > 1){
				var page='';
				var total='<a class="number" id="totalRecord" href="javascript:;" data-total="'+response['data']['total']+'">共'+response['data']['total']+'页</a>';
				for(var i=1;i <= response['data']['total'];i++){
					if(p == i){
						page = page + '<a id="R_page_'+i+'" data-page="'+i+'" class="number active" href="javascript:;" onclick="recordNextPage(this,'+i+')">'+i+'</a>';
					}else{
						if(i <= 4){
							page = page + '<a id="R_page_'+i+'" data-page="'+i+'" style="display:block;" class="number " onclick="recordNextPage(this,'+i+')" href="javascript:;">'+i+'</a>';
						}else{
							page = page + '<a id="R_page_'+i+'" data-page="'+i+'" style="display:none;" class="number " onclick="recordNextPage(this,'+i+')" href="javascript:;">'+i+'</a>';
						}
					}
				}
				page= page + total;
				$("#pageRecord").html(page);
			}
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//分页
function recordNextPage(obj,page){
	var  option=$("#pageRecord .active").attr('data-page');
	var total=$("#totalRecord").attr('data-total');
	//向前
	if(page > option){
		if(page > total){
			alert('当前已经是最后一页!');
		}else{
			if(page >= 4){
				start =page -3;
				$("#R_page_"+start).css('display','none');
				stop =page+1
				$("#R_page_"+stop).css('display','block');
				$(obj).addClass('active').siblings().removeClass('active');
			}else{
				$(obj).addClass('active').siblings().removeClass('active');
			}
			getRecord(page);
		}
	}else{
			start =page - 1;
			$("#R_page_"+start).css('display','block');
			stop =page+3
			$("#R_page_"+stop).css('display','none');
			$(obj).addClass('active').siblings().removeClass('active');
			getRecord(page);
	}
}
//查看交易记录
function  seeRecord(obj,id,type){
	var u='/index.php/center/manageData/recordInfo';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			if($.isArray(response['data'])){
				var content='';
				$.each(response['data'],function(k,v){
					content = content + '<div class="stuff active">'+
					'<input class="user" name="name[]" value="'+v['name']+'" readonly="readonly"/>'+
					'<input class="contact" name="mobile[]" value="'+v['mobile']+'" readonly="readonly"/>'+
					'<input class="while"  name="time[]" value="'+v['time']+'" readonly="readonly"/>'+
					'<input class="model"  name="type[]" value="'+v['type']+'" readonly="readonly"/>'+
					'<input class="turnover"  name="moeny[]" value="'+v['moeny']+'" readonly="readonly"/>'+
					'<input class="rated"  name="content[]"  value="'+v['content']+'" readonly="readonly"/>'+
					'</div>';
				});	
				var button='<div class="include" align="center">'+
					'<input type="button" value="提交" onclick="upRecord('+id+')" class="commite"/></div>';
				content = '<form id="upRecord_'+id+'">'+content+'</form>'+button;
				$("#infoRecord_"+id).html(content);
			}
			if(type == 1){
				displaySesRecord(obj);
			}else{
				displayUpRecord(obj);
			}
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//修改当前记录
function upRecord(id){
	var u='/index.php/center/manageData/upRecord';
	var d='id='+id+'&'+$("#upRecord_"+id).serialize();
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			fase();
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//删除当前记录
function delRecord(id){
	var u='/index.php/center/manageData/delRecord';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			sure();
			var  page=$("#pageRecord .active").attr('data-page');
			getRecord(page);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
