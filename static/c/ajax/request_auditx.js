//获取用户信息
function getinfo(){
	var mobiles = $('.query .seats').val();
	if(mobiles==''){
		alert('请输入电话号码!');
		return ;
	}
	var u = '/index.php/center/taskverify/XgetUser';
	var d = 'mobiles='+mobiles;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			var isto = '<span class="passed adopted">通过</span><div class="line"></div>';
				    $('.digitalorder').html('未审核');
			var list = '';
		    $.each(response['data'], function(i, v) {
		    	list+='<div class="group">'
                            +'<div class="graph">'
                                +'<div class="case"></div>'
                            +'</div>'
                            +'<div class="tel">'+v['mobile']+'</div>'
                            +'<div class="times">'+formatDate(v['jointime'])+'</div>'
                            +'<div class="state" opendid="'+v['oid']+'" lid="'+v['id']+'">'
                            +isto
                            +'</div>'
                        +'</div>';
		    });
            $('.contents').html(list);
            afteran();
	    }else{
            $('.contents').html('');
            alert(response['msg']);
	    }
	}
	AjaxRequest(u,d,f);
}
//通过与未通过
function getcheck(){
	var ok = $('.state .checkok');
	var nok = $('.state .nocheckok');
	var openids = '';
	var checkok = '';
	for (var i = 0; i < ok.length; i++) {
		openids += ok.eq(i).parent().attr('opendid')+',';
		checkok += ok.eq(i).parent().attr('lid')+',';
	};
	if (checkok=='') {return ;};
    var openids=openids.substring(0,openids.length-1);
    var checkok=checkok.substring(0,checkok.length-1);
	var u = '/index.php/center/taskverify/Xupuser';
	var d = 'data='+checkok+'&openid='+openids;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			alert(response['msg']);
			UrlGoto('/view/control/dataxAudit.html');
		}else{
			alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//时间转换  
function   formatDate(now)   {  
    if (now==0) {
    	return '未登录过';
    };    
	var   now= new Date(now*1000);     
	var   year=now.getFullYear();     
	var   month=now.getMonth()+1;     
	var   date=now.getDate();     
	var   hour=now.getHours();      
	var   minute=now.getMinutes();     
	var   second=now.getSeconds();      
	return   year+"-"+fixZero(month,2)+"-"+fixZero(date,2)+"-    "+fixZero(hour,2)+":"+fixZero(minute,2)+":"+fixZero(second,2); 
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
		pages += '<a class="" onclick="getinfo('+Alltype+','+(now-one_pag)+')" href="javascript:;">上一页</a>';
	};
	if (page<=5) {
	    for (var i = 0; i < page; i++) {
            pages += '<a class="sum" id="'+(i*one_pag)+'" onclick="getinfo('+Alltype+','+i*one_pag+')" href="javascript:;">'+(i+1)+'</a>';
	    };
	}else{
	    if ((now/one_pag)<3) {
	    	for (var i = 1; i <= 5; i++) {
                pages += '<a class="sum" id="'+((i-1)*one_pag)+'" onclick="getinfo('+Alltype+','+(i-1)*one_pag+')" href="javascript:;">'+i+'</a>';
	        };
	    }else if (now/one_pag>=(page-3)) {
	        for (var i = (page-4); i <= page; i++) {
                pages += '<a class="sum" id="'+((i-1)*one_pag)+'" onclick="getinfo('+Alltype+','+(i-1)*one_pag+')" href="javascript:;">'+i+'</a>';
	        };
	    }else{
            for (var i = (now/one_pag-1); i < (now/one_pag+4); i++) {
                pages += '<a class="sum" id="'+((i-1)*one_pag)+'" onclick="getinfo('+Alltype+','+(i-1)*one_pag+')" href="javascript:;">'+i+'</a>';
	        };
	    }
	}
	if (now<(page-1)*one_pag) {
		pages += '<a class="" onclick="getinfo('+Alltype+','+(now+one_pag)+')" href="javascript:;">下一页</a>';
	};
	$('.page').html(pages);
	$('#'+now+'').addClass('active');
}