var api={
	'add_editor_level':'/index.php/maijinadmin/tasklevel/add_editor_level',
	'delectlevel':'/index.php/maijinadmin/tasklevel/delectlevel',
	'uptasklevel':'/index.php/maijinadmin/tasklevel/uptasklevel'
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
		$("#img1").attr("src", '/static/task/images/addimg.png') ;//图片恢复
		$('#task_limit_other').html('');
		$("#dialog-emplyee select").each(function(){
			$(this).find('option').eq(0).attr('selected','selected');		 
		});

		$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
		
	});

	
	//添加等级
	$(".addemplyee").click(function(){		
		var power_name_val=$("#u_power_type option:selected").html();
		var dataObj={
				num	: $("#num").val(),
				lname : $("#lname").val(),
				levelnum:$("#levelnum").val(),
				limg:$("#limg").val(),
				lfund:$("#lfund").val(),
				lstatus:$("#lstatus").val()
		};
		$.ajax({ 
			 type: "post",
			 url: api.add_editor_level,
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
		var lnum=$(this).attr('orgid');
		var dataObj={
			id : lnum
		}
		$.ajax({
			type: "post",
			url: api.delectlevel,
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
		var lnum=$(this).attr('orgid');
		var dataObj={
			id : lnum
		}
		$.ajax({
			url: api.uptasklevel,
			type: 'post',
			dataType: 'json',
			data: dataObj,
			success:function(data){

				 var result=eval(data);
				 if(result.status=='2'){

					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});

					$("#dialog-emplyee #num").val(result['info'][0]['level_id']);
					$("#dialog-emplyee #lname").val(result['info'][0]['level_name']);
					$("#dialog-emplyee #levelnum").val(result['info'][0]['level_num']);
					$("#dialog-emplyee #limg").val(result['info'][0]['level_img']);
					$("#img1").attr('src',result['info'][0]['level_img']);
					$("#dialog-emplyee #lfund").val(result['info'][0]['level_integral']);
					$("#dialog-emplyee #lstatus").val(result['info'][0]['level_status']);

					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
				 }
			}
		});
		
	});


	
});

