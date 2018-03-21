<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通-填写地址</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/address.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css"/>
	</head>
	<body>
		<!--  grayBg start      -->
		<div class="grayBg"></div>
		<!--  grayBg end      -->
		<!--  ifGetOut start      -->
		<div class="ifGetOut01 ifGetOut">
			<p>地址信息属于必填项</p>
			<div class="btns clearfix">
				<a href="javascript:;" class="no fl" onclick="noGetOut()">取消</a>
				<a href="javascript:;" class="yes fr" onclick="noGetOut()">确认</a>
			</div>
		</div>
		<div class="ifGetOut02 ifGetOut">
			<p>请确认您的信息是否填写失误</p>
			<div class="btns clearfix">
				<a href="javascript:;" class="no fl" onclick="noGetOut();">取消</a>
				<a href="javascript:;" class="yes fr" onclick="checkdata(1);">确认</a>
			</div>
		</div>
		<!--  ifGetOut end      -->
		<!--  green start  -->
		<div class="green">
			<div class="back">
				<p>提交订单</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end  -->
		<!--  tip start  -->
		<div class="prompt">
			<p>请完善您的各项信息，方便回收商给出更高的报价</p>
		</div>
		<!--  tip start  -->
		<!--  phoneBrand start  -->
		<div class="phoneBran">
			<div class="phone clearfix">
				<span class="title fl">种类</span>
				<p class="con fl"><?php echo $proname; ?></p>
			</div>
			<div class="brand">
				<span class="title fl">品牌型号</span>
				<p class="con fl"><?php echo $typename; ?></p>
			</div>
			
		</div>
		<!--  phoneBrand start  -->
		<!--  <?php if (isset($address['name'])&&$address['name']!='') { ?>
		<a class="indication" href="<?php echo '/view/shop/selectAddress.html?id='.$offerid.'&oid='.$orderid.'&adr='.$address['id'].'&type=2' ?>">
		    <div class="icon fl">地址</div>
		    <div class="right"></div>
		    <div class="detail">
                <div class="datum">
                    <span id="name"><?php echo $address['name'];?></span>
                    <span class="tel" id="mobile"><?php echo $address['number'];?></span>
                </div>
                <div class="region" id="city" ><?php echo $address['city'];?></div>
                <div class="expli" id="quarters"><?php echo $address['details'];?></div>
		    </div>
		</a>
		<?php }else{ ?>
		<a class="addSite" href="javascript:;">
            <div class="icon fl">地址</div>
            <div class="add fr">点击添加地址</div>
		</a>
		<?php } ?>  -->
        <div class="mobileNew">
            <span class="title fl">联系方式:&nbsp;&nbsp;</span>
            <input type="text" name="mobile" class="cont" placeholder="请再次输入手机号">
        </div>
		<!--  botBtn start  -->
        		<div class="botBtn clearfix">
        			<a href="javascript:;" class="startTra" onclick="Choice('<?php echo $offerid; ?>','<?php echo $orderid;?>');">下一步</a>
        		</div>
        		<!--  botBtn end  -->

        <div class="shadow"></div>
        <div class="frame">
            <div class="title">
                填写收货地址
                <a class="close-btn" href="javascript:;">×</a>
            </div>
            <div class="messages">
            <form id="addresinfo">
                <div class="comprise">
                    <input id="names" name="name" class="import" type="text" placeholder="请输入您的姓名"/>
                </div>
                <div class="comprise">
                    <input id="tel" name="phone" class="import" type="text" placeholder="请输入联系方式"/>
                </div>
                <div class="comprise">
                     <input id="city1" name="address" readonly="readonly" class="import" type="text" placeholder="选择省份、城市、区县"/>
                     <div class="right"></div>
                </div>
                <div class="comprise">
                      <input id="desc" name="detail" class="import" type="text" placeholder="请输入街道、门牌等详细信息"/>
                </div>
               </form>
            </div>
            <a class="add-btn" href="javascript:;" onclick="Addres.submit();">添加地址</a>
        </div>
	</body>
	<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
    <script type="text/javascript" src="../../../static/m/js/Popt.js"></script>
    <script type="text/javascript" src="../../../static/m/js/cityJson.js"></script>
    <script type="text/javascript" src="../../../static/m/js/citySet.js"></script>
	<script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
	<script type="text/javascript" src="../../../static/m/ajax/r_addres.js"></script>
	<script type="text/javascript">
        $("#city1").click(function (e) {
        	SelCity(this,e);
        	var width = parseInt(document.body.clientWidth);
            if(width <= 360){
                $("._citys1").height(150);
            }
        });


    </script>
</html>
