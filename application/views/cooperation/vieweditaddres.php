<html>
<head>
<title>回收通-门店地址修改</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('JSPATH'); ?>jquery-1.7.2.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>common.css?v=2000102"/>
<link   rel="stylesheet" type="text/css" href="/static/weixin/public/css/order.css">
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
		$.cxSelect.defaults.url = '/static/weixin/public/js/cxselect/cityData.min.json';
		$('#province_china').cxSelect({
			selects: ['province', 'city', 'area'],
			nodata: 'none' 
		});
  })
</script>
<style>
 #loading{display:none;position:absolute;width:100%;top:30%;left:50%;margin-left:-150px;
    text-align:center;padding:7px 0 0 0;font:bold 11px Arial, Helvetica, sans-serif;}
 #closeaddres{display:none;} 
 .ctrl_title a {float: right;background-color: #078b09;color: white;width: 60px; height: 25px;line-height: 25px;text-align: center;}
</style>
</head>
<body>
<div class="form_ctrl page_head">修改地址</div>
<div id="subjects">
        <form method="post" action="" id="addresfrom">
		<div class="form_ctrl input_text">
			<label class="ctrl_title">联系方式:</label>
			<input type="text" name="phone" id="phone" value="<?php echo $address['0']['phone']; ?>" placeholder="请输入联系方式">
		</div>
		<div class="form_ctrl input_text">
			<label class="ctrl_title">门店名称:</label>
			<input type="text" name="name"  id="name" value="<?php echo $address['0']['name']; ?>" placeholder="请输入门店名称">
		</div>
		<div id="province_china" class="form_ctrl input_text">
		<label class="ctrl_title">归属地区</label>
    	   <?php 
    	       if(empty($address['0']['phone'])){
    	   ?>
        	    <select class="province"  name="province"  data-first-title="选择省"></select>            
                <select class="city"  name="city" data-first-title="选择市"></select>            
                <select class="area"  name="area" data-first-title="选择县"></select>
           <?php }else{ ?>
                <select class="province"  name="province" data-value="<?php echo $address['0']['province']; ?>"></select>            
                <select class="city"  name="city" data-value="<?php echo $address['0']['city']; ?>"></select>            
                <select class="area"  name="area" data-value="<?php echo $address['0']['county']; ?>"></select>
           <?php } ?>
		</div>
		<div class="form_ctrl input_text">
			<label class="ctrl_title">详细地址<a  href="javascript:void(0)" onclick="Getmapaddres();">获取定位</a></label>
			<input id="latitude" type="hidden"  name="latitude" value=""/>
            <input id="longitude" type="hidden" name="longitude" value=""/>
			<ul id="maplist">
			     <li><input type="text" name="mapaddres" id="mapaddres" value="<?php echo $address['0']['addres']; ?>"></li>
			</ul>
		</div>
		<div class="form_ctrl input_text">
			<label class="ctrl_title">营业时间</label>
			<input type="text" name="time" id="hours" value="<?php echo $address['0']['hours']; ?>" placeholder="例如10:00-18:00">
		</div>
		<div class="form_ctrl input_text">
			<label class="ctrl_title">营业状态</label>
		     <?php  switch ($address['0']['status']){
		         case '1':
		             ?>
		                                  营业<input type="radio" name="status" value="1" id="succ" checked="checked">休息<input type="radio" name="status" id="fall" value="-1">
		             <?php
		             break;
		         case '-1':
		             ?>
		                                 营业<input type="radio" name="status" value="1" id="succ">休息<input type="radio" checked="checked" name="status" id="fall" value="-1">
		             <?php
		             break;
		         default: 
		             ?>
		                                  营业<input type="radio" name="status" value="1" id="succ">休息<input type="radio" name="status" id="fall" value="-1">
		             <?php 
		             break;
		     } ?>	
		</div>
		<div class="form_ctrl input_text">
			<input type="button" onclick="SubAddres();" id="subaddres" value="提交">
		</div>
	</form>
</div>
<input type="button" onclick="WeixinJSBridge.call('closeWindow');"  value="关闭"  id="closeaddres"/>
<div id="loading">
<img src="/static/weixin/public/img/loading.gif" mce_src="/static/weixin/public/img/loading.gif" alt="loading.." />
</div>
</body>
<script>
  /**
   * 获取当前gps坐标  在百度地图中的地理位置
   */
  function Getmapaddres(){
	   var lat=$("#latitude").val();
	   var lon=$("#longitude").val();
	   var mapaddres=$("#mapaddres").val();
	   $.ajax({
  	       type: "POST",
  	       url: "/index.php/weixin/wxmap/Getbaidumap",
  	       data:{latitude:lat,longitude:lon},
  	       dataType: "json",
    	   beforeSend: function () {
         	   $("#loading").show();  
    	   },
  	       success: function(data){    
    	    	  var address = eval(data);
    	    	  if(address['status'] == 1){
    	    		  var addres=address['data']['addres'];
    	    		  if(addres == ''){
    	    			  addres = '';
    	    		  }
    	    		  var content ='<li><input type="radio" name="address" value="'+addres+'">'+addres+'</li>';
        	    	  $.each(address['data']['list'],function(n,value) {
     	    	              content += '<li><input type="radio" name="address" value="'+value+'">'+value+'</li>';  
      	  	    	  }); 
        	    	  content +='<li><input type="text" name="mapaddres" value=""></li>';
      	  	    	  $("#maplist").html(content);
        	      }
    	    	  if(address['status'] == 0){
   	    		     alert(address['msg']);
        	      }
    	    	  $("#loading").hide();
   	  	   },
    	   complete: function () {
      	        //$("#m_but").removeAttr("disabled");
      	   },
      	   error: function (data) {
          	    alert('System encountered trouble!');
        	    $("#loading").hide(); 
      	  }
  	   });
	   return false;
  }
  /**
  * 保存当前地址
  */
  function  SubAddres(){
	  var data=$('#addresfrom').serialize();
	  $.ajax({
	       type: "POST",
	       url: "/index.php/cooperation/user/Editaddres",
	       data:data,
	       dataType: "json",
  	   beforeSend: function () {
  	  	   // 禁用按钮防止重复提交loading.gif
  	  	   $("#subaddres").attr({ disabled: "disabled" });
       	   $("#loading").show();  
  	   },
	   success: function(data){    	  	
		 $("#loading").hide()       
  	     var response = eval(data);
  	     if(response['status'] == 0){
    	   alert(response.info);
  	  	 }
  	  	 if(response['status'] == 1){
   	  		  $("#closeaddres").css('display','block');
     	  	  $("#subaddres").css('display','none');
   	  	      alert(response.info);
  	  	 }
	   },
  	   complete: function () {
    	   $("#subaddres").removeAttr("disabled");
       },
       error: function (data) {
           alert('System encountered trouble!');
      	   $("#loading").hide(); 
    	  }
	   });
  }
</script>
<script src="<?php echo $this->config->item('JSPATH'); ?>cxselect/jquery.cxselect.min.js"></script>
</html>
