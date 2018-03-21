<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通报价详情</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css?v=20160706"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css?v=20160707"/>
	</head>
	<body>
		<!--  green start  -->
		<div class="green">
			<div class="back">
				<p>报价详情</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end  -->
		<!--  tip start  -->
		<div class="tip">
			<p>认证商家更有保障，寄售价更高</p>
		</div>
		<!--  tip start  -->
        <!--  estimate start  -->
		<div class="estimate">
			<?php if(in_array($offer['0']['number'],$this->config->item('js_cooplist'))){ ?>
			<!--  start寄售商信息  -->
			<div class="keynote">
			    <div class="quality" align="center" style="font-size:16px;color: #F3621F;">
			         寄售优势
			    </div>
                <div class="quality" align="center">
                    <span class="dot" style="margin-right:10px;">我们帮您卖</span>
                    <span class="dot">价格您说了算</span>
                </div>
                <div class="quality" align="center">
                    <span class="dot" style="margin-right:10px;">客户多,卖得快</span>
                    <span class="dot">帮您验机促成交</span>
                </div>
                <div class="quality" align="center">
                    <span class="dot" style="margin-right:10px;">钱先到您账户,零风险</span>
                    <span class="dot">按参考价寄售,包邮</span>
                </div>


            </div>
            <div class="caption">
            	<p class="first">寄售服务说明</span>
            	<p>&bull;寄售并非回收，寄售期为15天</p>
            	<p>&bull;寄售完成后收取10%手续费，并给您结算余款</p>
            	<p>&bull;寄售只能接受可以正常使用的数码产品，假货山寨有故障均不可寄售</p>
            	<p>&bull;您定的寄售价高于参考价，来回邮费自理</p>
            	<p>&bull;数码产品只会贬值，过长时间未成交参考价会下降，具体以市场为准</p>
            </div>
             <?php  }  ?>
            <!--  end寄售商信息  -->
		</div>

		<?php if(!in_array($offer['0']['number'],$this->config->item('js_cooplist'))){ ?>
		<div class="message">
            <div class="headline">
                <div class="serial fl">
                    <span>订单编号</span>
                    <span class="num"><?php echo $offer['0']['order_id'] ?></span>
                </div>
                <div class="model fr"><?php echo $offer['0']['offer_order_name'] ?></div>
            </div>
            <div class="substance">
                <div class="reveal">
                    <a class="rule fr" href="javascript:;"><span>免邮规则</span></a>
                    <div class="price">
                        <div class="cue">预估价格</div>
                        <div class="prix">
                            <div class="rate fl">
                                ￥<?php echo $offer['0']['offer_price'] ?>
                                <span>元</span>
                            </div>
                            <div class="region fl">
                            <?php if(empty($coup)){ ?>
                                <div class="sign fl"></div>
                                <div class=""></div>
                            <?php }else{?>
                                 <div class="sign fl">+</div>
                                 <div class="<?php echo $coup; ?>"></div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rest">
                    <div class="range fl"><?php echo $offer['0']['offer_distance'] ?>km</div>
                    <div class="serve fl">
                        <span>快递包邮</span>
                        <span>上门回收</span>
                    </div>
                </div>
            </div>
		</div>
		<?php } ?>
		<!--  estimate start  -->
		<!--  busiInfo start  -->
		<div class="busiInfo">
			<h3><?php echo in_array($offer['0']['number'],$this->config->item('js_cooplist')) ? '寄售商信息' : '回收商信息';?></h3>
			<!--  busiMan  -->
			<div class="busiMan">
				<div class="top clearfix">
				    <div class="tier">
                        <div class="tier-left">
                             <div class="name definit fl">
                                <span class="nameBlack"><?php echo mb_substr($offer['0']['offer_coop_name'],0,1).'师傅'; ?></span>
                             </div>
                             <div class="authen">
                                 <span class="noOrange"><?php  echo $auto[$offer['0']['offer_coop_auth']] ;?></span>
                             </div>
                        </div>
                        <div class="tier-float">
                           <div class="score fr">
                               服务评分：
                               <span><?php echo $offer['0']['cooperator_class']; ?></span>
                           </div>
                            <div class="score below fr">
                                成交单数 ：
                                <span style="color:#575757;"><?php echo $offer['0']['statistic_sum']; ?></span>
                            </div>
                    </div>
                </div>

			</div>

			</div>
			<!--  busiMan  -->
			<!--  sucRecord  -->
			<div class="sucRecord">
				<h4>成交记录</h4>
				<ul>
					<?php 
					foreach($recover as $key=>$val){
						if($recover[$key]['name']==$offer['0']['offer_coop_name']){
					      if(!empty($deal)){
                        	foreach (json_decode($deal[$key]['content']) as $keys=>$val){
                        	 if($keys<3){?>
                            <li class="clearfix">
						      <p class="brand fl"><?php echo mb_substr($val->type,0,11);?></p>
						      <p class="price fr"><?php empty($val->time) ? $time='' :$time=date('Y-m-d H:i:s',($val->time-24*3600)); echo $time;?></p>
					       </li>
                    <?php } } } } } ?> 
				</ul>
			</div>
			<!--  sucRecord  -->
			<!--  sucRecord  -->
			<div class="eval">
				<h4>收到的评价</h4>
				<ul>
					 <?php 
                        if(!empty($evalua)){
                        foreach ($evalua as  $val){?>
                            <li class="clearfix">
                						<p class="brand fl"><?php echo empty($val['wx_name']) ? '微信用户':$val['wx_name']; ?>:</p>
                						<p class="price fl"><?php echo $val['comment_reason']; ?></p>
                		   </li>
                       <?php }  } ?> 
				</ul>
			</div>
			<!--  sucRecord  -->
		</div>
		<!--  busiInfo end  -->
		<!--  botBtn start  -->
		<div class="botBtn clearfix">			
			<a href="/index.php/nonstandard/submitorder/address?fid=<?php echo $offerid;?>&oid=<?php echo $offer['0']['order_id'];?>" class="startTra">开始交易</a>
		</div>
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
</html>
