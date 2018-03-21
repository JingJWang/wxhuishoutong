//当前同类元素隐藏下个同类元素显示
function p_hide_show(obj) {
	if (!$(obj).hasClass('selected')) {};
	$(obj).attr('class', 'selected');
	$(obj).parents("ul").find('li:not(.selected)').hide(300);
	$(obj).parents("ul").siblings('.property_title').find(".xiugai_btn").show(300);
	$(obj).parents("ul").hide(300);//*** ***
}

//修改
function li_mod(obj) {
	$(obj).parents(".property_title").siblings("ul").find('li').show(300);
	$(obj).hide(300);
	$(obj).siblings(".conTxt").html("");//***清空p标签内的内容***
	$(obj).parents(".property_title").siblings("ul").slideDown(300);//***让属性选择栏显示***
	$(obj).parents(".property_title").css('background','#f1f1f1');//***点击修改时底色变回原来的颜色***
	//清除标志sign
	$(obj).siblings("span").removeClass("sign");
	/*if(!$(obj).siblings("span").hasClass("sign")){
		alert("111");
	}*/
	
}

//属性点击事件
function property_click(obj, suffix, property_id) {
	var name = $(obj).attr('name');
	$("li[name='" + name + "']").attr('class', '');
	$(obj).attr('class', 'selected');
	var liCon = $(obj).find("i").html();//***获取选中的***
	$(obj).parents("ul").siblings('.property_title').find(".conTxt").html(liCon);//***将属性添加到p标签中***
	$(obj).parents("ul").siblings('.property_title').css('background','#fff');//***让标题栏变色***
	$("#property_" + suffix).val(property_id);
    $("#"+suffix).val(liCon);
	//当前同类元素隐藏下个同类元素显示
	p_hide_show(obj);
	//自动下一步
	auto_go_next(obj);
	//scroll事件
	//$(window).scrollTop($(obj).parent().siblings('.property_title').offset().top);
	$('body,html').animate({'scrollTop':$(obj).parent().siblings('.property_title').offset().top},300);
	//二次点击修改时阻止下一个同类元素显示
	//为span添加标志 说明已经选过一次了
	$(obj).parent("ul").siblings(".property_title").children("span").addClass("sign");
	if($(obj).parents("dd").next("dd").children(".property_title").find("span").hasClass("sign")){
		//alert("1111");
		$(obj).parents("dd").next("dd").find("ul").hide();
	}
}
//描叙点击事件
function item_click(obj, suffix, desc_id) {
	
	var default_id = arguments[3] ? arguments[3] : desc_id;
	if (default_id != desc_id) { //可多选时
		var name = $(obj).attr('name');
		var current_class = $(obj).attr('class');
		if (current_class == 'selected') {
			$(obj).attr('class', '');
			$("#desc_id_" + suffix).val(default_id);
			var val=$(obj).attr('data-key');
			var content=$("#"+suffix).val();
			var n=content.replace(','+val,'');
			$("#"+suffix).val(n);
			return;
		}
	}
	
	var name = $(obj).attr('name');
	$("li[name='" + name + "']").attr('class', '');
	$(obj).attr('class', 'selected');
	
	var notice= $(obj).attr('data-val');
	if(notice =='notice'){
		var val=$(obj).attr('data-key');
		var content=$("#"+suffix).val();
		$("#"+suffix).val(content+","+val);
		return false;
	}
	//赋值描叙id
	$("#desc_id_" + suffix).val(desc_id);
	
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
	//自动下一步
	auto_go_next(obj);
	//scroll事件
	//$(window).scrollTop($(obj).parent().siblings('.property_title').offset().top);
	$('body,html').animate({'scrollTop':$(obj).parent().siblings('.property_title').offset().top},300);
	var liCon = $(obj).find("i").html();//***获取选中的***
	$("#"+suffix).val(desc_id);
	$(obj).parents("ul").siblings('.property_title').find(".conTxt").html(liCon);//***将属性添加到p标签中***
	$(obj).parents("ul").siblings('.property_title').css('background','#fff');//***让标题栏变色***	
	$('#step2 .property_title').css('background','#f1f1f1');//***让标题栏变色***	
	//二次点击修改时阻止下一个同类元素显示
	//为span添加标志 说明已经选过一次了
	$(obj).parent("ul").siblings(".property_title").children("span").addClass("sign");
	if($(obj).parents("dd").next("dd").children(".property_title").find("span").hasClass("sign")){
		//alert("1111");
		$(obj).parents("dd").next("dd").find("ul").hide();
	}
	
}

function auto_go_next(obj) {
	var p_div_id = $(obj).parent().parent().parent().parent().attr('id');

	$(obj).parent().parent().next().find(".pinggu_other").show(300);
	if (p_div_id == 'step1') {
		var n = true;
		$("#" + p_div_id + " input").each(function() {
			if ($(this).val() == 0) {
				n = false;
				return false;
			}
		});
		if (n) {
			$(".pinggu_other2").css('display', 'block');
		}
	}
}
//按钮换色
function btnColor() {
	$('.chakan_price').css('background-color', '#f75e26');
}