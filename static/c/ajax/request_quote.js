GetBrand();
//获取手机品牌
function GetBrand(){
	var u='/index.php/center/quote/brandList';
	var d='';
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){		
			var content='';
			$.each(data['data'],function(k,v){
				content = content + '<option name="" value="' + v['id'] + '">'+
				 v['name'] +
				 '</option>';
			});
			$("#brandlist").html(content);
			var typeid=data['data']['0']['id'];	
			inputValue("brandid",typeid);
			getTypes(typeid);
		}
		if(data['status'] == request_fall){
			alert('加载品牌列表出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	AjaxRequest(u,d,f);
	
}
//获取型号列表
function getTypes(id){
	$("#brandid").val(id);
	var u='/index.php/center/quote/typeList';
	var d='id='+id;
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			var content='';
			$.each(data['data'],function(k,v){
					content = content + '<tr style="height: 38px" onclick="blueBgLi(this),getOption('+v['id']+')"  data-val="'+v['id']+'"><td width="65%" style="padding-left: 5px;padding-right:15px;cursor:default;">'+v['name']+'</td>'+
					'<td width="17%" style="padding-right:8px;"><input type="text" data-pri="'+v['base']+'" name="" id="base_'+v['id']+'" onkeyup="pri(this)" value="'+v['base']+'" readonly="readonly" class="fl number01"/></td>'+
					'<td width="17%" style="padding-left:8px;"><input type="text" data-pri="'+v['garbage']+'" name="" id="garbage_'+v['id']+'"onkeyup="pri(this)" value="'+v['garbage']+'" readonly="readonly" class="fr number02"/></td>'+
					'</tr>';
			});
			var typeid=data['data']['0']['id'];
			inputValue("typeid",typeid);
			$("#typelist").html(content);
			//$('[data-toggle="tooltip"]').tooltip();
		}
		if(data['status'] == request_fall){
			alert('加载型号列表出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	AjaxRequest(u,d,f);

}
//搜素品牌
function seachBrand() {
	var keyword=$("#brandKey").val();
	if(keyword == ' ' || typeof(keyword) =='undefined'){
		alert("请输入搜索关键词");return false;
	}
	var u='/index.php/center/quote/searchBrand';
    var d="key="+keyword;
	var f=function(res){
			response=eval(res);
			if(response['status'] == request_succ){
				var content='';
				$.each(response['data'],function(k,v){
					content = content + '<li onclick="blueBgLi(this),getTypes('+v['id']+')">'+
					 '<input type="text" name="" value="'+v['name']+'" readonly="readonly"/>'+
					 '</li>';
				});
				$("#typelist").html('');
				$("#brandlist").html('');
				$("#brandlist").html(content);
				var typeid=response['data']['0']['id'];
				getTypes(typeid);
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
//搜素型号
function searchTypes(){
	var typeid=$("#brandid").val();
	var keyword=$("#typeKey").val();
	if(keyword == ' ' || typeof(keyword) =='undefined'){
		alert("请输入搜索关键词");return false;
	}
	var u='/index.php/center/quote/searchType';
	var d='id='+typeid+'&key='+keyword;
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			var content='';
			$.each(data['data'],function(k,v){
				content = content + '<tr style="height: 38px" onclick="blueBgLi(this),getOption('+v['id']+')"  data-val="'+v['id']+'"><td width="65%" style="padding-left: 5px;padding-right:15px;cursor:default;">'+v['name']+'</td>'+
					'<td width="17%" style="padding-right:8px;"><input type="text" data-pri="'+v['base']+'" name="" id="base_'+v['id']+'" onkeyup="pri(this)" value="'+v['base']+'" readonly="readonly" class="fl number01"/></td>'+
					'<td width="17%" style="padding-left:8px;"><input type="text" data-pri="'+v['garbage']+'" name="" id="garbage_'+v['id']+'"onkeyup="pri(this)" value="'+v['garbage']+'" readonly="readonly" class="fr number02"/></td>'+
					'</tr>';
			});
			$("#typelist").html(content);
			//$('[data-toggle="tooltip"]').tooltip();
		}

		if(data['status'] == request_fall){
			alert('加载型号列表出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	AjaxRequest(u,d,f);
}

//获取型号报价方案
function getOption(id){
	inputValue('typeid',id);
	var u='/index.php/center/quote/optionInfo';
	var d='id='+id;
	var f=function(res){
		var data=eval(res);
		var model=data['data']['model'],attr=data['data']['attr'],info=data['data']['info'],plan=data['data']['plan'];
		if(data['status'] == request_succ){	
			var title_common='<div class="basicInfor"><h5>基本信息</h5><div class="properList">';
			var title_important='<div class="basicInfor"><h5>重大故障</h5><div class="properList">';
		    var title_other='<div class="basicInfor"><h5>其他问题</h5><div class="properList">';
		    var content_common='',content_important='',content_other='';
			$.each(attr,function(k,v){
				if(model[k]['type'] == 0){
								content_common = content_common + '<div class="properLine clearfix">'+
								'<div class="title fl"><p>'+model[k]['name']+'</p></div>'+
								'<ul class="clearfix fl" style="width: 1025.78px;">';
								var temp='';
								$.each(v,function(i,n){
									if( plan == ''){
										var pri=0;
									}else{
										var pri=typeof(plan['content'][n]) == 'undefined' ? 0 : plan['content'][n];
									}
									//var pri=typeof(plan['content'][n]) == 'undefined' ? 0 : plan['content'][n];
									temp = temp +'<li><p class="fl">'+info[n]+'</p><div class="revise fl">'+
									'<span class="bra braleft" style="">【</span>'+
									'<input type="text" name="'+n+'" value="'+pri+'" size="8" readonly="readonly"'+
									'class="inputText" onkeyup="checkLength(this),textColor(this)" '+
									'style="">'+
									'<span class="bra braright" style="">】</span>'+
									'</div></li>';
								});
								content_common = content_common +temp+'</ul></div>';
				}
				if(model[k]['type'] == 1){		
								content_important = content_important + '<div class="properLine clearfix">'+
								'<div class="title fl"><p>'+model[k]['name']+'</p></div>'+
								'<ul class="clearfix fl">';
								var temp='';
								$.each(v,function(i,n){
									if( plan == ''){
										var pri=0;
									}else{
										var pri=typeof(plan['content'][n]) == 'undefined' ? 0 : plan['content'][n];
									}
									//var pri=typeof(plan['content'][n]) == 'undefined' ? 0 : plan['content'][n];
									temp = temp +'<li class="clearfix"><p class="fl">'+info[n]+'</p><div class="revise fl">'+
									'<span class="bra braleft">【</span>'+
									'<input type="text" name="'+n+'" value="'+pri+'" size="8" readonly="readonly"'+
									'class="inputText" onkeyup="checkLength(this),textColor(this)" />'+
									'<span class="bra braright">】</span></div></li>';
								});
								content_important = content_important +temp+'</ul></div>';
				}
				if(model[k]['type'] == 2 || model[k]['type'] == 3){
							content_other = content_other + '<div class="properLine clearfix">'+
							'<div class="title fl"><p>'+model[k]['name']+'</p></div>'+
							'<ul class="clearfix fl">';
							var temp='';
							$.each(v,function(i,n){
								if( plan == ''){
									var pri=0;
								}else{
									var pri=typeof(plan['content'][n]) == 'undefined' ? 0 : plan['content'][n];
								}
								//var pri=typeof(plan['content'][n]) == 'undefined' ? 0 : plan['content'][n];
								temp = temp +'<li class="clearfix"><p class="fl">'+info[n]+'</p><div class="revise fl">'+
								'<span class="bra braleft">【</span>'+
								'<input type="text" name="'+n+'" value="'+pri+'" size="8" readonly="readonly"'+
								'class="inputText" onkeyup="checkLength(this),textColor(this)" />'+
								'<span class="bra braright">】</span></div></li>';
							});
							content_other = content_other +temp+'</ul></div>';
				}
			});
			$('.properScroll').html('');
			var info_common = title_common + content_common+'</div></div>';
			var info_important = title_important + content_important + '</div></div>';
			var info_other = title_other + content_other + '</div></div>';
			var content ='<form action="" method="post" id="attrpri">'+info_common + info_important + info_other+'</form>';
			$('.properScroll').html(content);
			$(window).resize();
			textColors();
		}
		if(data['status'] == request_fall){
			alert(data['msg']);
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	if(flag == true){
		AjaxRequest(u,d,f);
	}
}
//保存型号基价市价
function saveTypePri(){
	var typeid= $("#typelist .sign").attr('data-val');
	if(typeof(typeid) == "undefined"){
		alert('没有获取到型号信息');
		return false;
	}
	var base = $("#base_"+typeid).val();
	var garbage = $("#garbage_"+typeid).val();
	var u='/index.php/center/quote/saveTypePri';
	var d='typeid='+typeid+'&garbage='+garbage+'&base='+base;
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			alert(data['msg']);
		}
		if(data['status'] == request_fall){
			alert('修改基础价格出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	AjaxRequest(u,d,f);
}
//保存价格方案
function saveAttrPri(){	
	var typeid= $("#typeid").val();
	if(typeof(typeid) == "undefined" || typeid == ''){
		alert('没有获取到型号信息');
		return false;
	}
	var base = $("#base_"+typeid).val();
	var garbage = $("#garbage_"+typeid).val();
	var u='/index.php/center/quote/saveQuote';
	var d=$("#attrpri").serialize()+'&typeid='+typeid+'&garbage='+garbage+'&base='+base;	
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			alert(data['msg']);
		}
		if(data['status'] == request_fall){
			alert(data['msg']);
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
		AjaxRequest(u,d,f);
}
//根据id 给  input赋值
function inputValue(sign,val){
	$("#"+sign).val(val);
}
