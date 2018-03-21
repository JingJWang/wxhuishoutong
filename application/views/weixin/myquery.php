<!DOCTYPE html>
<html>
<head>
<title>回收通</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta name="format-detection" content="telephone=no"/>
<meta name="description" content="回收通" />
<meta name="keywords" content="回收通" />
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('JSPATH'); ?>jquery-1.7.2.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->config->item('CSSPATH'); ?>common.css?v=2000102"/>
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
<style type="text/css">
    body{background:#F8F8FF;}
    .pkeyword{margin-top:10px;}
    #m_but{width: 60px;height:30px;border:1px solid rgb(230,230,230);background:#078b09;color:white;}
    .title{text-align:center;width:360px;height:40px;line-height:40px;}
    .content{margin-top:20px;text-align:center;background:#f9f9f9;}
    .content p{text-algin:center;}
    .content .maplist{text-align:left;background-color: white;margin-top: 15px;}
    .maplist{margin-left:10px;}
    .content .maplist dt{height:30px;font-size:13px;margin-top:2px;line-height: 30px;}
    .content .maplist .title{height:30px;font-size:15px;margin-top:2px;font-weight: bold;text-align:left;color:#FF8C00;}
    #loading{display:none;position:absolute;width:100%;top:30%;left:50%;margin-left:-150px;
    text-align:center;padding:7px 0 0 0;font:bold 11px Arial, Helvetica, sans-serif;}
    .footer{height:40px;line-height: 40px;display:none;text-align: center;}
    .footer button{bordre:1px solid white;height:30px;weight:80px;}
    .maplist span {width: 80px;height: 25px;background-color: #078b09;color: white; }
    .maplist .title{text-align:left;}
    #keyword{width: 60%;height: 30px;float: left;margin-left: 40px;}
    .rote{width: 80px;height:30px;float:left;line-height:30px;}
    .rote_list{width: 60px;height:25px;float:left;line-height:25px; font-weight: bold;
        border: 1px solid #379082;border-radius: 10px;padding: 1px 1px;text-align:center;margin-left:5px;}
    .rote_list a:link{color:#228B22;}.rote_list a:visited{color:#228B22;}
    #nearby {background:white;}
    #nearby dt{font-size:13px;margin-top:10px;line-height: 30px;text-indent:10px;}
    .nearby-title{margin:auto;text-align:center;font-weight: bold;}
    .none dt{font-size: 14x;margin-top: 10px; line-height: 30px;text-align: center;font-weight:bold;}
</style>
</head>
<body>
<div class="top t_c">
回收通
</div>
<form action="" method="post" id="mapdata">
<p style="text-align: center;font-weight:bold;">
本页面仅限于公众号内打开，转发无效！</br></br>回收通为您选择了<font style="color: blue;font-weight: bold;font-size:14px;">最近的5家回收点</font></br></br>
如果您看不到回收地点请<a href="http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=400195880&idx=1&sn=f827d6b1be190f84681518523dd680c2#rd" style="color:red;text-decoration:underline;">点击这里</a>
</p>
<p class="pkeyword">
<input id="keyword" type="text"  name="keyword" placeholder="请输入您所在的地点" value="<?php if(!empty($coordinate)){echo $coordinate['data']['addres'];} ?>"/>
<input id="latitude" type="hidden"  name="latitude" value=""/>
<input id="longitude" type="hidden" name="longitude" value=""/>
<input id="page" type="hidden" name="page" value="1"/>
<input type="hidden" name="map_token" value="<?php echo $token; ?>"/>
<input id="m_but" type="button" onclick="matching(1);" name="but"  value="查询"/>
</p>
<div class="nearbylist">
    <dl class="none" style="display: none;"><dt>点击下列地点修正当前位置</dt></dl>
    <dl id="nearby">
 	</dl>    
</div>
<div class="content">
    <?php 
        if(isset($addreslist['data']) && is_array($addreslist['data'])){
        foreach ($addreslist['data']['map']  as $key => $value){
            ?>
            <dl class="maplist">
                <dt class="title"><?php echo $value['name']; ?></dt>
                <?php if(isset($value['address']{24})){
                    echo '<dt>网点地址:'.$value['address'].'</dt>';
                }else{
                    echo '<dt>网点地址:'.mb_substr($value['address'],0,24).'</dt>';
                    echo '<dt>'.mb_substr($value['address'],24).'</dt>';
                    
                }?>
 	    	    <dt>联系电话:<?php echo $value['phone']; ?></dt>
 	    	    <dt>营业时间:<?php echo $value['housr']; ?></dt>
 	    	    <dt>
     	    	    <div class="rote"><font style="font-size:14px;font-weight: bold;">路线查看:</font></div>
     	    	    <div class="rote_list"><a href="<?php echo $value['map']['car']; ?>">驾车</a></div>
     	    	    <div class="rote_list"><a href="<?php echo $value['map']['bus']; ?>">公交</a></div>
     	    	    <div class="rote_list"><a href="<?php echo $value['map']['walk']; ?>">步行</a></div>
     	    	</dt>
 	    	</dl>    
            <?php } }?>
</div>
<p class="footer"></P>
</br>
</br>
</br>
</br>
<div id="loading">
<img src="/static/weixin/public/img/loading.gif" mce_src="/static/weixin/public/img/loading.gif" alt="loading.." />
</div>
</form>
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
  	    			  if(method == 1){
    	    				content= '<p>您附近的营业网点</p>';
    	    		  }else{
    	 	    	    	content='';
  	 	 	          }     
  	 	 	          //显示地址数量
  	 	 	          if(method == 1){
  	  	 	 	          var num=1;
  	  	 	 	      }else{
  	  	  	 	 	      var num=(Number($("#page").val())-1)*3+1;
  	  	  	 	 	  } 
  	  	  	 	 	  var nearby=''; //附近的地点
  	  	 	 	        $.each(address['data']['nearby'],function(n,value) {
  	  	  	 	 	    nearby += '<dt>'+value+'</dt>';
  	  	  	 	 	   }); 
    	  	  	 	   $(".none").show();
	  	  	 	 	   $("#nearby").html(nearby);
  	  	  	 	 	  //遍历结果
 	    	          $.each(address['data']['map'],function(n,value) {
 	 	    	              if(value['address'].length > 24){
 	 	 	    	             var address='';
 	 	 	 	    	            address ='<dt>网点地址:'+value['address'].substring(0,24)+'</dt>'+'<dt>'+value['address'].substring(24)+'</dt>';
 	 	 	    	          }else{
 	 	 	 	    	        	address='<dt>网点地址:'+value['address']+'</dt>';
 	 	 	 	    	      }
    	    	              content +='<dl class="maplist">'+
    	  	                    '<dt class="title">'+value['name']+'</dt>'+address+
    	  	     	    	    '<dt>联系电话:'+value['phone']+'</dt>'+
    	  	     	    	    '<dt>营业时间:'+value['housr']+'</dt><dt><div class="rote"><font style="font-size:14px;font-weight: bold;">路线查看:</font></div>'+
    	  	         	    	    '<div class="rote_list"><a href="'+value['map']['car']+'">驾车</a></div>'+
    	  	         	    	    '<div class="rote_list"><a href="'+value['map']['bus']+'">公交</a></div>'+
    	  	         	    	    '<div class="rote_list"><a href="'+value['map']['walk']+'">步行</a></div></dt></dl>';
    	    	              num++;       
  	  	    	      });  	
  	  	    	      //地址列表 	    	      
  	    	         if(method == 1){
    	    	           $('.content').html(content);
  	    	         }else{
    	    	           $('.content').append(content);
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
