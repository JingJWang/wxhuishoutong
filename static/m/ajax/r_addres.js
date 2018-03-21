/**
 * 
 */
function Choice(qid,oid){
	var u="/index.php/nonstandard/quote/ChoiceQuote";
	/*var city=$("#city").html();
	if(city == ''){
		alert('地址信息必填!');return false;
	}
	var quarters=$("#quarters").html();
	if(quarters == ''){
		alert('详细信息不能为空');return false;
	}*/
	var mobile=$(".cont").val();
	if(!mobile.match(/^(1[3|4|5|7|8][0-9]{9})$/)){
		alert('电话格式不正确');return false;
	}
	/*var name=$("#name").html();
	if(name == ''){
		alert('姓名不能为空');return false;
	}*/
	var d="qid="+qid+'&oid='+oid+'&mobile='+mobile;
    var f=function(data){
		response=eval(data);
		if(response['status'] == request_succ){
			UrlGoto(response['url']);
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
	}    
	AjaxRequest(u,d,f);
}
var Addres={
		submit:function(){
			var addres=$("#addresinfo").serialize();
			var u = '/index.php/shop/realgood/addadress';
		    var d = addres+'&isit=2';
		    var f = function(res){
		    	var response=eval(res);
		    	if(response['status'] == request_succ){	    		
		    		location.reload() 
		    	}else{
		    		alert(response['msg']);
		    	}
		    }
		    AjaxRequest(u,d,f);
		}
	    
}
