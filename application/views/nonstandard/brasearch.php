<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通搜索</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/m_cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="../../../static/m/css/orderDetail.css"/>
		<script type="text/javascript" src="../../../static/m/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="../../../static/m/js/myGoods.js"></script>
	</head>
	<body>
		<!--  grayBg start      -->
		<div class="grayBg"></div>
		<!--  grayBg end      -->
		<!--  green start  -->
		<div class="green">
			<div class="back">
				<p>搜索</p>
				<a href="JavaScript:history.back(1);">返回</a>
			</div>
		</div>
		<!--  green end  -->
		<div class="afficle">
		    <div class="contet">如果宝贝是山寨、高仿或描述严重不符需自付往返邮费</div>
		</div>
		<!--  searchBox start  -->
		<div class="searchBox">
			<div class="search clearfix">
				<p id="chose" class="fl" onclick="searchselect()">手机</p>
				<input type="text" name="" id="keyword" value="" placeholder="输入您的手机品牌型号查询" class="searchInput fl" />
				<a href="javascript:;" class="fr" onclick="TypeSearch(1);"></a>
			</div>
			<div class="downSlide">
				<ul>
					<li class="pinName" data-key="5">手机</li>
					<li>平板</li>
				</ul>
			</div>
		</div>
        <!--  searchBox end  -->
        <div style="width:100%;height:30px;"></div>

		<!--  result start  -->
		<div class="result">
		    <ul id="resultlsit">
				
			</ul>
		</div>
		<!--  result end  -->
		<!--  noresult start 搜索无结果的状态 -->
		<div class="noresult" style="display: none;">
			<span class="pic"></span>
			<p>小通没有找到符合条件的机型!</p>
		</div>
		<!--  noresult end  -->
	</body>
	<script  type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
	<script>
        function GetQueryString(name){
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
             var r = window.location.search.substr(1).match(reg);
              if(r!=null)return  unescape(r[2]); return null;
        }
        var myurl=GetQueryString("id");
        if(myurl !=null && myurl.toString().length>0){
            if(myurl == 1){
                $("#chose").html("平板");
            }else{
                $("#chose").html("手机");
            }
        }
    </script>
</html>
