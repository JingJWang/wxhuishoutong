var isShow=false;
//当前同类元素隐藏下个同类元素显示
function p_hide_show(obj) {
	$(obj).attr('class', 'selected');
	$(obj).parents("ul").find('li:not(.selected)').hide(300);
	$(obj).parents("ul").siblings('.property_title').find(".xiugai_btn").show(300);
	$(obj).parents("ul").hide(300);
}
//修改
function li_mod(obj) {
	$(obj).parents(".property_title").siblings("ul").find('li').show(300);
	$(obj).hide(300);
	$(obj).siblings(".conTxt").html("");//***清空p标签内的内容***
	$(obj).parents(".property_title").siblings("ul").slideDown(300);//***让属性选择栏显示***
	$(obj).parents(".property_title").css('background','#f1f1f1');//***点击修改时底色变回原来的颜色***
	$(obj).siblings("span").removeClass("sign");
}
//属性点击事件
function property_click(obj, suffix, property_id) {
	var name = $(obj).attr('name');
	$("li[name='" + name + "']").attr('class', '');
	$(obj).attr('class', 'selected');
	//获取选中的内容
	var liCon = $(obj).find("i").html();
	//将属性添加到p标签中
	$(obj).parents("ul").siblings('.property_title').find(".conTxt").html(liCon);
	//让标题栏变色
	$(obj).parents("ul").siblings('.property_title').css('background','#fff');
	//赋值
	$("#" + suffix).val(property_id);
	//当前同类元素隐藏下个同类元素显示
	p_hide_show(obj);
	//自动下一步
	auto_go_next(obj);
	//计算间距
	$('body,html').animate({'scrollTop':$(obj).parent().siblings('.property_title').offset().top},300);
	//二次点击修改时阻止下一个同类元素显示
	//为span添加标志 说明已经选过一次了
	$(obj).parent("ul").siblings(".property_title").children("span").addClass("sign");
	if($(obj).parents("dd").next("dd").children(".property_title").find("span").hasClass("sign")){
		$(obj).parents("dd").next("dd").find("ul").hide();
	}
}
//描叙点击事件
function item_click(obj, suffix, desc_id) {
	//可多选时
	if (suffix == "other") {
		var id=$("#" + suffix).val();
		if ($(obj).hasClass('selected')) {
			$(obj).attr('class', '');
			var temp = id.replace(","+desc_id,' ');
			$("#" + suffix).val(temp);
			return;
		}else{
			$(obj).attr('class', 'selected');
			$("#" + suffix).val(id +","+desc_id);
			return;
		}
	}else{
		//赋值描叙id
		$("#" + suffix).val(desc_id);
	}
	var name = $(obj).attr('name');
	$("li[name='" + name + "']").attr('class', '');
	$(obj).attr('class', 'selected');	
	//子类id
	var child_id = 'pingu_mx_' + suffix + '_child';
	//删除现有子类内容
	//$("#"+child_id).remove();
	$(".add_child_" + child_id).remove();
	if ($("#pingu_mx_" + desc_id).length != 0) {
		//加载子类描叙
		var html = "";
		html += '<dd class="add_child_' + child_id + '" id="pingu_mx_' + desc_id + '">';
		html += '<input type="hidden" name="desc_id[]" id="desc_id_' + desc_id + '" value="0" />';
		html += $("#pingu_mx_" + desc_id).html();
		html += '</dd>';
	}
	$("#pingu_mx_" + suffix).after(html);
	var pp_id = $(obj).parent().parent().parent().parent().attr('id');
	//当前同类元素隐藏下个同类元素显示
	if (pp_id != 'step2') p_hide_show(obj);
	//显示下一步按钮亮灯
	
	//if ($(obj).attr('next') == 'ok'){$('.chakan_price').attr('background','#58ab23');}
	//自动下一步
	auto_go_next(obj);
	//scroll事件
	$('body,html').animate({'scrollTop':$(obj).parent().siblings('.property_title').offset().top},300);
	var liCon = $(obj).find("i").html();//***获取选中的***
	$(obj).parents("ul").siblings('.property_title').find(".conTxt").html(liCon);//***将属性添加到p标签中***
	$(obj).parents("ul").siblings('.property_title').css('background','#fff');//***让标题栏变色***	
	$('#step2 .property_title').css('background','#f1f1f1');//***让标题栏变色***	
	//为span添加标志 说明已经选过一次了
	$(obj).parent("ul").siblings(".property_title").children("span").addClass("sign");
	if($(obj).parents("dd").next("dd").children(".property_title").find("span").hasClass("sign")){
		$(obj).parents("dd").next("dd").find("ul").hide();
	}
}
function auto_go_next(obj) {
	var p_div_id = $(obj).parent().parent().parent().parent().attr('id');
	$(obj).parent().parent().next().find(".pinggu_other").show(300);
	if (p_div_id == 'step1') {
		var n = true;
		$("#" + p_div_id + " input").each(function() {
			var c=$(this).val();
			if (c == 0) {
				n = false;
			}
		});
		if (n) {
			$(".pinggu_other2").css('display', 'block');
		}
	}
}
function isbool(obj,sign,type){
	//选中的添加标示
	var name = $(obj).attr('name');
	$("li[name='" + name + "']").attr('class', '');
	$(obj).attr('class', 'selected');
	var logic=model[sign]['logic'];
	if(type == 1){
		//删除其他不想管的属性
		$.each(attr,function(k,n){			
			if(logic.indexOf(k) >= 0) {
				$("#sign_"+k).remove();
			}
		});
		//显示bool类型的属性
		var temp='';
		var method=function (val,key){ return "item_click(this,'"+val+"','"+key+"')";}
		var radio ='<dd id="sign_'+sign+'_children"><div class="property_title  pinggu_title_on clearfix">'+
		'<span class="fl">'+model[sign]['name']+'</span><p class="conTxt fl TextOverflow"></p>'+
		'<input type="hidden"  id="'+sign+'" name="'+sign+'" value=""></input>'+
		'<a class="xiugai_btn fr" href="javascript:;" style="display:none" onclick="li_mod(this)">修改</a>'+																					
		'</div><ul class="pinggu_other widthBig" style="display:none;">';
		$.each(attr[sign],function(i,n){
			temp = temp +'<li onclick="'+method(sign,n)+'" name="'+sign+'">'+							
			'<span class="property_value"><i>'+info[n]+'</i></span><span class="gou"></span></li>';
		});
		radio = radio + temp + '<div class="clear">&nbsp;</div></ul></dd>';
		$("#sign_"+sign).after(radio);
		isShow=true;
	}else{
		if(isShow){
			$("#sign_"+sign+"_children").remove();
			var response=isBoolList(logic);
			$.each(attr,function(k,n){
				if(model[k]['type'] == 2){
					var  val=$("#"+k).val();
					$("li[name='" + k + "']").attr('class', '');
					$("li[name='" + k + "']").attr('style', 'display:block;');
				}
			});
			$("#step1 dl").append(response['radio']);
			$("#step2 dl").html(response['check']);
			isShow=false;
		}
	}
	$("#" + sign).val(type);
	//隐藏当前元素
	p_hide_show(obj);
	//下一个
	auto_go_next(obj);
	//获取选中的内容
	var liCon = $(obj).find("i").html();
	//将属性添加到p标签中
	$(obj).parents("ul").siblings('.property_title').find(".conTxt").html(liCon);
	//让标题栏变色
	$(obj).parents("ul").siblings('.property_title').css('background','#fff');
}
//按钮换色
function btnColor() {
	$('.chakan_price').css('background-color', '#f75e26');
}
//判断必选属性是否全部选择完毕
function btnPrevent(){
	var dd_length =  $('#step1 dl dd').length;
	var sign_number = $('.property_title span.sign').length;
	if (dd_length !== sign_number) return;
}
//删除按钮弹框
function deleAlert(){
	$('.deleteGray').show();
}
function deleAlerthide(){
	$('.deleteGray').hide();
}











