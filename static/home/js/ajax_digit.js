/**
 * 数码
 */


/**
 * 上传图片
 */
function UploadImg(submitData){
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/submitorder/UploadImg",
		   data: submitData,
		   dataType:"json",
		   async:false,
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if (data.status == request_succ) {
				 alert(data.data);
				 var attstr  = '<div class="swiper-slide"><img src="'+data.data+'" alt=""/></div>'; 
				 var imglist = $("#imglist").html();
				 $("#imglist").html(imglist+attstr);
			 }
			 if(data.status == request_fall){
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
 *提交订单
 */
function Submitorder(order,attr){
	$.ajax({
		   type: "POST",
		   url: "/index.php/nonstandard/submitorder/submitorder_electronic",
		   data:{order:'{'+order+'}'},
		   dataType:"json",
		   async:false,
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if(data.status == request_succ) {
				 UrlGoto(data.url);
			 }
			 if(data.status  !=  request_succ){
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
 * 数码产品----校验订单数据
 */
function checkdata(status) {
	var order = '';
	var latitude	  = $("#latitude").val(); //纬度
	var longitude	  = $("#longitude").val();//经度
	//校验是否获取到当前用户的坐标
	if(latitude == '' || longitude == ''){
		alert('没有获取到您的坐标!');
		return false;
	}
	order='"latitude":"'+latitude+'","longitude":"'+longitude+'","orderstatus":"'+status+'"';
	//校验地址
	var province = $("#sfdq_tj").val();
	var city     = $("#csdq_tj").val();
	var county   = $("#qydq_tj").val();
	if( province == '' || city == '' || county == ''){
		alert('城市信息不能属于必填选项!');
		return false;
	}
	order  = order +',"province":"'+province+'","city":"'+city+'","county":"'+county+'"';
	//校验详细地址
	var quarters = $("#quarters").val() ;
	//校验是否可用
	var isused = $("input[name='danbao']:checked").val();
	if(isused == 1 || isused == -1){
		order = order + ',"isused":"'+isused+'"';	
	}else{
		alert('是否可用,是必填选项!');
		return false;
	}
    //校验购买日期
	var date=$("#scroller").val()
	if( date == ''){
		alert('购买日期,是必填选项!');
		return false;
	}
	order = order + ',"residential_quarters":"'+quarters+'"';
	Submitorder(order);
}