/**
 * 
 */
function confirm(id){
	var u='/index.php/nonstandard/order/ConfirmQuote';
	var d='id='+id;
	var f=function(res){
		var response=eval(res);
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