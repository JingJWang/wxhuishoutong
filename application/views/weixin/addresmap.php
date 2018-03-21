<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title></title>
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
<meta http-equiv="Cache-Control" content="no-siteapp" />
<meta name="format-detection" content="telephone=no" />
<link rel="stylesheet" type="text/css" href="/static/mall/css/cssReset.css"/>
<link rel="stylesheet" type="text/css" href="/static/mall/css/brandInquery.css"/>
<link rel="stylesheet" type="text/css" href="/static/mall/css/searchStores.css"/>
<script type="text/javascript" src="<?php echo $this->config->item('JSPATH'); ?>jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/mall/js/brandIquery.js"></script>
<script>
/*
 * 注意：
* 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
* 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
* 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
*
* 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
* 邮箱地址：weixin-open@qq.com
* 邮件主题：【微信JS-SDK反馈】具体问题
* 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
*/
wx.config({
    debug: false,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [ // 所有要调用的 API 都要加到这个列表中	   
        'getLocation', 
    ]
  });
   
  wx.ready(function () {
   // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
	    wx.getLocation({
    		    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
    		    success: function (res) {
    		        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
    		        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
    		        var speed = res.speed; // 速度，以米/每秒计
    		        var accuracy = res.accuracy; // 位置精度
    		        //alert('已经获取到您的地理位置!');
      		         //alert('经度'+longitude+'维度'+latitude+'速度'+speed+'位置精度'+accuracy);
    		        $("#latitude").val(latitude);
    		        $("#longitude").val(longitude);
    		       
    		        
    		   }
	    });
  });
   wx.error(function(res){
    //config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
   });  
  $(document).ready(function(){
	  $("#nearby dt").live('click',function(){
		 $('#keyword').val($(this).html());
		});
  })
</script>
		
	</head>
	<body>
		<!-- --- top start --- -->
		<form action="" method="post" id="mapdata">
		<div class="topText" id="storeCon">
			<span class="storeTop">本页面仅限于公众号内打开，转发无效！</span>
			<span class="storeBot" style="padding-bottom:0px;">回收通为您选择了最近的5家回收点<br>如果您看不到回收地点请<a href="http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=400195880&idx=1&sn=f827d6b1be190f84681518523dd680c2#rd" style="color:red;text-decoration:underline;"> 点击这里 </a><font style="color:red;"><br>或手动输入地址查询</font></span>
			<div class="input clearfix">
				<input type="text"  name="keyword" id="keyword" value="<?php if(!empty($coordinate)){echo $coordinate['data']['addres'];} ?>" placeholder="输入您的地址查附近的门店" class="inputCon fl" />
				<input id="latitude" type="hidden"  name="latitude" value=""/>
				<input id="longitude" type="hidden" name="longitude" value=""/>
				<input id="page" type="hidden" name="page" value="1"/>
				<input type="hidden" name="map_token" value="<?php echo $token; ?>"/>
				<input type="button" name="but" id="m_but" value="" onclick="matching(1);" class="inputBtn fl" />
			</div>
		</div>
		<!-- --- top end --- -->
		<!-- --- storeCon start --- -->
		<div class="storeCon">
		<?php 
        	if(isset($addreslist['data']) && is_array($addreslist['data'])){
        	foreach ($addreslist['data']['map']  as $key => $value){
        	    ?>
			<div class="storeList">
				<div class="storeInfo">
					<h3><?php echo $value['name']; ?></h3>
					<p>
						<?php 
                		$addres_number=strlen($value['address']);
                		if($addres_number < 58 ){
                		    echo '<dt>网点地址:'.$value['address'].'</dt>';
                		}else{
                    		echo '<dt>网点地址:'.mb_substr($value['address'],0,24).'</dt>';
                    		echo '<dt>'.mb_substr($value['address'],24).'</dt>';
		                }
                		?>
					</p>
					<p>联系电话:<?php echo $value['phone']; ?></p>
					<p>营业时间:<?php echo $value['housr']; ?></p>
				</div>
				<div class="traffic">
					<ul class="clearfix">
						<li class="fl"><a href="<?php echo $value['map']['car']; ?>">驾车</a></li>
						<li class="fl"><a href="<?php echo $value['map']['bus']; ?>">公交</a></li>
						<li class="fl"><a href="<?php echo $value['map']['walk']; ?>">步行</a></li>
					</ul>
				</div>
			</div>
        <?php } }?>
		</div>
		<!-- --- storeCon end --- -->
		<!-- --- copy start --- -->
		<div class="copy">
			<p>© 2014  回收通 版权所有，并保留所有权利</p>
		</div>
		</form>
		<!-- --- copy end --- -->
<script type="text/javascript">
    function  matching(method){
   	  var latitude=$("#latitude").val();
      var longitude=$("#longitude").val();
      var datas=$("#mapdata").serialize();
      if(method == 1 && $("#page").val() != 1){
    	  $("#page").val('1');
    	  datas=$("#mapdata").serialize();
      }
  	  $.ajax({
  	       type: "POST",
  	       url: "/index.php/weixin/wxmap/maplist",
  	       data:datas,
  	       dataType: "json",
    	   beforeSend: function () {
    	  	   // 禁用按钮防止重复提交loading.gif
    	  	   $("#m_but").attr({ disabled: "disabled" });
         	   $("#loading").show();  
    	   },
  	       success: function(data){
    	    	  $("#loading").hide();    	  	       
    	    	  var address = eval(data)
    	    	  //请求失败
    	    	  if(address['status'] == 0){
    	    		  $('.content').html(address['msg']);
  	    		  }
  	    		  var content ='';
  	    		  //请求成功
  	    		  if(address['status'] == 1 ){
  	  	    		  //第一次请求是地址列表
  	    			  // if(method == 1){
    	    				// content= '<p>您附近的营业网点</p>';
    	    		  // }else{
    	 	    	    	content='';
  	 	 	          // }     
  	 	 	          //显示地址数量
  	 	 	          if(method == 1){
  	  	 	 	          var num=1;
  	  	 	 	      }else{
  	  	  	 	 	      var num=(Number($("#page").val())-1)*3+1;
  	  	  	 	 	  } 
  	  	  	 	 	 //  var nearby=''; //附近的地点   暂时取消
  	  	 	 	    //     $.each(address['data']['nearby'],function(n,value) {
  	  	  	 	 	 //    nearby += '<dt>'+value+'</dt>';
  	  	  	 	 	 //   }); 
    	  	  	 	 //   $(".none").show();
	  	  	 	 	   // $("#nearby").html(nearby);
  	  	  	 	 	  //遍历结果
 	    	          $.each(address['data']['map'],function(n,value) {
							if (value['address'].length > 24) {
								var address='';
								address ='<p>网点地址:'+value['address'].substring(0,24)+'</p>'+'<p>'+value['address'].substring(24)+'</p>';
							}else{
								address='<p>网点地址:'+value['address']+'</p>';
							}
							content += '<div class="storeList"><div class="storeInfo"><h3>'+value['name']+'</h3>'+address+'<p>联系电话:'+value['phone']+'</p><p>营业时间:'+value['housr']+'</p></div><div class="traffic"><ul class="clearfix"><li class="fl"><a href="'+value['map']['car']+'">驾车</a></li><li class="fl"><a href="'+value['map']['bus']+'">公交</a></li><li class="fl"><a href="'+value['map']['walk']+'">步行</a></li></ul></div></div>';
							num++;      
  	  	    	      });  	
  	  	    	      //地址列表 	    	      
  	    	         if(method == 1){
    	    	           $('.storeCon').html(content);
  	    	         }else{
    	    	           $('.storeCon').append(content);
 	 	 	 	     }
 	 	 	 	    /*  //结果集大于1页的时候 显示查看更多按钮
 	 	 	 	     if(address['data']['toail'] > 1){
 	 	 	 	 	       var p=Number($("#page").val())+1;
 	 	 	 	 	 	   $("#page").val(p);
 	 	 	 	 	 	   $(".footer").css('display','block');
 	 	 	 	 	 	   $(".footer").html('<input type="button" onclick="matching(2);" value="加载更多">');
 	 	 	 	 	 	   
 	 	 	 	 	 }
 	 	 	 	 	 //当 当前页 等于总页数是 隐藏掉加载更多按钮
 	 	 	 	 	 if(address['data']['page'] == address['data']['toail']){
 	 	 	 	 		 $(".footer").css('display','none');
 	 	 	 	 	 	 $(".footer").html('<input type="button" onclick="matching(2);" value="加载更多">');
 	 	 	 	     }   */
  	    	      }   
   	  	       },
    	   complete: function () {
      	        $("#m_but").removeAttr("disabled");
      	   },
      	   error: function (data) {
          	    alert('System encountered trouble!');
        	    $("#loading").hide(); 
      	  }
  	   });
  	    
    }
    function loadDiv(text) {
        var div = "<div id='_layer_'><div id='_MaskLayer_' style='filter: alpha(opacity=30); -moz-opacity: 0.3; opacity: 0.3;background-color: #000; width: 100%; height: 100%; z-index: 1000; position: absolute;" +
        "left: 0; top: 0; overflow: hidden; display: none'></div><div id='_wait_' style='z-index: 1005; position: absolute; width:430px;height:218px; display: none'  ><center><h3>" +""+ text + "<img src='../images/loading.gif' /></h3><button onclick='LayerHide()'>关闭</button></center></div></div>"; 
      return div; 
     }
</script>
<script src="<?php echo $this->config->item('JSPATH'); ?>cxselect/jquery.cxselect.min.js"></script>		
	</body>
</html>
