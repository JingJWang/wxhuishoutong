<html>
<head>
<title>回收通</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta name="format-detection" content="telephone=no">
<link href="/static/weixin/public/css/common.css" type="text/css" rel="stylesheet">
<style type="text/css">
.userlist{
    margin:0 auto; padding:0;width:300px;
}
.userlist table {
	width:300px;
	height:80px;
	
}
.userlist table tr td{
    margin-top:10px;
}
.userlist table tr td img{
	width:50px;
	height:60px;
	float:left;
}
.title{
	font-size:20px;
	text-align:center;
	margin-bottom:10px;
}
</style>
</head>
<body>
<div class="top t_c">
回收通
</div>
<div class="userlist">
 <p class="title"><?php echo date('Y-m-d'); ?>我的业绩:</br>关注:<?php echo $sub-$unsub; ?> 取消关注:<?php echo $unsub; ?>重复关注:<?php echo  $cfsub; ?></br><!-- 以下为业绩明细 --></p>
    <table>
      <?php
       /*  if(is_array($userlist)){
             foreach ($userlist as $user){
                       echo '<tr><td><img alt=""  src="'.$user['subscribe_img'].'"><p>用户 '.mb_substr($user['subscribe_name'],0,5).'**</br> 
                             <font style="color:#228B22;">'.$user['subscribe_jointime'].' 关注我们</font></br>';
                if($user['unsub'] == '-1'){
                       echo '<font style="color:red;">'.$user['unsubinfo'].' 取消关注</font></br>';
                   if($user['subscribe_type'] == '2'){
                       echo '<font style="color:#0000C6;">------------------该用户重复关注!</font></p></td></tr>';
                   }
                }else{
                    if($user['subscribe_type'] == '2'){
                       echo '<font style="color:#0000C6;">------------------该用户重复关注!</font></p></td></tr>';
                    }
                }
             }
         }  
     */?> 
     </table>
</div>

</body>