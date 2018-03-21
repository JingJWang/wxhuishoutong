/**
 * 奢侈品模块
 */
mySwiperOC();
OC();
shenFen();
mySwiperOC();
$(document).ready(function(){
	//点击属性事件
	$('.attr').click(function(){
		var  content   = $(this).html();
		var  obj  = $(this).attr('data-key');
		$('#'+obj).val(content);
		
	});
	//绑定点击属性事件
	$(document).on('click',".attr ",function(){
		var  content   = $(this).html();
		var  obj  = $(this).attr('data-key');
		$('#'+obj).val(content);
	});
	//绑定品牌点击事件
	$(document).on('click',"#brandlist p",function(){
		var  brandid   = $(this).attr('data-key');
		var  brandname = $(this).attr('data-val');
		$('#brandnames').val(brandname);
		$('#brandids').val(brandid);
	});
	//绑定点击分类事件
	$(document).on('click',"#prolist p",function(){
		var  proid   = $(this).attr('data-val');
		var  proname = $(this).html();
		$('#proids').val(proid);
		$('#pronames').val(proname);
		GetBrendList(proid);
		GetAttr(proid);
	});
	//上传图片
	$('#uploadphoto').localResizeIMG({
	      width: 1024,//图片大小
	      quality: 0.8,//压缩比例
	      success: function (result) {  
			   var submitData={
					base64_string:result.clearBase64, 
			   }; 
			   UploadImg(submitData);
	      }
	});
	Getprolist();
});
/**
 * 获取奢侈品下产品分类
 */
function Getprolist(){
	 $.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/option/appliance_type",
		   data: '',
		   dataType:"json",
		   beforeSend: function(){
				 $("#turn_gif_box").css('display','block');
		   },
		   success: function(data){
			   if(data.status == 1000)
			   var list='';
			   $.each(data.data, function(i, value) {
				   list = list +'<p data-val="'+value['id']+'">'+value.name+'</p>';
			   });
			   response=eval(data);
			   $("#proname").html(response['data']['0']['name']);
			   $('#proids').val(response['data']['0']['id']);
			   $('#pronames').val(response['data']['0']['name']);
			   $("#prolist").html(list);
			   GetBrendList(response['data']['0']['id']);
			   OC();
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   
		   }
	}); 	
}
/**
*  获取分类的下的品牌列表
*/
function  GetBrendList(id){
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/option/electronic_brand",
		   data: 'id='+id,
		   dataType:"json",
		   beforeSend: function(){
				 $("#turn_gif_box").css('display','block');
		   },
		   success: function(data){
			   	   if(data.status == 1000)
				   var list='';
				   $.each(data.data, function(i, value) {
					   var i=i+1;
					   list = list +'<p  data-val="'+value['name']+'" data-key="'+value['id']+'">'+value['name']+'</p>';
				   });
				   response=eval(data);
				   $("#branname").html(response['data']['0']['name']);
				   $('#brandnames').val(response['data']['0']['name']);
				   $('#brandids').val(response['data']['0']['id']);
				   $("#brandlist").html(list);
				   OC();
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   
		   }
	}); 	
}
/**
 * 上传图片
 */
function mySwiperOC(){
    var  mySwiper = new Swiper ('.swiper-container', {
        direction: 'horizontal',
        grabCursor : true,
        freeMode : false,
        slidesPerView : 'auto'
    });
}
/**
 * 上传图片
 */
function UploadImg(submitData){
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/submitorder/UploadImg",
		   data: submitData,
		   dataType:"json",
		   success: function(data){
			 if (data.status == 1000) {
				 alert(data.data);
				 var attstr  = '<div class="swiper-slide"><img src="'+data.data+'" alt=""/></div>'; 
				 var imglist = $("#imglist").html();
				 $("#imglist").html(imglist+attstr);
			 }
			 if(data.status == 3000){
				 alert(data.msg);
			 }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
				
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   
		   }
	}); 
}
/**
 * 提交订单
 */
function CheckData(status){
	//获取属性的值
	var d = {};
	var t = $('#attr').serializeArray();
	$.each(t, function() {
	   d[this.name] = this.value;
	});
	var attr=JSON.stringify(d);
	var order = '';
	var latitude	  = $("#latitude").val(); //纬度
	var longitude	  = $("#longitude").val();//经度
	//校验是否获取到当前用户的坐标
	if(latitude == '' || longitude == ''){
		alert('没有获取到您的坐标!');
		return false;
	}
	var pronames = $("#pronames").val();
	var proids =$("#proids").val();
	var brandnames = $("#brandnames").val();
	var brandids = $("#brandids").val();
	order='latitude='+latitude+'&longitude='+longitude+'&orderstatus='+status+
	'&pronames='+pronames+"&proids="+proids+"&brandnames="+brandnames+"&brandids="+brandids;
	var selling_price = $("#selling_price").val();  //价格
	//校验出售价格
	if(typeof selling_price == 'undefined' ){
			selling_price='';
	}
	var 	make = $("#textarea").val();//备注
	order = order + '&selling_price='+selling_price+'&make='+make;
	//校验地址
	var province = $("#sfdq_tj").val();
	var city     = $("#csdq_tj").val();
	var county   = $("#qydq_tj").val();
	if( province == '' || city == '' || county == ''){
		alert('城市信息不能属于必填选项!');
		return false;
	}
	order  = order +'&province='+province+'&city='+city+'&county='+county;
	//校验详细地址
	var quarters = $("#quarters").val() ;
	var number   = $("#number").val();
	//校验是否再保
	var goods = $("input[name='goods']:checked").val();
	if(goods == 1 || goods == -1){
		order = order + '&isused='+goods+'&attr='+attr+'&residential_quarters='+quarters+'&house_number='+number;	
	}else{
		alert('是否再保,是必填选项!');
		return false;
	}
	Submitorder(order);
}
/**
 *提交订单
 */
function Submitorder(order){
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/submitorder/submitorder_appliance",
		   data:order,
		   dataType:"json",
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if(data.status == 1000) {
				 alert(data.msg);
				 location.href=data.url;
			 }
			 if(data.status ==  3000){
				 alert(data.msg);
			 }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   
		   }
	}); 
}
/**
 * 获取分类属性
 */
function GetAttr(type){
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/option/GetApplinaceAtt",
		   data:'type='+type,
		   dataType:"json",
		   success: function(data){
			 if(data.status == 1000) {
				 response = eval(data);
				 var content='<div class="tit2"></div>';
				 $.each(response['data']['key'], function(i, value) {
					 	if( response['data']['val'][i] != '-1'){
					 		content = content +'<div class="modeBox selects">'+
                            '<div class="hBox oh"><div class="fl">'+value+'</div>'+
                            '<div class="fr pr16  fx_btn"><span class="fx_value">请选择</span><span class="bg bg1 fx_bg"></span></div>'+
                            '</div><div class="selectBox  fx_options">';
					 		$.each(response['data']['val'][i] , function(n, val) {
					 			content = content + '<p data-key="'+value+'" class="attr">'+val+'</p>';
					 		});
					 		content = content +'</div><input name="'+i+'" id="'+value+'" type="hidden" value=""></div></div>';
					 	}else{
					 		content = content + '<div class="modeBox selects"><div class="hBox oh">'+
	                             '<div class="fl">'+value+'</div>'+
	                             '<div class="fl inputBox"><input type="text" name="'+value+'" placeholder="请输入'+value+'"/></div>'+
	                             '</div></div>';
					 	}
				});
				 $("#attrcontent").html('');
				 $("#attrcontent").html(content);
				 OC();
			 }
			 if(data.status ==  3000){
				 alert(data.msg);
			 }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
				
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ //上传失败 
			   
		   }
	}); 
}
