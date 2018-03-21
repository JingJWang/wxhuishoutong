<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1" >
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
<title>回收通</title>
<link rel="stylesheet" type="text/css" href="/static/task/task_two/css/style-02.css?vs=1000"/>
<script src="/static/home/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" charset='utf-8' src="/static/task/task_two/js/public.js"></script>
<script src="/static/home/js/ajax_common.js"></script>
<script src="/static/task/js/task_list.js"></script>
<script type="text/javascript">
$(function(){
  var htmlDecode = function(str) {
      return str.replace(/&#(x)?([^&]{1,5});?/g,function($,$1,$2) {
          return String.fromCharCode(parseInt($2 , $1 ? 16:10));
      });
  };
  $('.instruction_content').html(htmlDecode("<?php echo $task_type['0']['process']; ?>"));
})
</script>
</head>

<body>
        <!-- load start-->
		<div id="caseBlanche">
			<div id="rond">
				<div id="test"></div>
			</div>
		</div>
		<!-- load end-->
		<!-- grayBg start-->
		<div class="grayBg"></div>
		<!-- grayBg end-->
		<!-- rwdSelect start-->
		<div class="rwdSelect">
			<i class="closegreen" onclick="rwdSelClose()"></i>
			<h3>选择奖励</h3>
			<ul id="awards">
			</ul>
			<?php if (isset( $getonetask['0']['taskid'])) { ?>
    			<span class="isyes" onclick="commit(<?php echo $getonetask['0']['taskid'] ?>)">确 认</span>
			<?php } ?>
		</div>
		<!-- rwdSelect end-->
		<!-- rwdSuccess start-->
		<div class="rwdSuccess">
			<i class="close" onclick="rwdSucClose()"></i>
			<div class="topcon">
				<h3 class="title"></h3>
				<p class="reawards">恭喜您获得 15通花+15点 成长值！</p>
				<!-- <a href="javascript:;">去通花商城换购礼品</a> -->
				<!-- <span>今天还可以领8个红包，继续努力吧！</span> -->
			</div>
			<div class="botcon">
				<a href="/view/shop/list.html"></a>
			</div>
		</div>
		<!-- rwdSuccess end-->
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

<?php if (isset($getonetask[0]['get_rewards'])&&!empty($getonetask[0]['get_rewards'])) { ?>
	<a onclick="getaward(<?php echo $getonetask['0']['taskid'] ?>)" class="itBut">领取奖励</a>
<?php }elseif ($getonetask[0]['task_type']==3||$getonetask['0']['task_id']==6) { ?>
	<a href="#" class="itBut qushe">未完成</a>
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

<div class="tsTing">
<div class="tsTit">任务流程</div>
<div style="margin:20px 0px;" class = "instruction_content">
 
</div>
<!-- <p><img src="/static/task/task_two/img/ddf_01.jpg"></p> -->
<!-- <p><img src="/static/task/task_two/img/ddf_02.jpg"></p> -->
</div>

<?php if ($getonetask[0]['task_type']==3){ ?>
	<div class="list-v2 m10">
	<h2>请选择您要宣传的文章</h2>
	<ul>
		<?php foreach ($taskIntroduction as $k => $v) { ?>
		<li><a href="<?php echo $v['share_url']; ?>"><?php echo $v['instruction_name']; ?></a><!-- <p>转发320次</p> --></li>
	    <?php } ?>
	</ul>
	</div>
<?php } ?>
<?php if ($getonetask[0]['task_type']!=3&&!isset($getonetask[0]['get_rewards'])&&empty($getonetask[0]['get_rewards'])) { ?>
	<div class="txBut"><a href="<?php echo $getonetask[0]['url']; ?>" style = 'color:#fff'><button id="but-06"><?php echo $getonetask[0]['process_name']; ?></button></a></div>
<?php } ?>	

</body>
</html>
