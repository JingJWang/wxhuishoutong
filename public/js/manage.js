jQuery(function(){
	//已处理订单详情的伸缩
	$(".y-orderlist-child-a").live('click', function(){
		$(this).parent().next().toggle(200);		
	});
	$(".h-order a").click(function(){
		var biaoshi=$(this).attr('biaoshi');
		$(".checked").removeClass("checked");
		$(this).addClass("checked");
		if(biaoshi=='d'){			
			order_list_no();
		}else{
			order_list_yes();			
		}
	});
	userinfo();
	order_list_no();
	order_list_num();
});
//获取登陆用户信息
function userinfo(){
	$.ajax({ 
		 type: "get",
		 url: "/index.php/userlogin/login/userinfo",
         data:'',
         dataType:"json",
         success: function(result){
			 var data=eval(result);
				$('#username').html(data.data.name+"("+data.data.power_name+")");
				$('#useraddress').html('归属地区'+data.data.address);
				if(data.data.pay_type==1){ $('#pay_type').html('支付方式:红包'); }else{ $('#pay_type').html('支付方式:现金');}
		 },
	});
}
function order_list_num(){
	$.ajax({ 
		 type: "get",
		 url: "/index.php/cooperation/cooperation/listnum",
         data:'',
         dataType:"json",
         success: function(result){
			 var data=eval(result);
			$("#order_type_yes").html('('+data['data']['num']['0']['num']+')');
			$("#order_type_no").html('('+data['data']['num']['1']['num']+')');
		 },
	});
}
//查询该地区还未处理的订单
function order_list_no(){
	$.ajax({ 
		 type: "get",
		 url: "/index.php/cooperation/cooperation/handleorder",
         data:{type:'1'},
         dataType:"json",
         success: function(result){
			    var data=eval(result);
				var content='';
				if(data['data'] != '0'){
					for(var i=0;i<data['data'].length;i++){
						if(data['data'][i]['wx_name'] ==null || data['data'][i]['wx_name']==' ' ){
							name='';
						}else{
							name=data['data'][i]['wx_name'];
						}
						 content+='<div class="d-orderlist-child"><a href="javascript:void(0)" class="d-orderlist-child-a" onclick="edit_order('+data['data'][i]['id']+');"><div class="d-orderlist-ms">订单编号'+data['data'][i]['order_randid'].substring(10)+'---昵称:'+name+'</div></a></div>';
					}
					$("#content-order-yes").html(content);
				}
		 },
	});
}
//我已经处理完毕的订单
function order_list_yes(){
	$.ajax({ 
		 type: "get",
		 url: "/index.php/cooperation/cooperation/handleorder",
         data:{type:'0'},
         dataType:"json",
         success: function(result){
			    var data=eval(result);
				var content='';
				for(var i=0;i<data['data'].length;i++){
					 if(data['data'][i]['wx_name'] ==null || data['data'][i]['wx_name']==' ' ){
						name='';
					}else{
						name=data['data'][i]['wx_name'];
					}
					content+='<div class="y-orderlist-child">'+'<div class="y-orderlist-ms">订单编号<a href="javascript:void(0)" onclick="look_order('+data['data'][i]['id']+');">'+data['data'][i]['order_randid'].substr(10)+'---昵称:'+name+'</a></div></div>';
					
				}
				$("#content-order-yes").html(content);
		 },
	});
}
//退出登录操作
function  login_out(){
	$.ajax({ 
		 type: "get",
		 url: "/index.php/userlogin/login/loginout",
         data:{action:'login_out'},
         dataType:"json",
         success: function(result){
			 var data=eval(result);
			 if(data.status==1){
				 UrlLocation(data.url);
			 }
		 },
	});
}
//提交需要修改的订单识别
function edit_order(id){
       var order_id=id;
	   $.ajax({ 
		 type: "post",
		 url: "/index.php/cooperation/cooperation/selectedit",
         data:{id:order_id},
         dataType:"json",
         success: function(result){
			 var data=eval(result);
			 if(data.status==1){
				 UrlLocation(data.url);
			 }
		 },
	});
}
//提交需要查看的订单识别
function look_order(id){
     var order_id=id;
     var requesturl='/index.php/cooperation/cooperation/savelookid/'+order_id;
	   $.ajax({ 
		 type: "post",
		 url:requesturl,
         data:'',
         dataType:"json",
         success: function(result){
			 var data=eval(result);
			 if(data.status==1){
				 UrlLocation(data.url);
			 }
		 },
	});
}