$(document).ready(function(){
	$('#content_ajax_page a').live('click',function(){	
		var url=$(this).attr('href');
		seachvoucher(url);
		return false;//阻止链接跳转
	});
})

/**
 * 搜索框
 */
function seachvoucher(url){
	var keyword=$("#keyword").val();
	var link='';
	if(url == "0"){
		link='/index.php/maijinadmin/voucherlog/searchvoucher';
	}else{
		link=url;
	}
	$.ajax({
		 type:"get",
		 url: link,
		 data:{keyword:keyword},
		 dataType:"json",
		 success: function(data){
			 var voucher=eval(data);
			 if(voucher.status == '1050'){
				 $("#content_list").html(voucher.info);
				 $("#content_page").html('');
			 }
			 if(voucher.status == '3000'){
				 $("#content_list").html(voucher.info);
				 $("#content_page").html('');
			 }
			 if(voucher.status == '1000'){
				 var clothesnum=voucher.clothesnum;
				 var content_title="<tr><td>类型</td><td>金额</td><td>领取时间</td><td>使用时间</td><td>过期时间</td><td>状态</td></tr>";
				 var content_list='';
				 var list=voucher.voucherlist;
				 var vouchertype=voucher.vouchertype;
				 var voucherstatus=voucher.voucherstatus;
				 for (var index = 0, len = list.length; index < len; index++) {  
					content_list += '<tr><td>'+vouchertype[list[index]['log_type']]+'</td><td>'+list[index]['voucher_pic']+'</td><td>'+list[index]['log_joindate']+'</td><td>'+list[index]['log_lastdate']+'</td><td>'+list[index]['log_exceed']+'</td><td>'+voucherstatus[list[index]['log_voucher_status']]+'</td></tr>';        
				 }
				 $("#content_page").html('');
				 $("#content_title").html(content_title);
				 $("#content_list").html(content_list);
				 $("#content_ajax_page").html(voucher.page);
			 }
		 }
	})
}

