var api={
	'searchuser':'/index.php/maijinadmin/adminuser/searchuser',
	'delete_admin_user':'/index.php/maijinadmin/adminuser/delete_admin_user',
	'get_admin_user':'/index.php/maijinadmin/adminuser/get_admin_user',
	'add_edit_admin_user':'/index.php/maijinadmin/adminuser/add_edit_admin_user',
	'select_admin_user_yj':'/index.php/maijinadmin/adminuser/select_admin_user_yj',
	'get_role':'/index.php/maijinadmin/adminuser/get_role'
};
$(document).ready(function(){
	$('#content_ajax_page a').live('click',function(){
		var url=$(this).attr('href');
		searchuser(url);
		return false;//阻止链接跳转
	});
	//时间dialog
	$(".datepicker").datepicker();
	
	//弹出添加员工对话框
	$(".btn-addEmplyee").click(function(){
		$.ajax({ 
			 type: "get",
			 url: api.get_role,
			 data:'',
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status=='1000'){
					 //清空内容
					 $("#dialog-emplyee input").each(function(){
						 $(this).val("");						 
					 });
					 $('#u_power_type').html('');
					 $("#dialog-emplyee select").each(function(){
						 $(this).find('option').eq(0).attr('selected','selected');		 
					 });
					 $("#dialog-emplyee #u_name").removeAttr('disabled');
					 
					 var option='';
					 var row=result['data'].length;
					 for(var i=0;i<row;i++){
						 option+='<option value="'+result['data'][i]['role_id']+'">'+result['data'][i]['role_name']+'</option>'
					 }
					 $('#u_power_type').html(option);
					 $("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
				 }else{
					 alert(result.info);
				 }
			 }
		});
	});
	
	//添加员工
	$(".addemplyee").click(function(){		
		var power_name_val=$("#u_power_type option:selected").html();
		var dataObj={
				id:$("#employeeid").val(),
				xingming:$("#u_xingming").val(),
				power_type:$("#u_power_type").val(),
				power_name:power_name_val,
				maile:$("#u_maile").val(),
				mobile:$("#u_mobile").val(),
				address:$("#u_address").val(),
				pay_type:$("#u_pay").val(),
				name:$("#u_name").val(),
				password:$("#u_password").val(),
				reqpassword:$("#u_reqpassword").val(),
				status:$("#u_status").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.add_edit_admin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 alert(result.info);
				 if(result.status=='1000'){
					 //清空内容
					 $("#dialog-emplyee input").each(function(){
						 $(this).val("");						 
					 });
					 $('#u_power_type').html('');
					 $("#dialog-emplyee select").each(function(){
						 $(this).find('option').eq(0).attr('selected','selected');		 
					 });
					 //关闭dialog
					 $("#dialog-emplyee").dialog('close');
					 location.reload();
				 }
			 }
		});
	});
	
	//修改员工信息
	$(".emplyee-edit").live("click",function(){
		var userid=$(this).attr('orgid');
		var dataObj={
				id:userid
		};
		$.ajax({ 
			 type: "post",
			 url: api.get_admin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);				 
				 if(result.status=='1000'){
					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
					$("#dialog-emplyee #u_xingming").val(result.data.user.xingming);
					$("#dialog-emplyee #u_maile").val(result.data.user.maile);
					$("#dialog-emplyee #u_mobile").val(result.data.user.mobile);
					$("#dialog-emplyee #u_address").val(result.data.user.address);
					$("#dialog-emplyee #u_name").val(result.data.user.name);
					$("#dialog-emplyee #u_pay").val(result.data.user.pay_type);
					$("#dialog-emplyee #u_password").val('');
					$("#dialog-emplyee #u_reqpassword").val('');
					$("#dialog-emplyee #u_status").val(result.data.user.status);
					$("#dialog-emplyee #employeeid").val(result.data.user.id);
					var option='';
					var row=result['data']['role'].length;
					for(var i=0;i<row;i++){
						if(result.data.user.power_type==result['data']['role'][i]['role_id']){
							option+='<option value="'+result['data']['role'][i]['role_id']+'" selected="selected">'+result['data']['role'][i]['role_name']+'</option>'
						}else{
							option+='<option value="'+result['data']['role'][i]['role_id']+'">'+result['data']['role'][i]['role_name']+'</option>'
						}
					}
					$('#u_power_type').html(option);
					
					$("#dialog-emplyee #u_name").attr('disabled','disabled');
				 }else{					 
					 alert(result.info);
				 }
			 }
		});
	});
	
	//禁用员工，设置为无效
	$(".emplyee-delete").live('click',function(){
		var userid=$(this).attr('orgid');
		var dataObj={
				id:userid
		};
		$.ajax({ 
			 type: "post",
			 url: api.delete_admin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 alert(result.info);
				 if(result.status == '1000'){
					location.reload();
				 }
			 }
		});
	});
	
	
	
	//检索员工
	$(".btn-search").click(function(){
		var dataObj={
				xingming:$("#s_xingming").val(),
				address:$("#s_address").val(),
				pay_type:$("#s_pay").val(),
				status:$("#s_status").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.searchuser,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){				 
				 var result=eval(data);
				 
				 if(result.status == '1050'){
					 $("#content_list").html(result.info);
					 $("#content_page").html('');
				 }
				 if(result.status == '3000'){
					 $("#content_list").html(result.info);
					 $("#content_page").html('');
				 }
				 if(result.status == '1000'){
					 var row=result['data'].length;
					 
					 var content='';				 
					 for(var i=0;i<row;i++){
						 content+='<tr><td><a href="javascript:void(0);" orgid="'+result['data'][i]['id']+'" title="查看员工业绩" class="employee-yj">'+result['data'][i]['xingming']+'</a></td><td>'+result['data'][i]['power_name']+'</td><td>'+result['data'][i]['maile']+'</td><td>'+result['data'][i]['mobile']+'</td><td>'+result['data'][i]['address']+'</td>';
						 if(result['data'][i]['pay_type']=="1"){
							content+='<td><font color="red">红包</td>';		 
						 }else{
							content+='<td><font color="blue">现金</td>';
						 }
						 content+='<td><a href="/'+result['data'][i]['weixin_code']+'" target="_blank">查看</a></td><td>'+result['data'][i]['name']+'</td>';
						 if(result['data'][i]['status']=="1"){
							content+='<td>有效</td>';		 
						 }else{
							content+='<td><font color="red">无效</font></td>';
						 }
						 content+='<td><a href="javascript:void(0);" class="emplyee-edit" orgid="'+result['data'][i]['id']+'">修改</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="emplyee-delete" orgid="'+result['data'][i]['id']+'">禁用</a></td></tr>';	 
					 }
					 $("#content_list").html(content);
					 $("#content_page").html('');			
					 $("#content_ajax_page").html(result.page)
				 }			 
			 }
		});		
	});
	
	
	
	//员工业绩查询
	$(".employee-yj").live('click',function(){
		$("#e_time").val('');
		$("#e_order_y").html('0');
		$("#e_order_d").html('0');
		$("#e_weight_yj").html('0');
		$("#e_pic_yj").html('0');
		$("#e_coupon_num_yj").html('0');
		$("#e_coupon_pic_yj").html('0');
		$("#e_guanzhu").html('0');
		
		var userid=$(this).attr('orgid');
		$("#employee-yj-id").val(userid);
		$("#dialog-emplyee-yj").dialog({height:250,width:960,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
	});
	
	//按日期查询员工业绩
	$(".selectemplyee-yj").live('click',function(){
		var times=$("#e_time").val();
		var employeeid=$("#employee-yj-id").val();
		if(times==""){
			alert('请输入检索日期');
			return false;
		}else{
			var dataObj={
					data_time:times,
					userid:employeeid
			};
			$.ajax({ 
				 type: "post",
				 url: api.select_admin_user_yj,
				 data:dataObj,
				 dataType:"json",
				 success: function(data){
					 var result=eval(data);
					 if(result.status=='1000'){
						 $("#e_weight_yj").html(result.data.sumweight);
						 $("#e_pic_yj").html(result.data.sumpic);
						 $("#e_coupon_pic_yj").html(result.data.sumvoucherpic);
						 $("#e_coupon_num_yj").html(result.data.sumvouchernum);
						 $("#e_order_y").html(result.data.sumyorder);
						 $("#e_order_d").html(result.data.sumdorder);
						 $("#e_guanzhu").html(result.data.sumdguanzhu);						 
					 }
				 }
			});
		}
	});
});


function searchuser(url){
	var dataObj={
			xingming:$("#s_xingming").val(),
			address:$("#s_address").val(),
			pay_type:$("#s_pay").val(),
			status:$("#s_status").val()
	};	
	
	$.ajax({
		 type:"post",
		 url: url,
		 data:dataObj,
		 dataType:"json",
		 success: function(data){
			 var result=eval(data);
			 
			 if(result.status == '1050'){
				 $("#content_list").html(result.info);
				 $("#content_page").html('');
			 }
			 if(result.status == '3000'){
				 $("#content_list").html(result.info);
				 $("#content_page").html('');
			 }
			 if(result.status == '1000'){
				 var row=result['data'].length;
				 
				 var content='';				 
				 for(var i=0;i<row;i++){
					 content+='<tr><td><a href="javascript:void(0);" orgid="'+result['data'][i]['id']+'" title="查看员工业绩" class="employee-yj">'+result['data'][i]['xingming']+'</a></td><td>'+result['data'][i]['power_name']+'</td><td>'+result['data'][i]['maile']+'</td><td>'+result['data'][i]['mobile']+'</td><td>'+result['data'][i]['address']+'</td>';
					 if(result['data'][i]['pay_type']=="1"){
						content+='<td><font color="red">红包</td>';		 
					 }else{
						content+='<td><font color="blue">现金</td>';
					 }
					 content+='<td><a href="/'+result['data'][i]['weixin_code']+'" target="_blank">查看</a></td><td>'+result['data'][i]['name']+'</td>';
					 if(result['data'][i]['status']=="1"){
						content+='<td>有效</td>';		 
					 }else{
						content+='<td><font color="red">无效</font></td>';
					 }
					 content+='<td><a href="javascript:void(0);" class="emplyee-edit" orgid="'+result['data'][i]['id']+'">修改</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="emplyee-delete" orgid="'+result['data'][i]['id']+'">禁用</a></td></tr>';	 
				 }
				 $("#content_list").html(content);
				 $("#content_page").html('');
				 $("#content_ajax_page").html(result.page);
			 }
		 }
	})
}

