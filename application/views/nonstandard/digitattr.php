<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
		<title>回收通-填写订单</title>
		<link rel="stylesheet" type="text/css" href="../../../static/home/css/evaluate.css">
		<link rel="stylesheet" media="screen" href="../../../static/home/css/cssreset.css" type="text/css" />
		<script type="text/javascript" src="../../../static/home/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/home/js/evaluate.js"></script>
	</head>
	<body>
		<div class="wrap">
			<header>
				<div class="title">
					<a href="javascript:;" class="TextOverflow"><span>手机型号</span>&nbsp;&nbsp;<samp><?php echo $typename; ?> </samp></a>
				</div> 
			</header>
			<article>
				<form name="myFrom" action="#" id="request" method="post">				
					<div id="property_list" class="property_list">
						<div id="step1">
							<dl>
								 <?php  
								 $count=count($attribute['val']);
								 $number=1;
								 foreach ($attribute['key'] as $key=>$name){ 
								        if($number == 1){
								     ?> 
        								 <dd>
        									<div class="property_title  pinggu_title_on clearfix">
        										<span class="fl"><?php echo $name; ?></span>
        										<p class="conTxt fl TextOverflow"></p>
        										<a class="xiugai_btn fr" href="javascript:;" style="display:none" onClick="li_mod(this)">修改</a>																					
        									</div>
        									<ul class="pinggu_other widthBig">
        									<?php foreach ($attribute['val'][$key] as  $val){ ?>
        										<li onClick="property_click(this,'<?php echo $key; ?>','<?php rand(1,999); ?>')" name="sx_child_1"><span class="property_value"><i><?php echo $val; ?></i></span><span class="gou"></span></li>
        									<?php } ?>
        										<div class="clear">&nbsp;</div>
        									</ul>
        									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="">
        								</dd>
        								<?php }else{ ?>
        								<dd>
    									<div class="property_title  pinggu_title_on clearfix">
    										<span class="fl"><?php echo $name; ?></span>
    										<p class="conTxt fl TextOverflow"></p>
    										<a class="xiugai_btn fr" href="javascript:;" style="display:none" onClick="li_mod(this)">修改</a>																					
    									</div>
    									<ul class="pinggu_other widthBig">
    									   <?php 
    									   foreach ($attribute['val'][$key] as  $val){
    									       if($count ==2){
    									           $sing=',btnColor()';
    									       }else{
    									           $sing='';
    									       }
    									       if($key == 'oather'){ 
    									           $name= rand(1,999);
    									    ?>
    									    <li onClick="item_click(this,'<?php echo $key;  ?>','<?php echo rand(1,999);;  ?>','<?php echo $name;  ?>'),btnColor()" data-val="notice"  data-key="<?php echo $val ?>" name="<?php echo $name; ?>"> <span class="property_value"><?php echo $val; ?></span> <span class="gou"></span> </li>   
    									   <?php }else{ ?>
    										<li onClick="item_click(this,'<?php echo $key; ?>','<?php echo $val;  ?>')<?php echo $sing; ?>" name="<?php echo $key; ?>"><span class="property_value"><i><?php echo $val; ?></i></span><span class="gou"></span></li>
    										<?php } }
    										?>
    										<div class="clear">&nbsp;</div>
    									</ul>
    									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="">
    									
    								</dd>
    								<?php } ?>
    								
								  <?php  $number--;--$count;} ?> 
							</dl>
							<input type="hidden" id="typename" name="typename" value="<?php echo $typename; ?>">
                            <input type="hidden" id="braname" name="braname" value="<?php echo $braname; ?>">
                            <input type="hidden" id="proname" name="proname" value="<?php echo $proname; ?>">
                            <input type="hidden" id="proid" name="proid" value="<?php echo $proid; ?>">
                            <input id="latitude" name="latitude"  type="hidden" value="" />
                            <input id="longitude" name="longitude" type="hidden" value="" />
						</div>   						
					</div>
				</form>
				<div class="chakan_price" style="cursor:pointer;" onclick="CheckAttr();">提交</div>
			</article>
		</div>
		<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script type="text/javascript" src="../../../static/home/js/request_common.js"></script>
		<script src="../../../static/home/js/ajax_digitattr.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					$("#step1 dl dd").find(".pinggu_other").each(function(i){if(i==0)$(this).css('display','block')})
					$("#step1 input[name='desc_id[]']").each(function(){$(this).val(0);})
					$("#pj_ids").val(0);
				})
				
			</script>
	</body>
</html>