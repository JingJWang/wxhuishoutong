<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
		<title>我的奖金</title>
		<link rel="stylesheet" href="../../../static/gold/css/public.css" />
		<link rel="stylesheet" href="../../../static/welfare/css/myBonus.css" />
	</head>
	<body>
		<header>
			<a href="javascript:;" class="head_a" onClick="javascript :history.back(-1);">
				<b class="left"></b>
				<p>返回</p>
			</a>
			<h1>我的奖金</h1>
			<a href="javascript:;" class="head_help" onclick="loadFun()">
				<b class="right"></b>
				<p>帮助</p>
			</a>
		</header>
		<div class="first">
			<div class="first_left">
				<a class="contA" onclick="tourl(646)">
					<span></span>
				</a>
			</div>
			<div class="first_center">
				<div class="photo_box">
				<?php if(count($mem)>0){$expire=''; ?>
				 <img class="photo" src="<?php if($mem['0']['img']!='') {echo $mem['0']['img'];}else{echo '../../../static/c/images/default2.jpg';} ?>" />
				 <?php  
				  	if($mem['0']['expire']!=''){
				  		$expire=date("Y-m-d",$mem['0']['expire']);
				  	}else{
				  		$expire='';
				  	}
		    		if($mem['0']['member']==1){?>
					<span class="photoMenter">年费会员</span>
					</div>
					<div class="huiyuan">
						<p class="uName"><?php echo $mem['0']['mobile']; ?></p >
						<b class="VipImg">
						<img src="../../../static/welfare/img/member_vip.png" /></b>
					</div>
					<?php   if(time()>$mem['0']['expire']){?>
					    <p class="first_center_p">会员已到期</p >
					<?php } else{?>
    					<p class="first_center_p">会员到期至
    					<?php echo $expire; ?>
    		    		</p >
				 <?php } }else if($mem['0']['member']==2){ ?>
					<span class="photoMenter">体验会员</span>
					</div>
					<div class="huiyuan">
						<p class="uName"><?php echo $mem['0']['mobile']; ?></p >
						<b class="VipImg">
						<img  src="../../../static/welfare/img/member.png"  /></b>
					</div>
					
					<?php   if(time()>$mem['0']['expire']){?>
					    <p class="first_center_p" style="margin-left:30px;">体验会员已到期</p >
					<?php } else{?>
    					<p class="first_center_p">体验至
    					<?php echo $expire; ?>
    		    		</p >
				 <?php } }else{?>
					</div>
					<div class="huiyuan">
						<p class="uName"><?php echo $mem['0']['mobile']; ?></p >
					</div>
				 <?php }}?>
			</div>
			<div class="first_right">
				<a class="contA" href="/index.php/nonstandard/mybonus/ranking">
					<span></span>
				</a>
			</div>
			<div class="earnings">
			<?php if(count($list)>0){ 
			 foreach($list as $key=>$value){?>
				<p class="earnings_p">
					<span>我的收益</span>
					<span>¥<?php echo $value['sbonus'] ? $value['sbonus']: 0;?></span>
				</p>
				<p class="earnings_p">
					<span>已结算收益</span>
					<span>¥<?php echo $value['ybonus'] ? $value['ybonus']: 0; ?></span>
				</p>
				<p class="earnings_p">
					<span>待结算收益</span>
					<span>¥<?php echo $value['wbonus'] ? $value['wbonus']: 0;?></span>
				</p>
				<?php }	}else{?>
				<p class="earnings_p">
					<span>我的收益</span>
					<span>0</span>
				</p>
				<p class="earnings_p">
					<span>已结算收益</span>
					<span>0</span>
				</p>
				<p class="earnings_p">
					<span>待结算收益</span>
					<span>0</span>
				</p>
				<?php } ?>
			</div>
		</div>
		<!--收益列表-->
		<div class="earnings_list">
			<h1>— 收益列表 —</h1>
			<?php if(count($getList)>0){
				foreach($getList as $key=>$value){
					if($value['source']==2){
			?>
			<div class="earCont">
				<img src="../../../static/welfare/img/earningsL.png">
				<?php }else{?>
			<div class="earCont">
				<img src="../../../static/welfare/img/earningsC.png">
				<?php }?>
				<p class="earContDetails">
					<span class="earContDSpan"><?php 
							if ($value['source']==2){
								echo '<s>'.substr_replace($value['mobile'],'*****',3,5).'</s><s>'.$value['name'].'/'.$value['bidprice'];
							}else{
								if($value['mobile']!='' || !empty($value['mobile']) || $value['mobile']!=null){
									echo '<s>'.substr_replace($value['mobile'],'*****',3,5).'</s><s>'.$value['name'].'/'.$value['reprice'];
								}else{
									if($value['phone']!=''){
										echo '<s>'.substr_replace($value['phone'],'*****',3,5).'</s><s>'.$value['name'].'/'.$value['price']/100;
									}else{
										echo '<s>139*****290</s><s>'.$value['name'].'/'.$value['price']/100;
									}
									
								}
							}
						?>
						元</s>
					</span>
					<span>
						<?php echo date('Y-m-d',$value['dealtime']);?>
					</span>
				</p>
				<p class="earContMoney">+<font><?php echo $value['bonus'];?></font>元</p>
				</div>
			<?php } }else{?>
				<div class="nonesj"><span></span></div>
				<p class="zanw">暂无收益</p>
			<?php }?>
		</div>
		<!--帮助-->
		<div class="posiDiv" style="display: none;">
			<div class="closeDiv">
				<span class="close" onclick="closefun()">关闭</span>
			</div>
			<div class="bz_cont">
				<div class="cont_bz">
					<p class="title_p">1.如何邀请用户？</p>
					<p class="content_p">可通过福利站任务、攻略库文章分享、通花商城商品分享等方式进行邀请用户。</p>
					<p class="title_p">2.怎样获得收益？</p>
					<p class="content_p">您邀请来的用户在通花商城购买商品即可获得收益。</p>
					<p class="title_p">3.收益是怎么算的？</p>
					<p class="content_p">根据商品不同，奖金也是不同的，一般奖金为1%-5%，数码及奢侈品相对较高。</p>
					<p class="title_p">4.在哪查看我的收益？</p>
					<p class="content_p">个人中心-我的奖金 可查看收益及详情。</p>
					<p class="title_p">5.如何获得会员？</p>
					<p class="content_p">可在通花商城中购买,新用户我们将赠送一个月时间的会员、老用户可在微信公众号对应文章中领取一个月时间会员。</p>
					<p class="title_p">6.如何提现？</p>
					<p class="content_p">每天系统会自动结算前一天的收益，结算的收益可在个人中心查看并提现。</p>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
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
		function tourl(id){
			location.href = '/view/shop/info.html?id='+id;
		};
	</script>
	<script src="../../../static/welfare/js/jquery-1.11.1.min.js"></script>
	<script src="../../../static/welfare/js/bangzhu.js"></script>
</html>
