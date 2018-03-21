var    request_fall  = 3000;
var    request_succ  = 1000;

var UrlGoto=function(url) {
	if(url != ''){
		location.href=url;
	}else{
		return '路径为空!';	
	}
}
var  AjaxRequest=function(u,d,f){
	var result= '';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	
        },
        success:function(res){
        	  f(res);
        },
        complete: function(){
       	},
        error:function(){
          
        }
    });
	
}
function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}