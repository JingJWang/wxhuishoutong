$('document').ready(function(){
	load(12);
	// getorder(-1,0);
	getlabal();
	getallarticle(0);
});

function getlabal(){
	var u = '/index.php/center/knowledge/getlables';
	var d = '';
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			var list = '';
			$.each(response['data'], function(i, v) {
				list += '<option value="'+v['label_id']+'">'+v['label_name']+'</option>';
			});
			$('#labels').append(list);
			// editor1.html('sdfsdaf');
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
	//var content = editor1.html();//编辑器的内容
	//var content= $('#textname').val();
	var content = UE.getEditor('editor').getContent();//编辑器的内容
	content = escape(content);//转义字符串
	var is_up = 0;
	if (name==''||content=='') {
		alert('文章内容和文章标题必填');
		return ;
	};
	if ($('#is_up').prop("checked")) {
		var is_up = 1;
	};
	var u ='/index.php/center/knowledge/putextinfo';
	var d= 'lid='+labid+'&name='+name+'&image='+images+'&desc='+desc+'&content='+content+'&is_up='+is_up;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			alert('保存成功');
        	UrlGoto('/view/control/knowledge.html');
		}else{
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//取得文章列表
function getallarticle(num){
	var u ='/index.php/center/knowledge/getarticlelist';
	var d= 'num='+num;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
		    var j = num+1;
			var list = '<tr style=" font-weight:700"><td width="5%"></td><td width="20%">文章名</td><td width="10%">文章类型</td>'
		    	       +'<td width="20%">生成时间</td><td width="8%">点击量</td><td width="8%">分享量</td>'
		    	       +'<td width="20%">状态</td></tr>';
			$.each(response['data']['articles'], function(i, v) {
				if (v['status']==1) {
					var s = '已上线';
				}else if(v['status']==0){
					var s = '未上线';
				}else{
					var s = '未知状态';
				}
				list += '<tr><td width="5%">'+(j++)+'</td><td width="20%">'+v['name']+'</td>'
		    	    	+'<td width="10%">'+v['label']+'</td>'
		    	    	+'<td width="20%">'+formatDate(v['jtime'])+'</td>'
		    	    	+'<td width="8%">'+v['click']+'</td>'
		    	    	+'<td width="8%">'+v['share']+'</td>'
		    	    	+'<td width="20%">'+s+'&nbsp;&nbsp;<a href="javascrip:;" onclick="getarticle('+v['id']+')" style="color:blue">编辑</a>'
		    	    	+'&nbsp;&nbsp;<a href="javascrip:;" onclick="getdelart('+v['id']+')" style="color:blue">删除</a></td></tr>';
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
	var u ='/index.php/center/knowledge/getarticle';
	var d= 'id='+id;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			$('.lists').css('display', 'none');
			// $('#names')
	        $('#names').val(response['data']['article']['0']['name']);
	        $('#imgs').val(response['data']['article']['0']['img']);
	        $('#desc').val(response['data']['article']['0']['des']);
	        $('#labels').val(response['data']['labels']['0']['lid']);
	        if (response['data']['article']['0']['status']==1) {
	        	$('#is_up').attr("checked", true)
	        };
	       // editor1.html('');//编辑器的内容
	        //UE.getEditor('editor').setContent();
            var str = response['data']['article']['0']['content'];
            if (str==null) {
            	//editor1.html('');
            	UE.getEditor('editor').setContent('');
            }else{
	            //editor1.html(str);//编辑器的内容
            	//$('#edui14_iframeholder').html(str);
            	//UE.getEditor('editor').getContent()==str;
            	UE.getEditor('editor').setContent(str);
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
	//var content = editor1.html();//编辑器的内容
	var content = UE.getEditor('editor').getContent();
	content = escape(content);//转义字符串
	var is_up = 0;
	if (name==''||content=='') {
		alert('文章内容和文章标题必填');
		return ;
	};
	if ($('#is_up').prop("checked")) {
		var is_up = 1;
	};
    var u ='/index.php/center/knowledge/uparticle';
	var d= 'lid='+labid+'&name='+name+'&image='+images+'&desc='+desc+'&content='+content+'&is_up='+is_up+'&id='+id;
	var f = function(res){
		var response = eval(res);
        if (response['status']==request_succ) {
        	UrlGoto('/view/control/knowledge.html');
        }else{
			alert(response['msg']);
        }
	}
	AjaxRequest(u,d,f);
}
//getdelart 删除文章 修改文章状态 
function getdelart(id){
	if(confirm('你确定要删除该文章吗')){
		 var u ='/index.php/center/knowledge/getdelart';
			var d= 'id='+id;
			var f = function(res){
				var response = eval(res);
		        if (response['status']==request_succ) {
		        	UrlGoto('/view/control/knowledge.html');
		        }else{
					alert(response['msg']);
		        }
			}
			AjaxRequest(u,d,f);
	}
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
	//editor1.html('');//编辑器的内容
	UE.getEditor('editor').setContent('');
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