<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>个人中心</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/personalCenter.css?v=201704001"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/footer.css"/>
		<script src="../../../static/gold/js/jquery-1.11.1.min.js"></script>
		<script src="../../../static/user/js/center.js"></script>
	</head>
	<body>
		<!--  green start      -->
		<div class="green">
			<div class="back">
				<a href="javascript:;" onclick="javascript:history.back(-1);">返回</a>
			</div>
			<a href="/index.php/nonstandard/wxuser/ViewUser" class="info clearfix">
				<div class="hd fl">
					<span class="photo"><?php echo '<img src="'.$_SESSION['userinfo']['user_img'].'" width="68px" height="68px"/>'; ?></span>
				</div>
				<div class="infomat fl">
					<p class="name"><?php echo $_SESSION['userinfo']['user_name']; ?></p>
					<span class="number"><?php echo $_SESSION['userinfo']['user_mobile']; ?></span>
				</div>
				<span class="next fr"></span>
			</a>
		</div>
		<!--  green end     -->
				<!--  money start   -->
		<div class="cashPage clearfix">
			<p class="fl">账户余额 :<span><?php echo $balance; ?><i>(元)</i></span></p>
			<!--添加充值按钮-->
			<!-- <button class="newRecharge">充值</button> -->
			<a onclick="return Verifi();"  href="<?php if ($iswx) { echo '/index.php/nonstandard/wxuser/ExtractView'; }else{echo '/index.php/nonstandard/wxuser/zfbExtractView';} ?>" class="fr">余额提现</a>
		</div>
		<div class="money clearfix">
			<div class="left fl">
				<p class="red"><?php echo sprintf("%.2f",$freeze_balance); ?><span></span></p>
				<a>已收到预支付</a> 
			</div>
			<div class="cen fl">
				<p class=""><?php  echo $fund; ?></p>
				<a>我的环保基金</a>
			</div>
			<div class="right fl">
				<p class=""><?php echo $integral; ?></p>
				<a>我的通花</a>
			</div>
			<span class="lineleft"></span>
			<span class="lineright"></span>
		</div>
		<!--  money end      -->
		<!--入口-->
		<div class="entrance">
			<a href="javascript:;" onclick="tourl(646)"></a>
		</div><!--跳转到购买页面tourl(646)-->
		<!--我的贵金属-->
		<div class="goldDeal">
			<a href="../../../view/gold/metaldeal.html" class="goldDealClassify_a">我的贵金属</a>
			<a href="../../../view/gold/metaldeal.html" class="goldDealClassify">
				<span>
					<p><?php echo empty($metal['gold']) ? 0 : $metal['gold'] ;?>g</p><p>黄金</p>
				</span>
				<span>
					<p><?php echo empty($metal['platinum']) ? 0 : $metal['platinum'] ;?>g</p><p>铂金</p>
				</span>
				<span>
					<p><?php echo empty($metal['silver']) ? 0 : $metal['silver'] ;?>g</p><p>白银</p>
				</span>
			</a>
		</div>
		<!--  goods start      -->
		<!--<div class="goods">
            			<a href="/index.php/nonstandard/order/ViewOrder?status=n" class="clearfix" style="display: block;position:relative;">
            				<p class="picTitle fl">回收订单</p>
            				<span class="check"></span>
            			</a>
            	</div>-->
		<div class="part01 part00 goods">
                			<div class="flour clearfix">
                				<span class="pic fl"></span>
                				<div class="right fl">
                					<a href="/index.php/nonstandard/order/ViewOrder?status=n"  class="clearfix">
                						<p class="picTitle fl">我的物品</p>
                						<span class="check"></span>
                					</a>
                				</div>
                			</div>
                		</div>
        <div class="part01 part00 shop">
        			<div class="flour clearfix">
        				<span class="pic fl"></span>
        				<div class="right fl">
        					<a href="/view/shop/record.html"  class="clearfix">
        						<p class="picTitle fl">商城订单</p>
        						<span class="check"></span>
        					</a>
        				</div>
        			</div>
        		</div>
        <!--手机维修-->	
        <div class="part01 part00 repairp">
        			<div class="flour clearfix">
        				<span class="pic fl"></span>
        				<div class="right fl">
        					<a href="/view/repair/repairform.html"  class="clearfix">
        						<p class="picTitle fl">维修订单</p>
        						<span class="check"></span>
        					</a>
        				</div>
        			</div>
        		</div>
		<!--  goods end      -->
		<!--  part01 start      -->
		<div class="part01 part00 evalute">
			<div class="flour clearfix">
				<span class="pic fl"></span>
				<div class="right fl">
					<a href="/index.php/nonstandard/wxuser/EvalLIst"  class="clearfix">
						<p class="picTitle fl">我的评价</p>
						<span class="check"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="part01 part00 voucher">
                         <div class="flour clearfix">
                             <span class="pic fl"></span>
                             <div class="right fl">
                                 <a href="/view/coupon/market.html"  class="clearfix">
                                	 <p class="picTitle fl">增值卡劵</p>
                                	 <span class="check"></span>
                                 </a>
                             </div>
                         </div>
                    </div>

		<!--  part01 end      -->	
		<!--  part01 start      -->
		<div class="part01">
			<div class="flour clearfix">
				<span class="pic fl"></span>
				<div class="right fl">
					<a href="/view/shop/list.html" class="clearfix">
						<p class="picTitle fl">通花商城</p>
						<span class="check"></span>
					</a>
				</div>
			</div>
			<!--我的奖金-->
			  <div class="flour invite clearfix">
				<span class="pic newPic fl"></span>
				<div class="right fl">
					<a href="/index.php/nonstandard/mybonus/mybonusList" class="clearfix">
						<p class="picTitle fl">我的奖金</p>
						<span class="check"></span>
					</a>
				</div>
			</div> 
			<div class="flour invite clearfix">
				<span class="pic fl"></span>
				<div class="right fl">
					<a href="/index.php/task/usercenter/taskcenter" class="clearfix">
						<p class="picTitle fl">邀请领奖</p>
						<span class="check"></span>
					</a>
				</div>
			</div>
		</div>
		<!--  part01 end      -->	
		<!--  part02 start      -->
		<div class="part01 part02">
			<div class="flour clearfix">
				<span class="pic fl"></span>
				<a  href="http://www.xingdongliu.com/h5preview/U388fb4f76c1f4d27e90baa243d52b95ce8c27e03?from=singlemessage&isappinstalled=0" class="right fl clearfix">
					<p class="picTitle fl">加盟合作</p>
					<span class="check"></span>
				</a>
			</div>
			<div class="flour invite clearfix">
				<span class="pic fl"></span>
				<a href="/view/m/dowload.html" class="right fl clearfix">
					<p class="picTitle fl">下载回收商App</p>
					<span class="check"></span>
				</a>
			</div>	
		</div>
		<!--  part02 end      -->		
		<!--  part03 start      -->
		<div class="part01 part02 part03">
			<div class="flour clearfix">
				<span class="pic fl"></span>
				<div class="right fl">
					<a href="http://s.70c.com/w/FAAWJYY-CAJACWF?s=Test" class="clearfix">
						<p class="picTitle fl">帮助</p>
						<span class="check"></span>
					</a>
				</div>
			</div>	
			<div class="flour invite clearfix">
				<span class="pic fl"></span>
				<div class="right fl">
					<a href="/view/mobile/about.html" class="clearfix">
						<p class="picTitle fl">关于我们</p>
						<span class="check"></span>
					</a>
				</div>
			</div>	
			<div class="flour link  clearfix">
				<span class="pic fl"></span>
				<div class="right fl clearfix">
					<p class="picTitle fl">联系我们</p>
					<p class="fr ser"><span>客服电话:</span>400-641-5080</p>
				</div>
			</div>	
		</div>
		<a class="quit" href="/index.php/nonstandard/center/loginout">退出登录</a>
		<!--  part03 end      -->	
		<div class="bottomNav">
			<div class="select">
				<a href="/index.php/nonstandard/system/welcome">回收/寄售</a>
    			<a href="/index.php/task/usercenter/taskcenter">福利</a>
    			<a href="/view/shop/list.html">商城</a>
    			<a href="/index.php/nonstandard/center/ViewCenter" class="select04">我的</a>
			</div>
		</div>
		<script>
			function tourl(id){
				var an = $('.left .sn').attr("data-id");
				var a = $('.right').scrollTop();
				window.history.pushState(null, null, '/view/shop/list.html?type='+an+'_'+a);//修改url
				location.href = '/view/shop/info.html?id='+id;
			};
		</script>
		<script src="../../../static/user/js/center.js"></script>
	</body>
</html>
