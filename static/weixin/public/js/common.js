var request_succ    =  1000;
var request_fall    =  3000;
var checkcode_fall	=  2001;
var access_fall     =  '';

var request_fall_msg = '请求出现异常!';
function login(){
	var code_char=$("#code_char").val();
	var u="/index.php/userlogin/login/userlogin";
	var d={name:$("#login_name").val(),pwd:$("#login_pwd").val(),code:code_char};
	var f=function(res){
		var data=eval(res);
			if(data['status'] == checkcode_fall){
				$("#errorinfo").show();
				$("#errorinfo").html(data['info']);
			}
			if(data['status'] == request_fall){
				$("#errorinfo").show();
				$("#errorinfo").html(data['info']);
			}
			if(data['status'] == request_succ){
				UrlLocation(data['url']);
			}
	}
	AjaxRequest(u,d,f);
}
/**
 * common 退出当前登录
 */
function  login_out(){
	$.ajax({ 
		type: "get",
		url: "/index.php/userlogin/login/loginout",
        data:{action:'check_online'},
        dataType:"json",
        success: function(result){
			var data=eval(result);
            if(data.status == '1'){
				 UrlLocation(data.info);
			}
		 },
	});
}
/**
 * common 校验当前用户是否已经登录
 */
function  check_online(){
	$.ajax({ 
		 type: "get",
		 url: "do/index.php",
         data:{action:'check_online'},
         dataType:"json",
         success: function(result){
			 var data=eval(result);
             if(data.status == '1'){
				 UrlLocation(data.info);
			 }else{}
		 },
	});
}
/**
 * common 跳转方法
 * @param  string   url   跳转地址
 */
var UrlLocation=function(url) {
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
var  AjaxRequest=function(u,d,f){
	var result= '';
	$.ajax({
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	//$("#turn_gif_box").css('display','block');
        },
        success:function(res){
        	  f(res);
        },
        complete: function(res){
       	 	//$("#turn_gif_box").css('display','none');
        },
        error:function(msg){
            alert(request_fall_msg+msg);
        }
    });
	
}
