<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title>回收通</title>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<link   type="text/css" rel="stylesheet" href="/static/weixin/public/css/common.css" />
</head>
<body>
<div style="height:50px;font-size:30px;line-height:50px;font-weight:bold;background:#078b09;color:#fff;text-align:center;margin-top:-20px;">
回收通
</div>
<?php
if($freezeinfo != ''){
    echo '<a style="text-align: center;" class="help" href="/index.php/weixin/voucher/voucherinfo">'.$freezeinfo.'</a>';
}
?>
<a style="text-align: center;" class="help" href="/index.php/weixin/voucher/voucherinfo">亲！请留好现金券，以后活动还能使用哦~</a>
<a style="text-align: center;" class="help" href="/index.php/weixin/voucher/voucherinfo">现金券使用帮助</a>
<div class="wrap" style="width: 300px;margin: 0 auto;padding: 0px 0 20px 0;">
<?php 
if( $notused != '0'){
    foreach($notused as $data){
    		switch($data['log_type']){ 
    				case '1':?>
    					 <div class="dyellow daijin">
    					<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
    					<p class="stit color_w">来源:关注</p>
    					<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
    					<?php if($data['log_voucher_status']==-1){?>
    					<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
    					<?php }elseif($data['log_voucher_status']==1){?>
    					<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
    					<?php }else{?>
    					<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
    					<?php }?>
    					</div>
    				<?php
    					break;
    				case '2':?>
    					<div class="dgreen daijin margint20">
    					<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
    					<p class="stit color_w">来源:首次报单</p>
    					<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
    					<?php if($data['log_voucher_status']==-1){?>
    					<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
    					<?php }elseif($data['log_voucher_status']==1){?>
    					<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
    					<?php }else{?>
    					<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
    					<?php }?>
    					</div>
    				<?php
    					break;
    				case '3':?>
    					<div class="dblue daijin">
    					<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
    					<p class="stit color_w">来源:分享</p>
    					<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
    					<?php if($data['log_voucher_status']==-1){?>
    					<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
    					<?php }elseif($data['log_voucher_status']==1){?>
    					<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
    					<?php }else{?>
    					<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
    					<?php }?>
    					</div>
    				<?php
    					break;
    				case '4':?>
    						<div class="dred daijin">
    						<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
    						<p class="stit color_w">来源:分享</p>
    						<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
    						<?php if($data['log_voucher_status']==-1){?>
    						<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
    						<?php }elseif($data['log_voucher_status']==1){?>
    						<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
    						<?php }else{?>
    						<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
    						<?php }?>
    						</div>
    					<?php
    					break;
    		}
    } 
}else{
	
}
if( $use != '0'){
foreach($use as $data){
    switch($data['log_type']){
				case '1':?>
					 <div class="dyellow daijin">
					<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
					<p class="stit color_w">来源:关注</p>
					<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
					<?php if($data['log_voucher_status']==-1){?>
					<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
					<?php }elseif($data['log_voucher_status']==1){?>
					<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
					<?php }else{?>
					<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
					<?php }?>
					</div>
				<?php
					break;
				case '2':?>
					<div class="dgreen daijin margint20">
					<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
					<p class="stit color_w">来源:首次报单</p>
					<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
					<?php if($data['log_voucher_status']==-1){?>
					<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
					<?php }elseif($data['log_voucher_status']==1){?>
					<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
					<?php }else{?>
					<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
					<?php }?>
					</div>
				<?php
					break;
				case '3':?>
					<div class="dblue daijin">
					<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
					<p class="stit color_w">来源:分享</p>
					<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
					<?php if($data['log_voucher_status']==-1){?>
					<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
					<?php }elseif($data['log_voucher_status']==1){?>
					<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
					<?php }else{?>
					<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
					<?php }?>
					</div>
				<?php
					break;
				case '4':?>
						<div class="dred daijin">
						<p class="btit color_w"><?php echo $data['voucher_pic']?>元现金券</p>
						<p class="stit color_w">来源:分享</p>
						<p class="stit color_w">有效期至：<?php echo $data['log_exceed']?></p>
						<?php if($data['log_voucher_status']==-1){?>
						<img src="/static/weixin/public/img/guoqi.png" alt="已使用" title="已过期" />
						<?php }elseif($data['log_voucher_status']==1){?>
						<img src="/static/weixin/public/img/weishiyong.png" alt="已使用" title="未使用" />
						<?php }else{?>
						<img src="/static/weixin/public/img/shiyong.png" alt="已使用" title="已使用" />
						<?php }?>
						</div>
					<?php
					break;
		  }
    } 
}
?>
</div>
</body>
</html>
