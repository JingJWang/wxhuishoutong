var api={
	'get_instruction_list':'/index.php/maijinadmin/instruction/get_instruction_list',
	'delete_instruction':'/index.php/maijinadmin/instruction/delete_instruction',
	'get_instruction':'/index.php/maijinadmin/instruction/get_instruction',
	'add_edit_instruction':'/index.php/maijinadmin/instruction/add_edit_instruction'
};
var editor;
$(document).ready(function(){	
	//弹出添加使用说明对话框
	$(".btn-instruction").click(function(){	
		$('#dialog-instruction').dialog({
			title : '添加使用说明',
			height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5},
			open : function(event, ui) {
				// 打开Dialog后创建编辑器
				editor =KindEditor.create('#i_content', {
					resizeType : 1
				});
			},
			beforeClose : function(event, ui) {
				// 关闭Dialog前移除编辑器
				editor.remove();
			}
		});		
		//清空内容
		$("#dialog-instruction input").each(function(){
			 $(this).val("");						 
		});
		$("#dialog-instruction select").each(function(){
			 $(this).find('option').eq(0).attr('selected','selected');		 
		});
		editor.html('');	
	});
	
	//添加使用说明
	$(".addinstruction").live('click',function(){
		var dataObj={
				id:$("#instructionid").val(),
				i_name:$("#i_name").val(),
				i_content:htmlHexEncode(editor.html()),
				i_status:$("#i_status").val()
		};
		$.ajax({
			 type: "post",
			 url: api.add_edit_instruction,
			 data:dataObj,
			 dataType:"json",			 
			 success: function(data){
				 var result=eval(data);
				 alert(result.info);
				 if(result.status==0){
					 //清空内容
					 $("#dialog-instruction input").each(function(){
						 $(this).val("");						 
					 });
					 $("#dialog-instruction select").each(function(){
						 $(this).find('option').eq(0).attr('selected','selected');		 
					 });
					 editor.remove();
					 //关闭dialog
					 $("#dialog-instruction").dialog('close');
					 location.reload();
				 }
			 }
		});
	});
	var htmlHexEncode=function(str) {//HTML hex encode.
    	var res=[];
    	for(var i=0;i < str.length;i++)
        	res[i]=str.charCodeAt(i).toString(16);
    	return "&#"+String.fromCharCode(0x78)+res.join(";&#"+String.fromCharCode(0x78))+";";//x ，防止ff下&#x 转义
	};
	var htmlDecode = function(str) {
    	return str.replace(/&#(x)?([^&]{1,5});?/g,function($,$1,$2) {
    	    return String.fromCharCode(parseInt($2 , $1 ? 16:10));
    	});
	};
	
	//修改使用说明信息
	$(".instruction-edit").live("click",function(){
		var instructionid=$(this).attr('orgid');
		var dataObj={
				id:instructionid	
		};
		$.ajax({ 
			 type: "post",
			 url: api.get_instruction,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);				 
				 if(result.status==0){					
					$('#dialog-instruction').dialog({
						title : '修改使用说明',
						height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5},
						open : function(event, ui) {
							// 打开Dialog后创建编辑器
							editor =KindEditor.create('#i_content', {
								resizeType : 1,
								uploadJson:'/maijinadmin/kindeditor/upload/upload_json.php',
								filterMode: false,//是否开启过滤模式
								
							});
						},
						beforeClose : function(event, ui) {
							// 关闭Dialog前移除编辑器
							editor.remove();
						}
					});
					$("#dialog-instruction #i_name").val(result.data.instruction_name);
					editor.html(htmlDecode(result.data.instruction_content));
					$("#dialog-instruction #i_status").val(result.data.status);
					$("#dialog-instruction #instructionid").val(result.data.id);
				 }else{					 
					 alert(result.info);
				 }
			 }
		});
	});
	
	//禁用使用说明，设置为无效
	$(".instruction-delete").live('click',function(){
		var instructionid=$(this).attr('orgid');
		var dataObj={
				id:instructionid	
		};
		$.ajax({ 
			 type: "post",
			 url: api.delete_instruction,
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
	//预览文章内容
	$(".viewcontent").live('click',function(){
		var instructionid=$(this).attr('orgid');
		var dataObj={
				id:instructionid	
		};
		$.ajax({ 
			 type: "post",
			 url: api.get_instruction,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);				 
				 if(result.status==0){
					$("#dialog-instruction-condiv").dialog({height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
					$("#dialog-instruction-content").html(result.data.instruction_content);
				 }else{					 
					 alert(result.info);
				 }
			 }
		});			
	});
	//查询所有使用说明
	instructionlist();
});


function instructionlist(){
		var dataObj={};
		$.ajax({ 
			 type: "post",
			 url: api.get_instruction_list,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var content='';
					 for(var i=0;i<row;i++){
						 content+='<tr><td>'+result['data'][i]['instruction_name']+'</td><td><a href="javascript:void(0);" class="viewcontent" orgid="'+result['data'][i]['id']+'">预览</td><td>'+result['data'][i]['instruction_joindate']+'</td><td>'+result['data'][i]['instruction_lastdate']+'</td>';
						 if(result['data'][i]['status']=="1"){
							content+='<td>有效</td>';		 
						 }else{
							content+='<td><font color="red">无效</font></td>';
						 }
						 content+='<td><a href="javascript:void(0);" class="instruction-edit" orgid="'+result['data'][i]['id']+'">修改</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="instruction-delete" orgid="'+result['data'][i]['id']+'">禁用</a></td></tr>';
					 }
					 $('#instruction_list').html(content);
				 }else{
					 alert(result.info);
				 }
			 }
	});
}
