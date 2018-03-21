<!DOCTYPE html>
<html>
<head>
<title><?php echo $this->lang->line('notopenwx_title'),'--',$this->lang->line('common_pagetitle');?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link   rel="stylesheet" type="text/css" href="<?php echo $this->config->item('CSSPATH'); ?>common.css">
</head>
<body>
<div class="top t_c">
回收通
</div>
    <div style="weight:100%;text-align:center;font-size:16px;color:red;margin-top:20px;"><?php if(isset($messageinfo)){ echo $messageinfo; }else{}?></div>
</body>
</html>