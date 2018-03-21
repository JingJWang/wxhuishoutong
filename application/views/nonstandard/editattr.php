<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
		<title></title>
		<link rel="stylesheet" type="text/css" href="../../../static/home/css/evaluate.css">
		<link rel="stylesheet" media="screen" href="../../../static/home/css/cssreset.css" type="text/css" />
		<script type="text/javascript" src="../../../static/home/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/home/js/evaluate.js"></script>
	</head>
	<body>
		<div class="wrap">
			<header>
				<div class="title">
					<a href="javascript:;" class="TextOverflow"><span>手机型号</span>&nbsp;&nbsp;<samp><?php echo $order['name']; ?> </samp></a>
				</div> 
			</header>
			<article>
				<form name="myFrom" action="/index.php/nonstandard/submitorder/CheckOrderAttr" id="request" method="post">				
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
        										<p class="conTxt fl TextOverflow"><?php  echo array_key_exists($key,$attrinfo) ? trim($attrinfo[$key],',') : ''; ?></p>
        										<a class="xiugai_btn fr" href="javascript:;" onClick="li_mod(this)">修改</a>																					
        									</div>
        									<ul class="pinggu_other widthBig">
        									<?php foreach ($attribute['val'][$key] as  $val){ ?>
        										<li onClick="property_click(this,'<?php echo $key; ?>','<?php rand(1,999); ?>')" name="sx_child_1"><span class="property_value"><i><?php echo $val; ?></i></span><span class="gou"></span></li>
        									<?php } ?>
        										<div class="clear">&nbsp;</div>
        									</ul>
        									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo array_key_exists($key,$attrinfo) ?trim($attrinfo[$key],',') : ''; ?>">
        								</dd>
        								<?php }else{ ?>
        								<dd>
    									<div class="property_title  pinggu_title_on clearfix">
    										<span class="fl"><?php echo $name; ?></span>
    										<p class="conTxt fl TextOverflow"><?php echo array_key_exists($key,$attrinfo) ? trim($attrinfo[$key],',') : ''; ?></p>
    										<a class="xiugai_btn fr" href="javascript:;"  onClick="li_mod(this)">修改</a>																					
    									</div>
    									<ul class="pinggu_other widthBig">
    									   <?php 
    									   foreach ($attribute['val'][$key] as  $val){
    									       if($count ==2){
    									           $sing=',btnColor()';
    									       }else{
    									           $sing='';
    									       }
    									       if($key == 'oather' && array_key_exists($key,$attrinfo)){ 
    									           $name= rand(1,999);
    									           if(strpos($attrinfo[$key],$val) !== false){
    									               $flag='class="selected"';
    									           }else{
    									               $flag='';
    									           }
    									    ?>
    									    <li onClick="item_click(this,'<?php echo $key;  ?>','<?php echo rand(1,999);;  ?>','<?php echo $name;  ?>'),btnColor()" data-val="notice"  data-key="<?php echo $val ?>" name="<?php echo $name; ?>" <?php echo $flag; ?>> <span class="property_value"><?php echo $val; ?></span> <span class="gou"></span> </li>   
    									   <?php }else{ ?>
    										<li onClick="item_click(this,'<?php echo $key; ?>','<?php echo $val;  ?>')<?php echo $sing; ?>" name="<?php echo $key; ?>"><span class="property_value"><i><?php echo $val; ?></i></span><span class="gou"></span></li>
    										<?php } }
    										?>
    										<div class="clear">&nbsp;</div>
    									</ul>
    									<input type="hidden" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo empty($attrinfo[$key])? ' ' :','.$attrinfo[$key]; ?>">
    									
    								</dd>
    								<?php } ?>
    								
								  <?php  $number--;--$count;} ?> 
							</dl>
                            <input type="hidden"  name="typeid" id="typeid" value="<?php echo $order['order_ctype']; ?>">
                            <input type="hidden"  name="number" id="number" value="<?php echo $id; ?>">
						</div>   						
					</div>
					<div class="chakan_price" style="cursor: pointer; background-color: rgb(247, 94, 38);" onclick="EditOrderAttr();">下一步</div>
				</form>
			</article>
		</div>
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