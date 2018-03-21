function Getbrands(){
	var u = '/index.php/mall/querys/luxurys';
	var d = '';
	var f = function(res){
		var response = eval(res);
		if(response['status'] == request_succ){
			var list='';
			$.each(response['data'],function(k,v){
				list += '<li id="ty'+v['id']+'" class="types" data-num="'+v['id']+'"><a href="javascript:;">'+v['name']+'</a></li>';
			});
			$('.picList ul').html(list);
		}else{
			alert(response['msg']);
		}
		$('.picList').find('ul li').first().click();
	}
	AjaxRequest(u,d,f);
}
$(function(){
	$(document).on('click',".brandname",function(){
		var obj = $(this);
		var id=obj.attr('data-num');
		if (id==''|| isNaN(id)) {
			location.reload();
			return '';
		};
		var u = '/index.php/mall/querys/luxurybrand';
		var d = 'id='+id;
		var f = function(res){
			var response = eval(res);
			if (response['status'] == request_succ) {
				var list='<ul>';
				$.each(response['data'],function(k,v){
					list += '<li><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Findex.php/weixin/wxmap/addresslist&response_type=code&scope=snsapi_base&state=aaa&connect_redirect=1#wechat_redirect" class="clearfix"><span class="logo fl"><img src="../../static/mall/img/brandimgs/'+v['image']+'" width="25px" height="25px"/></span><p class="fl">'+v['name']+'</p></a></li>';
				});
				list+='</ul>';
				$('.con').removeClass('deferOpen');
				$('.con').removeClass('searchNone');
				$('.con').html(list);
			}else{
				$('.con').addClass('deferOpen');
				$('.con').html('<p class="deferPic"></p>');
			}
		};
		AjaxRequest(u,d,f);
	});
});
function Brandsearch(){
	var text = $('#keysword').val();
	var type = $('.current').val();
	var u = '/index.php/mall/querys/seachbrand';
	var d = 'text='+text+'&type='+type;
	var f = function(res){
		var response = eval(res);
		var list = '';
		if (response['status'] == request_succ) {
			var list='<ul>';
			$.each(response['data'],function(k,v){
				list += '<li><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Findex.php/weixin/wxmap/addresslist&response_type=code&scope=snsapi_base&state=aaa&connect_redirect=1#wechat_redirect" class="clearfix"><span class="logo fl"><img src="../../static/mall/img/brandimgs/'+v['image']+'" width="25px" height="25px"/></span><p class="fl">'+v['name']+'</p></a></li>';
			});
			list+='</ul>';
			$('.types').removeClass();
			$('.con').removeClass('deferOpen');
			$('.con').removeClass('searchNone');
			$('.con').html(list);
		}else{
			$('.con').addClass('searchNone');
			$('.con').html('<span class="pic"></span><p class="text">系统里没有查到您搜索的品牌，该品牌暂时不支持回收，敬请期待吧！！！</p>');
		}
	}
	if (text!='') {
		AjaxRequest(u,d,f);
	};
}
function orderSearch(){
	var number = $('#orderWord').val();
	var u = '/index.php/mall/querys/orderSeach';
	var d = 'number='+number;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			var list = '<div class="orderCon soCon"><div class="title"><p>订单状态信息</p></div><div class="orderAdd"><p><span>订单编号:</span>'+response['data']['0']['num']+'</p><p><span>商品名称:</span>'
			    +response['data']['0']['bdname']+'&nbsp&nbsp&nbsp&nbsp'+response['data']['0']['pname']+'</p><p><span>商品状态:</span>'+response['data']['0']['status_c']+'</p></div></div>';
			$('.topText').next().remove();
			$('.topText').after(list);
		}else{
			var list = '<div class="noDataCon"><div class="noDataBg"></div><p>暂时无此订单消息</p></div>';
			$('.topText').next().remove();
			$('.topText').after(list);
		}
	}
	AjaxRequest(u,d,f);
}