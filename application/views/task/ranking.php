<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1" >
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
<title>回收通</title>
<link rel="stylesheet" type="text/css" href="/static/task/task_two/css/style-02.css"/>
<script type="text/javascript" charset='utf-8' src="/static/task/task_two/js/public.js"></script>
</head>

<body>
<div class="list-v3">
<h2>我的排名</h2>
<ul>
	<li>
        <div class="lbs2"><img src="<?php if ($userinfo['wx_img_face']!=''){echo $userinfo['wx_img_face'];}else{echo '/static/task/images/a.png';} ?>"><!-- <p>梦中佳人</p> --></div>
        <div class="lbs3"><h3><?php echo $userinfo['wx_name']; ?> </h3><p>等级：<?php echo $userinfo['n_level_num']; ?> 　　成长值：<?php echo $userinfo['center_all_integral']; ?></p><p>称号：<?php echo $userinfo['level_name']; ?></p></div>
    </li>
</ul>
</div>

<div class="list-v3">
<h2>用户排行榜</h2>
<ul>
    <?php foreach ($all_user as $k => $v) { ?>
    <li>
        <div class="lbs1"><b><?php echo $k+1 ?></b></div>
        <div class="lbs2">
            <?php if ($v['wx_img_face']!='') { ?>
                 <img src="<?php echo $v['wx_img_face']; ?>">
            <?php }else{ ?>
                 <img src="/static/task/images/a.png">
            <?php } ?>
        </div>
        <div class="lbs3"><h3><?php echo $v['wx_name'] ?></h3><p>等级：<?php echo $v['level_num'] ?>　　成长值：<?php echo $v['center_all_integral'] ?></p><p>称号：<?php echo $v['level_name']; ?></p></div>
    </li>
    <?php } ?>
</ul>
</div>
</body>
</html>
