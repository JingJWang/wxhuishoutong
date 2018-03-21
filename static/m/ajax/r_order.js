var orderList={
		list:function(obj,option){
			if(obj == true){
				$(obj).addClass('indent_hover').siblings().removeClass('indent_hover');
			}
			var u='/index.php/nonstandard/order/getOrderList';
			var d='sign='+option;
			var f=function(res){
				if(res.status == request_succ){
					var content='';
					var sign='';
					$.each(res.data,function(key,val){
						if(val.type == 1){
							sign = 'goldPhone';
						}else{
							sign = 'goldNum';
						}
						content = content + '<div class="list">'+
						'<div class="list_left"><p class="'+sign+'">'+val.name+'</p>'+
						'<p class="goldMoney"></p>'+
						'<p class="listTime">'+val.jointime+'</p>'+
						'</div><div class="list_right">'+val.status+'</div>'+
						'<a href="javascript:;" class="list_a">订单详情</a>'+
						'<a href="'+val.info+'" class="list_a">订单详情</a></div>';
					});
					$(".accomplishList").html(content);
				}
				if(res.status == request_fall){
					alert(res.msg);
					content = '<div class="nonesj"><span></span></div>';
					$(".accomplishList").html(content);
				}
			}
			AjaxRequest(u,d,f);
		}
}
orderList.list(true,'all');

