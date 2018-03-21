//获取代金券
function myvoucherlist(openid){
	$.ajax({ 
		 type: "post",
		 url: "do/index.php",
         data:{action:'myvoucher',openid:openid},
         dataType:"json",
         success: function(result){
			 var data=eval(result);
             var content='';
			 for(var i=0;i<data.num;i++){
				 switch(data['info'][i]['log_type']){
					 case '1':
						 content+='<div class="dyellow daijin margint20"><p class="btit color_w">'+data['info'][i]['voucher_pic']+'元现金券</p><p class="stit color_w">来源：关注&nbsp;&nbsp;&nbsp;有效期至：'+data['info'][i]['log_exceed']+'</p><img src="public/img/guoqi.png" alt="已使用" title="已过期" /></div>';
						 break;
					case '2':
						 content+='<div class="dgreen daijin"><p class="btit color_w">5元现金券</p><p class="stit<img src="images/guoqi.png"alt="已使用" title="已过期" /></div>';
						 break;
					case '3':
						 content+='<div class="dred daijin "><p class="btit color_w">'+data['info'][i]['voucher_pic']+'元现金券</p><p class="stit color_w">来源：首次分享&nbsp;&nbsp;&nbsp;有效期至：'+data['info'][i]['log_exceed']+'</p><img src="public/img/guoqi.png" alt="已使用" title="已过期" /></div>';
						 break;
					case '4':
						 content+='<div dblue="dblue daijin "><p class="btit color_w">'+data['info'][i]['voucher_pic']+'元现金券</p><p class="stit color_w">来源：分享&nbsp;&nbsp;&nbsp;有效期至：'+data['info'][i]['log_exceed']+'</p><img src="public/img/guoqi.png" alt="已使用" title="已过期" /></div>';
						 break;
					case '5':
						 content+='<div dblue="dred daijin "><p class="btit color_w">'+data['info'][i]['voucher_pic']+'元现金券</p><p class="stit color_w">来源：分享&nbsp;&nbsp;&nbsp;有效期至：'+data['info'][i]['log_exceed']+'</p><img src="public/img/guoqi.png" alt="已使用" title="已过期" /></div>';
						 break;
					 default:
					 break;
				 }
			 }
			 $("#content").html(content);
			 alert
		 },
	});
}