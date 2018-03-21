$('document').ready(function(){
	load(13);
	getlabal();
});
var lables = Array();
function getlabal(){
	var u = '/index.php/center/noticeNews/getlables';
	var d = '';
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			var list = '';
			$.each(response['data'], function(i, v) {
				list += '<option value="'+v['id']+'">'+v['title']+'</option>';
				lables[v['id']] = v['title'];
			});
			$('#labels').append(list);
			getallarticle(0);
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
	}
	AjaxRequest(u,d,f);
}
//提交文章信息
function putsinfo(){
	var labid = $('#labels').val();
	var name = $('#names').val();
	var images = $('#imgs').val();
	var desc = $('#desc').val();
	var akey = $('#akey').val();
	var content = editor2.html();//编辑器的内容
	content = escape(content);//转义字符串
	var is_up = 0;
	if (name==''||content=='') {
		alert('文章内容和文章标题必填');
		return ;
	};
	if ($('#is_up').prop("checked")) {
		var is_up = 1;
	};
	var u ='/index.php/center/noticeNews/putextinfo';
	var d= 'lid='+labid+'&name='+name+'&image='+images+'&desc='+desc+'&akey='+akey+'&content='+content+'&is_up='+is_up;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
        	UrlGoto('/view/control/noticeNews.html');
		}else{
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//取得文章列表
function getallarticle(num){
	var u ='/index.php/center/noticeNews/getarticlelist';
	var d= 'num='+num;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
		    var j = num+1;
			var list = '<tr style=" font-weight:700"><td width="5%"></td><td width="30%">文章名</td><td width="10%">文章类型</td>'
		    	       +'<td width="30%">生成时间</td>'
		    	       +'<td width="20%">状态</td></tr>';
			$.each(response['data']['articles'], function(i, v) {
				v['label'] = lables[v['fid']];
				if (v['status']==1) {
					var s = '已上线';
				}else if(v['status']==2){
					var s = '已上线';
				}else if(v['status']==0){
					var s = '未上线';
				}else{
					var s = '未知状态';
				}
				list += '<tr><td width="5%">'+(j++)+'</td><td width="10%">'+v['title']+'</td>'
		    	    	+'<td width="10%">'+v['label']+'</td>'
		    	    	+'<td width="30%">'+formatDate(v['jointime'])+'</td>'
		    	    	// +'<td width="10%">'+v['click']+'</td>'
		    	    	// +'<td width="10%">'+v['share']+'</td>'
		    	    	+'<td width="10%">'+s+'<a href="javascrip:;" onclick="getarticle('+v['id']+')" style="color:blue">编辑</a></td></tr>';
			});	
		    $('.articlelist').html(list);
		    page(10,num,response['data']['num']);
		}else{
			alert('没有任何文字');
		}
	}
	AjaxRequest(u,d,f);
}
//获取选中的文章
function getarticle(id){
	var u ='/index.php/center/noticeNews/getarticle';
	var d= 'id='+id;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			$('.lists').css('display', 'none');
	        $('#names').val(response['data']['article']['0']['title']);
	        $('#imgs').val(response['data']['article']['0']['icon']);
	        $('#desc').val(response['data']['article']['0']['des']);
	        $('#akey').val(response['data']['article']['0']['akey']);
	        $('#labels').val(response['data']['article']['0']['fid']);
	        if (response['data']['article']['0']['status']==1||response['data']['article']['0']['status']==2) {
	        	$('#is_up').attr("checked", true);
	        };
	        editor2.html('');//编辑器的内容
            var str = response['data']['article']['0']['text'];
            if (str==null) {
            	editor2.html('');
            }else{
	            editor2.html(str);//编辑器的内容
            }
	        $('#submitinfo').remove();
            $('#luxuryCon').append('<input id="submitinfo" style="margin:20px 0px 0px 20px;width:80px;height:30px;" type="button" onclick="uparticle('+response['data']['article']['0']['id']+')" value="更新文章">');
			$('#luxuryCon').css('display', 'block');
		}
	}
	AjaxRequest(u,d,f);
}
//更新文章
function uparticle(id){
	var labid = $('#labels').val();
	var name = $('#names').val();
	var images = $('#imgs').val();
	var desc = $('#desc').val();
	var akey = $('#akey').val();
	var content = editor2.html();//编辑器的内容
	content = escape(content);//转义字符串
	var is_up = 0;
	if (name==''||content=='') {
		alert('文章内容和文章标题必填');
		return ;
	};
	if ($('#is_up').prop("checked")) {
		var is_up = 1;
	};
    var u ='/index.php/center/noticeNews/uparticle';
	var d= 'lid='+labid+'&name='+name+'&image='+images+'&desc='+desc+'&akey='+akey+'&content='+content+'&is_up='+is_up+'&id='+id;
	var f = function(res){
		var response = eval(res);
        if (response['status']==request_succ) {
        	UrlGoto('/view/control/noticeNews.html');
        }else{
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
//打开编辑器
function openaddarticle(){
	$('#luxuryCon').css('display', 'block');
	$('.lists').css('display', 'none');
	$('#names').val('');
	$('#imgs').val('');
	$('#desc').val('');
	$('#labels').val('');
	$('#is_up').attr("checked", false);
	editor2.html('');//编辑器的内容
	$('#submitinfo').remove();
    $('#luxuryCon').append('<input id="submitinfo" style="margin:20px 0px 0px 20px;width:80px;height:30px;" type="button" onclick="putsinfo()" value="提交文章">');
}
//关闭编辑器
function closearticle(){
	$('#luxuryCon').css('display', 'none');
	$('.lists').css('display', 'block');
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
/**
 * @param      int       one_pag  一页总共的页数
 * @param      int       now      当前开始数字
 * @param      int       num      总共的页数
 * @param      int       status   根据需求添加的函数 
 **/
function page(one_pag,now,num,statu){
	var page = Math.ceil(num/one_pag);//可以分的页数
	var pages = '';
	if (num<=one_pag) {
		return ;
	}
	if (now>=one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getallarticle(0)">上一页</a></li>';
	};
	if (page<=5) {
	    for (var i = 0; i < page; i++) {
	        pages += '<li class="active"><a href="javascript:;" id="'+(i*one_pag)+'" onclick="getallarticle('+i*one_pag+')">'+(i+1)+'</a></li>';
	    };
	}else{
	    if ((now/one_pag)<3) {
	    	for (var i = 1; i <= 5; i++) {
	        	pages += '<li class="active"><a href="javascript:; " id="'+((i-1)*one_pag)+'" onclick="getallarticle('+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else if (now/one_pag>=(page-3)) {
	        for (var i = (page-4); i <= page; i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getallarticle('+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else{
            for (var i = (now/one_pag-1); i < (now/one_pag+4); i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getallarticle('+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }
	}
	if (now<(page-1)*one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getallarticle('+(now+one_pag)+')">下一页</a></li>';
	};
	pages += '<li class="active"><a href="javascript:;" onclick="getallarticle('+(page-1)*one_pag+')">末页</a></li>';
	$('.pagination').html(pages);
	$('#'+now+'').css({ background: '#337ab7',color: '#fff'});
}