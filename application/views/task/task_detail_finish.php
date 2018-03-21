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
<div class="invtDiv">
<div class="itPic">
	<?php switch ($getonetask['0']['task_id']) {
		case '5':
			$main_task_icon = 'game';
			break;
		case '6':
			$main_task_icon = 'guanzhu';
			break;
		default:
			$main_task_icon = 'task_types'.$getonetask['0']['task_type'];
			break;
	} ?>
	<img src="/static/task/task_two/img/<?php echo $main_task_icon.'.jpg'; ?>">
</div>
<div class="itPro"><h3><?php echo $getonetask[0]['info_name']; ?></h3><p>奖励：<?php echo $getonetask[0]['reward_content']; ?></p></div>

<?php if (isset($getonetask['0']['get_rewards'])&&!empty($getonetask[0]['get_rewards'])) { ?>
	<a href="<?php echo $getonetask['0']['reward_url']; ?>" class="itBut">去领奖</a>
<?php }elseif ($getonetask['0']['task_type']==3||$getonetask['0']['task_id']==6) { ?>
	<a href="#" class="itBut qushe">已完成</a>
<?php }else{ ?>
	<a href="<?php echo $getonetask[0]['url']; ?>" class="itBut"><?php echo $getonetask[0]['process_name']; ?></a>
<?php } ?>

<div class="clear"></div>
<div class="list-v2 m10">
<?php if ($getonetask[0]['task_type']==3) { ?>
	<ul>
		<?php foreach ($taskIntroduction as $k => $v) { ?>
		<li><a href="<?php echo $v['share_url']; ?>"><?php echo $v['instruction_name']; ?></a><!-- <p>转发320次</p> --></li>
	    <?php } ?>
	</ul>
<?php }elseif($getonetask['0']['task_id']==6){ ?>
	<img style="width:100%" src="/static/task/images/guangzhu.jpg" alt="" />
<?php } ?>
</div>
</div>

<div class="miaox">
<p><strong>活动说明</strong></p>
<p><?php echo $getonetask[0]['task_content']; ?></p>
</div>

</body>
</html>
