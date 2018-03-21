var allnum = {
	isload : true,//是否加载
    type : 0,//加载文章的类型（0为默认的全部）
    lastime : 0,//根据时间排序需要的参数
    sortnum : 1,//排序规则 1为时间 2为点击量
    contentnum : 0,//根据点击量排序的参数
    extendnum : '',//用户的要求码
    heatarticle : 0//本周最热门的文章
}
var Allabels = Array();//记录所有的标签
function sorttime(){
	if($(".timer").hasClass("desc")){
		return ;
	}else {					//否则的话它既不是升序，也不是降序，是默认的，所以需要添加成升序
		$(".timer").addClass("desc");
		$('.evalu').html('');
	    $('.full').html('正在加载').css('height','1000');
	    allnum.heatarticle = 0;
		getloadlist(allnum.type,1,0,0,0);
		allnum.sortnum = 1;
        allnum.lastime = 0;
        allnum.contentnum = 0;
        allnum.isload = true;//是否加载
	}
	$(".volume").removeClass("desc");
}

function sortnumber(){
	if($(".volume").hasClass("desc")){
		return ;
	}else{
		$(".volume").addClass("desc");
		$('.evalu').html('');
	    $('.full').html('正在加载').css('height','1000');
	    allnum.heatarticle = 0;
		getloadlist(allnum.type,2,0,0,0);
		allnum.sortnum = 2;
        allnum.lastime = 0;
        allnum.contentnum = 0;
        allnum.isload = true;//是否加载
	}
    $(".timer").removeClass("desc");
}
//获取标签
function getlabel(){
	var u = '/index.php/article/knowledge/getlabel';
	var d = '';
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			allnum.extendnum = response['data']['extendnum'];
			if (response['data']['isreg']==false) {$('.information').css('display','block')};
			var lnum = response['data']['label'].length+1;
			var list = '<div class="listIcon clearfix">';
			if (lnum<=5) {
				$.each(response['data']['label'], function(i, v) {
					list += '<a onclick="getlist('+v['id']+',1)" style = "background: url('+v['icon']+') no-repeat top center; background-size: 38px 38px;">'+v['name']+'</a>';
				});
				list += '<a onclick="getlist(0,1)" style = "background: url(/static/article/images/total.png) no-repeat top center; background-size: 38px 38px;">全部</a>';
				Allabels[v['id']] = v['name'];
			}else{
				var i = 0;
				$.each(response['data']['label'], function(i, v) {
					if (i<4) {
						list += '<a onclick="getlist('+v['id']+',1)" style = "background: url('+v['icon']+') no-repeat top center; background-size: 38px 38px;">'+v['name']+'</a>';
					}
					if (i==4) {
						list += '<a href="javascript:;" class="bgChange" onclick="showList(this)">更多</a></div><div class="listIcon listIconopen clearfix"></a>';
						list += '<a onclick="getlist('+v['id']+',1)" style = "background: url('+v['icon']+') no-repeat top center; background-size: 38px 38px;">'+v['name']+'</a>';
					}
					if (i>4) {
						list += '<a onclick="getlist('+v['id']+',1)" style = "background: url('+v['icon']+') no-repeat top center; background-size: 38px 38px;">'+v['name']+'</a>';
					};
					i++;
				    Allabels[v['id']] = v['name'];
				});
				list += '<a onclick="getlist(0,1)" style = "background: url(/static/article/images/total.png) no-repeat top center; background-size: 38px 38px;">全部</a>';
				    Allabels[0] = '全部';
			}
			list += '</div>';
			$('.classify').append(list);
			getlist(0,1);
		}else{
			alert(response['msg']);
			UrlGoto(response['url']);
		}
	}
	AjaxRequest(u,d,f);
}
function getlist(type){
	$('.evalu').html('');
    $('.full').html('正在加载').css('height','1000');
    $('.articletype').html('('+Allabels[type]+')');
	allnum.isload = true;
	allnum.type = type;
	var u = '/index.php/article/knowledge/getlist';
	var d = 'type='+type+'&sortby='+allnum.sortnum;
	var extendnum = (allnum.extendnum==undefined)?'':allnum.extendnum+'_';
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			$('.evalu').html('');
			var list = '';
			allnum.lastime = response['data']['article']['0']['time'];
            if (response['data']['heatar']!=false) {
				allnum.contentnum++;
				allnum.heatarticle = response['data']['heatar']['0']['id'];
            	var str = '';
				var strs=String(response['data']['heatar']['0']['label']).split(",");
				for(var j = 0;j<strs.length;j++){
                     str += '<div class="delicious fl">'+strs[j]+'</div>';
				};
				list += '<a href="/view/article/article.html?extendnum='+extendnum+response['data']['heatar']['0']['id']
				     +'"><div class="evaluList"><div class="article">'
					 +str+'<div class="TextOverflow">'+response['data']['heatar']['0']['name']+'</div></div>'
			         +'<div class="label clearfix"><p class="left fl">'+formatDate(response['data']['heatar']['0']['time'])+'</p>'
			         +'<p class="right fr">'+response['data']['heatar']['0']['sharenum']+'</p><p class="center fr">'
			         +response['data']['heatar']['0']['click']+'</p>'
			         +'</div></div></a>';
            };
			$.each(response['data']['article'], function(i, v) {
				allnum.contentnum++;
				if (response['data']['heatar']!=false&&v['id'] == response['data']['heatar']['0']['id']) {
					return true;//结束本次循环
				};
				var str = '';
				if (allnum.lastime>v['time']) {allnum.lastime=v['time']};//设置最后的时间
				var strs=String(v['label']).split(",");
				for(var j = 0;j<strs.length;j++){
                     str += '<div class="delicious fl">'+strs[j]+'</div>';
				};
				list += '<a href="/view/article/article.html?extendnum='+extendnum+v['id']
				     +'"><div class="evaluList"><div class="article">'
					 +str+'<div class="TextOverflow">'+v['name']+'</div></div>'
			         +'<div class="label clearfix"><p class="left fl">'+formatDate(v['time'])+'</p>'
			         +'<p class="right fr">'+v['sharenum']+'</p><p class="center fr">'+v['click']+'</p>'
			         +'</div></div></a>';
			});
			if (response['data']['article'].length<20) {
				allnum.isload = false;
				$('.full').html('没有文章了！');
			};
			$('.full').css('height', '35');
            $('.evalu').append(list);
		}else{
			$('.full').html('没有文章了！');
		}
	}
	AjaxRequest(u,d,f);
}
/**
 * 加载文章函数
 * @param        type        int         加载文章的类型（0为默认的全部）
 * @param        sortby      int         排序（时间或点击量）
 * @param        tlastime    int         根据时间排序需要的参数
 * @param        num         int         根据点击量排序的参数
 */
function getloadlist(type,sortby,tlastime,num){
    var u = '/index.php/article/knowledge/loadlist';
	var d = 'type='+type+'&lastime='+tlastime+'&sortby='+sortby+'&num='+num;
	var f = function(res){
		var response = eval(res);
		var extendnum = (allnum.extendnum==undefined)?'':allnum.extendnum+'_';
		if (response['status'] == request_succ) {
			var list = '';
			allnum.lastime = response['data']['article']['0']['time'];
			$.each(response['data']['article'], function(i, v) {
				if (allnum.heatarticle==v['id']) {return true;};
				allnum.contentnum++;
				if (allnum.lastime>v['time']) {allnum.lastime=v['time']};//设置最后的时间
				var str = '';
				var strs=String(v['label']).split(",");
				for(var j = 0;j<strs.length;j++){
                     str += '<div class="delicious fl">'+strs[j]+'</div>';
				};
				list += '<a href="/view/article/article.html?extendnum='+extendnum+v['id']
				     +'"><div class="evaluList"><div class="article">'
					 +str+'<div class="TextOverflow">'+v['name']+'</div></div>'
			         +'<div class="label clearfix"><p class="left fl">'+formatDate(v['time'])+'</p>'
			         +'<p class="right fr">'+v['sharenum']+'</p><p class="center fr">'+v['click']+'</p>'
			         +'</div></div></a>';
			});
			if (response['data']['article'].length<20) {
				allnum.isload = false;
				$('.full').html('没有文章了！');
			};
			$('.full').css('height', '35');
            $('.evalu').append(list);
		}else{
			$('.full').html('没有文章了！');
		};
	}
	AjaxRequest(u,d,f);
}
function seachtext(){
	var text = $('#seachtext').val();
	if (text=='') {
		return ;
	};
	$('.evalu').html('');
	$('.full').html('正在加载');
	var u = '/index.php/article/knowledge/search';
	var d = 'text='+text;
	var f = function(res){
		var response = eval(res);
		var extendnum = (allnum.extendnum==undefined)?'':allnum.extendnum+'_';
		if (response['status'] == request_succ) {
			var list = '';
			$.each(response['data']['info'], function(i, v) {
				var str = '';
				var strs=String(v['label']).split(",");
				for(var j = 0;j<strs.length;j++){
                     str += '<div class="delicious fl">'+strs[j]+'</div>';
				};
				list += '<a href="/view/article/article.html?extendnum='+extendnum+v['id']
				     +'"><div class="evaluList"><div class="article">'
					 +str+'<div class="TextOverflow">'+v['name']+'</div></div>'
			         +'<div class="label clearfix"><p class="left fl">'+formatDate(v['time'])+'</p>'
			         +'<p class="right fr">'+v['sharenum']+'</p><p class="center fr">'+v['click']+'</p>'
			         +'</div></div></a>';
			});
            $('.evalu').append(list);
			$('.full').html('没有文章了！');
			$('.full').css('height', '35');
		}else{
			$('.full').html('没有查到相关文章！');
		}
	}
	AjaxRequest(u,d,f);
}
//获取文章信息
function getarticle(extendnum){
	var url=encodeURIComponent(location.href.split('#')[0]);
    var u = '/index.php/article/knowledge/getarticle';
    var d = 'extendnum='+extendnum+'&code='+code+'&status=1&url='+url;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			if (response['data']['isreg']===false) {
                $(".shadow").css("display","block");
                var i="this.src='/codeimg/code_char.php?name=2&amp;d='+Math.random();";
                $('body').append('<div class="tips">'
                                    +'<div class="theme">'
                                        +'<div class="welfare">'
                                            +'<div class="close">'
                                                +'<a class="close-btn" href="javascript: closelo();">×</a>'
                                            +'</div>'
                                            +'<div class="title">'
                                                +'<span>注册回收通</span>'
                                            +'</div>'
                                            +'<div class="guide">做任务最低得3元现金</div>'
                                        +'</div>'
                                    +'</div>'
                                    +'<div class="include">'
                                        +'<div class="triangle"></div>'
                                    +'</div>'
                                    +'<div class="info">'
                                        +'<div class="interger">'
                                            +'<input type="hidden" id="invitation" value=""/>'
                                            +'<input type="text" id="mobile" placeholder="请输入手机号码" class="pane"/>'
                                           +'<input class="code" style="float:right;margin-top:8px;width:4.4rem;height:20px;border:0px;background-color:#F1F1F1;" value="获取验证码" onclick="Getcode(this);">'
                                        +'</div>'
                                        +'<div class="interger">'
                                        	+'<input type="" name="" id="imgcodej" value="" placeholder="请输入图形验证码" class="fl enter">'
                                        	+'<span class="fr chart"><img src="/codeimg/code_char.php" width="80px" onclick="'+i+'"  alt=""></span>'
                                        +'</div>'
                                        +'<div class="interger">'
                                            +'<input type="text" id="code" placeholder="请输入手机验证码" class="pane"/>'
                                        +'</div>'
                                        +'<div class="interger">'
                                            +'<input type="password" id="password" placeholder="请设置六位以上密码" class="pane don"/>'
                                            +'<div class="logo" ontouchstart="mima();"></div>'
                                        +'</div>'
                                    +'</div>'
                                    +'<div style="margin:0px 0 10px 20px;font-size:14px;">'
                                    	+'<a href="/index.php/nonstandard/system/Login">去登录</a>'
                                    +'</div>'
                                    +'<a class="atonce" onclick="arCheckcode(this);">立即注册</a>'
                                  +'</div>');
				var list = '<a class="btn" href="/index.php/task/usercenter/isreg"><span>注册就送3元现金及价值1200元游戏礼包</span></a>';
				$('.information').html(list);
			}else{
				var list = '<a class="btn" onclick="share();"><span>呼喊小伙伴签到得0.5元 点此参与</span></a>';
				$('.information').html(list);
			    if (response['data']['isub']===false) {
                    $(".shadow").css("display","block");
			    	$(".follow").css("display","block");
			    };
			}
			$('.fuzhi .url').html(response.data.signPackage.url);
			$('.addtext').html(response['data']['content']);
			var shareurl='http://wx.recytl.com/view/article/article.html?extendnum=c2c352_260';
			var wxshare='https://open.weixin.qq.com/connect/oauth2/authorize?appid='+response.data.signPackage.appId+'&redirect_uri='+shareurl+'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
			var content='<script type="text/javascript">'+
	            'wx.config({'+
	                'debug: false,'+
	                'appId: "'+response.data.signPackage.appId+'",'+
	                'timestamp: "'+response.data.signPackage.timestamp+'",'+
	                'nonceStr: "'+response.data.signPackage.nonceStr+'",'+
	                'signature: "'+response.data.signPackage.signature+'",'+
	                'jsApiList: [ '+  
	                    '"onMenuShareTimeline",'+
	                    '"onMenuShareAppMessage" '+  
	                ']'+
	            '});'+
	            'wx.ready(function () {'+	   
					'wx.checkJsApi({'+
						'jsApiList: ["onMenuShareTimeline"],'+ // 需要检测的JS接口列表，所有JS接口列表见附录2,
						'success: function(res) {'+
							// 以键值对的形式返回，可用的api值true，不可用为false
							// 如：{"checkResult":{"chooseImage":true},"errMsg":"checkJsApi:ok"}
						'}'+
					'});'+				
	                'wx.onMenuShareTimeline({'+
	                    'title: "'+response['data']['name']+'",'+
	                    'desc:  "'+response['data']['desc']+'",'+
						'imgUrl:"'+response['data']['img']+'",'+
	                    'link:  "'+response['data']['shareurl']+'",'+
	                    'trigger: function (res) {'+       
	                    '},'+
	                    'success: function (res) {'+
	                        'addsharenum();'+
	                        'alert("分享成功");'+	                         
	                    '},'+
	                    'cancel: function (res) {'+
	                    '},'+
	                    'fail: function (res) {'+
	                      'alert(JSON.stringify(res));'+
	                    '}'+
	                '});'+
	                'wx.onMenuShareAppMessage({'+
		                'title: "'+response['data']['name']+'",'+
	                    'desc:  "'+response['data']['desc']+'",'+
						'imgUrl:"'+response['data']['img']+'",'+
	                    'link:  "'+response['data']['shareurl']+'",'+
	                    'success: function () {'+
	                        'addsharenum();'+
	                        'alert("分享成功");'+
	                    '},'+
	                    'cancel: function () { '+
	                        // 用户取消分享后执行的回调函数
	                    '}'+
	                '});'+
	            '});'+
	            'wx.error(function(res){'+
	            '});'+
	    '</script>';		
			$('body').before(content);
		};
		
    }
	AjaxRequest(u,d,f);
}

//获取文章信息
function getarticleshare(extendnum){
    var u = '/index.php/article/knowledge/getarticle';
    var d = 'extendnum='+extendnum+'&code='+code+'&status=2';
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			if (response['data']['isreg']===false) {
				var list = '<a class="btn" href="/index.php/task/usercenter/isreg"><span>注册最高送3元现金</span></a>';
				$('.information').html(list);
			}else{
				var list = '<a class="btn" onclick="share();"><span>呼喊小伙伴签到得0.5元 点此参与</span></a>';
				$('.information').html(list);
			}
			// if (response['data']['isub']===false) {
   //              $(".shadow").css("display","block");
			// 	$(".follow").css("display","block");
			// };
			$('.addtext').html(response['data']['content']);
			AappId = response.data.signPackage.appId;
			Atimestamp = response.data.signPackage.timestamp;
			AnonceStr = response.data.signPackage.nonceStr;
			Asignature = response.data.signPackage.signature;
			window.shareData.timgUrl = response['data']['img'];
			window.shareData.tContent = response['data']['desc'];
			window.shareData.timeLineLink = response['data']['shareurl'];
			window.shareData.tTitle = response['data']['name'];
			sharetext();
		};
    }
	AjaxRequest(u,d,f);
}
//分享次数添加
function addsharenum(){
    var u = '/index.php/article/knowledge/aftershare';
    var d = 'extendnum='+extendnum;
    var f = function(res){
    }
	AjaxRequest(u,d,f);
}
/**
 * 校验验证码
 * @param int  mobile
 * @param int  code
 */
function arCheckcode(){
	var imgcode = $("#imgcodej").val();
	var  mobile=$("#mobile").val();
	var  Mreg=/^\d{11}$/; 
	if(!Mreg.test(mobile)){
		alert("手机号码为空或者格式不正确!");
		return false;
	}
	var  code=$("#code").val();
	var  Creg=/^\d{6}$/; 
	if(!Creg.test(code)){
		alert("验证码为空或者格式不正确!");
		return false;
	}
	var  invitation =  $("#invitation").val();
	var  password = $("#password").val();
	if(password == '' ){
		alert('密码为必填选项!');
		return false;
	}
    var u = '/index.php/nonstandard/system/binding_mobile';
    var d = "mobile="+mobile+"&code="+code+'&password='+password+'&invitation='+invitation+"&imgcode="+imgcode;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			alert('注册成功');
            $(".tips").css("display","none");
            $(".ceng").css("display","none");
			$(".follow").css("display","block");
			$('.information').html('<a class="btn" onclick="share();"><span>点击分享文章</span></a>');
		}
		if(response['status'] == request_fall){
			alert(response['msg']);
		}
    }
	AjaxRequest(u,d,f);
}
//时间转换  
function   formatDate(now)   {  
    if (now==0) {
    	return '-';
    };    
	var   now= new Date(now*1000);     
	var   year=now.getFullYear();     
	var   month=now.getMonth()+1;     
	var   date=now.getDate();     
	var   hour=now.getHours();      
	var   minute=now.getMinutes();     
	var   second=now.getSeconds();      
	return   year+"年"+fixZero(month,2)+"月"+fixZero(date,2)+"日    "+fixZero(hour,2)+":"+fixZero(minute,2)+":"+fixZero(second,2); 
}
//时间如果为单位数补0  
function fixZero(num,length){     
	var str=""+num;      
	var len=str.length;     
	var s="";      
	for(var i=length;i-->len;){         
		s+="0";     
	}      
	return s+str; 
}