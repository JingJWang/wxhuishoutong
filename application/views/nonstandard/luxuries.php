<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1" />
    <title>奢侈品产品</title>
    <script type="text/javascript" src="../../../static/home/js/f_js.js"></script>
    <link rel="stylesheet" href="../../../static/home/css/f_style.css"/>
    <link rel="stylesheet" href="../../../static/home/css/citychoose.css"/>
    <link rel="stylesheet" href="../../../static/home/css/swiper.3.1.2.min.css"/>
    <link rel="stylesheet" href="../../../static/home/css/OldCloth.css"/>
</head>
<body class="OCBody">
<div class="box">
    <header class=" header">
        <div class="main">
            <div class="headerBg">
                 <img src="../../../static/home/images/warn.png" alt=""/>
            </div>
            <p>亲，您的信息填的越全，报价越高。信息太少大家<br/>不敢报价哟~</p>
        </div>
    </header>
    <!--地址 开始-->
    <div class="mode1">
        <div class="tit">
            <h1 class="h1">地址</h1>
        </div>
<div style="clear: both;"></div>
        <div id="sjld">
            <div class="m_zlxg bg3" id="shenfen">
                <p title="">选择省份</p>
                <div class="m_zlxg2">
                    <ul></ul>
                </div>
            </div>
            <div class="m_zlxg bg3" id="chengshi">
                <p title="">选择城市</p>
                <div class="m_zlxg2">
                    <ul></ul>
                </div>
            </div>
            <div class="m_zlxg bg3" id="quyu">
                <p title="">选择区域</p>
                <div class="m_zlxg2">
                    <ul></ul>
                </div>
            </div>
            <input id="sfdq_num" type="hidden" value=""/>
            <input id="csdq_num" type="hidden" value=""/>
            <input id="sfdq_tj"  type="hidden" value=""/>
            <input id="csdq_tj"  type="hidden" value=""/>
            <input id="qydq_tj"  type="hidden" value=""/>
            <input id="latitude" type="hidden" value="40.0079520000"/>
            <input id="longitude"type="hidden" value="116.3611040000"/>
            <input id="pronames"  type="hidden" value=""/>
            <input id="proids"    type="hidden" value=""/>
            <input id="brandnames"  type="hidden" value=""/>
            <input id="brandids"    type="hidden" value=""/>
         </div>
        <div style="clear: both;"></div>
    </div>
    <!--地址 结束-->
    <!--楼门牌号 开始-->
    <div class="mode2">
        <div class="tit2"></div>
        <div class="mode2Box">
            <div class="h2Box oh">
                <div class="Txt TxtBg"><span>小区名称</span><input  class="TxtBox" id="quarters" type="text" placeholder="请输入小区名称"/></div>
                <div class="Txt "><span>楼门房号</span><input  class="TxtBox" id="number" type="text" placeholder="请输入楼门房号"/></div>
            </div>
        </div>
    </div>
    <!--楼门牌号 结束-->
    <!--物品属性 开始-->
    <div class="mode1">
        <div class="tit">
            <h1 class="h1">物品属性</h1>
        </div>
        <div class="modeBox selects">
            <div class="hBox oh">
                <div class="fl">种类</div>
                <div class="fr pr16  fx_btn">
                    <span class="fx_value" id="proname" data-val="">包</span><span class="bg bg1 fx_bg"></span>
                </div>
            </div>
            <div class="selectBox  fx_options" id="prolist"></div>
            
        </div>
        <div class="modeBox selects">
            <div class="hBox oh noBorder">
                <div class="fl">品牌</div>
                <div class="fr pr16  fx_btn"><span class="fx_value" id="branname"></span><span class="bg bg1 fx_bg"></span></div>
            </div>
            <div class="selectBox  fx_options" id="brandlist">               
            </div>
        </div>
    </div>
    <!--物品属性 结束-->
    <form action="#" id="attr">
    <div class="mode1" id="attrcontent">
        <div class="tit2"></div>
        <?php  
        foreach ($attribute['key'] as $key=>$attrkey){?> 
                  <?php  if(is_array($attribute['val'][$key])){?>
                     <div class="modeBox selects">
                            <div class="hBox oh">
                                <div class="fl"><?php echo $attrkey; ?></div>
                                <div class="fr pr16  fx_btn"><span class="fx_value">请选择</span><span class="bg bg1 fx_bg"></span></div>
                            </div>
                    <div class="selectBox  fx_options">       
                    <?php
                        foreach ($attribute['val'][$key] as $attr){ ?>
                            <p data-key="<?php echo $key; ?>" class="attr"><?php echo $attr; ?></p>
                            <?php   }?>
                  </div>
                  <input name="<?php echo $key; ?>" id="<?php echo $key; ?>" type="hidden" value=" ">
                </div>
                <?php  }else{  ?>
                    <div class="modeBox selects">
                        <div class="hBox oh">
                             <div class="fl"><?php echo $attrkey; ?></div>
                            <div class="fl inputBox"><input type="text" name="<?php echo $key; ?>" placeholder="请输入<?php echo $attrkey; ?>"/></div>
                        </div>
                    </div>
                    <?php }?>
        <?php } ?>
    </div>
    </form>
    <!--价格 开始-->
    <div class="mode2">
        <div class="tit2"></div>
        <div class="mode2Box">
            <div class="h2Box oh">
                <div class="Txt TxtBg" id="Bao">
                    <span>是否在保</span>
                    <label class="btnY choose active"><input type="radio" value="yes" name="danbao" checked="checked" class="fhide">是</label>
                    <label class="btnN choose"><input type="radio" value="no" name="danbao" class="fhide">否</label>
                </div>
                <div class="Txt "><span>原价</span><input  class="TxtBox" type="text" id="selling_price" placeholder="请输入原价"/></div>
            </div>
        </div>
    </div>
    <!--价格 结束-->
    <!--第二个物品属性 开始
    <div class="mode3">
        <div class="tit">
            <h1 class="h1">物品属性</h1>
        </div>
        <div class="mode3Box">
            <div class="h2Box oh"  id="Duo">
                <div class="mode3block oh">
                    <div class="active  ddd"><label for="ff1"><span>购买发票</span></label><input type="checkbox" checked="checked" value="1" name="shuxing" checked="checked" id="ff1" class="fhide"></div>
                    <div><label for="ff2"><span>购买发票</span></label><input type="checkbox" value="1" name="shuxing" checked="checked" id="ff2" class="fhide"></div>
                    <div><label for="ff1"><span>购买发票</span></label><input type="checkbox" value="1" name="shuxing" checked="checked" id="ff3" class="fhide"></div>
                    <div><label for="ff4"><span>购买发票</span></label><input type="checkbox" value="1" name="shuxing" checked="checked" id="ff4" class="fhide"></div>
                </div>
            </div>
        </div>
    </div>
    第二个物品属性 结束-->
    <!--更多信息 开始-->
    <div class="mode1 oh">
        <div class="tit">
            <h1 class="h1">更多信息</h1>
        </div>
        <div class="modeBox">
            <div class="">
                <div class="Txt desc">
                    照片(3-10张):如果您的产品有破损请对破损处单独拍照
                </div>
                <div class="swiper-container">
                        <div class="imgBox swiper-wrapper" id="imglist">
                            <div class="swiper-slide" onclick="uploadphoto.click();"><img src="../../../static/home/images/jia.png" alt=""/></div>
                        </div>
                        <div>
                        <input type="file" id="uploadphoto" name="uploadfile" value="请点击上传图片"   style="display:none;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--更多信息 结束-->

    <!--出售价格 开始-->
    <div class="mode2 mt">
        <div class="tit2"></div>
        <div class="mode2Box">
            <div class="h2Box oh">
                <div class="Txt TxtBg" id="Ture">
                    <span>保证真品</span>
                    <label class="btnY choose active"><input type="radio" value="1" name="goods" checked="checked" class="fhide">是</label>
                    <label class="btnN choose"><input type="radio" value="-1" name="goods" class="fhide">否</label>
                </div>
                <div class="Txt "><span>出售价格</span><input  class="TxtBox" type="text" id="selling_price" placeholder="请输入出售价格"/></div>
            </div>
        </div>
    </div>
    <!--出售价格 结束-->

    <!--更多信息 开始-->
    <div class="mode1 oh">
        <div class="tit">
            <h1 class="h1">补充描述</h1>
        </div>
        <div class="modeBox">
            <textarea name="" id="textarea"  id="ordermake" cols="30" rows="10"></textarea>
        </div>
    </div>
    <!--更多信息 结束-->
    <!--提交 开始-->
    <div class="mode1">
        <div class="tit2"></div>
        <div class="botBox">
            <span onclick="CheckData(-1);">保存订单</span>
            <span class="active" onclick="CheckData(1);">提交订单</span>
        </div>
    </div>
    <!--提交 结束-->

</div>
<script type="text/javascript" src="../../../static/home/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../../../static/home/js/swiper.3.1.2.jquery.min.js"></script>
<script type="text/javascript" src="../../../static/home/js/address.js"></script>
<script type="text/javascript" src="../../../static/home/js/fxjs.js"></script>
<script type='text/javascript' src="../../../static/home/js/uploadimg/LocalResizeIMG.js"></script>
<script type='text/javascript' src="../../../static/home/js/uploadimg/mobileBUGFix.mini.js"></script>
<script type="text/javascript" src="../../../static/home/js/ajax_luxuries.js"></script>
</body>
</html>