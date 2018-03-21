/**
 * @description 获取添加表单
 */
function viewaddgroup(){
	//获取所有主管 总监  组长
	 $.ajax({
		 type:"get",
		 url:'/index.php/maijinadmin/group/getgroupleader',
		 data:'',
		 dataType:"json",
		 success: function(data){	
			 var datainfo=eval(data);
			 var leader=datainfo.leader;
			 var executives=datainfo.executives;
			 var majordomo=datainfo.majordomo; 
			 var leadercontent = '<option value="0">请选择组长</option>';
			 var executivescontent = ' <option value="0">请选择主管</option>';
			 var majordomocontent = '<option value="0">请选择总监</option>';
			 for(var i=0 ; i < leader.length;i++){
				 leadercontent += "<option value="+leader[i]["id"]+">"+leader[i]["xingming"]+"</option>";
				 
			 }
			 for(var i=0 ; i < executives.length;i++){
				 executivescontent += "<option value="+executives[i]["id"]+">"+executives[i]["xingming"]+"</option>";
			 }
			 for(var i=0 ; i < majordomo.length;i++){
				 majordomocontent += "<option value="+majordomo[i]["id"]+">"+majordomo[i]["xingming"]+"</option>";
			 }			
			 $("#group_leader").html(leadercontent);
		     $("#group_executives").html(executivescontent);
		     $("#group_majordomo").html(majordomocontent);
		     
		 },
	});	
	 $("#dialog-addgroup").dialog({height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
}
/**
 * @description 保存小组
 */
function  group_groupadd(){
	alert('a');
	var group_name    =$("#group_name").val();
	var group_leader  =$("#group_leader").val();
	if(group_name ==''){
		$("#error-info").html("小组名称不可为空");
		return false;
	}	
	if(group_leader =='' || group_leader == '0'){
		$("#error-info").html("小组组长不可为空");
		return false;
	}
	$.ajax({
		 type:"post",
		 url:'/index.php/maijinadmin/group/addgroup',
		 data:$("#groupdata").serialize(),
		 dataType:"json",
		 beforesend:function(){
			 $("#button-add-group").attr({disabled: "disabled"});
		 },
		 success: function(data){	
			var datainfo=eval(data);
			$("#button-add-group").hide();
			$("#error-info").html(datainfo.info);
			location.reload();
		 },
	});	
	
		
}

/**
 * @description 查看地推小组业绩 成员
 * 
 */
function lookgroup(){
	
	
}
/**
 *@description 配置组员 
 *	 
 */
function editteam(id){
	$("#error-info-editteam").html('');
	$.ajax({
		 type:"get",
		 url:'/index.php/maijinadmin/group/getgroupteam',
		 data:{group_id:id},
		 dataType:"json",
		 beforesend:function(){			
		 },
		 success: function(data){	
			var datainfo=eval(data);
			var userdata=datainfo.userlist;
			var userlistcontent='';
			if(datainfo.status == '1'){				
				for (var i = 0; i < userdata.length; i++) {
					if(userdata[i]['group_id'] == '0'){
						userlistcontent += '<li><input type="checkbox"  name="userid" value="'+userdata[i]['id']+'"/>'+userdata[i]['xingming']+'</li>';
					}else{
						userlistcontent += '<li><input type="checkbox" checked="checked" name="userid" value="'+userdata[i]['id']+'"/>'+userdata[i]['xingming']+'</li>';
					}
				}
				$("#teamgroupid").val(id);
				$("#userlistcontent").html(userlistcontent);
			}else{
				$("#dialog-editteam").html(userdata.info);
			}
		 },
	});	
	$("#dialog-editteam").dialog({height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
}

/**
 * @description 修改当前小组的成员
 * 
 */
function group_edit_groupteam() {
	 var userid='';//定义一个数组      
     $('input[name="userid"]:checked').each(function(){    
    	 userid +=$(this).val()+',';   
     });
	$.ajax({
		 type:"post",
		 url:'/index.php/maijinadmin/group/editgroupmember',
		 data:{userid:userid,groupid:$("#teamgroupid").val()},
		 dataType:"json",
		 beforesend:function(){	
			 
		 },
		 success: function(data){	
			var datainfo=eval(data);
			$("#error-info-editteam").html(datainfo.info);
		 },
	});	

}
/**
 * @description 移除小组内的组员
 */
function group_clear_groupteam(){
	 var userid='';
     $('input[name="userid"]:checked').each(function(){    
    	 userid +=$(this).val()+',';   
     });
     if(userid == ''){
    	 $("#error-info-editteam").html('请选择编号');
    	 return false;
     }
     $.ajax({
		 type:"post",
		 url:'/index.php/maijinadmin/group/cleargroupmember',
		 data:{userid:userid,groupid:$("#teamgroupid").val()},
		 dataType:"json",
		 beforesend:function(){				 
		 },
		 success: function(data){	
			var datainfo=eval(data);
			$("#error-info-editteam").html(datainfo.info);
		 },
	});	
	
}
/**
 * @description 查看小组业绩明细
 */
function performance(id){
	$("#manageinfo").html('');
	$("#performance").html('');
	$("#error-info-performance").html('');
	$.ajax({
		 type:"get",
		 url:'/index.php/maijinadmin/group/group_performance',
		 data:{groupid:id},
		 dataType:"json",
		 beforesend:function(){				 
		 },
		 success: function(data){	
			var datainfo=eval(data);
			if(datainfo.status == '1'){
				var groupinfo=datainfo.groupinfo;
				var userinfo=datainfo.userinfo;
				var performance=datainfo.performance;
				var content='';
				var sum=0;
				var marageinfo='小组名称:'+groupinfo["0"]["group_name"]+'创建时间:'+groupinfo["0"]["group_jointime"]+"";
				for (var i = 0; i < userinfo.length; i++) {
					marageinfo +=userinfo[i]['xingming']+"("+userinfo[i]['power_name']+")";
				}
				if(performance == '0'){
					$("#manageinfo").html(marageinfo);
					$("#performance").html('小组内没有组员');
					return false;
				}
				if(performance == '-1'){
					$("#manageinfo").html(marageinfo);
					$("#performance").html('当天业绩为0!');
					return false;
				}
				for (var i = 0; i < performance.length; i++) {
					content += '<li class="sub">编号:'+performance[i]["id"]+'今天关注:'+performance[i]["sub"]+'</li>'+
							   '<li class="unsub">取消关注:'+performance[i]["unsub"]+'</li>'+
							   '<li>今天合计:'+performance[i]["Total"]+'</li>'+
							   '<li><a target="_blank" href="/index.php/scancode/scancode/viwelist/'+performance[i]["id"]+'">业绩明细</a></li>';
				}
				$("#performance").html(content);
				$("#manageinfo").html(marageinfo);				
			}
		 },
	});
	$("#dialog-group-performance").dialog({height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
}
/**
 *@description 删除小组
 * 
 */
function delgroup(id){
	$.ajax({
		 type:"post",
		 url:'/index.php/maijinadmin/group/delgroup',
		 data:{groupid:id},
		 dataType:"json",
		 beforesend:function(){				 
		 },
		 success: function(data){	
			var datainfo=eval(data);
			alert(datainfo.info);
			location.reload();
		 },
	});	
}
/**
 * @description 编辑小组
 * 
*/
function editgroup(id) {
	$("#editgroup-error-info").html('');
	$("#dialog-group-editsave").dialog({height:510,width:840,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
	$.ajax({
		 type:"get",
		 url:'/index.php/maijinadmin/group/editgroup',
		 data:{groupid:id},
		 dataType:"json",
		 beforesend:function(){				 
		 },
		 success: function(data){	
			 var datainfo=eval(data);
			 var groupname=datainfo['groupinfo']["0"]['group_name'];
			 $("#group_edit_name").val(groupname);
			 var groupid=datainfo['groupinfo']["0"]['group_id'];
			 $("#save_edit_groupid").val(groupid);
			 var leader=datainfo['option']['leader'];
			 var executives=datainfo['option']['executives'];
			 var majordomo=datainfo['option']["majordomo"]; 
			 var leadercontent = '<option value="0">请选择组长</option>';
			 var executivescontent = ' <option value="0">请选择主管</option>';
			 var majordomocontent = '<option value="0">请选择总监</option>';
			 for(var i=0 ; i < leader.length;i++){
				 if(datainfo['groupinfo']["0"]['group_leader'] != leader[i]["id"]){
					 leadercontent += "<option value="+leader[i]["id"]+" >"+leader[i]["xingming"]+"</option>";
				 }else{					 
					 leadercontent += "<option value="+leader[i]["id"]+" selected='selected'>"+leader[i]["xingming"]+"</option>";
				 }
			 }
			 for(var i=0 ; i < executives.length;i++){
				 if(datainfo['groupinfo']["0"]['group_executives'] != executives[i]["id"]){
					 executivescontent += "<option value="+executives[i]["id"]+">"+executives[i]["xingming"]+"</option>";
				 }else{
					 executivescontent += "<option value="+executives[i]["id"]+" selected='selected'>"+executives[i]["xingming"]+"</option>";
				 }
			 }
			 for(var i=0 ; i < majordomo.length;i++){
				 if(datainfo['groupinfo']["0"]['group_majordomo'] != majordomo[i]["id"]){
					 majordomocontent += "<option value="+majordomo[i]["id"]+">"+majordomo[i]["xingming"]+"</option>";
				 }else{
					 majordomocontent += "<option value="+majordomo[i]["id"]+" selected='selected'>"+majordomo[i]["xingming"]+"</option>";
				 }
			 }			
			 $("#group_edit_leader").html(leadercontent);
		     $("#group_edit_executives").html(executivescontent);
		     $("#group_edit_majordomo").html(majordomocontent);			
		 },
	});
}
/**
 * @description 保存修改的小组
 * 
 */
function save_edit_groupinfo(){
	var group_name    =$("#group_edit_name").val();
	var group_leader  =$("#group_edit_leader").val();
	if(group_name ==''){
		$("#editgroup-error-info").html("小组名称不可为空");
		return false;
	}	
	if(group_leader =='' || group_leader == '0'){
		$("#editgroup-error-info").html("小组组长不可为空");
		return false;
	}
	$.ajax({
		 type:"post",
		 url:'/index.php/maijinadmin/group/save_editgroup',
		 data:$("#editgroupdata").serialize(),
		 dataType:"json",
		 beforesend:function(){				 
		 },
		 success: function(data){	
			var datainfo=eval(data);
			$("#editgroup-error-info").html(datainfo.info);
		 },
	});
}