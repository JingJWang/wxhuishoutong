$('document').ready(function(){
	load(22);
	getorder(0,0);
	getOrderList(0,0);
});
/**获取奖金比例设置商城列表**/
function getorder(number,statu){
	var u='/index.php/nonstandard/homebonus/bonusSetShop';
	var name=$('#name').val();
	var d='page='+number+'&name='+name;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			var content= type= value= '';
			if(response['data']['list'].length>0){
				$.each(response['data']['list'],function(k,v){
					if(v['type']==1){
						type='比例';
						value=v['value'];
					}else if(v['type']==2){
						type='固定值';
						value=v['value'];
					}
					content += '<div class="breed">'+
		                		/*'<div class="icon"><div class="graph" onclick="chosen(this);"></div></div>'+*/
		                		'<div class="number">'+v['name']+'</div><div class="item">'+
		                		v['goodid']+'</div><div class="while">'+
		                		type+'</div><div class="nowValue">'+
		                		value+'</div><div class="operate">'+
		                        '<input type="button" value="修改" class="alter" onclick="updateShopBonus('+v['id']+');"/>'+
		                        '<input type="hidden" value="删除" class="delet" onclick="delFun('+v['id']+');"/>'+
		                        '</div></div>';
				});
			}else{
				content='<div class="breed" style="text-align:center;padding-top:20px;">暂无数据</div>';
			}
			$("#brandlist").html(content);
			 //下面是分页
	        var one_pag = 10;
	        var now = Number(response['data']['num']['now']);//当前开始数字
	        var num = response['data']['num']['0']['num'];//总数
	        page(one_pag,now,num,statu);
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
/*****获取商城商品列表*******/
function typeShoplist(){
	$('.selUl').toggle();
	var u='/index.php/nonstandard/homebonus/bonusSetShopList';
	var d='';
	var f=function(res){
		var response=eval(res);
		var content = '';
		if( response['status'] == request_succ ){
			$.each(response['data'],function(k,v){
				content += '<li id="'+v['id']+'" data-id="'+v['name']+'" onclick="listVal(this)">'+v['id']+'.'+v['name']+'</li>';
			});
		}
		$('#selShopUl').append(content);
	}
	AjaxRequest(u,d,f);
}
function listVal(obj){
	var lval = $(obj).html();
	$('.firOpA').html(lval);
	var id = lval.split('.')[0];
	var dataid = lval.split('.')[1];
	$('.firOpA').attr({'id':id,'data-id':dataid});
	$('.selUl').css('display','none');
}
/*****获取回收商品列表*******/
function  typeOrderList(){
	$('.orderul').toggle();
	var u = '/index.php/nonstandard/homebonus/bonusSetOrderList';
	var d = '';
	var f = function(res){
		var response = eval(res);
		var content = '';
		if(response['status'] == request_succ){
			$.each(response['data'],function(k,v){
				content += '<li id="'+v['id']+'" data-id="'+v['name']+'" onclick="listorderVal(this)">'+v['id']+'.'+v['name']+'</li>';
			})
		}
		$('#orderUl').append(content);
	}
	AjaxRequest(u,d,f)
}
function listorderVal(obj){
	var lval = $(obj).html();
	$('.ordera').html(lval);
	var id = lval.split('.')[0];
	var dataid = lval.split('.')[1];
	$('.ordera').attr({'id':id,'data-id':dataid});
	$('.orderul').css('display','none');
}
/*****添加商城奖金设置*******/
function addShop(){
	var addvalue= $('#addvalue').val();
	var id=$('.selShop .firOpA').attr('id');
	var name=$('.selShop .firOpA').attr('data-id');
	var radios = document.getElementsByName("type");	
	var type='';
	for(var i=0;i<radios.length;i++){
	 	if(radios[i].checked){
	 		if(i==0){
	 			type=2;
	 		}else if(i==1){
	 			type=1;
	 		}
	 	};
	};
	if(typeof(id) == "undefined" || id == ''){
	alert('没有获取选择的产品编号');
		return false;
	}
	if(typeof(name) == "undefined" || name == ''){
	alert('没有获取选择的产品信息');
		return false;
	}
	if(typeof(addvalue) == "undefined" || addvalue == ''){
	alert('没有获取到奖金值');
		return false;
	};
	var u='/index.php/nonstandard/homebonus/bonusSetSaveShop';
	var d='name='+name+'&id='+id+'&addvalue='+addvalue+'&type='+type;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert('添加成功');
			//location.href=response['url'];
			getorder(0,0);
		}
		if( response['status'] == request_fall ){
			alert('添加失败');
			getorder(0,0);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/*****添加回收奖金设置*******/
function addOrder(){
	var qujian = $('#orderInpa').val();//区间值
	qujian = qujian+'-'+$('#orderInpb').val();
	var id=$('.ordera').attr('id');
	var name=$('.ordera').attr('data-id');
	var ordervalue= $('#ordervalue').val();
	var radioa = document.getElementsByName("radionews");
	if(radioa[0].checked){
		if(typeof(qujian) == "undefined" || qujian == ''){
			alert('没有获取区间值');	
			return false;
		}
   	}
   	if(radioa[1].checked){
   		if(typeof(id) == "undefined" || id == ''){
			alert('没有获取选择的产品编号');
			return false;
		};
		if(typeof(name) == "undefined" || name == ''){
			alert('没有获取选择的产品名字');
			return false;
		};
   	};
	var radios = document.getElementsByName("radiomoney");	
	var type='';
	for(var i=0;i<radios.length;i++){
	 	if(radios[i].checked){
	 		if(i==0){
	 			type=2;
	 		}else if(i==1){
	 			type=1;
	 		}
	 	};
	};
	if(typeof(ordervalue) == "undefined" || ordervalue == ''){
		alert('没有获取到结果');
		return false;
	};
	var u='/index.php/nonstandard/homebonus/bonusSetSaveOrder';
	var d='qujian='+qujian+'&name='+name+'&id='+id+'&ordervalue='+ordervalue+'&type='+type;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			alert('添加成功');
			//location.href=response['url'];
			getOrderList(0,0);
		}
		if( response['status'] == request_fall ){
			alert('添加失败');
			getOrderList(0,0);
		}
		if(typeof(response['url']) != 'undefined' && response['url'] !=''){
			urlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
/*****获取单条需要修改的商城奖金设置*******/
function updateShopBonus(id){
	$(".shadow").css("display","block");
    $(".amendGain").css("display","block");
    var u='/index.php/nonstandard/homebonus/updateShopBonus';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			$(".addition #names").val(response['data']['0']['name']);
			$(".addition #goodid").val(response['data']['0']['goodid']);
			$(".addition #id").val(response['data']['0']['id']);
			if(response['data']['0']['type']==1){
				$("#type1").attr("checked","true");
				$(".radioNumBox .radionum").eq(1).show().siblings().hide();
				$(".radioNumBox .radionum").eq(1).find("input").val(response['data']['0']['value']);
			}else if(response['data']['0']['type']==2){
				$("#type2").attr("checked","true");
				$(".radioNumBox .radionum").eq(0).show().siblings().hide();
				$(".radioNumBox .radionum").eq(0).find("input").val(response['data']['0']['value']);
			}
			$(".sure.tier .confirm").attr("onclick","updateShopSave("+response['data']['0']['id']+")");
		}
	}
	AjaxRequest(u,d,f);
};
/**保存单条需要修改的商城奖金设置**/
function updateShopSave(id){
	var data=decodeURIComponent($("#updateShop").serialize(),true);
    var u='/index.php/nonstandard/homebonus/updateShopSave';
    var d ='id='+id+'&'+data;
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert('修改成功');
            //location.href=response['url'];
            getorder(0,0);
		} 
    }
	AjaxRequest(u,d,f);
}
/**商品设置删除**/
function delBtnSure(){
	
}
/**获取奖金比例设置回收列表**/
function getOrderList(statu,number){
	var u='/index.php/nonstandard/homebonus/bonusSetOrder';
	var goodid=$('#goodid').val();
	var start=$('#start').val();
	var end=$('#end').val();
	var d='page='+number+'&goodid='+goodid+'&start='+start+'&end='+end;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			var content= type= '';
			if(response['data']['list'].length>0){
				$.each(response['data']['list'],function(k,v){
					if(v['type']==1){
						type='比例';
					}else if(v['type']==2){
						type='固定值';
					}
					if(v['goodname']=='' || v['goodname']==null){
						name=v['start']+'到'+v['end'];
					}else{
						name=v['goodid']+'-'+v['goodname'];
					}
					content += '<div class="breed">'+
		                		/*'<div class="icon"><div class="graph" onclick="chosen(this);"></div></div>'+*/
		                		'<div class="number">'+name+'</div><div class="item">'+
		                		type+'</div><div class="nowValue">'+
		                		v['value']+'</div><div class="operate">'+
		                        '<input type="button" value="修改" class="alter" onclick="updateOrderBonus('+v['goodid']+');"/>'+
		                        '<input type="hidden" value="删除" class="delet" onclick="strike(this),delbrand('+v['goodid']+');"/>'+
		                        '</div></div>';
				});
			}else{
				content='<div class="breed" style="text-align:center;padding-top:20px;">暂无数据</div>';
			}
			$("#typelist").html(content);
			 //下面是分页
	        var one_pag = 10;
	        var now = Number(response['data']['num']['now']);//当前开始数字
	        var num = response['data']['num']['0']['num'];//总数
	        pageOrder(one_pag,now,num,number);
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
/*****获取单条需要修改的回收奖金设置*******/
function updateOrderBonus(id){
	$(".shadow").css("display","block");
	$(".changeject").css("display","block");
    var u='/index.php/nonstandard/homebonus/updateOrderBonus';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
		if( response['status'] == request_succ ){
			if(response['data']['0']['start']!=null || response['data']['0']['end']!=null){
				$(".classvalue").css('display','block');
				$(".classname").css('display','none');
				$(".modelMqu #start").val(response['data']['0']['start']);
				$(".modelMqu #end").val(response['data']['0']['end']);
			}
			if(response['data']['0']['goodid']!=null){
				$(".classvalue").css('display','none');
				$(".classname").css('display','block');
				$(".modelMqu #goodid").val(response['data']['0']['goodid']);
			}
			if(response['data']['0']['type']==1){
				$(".radioJ #typejj").attr("checked","true");
				$(".huishou .radionum").eq(1).show().siblings().hide();
				$(".huishou .radionum").eq(1).find("input").val(response['data']['0']['value']);
			}else if(response['data']['0']['type']==2){
				$(".radioJ #typej").attr("checked","true");
				$(".huishou .radionum").eq(0).show().siblings().hide();
				$(".huishou .radionum").eq(0).find("input").val(response['data']['0']['value']);
			}
			$(".modelMqu #id").val(response['data']['0']['id']);
			$(".sure").attr("onclick","updateOrderSave("+response['data']['0']['id']+")");
		}
	}
	AjaxRequest(u,d,f);
};
/**保存单条需要修改的回收奖金设置**/
function updateOrderSave(){
	$(".shadow").css("display","none");
	$(".changeject").css("display","none");
	var data=decodeURIComponent($("#updateOrder").serialize(),true);
    var u='/index.php/nonstandard/homebonus/updateOrderSave';
    var d = data;
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert('修改成功');
            getOrderList(0,0);
		} 
    }
	AjaxRequest(u,d,f);
}
function pageOrder(one_pag,now,num,statu){
	var page = Math.ceil(num/one_pag);//可以分的页数
	var pages = '';
	if (num<=one_pag) {
		 pages='';
	}
	if (now>=one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getorder('+statu+',0)">上一页</a></li>';
	};
	if (page<=5) {
	    for (var i = 0; i < page; i++) {
	        pages += '<li class="active"><a href="javascript:;" id="'+(i*one_pag)+'" onclick="getOrderList('+statu+','+i*one_pag+')">'+(i+1)+'</a></li>';
	    };
	}else{
	    if ((now/one_pag)<3) {
	    	for (var i = 1; i <= 5; i++) {
	        	pages += '<li class="active"><a href="javascript:; " id="'+((i-1)*one_pag)+'" onclick="getOrderList('+statu+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else if (now/one_pag>=(page-3)) {
	        for (var i = (page-4); i <= page; i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getOrderList('+statu+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else{
            for (var i = (now/one_pag-1); i < (now/one_pag+4); i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getOrderList('+statu+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }
	}
	if (now<(page-1)*one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getorder('+statu+','+(now+one_pag)+')">下一页</a></li>';
	};
	$('.pagination').html(pages);
	$('#'+now+'').css({ background: '#337ab7',color: '#fff'});
}
