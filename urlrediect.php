<?php
$url=urlencode('http://wx.recytl.com/');
if(isset($_GET['url'])){
	if($_GET['url']=='create_order'){
		header('Location:'.'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.'index.php/weixin/order/index&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect');		
	}else if($_GET['url']=='fenxiang'){
		header('Location:'.'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.'index.php/weixin/voucher&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect');		
	}else if($_GET['url']=='xianjinquan'){
		header('Location:'.'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri='.$url.'index.php/weixin/voucher/myvoucherlist&response_type=code&scope=snsapi_base&state=aaa#wechat_redirect');		
	}
}

?>