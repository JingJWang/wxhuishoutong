var api={
	'add_editor_task':'/index.php/maijinadmin/task/add_editor_task',
	'gettask':'/index.php/maijinadmin/task/gettask',
	'delecttask':'/index.php/maijinadmin/task/delecttask',
	'uptask':'/index.php/maijinadmin/task/selecttask'
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

		$.ajax({
			url: api.gettask,
			type: 'post',
			dataType: 'json',
			data: '',
			success: function(data){
				var result=eval(data);
				if (result.status=='4') {
					//清空内容
					 $("#dialog-emplyee input").each(function(){
						 $(this).val("");						 
					 });
					 $('#task_limit_other').html('');
					 $("#dialog-emplyee select").each(function(){
						 $(this).find('option').eq(0).attr('selected','selected');		 
					 });

					 var option='';
					 var option='<option value="-1">请选择任务，不选择表示不被限制</option>';
					 var row=result['data'].length;
					 for(var i=0;i<row;i++){
						 option+='<option value="'+result['data'][i]['task_id']+'">'+'任务等级：'+result['data'][i]['task_level']+' 任务名称：'+result['data'][i]['info_name']+'</option>'
					 }
					 option += '<option value="-2">限制其它全部任务</option>';
					 $('#task_limit_other').html(option);
					 $("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
				}else{
					alert(result.info);
				};
			}
		});
		
		
	});
	
	//添加任务
	$(".addemplyee").click(function(){		
		var power_name_val=$("#u_power_type option:selected").html();
		var dataObj={
				task_id:$("#task_id").val(),
				info_name:$("#info_name").val(),
				reward_content:$("#reward_content").val(),
				task_content:$("#task_content").val(),
				task_type:$("#task_type").val(),
				task_level:$("#task_level").val(),
				reward_id1:$("#reward_id1").val(),
				reward_id2:$("#reward_id2").val(),
				reward_id3:$("#reward_id3").val(),
				reward_id4:$("#reward_id4").val(),
				task_sign:$("#task_sign").val(),
				task_turnover:$("#task_turnover").val(),
				task_invite_u:$("#task_invite_u").val(),
				task_invite_m:$("#task_invite_m").val(),
				task_share:$("#task_share").val(),
				task_url:$("#task_url").val(),
				task_share_url:$("#task_share_url").val(),
				reward_num:$("#reward_num").val(),
				task_limit_time:$("#task_limit_time").val(),
				task_limit_other:$("#task_limit_other").val(),
				task_status:$("#task_status").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.add_editor_task,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 alert(result.info);
				 if(result.status=='4' || result.status=='7'){
					 
					 location.reload();
				 }
			 }
		});
	});
	
	//删除任务
	$(".task-delete").live("click",function(){
		var taskid=$(this).attr('orgid');
		var dataObj={
			id : taskid
		}
		$.ajax({
			type: "post",
			url: api.delecttask,
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

	//修改任务
	$(".task-edit").live("click",function(){
		var taskid=$(this).attr('orgid');
		var dataObj={
			id : taskid
		}
		$.ajax({
			url: api.uptask,
			type: 'post',
			dataType: 'json',
			data: dataObj,
			success:function(data){
				 var result=eval(data);
				 if(result.status=='2'){
					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});

					$("#dialog-emplyee #task_id").val(result['info']['this_task'][0]['task_id']);
					$("#dialog-emplyee #info_name").val(result['info']['this_task'][0]['info_name']);
					$("#dialog-emplyee #reward_content").val(result['info']['this_task'][0]['reward_content']);
					$("#dialog-emplyee #reward_num").val(result['info']['this_task'][0]['reward_num']);
					$("#dialog-emplyee #task_content").val(result['info']['this_task'][0]['task_content']);
					$("#dialog-emplyee #task_type").val(result['info']['this_task'][0]['task_type']);
					$("#dialog-emplyee #task_level").val(result['info']['this_task'][0]['task_level']);
					$("#dialog-emplyee #task_sign").val(result['info']['this_task'][0]['task_sign']);
					$("#dialog-emplyee #reward_id1").val(result['info']['this_task'][0]['reward_id1']);
					$("#dialog-emplyee #reward_id2").val(result['info']['this_task'][0]['reward_id2']);
					$("#dialog-emplyee #reward_id3").val(result['info']['this_task'][0]['reward_id3']);
					$("#dialog-emplyee #reward_id4").val(result['info']['this_task'][0]['reward_id4']);
					$("#dialog-emplyee #task_turnover").val(result['info']['this_task'][0]['task_turnover']);
					$("#dialog-emplyee #task_invite_u").val(result['info']['this_task'][0]['task_invite_u']);
					$("#dialog-emplyee #task_invite_m").val(result['info']['this_task'][0]['task_invite_m']);
					$("#dialog-emplyee #task_share").val(result['info']['this_task'][0]['task_share']);
					$("#dialog-emplyee #task_url").val(result['info']['this_task'][0]['task_url']);
					$("#dialog-emplyee #task_share_url").val(result['info']['this_task'][0]['task_share_url']);
					$("#dialog-emplyee #task_limit_time").val(result['info']['this_task'][0]['task_limit_time']);
					$("#dialog-emplyee #task_status").val(result['info']['this_task'][0]['task_status']);
					// alert(result['info']['all_task'][0]['task_id']);


					var option='';
					var option='<option value="-1">请选择任务，不选择表示不被限制</option>';
					var row=result['info']['all_task'].length;
					for(var i=0;i<row;i++){
						option+='<option value="'+result['info']['all_task'][i]['task_id']+'">'+'任务等级：'+result['info']['all_task'][i]['task_level']+' 任务名称：'+result['info']['all_task'][i]['info_name']+'</option>'
					}
					option += '<option value="-2">限制其它全部任务</option>';
					$('#task_limit_other').html(option);

					$("#dialog-emplyee #task_limit_other").val(result['info']['this_task'][0]['task_limit_other']);
					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
				 }
				
			}
		});
		
	});
	
});

