//获取游戏列表
function getgames(code,id){
	var d = 'code='+code+'&id='+id;
	var u = '/index.php/activity/games/getgames';
	var f = function(res){
	    var response = eval(res);
	    if (response['status'] == request_succ) {
	        var list = '';
	        if (response['data']['beach']==false) {
	        	var stage = 0;
	        	var num = 0;
	        }else{
	        	var stage = response['data']['beach']['stage']['0']['stage'];
	        	var num = response['data']['beach']['allnum'];
	        }
			if(response['data']['luck'] == 1){
				$(".container .munch").html('本次免费');
			}else{
				$(".container .munch").html('30通花');
			}
			var ua = window.navigator.userAgent.toLowerCase();
			if(ua.match(/MicroMessenger/i) == 'micromessenger'){
				var iswx = 1;
			}else{
				var iswx = 0;
			}
	        $.each(response['data']['games'], function(i, v) {
	        	if (v['gid']==3&&iswx==1) {
	        		var beachlist = '<div class="lcon">'
                                    +'<div class="game-icon"><img src="/static/games/images/gamelist/plage.png"></div>'
                                +'</div>'
                                +'<div class="playing">'
                                    +'<div class="playing-game">'
                                        +'<div class="munch">免费</div>'
                                        +'<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Fdi%2Findex.html&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect"><div class="play-btn">开始玩</div></a>'
                                    +'</div>'
                                +'</div>'
                                +'<div class="explain">'
                                    +'<div class="information">'
                                        +'<div class="title">'
                                            +'<span class="game-name">海滩英雄</span>'
                                        +'</div>'
                                        +'<div class="rulecontent">一款射击类的游戏,本款游戏讲述的是...</div>'
                                        +'<div class="gameplayer">'
                                            +'<div class="player"><span>参与人数</span><span class="number"> '+num+'</span></div>'
                                            +'<div class="record" style="padding-left:0;"><span style="margin-left:5px;">当前关卡</span><span class="score"> '+stage+'</span></div>'
                                        +'</div>'
                                    +'</div>'
                                +'</div>';
                    $('.beachc').html(beachlist);
                    return true;
	        	}else if(v['gid']==3){
	        		$('#beach').remove();
	        		return true;
	        	}
			    if (v['tnum']>=v['freenum']) {
			    	var need = v['needinter']+'通花/次';
			    }else{
			        var need = '本次免费';
			    }
			    if (v['man']-v['tman']>0) {
			    	var tong = Math.ceil(v['tman']/v['ex']);
			    }else{
			    	var tong = Math.ceil(v['man']/v['ex']);
			    }
			    list += '<div class="box"><div class="container"><div class="lcon">'
                     +'<div class="game-icon"><img src="'+v['img']+'"></div>'
                     +'</div><div class="playing">'
                     +'<div class="playing-game"><div class="munch">'
                     +need+'</div><a href="'+v['url']+'"><div class="play-btn">开始玩</div></a></div></div>'
                     +'<div class="explain"><div class="information">'
                     +'<div class="title"><span class="game-name">'+v['name']+'</span><span class="gamerule"></span></div>'
                     +'<div class="rulecontent">'+v['text']+'</div>'
                     +'<div class="gameplayer"><div class="player"><span>已玩</span><span class="number">'+v['playnum']+'次</span>'
                     +'</div><div class="record"><span>本人最高纪录</span><span class="score">'+v['lman']+'分</span>'
                     +'</div></div></div></div></div>'
                     +'<div class="news"><div class="news_ng"><div class="best"><div class="fraction">'
                     +'<span>我的今日最佳：</span><span>'+v['tman']+'分</span></div></div></div>'
                     +'<div class="news_nf"><div class="reward"><div class="tips"><span>我的今日奖励：</span><span>'+tong+'通花</span>'
                     +'</div></div></div></div></div>';
	            });
	            $('.gamelist').append(list);
	    }else{
	    	alert(response['msg']);
	    	if (response['url']!='') {
	    		location.href = response['url'];
	    	};
	    };
	};
	AjaxRequest(u,d,f);	
}
var code=getUrlParam('code');
var id = getUrlParam('openid'); 
getgames(code,id);