function login(){
	var user=checkPhone();	
	var pwd=checkpassword();
	if(!user || !pwd){
		return false;
	}
	var u='/index.php/center/login/';
	var d='user='+$(".username").val()+'&pwd='+$(".password").val();
	var f=function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			if (response['url']!='') {
			    location.href=response['url'];
			};
		};
	    if(response['status'] == request_fall){
	    	alert(response['msg']);
	    }
	}
	AjaxRequest(u,d,f);
}
function isOnline() {
	var u='/index.php/center/login/isOnLine';
	var d='';
	var f=function(res){
		var response = eval(res);
	    if(typeof(response['msg']) != 'undefined' && response['msg'] != ''){
	    	alert(response['msg']);
	    }
	    if(typeof(response['url']) != 'undefined' && response['url'] != '' 
	    	&& response['status'] != request_fall){
	    	location.href=response['url'];
	    }
	    
	}
	AjaxRequest(u,d,f);
}
$(document).keyup(function(e){ 
    var curkey = e.which; 
    if(curkey == 13){ 
        $(".loginNow").click(); 
        return false; 
    } 
});