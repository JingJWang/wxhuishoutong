/**
 * 
 */
//批量回收
var More={
		submit:function(){
			var content=$("#moredata").serialize();
			var u='/index.php/nonstandard/submitorder/moreSubmit';
			var d=content;
			var f=function(res){
				var resp=eval(res);
				if(resp['status'] == request_fall){
					if(resp['msg'] != '' ){
						alert(resp['msg']);
						if(resp['url'] != '' ){
							UrlGoto(resp['url']);
						}
					}					
				}
				if(resp['status'] == request_succ){
					if(resp['url'] != '' ){
						UrlGoto(resp['url']);
					}
				}
			}
			AjaxRequest(u,d,f);
		}
		
		
}
