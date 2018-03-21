var userEx = '';
var isShareA=0;
function getaskInfo(id){
	var u = '/index.php/task/taskspecific/task_detail';
    var d = 'tid='+id;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			isShareA=response['data']['getonetask']['0']['isShareAdd'];
			userEx=response['data']['getonetask']['0']['extend_num'];
		    switch(response['data']['getonetask']['0']['task_id']){
		    	case '5':
		    	    var main_task_icon = 'game';
		    	    break;
		    	case '6':
		    	    var main_task_icon = 'guanzhu';
		    	    break;
		    	case '7':
		    	    var main_task_icon = 'knowledge2';
		    	    break;
		    	case '8':
		    	    var main_task_icon = 'shop';
		    	    break;
		    	case '9':
		    	    var main_task_icon = 'quote';
		    	    break;
		    	case '15':
		    	    var main_task_icon = 'game2';
		    	    break;
		    	case '16':
		    	    var main_task_icon = 'shopshare';
		    	    break;
		    	case '17':
		    	    var main_task_icon = 'regiter';
		    	    break;
		    	case '18':
		    	    var main_task_icon = 'xerfu';
		    	    break;
    	    	default:
    	    	    var main_task_icon = 'task_types'+response['data']['getonetask']['0']['task_type'];
		    	    break;
		    }
		    if (response['data']['getonetask']['0']['task_type']==9) {
		    	var main_task_icon = response['data']['getonetask']['0']['icon_img'];
		    };
		    $('.itPic').append('<img src="/static/task/task_two/img/'+main_task_icon+'.jpg">');
		    $('.itPro h3').html(response['data']['getonetask']['0']['info_name']);
		    $('.itPro p').html(response['data']['getonetask']['0']['reward_content']);
		    if (response['data']['getonetask']['0']['task_id']==16) {
		    	shopinfo(response['data']);
		    	return ;
		    };
		    if (typeof(response['data']['getonetask']['0']['get_rewards'])!='undefined'&&response['data']['getonetask']['0']['get_rewards']!='') {
			    $('.itPro').after('<a onclick="getaward('+response['data']['getonetask']['0']['task_id']+')" class="itBut">领取奖励</a>');
			    $('#awards').after('<span class="isyes" onclick="commit('+response['data']['getonetask']['0']['taskid']+')">确 认</span>');
		    }else if(response['data']['getonetask']['0']['task_type']==3||response['data']['getonetask']['0']['task_id']==6){
			    if (typeof(response['data']['process'])!='undefined'&&response['data']['process']==4) {
			    	$('.itPro').after('<a href="#" class="itBut qushe">已完成</a>');
			    }else{
			    	$('.itPro').after('<a href="#" class="itBut qushe">未完成</a>');
			    }
		    }else if(response['data']['getonetask']['0']['task_type']==9){
			    $('.itPro').after('<a onclick="avdsclick('+response['data']['getonetask']['0']['task_id']+')" class="itBut">'+response['data']['getonetask']['0']['process_name']+'</a>');
		    }else if(response['data']['getonetask']['0']['task_type']==5){
		    	$('.choice-box .classify').eq(0).attr('tourl',response['data']['getonetask']['0']['url']);
		    	$('.choice-box .classify').eq(1).attr('tourl',response['data']['getonetask']['0']['urlT']);
		    	$('.itPro').after('<a href="javascript:void()" onclick="inviteSelect()" class="itBut">'+response['data']['getonetask']['0']['process_name']+'</a>');
		    	$('.tsTing').after('<div class="txBut"><a href="javascript:void()" onclick="inviteSelect()" style = "color:#fff"><button id="but-06">'+response['data']['getonetask']['0']['process_name']+'</button></a></div>');
		    	$('.miaox').find('p').eq(1).html(response['data']['getonetask']['0']['task_content']);
			    $('.tsTit').html('任务流程');
			    if (response['data']['task_type']!=0) {
			        $('.instruction_content').html(htmlDecode(response['data']['task_type']['0']['process']));
			    };
		    	return ;
		    }else{
		    	$('.itPro').after('<a href="'+response['data']['getonetask']['0']['url']+'" class="itBut">'+response['data']['getonetask']['0']['process_name']+'</a>');
		    }
		    if (response['data']['getonetask']['0']['task_type']==3) {
		    	var list = '<ul>';
		    	$.each(response['data']['taskIntroduction'], function(k, v) {
		    		list += '<li><a href="'+v['share_url']+'">'+v['instruction_name']+'</a></li>';
		    	});
		    	list += '</ul>';
		    	$('.list-v2').html(list);
		    	$('.tsTing').after('<div class="list-v2 m10"><h2>请选择您要宣传的文章</h2>'+list+'</div>');
		    }else if(response['data']['getonetask']['0']['task_id']==6){
		    	$('.list-v2').html('<img style="width:100%" src="/static/task/images/guangzhu.jpg" alt="" />');
		    }
		    $('.miaox').find('p').eq(1).html(response['data']['getonetask']['0']['task_content']);
		    if (typeof(response['data']['process'])!='undefined'&&response['data']['process']==4) {
		    	return ;
		    };
		    $('.tsTit').html('任务流程');
		    if (response['data']['task_type']!=0) {
		        $('.instruction_content').html(htmlDecode(response['data']['task_type']['0']['process']));
		    };
		    if (response['data']['getonetask']['0']['task_type']!=3&&typeof(response['data']['getonetask']['0']['get_rewards'])=='undefined') {
		    	if (response['data']['getonetask']['0']['task_type']==9) {
		    	    $('.tsTing').after('<div class="txBut"><a onclick="avdsclick('+response['data']['getonetask']['0']['task_id']+')" style = "color:#fff"><button id="but-06">'+response['data']['getonetask']['0']['process_name']+'</button></a></div>');
		    	}else{
		    	    $('.tsTing').after('<div class="txBut"><a href="'+response['data']['getonetask']['0']['url']+'" style = "color:#fff"><button id="but-06">'+response['data']['getonetask']['0']['process_name']+'</button></a></div>');
		        }
		    };
		}else{
			if (response['msg']!='') {
    			alert(response['msg']);
			};
			if (response['url']!='') {
    			location.href = response['url'];
			};
		}
    }
    AjaxRequest(u,d,f);
}
function inviteSelect(){
	$('.shade').css('display', 'block');
	$('.choice-box').css('display', 'block');
}
function tourl(){
	var tourl = $('.choice-box .active').attr('tourl');
	if (tourl==undefined) {
		return ;
	};
	UrlGoto(tourl);
}
//商品提成的任务信息
function shopinfo(data){
	$('.invtDiv .itPro p').remove();
	var time = '';
	if(data['shoptake']['closetime']>86400){
		var time = parseInt(data['shoptake']['closetime']/(3600*24))+'天后可提取';
	}else if(data['shoptake']['closetime']>3600){
		var time = parseInt(data['shoptake']['closetime']/(3600))+'小时后可提取';
	}else if(data['shoptake']['closetime']>60){
		var time = parseInt(data['shoptake']['closetime']/(3600))+'分钟后可提取';
	}
	if (data['shoptake']==false) {
	    $('.invtDiv .itPro').append('<div>可提取资金：<span class="cantake" style="color:red">0</span>元'
		                        +'<br>待提取资金：<span style="color:red">0</span>元</div>');
	}else{
	    $('.invtDiv .itPro').append('<div>可提取资金：<span class="cantake" style="color:red">'+data['shoptake']['cantake']+'</span>元'
		                        +'<br>待提取资金：<span style="color:red">'+data['shoptake']['havetake']+'</span>元 '+time+'</div>');
	}
	if (data['shoptake']['cantake']>0) {
	    $('.itPro').after('<a onclick="shopreward()" class="itBut">提取资金</a>');
		$('.tsTing').after('<div class="txBut"><a onclick="shopreward()" style = "color:#fff"><button id="but-06">提取资金</button></a></div>');
	}else{
		$('.itPro').after('<a href="'+data['getonetask']['0']['url']+'" class="itBut">'+data['getonetask']['0']['process_name']+'</a>');
		$('.tsTing').after('<div class="txBut"><a href="'+data['getonetask']['0']['url']+'" style = "color:#fff"><button id="but-06">'+data['getonetask']['0']['process_name']+'</button></a></div>');
	}
	$('.miaox').find('p').eq(1).html(data['getonetask']['0']['task_content']);
	$('.tsTit').html('任务流程');
	if (data['task_type']!=0) {
	    $('.instruction_content').html(htmlDecode(data['task_type']['0']['process']));
	};
}
function getaward(id){
	var u = '/index.php/task/task_detail/rewardInfo';
	var d = 'tid='+id;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			var list = '';
			var num = response['data']['rewards'].length;
			$.each(response['data']['rewards'], function(k, v) {
				if (v['reward_bonus']>0) {
					list += '<li class="red" onclick="select(this)" num='+v['reward_id']+'><p>';
					list += v['reward_bonus']+'奖金+';
					if (v['reward_fund']>0) {list += v['reward_fund']+'基金+'};
					if (v['reward_all_integral']>0) {list += v['reward_all_integral']+'成长值+'};
					if (v['reward_integral']>0) {list += v['reward_integral']+'通花+'};
					list=list.substring(0,list.length-1);
					list += '</p></li>';
				}else{
					list += '<li class="flour" onclick="select(this)" num='+v['reward_id']+'><p>';
					if (v['reward_fund']>0) {list += v['reward_fund']+'基金+'};
					if (v['reward_all_integral']>0) {list += v['reward_all_integral']+'成长值+'};
					if (v['reward_integral']>0) {list += v['reward_integral']+'通花+'};
		            list=list.substring(0,list.length-1);
					list += '</p></li>';
				}
		    });
		    $('#awards').html(list);
		    if (num == 1) {
		    	$('#awards li').addClass('selected');
		    	commit(id);
		    	//return '';
		    };
		    $('.grayBg,.rwdSelect').slideDown(200);
		}else{
			alert(response['msg']);
			location.href = '/index.php/task/usercenter/taskcenter';
		}
    }
	AjaxRequest(u,d,f);
}
function commit(id){
    var num = $('#awards').children().length;
    var award = '';
    for (var i = 0; i < num; i++) {
    	var selected = $('#awards').children().eq(i).hasClass('selected');
    	if (selected == true) {
    	    var a = $('#awards').children().eq(i).attr('num');
    	    award += (a+',');
    	};
    };
    if (award=='') {return ;};
    var u = '/index.php/task/task_detail/obainaward';
    var d = 'id='+id+'&award='+award;
    var f = function(res){
		var response = eval(res);
		var text = '';
		if (response['status']==request_succ) {
			text += '恭喜您获得 ';
			if (response['data']['str']['add_all_intergral']>0) {text += response['data']['str']['add_all_intergral']+'成长值+';}
			if (response['data']['str']['add_integral']>0) {text += response['data']['str']['add_integral']+'通花+';}
			if (response['data']['str']['add_fund']>0) {text += response['data']['str']['add_fund']+'元基金+';}
			if (response['data']['str']['add_bonus']>0) {text += response['data']['str']['add_bonus']+'元奖金+';}
			if (isShareA==1) {
				var url = '<a href="/index.php/task/otherget/getothersaytwo?extendnum='
					+userEx+'_10_'+id+'"><div class="prizes">领取随机倍数奖励</div><div class="draw">呼喊小伙伴点链接即可领取</div></a>';
				var sn = '<a href="/index.php/task/otherget/getothersaytwo?extendnum='
					+userEx+'_10_'+id+'></a>'
			}else{
				var url = '<a href="/index.php/task/usercenter/taskcenter"><div class="prizes">领取随机倍数奖励</div><div class="draw">呼喊小伙伴点链接即可领取</div></a>';
				var sn = '<a href="/index.php/task/usercenter/taskcenter"></a>'
			}
		    text=text.substring(0,text.length-1);
			$('.reawards').html(text);
			$('.reawards').after(url);
			$(".botcon").html(sn);
			$('.itBut').remove();
			$('.itPro').after('<a href="#" class="itBut qushe">已完成</a>');
			if(!$('.rwdSelect ul li').hasClass('selected')){
	        	return false;
	        }else{
	        	$('.grayBg,.rwdSuccess').slideDown(200);
	        	$('.rwdSelect').slideUp(200);
	        }
		}else{
			var text = response['msg'];
			$('.reawards').html(text);
			$(".rwdSuccess .topcon").addClass("false");
			var url = '<a href="/index.php/task/usercenter/taskcenter"><div class="prizes">领取随机倍数奖励</div><div class="draw">呼喊小伙伴点链接即可领取</div></a>';
			var sn = '<a href="/index.php/task/usercenter/taskcenter"></a>';
			$('.reawards').after(url);
			$(".botcon").html(sn);
	        $('.grayBg,.rwdSuccess').slideDown(200);
	        $('.rwdSelect').slideUp(200);
		}
    };
	AjaxRequest(u,d,f);
}
//商品提成
function shopreward(){
    var u = '/index.php/task/finishtask/getdivide';
    var d = '';
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			var url = '<a href="/index.php/nonstandard/center/ViewCenter">去个人中心提现</a>';
			text = '恭喜您获得 '+(response['data']['cantake']/100)+'元奖金';
			$('.topcon h3').html('提取成功啦！');
			$('.reawards').html(text);
			$('.reawards').after(url);
			$('.cantake').html('0');
	        $('.grayBg,.rwdSuccess').slideDown(200);
	        $('.rwdSelect').slideUp(200);
		}else{
			$('.topcon h3').html('领奖失败！');
			var text = response['msg'];
			$('.reawards').html(text);
			var url = '<a href="/view/shop/list.html">去通花商城换购礼品</a>';
			$('.reawards').after(url);
	        $('.grayBg,.rwdSuccess').slideDown(200);
	        $('.rwdSelect').slideUp(200);
		}
    }
	AjaxRequest(u,d,f);
}
//广告跳转
function avdsclick(id){
    var u = '/index.php/task/otherget/advs';
    var d = 'id='+id;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			UrlGoto(response['url']);
		}
    }
	AjaxRequest(u,d,f);
}
function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}
//编码转换
function htmlDecode(str) {
	return str.replace(/&#(x)?([^&]{1,5});?/g, function($, $1, $2) {
		return String.fromCharCode(parseInt($2, $1 ? 16 : 10));
	});
};