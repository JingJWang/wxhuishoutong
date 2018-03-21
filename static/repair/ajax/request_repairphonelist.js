//当前搜素的型号id
var typeid='';
var attrid='';
GetPhoneBrand();
//获取手机维修管理手机品牌
function GetPhoneBrand(){
	var u='/index.php/repair/repairHome/GetBrand';
	var d='';
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){		
			var content='';
			$.each(data['data'],function(k,v){
				content = content + '<li onclick="blueBgLi(this),getTypes('+v['id']+')">'+
				 '<input type="text" name="" value="'+v['name']+'" readonly="readonly"/>'+
				 '<input type="hidden" id="mids" value="'+v['id']+'" />'+
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
			UrlGoto(data['url']);
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
						'<input type="text" id="goodsname" name="" value="'+v['name']+'" readonly="readonly"/></li>';
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
			UrlGoto(data['url']);
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
	var sign='';
	var stu='';
	var f=function(res){
		var data=eval(res);
		if(data['status'] == request_succ){
			var title_common='<div class="basicInfor"><h5>基本信息</h5><input type="hidden" id="faults"><div class="properList" ><input type="hidden" id="phoneid" value="'+id+'"><input type="hidden" id="goodsname" value="'+name+'">';
			var title='';
			$.each(data['data']['big'],function(k,v){
				title += '<div class="properLine clearfix"><div class="title fl"><input type="text" name="" id="'+v['id']+'"value="'+v['name']+'" size="10" readonly="readonly" class="inputText" onkeyup="fixedLength(this)" />'+
				'<span class="delete" style="display: none;" onclick="deleteList(this)"></span></div><ul class="clearfix fl">';
				$.each(data['data']['con'],function(key,val){
					if(val[0]==v['id']){
						$.each(data['data']['small'],function(keys,vals){
							if(vals['pid']==val[1]){
								pname=vals['pname'];
							}
					    })
						if(val[3] == 1){
							sign ='sign';
							stu=1;
						}else{
							sign = '';
							stu=0;
						}
					title += '<li><input type="text" id="'+val[1]+'" data-choose='+stu+' class="addBg '+sign+'" sign-id='+val['0']+' readonly="readonly"  value="'+pname+'" data_size="10"  onclick="addsign_ch(this)" >'+
							'<input type="text" class="price" style="float:left;width:80px;margin-left:10px;float:right"  value="'+val[2]/100+'">'+
							'<span class="delete1" style="display: none;" onclick="delAttr(this)"></span></li>';
					}
				});
				title +='<ul/></div>';
			});
			content = title_common+title +'</div></div>';
			$("#optionlist").html('');
			$("#optionlist").html(content);
			mobile();
			attrid=id;
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

//保存选中内容
function Optionsave(){
	var content=new Array();
	var phone='';
	var mid=$(".blueBgClick #mids").val();
	var pid=$(".basicInfor #phoneid").val();
	var fault=faults();
	if(mid == undefined || pid == undefined ){
		alert('你没有选择相应的手机信息,请选择后再保存');
		return false;
	}
	if(fault==''){
		alert('基本信息没有选择,请选择后再保存');
		return false;
	}else{
		$('.properList .addBg').each(function(index,element){
			var name=$(this).attr('sign-id');
			var id=$(this).attr('id');
			var price='';
			var stu=$(this).attr('data-choose');
			if(stu==0){
				price=0;
			}else if(stu==1){
				price=parseInt($(this).next('.price').val()*100);
			}
			content.push(name +':'+id+':'+price+':'+stu);
		});
	}
	var u='/index.php/repair/repairHome/upSaveAttr';
	var d='mid='+mid+'&pid='+pid+'&content='+content;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
		if(response['status'] == request_succ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			UrlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
function faults(){
	var faults='';
	$('.properList .sign').each(function(){
		faults=$('#faults').val()+1;
	});	
	return faults;
}