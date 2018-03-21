var api={
	'select_admin_user':'/index.php/maijinadmin/adminuser/select_admin_user',
    'select_admin_user_group':'/index.php/maijinadmin/adminuser/select_admin_user_group',
    'add_edit_group':'/index.php/maijinadmin/group/add_edit_group',
    'get_group':'/index.php/maijinadmin/group/get_group'
};
$(document).ready(function(){
	
	//弹出对话框
	$(".btn-select").click(function(){
		//清空内容
		$("#dialog-select input").each(function(){
			 $(this).val("");						 
		});
		$("#dialog-select select").each(function(){
			 $(this).find('option').eq(0).attr('selected','selected');		 
		});
        $("#listinfo").html('');
        $('.fengye').addClass('disabled');
        $('#pagenum').html('');
        $('#pagenow').html('');
        $('#pagetotal').html('');
        
		$("#dialog-select").dialog({height:560,width:1020,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
        
        var datatype=$(this).attr('data-type');
        $("#dialog-select #gx_type").val(datatype);
	});
    //弹出对话框
	$(".btn-select-group").click(function(){
		//清空内容
        $("#idcell").attr('checked',false);
        $("#grouplistinfo").html('');
        var dataObj={
                power_type:5,
                group_id:$("#groupid").val(),
				status:1
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_admin_user_group,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
                     var row=result.num;
					 var content='';				 
					 for(var i=0;i<row;i++){
						 content+='<tr>';
                         if(result['data'][i]['group_id']!=0){
                             content+='<td><input type="checkbox" name="idcell" value="'+result['data'][i]['id']+'" data-name="'+result['data'][i]['xingming']+'" checked></td>';
                         }else{
                             content+='<td><input type="checkbox" name="idcell" value="'+result['data'][i]['id']+'" data-name="'+result['data'][i]['xingming']+'"></td>';
                         }
                         content+='<td>'+result['data'][i]['xingming']+'</td><td>'+result['data'][i]['maile']+'</td><td>'+result['data'][i]['mobile']+'</td><td>'+result['data'][i]['address']+'</td>';
					 }
					 $('#grouplistinfo').html(content);
				 }else{
					 alert(result.info);
				 }	 
			 }
		});
        $("#dialog-select-group").dialog({height:580,width:1060,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
	});
    
    //检索员工
	$(".btn-search").click(function(){
        var power_type="";
        var gx_type=$("#gx_type").val();
        if(gx_type=="leader"){
            power_type=6;
        }else if(gx_type=="executives"){
            power_type=7;
        }else if(gx_type=="majordomo"){
            power_type=8; 
        }
		var dataObj={
				xingming:$("#gx_xingming").val(),
				address:$("#gx_address").val(),
                power_type:power_type,
				status:1
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_admin_user,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;
					 var pagenum=result.pagenum;//总页数
					 var pagetotal=result.pagetotal;//总条数
					 $("#pagetotal").html(pagetotal);
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
						 content+='<tr><td>'+result['data'][i]['xingming']+'</td><td>'+result['data'][i]['maile']+'</td><td>'+result['data'][i]['mobile']+'</td><td>'+result['data'][i]['address']+'</td>';
						 content+='<td><button type="button" class="btn btn-primary btn-xs" data-id='+result['data'][i]['id']+' data-name='+result['data'][i]['xingming']+'>选择</button></td>';
					 }
					 $('#listinfo').html(content);
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
		};
		var orgid=$(this).attr('orgid');
		var pagetotal=$("#pagetotal").html();
		var pagenow=parseInt($("#pagenow").html());
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
        var power_type="";
        var gx_type=$("#gx_type").val();
        if(gx_type=="leader"){
            power_type=6;
        }else if(gx_type=="executives"){
            power_type=7;
        }else if(gx_type=="majordomo"){
            power_type=8; 
        }
		var dataObj={
				xingming:$("#gx_xingming").val(),
				address:$("#gx_address").val(),
                power_type:power_type,
				status:1,
				page:pagenow
		};
		$.ajax({ 
			 type: "post",
			 url: api.select_admin_user,
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
						 content+='<tr><td>'+result['data'][i]['xingming']+'</td><td>'+result['data'][i]['maile']+'</td><td>'+result['data'][i]['mobile']+'</td><td>'+result['data'][i]['address']+'</td>';
						 content+='<td><button type="button" class="btn btn-primary btn-xs" data-id='+result['data'][i]['id']+' data-name='+result['data'][i]['xingming']+'>选择</button></td>';	 
					 }
					 $('#listinfo').html(content);
				}else{
					 alert(result.info);
				}
			 }
		});	
	});
    
    //选择
	$(".btn-xs").live('click',function(){
		 var id=$(this).attr('data-id');
         var name=$(this).attr('data-name');
         var gx_type=$('#gx_type').val();
         $('#g_'+gx_type+'_name').val(name);
         $('#g_'+gx_type+'_id').val(id);
         $("#dialog-select").dialog("close");
	});
    
    
    //全选
    $('#idcell').click(function(){
        if($(this).is(':checked')){
          $('#grouplistinfo input[type=checkbox]').prop({checked:true});
        }else{
          $('#grouplistinfo input[type=checkbox]').prop({checked:false});
        }
    });
    
    //多选
    $(".btn-xz-group").live('click',function(){
         var idstring="";
         var namestring="";
         var len=$('#grouplistinfo input[type=checkbox]:checked').length;
		 $('#grouplistinfo input[type=checkbox]:checked').each(function (i,n) {
            if(i==(len-1)){
                idstring+=$(this).val();
                namestring+=$(this).attr("data-name");
            }else{
                idstring+=$(this).val()+',';
                namestring+=$(this).attr("data-name")+',';
            }
         });
         $('#g_member_name').val(namestring);
         $('#g_member_id').val(idstring);
         $("#dialog-select-group").dialog("close");
	});
    
    //添加小组
    $(".addgroup").click(function(){
        var dataObj={
                id:$("#groupid").val(),
                g_name:$("#g_name").val(),
                g_leader_id:$("#g_leader_id").val(),
                g_leader_name:$("#g_leader_name").val(),
                g_executives_id:$("#g_executives_id").val(),
                g_executives_name:$("#g_executives_name").val(),
                g_majordomo_id:$("#g_majordomo_id").val(),
                g_majordomo_name:$("#g_majordomo_name").val(),
                g_member_id:$("#g_member_id").val(),
                g_member_name:$("#g_member_name").val(),
                g_member_old_id:$("#g_member_old_id").val(),
                g_status:$("#g_status").val()
        };
        $.ajax({
             type: "post",
             url: api.add_edit_group,
             data:dataObj,
             dataType:"json",
             success: function(data){
                 var result=eval(data);
                 alert(result.info);
                 if(result.status==0){
                     window.location.href='./group.html';
                 }
             }
        });
    });
    
    //编辑时获取地堆小组内容
    var type=getQueryStringByName("type");
    var group_id=getQueryStringByName("id");
    if(type=='edit'){
        groupinfo(group_id);
    }
});
function groupinfo(group_id){
    var dataObj={
                id:group_id
    };
    $.ajax({
         type: "post",
         url: api.get_group,
         data:dataObj,
         dataType:"json",
         success: function(data){
             var result=eval(data);
             if(result.status==0){
                $("#groupid").val(result.data.group_id);
                $("#g_name").val(result.data.group_name);
                $("#g_leader_id").val(result.data.group_leader_id);
                $("#g_leader_name").val(result.data.group_leader_name);
                $("#g_executives_id").val(result.data.group_executives_id);
                $("#g_executives_name").val(result.data.group_executives_name);
                $("#g_majordomo_id").val(result.data.group_majordomo_id);
                $("#g_majordomo_name").val(result.data.group_majordomo_name);
                $("#g_member_id").val(result.data.group_member_id);
                $("#g_member_name").val(result.data.group_member_name);
                $("#g_member_old_id").val(result.data.group_member_id);
                $("#g_status").val(result.data.group_status);
             }else{
                 alert(result.info);
             }
         }
    });
}