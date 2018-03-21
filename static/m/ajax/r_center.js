/**
 * 
 */
function editUserData(){
	var u = '/index.php/nonstandard/wxuser/update_wxuserinfo';
	var city = $("#city").val();
	var address	= $("#address").val();
	if(city == ''){
		alert('为了更好的服务您,省名必填!');
		return false;
	}
	if(address == ''){
		alert('为了更好的服务您,市名必填!');
		return false;
	}
	var d ='address='+address+'&city='+city;
	var f=function(res){
		var response =eval(res);
		if(response['status'] == request_succ){
			alert(response['msg']);
			UrlGoto(response['url']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
} 