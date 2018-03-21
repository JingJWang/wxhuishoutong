<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>查询订单</title>
    <link rel="stylesheet" href="/static/home/css/f_style.css"/>
    <link rel="stylesheet" href="/static/home/css/biaodan.css"/>
</head>
<style>
.title{
	margin:0;pading:0;width:100%;height:40px;text-align:center;line-height:40px;background:#019858;color:white;font-size:16px;
}
.search{
	margin-top:30px;
}
.keyword{
	width:50%;height:30px;border:1px solid #d0d0d0;margin-left:10%;border-radius:10px;text-align:center;
}
.but{
	width:20%;height:30px;margin-left:10px;color:white;background:#019858;border-radius:10px;font-size:14px;
}
.content{
	width:100%;margin-top:30px;border-radius:10px;
}
.list{
	width:100%;margin-top:10px;font-family:"Arial";font-size:15px; 
}
.list li{
	width:100%;border:1px solid #F0F0F0;background:#FCFCFC;text-align:left;margin-top:10px;
}
.list li .name{
	margin-left:5%;
}
.list li .number{
	margin-left:5%;margin-top:10px;
}
.list li .attr{
	margin-left:5%;margin-top:10px;
}
#turn_gif_box{width:100%; height:100%; background: rgba(255, 255, 255, 0.7); display: none; position:fixed; left:0; top:0; z-index: 10;}
#turn_gif{width:100%; height:100%; display: table;}
#turn_gif span{display: table-cell; vertical-align: middle; text-align: center;font-size: 0; }
#turn_gif span img{width:4.4rem; height:3rem;}
</style>
<body>
<div class="title">回收通 </div>
<div class="search">
    <input type="text" id="keyword" class="keyword"/><input  onclick="searchorder();" type="button" class="but" value="搜索"/>
</div>
<div class="content">
         <ul id="orderlist" class="list">
                 
        </ul>
</div>
<div id="turn_gif_box">
    <div id="turn_gif">
        <span>
            <img src="/static/home/images/loading.gif" alt=" "/>
        </span>
    </div>
</div>
<script src="/static/home/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
var  searchorderUrl='/index.php/cooperation/coop/SearchOrder';
/**
 * 搜索订单
 */
function searchorder(){
	var wxUrl='/index.php/cooperation/coop/ViewOrdre';
	var mobile  = $("#keyword").val();
	var reg=/^\d{11}$/;
    if(!reg.test(mobile)){
        alert('请输入手机号码');  
        return false;  
    }
	$.ajax({
		   type: "POST",
		   url:  searchorderUrl,
		   data: "mobile="+mobile,
		   dataType:"json",
		   beforeSend: function(){
	        	 $("#turn_gif_box").css('display','block');
	       },
		   success: function(data){
			 if(data.status == 1000 ){
				 var list = '';
				 var orderattr = '';
				 $.each(data.data, function(i, item){     
					 list = list + '<li><p class="name">物品名称:'+item['name']+'</p>'+
					   '<p class="number">订单编号:'+item['number']+'</p>'
					   +'<p class="number">订单处理:<a href="'+wxUrl+'?id='+item['number']+'">去支付</a></p>';
			     });
			     $("#orderlist").html(list); 
		     }
		     if(data.status == 3000){
		    	    alert(data.msg);
		     }
		   }, 
		   complete :function(XMLHttpRequest, textStatus){
			   $("#turn_gif_box").css('display','none');
		   },
		   error:function(XMLHttpRequest, textStatus, errorThrown){ 
			   
		   }
	}); 
	
}
</script>
</body>
</html>