var api={
		'select_weixin_user':'/index.php/maijinadmin/wxuser/select_weixin_user',
		'select_voucher_log':'/index.php/maijinadmin/voucherlog/select_voucher_log',
		'select_order_list':'/index.php/maijinadmin/order/select_order_list'
};
$(document).ready(function(){	
	//时间dialog
	$( ".datepicker" ).datepicker();	
	
	//查询所有粉丝
	winxin_userlist();
	
	//统计点击时查询
	$(".wxuser-count").click(function(){
		var attrid=$(this).attr('id');
		if($(this).html()=='0'){
			return false;
		}
		var dataObj={
				timetype:attrid
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_weixin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var pagenum=result.pagenum;//共条数
					 var pagetotal=result.pagetotal;//总页数
					 $("#pagetotal").html(pagetotal);
					 $("#pagenum").html(pagenum);
					 $('#timetype').val(attrid);
					 
					 if(row==0&&pagetotal==0){
						$("#pagenow").html("0");
						$('.fengye[orgid="first"]').addClass('disabled');
						$('.fengye[orgid="next"]').addClass('disabled');
						$('.fengye[orgid="last"]').addClass('disabled');
					 }else if(row!=0&&pagetotal==1){
						$("#pagenow").html("1");
						$('.fengye[orgid="next"]').addClass('disabled');
						$('.fengye[orgid="first"]').addClass('disabled');
						$('.fengye[orgid="last"]').addClass ('disabled');
					 }else{
						$("#pagenow").html("1");
						$('.fengye[orgid="first"]').removeClass('disabled');
						$('.fengye[orgid="next"]').removeClass('disabled');
						$('.fengye[orgid="last"]').removeClass('disabled');
					 }
					 $('.fengye[orgid="prev"]').addClass('disabled');
					 
					 
					 var content='';
					 for(var i=0;i<row;i++){
						 content+='<tr><td><a href="'+result['data'][i]['wx_img']+'" target="_blank" title="点击查看大图"><img src="'+result['data'][i]['wx_img']+'" width="35" height="35" /></a></td><td>'+result['data'][i]['wx_name']+'</td><td>'+result['data'][i]['wx_jointime']+'</td>';
						 if(result['data'][i]['wx_updatetime']=='0000-00-00 00:00:00'){
							 content+='<td>'+result['data'][i]['wx_jointime']+'</td>';
						 }else{
							 content+='<td>'+result['data'][i]['wx_updatetime']+'</td>';
						 }
						 content+='<td>'+result['data'][i]['adminxingming']+'</td><td>'+result['data'][i]['address']+'</td><td><a href="javascript:void(0);" class="orderinfo" openid="'+result['data'][i]['wx_openid']+'">查看</a></td><td><a href="javascript:void(0);" class="couponinfo" openid="'+result['data'][i]['wx_openid']+'">查看</a></td>';
					 }
					 $('#weixinuser_list').html(content);
				}else{
					 alert(result.info);
				}
			 }
		});		
	});
	
	//分页
	$(".fengye").click(function(){
		if($(this).hasClass('disabled')){
			return false;			
		}
		var orgid=$(this).attr('orgid');
		var pagetotal=$("#pagetotal").html();
		var pagenow=parseInt($("#pagenow").html());
		var wx_timetype=$('#timetype').val();
		if(orgid=="first"){
			pagenow=1;			
		}
		if(orgid=="prev"){
			pagenow=pagenow-1;			
		}
		if(orgid=="next"){			
			pagenow=pagenow+1;			
		}
		if(orgid=="last"){
			pagenow=pagetotal;		
		}
		var dataObj={
				timetype:wx_timetype,
				page:pagenow
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_weixin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 $("#pagenow").html(pagenow);//当前页			 
					 if(pagenow==pagetotal){
						//下一页不能点击
						$('.fengye[orgid="next"]').addClass('disabled');
					 }else{
						$('.fengye[orgid="next"]').removeClass('disabled'); 
					 }
					 if(pagenow==1){					 
						//上一页不能点击
						$('.fengye[orgid="prev"]').addClass('disabled');
					 }else{
						$('.fengye[orgid="prev"]').removeClass('disabled');				 
					 }
					 var content='';				 
					 for(var i=0;i<row;i++){
						  content+='<tr><td><a href="'+result['data'][i]['wx_img']+'" target="_blank" title="点击查看原图"><img src="'+result['data'][i]['wx_img']+'" width="35" height="35" /></a></td><td>'+result['data'][i]['wx_name']+'</td><td>'+result['data'][i]['wx_jointime']+'</td>';
						 if(result['data'][i]['wx_updatetime']=='0000-00-00 00:00:00'){
							 content+='<td>'+result['data'][i]['wx_jointime']+'</td>';
						 }else{
							 content+='<td>'+result['data'][i]['wx_updatetime']+'</td>';
						 }
						 content+='<td>'+result['data'][i]['adminxingming']+'</td><td>'+result['data'][i]['address']+'</td><td><a href="javascript:void(0);" class="orderinfo" openid="'+result['data'][i]['wx_openid']+'">查看</a></td><td><a href="javascript:void(0);" class="couponinfo" openid="'+result['data'][i]['wx_openid']+'">查看</a></td>';
					 }
					 $('#weixinuser_list').html(content);
				}else{
					 alert(result.info);
				}
			 }
		});	
	});
	
	//查询报单用户的所有订单
	$('.orderinfo').live('click',function(){
		//清空内容
		$('#user_order_list').html('');
		$('.u_o_ul span').html('');
		$("#dialog-user-order input").each(function(){
			 $(this).val("");						 
		});
		$("#dialog-user-order select").each(function(){
			 $(this).find('option').eq(0).attr('selected','selected');		 
		});
		var openid=$(this).attr('openid');
		$('#order-user-openid').val(openid);
		$("#dialog-user-order").dialog({height:500,width:1000,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
	});
	//查询报单用户的所有代金券
	$('.couponinfo').live('click',function(){
		//清空内容
		$('#user_coupon_list').html('');
		$('.u_c_ul span').html('');		
		$("#dialog-user-coupon input").each(function(){
			 $(this).val("");						 
		});
		$("#dialog-user-coupon select").each(function(){
			 $(this).find('option').eq(0).attr('selected','selected');		 
		});
		var openid=$(this).attr('openid');
		$('#coupon-user-openid').val(openid);
		$("#dialog-user-coupon").dialog({height:500,width:1000,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});		
	});
	
	//检索订单
	$(".btn-user-order").live('click',function(){
		var dataObj={
				openid:$("#order-user-openid").val(),
				user_name:$("#u_o_user_id").val(),
				order_joindate:$("#u_o_time").val(),
				order_status:$("#u_o_status").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_order_list,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var pagenum=result.pagenum;//总页数
					 var pagetotal=result.pagetotal;//总条数
					 $("#u_o_pagetotal").html(pagetotal);
					 $("#u_o_pagenum").html(pagenum);
					 
					 
					 if(row==0&&pagetotal==0){
						$("#u_o_pagenow").html("0");
						$('.u_o_fengye[orgid="first"]').addClass('disabled');
						$('.u_o_fengye[orgid="next"]').addClass('disabled');
						$('.u_o_fengye[orgid="last"]').addClass('disabled');
					 }else if(row!=0&&pagetotal==1){
						$("#u_o_pagenow").html("1");
						$('.u_o_fengye[orgid="next"]').addClass('disabled');
						$('.u_o_fengye[orgid="first"]').addClass('disabled');
						$('.u_o_fengye[orgid="last"]').addClass ('disabled');
					 }else{
						$("#u_o_pagenow").html("1");
						$('.u_o_fengye[orgid="first"]').removeClass('disabled');
						$('.u_o_fengye[orgid="next"]').removeClass('disabled');
						$('.u_o_fengye[orgid="last"]').removeClass('disabled');
					 }
					 $('.u_o_fengye[orgid="prev"]').addClass('disabled');
						 
						 
						 
					 var content='';				 
					 for(var i=0;i<row;i++){
						 content+='<tr><td>'+result['data'][i]['order_mobile']+'</td><td>'+result['data'][i]['order_province']+result['data'][i]['order_city']+'</td><td>'+result['data'][i]['order_address']+'</td><td>旧衣回收</td><td>'+result['data'][i]['order_num']+'</td><td>'+result['data'][i]['order_weight']+'</td><td>'+result['data'][i]['order_pic']+'</td><td>'+result['data'][i]['order_joindate']+'</td><td>'+result['data'][i]['order_lastdate']+'</td><td>'+result['data'][i]['user_address']+'</td>';					 
						 if(result['data'][i]['order_status']==1){
							content+='<td>未成交</td>';						 
						 }else if(result['data'][i]['order_status']==0){
							content+='<td><font color="red">已成交</font></td>';						 
						 }else{
							content+='<td><font color="blue">已作废</font></td>';
						 }
						 content+='<td>'+result['data'][i]['xingming']+'</td><td>'+result['data'][i]['order_make']+'</td>';	 
					 }
					 $('#user_order_list').html(content);
				 }else{
					 alert(result.info);
				 }
			 }
		});		
	});
	
	//订单分页
	$(".u_o_fengye").click(function(){
		if($(this).hasClass('disabled')){
			return false;			
		};
		var orgid=$(this).attr('orgid');
		var pagetotal=$("#u_o_pagetotal").html();
		var pagenow=parseInt($("#u_o_pagenow").html());
		if(orgid=="first"){
			pagenow=1;			
		}
		if(orgid=="prev"){
			pagenow=pagenow-1;			
		}
		if(orgid=="next"){			
			pagenow=pagenow+1;			
		}
		if(orgid=="last"){
			pagenow=pagetotal;		
		}
		var dataObj={
				openid:$("#order-user-openid").val(),
				user_name:$("#u_o_user_id").val(),
				order_joindate:$("#u_o_time").val(),
				order_status:$("#u_o_status").val(),
				page:pagenow
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_order_list,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 var row=result.num;
				 $("#u_o_pagenow").html(pagenow);//当前页			 
				 if(pagenow==pagetotal){
					//下一页不能点击
					$('.u_o_fengye[orgid="next"]').addClass('disabled');
				 }else{
					$('.u_o_fengye[orgid="next"]').removeClass('disabled'); 
				 }
				 if(pagenow==1){					 
					//上一页不能点击
					$('.u_o_fengye[orgid="prev"]').addClass('disabled');
				 }else{
					$('.u_o_fengye[orgid="prev"]').removeClass('disabled');				 
				 }
				 var content='';				 
				 for(var i=0;i<row;i++){
					 content+='<tr><td>'+result['data'][i]['order_mobile']+'</td><td>'+result['data'][i]['order_province']+result['data'][i]['order_city']+'</td><td>'+result['data'][i]['order_address']+'</td><td>旧衣回收</td><td>'+result['data'][i]['order_num']+'</td><td>'+result['data'][i]['order_weight']+'</td><td>'+result['data'][i]['order_pic']+'</td><td>'+result['data'][i]['order_joindate']+'</td><td>'+result['data'][i]['order_lastdate']+'</td><td>'+result['data'][i]['user_address']+'</td>';					 
					 if(result['data'][i]['order_status']==1){
						content+='<td>未成交</td>';						 
					 }else if(result['data'][i]['order_status']==0){
						content+='<td><font color="red">已成交</font></td>';						 
					 }else{
						content+='<td><font color="blue">已作废</font></td>';
					 }
					 content+='<td>'+result['data'][i]['xingming']+'</td><td>'+result['data'][i]['order_make']+'</td>';	 
				 }
				 $('#user_order_list').html(content);
			 }
		});	
	});
	
	//检索代金券日志
	$(".btn-user-coupon").live('click',function(){
		var dataObj={
				openid:$("#coupon-user-openid").val(),
				log_lastdate:$("#u_c_time").val(),
				log_type:$("#u_c_type").val(),
				log_voucher_status:$("#u_c_status").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_voucher_log,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var pagenum=result.pagenum;//总条数
					 var pagetotal=result.pagetotal;//总页数
					 $("#u_c_pagetotal").html(pagetotal);
					 $("#u_c_pagenum").html(pagenum);
					
					 
					 
					 if(row==0&&pagetotal==0){
						$("#u_c_pagenow").html("0");
						$('.u_c_fengye[orgid="first"]').addClass('disabled');
						$('.u_c_fengye[orgid="next"]').addClass('disabled');
						$('.u_c_fengye[orgid="last"]').addClass('disabled');
					 }else if(row!=0&&pagetotal==1){
						$("#u_c_pagenow").html("1");
						$('.u_c_fengye[orgid="next"]').addClass('disabled');
						$('.u_c_fengye[orgid="first"]').addClass('disabled');
						$('.u_c_fengye[orgid="last"]').addClass ('disabled');
					 }else{
						$("#u_c_pagenow").html("1");
						$('.u_c_fengye[orgid="first"]').removeClass('disabled');
						$('.u_c_fengye[orgid="next"]').removeClass('disabled');
						$('.u_c_fengye[orgid="last"]').removeClass('disabled');
					 }
					 $('.u_c_fengye[orgid="prev"]').addClass('disabled');
					 

					 var content='';				 
					 for(var i=0;i<row;i++){
						 content+='<tr><td>'+result['data'][i]['voucher_miaoshu']+'</td><td>'+result['data'][i]['voucher_pic']+'</td><td>'+result['data'][i]['log_joindate']+'</td><td>'+result['data'][i]['log_lastdate']+'</td><td>'+result['data'][i]['log_exceed']+'</td>';
						 if(result['data'][i]['log_voucher_status']=="1"){
							content+='<td>未使用</td>';		 
						 }else if(result['data'][i]['log_voucher_status']=="2"){
							content+='<td><font color="red">已使用</font></td>';
						 }else{
							content+='<td><font color="blue">已过期</font></td>';
						 }						 
					 }
					 $('#user_coupon_list').html(content);
				}else{
					 alert(result.info);
				}
			 }
		});		
	});
	
	//代金券日志分页
	$(".u_c_fengye").click(function(){
		if($(this).hasClass('disabled')){
			return false;	
		};
		var orgid=$(this).attr('orgid');
		var pagetotal=$("#u_c_pagetotal").html();
		var pagenow=parseInt($("#u_c_pagenow").html());
		if(orgid=="first"){
			pagenow=1;			
		}
		if(orgid=="prev"){
			pagenow=pagenow-1;			
		}
		if(orgid=="next"){			
			pagenow=pagenow+1;			
		}
		if(orgid=="last"){
			pagenow=pagetotal;		
		}
		var dataObj={
				openid:$("#coupon-user-openid").val(),
				log_lastdate:$("#u_c_time").val(),
				log_type:$("#u_c_type").val(),
				log_voucher_status:$("#u_c_status").val(),
				page:pagenow
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_voucher_log,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 $("#u_c_pagenow").html(pagenow);//当前页			 
					 if(pagenow==pagetotal){
						//下一页不能点击
						$('.u_c_fengye[orgid="next"]').addClass('disabled');
					 }else{
						$('.u_c_fengye[orgid="next"]').removeClass('disabled'); 
					 }
					 if(pagenow==1){					 
						//上一页不能点击
						$('.u_c_fengye[orgid="prev"]').addClass('disabled');
					 }else{
						$('.u_c_fengye[orgid="prev"]').removeClass('disabled');				 
					 }
					 var content='';				 
					 for(var i=0;i<row;i++){
						content+='<tr><td>'+result['data'][i]['voucher_miaoshu']+'</td><td>'+result['data'][i]['voucher_pic']+'</td><td>'+result['data'][i]['log_joindate']+'</td><td>'+result['data'][i]['log_lastdate']+'</td><td>'+result['data'][i]['log_exceed']+'</td>';
						 if(result['data'][i]['log_voucher_status']=="1"){
							content+='<td>未使用</td>';		 
						 }else if(result['data'][i]['log_voucher_status']=="2"){
							content+='<td><font color="red">已使用</font></td>';
						 }else{
							content+='<td><font color="blue">已过期</font></td>';
						 }						
					 }
					 $('#user_coupon_list').html(content);
				}else{
					 alert(result.info);
				}
			 }
		});	
	});
	
	
});


function winxin_userlist(){
		var dataObj={};
		$.ajax({ 
			 type: "post",
			 url: api.select_weixin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var pagenum=result.pagenum;//共条数
					 var pagetotal=result.pagetotal;//总页数
					 $("#pagetotal").html(pagetotal);
					 
					 $("#total").html(pagenum);
					 $("#todaytotal").html(result.todaytotal);
					 $("#yesterdaytotal").html(result.yesterdaytotal);
					 $("#monthtotal").html(result.monthtotal);
					 
					 $("#total_wx").html(result.total_wx);
					 $("#todaytotal_wx").html(result.todaytotal_wx);
					 $("#yesterdaytotal_wx").html(result.yesterdaytotal_wx);
					 $("#monthtotal_wx").html(result.monthtotal_wx);
					 
					 
					 $("#pagenum").html(pagenum);
					 
					 
					 if(row==0&&pagetotal==0){
						$("#pagenow").html("0");
						$('.fengye[orgid="first"]').addClass('disabled');
						$('.fengye[orgid="next"]').addClass('disabled');
						$('.fengye[orgid="last"]').addClass('disabled');
					 }else if(row!=0&&pagetotal==1){
						$("#pagenow").html("1");
						$('.fengye[orgid="next"]').addClass('disabled');
						$('.fengye[orgid="first"]').addClass('disabled');
						$('.fengye[orgid="last"]').addClass ('disabled');
					 }else{
						$("#pagenow").html("1");
						$('.fengye[orgid="first"]').removeClass('disabled');
						$('.fengye[orgid="next"]').removeClass('disabled');
						$('.fengye[orgid="last"]').removeClass('disabled');
					 }
					 $('.fengye[orgid="prev"]').addClass('disabled');
					 
					 
					 var content='';
					 for(var i=0;i<row;i++){
						 content+='<tr><td><a href="'+result['data'][i]['wx_img']+'" target="_blank" title="点击查看原图"><img src="'+result['data'][i]['wx_img']+'" width="35" height="35" /></a></td><td>'+result['data'][i]['wx_name']+'</td><td>'+result['data'][i]['wx_jointime']+'</td>';
						 if(result['data'][i]['wx_updatetime']=='0000-00-00 00:00:00'){
							 content+='<td>'+result['data'][i]['wx_jointime']+'</td>';
						 }else{
							 content+='<td>'+result['data'][i]['wx_updatetime']+'</td>';
						 }
						 content+='<td>'+result['data'][i]['adminxingming']+'</td><td>'+result['data'][i]['address']+'</td><td><a href="javascript:void(0);" class="orderinfo" openid="'+result['data'][i]['wx_openid']+'">查看</a></td><td><a href="javascript:void(0);" class="couponinfo" openid="'+result['data'][i]['wx_openid']+'">查看</a></td>';
					 }
					 $('#weixinuser_list').html(content);
				}else{
					alert(result.info);
				}
			 }
	});
}
