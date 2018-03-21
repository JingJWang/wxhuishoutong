var attr='';
var model='';
var info='';
function GetTypeOffer(){
	$(".xinghao").html(name);
	var u='/index.php/newmobile/newmobile/nature';
	var d = 'id='+id;
	var f=function(res){
			var response=eval(res);
			var radio='';
			var check='';
			attr=response['data']['attr'];
			model=response['data']['model'];
			info=response['data']['info'];
			var num=1;
			if(response['status'] == request_succ){
				$.each(attr,function(k,v){
					//多选
					if(model[k]['model'] != 2){		
						radio = radio + '<div class="property_title  pinggu_title_on clearfix">'+
							'<p class="p_name">'+model[k]['name']+'</p>'+
							'<input type="hidden" class="radioInp"  id="'+k+'" name="'+k+'" value="" data-childName="" data-childNum=""></input>'+																				
							'</div><div class="cont">';
						var temp='';
							$.each(v,function(i,n){
								temp = temp +'<span name="'+n+'" id="'+info[n]+'" onclick="choose(this)" class="cont_a">'+info[n]+'</span>';
							});
						radio = radio + temp + '</div></dd>';
					}else{
							check = check +'<div id="step2"><dl><dd id="sign_'+k+'">'+
								 '<p class="p_name noMarBot">'+model[k]['name']+'<b>(可多选)</b></p>'+'<p class="p_name_last">如没有问题，点击“获取估价”</p>'+
								 '<input type="hidden" class="manyChoose" id="'+k+'" name="'+k+'" value=""></input>'+
								 '<div class="cont">';
								 var temp='';
								 $.each(v,function(i,n){
									 temp = temp +'<span name="'+n+'" onclick="manyChoose(this)" class="last_cont_a cont_a">'+info[n]+'</span>';//+'<input type="hidden" data-name="" data-num=""/>'
								 });
								 check = check+temp+'</div></dl></div>';
					}
				});
				content ='<div id="step1">' + radio +'</div>' + check;
				$(".choose").html(content);
				//$('.next').attr('href','quote.html?id='+id+'&name='+escape(name));
				//给单选内容的最后一个添加class,用来判断
				$('#step1').children(".cont:last-of-type").addClass('last_step1');
			}
			if(response['status'] == request_fall){
				alert(response['msg']);
			}
		}
	AjaxRequest(u,d,f);
}
//点击内容想保存信息
function choose(obj){
	//判断前一个是不是已经点击
	if($(obj).parent().prev().prev().prev().find('input').attr('data-childName') == ''){
		alert('请按顺序选择！')
		return false;
	}
	//添加class效果
	var setT = $('header').height()+$('.titlea').height();		
	$(obj).addClass('active').siblings().removeClass('active');			//添加class active
	//点击分类信息，让下一个内容到顶部
	if($("#step2").length > 0){
		if($(obj).parent().nextAll().length != 0){
			$('html,body').animate({scrollTop: $(obj).parent().next().find('.p_name').offset().top-setT+'px'},500);
		}else{
			$('html,body').animate({scrollTop: $(obj).parent().parent().next().find('dl').find('dd').find('.p_name').offset().top-setT+'px'},500);
			$('.fix_last').animate({height:'3.7rem'},"slow");	//最后一步再显示按钮	
		}
	}else{
		if($(obj).parent().nextAll().length != 0){
			$('html,body').animate({scrollTop: $(obj).parent().next().find('.p_name').offset().top-setT+'px'},500);
		}else{
			$('.fix_last').animate({height:'3.7rem'},"slow");	//最后一步再显示按钮	
		}
	}
	//把值给input
	var chilrName = $(obj).html();			//名字
	var childNum = $(obj).attr('name');		//id
	$(obj).parent().prev().find('input').attr({'value':childNum});
	$(obj).parent().prev().find('input').attr({'data-childName':chilrName,'data-childNum':childNum});
}
//多选部分
function manyChoose(obj){
	var moreArr = [];
	//input放入点击该属性的值
	var manyNum = $(obj).attr('name');
	var manyName = $(obj).html();
	//判断上面的是不是已经选择了
	if($('.last_step1 span').hasClass('active')){                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
		if($(obj).hasClass('choose_active')){
			$(obj).removeClass('choose_active');		//删除class
		}else{
			$(obj).addClass('choose_active');			//添加class
		}
		$('.choose_active').each(function(){
			//moreArr += $(this).html()+';';
			moreArr += $(this).attr('name')+',';
		})
		$('.manyChoose').val(moreArr);
	}else{
		alert('请按顺序选择！');
		return false;
	}
}
//点击下一步
function nextGo(){
	var u='/index.php/newmobile/newmobile/saveBigScreen';
	var id=getUrlParam('id');
	var name=getUrlParam('name');
	var t = $("#attr").serialize();
	var d = 'id='+id+'&name='+name+'&'+t;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			UrlGoto(response['url']+'?name='+response['data'][0]['name']+'&oid='+response['data'][0]['orderid']);
		}
		if(response['status'] == request_fall){
			alert("信息有误,请重新选择");
			UrlGoto('index.php/view/newmobile/m_index.html');
		}
	};
	AjaxRequest(u,d,f);
}