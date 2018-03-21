var api={
	'addtasktype':'/index.php/maijinadmin/tasktype/add_editor_type',
	'selectype':'/index.php/maijinadmin/tasktype/selectype',
	'delectype':'/index.php/maijinadmin/tasktype/delectype'
};
var editor;
$(document).ready(function(){	
	//弹出添加使用说明对话框
	$(".btn-instruction").click(function(){	
		$('#dialog-instruction').dialog({
			title : '添加任务类型信息',
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
	$(".addtasktype").live('click',function(){
		var dataObj={
				id:$("#tasktypeid").val(),
				type:$("#types").val(),
				taskid:$("#taskid").val(),
				i_content:htmlHexEncode(editor.html()),
				i_status:$("#i_status").val()
		};
		$.ajax({
			 type: "post",
			 url: api.addtasktype,
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
	$(".tasktype-edit").live("click",function(){
		var id=$(this).attr('orgid');
		var dataObj={
				id:id	
		};
		$.ajax({ 
			 type: "post",
			 url: api.selectype,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);				 
				 if(result.status==0){					
					$('#dialog-instruction').dialog({
						title : '修改任务类型信息',
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
					// console.log(result.info.this_type[0].id);
					$("#dialog-instruction #tasktypeid").val(result.info.this_type[0].id);
					$("#dialog-instruction #types").val(result.info.this_type[0].num);
					editor.html(htmlDecode(result.info.this_type[0].text));
					$("#dialog-instruction #taskid").val(result.info.this_type[0].tid);
				 }else{					 
					 alert(result.info);
				 }
			 }
		});
	});
	
	//禁用使用说明，设置为无效
	$(".tasktype-delete").live('click',function(){
		var id=$(this).attr('orgid');
		var dataObj={
				id:id	
		};
		$.ajax({ 
			 type: "post",
			 url: api.delectype,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 alert(result.info);
				 if(result.status==1000){
					location.reload();
				 }
			 }
		});
	});
});