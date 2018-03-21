//**************************************************手机管理页面*********************************************************    
//点击编辑按钮时触发的一系列动作
function brandEdit(obj){
	//控制编辑按钮隐藏   保存和取消按钮显示
	$(obj).parent('.editBtn').hide();
	$(obj).parents('#brandedit').children('.savaBtns').show();
	//控制品牌列表发生变化
	$('#phoneCon .brandList ul').addClass('editInput');
	$('#phoneCon .brandList .delete').show();
	//底部添加按钮显示
	$('.brandList .addLi').show();
	//禁用search搜索框
	$('.search .searchText').val("").attr('readonly','readonly');
	//鼠标划上input背景色和字体颜色改变
	$('#phoneCon .brandList ul').removeClass('blueBg');
	//查找之前点击后改变的背景蓝色  使之变成白色
	if($('.brandList ul li').hasClass('blueBgClick')){
		$('.blueBgClick').addClass('whiteBgClick');
		$('.brandList ul li').removeClass('blueBgClick');
	}
}
function modelEdit(obj){
	//控制编辑按钮隐藏   保存和取消按钮显示
	$(obj).parent('.editBtn').hide();
	$(obj).parents('#modeledit').children('.savaBtns').show();
	//控制手机型号列表发生变化
	$('#phoneCon .modelList ul').addClass('editInput');//样式改变
	$('#phoneCon .modelList .delete').show();//删除按钮显示
	//底部添加按钮显示
	$('.modelList .addLi').show();
	//禁用search搜索框
	$('.search .searchText').val("").attr('readonly','readonly');
	//鼠标划上input背景色和字体颜色改变
	$('#phoneCon .modelList ul').removeClass('blueBg');
	//查找之前点击后改变的背景蓝色  使之变成白色
	if($('.modelList ul li').hasClass('blueBgClick')){
		$('.blueBgClick').addClass('whiteBgClick');
		$('.modelList ul li').removeClass('blueBgClick');
	}
}
function properEdit(obj){
	//控制编辑按钮隐藏   保存和取消按钮显示
	$(obj).parent('.editBtn').hide();
	$(obj).parents('#properedit').children('.savaBtns').show();
	//每列发生样式变化(可输入、.title加边框、可删除按钮)
	$('#phoneCon .properList .title').addClass('titlestyle');//样式改变
	$('#phoneCon .properList .delete').show();//删除按钮显示
	//添加一行的按钮显示
	//添加按钮显示
	$('.properLine ul .addLi').show();
	$('.properList .addProper').show();
	//== checkbox ==
	//每列发生样式变化(可输入、.title加边框、可删除按钮)
	$('.checkBox ul').addClass('inputStyle');
	$('.checkBox ul li i').hide();
	$('#phoneCon .checkboxList .delete').show();//删除按钮显示
	//添加一行的按钮显示
	//添加按钮显示
	$('.checkboxLine ul .addLi').css('display','block');
	//点击编辑  清除所有click让底色变蓝色的事件
	
}
//=== 点击取消按钮的时候 ===
function brandNo(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#brandedit').children('.editBtn').show();
	//控制品牌列表发生变化
	$('#phoneCon .brandList ul').removeClass('editInput');
	$('#phoneCon .brandList .delete').hide();	
	$('#phoneCon .brandList ul li input').attr('readonly','readonly');
	//底部添加按钮隐藏
	$('.brandList .addLi').hide();
	//点击取消恢复input的默认值
	$('#phoneCon .brandList li').each(function(index,element){
		var zhi = $(element).children('input').attr('value');
		//alert(zhi);
		$(element).children('input').val(zhi);
	})
	//还原删除的每一行
	$('.brandList ul .hideLi').show();
	$('.brandList ul li').removeClass('hideLi');
	//判断新增li里面input是否为空  若空则移除
	$('#phoneCon .brandList li').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//启用search搜索框
	$('.search .searchText').removeAttr('readonly');
	//鼠标划上input背景色和字体颜色还原
	$('#phoneCon .brandList ul').addClass('blueBg');
	//查找之前点击后改变的背景白色  使之恢复原位置的蓝色
	//$('.whiteBgClick').addClass('blueBgClick');
	$('.brandList ul li').removeClass('blueBgClick');
	if($('.brandList ul li').hasClass('whiteBgClick')){
		$('.whiteBgClick').addClass('blueBgClick');
		$('.brandList ul li').removeClass('whiteBgClick');
	}
}
function modelNo(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#modeledit').children('.editBtn').show();
	//控制手机型号列表发生变化
	$('#phoneCon .modelList ul').removeClass('editInput');//样式改变
	$('#phoneCon .modelList .delete').hide();//删除按钮隐藏
	$('#phoneCon .modelList ul li input').attr('readonly','readonly');//input变为只读状态
	//底部添加按钮隐藏
	$('.modelList .addLi').hide();
	//点击取消恢复input的默认值
	$('#phoneCon .modelList li').each(function(index,element){
		var zhi = $(element).children('input').attr('value');
		//alert(zhi);
		$(element).children('input').val(zhi);
	})
	//还原删除的每一行
	$('.modelList ul .hideLi').show();
	$('.modelList ul li').removeClass('hideLi');
	
	//判断新增li里面input是否为空  若空则移除
	$('#phoneCon .modelList li').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//启用search搜索框
	$('.search .searchText').removeAttr('readonly');
	//鼠标划上input背景色和字体颜色还原
	$('#phoneCon .modelList ul').addClass('blueBg');
	//查找之前点击后改变的背景白色  使之恢复原位置的蓝色
	//$('.whiteBgClick').addClass('blueBgClick');
	$('.modelList ul li').removeClass('blueBgClick');
	if($('.modelList ul li').hasClass('whiteBgClick')){
		$('.whiteBgClick').addClass('blueBgClick');
		$('.modelList ul li').removeClass('whiteBgClick');
	}
}
function properNo(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#properedit').children('.editBtn').show();
	//每列发生样式变化(不可输入、.title加去边框、可删除按钮隐藏)
	$('#phoneCon .properList .title').removeClass('titlestyle');//样式改变
	$('#phoneCon .properList ul li input,#phoneCon .properList .title input').attr('readonly','readonly');//input变为只读状态
	$('#phoneCon .properList .delete').hide();//删除按钮隐藏
		//还原删除的每一个属性框框
		$('.properList ul .hideLi').show();
		$('.properList ul li').removeClass('hideLi');
		//还原删除的每一行
		$('.properList').find('.hideLine').show();
		$('.properList').find('.properLine').removeClass('hideLine');
		//还原功能性问题的每一个属性框框
		$('.checkboxList ul .hideLi').show();
		$('.checkboxList ul li').removeClass('hideLi');
	//点击取消恢复input的默认值(要先恢复默认值再移除)
	$('#phoneCon .properList li').each(function(index,element){
		var zhi = $(element).children('input').attr('value');
		//alert(zhi);
		$(element).children('input').val(zhi);
	})
	//判断新增li里面input是否为空  若空则移除(要先恢复默认值再移除)
	$('#phoneCon .properList li:not(.addLi)').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	$('#phoneCon .properList .title').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//删除一行
	$('#phoneCon .properLine').each(function(index,element){
		if($.trim($(element).find('.title input').val()) == ""){ // 判断.title下的input的value值是否为空
			$(element).remove();
		}
		if($.trim($(element).find('.title input').val()) !== ""&&$.trim($(element).find('ul li:not(.addLi)').val()) == ""){
			//alert(1);
			$(element).remove();
		}	
	})
	//添加按钮隐藏
	$('.properLine ul .addLi').hide();
	//添加一行  按钮隐藏
	$('.properList .addProper').hide();
	//== checkbox ==
	//每列发生样式变化(可输入、.title加边框、可删除按钮)
	$('.checkBox ul').removeClass('inputStyle');
	$('.checkBox ul li i').show();
	$('#phoneCon .checkboxList ul li input,#phoneCon .properList .title input').attr('readonly','readonly');//移除只读
	$('#phoneCon .checkboxList .delete').hide();//删除按钮显示
	//添加按钮隐藏
	$('.checkboxLine ul .addLi').hide();
	//点击取消恢复input的默认值(要先恢复默认值再移除)
	$('#phoneCon .checkboxList li').each(function(index,element){
		var zhi = $(element).children('input').attr('value');
		//alert(zhi);
		$(element).children('input').val(zhi);
	})
	//判断新增li里面input是否为空  若空则移除(要先恢复默认值再移除)
	$('#phoneCon .checkboxList li:not(.addLi)').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})

}
//=== 点击保存按钮的时候 ===
function brandYes(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#brandedit').children('.editBtn').show();
	//控制品牌列表发生变化
	$('#phoneCon .brandList ul').removeClass('editInput');
	$('#phoneCon .brandList .delete').hide();	
	$('#phoneCon .brandList ul li input').attr('readonly','readonly');
	//底部添加按钮隐藏
	$('.brandList .addLi').hide();
	//点击保存移除隐藏的每一行
	$('.brandList ul .hideLi').remove();
	
	//点击保存让把改变后的input值输入到value中
	//alert($('.brandList ul li').eq(0).children('input').val());
	$('#phoneCon .brandList li').each(function(index,element){
		var zhi = $(element).children('input').val();
		//alert(zhi);
		$(element).children('input').attr('value',zhi);
	})
	//判断新增li里面input是否为空  若空则移除
	$('#phoneCon .brandList li').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//启用search搜索框
	$('.search .searchText').removeAttr('readonly');
	//鼠标划上input背景色和字体颜色还原
	$('#phoneCon .brandList ul').addClass('blueBg');
	//$('.whiteBgClick').addClass('blueBgClick');
	//查找之前点击后改变的背景白色  使之恢复原位置的蓝色	
	$('.brandList ul li').removeClass('blueBgClick');	
	if($('.brandList ul li').hasClass('whiteBgClick')){
		$('.whiteBgClick').addClass('blueBgClick');
		$('.brandList ul li').removeClass('whiteBgClick');
	}
	getnewbrand();
}
function modelYes(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#modeledit').children('.editBtn').show();
	//控制手机型号列表发生变化
	$('#phoneCon .modelList ul').removeClass('editInput');
	$('#phoneCon .modelList .delete').hide();	
	$('#phoneCon .modelList ul li input').attr('readonly','readonly');
	//底部添加按钮隐藏
	$('.modelList .addLi').hide();
	//点击保存移除隐藏的每一行
	$('.modelList ul .hideLi').remove();
	
	//点击保存让把改变后的input值输入到value中
	//alert($('.modelList ul li').eq(0).children('input').val());
	$('#phoneCon .modelList li').each(function(index,element){
		var zhi = $(element).children('input').val();
		//alert(zhi);
		$(element).children('input').attr('value',zhi);
	})
	//判断新增li里面input是否为空  若空则移除
	$('#phoneCon .modelList li').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//启用search搜索框
	$('.search .searchText').removeAttr('readonly');
	//鼠标划上input背景色和字体颜色还原
	$('#phoneCon .modelList ul').addClass('blueBg');
	//$('.whiteBgClick').addClass('blueBgClick');
	//查找之前点击后改变的背景白色  使之恢复原位置的蓝色	
	$('.modelList ul li').removeClass('blueBgClick');	
	if($('.modelList ul li').hasClass('whiteBgClick')){
		$('.whiteBgClick').addClass('blueBgClick');
		$('.modelList ul li').removeClass('whiteBgClick');
	}
	getnewmodel();
}
function properYes(obj){
	if(confirm("确定要新增加属性吗?") === false){
		  properNo(obj);
		  return false;
	}
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#properedit').children('.editBtn').show();
	//每列发生样式变化(不可输入、.title加去边框、可删除按钮隐藏)
	$('#phoneCon .properList .title').removeClass('titlestyle');//样式改变
	$('#phoneCon .properList ul li input,#phoneCon .properList .title input').attr('readonly','readonly');//input变为只读状态
	$('#phoneCon .properList .delete').hide();//删除按钮隐藏
		//删除每一个隐藏的属性框框
		$('.properList ul .hideLi').remove();
		//$('.properList ul li').removeClass('hideLi');
		//删除每个隐藏的一行
		$('.properList').find('.hideLine').remove();
		//$('.properList').find('.properLine').removeClass('hideLine');
		//删除功能性问题的每一个属性框框
		$('.checkboxList ul .hideLi').remove();
		//$('.checkboxList ul li').removeClass('hideLi');
	
	//判断新增li里面input是否为空  若空则移除
	$('#phoneCon .properList li:not(.addLi)').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	$('#phoneCon .properList .title').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//点击保存让把改变后的input值输入到value中
	//alert($('.properList ul li').eq(0).children('input').val());
	$('#phoneCon .properList li,#phoneCon .properList .title').each(function(index,element){
		var zhi = $(element).children('input').val();
		//alert(zhi);
		$(element).children('input').attr('value',zhi);
	})
	//删除一行
	$('#phoneCon .properLine').each(function(index,element){
		if($.trim($(element).find('.title input').val()) == ""){ // 判断.title下的input的value值是否为空
			$(element).remove();
		}
		if($.trim($(element).find('.title input').val()) !== ""&&$.trim($(element).find('ul li:not(.addLi)').val()) == ""){
			//alert(1);
			$(element).remove();
		}	
	})
	//添加按钮隐藏
	$('.properLine ul .addLi').hide();
	//添加一行的按钮隐藏
	$('.properList .addProper').hide();
	
	//== checkbox ==
	//每列发生样式变化(可输入、.title加边框、可删除按钮)
	$('.checkBox ul').removeClass('inputStyle');
	$('.checkBox ul li i').show();
	$('#phoneCon .checkboxList ul li input,#phoneCon .properList .title input').attr('readonly','readonly');//移除只读
	$('#phoneCon .checkboxList .delete').hide();//删除按钮显示
	//添加按钮隐藏
	$('.checkboxLine ul .addLi').hide();
	//判断新增li里面input是否为空  若空则移除
	$('#phoneCon .checkboxList li:not(.addLi)').each(function(index,element){
		if($.trim($(element).children('input').val()) == "") // 判断value值是否为空
        $(element).remove();
	})
	//点击保存让把改变后的input值输入到value中
	//alert($('.checkboxList ul li').eq(0).children('input').val());
	$('#phoneCon .checkboxList li').each(function(index,element){
		var zhi = $(element).children('input').val();
		$(element).children('input').attr('value',zhi);
	})
	saveAttr();
}
//在ul后面动态添加节点li
function brandAddLi(obj){
	var myTag = $('<li onclick="blueBgLi(this)" class="newAdd"><input type="text" name="" value=""/><span class="delete" onclick="deleteLi(this)"></span></li>');
	$(obj).siblings('.brandList ul').append(myTag);
}
function modelAddLi(obj){
	var myTag = $('<li onclick="blueBgLi(this)" class="newAdd"><input type="text" name="" value=""/><span class="delete" onclick="deleteLi(this)"></span></li>');
	$(obj).siblings('.modelList ul').append(myTag);
}
function porperAddLi(obj){
	var myTag = $('<li><input type="text" name="" value="" size="10" class="inputText" onkeyup="checkLength(this)" onclick="newaddsign(this)"/><span class="delete" onclick="deleteLi(this)"></span></li>');
	$(obj).before(myTag);
	 // onclick="addsign(this)"
}
function checkboxAddLi(obj){
	var myTag = $('<li><i style="display: none;" onclick="checkboxI(this)"></i><input type="text" name="" value="" size="10" class="inputText" onkeyup="checkLength(this)"/><span class="delete" onclick="deleteLi(this)"></span></li>');
	$(obj).before(myTag);
}
//给brand和model的选中li加背景色
function blueBgLi(obj){
	$(obj).addClass('blueBgClick').siblings().removeClass('blueBgClick');
}

/*proper添加一行*/
function addLine(obj){
	var myTag = $('<div class="properLine clearfix"><div class="title titlestyle fl"><input type="text" name="" value="" size="10" class="inputText" onkeyup="checkLength(this)" /><span class="delete" style="display: block;" onclick="deleteList(this)"></span></div><ul class="clearfix fl"><li><input type="text" name="" value="" size="10" class="inputText" onkeyup="checkLength(this)" /><span class="delete" style="display: block;" onclick="deleteLi(this)"></span></li><li class="addLi fl" style="display: block;" onclick="porperAddLi(this)"><a href="javascript:;"><span></span></a></li></ul></div>');
	$(obj).before(myTag);
	//将给新添加的ul加宽度
	var properULwidth = $('.properList').width();
	$('.properLine ul').width(properULwidth - 141);
    $(window).resize(function () {
		var properULwidth = $('.properList').width();
		$('.properLine ul').width(properULwidth - 141);
    });	
}
//点击列表里面的删除按钮的时候删除该行	
function deleteLi(obj){
	$(obj).parent('li').hide();
	$(obj).parent('li').addClass('hideLi');
	
}
//properList 点击删除该行内容
function deleteList(obj){
	$(obj).parents('.properLine').addClass('hideLine');
	$(obj).parents('.properLine').hide();
}
//input宽度自适应
function checkLength(obj) {
    var oTextCount = document.getElementsByClassName("inputText");   
    iCount = obj.value.replace(/[^\u0000-\u00ff]/g,"aa");
    oTextCount.innerHTML = "<font color=#FF0000>"+ iCount.length+"</font>";
    obj.size=iCount.length+2;
}
function fixedLength(obj) {
    var oTextCount = document.getElementsByClassName("inputText");   
    iCount = obj.value.replace(/[^\u0000-\u00ff]/g,"aa");
    oTextCount.innerHTML = "<font color=#FF0000>"+ iCount.length+"</font>";
    //obj.size=iCount.length+2;
    if(iCount.length>10){
    	iCount.length=10;
    }else{
    	obj.size=iCount.length+2;
    }
}
/*=== checkbox ===*/
//选中哪一个添加类名 改变样式
function checkboxI(obj){
	if($(obj).hasClass('right')){
		$(obj).removeClass('right');
	}else{
		$(obj).addClass('right');		
	}
}
//**************************************************数据页面  每日数据页面*********************************************************
function showTwoDate(){
	$('.dataCon .box0ne').hide();
	$('.dataCon .boxTow').show();
}
function showOneDate(){
	$('.dataCon .box0ne').show();
	$('.dataCon .boxTow').hide();
}
$(function(){
//获取浏览器页面的高度
	var winheight = $(window).height();
	$("#contentBox,#phoneCon,.dataCon").height(winheight - 90);
	//每日数据页面
	$(".dataConBot").height(winheight - 120);
	$("#phoneCon .pDetails .brand .brandList").height(winheight - 265);
	$("#phoneCon .pDetails .model .modelList").height(winheight - 265);
	$("#phoneCon .rightscroll").height(winheight - 265);
    $(window).resize(function () {
	    var winheight = $(window).height();
	    $("#contentBox,#phoneCon,.dataCon").height(winheight - 90);
	    //每日数据页面
	    $(".dataConBot").height(winheight - 120);
		$("#phoneCon .pDetails .brandList").height(winheight - 265);
		$("#phoneCon .pDetails .brandList").height(winheight - 265);
		$("#phoneCon .pDetails .model .modelList").height(winheight - 265);	
		$("#phoneCon .rightscroll").height(winheight - 265);
	    
    });
//**************************************************手机管理页面*********************************************************    
    mobile();
    //获取input中字的size是多少
	$('.properLine').each(function(index,element){
		$(element).find('ul li:not(.addLi)').each(function(index02,element02){
			var lang = $(element02).find('input').val();
			var langlength = lang.length;
			var langsize = langlength * 2 + 2;
			$(element02).find('input').attr('size',langsize);
		})
	});
});
//给properList的ul加宽度
function mobile(){
	var properULwidth = $('.properList').width();
	$('.properLine ul').width(properULwidth - 160);
    $(window).resize(function () {
		var properULwidth = $('.properList').width();
		$('.properLine ul').width(properULwidth - 160);
    });
}
//给选中属性添加标示
function addsign(obj){
	if($(obj).hasClass('sign')){
		$(obj).removeClass('sign');
	}else{
		$(obj).addClass('sign');
	}
}
//给选中属性添加标示
function addsign_ch(obj){
	if($(obj).hasClass('sign')){
		$(obj).removeClass('sign').attr('data-choose','0');
	}else{
		$(obj).addClass('sign').attr('data-choose','1');
	}
}
//点击编辑时点击input时背景不变色，删除点击变色函数
function whitebg(){
	//alert("删除函数和类名");
	$('.properLine ul li input').removeClass('sign');
	$('.properLine ul li input').attr('onclick','');
}
function backproper(){
	//alert("还原函数");
	$('.properLine ul li input').attr('onclick','addsign(this)');
	$('.properLine ul li input').removeClass('newadd');
	$('.properLine').removeClass('newaddLine');
}
//给新增属性添加标示
function newaddsign(obj){
	$(obj).addClass('newadd');
	$(obj).parents('.properLine').addClass('newaddLine');
}
var flag=true;
//移除品牌列表的点击事件
function clickNone(){
	 flag=false;
}
function clickYes(){
	 flag=true;
}
//添加品牌列表的点击事件
function brandonlick(){
	$("#brandlist li").each(function(i,e) {
		$(this).attr('onclick','blueBgLi(this),getTypes(1)');
	});
}
function  choice(obj){
	alert('a');
	$(obj).addClass('sign').parent().siblings().find('input').removeClass('sign');
}

