var api={
	'add_editor_reward':'/index.php/maijinadmin/taskreward/add_editor_reward',
	'delectreward':'/index.php/maijinadmin/taskreward/delectreward',
	'uptaskreward':'/index.php/maijinadmin/taskreward/uptaskreward'
};
$(document).ready(function(){
	$('#content_ajax_page a').live('click',function(){
		var url=$(this).attr('href');
		searchuser(url);
		return false;//阻止链接跳转
	});
	//时间dialog
	$(".datepicker").datepicker();
	
	//弹出添加任务对话框
	$(".btn-addEmplyee").click(function(){

		$("#dialog-emplyee input").each(function(){
			$(this).val("");						 
		});
		$('#task_limit_other').html('');
		$("#dialog-emplyee select").each(function(){
			$(this).find('option').eq(0).attr('selected','selected');		 
		});

		$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
		
	});
	
	//添加任务奖励
	$(".addemplyee").click(function(){		
		var power_name_val=$("#u_power_type option:selected").html();
		var dataObj={
				num : $("#num").val(),
				rbonus:$("#rbonus").val(),
				rintegral:$("#rintegral").val(),
				all_rintegral:$("#all_rintegral").val(),
				rfund:$("#rfund").val(),
				rstatus:$("#rstatus").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.add_editor_reward,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				var result=eval(data);
				alert(result.info);
				if(result.status == '5' || result.status == '7'){
					location.reload();
				}
			 }
		});
	});
	
	//删除奖励
	$(".task-delete").live("click",function(){
		var rnum=$(this).attr('orgid');
		var dataObj={
			id : rnum
		}
		$.ajax({
			type: "post",
			url: api.delectreward,
			data:dataObj,
			dataType:"json",
			success:function(data){
				var result=eval(data);
				alert(result.info);
				if(result.status == '1000'){
					location.reload();
				}
			}
		});
	});

	//修改奖励
	$(".task-edit").live("click",function(){
		var rnum=$(this).attr('orgid');
		var dataObj={
			id : rnum
		}
		$.ajax({
			url: api.uptaskreward,
			type: 'post',
			dataType: 'json',
			data: dataObj,
			success:function(data){
				 var result=eval(data);
				 if(result.status=='2'){

					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});

					$("#dialog-emplyee #num").val(result['info'][0]['reward_id']);
					$("#dialog-emplyee #rbonus").val(result['info'][0]['reward_bonus']);
					$("#dialog-emplyee #rintegral").val(result['info'][0]['reward_integral']);
					$("#dialog-emplyee #all_rintegral").val(result['info'][0]['reward_all_integral']);
					$("#dialog-emplyee #rfund").val(result['info'][0]['reward_fund']);
					$("#dialog-emplyee #rstatus").val(result['info'][0]['reward_status']);

					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
				 }
			}
		});
		
	});
	
});

