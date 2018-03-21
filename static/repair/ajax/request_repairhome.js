//当前搜素的型号id
var typeid='';
var attrid='';
GetBrand();
//获取手机品牌
function GetBrand(){
	var u='/index.php/repair/repairHome/getBrand';
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
		$('.blueBg').find('li').eq(0).addClass('blueBgClick');
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
	var u='/index.php/repair/repairHome/getTypes';
	var d='id='+id;
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){	
			var content='';
			$.each(data['data'],function(k,v){
					content = content +'<li onclick="blueBgLi(this),getOption('+v['id']+',\''+v['name']+'\')" data-toggle="tooltip" data-placement="left" title="'+v['name']+'">'+
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
function getOption(id,name){
	var u='/index.php/repair/repairHome/getOption';
	var d='id='+id+'&name='+name;
	var pname='';
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){
			var title_common='<div class="basicInfor"><h5>基本信息</h5><div class="properList" ><input type="hidden" id="phoneid" value="'+id+'"><input type="hidden" id="goodsname" value="'+name+'">';
			var title='';
			$.each(data['data']['big'],function(k,v){
				title += '<div class="properLine clearfix"><div class="title fl"><input type="text" name="" id="'+v['id']+'"value="'+v['name']+'" size="10" readonly="readonly" class="inputText" onkeyup="fixedLength(this)" />'+
				'<span class="delete" style="display: none;" onclick="deleteList(this)"></span></div><ul class="clearfix fl conte">';
				$.each(data['data']['con'],function(key,val){
					if(val[0]==v['id']){
						$.each(data['data']['small'],function(keys,vals){
							if(vals['pid']==val[1]){
								pname=vals['pname'];
							}
					    })
						if(val[3] == 1){
							title += '<li><input type="text" id="'+val['1']+'" sign-id='+val['0']+'  readonly="readonly"  value="'+pname+'" data_size="10">'+
							'<input type="text" class="price" style="float:left;width:80px;margin-left:10px;float:right" readonly="readonly"  value="'+val[2]/100+'">'+
							'<span class="delete1" style="display: none;" onclick="delAttr(this)"></span></li>';
						}
					}
				});
				title +='<ul/></div>';
			});
			content = title_common+title +'</div></div>';
			$("#optionlist").html('');
			$("#optionlist").html(content);
			mobile();
			attrid=id;
			 $('.properLine .conte').each(function(){
             	if($(this).children().length==1){
             		$(this).parent().hide();	
             	}
             });
		}
		if(data['status'] == request_fall){
			alert('加载型号列表出现异常!');
		}
		if(typeof(data['url']) != 'undefined' && data['url'] !=''){
			UrlGoto(data['url']);
		}
	}
	if(flag == true){
		AjaxRequest(u,d,f);
	}
}