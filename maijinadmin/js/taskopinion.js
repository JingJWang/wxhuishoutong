var api={
	'examine_opinion':'/index.php/maijinadmin/taskopinion/examine_opinion',
	// 'delectreward':'/index.php/maijinadmin/taskreward/delectreward',
	'adoption_opinion':'/index.php/maijinadmin/taskopinion/adoption_opinion'
};
$(document).ready(function(){
	$('#content_ajax_page a').live('click',function(){
		var url=$(this).attr('href');
		searchuser(url);
		return false;//阻止链接跳转
	});
	//时间dialog
	$(".datepicker").datepicker();
	
	// 弹出添加任务对话框
	// $(".btn-addEmplyee").click(function(){

	// 	$("#dialog-emplyee input").each(function(){
	// 		$(this).val("");						 
	// 	});
	// 	$('#task_limit_other').html('');
	// 	$("#dialog-emplyee select").each(function(){
	// 		$(this).find('option').eq(0).attr('selected','selected');		 
	// 	});

	// 	$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
		
	// });
	
	//添加任务奖励
	$(".adoption").click(function(){		
		// var power_name_val=$("#u_power_type option:selected").html();
		if (!confirm('你确定要采纳吗?')) {
			return 0;
		};
		var dataObj={
				num : $("#num").val(),
				// opinion_name:$("#opinion_name").text(),
				user_mobile:$("#user_mobile").text(),
				rewards:$("#rewards").val()
		};
		// alert(dataObj.rewards);
		$.ajax({ 
			 type: "post",
			 url: api.adoption_opinion,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				var result=eval(data);
				alert(result.info);
				if(result.status == '7'){
					location.reload();
				}
			 }
		});
	});
	
	//删除奖励
	// $(".task-delete").live("click",function(){
	// 	var rnum=$(this).attr('orgid');
	// 	var dataObj={
	// 		id : rnum
	// 	}
	// 	$.ajax({
	// 		type: "post",
	// 		url: api.delectreward,
	// 		data:dataObj,
	// 		dataType:"json",
	// 		success:function(data){
	// 			var result=eval(data);
	// 			alert(result.info);
	// 			if(result.status == '1000'){
	// 				location.reload();
	// 			}
	// 		}
	// 	});
	// });

	//查看评论
	$(".task-edit").live("click",function(){
		var rnum=$(this).attr('orgid');
		var dataObj={
			id : rnum
		}
		$.ajax({
			url: api.examine_opinion,
			type: 'post',
			dataType: 'json',
			data: dataObj,
			success:function(data){
				 var result=eval(data);
				 // alert(data);
				 console.log(result.info.opinion);
				 if(result.status=='1'){

					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});

					$("#dialog-emplyee #opinion_name").html(result['info']['opinion']['wx_name']);
					$("#dialog-emplyee #user_mobile").html(result['info']['opinion']['wx_mobile']);
					$("#dialog-emplyee #content").html(result['info']['opinion']['opinion_content']);
					$("#dialog-emplyee #user_time").html(result['info']['opinion']['opinion_join_time']);
					$("#dialog-emplyee #rewards").val(result['info']['opinion']['reward_id']);
					$("#dialog-emplyee #num").val(result['info']['opinion']['opinion_id']);

					$("#dialog-emplyee").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
				 }else{
				 	alert(result.info);
				 }
			}
		});
		
	});
	
});

