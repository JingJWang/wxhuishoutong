/*
 *
 *  手机品牌型号管理
 */
getBrandLsit('1');
/**获取手机品牌列表**/
function getBrandLsit(number){
	var u='/index.php/center/mobile/brandLsit';
	var d='page='+number;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			var content='';
			//循环品牌列表
			$.each(response['data']['list'],function(k,v){
				content = content + '<div class="breed"><div class="icon">'+
	                		'<div class="graph" onclick="chosen(this);"></div></div>'+
	                		'<div class="number">'+v['id']+'</div><div class="item">'+
	                		'<div class="caddy"><div class="chart">'+
	                        '<div class="keep"   onclick="conserve(this),saveBrand('+v['id']+');"></div>'+
	                        '<div class="remove" onclick="cancle(this);"></div></div>'+
	                        '<div class="bong">'+
	                        '<input class="field" class="armani" data-id="'+v['id']+'" id="brand_'+v['id']+'" readonly="readonly" value="'+v['name']+'"/>'+
	                        '</div></div></div><div class="while">'+$.myTime.UnixToDate(v['jointime'])+'</div>'+
	                        '<div class="nowValue">'+'</div>'+
	                        '<div class="operate">'+
	                        '<input type="button" value="修改" class="alter" onclick="revise(this);"/>'+
	                        '<input type="button" value="删除" class="delet" onclick="strike(this),delbrand('+v['id']+');"/>'+
	                        '</div></div>';
			});
			$("#brandlist").html(content);
			//读取分页
			var total=response['data']['total'];
			var page='';
			if( total > 1){
				for(var i=1;i<=total;i++){
					if(number == i){
						page = page + '<a class="fewpage dig" onclick="getBrandLsit('+i+'),pageBj(this)" href="javascript:;">'+i+'</a>';
					}else{
						page = page + '<a class="fewpage" onclick="getBrandLsit('+i+'),pageBj(this)" href="javascript:;">'+i+'</a>';
					}
				}
				page = page +'<a class="fewpage" onclick="nextpage();" href="javascript:;">下一页</a>'+
							 '<a class="fewpage" id="total" data-val="'+total+'" href="javascript:;">共'+total+'页</a>';
			}else{
				page='<a class="fewpage dig" onclick="getBrandLsit(1),pageBj(this)" href="javascript:;">1</a>';
				
			}	
			$("#brandpage").html(page);
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/***修改品牌名字***/
function saveBrand(id){
	var name=$("#brand_"+id).val();
	var u='/index.php/center/mobile/editBrand';
	var d='id='+id+'&name='+name;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert(response['msg']);
			window.location.reload();
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
	
}
/******删除品牌******/
function delbrand(id){
	var name=$("#brand_"+id).val();
	var u='/index.php/center/mobile/delbrand';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert(response['msg']);
			window.location.reload();
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/*******批量删除品牌********/
function delMultiBrand(){
	var id='';
	$(".field,.sign").each(function(index,element){
		id = id + $(element).attr("data-id")+',';
	});
	var u='/index.php/center/mobile/delMultiBrand';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert(response['msg']);
			window.location.reload();
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/********添加品牌******/
function addBrnad(){
	var name=$("#brand").val();
	var u='/index.php/center/mobile/addBrnad';
	var d='name='+name;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			eyebrow();
			alert(response['msg']);
			window.location.reload();
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/******品牌列表点击下一页********/
function nextpage(){
	var page=$(".dig").html();
	var total=$("#total").attr('data-val');
	if(page >= total){
		alert('当前已经是最后一页!');
	}else{
		page = Number(page) + 1; 
		getBrandLsit(page);
	}
}
/*****型号管理 获取品牌列表*******/
function typeBrandlist(){
	var name=$("#brand").val();
	var u='/index.php/center/mobile/typeBrandlist';
	var d='name='+name;
	var f=function(res){
		var response=eval(res);
		var content='<option>请选择</option>';
		if( response['status'] == request_succ ){
			$.each(response['data'],function(k,v){
				content = content + '<option id="brnad_'+v['id']+'" data-id="'+v['id']+'">'+v['name']+'</option>';
			});
			$("#brandType").html(content);
			$("#typebrand").html(content);
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/********型号管理 获取型号列表***********/
function typeList(number){
	var id=$('#brandType option:selected').attr('data-id');
	if(typeof(id) == "undefined" || id == ''){
		alert('没有获取选中的品牌编号!');
		return false;
	}
	if(id == 0){
		return false;
	}
	//修改
	var u='/index.php/center/mobile/typeList';
	var d='id='+id+'&page='+number;
	var f=function(res){
		var response=eval(res);
		var content='';
		if( response['status'] == request_succ ){
			$.each(response['data']['list'],function(k,v){
				content = content + '<div class="breed"><div class="icon">'+
                		'<div class="graph" onclick="chosen1(this);"></div>'+
                		'</div><div class="number">'+v['id']+'</div><div class="item">'+
                		'<div class="caddy"><div class="chart">'+
                        '<div class="keep" onclick="editTypeName('+v['id']+'),conserve1(this);"></div>'+
                        '<div class="remove" onclick="cancle1(this);"></div></div>'+
                        '<div class="bong">'+
                        '<input class="field pag1" id="type_'+v['id']+'" class="armani" readonly="readonly" value="'+v['name']+'"/>'+
                        '</div></div></div><div class="while">'+$.myTime.UnixToDate(v['jointime'])+'</div>'+
                        '<div class="addJ">'+'</div>'+
                        '<div class="addZhi">'+'</div>'+
                        '<div class="operate">'+
                        '<input type="button" value="修改" class="alter" onclick="revise1(this);"/>'+
                        '<input type="button" value="删除" class="delet" onclick="deltype('+v['id']+');"/>'+
                        '</div></div>';
			});
			$("#typelist").html(content);
			//读取分页
			if(number == 1){
				var total=response['data']['total'];
				var page='';
				if( total > 1){
					for(var i=1;i<=total;i++){
						if( i > 4 ){
							var display='style="display:none;"';
						}else{
							var display='';
						}
						
						if(number == i){
							page = page + '<a id="typepage_'+i+'" class="fewpage dig" onclick="typeNextPage('+i+'),pageBj(this)" href="javascript:;">'+i+'</a>';
						}else{
							page = page + '<a '+display+' id="typepage_'+i+'" class="fewpage" onclick="typeNextPage('+i+'),pageBj(this)" href="javascript:;">'+i+'</a>';
						}
					}
					page = page +'<a class="fewpage" onclick="typeNextPage(-1)" href="javascript:;">下一页</a>'+
								 '<a class="fewpage" id="total" data-val="'+total+'" href="javascript:;">共'+total+'页</a>';
				}else{
					page='<a class="fewpage dig" onclick="getBrandLsit(1),pageBj(this)" href="javascript:;">1</a>';
				}	
				$("#typepage").html(page);
			}
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/*********手机型号列表分页*********/
function typeNextPage(page){
	if(page == "-1"){		
		var select=$("#typepage .dig").html();
		var next = Number(select) + 1;
		console.log('当前页'+select+'下一页'+next);
		$("#typepage_"+select).removeClass("dig");
		$("#typepage_"+next).addClass("dig");
		typeList(next);
		if(next > 3){
			var none = next -4;
			var diplay = next + 1;
			$("#typepage_"+next).css("display","block");
			$("#typepage_"+diplay).css("display","block");
			$("#typepage_"+none).css("display","none");
		}
	}else{
		var next=page+1;
		//if( $("#page_"+next).length>0 ){
		if( $("#typepage_"+next).length>0 ){
		  typeList(page);
		}else{
		   alert('当前页已经是最后一页');
		   return false;
		}
		if(page > 3){
			var none = next -4;
			$("#typepage_"+next).css("display","block");
			$("#typepage_"+none).css("display","none");
		}
	}		
}
/********修改型号名称**********/
function editTypeName(id){
	var brand=$('#brandType option:selected').attr('data-id');
	if(typeof(brand) == "undefined" || brand == ''){
		alert('没有获取选择的品牌编号');return false;
	}
	var name=$("#type_"+id).val();
	if(typeof(name) == "undefined" || name == ''){
		alert('没有获取到输入的内容');return false;
	}
	name=name.replace('+','KEYA1');
	var u='/index.php/center/mobile/editTypeName';
	var d='id='+id+'&name='+name+"&brand="+brand;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert(response['msg']);
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/******搜索型号********/
function searchType(page){
	var brand=$('#brandType option:selected').attr('data-id');
	if(typeof(brand) == "undefined" || brand == ''){
		alert('没有获取选择的品牌编号');return false;
	}
	var keyword=$("#typekeyword").val();
	if(typeof(keyword) == "undefined" || keyword == ''){
		alert('没有获取到输入的内容');return false;
	}
	var u='/index.php/center/mobile/searchType';
	var d='keyword='+keyword+"&brand="+brand+"&page="+page;
	var f=function(res){
		var response=eval(res);
		var content='';
		if( response['status'] == request_succ ){
			$.each(response['data']['list'],function(k,v){
				content = content + '<div class="breed"><div class="icon">'+
                		'<div class="graph" onclick="chosen1(this);"></div>'+
                		'</div><div class="number">'+v['id']+'</div><div class="item">'+
                		'<div class="caddy"><div class="chart">'+
                        '<div class="keep" onclick="editTypeName('+v['id']+'),conserve1(this);"></div>'+
                        '<div class="remove" onclick="cancle1(this);"></div></div>'+
                        '<div class="bong">'+
                        '<input class="field pag1" id="type_'+v['id']+'" class="armani" readonly="readonly" value="'+v['name']+'"/>'+
                        '</div></div></div><div class="while">'+$.myTime.UnixToDate(v['jointime'])+'</div>'+
                        '<div class="operate">'+
                        '<input type="button" value="修改" class="alter" onclick="revise1(this);"/>'+
                        '<input type="button" value="删除" class="delet" onclick="deltype('+v['id']+');strike(this);"/>'+
                        '</div></div>';
			});
			$("#typelist").html(' ');
			$("#typelist").html(content);
			//读取分页
						
				var total=response['data']['total'];
				var page='';
				if( total > 1){
					for(var i=1;i<=total;i++){
						if( i > 4 ){
							var display='style="display:none;"';
						}else{
							var display='';
						}						
						if(number == i){
							page = page + '<a id="typepage_'+i+'" class="fewpage dig" onclick="typeNextPage('+i+'),pageBj(this)" href="javascript:;">'+i+'</a>';
						}else{
							page = page + '<a '+display+' id="typepage_'+i+'" class="fewpage" onclick="typeNextPage('+i+'),pageBj(this)" href="javascript:;">'+i+'</a>';
						}
					}
					page = page +'<a class="fewpage" onclick="typeNextPage(-1)" href="javascript:;">下一页</a>'+
								 '<a class="fewpage" id="total" data-val="'+total+'" href="javascript:;">共'+total+'页</a>';
				}else{
					page='<a class="fewpage dig" onclick="getBrandLsit(1),pageBj(this)" href="javascript:;">1</a>';
				}	
				$("#typepage").html(' ');
				$("#typepage").html(page);
			}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/******删除型号******/
function deltype(id){
	var brand=$('#brandType option:selected').attr('data-id');
	if(typeof(brand) == "undefined" || brand == ''){
		alert('没有获取选择的品牌编号');return false;
	}
	if(typeof(id) == "undefined" || id == ''){
		alert('没有获取到型号编号');return false;
	}
	var u='/index.php/center/mobile/deltype';
	var d='typeid='+id+'&brandid='+brand;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert(response['msg']);
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/*********添加型号*************/
function adddtype(){
	var brand=$('#typebrand option:selected').attr('data-id');
	if(typeof(brand) == "undefined" || brand == ''){
		alert('没有获取选择的品牌编号');return false;
	}
	var typename = $('#typename').val();
	if(typeof(typename) == "undefined" || typename == ''){
		alert('没有获取到型号编号');return false;
	}
	typename=typename.replace('+','KEYA1');
	var u='/index.php/center/mobile/addtype';
	var d='type='+typename+'&brand='+brand;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert(response['msg']);
		}
		if( response['status'] == request_fall ){
			alert(response['msg']);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}