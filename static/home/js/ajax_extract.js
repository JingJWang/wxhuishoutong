$(document).ready(function() {
	balance();
	$("#getcode_char").click(function(){
		$(this).attr("src",'/codeimg/code_char.php?name=1&' + Math.random());
	});

});

function balance(){	
	var u='/index.php/nonstandard/center/balance';
	var d='';
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$("#balance").html(response['data']);
		}		
	}
	AjaxRequest(u,d,f);
}
function extract(){	
	var code=$("#noteCode").val();
    if(code == ''){
    	alert('验证码为必填选项!');
    	return  false;
    }
    var  pic = $("#cashIput").val();
    if(pic == '' || pic == 0){
    	alert('提现金额为必填选项!');
    	return  false;
    }
    /*var  name = $("#nameInput").val();
    if(name == ' ' ){
    	alert('请输入实名认证姓名!');
    	return  false;
    } */   
	var u='/index.php/nonstandard/center/extract';
	var d='pic='+pic+'&code='+code;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
		if(response['status'] == request_succ){
			alert(response['msg']);
			UrlGoto(response['url']);
		}		
	}
	AjaxRequest(u,d,f);
}

function zfbextract(){
	var code=$("#noteCode").val();
    if(code == ''){
    	alert('验证码为必填选项!');
    	return  false;
    }
    var  pic = $("#cashIput").val();
    if(pic == '' || pic == 0){
    	alert('提现金额为必填选项!');
    	return  false;
    }
    var acc = $("#account").val();
    if(acc == ''){
    	alert('请支付宝输入账号!');
    	return  false;
    }
    var  name = $("#nameInput").val();
    if(name == ' ' ){
    	alert('请输入实名认证姓名!');
    	return  false;
    }
    var u='/index.php/nonstandard/center/zfbextract';
	var d='pic='+pic+'&code='+code+'&name='+name+'&acc='+acc;
	var f=function(res){
		var response=eval(res);
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
		if(response['status'] == request_succ){
			alert(response['msg']);
			UrlGoto(response['url']);
		}		
	}
	AjaxRequest(u,d,f);
}