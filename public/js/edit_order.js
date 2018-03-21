jQuery(function(){
	edit_order_info();
});
//获得需要修改订单的内容
function edit_order_info(){
	$.ajax({ 
		 type: "get",
		 url: "/index.php/cooperation/cooperation/editorderinfo",
         data:'',
         dataType:"json",
         success: function(result){
			var data=eval(result);			
			$("#h_tel").html(data['data']['order']['0']['order_mobile']);
			$("#city").html(data['data']['order']['0']['order_province']+data['data']['order']['0']['order_city']+data['data']['order']['0']['order_county']);
			$("#address").html(data['data']['order']['0']['order_address']);
			$("#date").html(data['data']['order']['0']['order_joindate']);
			$("#openid").val(data['data']['order']['0']['weixin_id']);
			var type=data['data']['order']['0']['order_type'];
			var ordertype=data['data']['ordertype'][type];
			$("#ordertype").html(ordertype);
			switch(data['data']['order']['0']['order_num']){
				case '1':
					$("#num").html('10件以下');
					break;
				case '2':
					$("#num").html('10-40件');
					break;
				case '3':
					$("#num").html('40-80件以下');
					break;
				case '4':
					$("#num").html('80件以上');
					break;
				default:
					break;
			}
			var content='<label class="ctrl_title">我的现金券</label>';
			var content1='<label class="ctrl_title">首次报单券</label>';
			var content2='<label class="ctrl_title">每周分享现金券</label>';
			var content3='<label class="ctrl_title">订单分享现金券</label>';
			var ontent1num=0;
			var ontent2num=0;
			var ontent3num=0;
			var ontent4num=0;
			for(var i=0;i<data['data']['voucher'].length;i++){
				switch(data['data']['voucher'][i]['log_type']){
					case '1':
						ontent1num+=1;
						content+='<input type="checkbox" name="h_quan"  value="'+data['data']['voucher'][i]['id']+'"><div class="quan-value">面值'+data['data']['voucher'][i]['voucher_pic']+'现金券(关注券)</div><br/>';
						break;
					case '2':
						ontent2num+=1;
						content1+='<input type="checkbox" name="h_quan"  value="'+data['data']['voucher'][i]['id']+'"><div class="quan-value">面值'+data['data']['voucher'][i]['voucher_pic']+'现金券(首次报单券)</div><br/>';
						break;
					case '3':
						ontent3num+=1;
							content2+='<input type="radio" name="h_quan_pyq"  value="'+data['data']['voucher'][i]['id']+'"><div class="quan-value">面值'+data['data']['voucher'][i]['voucher_pic']+'现金券(分享券)</div><br/>';
						break;
					case '4':
						ontent4num+=1;
							content3+='<input type="radio" name="h_quan_order"  value="'+data['data']['voucher'][i]['id']+'"><div class="quan-value">面值'+data['data']['voucher'][i]['voucher_pic']+'现金券(分享券)</div><br/>';
						break;
				}
			}
			if(ontent1num!='0'){
				$("#voucher1").html(content);
			}
			if(ontent2num!='0'){
				$("#voucher2").html(content1);
			}
			if(ontent3num!='0'){
				$("#voucher3").html(content2);
			}
			if(ontent4num!='0'){
				$("#voucher4").html(content3);
			}
		 },
	});
	
}
//提交已经完成的订单
function update_order(){	
	 var h_quan=checkbox();
	 var h_quan_pyq=$('input[name="h_quan_pyq"]:checked').val();
	 var h_quan_order=$('input[name="h_quan_order"]:checked').val();
	 if(typeof h_quan_pyq!="undefined"){
		 voucher1=h_quan+h_quan_pyq;
	 }else{
		 voucher1=h_quan;
	 }
	 if(typeof h_quan_order!="undefined"){		 
		 if(typeof h_quan_pyq!="undefined"){
			  voucher2=voucher1+','+h_quan_order;
		 }else{
			  voucher2=voucher1+h_quan_order;		 }
	 }else{
		 voucher2=voucher1;
	 }
	 var price=$('#h_price').val();
	 var make=$('#h_remark').val();
	 var weight=$('#order_weight').val();
	 if(price !='' && weight !=''){
		  $.ajax({ 
			 type: "post",
			 url: "/index.php/cooperation/cooperation/ordersucc",
			 data:{prirce:price,make:make,voucher:voucher2,order_weight:weight,openid:$("#openid").val()},
			 dataType:"json",
			 success: function(result){
				 var data=eval(result);
				 if(data['status']==1){					 
					 UrlLocation('/cooperation/manage.html');
				 }else{
					 alert(data.info);
					 UrlLocation('/cooperation/manage.html');
				 }
			 },
		});
	 }else{
		 $("#info").html('必填选项不可为空');
	 }
	
}
//获取选中的checkbox
function checkbox(){
	var str='';
	$('input:checkbox').each(function() {
        if ($(this).attr('checked') ==true) {
                str+=$(this).val()+',';
        }
	});
	return str;
}