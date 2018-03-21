function modelEdit(obj){
	//控制编辑按钮隐藏   保存和取消按钮显示
	$(obj).parent('.editBtn').hide();
	$(obj).parents('#modeledit').children('.savaBtns').show();
	//输入框 input 发生改变
	$('#phoneCon .modelList').addClass('editInput');//样式改变
	$('#phoneCon .modelList table td input').removeAttr('readonly');//移除只读
	//禁用search搜索框
	$('.search .searchText').val("").attr('readonly','readonly');
	//鼠标划上input背景色和字体颜色改变
	$('#phoneCon .modelList table').removeClass('blueBg');
	//查找之前点击后改变的背景蓝色  使之变成白色
	if($('.modelList table tr').hasClass('blueBgClick')){
		$('.blueBgClick').addClass('whiteBgClick');
		$('.modelList table tr').removeClass('blueBgClick');
	}
}
function properEdit(obj){
	//控制编辑按钮隐藏   保存和取消按钮显示
	$(obj).parent('.editBtn').hide();
	$(obj).parents('#properedit').children('.savaBtns').show();
	//每列发生样式变化(可输入、.title加边框、可删除按钮)
	$('#phoneCon .properList ul').addClass('editInput');//样式改变
	$('#phoneCon .properList ul li input').removeAttr('readonly');//移除只读
	//== checkbox ==
	//每列发生样式变化(可输入、.title加边框、可删除按钮)
	$('.checkBox ul').addClass('inputStyle');
	$('.checkBox ul li i').hide();
	$('#phoneCon .checkboxList ul li input').removeAttr('readonly');//移除只读
	//括号隐藏
	$('.properLine .bra').hide();
	$('#phoneCon .properList li').each(function(index,element){
		//判断每个input中的正负号给括号添加相应的颜色
		var zhi = $(element).find('input').val();
		var symbol = zhi.substring(0,1);
		if(symbol == "+" || symbol > 0){
			$(element).find('input').css('border-color','#ff7160');
		}else if(symbol == "-"){
			$(element).find('input').css('border-color','#7db3ff');
		}
		//把当前size赋值给myattr
		var mysize = $(element).find('input').attr('size');
		$(element).find('input').attr('myattr',mysize);
		console.log(mysize);
	})
	
}
//=== 点击取消按钮的时候 ===
function modelNo(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#modeledit').children('.editBtn').show();
	//控制手机型号列表发生变化
	$('#phoneCon .modelList').removeClass('editInput');//样式改变
	$('#phoneCon .modelList table td input').attr('readonly','readonly');//input变为只读状态
	//点击取消恢复input的默认值
	$('#phoneCon .modelList tr').each(function(index,element){
		var zhi01 = $(element).children('.number01').attr('value');
		$(element).children('.number01').val(zhi01);
		//alert(zhi);
		var zhi02 = $(element).children('.number02').attr('value');
		$(element).children('.number02').val(zhi02);
	})
	//启用search搜索框
	$('.search .searchText').removeAttr('readonly');
	//鼠标划上input背景色和字体颜色还原
	$('#phoneCon .modelList table').addClass('blueBg');
	//查找之前点击后改变的背景白色  使之恢复原位置的蓝色
	//$('.whiteBgClick').addClass('blueBgClick');
	$('.modelList table tr').removeClass('blueBgClick');
	if($('.modelList table tr').hasClass('whiteBgClick')){
		$('.whiteBgClick').addClass('blueBgClick');
		$('.modelList table tr').removeClass('whiteBgClick');
	}
	//删除所有的sign类名
	$('.modelList table tr').removeClass('sign');
}

function properNo(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#properedit').children('.editBtn').show();
	//每列发生样式变化(不可输入、.title加去边框、可删除按钮隐藏)
	$('#phoneCon .properList ul').removeClass('editInput');//样式改变
	$('#phoneCon .properList ul li input,#phoneCon .properList .title input').attr('readonly','readonly');//input变为只读状态
	//点击取消恢复input的默认值(要先恢复默认值再移除)
	$('#phoneCon .properList li').each(function(index,element){
		var zhi = $(element).find('input').attr('value');
		$(element).find('input').val(zhi);
		//把编辑之前的size复原(通过myattr获取)
		var mysize = $(element).find('input').attr('myattr');		
		$(element).find('input').attr('size',mysize);
		console.log(mysize);
	})
	//括号显示
	$('.properList .properLine ul li .bra').show();
	//删除修改的标志sign
	$('.modelList ul li').each(function(index,element){
		$(element).removeClass('sign');
	});

}
//=== 点击保存按钮的时候 ===
function modelYes(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#modeledit').children('.editBtn').show();
	//控制手机型号列表发生变化
	$('#phoneCon .modelList').removeClass('editInput');
	$('#phoneCon .modelList table td input').attr('readonly','readonly');
	//点击保存让把改变后的input值输入到value中
	$('#phoneCon .modelList tr').each(function(index,element){
		var zhi01 = $(element).children('.number01').val();
		$(element).children('.number01').attr('value',zhi01);	
		var zhi02 = $(element).children('.number02').val();
		$(element).children('.number02').attr('value',zhi02);
	})
	//启用search搜索框
	$('.search .searchText').removeAttr('readonly');
	//鼠标划上input背景色和字体颜色还原
	$('#phoneCon .modelList table').addClass('blueBg');
	//$('.whiteBgClick').addClass('blueBgClick');
	//查找之前点击后改变的背景白色  使之恢复原位置的蓝色	
	$('.modelList table tr').removeClass('blueBgClick');
	if($('.modelList table tr').hasClass('whiteBgClick')){
		$('.whiteBgClick').addClass('blueBgClick');
		$('.modelList table tr').removeClass('whiteBgClick');
	}
}
function properYes(obj){
	$(obj).parent('.savaBtns').hide();
	$(obj).parents('#properedit').children('.editBtn').show();
	//每列发生样式变化(不可输入、.title加去边框、可删除按钮隐藏)
	$('#phoneCon .properList ul').removeClass('editInput');//样式改变
	$('#phoneCon .properList ul li input').attr('readonly','readonly');//input变为只读状态
	//点击保存让把改变后的input值输入到value中
	$('#phoneCon .properList li').each(function(index,element){
		var zhi = $(element).find('input').val();
		$(element).find('input').attr('value',zhi);
		//判断每个input中的正负号给括号添加相应的颜色
		var zhizhi = $(element).find('input').val();
		var symbol = zhizhi.substring(0,1);
		if(symbol == "+"|| symbol > 0){
			$(element).children('.bra').css('color','#ff7160');
			$(element).children('input').css('color','#ff7160');
		}else if(symbol == "-"){
			$(element).children('.bra').css('color','#7db3ff');
			$(element).children('input').css('color','#7db3ff');
		}
		//更新myattr的值
		var myattr = $(element).find('input').attr('size');		
		$(element).find('input').attr('myattr',myattr);
	})
	//括号显示
	$('.properLine ul li .bra').show();
	//删除修改的标志sign
	$('.modelList ul li').each(function(index,element){
		$(element).removeClass('sign');
	});

}
//给brand和model的选中li加背景色
function blueBgLi(obj){
	$(obj).addClass('blueBgClick').siblings().removeClass('blueBgClick');
	$(obj).addClass('sign').siblings().removeClass('sign');
}

//input宽度自适应
function checkLength(obj) {
    var oTextCount = document.getElementsByClassName("inputText");   
    iCount = obj.value.replace(/[^\u0000-\u00ff]/g,"aa");
    oTextCount.innerHTML = "<font color=#FF0000>"+ iCount.length+"</font>";
    obj.size=iCount.length+2;
}
//键盘输入数值的时候判断正负号添加相应颜色
function textColor(obj){
	//判断每个input中的正负号给括号添加相应的颜色
		var zhi = $(obj).val();
		var symbol = zhi.substring(0,1);
	//alert(symbol);
	if(symbol == "+"|| symbol > 0){
		$(obj).siblings('.bra').css('color','#ff7160');
		$(obj).css({'color':'#ff7160','border-color':'#ff7160'});
	}else if(symbol == "-"){
		$(obj).siblings('.bra').css('color','#7db3ff');
		$(obj).css({'color':'#7db3ff','border-color':'#7db3ff'});
	}
}

//页面加载完后给正负值添加相应颜色
function textColors(){
	$('#phoneCon .properList li').each(function(index,element){
		//判断每个input中的正负号给括号添加相应的颜色
		var zhi = $(element).find('input').val();
		var symbol = zhi.substring(0,1);
		if(symbol == "+" || symbol > 0){
			$(element).find('.bra').css('color','#ff7160');
			$(element).find('input').css('color','#ff7160');
		}else if(symbol == "-"){
			$(element).find('.bra').css('color','#7db3ff');
			$(element).find('input').css('color','#7db3ff');
		}
	})
}

$(function(){
	//获取屏幕的宽度
	var winWidth = $()
	//给properList的ul加宽度
	var properULwidth = $('.properList').width();
	$('.properLine ul').width(properULwidth - 130);
    $(window).resize(function () {
		var properULwidth = $('.properList').width();
		$('.properLine ul').width(properULwidth - 130);
    });

})
function pri(obj){
	var k = $(obj).attr('data-pri');
	var v = $(obj).val();	
	if(k != v){
		$(obj).parent('li').addClass('sign');
	}
	/*else{
		$(obj).parent('li').removeClass('sign');
	}*/
};
/*function priVal(){
	var content='';
	$('.modelList ul .sign').each(function(index,element){
		var dateVal = $(element).attr('data-val');
		var number01 = $(element).find('.number01').val();
		var number02 = $(element).find('.number02').val();
		var val=dateVal+','+number01+','+number02;
		content = content + val+'/';
	});
	alert(content);
};*/