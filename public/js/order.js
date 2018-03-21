//提交订单
function orderpost(){
	    var number=$("#number option:selected").val()
	    var mobile=$("#h_tel").val();
	    var province=$("#h_province").val();
	    var city=$("#h_city").val();
	    var dist=$("#h_dist").val();
	    var addinfo=$("#h_addinfo").val();
	    var type= $("#h_type").val();
	    if(mobile !=''&& addinfo!='' &&type!=''&&number!=0){
	    	$.ajax({ 
				 type: "post",
				 url: "do/index.php",
				 data:{action:'orderpost',openid:$("#openid").val(),mobile:mobile,province:province,city:city,county:dist,address:addinfo,type:type,num:number},
				 dataType:"json",
				 success: function(result){
					 var data=eval(result);
					 if(data.status==0){
						 UrlLocation(data.info);
					 }else{
						 alert(data.info);
					 }
				 },
	    	});
	    }else{
	    	$("#message").html('必填选项不可为空');
	    }
}