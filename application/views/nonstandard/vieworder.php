<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通订单详情</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css"/></head>
	<body>
		<!--  gray start    -->
		<div class="odGray">
			<div class="towBid">
				<p>已成功确认，等待回收商完成订单</p>
				<a href="javascript:;">确 认</a>
			</div>
		</div>
		<!--  gray end      -->
		<!--  green start      -->
		<div class="green">
			<div class="back">
				<p>订单详情</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end      -->
         <div class="afficle" style="z-index:100;">
               <div class="contet">成交价50元以上包邮。钱先到账、快递回收覆盖全国</div>
         </div>
         <div style="width:100%;height:30px;"><div>
		<!--  phoneDetail start    -->
		<div class="phoneDetail" style="margin-top:30px;">
		    <i class="orderState"><?php
                        switch ($order['order_orderstatus']){
                            case '-2':
                                echo '等待补充完整!';
                                break;
                            case '-1':
                                echo '订单被取消';
                                break;
                            case '1':
                                echo '等待报价中';
                                break;
                            case '2':
                                echo '等待回收商预支付';
                                break;
                            case '3':
                                echo '请发货';
                                break;
                            case '10':
                                echo '交易完成';
                                break;
                        }
            ?></i>
			<span class="code">编号：<span class="codeNo"><?php echo $order['order_number']; ?></span></span>
			<p class="phoneBrand"><?php echo $order['order_name']; ?></p>
			<div class="bot clearfix">
				<!-- <span class="posi fl"><?php echo $order['order_province'],$order['order_city'],
            $order['order_county'],$order['order_residential_quarters']; ?></span>-->
				<span class="timer fr" style="float:left"><?php echo date('Y-m-d H:i:s',$order['jointime']); ?></span>
			</div>
		</div>
		<!--  phoneDetail end  -->
		<?php if(!empty($quote)){ ?>
		<!-- ** bidInfo start (报价中/报价结束状态 )  如果报价为0人时该项隐藏  -->
		<div class="bidInfo" style="">
			<h3>报价信息</h3>
			<div class="bidList">
				<ul>
				<?php 
                    foreach ($quote as $k=>$v){ ?>
					<li>
						<a href="/index.php/nonstandard/quote/ViewQuote?id=<?php echo $order['order_number']; ?>" class="clearfix">
							<p class="busiman fl"><?php echo in_array($v['number'],$this->config->item('js_cooplist'))?'寄售商':'回收商';?> <span class="name"><?php echo  mb_substr($v['name'],0,1).'师傅'?></span></p>
							<p class="money fr">报价金额<span class="number"><?php echo $v['price']; ?>元</span></p>
						</a>
					</li>
					<?php  } ?>
				</ul>
			</div>
		</div>
		<!--  bidInfo end      -->
		<?php } ?>
		<?php  if(!empty($offer)){ ?>
		<!-- ** bidInfo start (待交易状态 )  如果报价为0人时该项隐藏  -->
		<div class="tradeBid" style="">
			<h3>回收商信息</h3>
			<div class="tradeInfo">
				<div class="basic">
					<p class="name"><?php echo  mb_substr($offer['0']['name'],0,1).'师傅'?><span><?php
                        switch ($offer['0']['auth']){
                            case '0':echo '未认证';break;
                            case '1':echo '个人认证';break;
                            case '2':echo '企业认证';break;
                            case '3':echo '保证金认证';break;
                            case '4':echo '个人+保证金';break;
                            case '5':echo '企业+保证金';break;
                        }
					?></span></p>
					<p class="posi"><?php echo $offer['0']['address'];?></p>
					<p class="number clearfix">
						<i class="line"></i>
						<span class="phoneNo fl"><i></i><?php echo '<a href="tel:'.$offer["0"]["mobile"].'">'.$offer["0"]["mobile"].'</a>';?></span>
						<span class="weiChat fl"><i></i><?php echo  empty($offer['0']['wx'])? '未绑定' : $offer['0']['wx'] ;?></span>
					</p>
					<a class="rule" href="javascript:;">
					    <span>免邮规则</span>
					</a>
				</div>
				<?php if($order['order_orderstatus'] == 2){?>
				   <div class="prePay">
					<p class="pre fl">预支付 <span class="preNo"><?php echo $offer['0']['price'];?>元</span></p>
					<p class="money fr">报价金额<span class="moneyNo"><?php echo $offer['0']['price'];?>元</span></p>
				</div>
				<?php if (isset($couponInfo)) { ?>
					<div class="seize">
	                    <div class="hints">已使用<?php echo $couponInfo['names'] ?>	，交易完成后增值券将发放到余额</div>
	                </div>
				<?php } ?>
                
				<?php } ?>
				<?php if($order['order_orderstatus'] == 3){?>
				   <div class="prePay">
					<p class="pre fl">已收到预支付金额 <span class="preNo"><?php echo $offer['0']['price'];?>元</span></p>
					<p class="money fr"><a href="/index.php/nonstandard/center/ViewCenter">点此去查看</a><span class="moneyNo"></span></p>
				</div>
				<?php } ?>
				<!-- ** changeBid start  有二次报价时该项显示-->
				<?php 
				    if(!empty($offer['0']['second'])){
				?>
				<div class="changeBid">
					<p class="fl">回收商修改报价为 <span><?php echo $offer['0']['second'];?>元</span></p>
					<p class="state fr"><?php echo empty($offer['0']['isagree'])?'未确认':'已确认'; ?></p>
				</div>
					<?php if (isset($couponInfo)) { ?>
					<div class="seize">
	                     <div class="hints">可用<?php echo $couponInfo['names'] ?></div>
	                </div>
	                <?php } ?>
				<?php } ?>
				<!--  changeBid end  -->
			</div>
		</div>
		<!--  bidInfo end      -->
		<?php } ?>
		<!--  proInfo start  -->
		<div class="proInfo">
			<h3>产品信息</h3>
			<div class="porList">
				<ul>
				 <?php   
                    if(!empty($order['electronic_oather'])){
                        $attr=json_decode($order['electronic_oather'],true);
                        unset($attr['proname']);unset($attr['braname']);unset($attr['typename']);
                       
                        foreach ($attr as $key=>$val){
                            if(array_key_exists($key,$attrname)){
                                if($attrname[$key] != 'oather'){
                        ?>
                               <li>
                                    <span><?php echo $attrname[$key];?>：</span>
        						    <span><?php echo $val;?></span>
        					   </li>
                                <?php }else{ ?>
        						<li class="clearfix other">
        						      <span class="fl"><?php echo $attrname[$key];?>：</span>
        						      <span class="fl othercon"><?php echo $val;?></span>
        					   </li>
                                <?php }
                                }
                        }
                    }
                    ?> 
        <?php 
		  if($order['order_orderstatus'] == 1 || $order['order_orderstatus'] == 2 || $order['order_orderstatus'] == 3){
		?>
		<li>
			<span style="background: #DCDCDC;width:30%;height:25px;text-align:center;line-height:25px;">
			 <a href="/index.php/nonstandard/order/Viewcancel?oid=<?php echo $order['order_number']; ?>">取消交易</a>
		    </span>
		</li>
		<?php } ?>
				</ul>
			</div>
		</div>
		<!--  proInfo end      -->
		<!-- ** cancel start (待交易状态 )  如果报价为0人时该项隐藏  -->
		<?php 
		  if($order['order_orderstatus'] == 1 || $order['order_orderstatus'] == 2 ){
		?>
		<div class="cancelTrasic">
			<a href="/index.php/nonstandard/system/welcome">关闭</a>
		</div>
		<?php } ?>
		<?php 
		  if($order['order_orderstatus'] == 3 && !empty($offer['0']['isagree']) ){
		?>
		<div class="cancelTrasic">
			<a href="/index.php/nonstandard/system/welcome">关闭</a>
		</div>
		<?php } ?>		
		<!--  cancel end      -->
		<!--  botBtn start  -->
		<?php 
		  if(!empty($offer) && !empty($offer['0']['second']) && empty($offer['0']['isagree'])){
		?>
		<div class="botBtn clearfix">
			<!--<a href="/index.php/nonstandard/order/Viewcancel?oid=<?php echo $order['order_number']; ?>" class="reSelect">取消交易</a>  -->
			<a href="javascript:;" class="startTra" onclick="confirm('<?php echo $order['order_number']; ?>');">确认第二次报价</a>
		</div>
		<?php } ?>
		<!--  botBtn end  -->
		<div class="shade"></div>
        	<div class="regulation">
        		<div class="title">
                    快递免邮规则
                    <a class="closeBtn" href="javascript:;">×</a>
        		</div>
        		<div class="content">
        		    <div class="written">
                        <p>1.由于描述不符导致交易失败需要退回物品时，来去物流费用均由卖方承担</p>
                        <p>2.由于描述不符导致物品最终成交价格低于包邮价格时，无论是否继续交易，产生的物流费用均由卖方承担</p>
                        <p>3.寄售方自定义寄售价格，交易过程中产生的物流费用均由寄售发起人承担</p>
        		    </div>
        		    <div class="aware" align="center">
                         <a class="realize" href="javascript:;">我知道了</a>
                    </div>
                </div>
        	</div>
	</body>
	<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
	<script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
	<script type="text/javascript" src="../../../static/home/js/ajax_vieworder.js"></script>
</html>
