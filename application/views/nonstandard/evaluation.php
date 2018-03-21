<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通订单评价</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
		<script type="text/javascript" src="../../../static/m/js/startScore.js"></script>
	</head>
	<body>
		<!--  green start  -->
		<div class="green">
			<div class="back">
				<p>订单评价</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end  -->
		<!--  evaluScore start  -->
		<div class="evaluScore">
			<div class="starEvalu">
			    <div id="startone"  class="block clearfix" >
			    	<div class="text clearfix">
			    		<i class="fl">请为订单评分：</i>
			    		<p class="fr"><span class="fenshu"></span> 分</p>
			    	</div>
			        <div class="star_score"></div>
			    </div>
			</div>
			<div class="label">
				<ul class="clearfix">
				 <?php  
                    foreach ($option as $key=>$value) {
                        echo '<li onclick="label(this)" data-key="'.$key.'">'.$value.'</li>';
                    }
                ?>
				</ul>
			</div>
		</div>
		<!--  evaluScore end  -->
		<!--  opinion start  -->
		<div class="opinion">
			<p class="title">请留下宝贵意见：</p>
			<textarea name=""  id="textarea" cols="30" rows="10" placeholder="请输入宝贵意见"></textarea>
		</div>
		<input type="hidden" id="oid" value="<?php echo $oid; ?>"/>
        <input type="hidden" id="type" value="<?php echo $type; ?>"/>
		<!--  opinion end  -->
		<div class="btn">
			<a href="javascript:;"  onclick="SubmitEvaluation();" class="submitBtn">提交评价</a>
		</div>		
	</body>
	<script src="../../../static/home/js/ajax_common.js"></script>
    <script type="text/javascript">
     	scoreFun($("#startone"))
    </script>
</html>
