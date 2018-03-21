//共用函数
var    request_fall  = 3000;
var    request_succ  = 1000;

var UrlGoto=function(url) {
	if(url != ''){
		location.href=url;
	}else{
		return '路径为空!';	
	}
}
var  AjaxRequest=function(u,d,f){
	var result= '';
	$.ajax({
        async: false,
        url:u,
        type:"POST",
        dataType:"json",
        data:d,
        beforeSend: function(){
       	 	
        },
        success:function(res){
        	  f(res);
        },
        complete: function(){
       	},
        error:function(){
          
        }
    });
	
}
//公用函数结束
var tong = 0;
//点击游戏开始
function requestgamestar(id){
	var u = '/index.php/activity/games/gamestar'
	var d = 'id='+id;
    var f = function(res){
    	if (res.status == request_succ) {
		}else{
			alert(res.msg);
		    if (res.url!='') {
		    	location.href = res.url;
		    };	
		}
    }
	AjaxRequest(u,d,f);
}
//游戏结束
function requestgamend(id,score){
	var u = '/index.php/activity/games/endgame'
	var d = 'id='+id+'&score='+score;
    var f = function(res){
		if (res.status == request_succ) {
		    tong = res.data;
		}else{
			alert(res.msg);
		}
    }
	AjaxRequest(u,d,f);
	return tong;
}
//游戏加载时调用
function requestload(id){
	var need;
	var u = '/index.php/activity/games/gameload';
	var d = 'id='+id+'&url='+encodeURIComponent(location.href.split('#')[0]);
    var f = function(res){
		if (res.status == request_succ) {
			AappId = res.data.signPackage.appId;
			Atimestamp = res.data.signPackage.timestamp;
			AnonceStr = res.data.signPackage.nonceStr;
			Asignature = res.data.signPackage.signature;
            // nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            // signature: '<?php echo $signPackage["signature"];?>',
            sharegame();
			need = res.data;
		}else{
			alert(res.msg);
		    if (res.url!='') {
		    	location.href = res.url;
		    };	
			need = false;
		}
    }
	AjaxRequest(u,d,f);
	return need;
}