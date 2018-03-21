/**
 * 
 */
/**
* 查看报价详情
**/
function ViewTransactions(){
	//获取验证码
	$.ajax({
		   type: "POST",
		   url:  GetcodeUrl,
		   data: "mobile="+mobile,
		   dataType:"json",
		   beforeSend: function(){
			     time(obj);
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if (data.status == request_succ) {
				 alert(data.msg);
			 }
			 if(data.status != request_succ){
				 alert(data.msg);
			 }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ 
			   
		   }
	}); 
}