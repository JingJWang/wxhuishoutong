<!DOCTYPE html>
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>回收通</title>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link  type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>common.css"/>
</head>
<body class="bgx">
<div class="top t_c">
回收通
</div>
<div id="content">
<?php foreach ($hellpinfo as $info){
    echo '<div class="hongbaotit"><p>'.$info['instruction_name'].'</p></div>';
    echo '<div class="boxf"><div class="boxwrap">'.$info['instruction_content'].'</div></div>';
} ?>
</div>
</body>
</html>
