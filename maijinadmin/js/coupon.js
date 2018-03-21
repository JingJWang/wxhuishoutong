var api={
	'select_voucher':'/index.php/maijinadmin/voucher/select_voucher',
	'get_voucher_id':'/index.php/maijinadmin/voucher/get_voucher_id',
	'update_voucher':'/index.php/maijinadmin/voucher/update_voucher'
};
$(document).ready(function(){
	
	//修改代金券dialog
	$(".a-coupon").live('click',function(){
		var couponid=$(this).attr('orgid');	
		var dataObj={
				id:couponid
		};
		$.ajax({ 
			 type: "post",
			 url: api.get_voucher_id,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);	
				 if(result.status==0){
					$("#dialog-coupon").dialog({height:320,width:740,modal: true,overlay: {backgroundColor: '#000',opacity: 0.5}});
					$("#dialog-coupon .pic-coupon").val(result.data.voucher_pic);
					$("#dialog-coupon .day-coupon").val(result.data.voucher_day);
					$("#dialog-coupon #id-coupon").val(result.data.id);
					if(result.data.id>2){//>2 随机金额
						$("#dialog-coupon .pic-coupon").attr('disabled',true);
					}else{
						$("#dialog-coupon .pic-coupon").attr('disabled',false);
					}
				 }else{					 
					 alert(result.info);
				 }
			 }
		});
	});
	
	//修改代金券金额
	$(".edit-coupon").live("click",function(){
		var couponid=$('#id-coupon').val();
		var pic=$('.pic-coupon').val();
		var day=$('.day-coupon').val();
		var dataObj={
				id:couponid,
				voucher_pic:pic,
				voucher_day:day
		};
		if(pic!=''){
			if(/^[0-9]+(\.[0-9]+)?$/g.test(pic)){
				if(/^[0-9]+$/g.test(day)){
					$.ajax({ 
					 type: "post",
					 url: api.update_voucher,
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
				}else{
					alert('有效期必须为整数类型');
				}
			}else{
				alert('金额必须为整数或者小数类型');
			}			
		}else{
			alert('请输入金额');			
		}
	});
	
	//查询代金券
	coupon_list();
	
});


function coupon_list(){
		var dataObj={};
		$.ajax({ 
			 type: "post",
			 url: api.select_voucher,
			 data:dataObj,
			 dataType:"json",
			 success: function(data){
				 var result=eval(data);
				 if(result.status==0){
					 var row=result.num;			
					 var content='';
					 for(var i=0;i<row;i++){
						 content+='<tr><td>'+result['data'][i]['voucher_miaoshu']+'</td>';			 
						 if(result['data'][i]['id']>2){
							content+='<td>随机金额</td>';
						 }else{
							content+='<td>'+result['data'][i]['voucher_pic']+'元</td>';
						 }
						 content+='<td>'+result['data'][i]['voucher_day']+'天</td><td><a href="javascript:void(0);" class="a-coupon" orgid="'+result['data'][i]['id']+'">修改</a></td></tr>';
					 }
					 $('#coupon_list').html(content);
				 }else{
					 alert(result.info);
				 }
			 }
	});
}
