var api={
	'get_branch_list':'/index.php/maijinadmin/branch/get_branch_list',
	'add_edit_branch':'/index.php/maijinadmin/branch/add_edit_branch',
	'delete_branch':'/index.php/maijinadmin/branch/delete_branch',
	'get_branch':'/index.php/maijinadmin/branch/get_branch'
};
$(document).ready(function(){
	//时间dialog
	$(".datepicker").datepicker();
	
	//弹出添加营业网点对话框
	$(".btn-addbranch").click(function(){
		 //清空内容
		 $("#dialog-branch input").each(function(){
			 $(this).val("");						 
		 });
		 $("#dialog-branch select").each(function(){
			 $(this).find('option').eq(0).attr('selected','selected');		 
		 });
		$("#dialog-branch").dialog({height:480,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});	
	});
	
	//添加营业网点
	$(".addbranch").click(function(){
		if(/^[0-9]+(\.[0-9]+)?$/g.test($("#b_sort").val())){
			var dataObj={
					id:$("#branchid").val(),
					b_time:$("#b_time").val(),
					b_address:$("#b_address").val(),
					b_sort:$("#b_sort").val(),
					b_status:$("#b_status").val()
			};
			$.ajax({
				 type: "post",
				 url: api.add_edit_branch,
				 data:dataObj,
				 dataType:"json",
				 success: function(data){
					 var result=eval(data);
					 alert(result.info);
					 if(result.status==0){
						 //清空内容
						 $("#dialog-branch input").each(function(){
							 $(this).val("");						 
						 });
						 $("#dialog-branch select").each(function(){
							 $(this).find('option').eq(0).attr('selected','selected');		 
						 });
						 //关闭dialog
						 $("#dialog-branch").dialog('close');
						 location.reload();
					 }
				 }
			});			
		}else{
			alert('排序值不是数字类型');
			return false;
		}
	});
	
	//修改营业网点
	$(".branch-edit").live("click",function(){
		var branchid=$(this).attr('orgid');	
		var dataObj={
				id:branchid
		};
		$.ajax({ 
			 type: "post",
			 url: api.get_branch,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);				 
				 if(result.status==0){
					$("#dialog-branch").dialog({height:500,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
					$("#dialog-branch #b_time").val((result.data.branch_date.replace(/ 00:00:00/g,'')));
					$("#dialog-branch #b_address").val(result.data.branch_address);
					$("#dialog-branch #b_sort").val(result.data.branch_sort);
					$("#dialog-branch #b_status").val(result.data.status);
					$("#dialog-branch #branchid").val(result.data.id);
				 }else{					 
					 alert(result.info);
				 }
			 }
		});
	});
	
	//禁用营业网点，设置为无效
	$(".branch-delete").live('click',function(){
		var branchid=$(this).attr('orgid');
		var dataObj={
				id:branchid
		};
		$.ajax({ 
			 type: "post",
			 url: api.delete_branch,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 alert(result.info);
				 if(result.status==0){
					location.reload();
				 }
			 }
		});
	});
	
	//查询所有营业网点
	branchlist();
});


function branchlist(){
		var dataObj={};
		$.ajax({ 
			 type: "post",
			 url: api.get_branch_list,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var content='';
					 for(var i=0;i<row;i++){
						 content+='<tr><td>'+result['data'][i]['branch_date'].replace(/ 00:00:00/g,'')+'</td><td>'+result['data'][i]['branch_address']+'</td><td>'+result['data'][i]['branch_joindate']+'</td><td>'+result['data'][i]['branch_lastdate']+'</td><td>'+result['data'][i]['branch_sort']+'</td>';
						 if(result['data'][i]['status']=="1"){
							content+='<td>有效</td>';		 
						 }else{
							content+='<td><font color="red">无效</font></td>';
						 }
						 content+='<td><a href="javascript:void(0);" class="branch-edit" orgid="'+result['data'][i]['id']+'">修改</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="branch-delete" orgid="'+result['data'][i]['id']+'">禁用</a></td></tr>';
					 }
					 $('#branch_list').html(content);
				 }else{
						alert(result.info);
				 }
			 }
	});
}
