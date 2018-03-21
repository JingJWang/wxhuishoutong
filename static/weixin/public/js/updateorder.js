function updateorder(){
	    var number=$("#number option:selected").val()
		$.ajax({ 
			 type: "post",
			 url: "do/index.php",
			 data:{action:'updateorder',mobile:$("#h_tel").val(),province:$("#h_province").val(),city:$("#h_city").val(),county:$("#h_dist").val(),address:$("#h_addinfo").val(),num:number,id:$("#orderid").val()},
			 dataType:"json",
			 success: function(result){
				 var data=eval(result);
				 alert
				 if(data.status==0){
					alert(data.info);
					WeixinJSBridge.call('closeWindow');
				 }else{
					 alert(data.info);
				 }
			 },
	});
	
}