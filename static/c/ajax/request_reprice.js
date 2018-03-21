/*
 *
 *  手机品牌型号管理
 */
/*****型号管理 获取品牌列表*******/
function typeBrandlist(){
	var name=$("#brand").val();
	var u='/index.php/center/reprice/typeBrandlist';
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
	var u='/index.php/center/reprice/typeList';
	var d='id='+id+'&page='+number;
	var f=function(res){
		var response=eval(res);
		var content='';
		if( response['status'] == request_succ ){
			$.each(response['data']['list'],function(k,v){
				content = content + '<div class="breed">'+
                		'<div class="number">'+v['id']+'</div>'+
                        '<div class="while" >'+v['name']+'</div>'+
                        '<div class="item" ><div class="caddy">'+
                        '<div class="chart">'+
                        '<div class="keep" onclick="editTypePrice('+v['id']+',this),conserve1(this);"></div>'+
                        '<div class="remove" onclick="cancle1(this);"></div></div><div class="bong">'+
                        '<input class="field pag1" id="type_'+v['id']+'" class="armani" readonly="readonly" value="'+v['price']+'"/>'+
                       '</div></div></div>'+
                        '<div class="operate">'+
                        '<input type="button" value="修改" class="alter" onclick="revise1(this);"/>'+
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
		if( $("#page_"+next).length>0 ){
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
	var u='/index.php/center/reprice/searchType';
	var d='keyword='+keyword+"&brand="+brand+"&page="+page;
	var f=function(res){
		var response=eval(res);
		var content='';
		if( response['status'] == request_succ ){
			$.each(response['data']['list'],function(k,v){
				content = content + '<div class="breed">'+
        		'<div class="number">'+v['id']+'</div>'+
                '<div class="while" >'+v['name']+'</div>'+
                '<div class="item" ><div class="caddy">'+
                '<div class="chart">'+
                '<div class="keep" onclick="editTypePrice('+v['id']+',this),conserve1(this);"></div>'+
                '<div class="remove" onclick="cancle1(this);"></div></div><div class="bong">'+
                '<input class="field pag1" id="type_'+v['id']+'" class="armani" readonly="readonly" value="'+v['price']+'"/>'+
               '</div></div></div>'+
                '<div class="operate">'+
                '<input type="button" value="修改" class="alter" onclick="revise1(this);"/>'+
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

function editTypePrice(id,obj){
	var brand=$('#brandType option:selected').attr('data-id');
	if(typeof(brand) == "undefined" || brand == ''){
		alert('没有获取选择的品牌编号');return false;
	}
	var field=$(obj).parent().next().find("input").val();
	if(field=='' || isNaN(field)){
		alert('请正确填写价格');return false;
	}
	var u='/index.php/center/reprice/editTypePrice';
	var d='id='+id+'&price='+field+"&brand="+brand;
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
function savepi(){
	var dataid=$('.saveModel').attr('data-id');
	var radck=$('.radck').val();
	var hideRadio=$('.hideRadio').val();
	var hoverinp=$('.hover_inp').attr('data');
	var u='/index.php/center/reprice/upcountprice';
	var d='radck='+radck+'&hideRadio='+hideRadio+"&hoverinp="+hoverinp+"&dataid="+dataid;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			UrlGoto('/view/control/recycleprice.html');
		}else{
			UrlGoto('/view/control/batchMoney.html');
		}
		$('.shadow').show();
		$('.posi_j').hide();
		alert(response['msg']);
	}
	AjaxRequest(u,d,f);
}