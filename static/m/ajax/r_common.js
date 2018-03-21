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
function isOnline(){
	var code = getUrlParam('code');
	if(typeof(code) ==  null){
		code='';
	}
	var id = getUrlParam('openid');
	if(typeof(opneid) == null){
		opneid='';
	}
	var u = '/index.php/nonstandard/system/isOnline';
	var d = 'code='+code+'&id='+id;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {			
			
		}
		if(response['status'] == request_fall){
			if ( response['msg'] != '' ) {
				alert(response['msg']);
			}
			if ( response['url'] != '' ) {
				UrlGoto(response['url']);
			}
		}
	}
	AjaxRequest(u,d,f);
}
function loginOut(){
	var u = '/index.php/nonstandard/center/loginOut';
	var d = '';
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {			
			if ( response['url'] != '' ) {
				UrlGoto(response['url']);
			}
		}
		if(response['status'] == request_fall){
			
		}
	}
	AjaxRequest(u,d,f);
	
}