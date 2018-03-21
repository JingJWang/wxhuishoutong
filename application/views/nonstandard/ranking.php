<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
		<title>排行榜</title>
		<link rel="stylesheet" href="../../../static/gold/css/public.css" />
		<link rel="stylesheet" href="../../../static/welfare/css/ranking.css" />
	</head>
	<body>
		<header>
			<a href="javascript:;" class="head_a" onClick="javascript:history.back(-1);">
				<b class="left"></b>
				<p>返回</p>
			</a>
			<h1>排行榜</h1>
			<a href="javascript:;" class="head_help" onclick="myHover()">
				<p>奖励规则</p>
			</a>
		</header>
		<div class="myRanking">
			<h1 class="ranking_h1">我的排名</h1>
			<div class="rankingCont">
			<?php 
			$a=0;
			if(count($list)>0){
				foreach ($list as $k=>$v){
					if (in_array($_SESSION['userinfo']['user_mobile'],$v)){
						$a++;
			?>
				<div class="rankingCont_photo"><img  <?php if(isset($v['img']) && $v['img']!=''){ echo 'src='.$v['img'];}else{ ?>src="../../../static/c/images/default2.jpg"<?php }?> /></div>
				<div class="rankingContDiv">
					<p><?php echo $v['mobile']?></p>
					<p>上周收益  ¥<?php echo $v['sbonus']?$v['sbonus']:0; ?></p>
					<p>上周排名&nbsp;&nbsp;<?php  echo '第'.intval($k+1).'名'; ?><!-- (上榜人数20人) -->
			  	  	</p>
				</div>
				<?php break;}}}
				if ($a==0){?>
				<div class="rankingCont_photo">	<img src="../../../static/c/images/default2.jpg" /></div>
					<div class="rankingContDiv">
						<p><?php echo $_SESSION['userinfo']['user_mobile']?></p>
						<p>上周收益  ¥0</p>
						<p>未上榜 (上榜人数20人)</p>
					</div>	
			<?php }?>
			</div>
		</div>
		<div class="userRanking">
			<h1 class="ranking_h1">用户排行榜</h1>
			<!--第一名-->
			  <?php 
			  	if(count($getrank)>0){
			  	  foreach($getrank as $k=>$v){
			  	  if($k==0){
			  ?>
			  	<div class="userRank">
				<span class="userRank_span one_s"></span>
				<?php }elseif($k==1){?>
				<!--第二名-->
				<div class="userRank">
				<span class="userRank_span two_s"></span>
				<?php }elseif($k==2){?>
				<!--第三名-->
				<div class="userRank">
				<span class="userRank_span three_s"></span>
				<?php }elseif($k>=3){?>
				<!--第四名-->
				<div class="userRank">
				<span class="userRank_span_other"><?php echo $k+1; ?></span>
				<?php } ?>
				<img <?php if($v['img']!=''){ echo 'src='.$v['img'];}else {echo 'src="../../../static/c/images/default2.jpg" ';}?> />
				<div>
					<p class="Rank_userName"><?php echo substr_replace($v['mobile'],'*****',4,5)?></p>
					<p class="Rank_userMoney">上周结算收益  ¥<?php echo $v['ybonus']?></p>
				</div>
				<?php if($k==0){?>
				<p class="userRank_a one_a">获得奖励300元</p>
				<?php }elseif($k==1){?>
				<p class="userRank_a two_a">获得奖励200元</p>
				<?php }elseif($k==2){?>
				<p class="userRank_a three_a">获得奖励100元</p>
				<?php } ?>
			</div>
			<?php }}else{ echo '<div class="userRank">
				<p style="text-align:center">暂无排行榜</p>
						</div>';} ?>
		</div>
		<!--奖励规则-->
		<div class="rule_box" style="display: none;">
			<div class="rule">
				<h1>奖励规则</h1>
				<p>排行榜为周一更新，以周收益排名排行榜仅显示前20名</p>
				<p>每周一系统自动发放前三名奖励</p>
				<p>奖励：</p>
				<p class="jinE">第一名：300元</p>
				<p class="jinE">第二名：200元</p>
				<p class="jinE">第三名：100元</p>
				<span class="iNow">我知道了</span>
			</div>	
		</div>
	</body>
	<script>
		!function(win) {
		    function resize() {
		        var domWidth = domEle.getBoundingClientRect().width;
		        if(domWidth / v > 540){
		            domWidth = 540 * v;
		        }
		        win.rem = domWidth / 16;
		        domEle.style.fontSize = win.rem + "px";
		    }
		    var v, initial_scale, timeCode, dom = win.document, domEle = dom.documentElement, viewport = dom.querySelector('meta[name="viewport"]'), flexible = dom.querySelector('meta[name="flexible"]');
		    resize();
		}(window);
	</script>
	<script src="../../../static/welfare/js/jquery-1.11.1.min.js" defer="defer"></script>
	<script type="text/javascript">
		function myHover(){
			$('.rule_box').css('display','block');
			var ruleH = $(window).height();
			$('.rule_box').height(ruleH);
			$('.iNow').click(function(){
				$('.rule_box').css('display','none');
			});
		};
	</script>
</html>