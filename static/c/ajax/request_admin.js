$('document').ready(function(){
	load(10);
	getWhole(0);
});
//获取用户信息
function getWhole(id){
	var u = '/index.php/center/adminUser/checkAdmins';
	var d = 'page='+id;
	var f = function(res){
		var response = eval(res);
        if (response['status']==request_succ) {
        	var list='';
        	$.each(response['data']['list'], function(k, v) {
        		list += '<ul class="clearfix">'
					    +'<li>'+(id+k+1)+'</li>'
					    +'<li>'+v['name']+'</li>'
					    +'<li>'+v['mobile']+'</li>'
					    +'<li>'+v['email']+'</li>'
					    +'<li>'+formatDate(v['time'])+'</li>'
					    +'<li>普通用户</li>'
					    +'<li class="clearfix">';
			    if (v['status']==1) {
			    	list += '<a href="javascript:;" class="revise fl" onclick="getInRevise('+v['uid']+')">修改</a>'
			    	        +'<a href="javascript:;" class="delete fl" onclick="delInfo('+v['uid']+')">删除</a>'
							+'</li></ul>';
			    }else if(v['status']==-1){
			    	list += '<a href="javascript:;" class="revise fl" onclick="getInRevise()">修改</a>'
			    	        +'<a href="javascript:;" class="delete fl">已删除</a>'
							+'</li></ul>';
			    }
			    $('#userScroll').html(list); 
        	});
        	$('.mySelect a').html(response['data']['name']);
			var one_pag = 10;
	        var now = id;//当前开始数字
	        var num = Number(response['data']['num']['number']);//总数
			page(one_pag,now,num);
        };
	}
	AjaxRequest(u,d,f);
}
//分页
function page(one_pag,now,num){
	var page = Math.ceil(num/one_pag);//可以分的页数
	var pages = '';
	if (num<=one_pag) {
		return ;
	}
	if (now>=one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getWhole(0)">上一页</a></li>';
	};
	if (page<=5) {
	    for (var i = 0; i < page; i++) {
	        pages += '<li class="active"><a href="javascript:;" id="'+(i*one_pag)+'" onclick="getWhole('+i*one_pag+')">'+(i+1)+'</a></li>';
	    };
	}else{
	    if ((now/one_pag)<3) {
	    	for (var i = 1; i <= 5; i++) {
	        	pages += '<li class="active"><a href="javascript:; " id="'+((i-1)*one_pag)+'" onclick="getWhole('+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else if (now/one_pag>=(page-3)) {
	        for (var i = (page-4); i <= page; i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getWhole('+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else{
            for (var i = (now/one_pag-1); i < (now/one_pag+4); i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getWhole('+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }
	}
	if (now<(page-1)*one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getWhole('+(now+one_pag)+')">下一页</a></li>';
	};
	$('.pagination').html(pages);
	$('#'+now+'').css({ background: '#337ab7',color: '#fff'});
}
function joinAdmin(){
	if (!checkPhone()||!checkName('.nameInput')||!checkEmail()||!checkpassword()) {
		return ;
	};
	var rid = $('.addcon .roleselected').attr('num');
	var mobile = $('.addcon .noInput').val();
	var name = $('.addcon .nameInput').val();
	var email = $('.addcon .emailInput').val();
	var pw = $('.addcon .pwInput').val();
	var u = '/index.php/center/adminUser/joinAdmin';
	var d = 'rid='+rid+'&mobile='+mobile+'&name='+name+'&email='+email+'&pw='+pw;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
		    $('.grayBg,.addAlert').slideUp(150);
	        $('.addcon .roleselected').attr('num','');
	        $('.addcon .roleselected').html('请选择角色');
	        $('.addcon .noInput').val('');
	        $('.addcon .nameInput').val('');
	        $('.addcon .emailInput').val('');
	        $('.addcon .pwInput').val('');
            getWhole(0);
		}else{
            alert(response['msg']);
		}

	}
	AjaxRequest(u,d,f);
}
function delInfo(id){
	$('.grayBg,.deleteAlert').slideDown(150);
	$('.grayBg,.deleteAlert .btns').attr('num',id);
}
function getOutDelete(){//点击确定
    var	id = $('.grayBg,.deleteAlert .btns').attr('num');
	var u = '/index.php/center/adminUser/delAdmin';
	var d = 'uid='+id;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
	        $('.grayBg,.deleteAlert .btns').attr('num','');
	        $('.grayBg,.deleteAlert').slideUp(150);
            getWhole(0);
		}else{
            alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
//删除数据
function getDelete(){//点击取消
	$('.grayBg,.deleteAlert .btns').attr('num','');
	$('.grayBg,.deleteAlert').slideUp(150);
}
function getInRevise(id){
	var u = '/index.php/center/adminUser/getOneAdmin';
	var d = 'uid='+id;
	var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
			switch(response['data']['id']){
				case '1':
				    $('.revisecon .roleselected').html('系统管理员');
				    $('.revisecon .roleselected').attr('num', '1');
				    break;
				case '2':
				    $('.revisecon .roleselected').html('业务人员');
				    $('.revisecon .roleselected').attr('num', '2');
				    break;
				case '3':
				    $('.revisecon .roleselected').html('普通用户');
				    $('.revisecon .roleselected').attr('num', '3');
				    break;
				case '4':
				    $('.revisecon .roleselected').html('财务管理人员');
				    $('.revisecon .roleselected').attr('num', '4');
				    break;
				case '5':
				    $('.revisecon .roleselected').html('其他人员');
				    $('.revisecon .roleselected').attr('num', '5');
				    break;
                default:
                    break;
			}
            $('.revisecon .nameInput').val(response['data']['name']);
            $('.revisecon .pwInput').val('');
            var list = '<input type="button" name="" id="" value="保存修改" class="isadd" onclick="getmed('
            	       +response['data']['uid']+')"/>'
                       +'<input type="button" name="" id="" value="放弃修改" class="noadd" onclick="getOutRevise()" />';
            $('#buttons').html(list);
	        $('.grayBg,.reviseAlert').slideDown(150);
		}else{
            alert(response['msg']);
		}
	}
	AjaxRequest(u,d,f);
}
function getmed(id){
    checkNameR(".revisecon .nameInput");
    var rid = $('.revisecon .roleselected').attr('num');
    var name = $('.revisecon .nameInput').val();
    var pw = $('.revisecon .pwInput').val();
    if (pw!='') {
        checkpasswordR();
    };
    var u = '/index.php/center/adminUser/modifyAdmin';
    var d = 'uid='+id+'&rid='+rid+'&name='+name+'&pw='+pw;
    var f = function(res){
		var response = eval(res);
		if (response['status']==request_succ) {
	        $('.grayBg,.reviseAlert').slideUp(150);
            getWhole(0);
		}else{
            alert(response['msg']);
		}
    }
	AjaxRequest(u,d,f);
}
function selectmobile(){
	var mobile = $('#smobile').val();
	if (mobile==''||!mobile.match(/^(1[3|4|5|7|8][0-9]{9})$/)) {
		alert('请填写正确的手机格式');
		return ;
	};
    var u = '/index.php/center/adminUser/select';
    var d = 'mobile='+mobile;
    var f = function(res){
		var response = eval(res);
		var list = '';
		if (response['status']==request_succ) {
			list += '<ul class="clearfix">'
					    +'<li>'+1+'</li>'
					    +'<li>'+response['data']['name']+'</li>'
					    +'<li>'+response['data']['mobile']+'</li>'
					    +'<li>'+response['data']['email']+'</li>'
					    +'<li>'+formatDate(response['data']['time'])+'</li>'
					    +'<li>普通用户</li>'
					    +'<li class="clearfix">';
			if (response['data']['status']==1) {
				list += '<a href="javascript:;" class="revise fl" onclick="getInRevise('+response['data']['uid']+')">修改</a>'
				        +'<a href="javascript:;" class="delete fl" onclick="delInfo('+response['data']['uid']+')">删除</a>'
			            +'</li></ul>';
			}else if(response['data']['status']==-1){
				list += '<a href="javascript:;" class="revise fl" onclick="getInRevise()">修改</a>'
				        +'<a href="javascript:;" class="delete fl">已删除</a>'
			            +'</li></ul>';
			}
			$('#userScroll').html(list); 
            console.info(response);
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