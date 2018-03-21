/**
 * 
 */
$(document).ready(function(){
	lookodrer();
})
function UrlLocation(){
	window.location.href='manage.html';
}
function lookodrer(){
	 $.ajax({ 
		 type:"post",
		 url:"/index.php/cooperation/cooperation/lookorder",
         data:'',
         dataType:"json",
         success: function(result){
			 var data=eval(result);
			 if(data.status==1){
				 var num='';
				 var voucher='';
				 $('#monile').html(data['data']['order']['0']['order_mobile']);//手机号
				 $('#address').html(data['data']['order']['0']['order_province']+data['data']['order']['0']['order_city']+data['data']['order']['0']['order_county']);//地址
				 $('#addresinfo').html(data['data']['order']['0']['order_address']);//小区
				 $('#joindate').html(data['data']['order']['0']['order_joindate']);//加入时间
				 $('#site').html(data['data']['order']['0']['user_address']);//成交地点
				 $('#lastdate').html(data['data']['order']['0']['order_lastdate']);//成交时间
				 $('#weight').html(data['data']['order']['0']['order_weight']);//重量
				 $('#pic').html(data['data']['order']['0']['order_pic']);//金额
				 switch (data['data']['order']['0']['order_num']) {
						case '1':
							num='10以下';
							break;
						case '2':
							num='10-40';					
							break;
						case '3':
							num='40-80';
							break;
						case '4':
							num='80以上';
							break;
					default:
						break;
				}
				 $('#num').html(num);//数量				
				for (var i = 0; i < data['data']['voucher'].length; i++) {
					 switch (data['data']['voucher'][i]['log_type']) {
							case '1':
								voucher += '<p class="paddingl60">'+data['data']['voucher'][i]['voucher_pic']+'元现金券(关注)';
								break;
							case '2':
								voucher += '<p class="paddingl60">'+data['data']['voucher'][i]['voucher_pic']+'元现金券(首次报单)';
								break;
							case '3':
								voucher += '<p class="paddingl60">'+data['data']['voucher'][i]['voucher_pic']+'元现金券(每周分享)';
								break;
							case '4':
								voucher += '<p class="paddingl60">'+data['data']['voucher'][i]['voucher_pic']+'元现金券(订单分享)';
								break;
							default:
								break;
					}
				}
			 $("#voucherinfo").html(voucher);				 
			 }
		 },
	});
	
}