var request=''

function UrlLocation(url){
	window.location.href=url;
}
function login(){
	var code_char=$("#code_char").val();
	$.ajax({ 
			type: "post",
			url: "/index.php/userlogin/login/userlogin",
			data:{name:$("#login_name").val(),pwd:$("#login_pwd").val(),code:code_char},
			dataType:"json",
			success: function(result){
				var data=eval(result);
				switch (data.status) {					
					case '2001':
						$("#errorinfo").show();
						$("#errorinfo").html(data.info);
						break;
					case '0':
						$("#errorinfo").show();
						$("#errorinfo").html(data.info);
						break;
					case '1':
						UrlLocation(data.url);
						break;
					default:
						break;
				}
			},
		});
}
//退出登陆
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