$('document').ready(function(){
	load();
	$('.systemMag').addClass('whiteBg');
	$('.systemMag ul li').eq(1).addClass('borderLeft');
	// getorder(-1,0);
	getinfo(0,1);
	// getallarticle(0);
});

function getinfo(num,type){
	var u = '/index.php/center/preshop/getpreinfo';
	var d = 'num='+num+'&type='+type;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			var j = 1+num;
			var content = '';
			var list = '<tr style=" font-weight:700"><td width="5%"></td><td width="5%">电话</td><td width="10%">手机名称</td>'
		    	       +'<td width="10%">时间</td><td width="60%">需求</td>'
		    	       +'<td width="5%">状态</td></tr>';
			$.each(response['data'], function(i, v) {
				list += '<tr><td width="5%">'+(j++)+'</td><td width="5%">'+v['mobile']+'</td>'
		    		+'<td width="10%">'+v['content']['proinfo']['cname']+'</td>'
		    		+'<td width="10%">'+formatDate(v['jtime'])+'</td>'
		    		+'<td width="60%">'+v['content']['order']+'</td>'
		    		if (type==1) {
		    			list += '<td width="5%"><a href="javascrip:;" onclick="changecall('+v['id']+')" style="color:blue">已通话</a></td></tr>';
		    		}else{
		    			list += '<td width="5%">已通话</td></tr>'
		    		}
				});
		    $('.articlelist').html(list);
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
	}
	AjaxRequest(u,d,f);
}

function selectypes(){
	var num = ($('.pages').val()-1)*20;
	var type = $('.types').val();
	getinfo(num,type);
}

function changecall(id){
	var u = '/index.php/center/preshop/havecall';
	var d = 'id='+id;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
			var pages = $('.pages').val()
			if (pages==0) {
				pages = 1;
			};
			var num = (pages-1)*20;
			var type = $('.types').val();
			getinfo(num,type);
		}else{
			alert('修改失败！');
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
	$('.pagination').html(pages);
	$('#'+now+'').css({ background: '#337ab7',color: '#fff'});
}