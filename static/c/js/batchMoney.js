//ajax-手机品牌
function loadmodel(){
	var u = '/index.php/center/option/getBrand';
	var d = '';
	var f = function(res){
		var data = eval(res);
		if(data.status == request_succ){
			var content = '';
			$.each(data.data,function(k,v){
				if(v.name != '贵金属' && v.name!='IUNI' && v.name!='乐视' && v.name!='戴尔' && v.name!='夏普' && v.name!='多普达' && v.id!='540'){
					content = content + '<label class="lableInp">'
					+'<input class="brand_inp" type="checkbox" value="" id="'+v.id+'" onclick="modelCheck(this);" name="modelChe" data-click="" data-name="'+v.name+'">'
					+'<span class="brand_j">'+v.name+'</span>'
					+'</label>'
				}
			})
			content = content + '<input type="hidden" class="saveModel" data-id="" value="">'
			$('#model_j').html(content);
		}else{
			alert('加载品牌列表出现异常!');
		}
	}
	AjaxRequest(u,d,f);
}
//style-checkbox
function modelCheck(obj){
	var checkedOfAll=$(obj).prop("checked"); 
	var moreModel = [];
	var moreId = [];
	if(checkedOfAll == ''){
		$(obj).attr('data-click','');
		$(obj).removeClass('testInp');
	}else{
		$(obj).attr('data-click','check');
		$(obj).addClass('testInp');
	}
	$('.testInp').each(function(){
		moreModel += $(this).attr('data-name')+',';		//取checkbox的name
		moreId += $(this).attr('id')+',';			//取checkbox的id
	})
	$('.saveModel').val(moreModel).attr('data-id',moreId);	//把取到的值放在隐藏的input
	$('.showCheckModel').html(moreModel);
}
//全选
$("#selectAll").click(function () {
	var moreModel = [];
	var moreId = [];
    $(".brand_inp").prop("checked", "checked"); 
    $(".brand_inp").addClass("testInp");
	$('.testInp').each(function(){
		moreModel += $(this).attr('data-name')+',';		//取checkbox的name
		moreId += $(this).attr('id')+',';			//取checkbox的id
	})
	$('.saveModel').val(moreModel).attr('data-id',moreId);	//把取到的值放在隐藏的input
	$('.showCheckModel').html(moreModel);
});  
//全不选
$("#unSelect").click(function () {  
     $(".brand_inp").removeClass("testInp").prop("checked","");  
     $(".brand_inp").removeAttr("data-click","check");
     $('.saveModel').val('').attr('data-id','');
 	 $('.showCheckModel').html('');
});  
//style-点击radio显示金额或者比例
function showJ(obj){
	$('.jine').show().siblings().hide();
}
function showB(){
	$('.bili').show().siblings().hide();
}
//style-二次弹框
function showShadow(obj){
	if($(obj).attr('data') == 0){
		$('.posi_j_one').show().next('.posi_j_two').hide();
	}else{
		$('.posi_j_two').show().prev('.posi_j_one').hide();
		$('.showQujian').html($('.nowoption').val());
	}
	$('.shadow').show();
}
//style-点击按钮隐藏弹框
function hidebtn(){
	$('.shadow').hide();
	$('.posi_j').hide();
}
//save-radio
function dataRadio(obj){
	if($(obj).attr('data-radio') == 1){
		$('.hideRadio').attr('data','元');
		$(obj).addClass('radck').parent().next().find('input').removeClass('radck');
		$('.final_font').attr('data','1');
		$(obj).val(1);
	}else{
		$('.hideRadio').attr('data','%');
		$(obj).val(2);
		$(obj).addClass('radck').parent().prev().find('input').removeClass('radck');
		$('.final_font').attr('data','2');
	}
}
//保存radio对应的input的值
function saveInpVal(obj){
	var radioVal = $(obj).val();
	$('.hideRadio').val(radioVal);
	var danwei = $('.hideRadio').attr('data');
	$('.final_font').html(radioVal+danwei);
	$('.final_typeInp').html($('.hover_inp').val());
}
//获取调价的加减
function oper(obj){
	$(obj).addClass('hover_inp').siblings().removeClass('hover_inp');
	if($(obj).attr('data') == 1){
		$('.final_typeInp').html('加价').attr('data','1');
	}else{
		$('.final_typeInp').html('减价').attr('data','2');
	}
}
//tab切换--手机市价价格
function changeForm(obj){
	var num = $(obj).index();
	$(obj).addClass('clickLi').siblings().removeClass('clickLi');
	$('.tabdiv .model').eq(num).show().siblings().hide();
	$('.save_j').attr('data',num);
};
//点击下拉菜单
function rangepir(obj){
	if($('.priRange').is(":hidden")){
		$('.priRange').show();
	}else{
		$('.priRange').hide();
	}
};
function saverang(obj){
	$('.nowoption').val($(obj).html());
	$('.priRange').hide();
};
