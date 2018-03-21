<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->lang->line('common_pagetitle');?></title>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>common.css">
</head>
<body class="bgh">
<div class="top t_c">
<?php echo $this->lang->line('common_pagetitle');?>
</div>
<div class="wrap">
<p class="t_c">
<?php echo $submitinfo;?>
</p>
<p class="margint10 t_c"><?php echo $this->lang->line('orderright_content0');?></p>
<p class="margint50 t_c"><font style="color:blue;"><?php echo $this->lang->line('orderright_content1');?></font></p>
<p class="margint50 t_c"><font style="color:blue;"><?php echo $this->lang->line('orderright_content2');?></font></p>
<p class="margint50 t_c"><?php echo $this->lang->line('orderright_content3');?></p>
<p class="margint50 size20"><?php echo $this->lang->line('orderright_address_list');?><br/><br/></p>
<div id="content">
</div>
<p class="t_c">
<input class="guanzhu" value="关 闭" type="button" onclick="WeixinJSBridge.call('closeWindow');"/>
</p>
</div>
</body>
</html>
