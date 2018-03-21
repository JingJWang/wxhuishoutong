//当前搜素的型号id
var typeid='';
var attrid='';
GetBrand();
//获取手机品牌
function GetBrand(){
	var u='/index.php/center/option/getBrand';
	var d='';
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){		
			var content='';
			$.each(data['data'],function(k,v){
				content = content + '<li onclick="blueBgLi(this),getTypes('+v['id']+')">'+
				 '<input type="text" name="" value="'+v['name']+'" readonly="readonly"/>'+
				 '<span class="delete" style="display: none;" onclick="deleteLi(this)"></span>'+
				 '</li>';
			});
			$("#brandlist").html(content);
			typeid=data['data']['0']['id'];
			getTypes(typeid);
		}
		if(data['status'] == request_fall){
			alert('加载品牌列表出现异常!');
		}
		if(typeof(data['url']) !== 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	if(flag == true){
		AjaxRequest(u,d,f);
	}
	
}
//获取型号信息
function getTypes(id){
	var u='/index.php/center/option/getTypes';
	var d='id='+id;
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			var content='';
			$.each(data['data'],function(k,v){
					content = content +'<li onclick="blueBgLi(this),getOption('+v['id']+')" data-toggle="tooltip" data-placement="left" title="'+v['name']+'">'+
						'<input type="text" name="" value="'+v['name']+'" readonly="readonly"/>'+
						'<span class="delete" style="display: none;" onclick="deleteLi(this)"></span></li>';
			});
			typeid=id;
			attrid=data['data']['0']['id'];
			$("#typelist").html(content);
			$('[data-toggle="tooltip"]').tooltip();
		}
		if(data['status'] == request_fall){
			alert('加载型号列表出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	if(flag == true){
		AjaxRequest(u,d,f);
	}	
}
//获取属性信息
function getOption(id){
	var u='/index.php/center/option/getOption';
	var d='id='+id;
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			var title_common='<div class="basicInfor"><h5>基本信息</h5><div class="properList" >';
			var title_important='<div class="basicInfor"><h5>重大故障</h5><div class="properList" >';
			var title_content='<div class="basicInfor"><h5>其他问题</h5><div class="properList" >';
			var content_common='';
			var content_important='';
			var content_other='';
			var content_edition='';
			$.each(data['data']['model'],function(k,v){
				if (v['type'] == 0 ){		
							content_common = content_common +'<div class="properLine clearfix"><div class="title fl">'+
							'<input type="text" name="" sign="'+v['alias']+'"  data-id="'+v['id']+'" value="'+v['name']+'" size="10" readonly="readonly" class="inputText" onkeyup="fixedLength(this)" />'+
							'<span class="delete" style="display: none;" onclick="deleteList(this)"></span></div>'+
							'<ul class="clearfix fl">';
							 var info = '';
							 if(Object.prototype.toString.call(v['content']) === '[object Array]'){
								$.each(v['content'],function(i,n){
									if(n['sign'] == 1){
										var styles='inputText sign';
									}else{
										var styles='inputText';
									}
									info = info + '<li><input type="text" sign-id="'+n['id']+'" data-id="'+v['id']+'"  data-val="'+v+'" value="'+data['data']['option'][n['id']]+'" size="10" readonly="readonly" onclick="addsign(this)" class="'+styles+'" onkeyup="checkLength(this)">'+
										'<span class="delete" style="display: none;" onclick="delAttr(this)"></span></li>';
									
								});
							}
							info = info +'<li class="addLi" style="display: none;" onclick="porperAddLi(this)"><a href="javascript:;"><span></span></a></li></ul></div>';
							content_common = content_common  + info ;
			}
			if(v['type'] == 1){
							content_important = content_important +'<div class="properLine clearfix"><div class="title fl">'+
							'<input type="text" name="" sign="'+v['alias']+'"  data-id="'+v['id']+'" value="'+v['name']+'" size="10" readonly="readonly" class="inputText" onkeyup="fixedLength(this)" />'+
							'<span class="delete" style="display: none;" onclick="deleteList(this)"></span></div>'+
							'<ul class="clearfix fl">';
							var info = '';
							 if(Object.prototype.toString.call(v['content']) === '[object Array]'){
								$.each(v['content'],function(i,n){
									if(n['sign'] == 1){
										var styles='inputText sign';
									}else{
										var styles='inputText';
									}
									info = info + '<li><input type="text" sign-id="'+n['id']+'" data-id="'+v['id']+'"  value="'+data['data']['option'][n['id']]+'" size="10" readonly="readonly" class="'+styles+'" onclick="addsign(this)" onkeyup="checkLength(this)">'+
										'<span class="delete" style="display: none;" onclick="delAttr(this)"></span></li>';
								});
							}
							info = info +'<li class="addLi" style="display: none;" onclick="porperAddLi(this)"><a href="javascript:;"><span></span></a></li></ul></div>';
							content_important = content_important  + info;
			}
			if(v['type'] == 2 || v['type'] == 3){
							content_other = content_other +'<div class="properLine clearfix"><div class="title fl">'+
							'<input type="text" name="" sign="'+v['alias']+'"  data-id="'+v['id']+'" value="'+v['name']+'" size="10" readonly="readonly" class="inputText" onkeyup="fixedLength(this)" />'+
							'<span class="delete" style="display: none;" onclick="deleteList(this)"></span></div>'+
							'<ul class="clearfix fl">';
							var info = '';
							 if(Object.prototype.toString.call(v['content']) === '[object Array]'){
								$.each(v['content'],function(i,n){
									if(n['sign'] == 1){
										var styles='inputText sign';
									}else{
										var styles='inputText';
									}
									info = info + '<li><input type="text" sign-id="'+n['id']+'" data-id="'+v['id']+'" name="'+v['alias']+'[]" value="'+data['data']['option'][n['id']]+'" size="10" readonly="readonly" class="'+styles+'" onclick="addsign(this)" nkeyup="checkLength(this)">'+
										'<span class="delete" style="display: none;" onclick="delAttr(this)"></span></li>';
								});
							}
							info = info +'<li class="addLi" style="display: none;" onclick="porperAddLi(this)"><a href="javascript:;"><span></span></a></li></ul></div>';
							content_other = content_other  + info ;
				}
			});
			content_common  = title_common + content_common +'</div></div>';
			content_important =title_important + content_important +'</div></div>';
			content_other = title_content + content_other +'</div></div>';
			content =content_common+content_important+content_other;
			$("#optionlist").html('');
			$("#optionlist").html(content);
			mobile();
			attrid=id;
		}
		if(data['status'] == request_fall){
			alert('加载型号列表出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			urlGoto(data['url']);
		}
	}
	if(flag == true){
		AjaxRequest(u,d,f);
	}
}
//搜素品牌
function searchBrand(){
	var keyword=$("#searchbrand").val();
	if(keyword == ' ' || typeof(keyword) =='undefined'){
		alert("请输入搜索关键词");return false;
	}
	var u='/index.php/center/option/searchBrand';
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
				$("#brandlist").html(content);
				typeid=response['data']['0']['id'];
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
//搜索型号
function searchType(){
	var keyword=$("#searchtype").val();
	if(keyword == ' ' || typeof(keyword) =='undefined'){
		alert("请输入搜索关键词");return false;
	}
	alert(keyword + typeid);
	var u='/index.php/center/option/searchType';
    var d="key="+keyword+"&type="+typeid;
	var f=function(res){
			response=eval(res);
			if(response['status'] == request_succ){
				var content='';
				$.each(response['data'],function(k,v){
						content = content +'<li onclick="blueBgLi(this),getOption('+v['id']+')">'+
							'<input type="text" name="" value="'+v['name']+'" readonly="readonly"/>'+
							'<span class="delete" style="display: none;" onclick="deleteLi(this)"></span></li>';
				});
				$("#typelist").html(content);
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
//新增属性内容
function  saveOption(content){	    
	    var u='/index.php/center/option/saveOption';
		var d=content;
		var f=function(res){
			var response=eval(res);
			if(response['status'] == request_fall){
				alert(response['msg']);
			}
			if(response['status'] == request_succ){
				getOption(typeid);
			}
			if(typeof(response['url']) != 'undefined' && response['url'] !=''){
				urlGoto(response['url']);
			}
		}
		AjaxRequest(u,d,f);	   
}
//保存选中的属性
function  Saveattr(content){	    
    var u='/index.php/center/option/optionSave';
	var d='info='+content+'&attrid='+attrid;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
		if(response['status'] == request_succ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);	   
}
//保存选中内容
function Optionsave(){
	var content='';
	$('#optionlist .sign').each(function(index,element){
		var name=$(this).attr('sign-id');
		var id=$(this).parents('ul').siblings('.title').find('input').attr('sign');
		content = content + id +':'+name+'|';		
	});
	Saveattr(content);
}
//获取新增加的属性
function saveAttr(){
	var content='';
	$('#optionlist .properList .newaddLine').each(function(index,element){
		var signs ='';
		$(element).find('ul .newadd').each(function(index02,element02){
			var sign=$(element02).val();
			signs = signs + sign+',';
		})
		var title = $(element).find('.newadd').parents('ul').siblings('.title').find('input').val();
		var id=$(element).find('.newadd').parents('ul').siblings('.title').find('input').attr("sign");
		var val=id+ '='+signs;
		content = content + val+'&';
	});
	saveOption(content);
};
//删除属性
function delAttr(obj){
	var id=$(obj).siblings('input').attr('sign-id');
	var u='/index.php/center/option/delAttr';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_fall){
			alert(response['msg']);
			return false;
		}
		if(response['status'] == request_succ){
			alert(response['msg']);
			deleteLi(obj);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);	   
}


