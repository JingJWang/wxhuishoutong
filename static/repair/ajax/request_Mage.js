$('document').ready(function(){
	load(24);
	getorder(0,0);
});
//获取订单列表
function getorder(statu,id){
	var data=$("#search").serialize();
	var u='/index.php/repair/repairHome/repairList';
	var d='page='+id+'&'+data;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        var list = status = cont= '';
	        if(response['data']['list'].length>0){
	        	 $.each(response['data']['list'], function(k, v) {
	 	        	switch(v['status']){
	 	        	case '0': status='已取消';cont='';break;
	 	        	case '1': status ='已下单';cont='<span class="view" value="" style="cursor:pointer" onclick="orderEdit(this,'+v['id']+')"> 编辑 </span>';break;
	 	        	case '2': status ='已发货';cont='<span class="view" value="" style="cursor:pointer" onclick="orderEdit(this,'+v['id']+')"> 编辑 </span>';break;
	 	        	case '3': status ='维修中';cont='<span class="view" value="" style="cursor:pointer" onclick="orderEdit(this,'+v['id']+')"> 编辑 </span>';break;
	 	        	case '4': status ='已完成';cont='<span class="view" value="" style="cursor:pointer" onclick="orderEdit(this,'+v['id']+')"> 编辑 </span>';break;
	 	        	}
	 	        	list += '<ul class="clearfix"><li style="width:50px">'+v['id']+'</li><li>'
	 	        	           +v['goodsname']+'</li><li  style="width:100px">'
	 	        	           +v['phone']+'</li><li style="width:100px">'
	 	        	           +v['name']+'</li><li style="width:15%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'+v['adr']+'">'
	 	        	           +v['adr']+'</li><li style="width:20%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'+v['con']+'">'
	 	        	           +v['con']+'</li><li style="width:100px">'
	 	        	           +v['money']/100+'</li><li style="width:100px">'
	 	        	           +userDate(v['jointime'])+'</li><li style="width:50px">'
	 	        	           +status+'</li><li>'
	 	        	          +'<div class="check">'
	 	        	          +'<table width="100%" height="48px" cellspacing="0" cellpadding="0" border="0"><tr width="100%" height="100%"><td width="100%" height="100%">'
	 	        	          +'<span class="view" style="cursor:pointer" value="" onclick="orderInfo(this,'+v['id']+')" > 查看 </span>'
	 	        	         +cont+'</td></tr></table></div></div></li></ul>';
	 	        });
	        }else{
	        	$('.showBut').css('display','none');
	        	list='<div  style="text-align:center;margin-top:10px;border:0px;">暂无数据</div>';
	        }
	        $('.luxuryBox .luxuryInfo').html(list);
	        
	        //下面是分页
	        var one_pag = 10;
	        var now = Number(response['data']['num']['now']);//当前开始数字
	        var num = response['data']['num']['0']['sum'];//总数
	        page(one_pag,now,num,statu);
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
	}
	AjaxRequest(u,d,f);
}
/***********查看订单*************/
function orderInfo(obj,id){
	lookup(obj);
	var u='/index.php/repair/repairHome/getOnerepair';
	var d='id='+id;
	var status = '';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$.each(response['data']['list'], function(k, v) {
				switch(v['status']){
 	        	case '0': status='已取消';break;
 	        	case '1': status ='已下单';break;
 	        	case '2': status ='已发货';break;
 	        	case '3': status ='维修中';break;
 	        	case '4': status ='已完成';break;
 	        	}
				$("#ids").html(v['id']);
				$("#goodsname").html(v['goodsname']);
				$("#phones").html(v['phone']);
				$("#name").html(v['name']);
				$("#other").html(v['other']);
				$("#adr").html(v['adr']);
				$("#money").html(parseInt(v['money']/100)+'元');
				$("#discount").html(parseInt(v['dis']/100)+'元');
				$("#bonus").html(parseInt(v['bonus']/100)+'元');
				$("#jointime").html(userDate(v['jointime']));
				if(v['updatetime']==0 || v['updatetime']==null){
					$("#updatetime").html();
				}else{
					$("#updatetime").html(userDate(v['updatetime']));
				}
				$("#express").html(v['express']);
				$("#num").html(v['num']);
				$("#con").html(v['con']);
				$("#status").html(status);
				if(v['paysta'] == 1){
					$('#paystatus').html('已支付');
				}else{
					$('#paystatus').html('未支付');
				}
				$("#comment").html(v['comment']);
			});
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
function orderEdit(obj,id){
	$(obj).parents(".clearfix").addClass("active");
    $(".fulls").css("display","block");
    $(".edti").css("display","block");
    $('.shadow').css("display","block");
    $("body,html").css("overflow","hidden");
    var u='/index.php/repair/repairHome/getOnerepair';
	var d='id='+id;
	var statuss ='';
	var conts='';
	var vals='';
	var ychage='';
	var wchage='';
	var array=parray=re=[];
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$.each(response['data']['list'], function(k, v) { 
				switch(v['status']){
	 	        	case '0': statuss='已取消';break;
	 	        	case '1': statuss ='已下单';break;
	 	        	case '2': statuss ='已发货';break;
	 	        	case '3': statuss ='维修中';break;
	 	        	case '4': statuss ='已完成';break;
 	        	}
				$("#wxid").val(v['wxid']);
				$("#egoodsname").html(v['goodsname']);
				$("#ephones").val(v['phone']);
				$("#ename").val(v['name']);
				$("#eadr").val(v['adr']);
				$("#emoney").val(parseInt(v['money']/100));
				$("#ediscount").val(parseInt(v['dis']/100));
				$("#ebonus").val(parseInt(v['bonus']/100));
				$("#eexpress").val(v['express']);
				$("#enum").val(v['num']);
				$("#paysta").val(v['paysta']);
				$("#others").html(v['other']);
				$.each(response['data']['content'],function(kc,vc){
					$.each(response['data']['text'],function(kcon,vcon){
						if(vc[1]==vcon['id']){
							if(v['status']==2 || v['status']==3){
								ychage='onclick="checkFun(this,-1);" ';
							}else{
								ychage='';
							}
							vals +='<div class="checkdiv"><span data-id="'+vcon['name']+'" name="'+vc[0]+':'+vc[1]+':'+vc[2]+'" '+
							'class="yetcheck_span ospan" '+ychage+' data="'+vc[2]/100+'" >'+vcon['name']+' '+
							vc[2]/100+'元&nbsp;&nbsp</span></div>';
						}
					});
				});
				$("#yetcheck").html(vals);
				$.each(response['data']['gu'],function(kcont,vcont){
					$.each(response['data']['text'],function(ktext,vtext){
						if(vcont[1]==vtext['id'] && vcont[3]==1){
							if(v['status']==2 || v['status']==3){
								wchage='onclick="checkFun(this,1);"';
							}else{
								wchage='';
							}
							conts+='<div class="nocheckdiv"><span data-id="'+vtext['name']+'" '+
							'name="'+vcont[0]+':'+vcont[1]+':'+vcont[2]+'" class="allcheck_span ospan"'+wchage+
							'data="'+vcont[2]/100+'">'+vtext['name']+' '+vcont[2]/100+'元&nbsp;&nbsp</span></div>';
						}
					});
				});
				$("#allcheck").html(conts);
				var arr = ["已取消","已下单","已发货","维修中","已完成"];
				var cont = '';
				var sel = '';
				$.each(arr,function(n,value){
					if(n==v['status']){
						sel='selected="selected"';
					}else{
						sel='';
					}
					cont += '<option value='+n+'  '+sel+'>'+value+'</option>';
				})
				$("#estatus").html('<select name="statuss">'+cont+'</select>');
				$("#ecomment").val(v['comment']);
				$(".cv .btns").html('<a href="javascript:;" class="save fl" onclick="editorder('+v['id']+')">保存</a><a href="javascript:;" onclick="shut()" class="noSave fl">取消</a>');

			})
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
//故障选择点击事件
function checkFun(obj,num){
	var newname = $(obj).attr('name');
	var newdataid = $(obj).attr('data-id');
	var newpirce = $(obj).attr('data');
	if(num == 1){
		var newInp = '<div class="checkdiv"><span type="text" data-id="'+newdataid+'" class="yetcheck_span ospan" name="'+newname+'" data="'+newpirce+'" onclick="checkFun(this,-1);">'+newdataid+' '+newpirce+'</span></div>';
		$('.checkdiv:last').after(newInp);
		$(obj).parent().remove();
		if($('.yetcheck_span').length == 0){
			$('.yetcheck').html(newInp);
		}
	}else{
		var newInp = '<div class="nocheckdiv"><span type="text" data-id="'+newdataid+'" class="allcheck_span ospan" name="'+newname+'" data="'+newpirce+'" onclick="checkFun(this,1);">'+newdataid+' '+newpirce+'</span></div>';
		$('.nocheckdiv:last').after(newInp);
		$(obj).parent().remove();
		if($('.nocheckdiv').length == 0){
			$('.allcheck').html(newInp);
		}
	}
	var allMoney = 0;
	var i=0;
	$('.yetcheck_span').each(function(){
		allMoney +=Number($(this).attr('data'));
		i++;
	})
	if(i != 0){
		$('#emoney').val(parseInt(allMoney-(i-1)*40));
		//获取优惠价
		$('#ediscount').val(parseInt((i-1)*40));
	}else{
		$('#emoney').val(0);
		//获取优惠价
		$('#ediscount').val(0);
	}
}
/***********保存订单*************/
function editorder(id){
	var data=$("#editor").serialize();
	var cont='';
	var names='';
	
	$('.yetcheck_span').each(function(){
		cont +=$(this).attr('data-id')+';';
		names +=$(this).attr('name')+';';
	})
	var d='id='+id+'&cont='+cont+'&cid='+names+'&'+data;
	var u='/index.php/repair/repairHome/editorder';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			$(this).parents(".clearfix").removeClass("active");
		    $(".fulls").css("display","none");
		    $(".edti").css("display","none");
		    location=location;
			
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
function userDate(uData){
	var myDate = new Date(uData*1000);
	var year = myDate.getFullYear();
	var month = myDate.getMonth() + 1;
	var day = myDate.getDate();
	return year + '-' + month + '-' + day;
}
function lookup(el){
    $(el).parents(".clearfix").addClass("active");
    $('.shadow').css("display","block")
    $(".full").css("display","block");
    $(".detail").css("display","block");
    $("body,html").css("overflow","hidden")
}
//订单详情里的关闭按钮
function shut(){
    $(".full").css("display","none");
    $(".detail").css("display","none");
    $(".shadow").css("display","none");
    $(".fulls").css("display","none");
    $(".edti").css("display","none");
    $(".clearfix.active").removeClass("active");
    $("body,html").css("overflow","auto");
}
