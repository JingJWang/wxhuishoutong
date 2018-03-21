getinfo();
isOnline();
function getinfo(){
	var u = '/index.php/nonstandard/system/dynamic';
	var d = '';
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			$('.fl .number').html(response['data']['deal']["0"]+'<span>单</span>');
			$('.fr .number').html('<span>￥</span>'+response['data']['deal']["1"]+'<span>元</span>');
			var list = '';
			var timestr = '';
			var time = 0;
			timestamp = 0;
			$.each(response['data']['dynamic'], function(i, v) {
			    timestamp = Date.parse(new Date())/1000;
			    time = (timestamp-v['ordre_dealtime'])/60
			    switch(true){
			    	case (time<60): timestr = Math.floor(time)+'分钟以前'; break;
			    	case (time>60&&time<1440) : timestr = Math.floor(time/24)+'小时以前';break;
			    	case (time>1440) : timestr = Math.floor(time/1440)+'天以前';break;
			    }
				list += '<div class="successLog"><div class="userMsg clearfix">'
				  +'<span class="userName fl">'+v['wx_name']+'</span>'
				  +'<span class="userPhone fl phoneNumber">'+v['wx_mobile']+'</span>'
				  +'<span class="time fr">'+timestr+'</span>'
				+'</div>'
				+'<div class="tradeMsg">'
				  +'<p>成功卖出'+v['order_name']+'，获得<span>'+v['order_bid_price']+'</span>奖励</p>'
				+'</div>'
				+'<div class="assess">'
					+'<p><span>评价：</span>回收通平台服务好，价格高</p>'
				+'</div></div>';
			});
			$('.useRecord').append(list);
		};
	}
	AjaxRequest(u,d,f);
}
