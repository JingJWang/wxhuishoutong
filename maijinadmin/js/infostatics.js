var api={
	'searchinfo':'/index.php/maijinadmin/infostatics/staticda',
	'delete_admin_user':'/index.php/maijinadmin/adminuser/delete_admin_user',
	'get_admin_user':'/index.php/maijinadmin/adminuser/get_admin_user',
	'add_edit_admin_user':'/index.php/maijinadmin/adminuser/add_edit_admin_user',
	'select_admin_user_yj':'/index.php/maijinadmin/adminuser/select_admin_user_yj',
	'get_role':'/index.php/maijinadmin/adminuser/get_role'
};
$(document).ready(function(){
	$('#content_ajax_page a').live('click',function(){
		var url=$(this).attr('href');
		searchuser(url);
		return false;//阻止链接跳转
	});
	//时间dialog
	$(".datepicker").datepicker();
	$(".selectemplyee-yj").click(function() {
		$.ajax({ 
			type: "post",
			url: api.searchinfo,
			data:{date:$(".datepicker").val()},
			dataType:"json",
			success: function(data){
				 var result=eval(data);
         		if (result.status==400) {
         			var str = ' <p><b>用户统计：</b>&nbsp;&nbsp;加入人数：<a href="javascript:void(0);" id="e_guanzhu">'+result.data.user.join_num+'</a>人<br><b>报价统计：</b>成交单数：<a href="javascript:void(0);" id="e_guanzhu">'+result.data.user.order_count+'</a> 单&nbsp;&nbsp;&nbsp;&nbsp; 成交额：<a href="javascript:void(0);" id="e_order_y">'+result.data.user.order_sum+'</a> 元<br><b>任务统计：</b>回收任务：<a href="javascript:void(0);" id="e_order_d">'+result.data.task.turnover+'</a> 次&nbsp;&nbsp;&nbsp;&nbsp;邀请任务：<a href="javascript:void(0);" id="e_weight_yj">'+result.data.task.invite_u+'</a> 次&nbsp;&nbsp;&nbsp;&nbsp;游戏任务：<a href="javascript:void(0);" id="e_weight_yj">'+result.data.task.game+'</a> 次<br><b>商城：</b>成交单数：<a href="javascript:void(0);" id="e_order_d">'+result.data.shop.count+'</a> 单</p>';
         			$('.alert-success_next').html(str);
         		}else{
					alert(result.info);
         		}
			}
		})
	});

})

