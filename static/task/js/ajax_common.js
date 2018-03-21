var   request_succ   =   1000;//请求成功
var   request_fall	 =	 3000;//请求失败
var   msg_request_fall = '出现异常,请稍后';
//跳转
var UrlGoto=function name(url) {
	if(url != ''){
		location.href=url;
	}else{
		return '路径为空!';	
	}
}
/**
 * common  请求方法
 * @param int     u    请求地址
 * @param string  d    请求数据
 */
var AjaxRequest=function(u,d,f){
	var result= '';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	$('#caseBlanche').css('display', 'block');
        },
        success:function(res){
        	  f(res);
        },
        complete: function(){
       	 	$('#caseBlanche').css('display', 'none');
       	},
        error:function(){
          
        }
    });
}