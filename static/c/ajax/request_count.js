$('document').ready(function(){
	getcount();
});
function getcount(){
	var u='/index.php/center/statistics/backinfo';
	var d='';
	var f=function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        $('.laydate-icon').val(res['data']['day']);
	        $('#start').html(res['data']['day']);
	        $('.selected').html(response['data']['name']);
	        $('.systemData').addClass('whiteBg');
	        $('.systemData ul li').eq(0).addClass('borderLeft');
	        var list = '<div class="dataList"><p class="title">用户统计</p><div class="dataLine clearfix">'
	                +'<span class="blue6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="blue3">加入人数</i><p>'+response['data']['login_n']['joinum']+'</p></li>'
	                +'<li><i class="blue3">登录人数</i><p>'+response['data']['login_n']['loginum']+'</p></li>'
	                +'</ul></div></div>'
	                +'<div class="dataList"><p class="title">交易数据</p><div class="dataLine clearfix">'
	                +'<span class="orange6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="orange3">订单数</i><p>'+response['data']['order_n']['order_count']+'</p></li>'
					+'<li><i class="orange3">订单总额</i><p>'+response['data']['order_n']['order_sum']+'</p></li></ul></div></div>'
					+'<div class="dataList"><p class="title">商城统计数据</p><div class="dataLine clearfix">'
	                +'<span class="yellowgreen6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="yellowgreen3">订单数</i><p>'+response['data']['tong_order']+'</p></li></ul></div></div>'
					+'<div class="dataList"><p class="title">任务统计数据</p><div class="dataLine clearfix">'
	                +'<span class="red6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="red3">回收任务</i><p>'+response['data']['task']['turnover']+'</p></li>'
	                +'<li><i class="red3">邀请任务</i><p>'+response['data']['task']['invite_u']+'</p></li>'
	                +'<li><i class="red3">游戏任务</i><p>'+response['data']['task']['game']+'</p></li></ul></div></div>';
	        $('.dataConBot').html(list);
		}else{
            alert(response['msg']);
            location.href=response['url'];
		}
	}
	AjaxRequest(u,d,f);
}
function datecount(date){
    var u='/index.php/center/statistics/dateback';
    var star = $('#start').html();
    var end = $('#end').html();
    if (end == '结束日') {end = '';}
    var d='date='+date+'&star='+star+'&end='+end;
    if (date == 'limit' && end== '' ) {
        alert('请开始与结束输入日期!');
        return ;
    };
    $('.dataConBot').html('');
    var f=function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
    	    var list = '<div class="dataList"><p class="title">用户统计</p><div class="dataLine clearfix">'
	                +'<span class="blue6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="blue3">加入人数</i><p>'+response['data']['login_n']['joinum']+'</p></li>'
	                +'</ul></div></div>'
	                +'<div class="dataList"><p class="title">交易数据</p><div class="dataLine clearfix">'
	                +'<span class="orange6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="orange3">订单数</i><p>'+response['data']['order_n']['order_count']+'</p></li>'
					+'<li><i class="orange3">订单总额</i><p>'+response['data']['order_n']['order_sum']+'</p></li></ul></div></div>'
					+'<div class="dataList"><p class="title">商城统计数据</p><div class="dataLine clearfix">'
	                +'<span class="yellowgreen6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="yellowgreen3">订单数</i><p>'+response['data']['tong_order']+'</p></li></ul></div></div>'
					+'<div class="dataList"><p class="title">任务统计数据</p><div class="dataLine clearfix">'
	                +'<span class="red6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="red3">回收任务</i><p>'+response['data']['task']['turnover']+'</p></li>'
	                +'<li><i class="red3">邀请任务</i><p>'+response['data']['task']['invite_u']+'</p></li>'
	                +'<li><i class="red3">游戏任务</i><p>'+response['data']['task']['game']+'</p></li></ul></div></div>'
					+'<div class="dataList"><p class="title">每日统计数据</p><div class="dataLine clearfix">'
	                +'<span class="blue6 fl"></span><ul class="fl clearfix">'
	                +'<li><i class="blue3">登陆人数</i><p>'+response['data']['every']['login']+'</p></li>'
	                +'<li><i class="blue3">分享人数</i><p>'+response['data']['every']['share']+'</p></li>'
	                +'<li><i class="blue3">第一次签到人数</i><p>'+response['data']['every']['sign']+'</p></li></ul></div></div>';
	        $('.dataConBot').html(list);
	    }
    }
    AjaxRequest(u,d,f);
}
