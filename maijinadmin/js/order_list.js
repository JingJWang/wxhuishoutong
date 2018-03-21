$(document).ready(function(){
	$('#content_ajax_page a').live('click',function(){
		var url=$(this).attr('href');
		seachorder(url);
		return false;//阻止链接跳转
	});
})

/**
 * 搜索框
 */
function seachorder(url){
	var keyword=$("#keyword").val();	
	var link='';
	if(url == "0"){
		link='/index.php/maijinadmin/order/searchordre';
	}else{
		link=url;
	}
	$.ajax({
		 type:"get",
		 url: link,
		 data:{keyword:keyword},
		 dataType:"json",
		 success: function(data){
			 var order=eval(data);
			 if(order.status == '1050'){
				 $("#content_list").html(order.info);
				 $("#content_page").html('');
			 }
			 if(order.status == '3000'){
				 $("#content_list").html(order.info);
				 $("#content_page").html('');
			 }
			 if(order.status == '1000'){
				 var clothesnum=order.clothesnum;
				 var content_title="<tr><th>用户昵称</th><th>用户状态</th><th>订单编号</th><th>联系方式</th><th>归属省份</th><th>归属市/区</th><th>归属县/乡</th><th>详细地址</th><th>数量</th><th>成交金额</th><th>重量</th><th>提交时间</th><th>订单状态</th></tr>";
				 var content_list='';
				 var list=order.list;
				 var wxuserstatus='';
				 var orderstatus='';
				 for (var index = 0, len = list.length; index < len; index++) {  
					 wxuserstatus=order['wx_status'] == '1'? '关注状态' :'未关注';
	                 orderstatus=order['order_status'] == '1' ?'未成交':'已成交';                        
	                 content_list += '<tr><th>'+list[index]['wx_name']+'</th><th>'+wxuserstatus+
	                 '</th><th>'+list[index]['order_randid']+'</th><th>'+list[index]['order_mobile']+
	                 '</th><th>'+list[index]['order_province']+'</th><th>'+list[index]['order_city']+
	                 '</th><th>'+list[index]['order_county']+'</th><th>'+list[index]['order_address']+
	                 '</th><th>'+clothesnum[list[index]['order_num']]+'</th><th>'+list[index]['order_pic']+
	                 '</th><th>'+list[index]['order_weight']+'</th><th>'+list[index]['order_joindate']+
	                 '</th><th>'+orderstatus+'</th></tr>';                
				 }	
				 $("#content_title").html(content_title);
				 $("#content_list").html(content_list);
				 $("#content_page").html('');			
				 $("#content_ajax_page").html(order.page)
			 }
		 }
	})
}

