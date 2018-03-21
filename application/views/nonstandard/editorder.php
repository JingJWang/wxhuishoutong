<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>数码产品</title>
    <script type="text/javascript" src="../../../static/home/js/f_js.js"></script>
    <link rel="stylesheet" href="../../../static/home/css/swiper.3.1.2.min.css"/>
    <link rel="stylesheet" href="../../../static/home/css/f_style.css"/>
    <link rel="stylesheet" href="../../../static/home/css/citychoose.css"/>
    <link rel="stylesheet" href="../../../static/home/css/OldCloth.css"/>
</head>
<body id="DGbody">
<div class="box">
    <header class=" header">
        <div class="main">
            <div class="headerBg">
                 <img src="../../../static/home/images/warn.png" alt=""/>
            </div>
            <p>亲，您的信息填的越全，报价越高。信息太少大家<br/>不敢报价哟~</p>
        </div>
    </header>
     <!--物品属性 开始
    <div class="mode1">
        <div class="tit">
            <h1 class="h1">物品属性</h1>
        </div>
        <div class="modeBox selects">
            <div class="hBox oh">
                <div class="fl">种类</div>
                <div class="fr pr16  fx_btn"><span class="fx_value"><?php echo $attrinfo['proname']; ?></span></div>
            </div>
        </div>
        <div class="modeBox selects">
            <div class="hBox oh noBorder">
                <div class="fl">品牌</div>
                <div class="fr pr16  fx_btn"><span class="fx_value"><?php echo $attrinfo['braname']; ?></span></div>
            </div>
            
        </div>
        <div class="modeBox selects">
            <div class="hBox oh noBorder">
                <div class="fl">型号</div>
                <div class="fr pr16  fx_btn"><span class="fx_value"><?php echo $attrinfo['typename']; ?></span></div>
            </div>
        </div>
    </div>-->
    <!--物品属性 结束-->
    <!--地址 开始-->
    <div class="mode1">
        <div class="tit">
            <h1 class="h1">地址(必填)</h1>
        </div>
        <div style="clear: both;"></div>
        <div id="sjld">
            <div class="m_zlxg bg3" id="shenfen">
                <p title=""><?php echo $order['order_province']; ?></p>
                <div class="m_zlxg2">
                    <ul></ul>
                </div>
            </div>
            <div class="m_zlxg bg3" id="chengshi">
                <p title=""><?php echo $order['order_city']; ?></p>
                <div class="m_zlxg2">
                    <ul></ul>
                </div>
            </div>
            <div class="m_zlxg bg3" id="quyu">
                <p title=""><?php echo $order['order_county']; ?></p>
                <div class="m_zlxg2">
                    <ul></ul>
                </div>
            </div>            
        </div>
        <div style="clear: both;"></div>
    </div>
    <!--地址 结束-->
    <!--楼门牌号 开始-->
    <div class="mode2">
        <div class="tit2"></div>
        <div class="mode2Box">
            <div class="h2Box oh">
                <div class="Txt TxtBg"><span>小区名称(必填)</span>
                <input id="quarters" class="TxtBox" type="text" 
                value="<?php echo $order['order_residential_quarters']; ?>" placeholder="例如 :天通苑"/></div>
             </div>
        </div>
    </div>
    <!--更多信息 开始-->
    <div class="mode2">
        <div class="tit">
            <h1 class="h1">更多信息</h1>
        </div>
        <div class="mode2Box">
            <div class="h2Box oh">
                <div class="Txt TxtBg">
                    <span>型号(选填)</span>
                    <div class="fr pr16 fx_btn"><span class="fx_value"><input id="tytename" type="text" value="" 
                placeholder="例如 三星  S4  I9500"></span></div>
                </div>
            </div>
        </div>
        <div class="mode2Box">
            <div class="h2Box oh">
                <div class="Txt TxtBg" id="Bao">
                    <span>是否可用</span>
                    <label class="btnY choose active">
                    <input type="radio" value="1" name="danbao" <?php echo $order['order_isused']==1 ? 'checked="checked"' :'';  ?> class="fhide">是</label>
                    <label class="btnN choose">
                    <input type="radio" value="-1" name="danbao" <?php echo $order['order_isused']==-1 ? 'checked="checked"' :'';  ?>class="fhide">否</label>
                    <input id="sfdq_tj" type="hidden" value="<?php echo $order['order_province']; ?>" />
                    <input id="csdq_tj" type="hidden" value="<?php echo $order['order_city']; ?>" />
                    <input id="qydq_tj" type="hidden" value="<?php echo $order['order_county']; ?>" />
                    <input id="latitude" type="hidden" value="<?php echo $order['order_latitude']; ?>" />
                    <input id="longitude" type="hidden" value="<?php echo $order['order_longitude']; ?>" />
                    <input id="oid" type="hidden" value="<?php echo $oid; ?>" />
                </div>
            </div>
        </div>
    </div>
    <!--更多信息  结束-->
    <!--更多信息 开始-->
    <div class="mode1 oh">
        <!--提交 开始-->
        <div class="mode1">
            <div class="tit2"></div>
            <div class="botBox">
                <span  onclick="EditOrder(-2);" >保存订单</span>
                <span  onclick="EditOrder(1);"  class="active">提交订单</span>
            </div>
        </div>
        <!--提交 结束-->
    </div>
<div id="turn_gif_box">
    <div id="turn_gif">
        <span>
            <img src="../../../static/home/images/loading.gif" alt=" "/>
        </span>
    </div>
</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="../../../static/home/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../../static/home/js/swiper.3.1.2.jquery.min.js"></script>
<script type="text/javascript" src="../../../static/home/js/address.js"></script>
<script type="text/javascript" src="../../../static/home/js/fxjs.js"></script>
<script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
<script type="text/javascript">
                wx.config({
                    debug: false,
                    appId: '<?php echo $signPackage["appId"];?>',
                    timestamp: <?php echo $signPackage["timestamp"];?>,
                    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
                    signature: '<?php echo $signPackage["signature"];?>',
                    jsApiList: [ 	   
                        'getLocation', 
                    ]
                  });
                  wx.ready(function () {
                	    wx.getLocation({
                    		    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    		    success: function (res) {
                    		        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    		        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    		        var speed = res.speed; // 速度，以米/每秒计
                    		        var accuracy = res.accuracy; // 位置精度
                      		         alert('经度'+longitude+'维度'+latitude+'速度'+speed+'位置精度'+accuracy);
                    		        $("#latitude").val(latitude);
                    		        $("#longitude").val(longitude);
                    		   }
                	    });
                  });
                  //config 信息配置出错后 出现
                  wx.error(function(res){
              	    
                  });
                  //初始化函数
                  $(document).ready(function(){
                  	    mySwiperOC();
                  	    Dxiala();
                  	    shenFen();
                  	    
                  });
    </script>
</body>
</html>