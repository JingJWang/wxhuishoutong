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
.info{
	font-sizt:15px;color:red;text-align:center;margin-top:10px;
}
.content{
	margin-top:30px;pading:0;
}
.content ul li{
	margin-top:10px;margin-left:10%;
}
.content dl {
	margin-left:20%;
}
.submit{
	text-align:center;margin-top:20px;
}
.but{
	width:30%;height:40px;margin-left:10px;margin-top:5px;color:white;background:#019858;border-radius:10px;font-size:14px;
}
.keyword{
	width:50%;height:30px;border:1px solid #d0d0d0;margin-left:2%;border-radius:10px;text-align:center;
}
</style>
<body>
<div class="title">回收通 </div>
<p class="info">请您仔细核对以下用户订单信息</p>
<div class="content"> 
    <ul>
        <li>用户昵称:<?php echo $orderinfo['0']['wx_name']; ?></li>
        <li>联系电话:<?php echo $orderinfo['0']['wx_mobile']; ?></li>
        <li>订单标题:<?php echo $orderinfo['0']['order_name']; ?></li>
        <li>订单编号:<?php echo $orderinfo['0']['order_number']; ?></li>
        <li>物品属性:</li>
    </ul>   
    <dl>
        <?php  
         $orderattr=json_decode($orderinfo['0']['electronic_oather']);
          foreach ($orderattr  as $key=>$val){
                        echo '<dd><span>',$attr[$key],':</span>',$val,'</dd>';
         }?>
    </dl>
</div>
<div class="submit">
<form action="<?php echo $jspay; ?>" method="post">
 <ul>
     <li>
            请输入金额:<input type="text" id="keyword"  name="keyword" class="keyword"/>RMB
             <input type="hidden" name="ordername" value="<?php echo $orderinfo['0']['order_name']; ?>"/>
             <input type="hidden" name="ordernumber" value="<?php echo $orderinfo['0']['order_number']; ?>"/>
     </li>
     <li><input  type="submit" onclick="return Check();" class="but" value="去支付"/></li>
 </ul>
</form>
</div>
<script src="/static/home/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
/**
 * 校验数据格式
 */
function Check(){
	var pri  = $("#keyword").val();
	var reg=/^(([1-9]\d*)|\d)(\.\d{1,2})?$/;
    if(!reg.test(pri)){
        alert('请输入正确的金额');  
        return false;  
    }
    return true;  
}
</script>
</body>
</html>