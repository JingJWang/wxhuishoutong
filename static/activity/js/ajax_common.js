var   request_succ   =   1000;
var   request_fall	 =	 3000;
var   msg_request_fall = '出现异常,请稍后';
//跳转
var UrlGoto=function name(url) {
  if(url != ''){
    location.href=url;
  }else{
    return '路径为空!'; 
  }
}
//ajax
var  AjaxRequest=function(u,d,f){
	var result= '';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	$("#turn_gif_box").css('display','block');
        },
        success:function(res){
        	  f(res);
        },
        complete: function(res){
       	 	$("#turn_gif_box").css('display','none');  
       	 	return result;
       	},
        error:function(msg){
            alert(msg_request_fall+msg);
        }
    });
	
}