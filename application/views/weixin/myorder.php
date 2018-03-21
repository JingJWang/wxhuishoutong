<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>回收通</title>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta name="format-detection" content="telephone=no"/>
<link type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>common.css?v=2000102"/>
</head>
<body class="bgf">
<div class="top t_c">
回收通
</div>
<div class="wrap" style="width:auto;min-width:300px;padding-top:0px;">
			<?php
					if($nodeal !='0'){ 
						foreach($nodeal as $data){?>
								<div class="box">
								<div class="dtit"><p class="dingdan">订单编号:<?php echo $data['order_randid'];?></p><p class="chenggong">等待交易</p></div>
								<div class="jiuyi"><p><?php echo $standard_product[$data['order_type']]; ?></p><span class="f_normal">数量：
								<?php echo $ordertype_clothes[$data['order_num']]; ?></span></div>
								<div class="baodan">报单日期：<?php echo $data['order_joindate'];?> </div>
							   </div>
													 
						<?php }
					}
					if($deal!='0'){
    				    foreach($deal as $data){  ?>
    				    		<div class="box">
    				    			<div class="dtit">
    				    			<p class="dingdan"><a href="ordersuccess.php?id=<?php echo $data['id'];?>">订单编号:<?php echo $data['order_randid'];?></a></p><p class="chenggong"><img src="/static/weixin/public/img/yichengjiao.png"/></p></div>
    				    			<div class="jiuyi"><p><?php echo $standard_product[$data['order_type']]; ?></p><div class="gongjin"><span>公斤：<?php echo $data['order_weight'];  ?>kg</span><span>金额：<?php echo $data['order_pic'];  ?>元</span></div></div>
    				    			<div class="baodan">报单日期：<?php echo $data['order_joindate'];?><a href="/index.php/weixin/order/lookorder/<?php echo $data['id'];?>">分享订单</a></div>
    				    		</div>					    				 
    				        <?php } 
					   }
				    ?>
						
</div>
</body>
</html>
