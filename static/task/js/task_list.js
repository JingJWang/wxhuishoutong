var have_inviter = 0;
var Gresponse = '';
function getasklist(){
	var u = '/index.php/task/lists/tasklist';
	var d = '';
	var f=function(res){
		var response = eval(res);
		Gresponse = response;
		alltasksort(response,0);
		
	}
	AjaxRequest(u,d,f);
}
function alltasksort(response,rnum){
	var lists='',main_task_icon='',main_name='',taskClass = '',hard='';
	var fiele_name = '',uer_have_num,num;
	var ua = window.navigator.userAgent.toLowerCase();
	var iswx = (ua.match(/MicroMessenger/i) == 'micromessenger')?1:0;
	if (typeof(response['data']['nofinishlist']['main']) == 'undefined' ) {response['data']['nofinishlist']['main']=''};
	if (typeof(response['data']['nofinishlist']['other']) == 'undefined' ) {response['data']['nofinishlist']['other']=''};
	if (typeof(response['data']['finishlist']) == 'undefined' ) {response['data']['finishlist']=''};
	if (typeof(response['data']['nofinishlist']['advs']) == 'undefined' ) {response['data']['nofinishlist']['advs']=''};
	if (typeof(response['data']['nofinishlist']['votes']) == 'undefined' ) {response['data']['nofinishlist']['votes']=''};
	$.each(response['data']['nofinishlist']['other'], function(k, v) {
		if (rnum!=0) {
			if(rnum != v['rety']&&v['rety']!=3){return true;}
		};
		if (response['data']['sign_task']=='1' && v['task_type']=='1') {return true;}
		fiele_name = response['data']['task_types_data'][v['task_type']];
		if (typeof(v['type_max']) != "undefined") {
			uer_have_num = response['data']['usertasktype'][fiele_name]-v['type_max'];
			num = v[fiele_name]-v['type_max'];
		}else{
			uer_have_num = response['data']['usertasktype'][fiele_name];
			num = v[fiele_name];
		}
		hard = tasklevel(v['hard']);
		if (v['task_process']==3||uer_have_num>=num) {
			main_name = '去领奖';
			uer_have_num = num;
		}else{
			switch(v['task_type']){
				case '1':
					main_name = '去签到';
					taskClass = 'aj_task_1';
					break;
				case '2':
					main_name = '首页交易';
					taskClass = 'aj_task_2';
					break;
				case '3':
					main_name = '查看任务';
					taskClass = 'aj_task_3';
					break;
				case '5':
					main_name = '去邀请';
					taskClass = 'aj_task_5';
					break;
				case '6':
					main_name = '去邀请';
					taskClass = 'aj_task_6';
					break;
			}
		}
		main_task_icon='task_types'+v['task_type'];
		if (uer_have_num>=num && v['task_type']==5) {
		    if (typeof(v['type_max']) != "undefined") {
			    have_inviter = v['type_max'];
		    }
			taskClass = 'aj_task_5';
			checktask(v['task_id']);
			lists += '<li class="'+taskClass+'"><a href="/view/task/taskDetail.html?id='+v['task_id']+'"><div class="pic"><span>'+'</span><img src="/statictask/task_two/img/'+main_task_icon+'.jpg"></div><div class="con"><h3>'+v['info_name']+'</h3><div class="state">'+hard+'<div class="award">奖励：'+v['reward_content']+'</div></div><p>'+v['task_content']+'</p></div><div class="but"><span class="but-01"></span></div><div class="clear"></div></a></li>';
		}else{
		    lists += '<li class="'+taskClass+'"><a href="/view/task/taskDetail.html?id='+v['task_id']+'"><div class="pic"><span>'+uer_have_num+'/'+num+'</span><img src="/static/task/task_two/img/'+main_task_icon+'.jpg"></div><div class="con"><h3>'+v['info_name']+'</h3><div class="state">'+hard+'<div class="award">奖励：'+v['reward_content']+'</div></div><p>'+v['task_content']+'</p></div><div class="but"><span class="but-01">'+main_name+'</span></div><div class="clear"></div></a></li>';
		}
	});
	lists+='<div class="classbtn"><input class="abtn" type="button" value="向上滑继续查看"></div>';
	$.each(response['data']['nofinishlist']['main'], function(k, v) {
		if (rnum!=0) {
			if(rnum != v['rety']||v['rety']==3){return true;}
		};
		hard = tasklevel(v['hard']);
		uer_have_num = '';
       	switch(v['task_id']){
       		case '5':
       			if (iswx!=1) {
       				return false;
       			};
       			main_task_icon = 'game';
       			main_name = '去玩游戏';
                   break;
       		case '6':
       			main_task_icon = 'guanzhu';
       			if (v['task_have_finish']==1) {
       				main_name = '去领奖';
       			}else{
       				main_name = '去关注';
       			}
                   break;
       		case '7':
       		    if(response['data']['usertaskinfo']['0']['center_klegdtime']>response['data']['usertaskinfo']['0']['strtotime']
       		    	&&v['task_process']!=3){
       		    	return true;
       		    }
       		    (v['task_process']==3)?uer_have_num = '<span>1/1</span>':uer_have_num = '<span>0/1</span>';
       			main_task_icon = 'knowledge2';
       			main_name = '去攻略库';
                   break;
       		case '8':
       		    (v['task_process']==3)?uer_have_num = '<span>1/1</span>':uer_have_num = '<span>0/1</span>';
       			main_task_icon = 'shop';
       			main_name = '去买商品';
                   break;
       		case '9':
       			main_task_icon = 'quote';
       		    main_name = '体验报单';
       		    (v['task_process']==3)?uer_have_num = '<span>1/1</span>':uer_have_num = '<span>0/1</span>';
                break;
       		case '15':
        	    if(response['data']['usertaskinfo']['0']['center_plgametime']>response['data']['usertaskinfo']['0']['strtotime']
        	    	&&v['task_process']!=3){
        	    	return true;
        	    }
        		main_task_icon = 'game2';
        		main_name = '去玩游戏';
        	    (v['task_process']==3)?uer_have_num = '<span>1/1</span>':uer_have_num = '<span>0/1</span>';
                   break;
        	case '16':
        		main_task_icon = 'shopshare';
        		main_name = '去帮忙';
                   break;
        	case '17':
        		main_task_icon = 'regiter';
        		main_name = '去注册';
                   break;
        	case '18':
        		main_task_icon = 'xerfu';
        		main_name = '去注册';
                   break;
            default:
        		main_task_icon = 'task_types7';
                break;
        }
        	if(v['task_process']==3){
       	    	main_name = '去领奖';
        }
       	lists += '<li class="btnhid"><a href="/view/task/taskDetail.html?id='+v['task_id']+'"><div class="pic">'+uer_have_num+'<img src="/static/task/task_two/img/'+main_task_icon+'.jpg"></div><div class="con"><h3>'+v['info_name']+'</h3><div class="state">'+hard+'<div class="award">奖励：'+v['reward_content']+'</div></div><p>'+v['task_content']+'</p></div><div class="but"><span class="but-01">'+main_name+'</span></div><div class="clear"></div></a></li>';
	});
	$.each(response['data']['nofinishlist']['advs'], function(k, v) {
		if (rnum!=0) {
			if(rnum != v['rety']||v['rety']==3){return true;}
		};
		hard = tasklevel(v['hard']);
		main_name = response['data']['c_task'][v['task_id']]['icon'];
        uer_have_num = '<span>0/1</span>';
		main_task_icon=response['data']['c_task'][v['task_id']]['img'];
       	if(v['task_process']==3){
           	main_name = '去领奖';
       		uer_have_num = '<span>1/1</span>';
        }
        lists += '<li class="btnhid"><a href="/view/task/taskDetail.html?id='+v['task_id']+'"><div class="pic">'+uer_have_num+'<img src="/static/task/task_two/img/'+main_task_icon+'.jpg"></div><div class="con"><h3>'+v['info_name']+'</h3><div class="state">'+hard+'<div class="award">奖励：'+v['reward_content']+'</div></div><p>'+v['task_content']+'</p></div><div class="but"><span class="but-01">'+main_name+'</span></div><div class="clear"></div></a></li>';
	});
	$.each(response['data']['nofinishlist']['votes'], function(k, v) {
		if (rnum!=0) {
			if(rnum != v['rety']||v['rety']==3){return true;}
		};
		hard = tasklevel(v['hard']);
        if(v['task_process']==3){
       		main_name = '去领奖';
        	uer_have_num = '<span>1/1</span>';
        }else{
           	main_name = '去投票';
      	    	uer_have_num = '<span>0/1</span>';
        }
		main_task_icon='task_types'+v['task_type'];
       	lists += '<li class="btnhid"><a href="/view/task/taskDetail.html?id='+v['task_id']+'"><div class="pic">'+uer_have_num+'<img src="/static/task/task_two/img/'+main_task_icon+'.jpg"></div><div class="con"><h3>'+v['info_name']+'</h3><div class="state">'+hard+'<div class="award">奖励：'+v['reward_content']+'</div></div><p>'+v['task_content']+'</p></div><div class="but"><span class="but-01">'+main_name+'</span></div><div class="clear"></div></a></li>';
	});
	$('.tasklist').html(lists);
	$('#but-03').remove();
}
function checktask(id){
	var u = '/index.php/task/taskspecific/checktaskplan';
	var d = 'id='+id;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			if (response['data']['now']>=response['data']['need']) {
				var uer_have_num = response['data']['need']-have_inviter;
				$('.aj_task_5 .pic span').html(uer_have_num+'/'+uer_have_num);
				$('.aj_task_5 .but span').html('去领奖');
			}else{
				var uer_have_num = response['data']['need']-have_inviter;
				var num = response['data']['now']-have_inviter;
				$('.aj_task_5 .pic span').html(num+'/'+uer_have_num);
				$('.aj_task_5 .but span').html('去邀请');
			}
		};
    }
	AjaxRequest(u,d,f);
}

//切换任务分类
$(".motif").click(function(){
	$(".motif.active").removeClass("active");
	$(this).addClass("active");
});

$(document).bind('scroll', function (e){
	var t = $("body").scrollTop();  //获取滚动距离
	var st = $(".locate").offset().top;
	if(parseInt(st - t) <= 0){
		$(".mission").addClass("fix");
	}else{
		$(".mission").removeClass("fix");
	}
});

function taskSort(id){
	if (id==3) {
		var lists = '';
		$.each(Gresponse['data']['finishlist'], function(k, v) {
			switch(v['task_type']){
				case '1':
					main_name = '去签到';
					break;
				case '2':
					main_name = '首页交易';
					break;
				case '3':
					main_name = '查看任务';
					break;
				case '5':
					main_name = '去邀请';
					break;
				case '6':
					main_name = '去邀请';
					break;
			}
			switch(v['task_id']){
				case '5':
					main_task_icon = 'game';
					break;
				case '6':
					main_task_icon = 'guanzhu';
					break;
       			case '7':
       				main_task_icon = 'knowledge2';
					break;
       			case '8':
       				main_task_icon = 'shop';
					break;
       			case '9':
       				main_task_icon = 'quote';
					break;
       			case '15':
        			main_task_icon = 'game2';
					break;
       			case '16':
        			main_task_icon = 'shopshare';
					break;
       			case '17':
        			main_task_icon = 'regiter';
					break;
       			case '18':
        			main_task_icon = 'xerfu';
					break;
				default:
					main_task_icon = 'task_types'+v['task_type'];
					break;
			}
			if (v['task_type']==9) {
				main_task_icon=Gresponse['data']['c_task'][v['task_id']]['img'];
			};
			hard = tasklevel(v['hard']);
			lists += '<li><a href="/view/task/taskDetail.html?id='+v['task_id']+'"><div class="pic"><img src="/static/task/task_two/img/'+main_task_icon+'.jpg"></div><div class="con"><h3>'+v['info_name']+'</h3><div class="state">'+hard+'<div class="award">奖励：'+v['reward_content']+'</div></div><p>'+v['task_content']+'</p></div><div class="but"><span class="but-02">已领奖</span></div><div class="clear"></div></a></li>';
		})
		$('.tasklist').html(lists);
	};
	if (id==1) {
		alltasksort(Gresponse,num=1);
	};
	if (id==2) {
		alltasksort(Gresponse,num=2);
	};
	if (id==0) {
		alltasksort(Gresponse,num=0);
	};
}
function tasklevel(num){
	if (num == 1) {
		return '<div class="grade">简单</div>'
	};
	if (num == 2) {
		return '<div class="grade plain">普通</div>';
	};
	if (num == 3) {
		return '<div class="grade hard">困难</div>';
	};
	return '';
}