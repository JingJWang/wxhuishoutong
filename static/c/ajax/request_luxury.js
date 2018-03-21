$('document').ready(function(){
	load(5);
	getorder(-1,0);
});
//获取订单列表
function getorder(statu,id){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
	var u='/index.php/center/luxurys/queryorder';
	var d='page='+id+'&orderStatus='+statu;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        $('.mySelect .selected').html(response['data']['name']);
	        var list = status = '';
	        $.each(response['data']['list'], function(k, v) {
	        	switch(v['status']){
                    case '0':
                        status='无效/删除';
                        break;
                    case '1':
                        status='检验通过 待上架';
                        break;
                    case '2':
                        status='已售出 待付款';
                        break;
                    case '3':
                        status='已付款';
                        break;
                    case '4':
                        status='上架成功';
                        break;
                    case '5':
                        status='寄售期到 已下架';
                        break;
                    case '6':
                        status='退回';
                        break;
                    default:
                        break;
	        	}
	        	list += '<ul class="clearfix"><li>'+(id+k+1)+'</li><li>'
	        	           +v['num']+'</li><li>'
	        	           +formatDate(v['jtime'])+'</li><li>'
	        	           +v['bdname']+'</li><li>'
	        	           +v['pname']+'</li><li>'
	        	           +v['bhname']+'</li><li>'+status+'</li><li class="infoEdit clearfix">'
	        	           +'<a href="javascript:;" class="revise fl" onclick="editinfo('
	        	           	+v['lid']+')"></a></li></ul>';
	        });
	        $('.luxuryBox .luxuryInfo').html(list);
	        //下面是分页
	        var one_pag = 10;
	        var now = Number(response['data']['num']['now']);//当前开始数字
	        var num = response['data']['num']['0']['num'];//总数
	        page(one_pag,now,num,statu);
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
	}
	AjaxRequest(u,d,f);
}
//获取编辑信息
function editinfo(id){
	$('.luxuryBox').css('display', 'none');
	$('.luxuryBoxRevise').css('display', 'block');
	$('.luxuryBoxAdd').css('display', 'none');
	u = '/index.php/center/luxurys/onequery';
	d = 'id='+id;
    f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
			$('.luxuryBoxRevise .reviseInput').val(response['data']['order']['list']['0']['num']);
			var list = '';
			$.each(response['data']['type'], function(k, v) {//类型列表
				if (response['data']['order']['list']['0']['pid']==v['prid']) {
					list += '<li class="current"><a href="javascript:;" onclick="getbrand('+v['prid']+')" tid='+v['prid']+'>'+v['prname']+'</a></li>';
				}else{
					list += '<li><a href="javascript:;" onclick="getbrand('+v['prid']+')" tid='+v['prid']+'>'+v['prname']+'</a></li>';
				}
			})
			$('.UlRevise').html(response['data']['order']['list']['0']['pname']);
			$('.UlRevise').attr('tid', response['data']['order']['list']['0']['pid']);
            $('.Ultype').html(list);
            list = '';
            $.each(response['data']['brand'], function(k, v) {//品牌列表
            	if (response['data']['order']['list']['0']['brid']==v['bdid']) {
					list += '<li class="current"><a href="javascript:;" tid='+v['bdid']+'>'+v['bdname']+'</a></li>';
            	}else{
					list += '<li><a href="javascript:;" tid='+v['bdid']+'>'+v['bdname']+'</a></li>';
            	}
            })
            $('.Ulwup').html(list);
			$('.UlwRevise').html(response['data']['order']['list']['0']['bdname']);
            $('.UlwRevise').attr('tid', response['data']['order']['list']['0']['brid']);
            list = '';
            $.each(response['data']['branch'], function(k, v) {//商店列表
            	if (response['data']['order']['list']['0']['bhid']==v['id']) {
					list += '<li class="current"><a href="javascript:;" tid='+v['id']+'>'+v['bname']+'</a></li>';
            	}else{
					list += '<li><a href="javascript:;" tid='+v['id']+'>'+v['bname']+'</a></li>';
            	}
            })
			$('.UlsRevise').html(response['data']['order']['list']['0']['bhname']);
			$('.UlsRevise').attr('tid', response['data']['order']['list']['0']['bhid']);
            $('.Ulshop').html(list);
            var status = $('.Ulstatus').find('li a');//状态列表
            var le = status.length;
            for (var i = 0; i <le; i++) {
            	var tid = status.eq(i).attr('tid');
            	if (tid==response['data']['order']['list']['0']['status']) {
                    status.eq(i).parent('li').addClass('current');
                    $('.UlstRevise').attr('tid', tid);
                    $('.UlstRevise').html(status.eq(i).html());
            	};
            };
            $('.ordertimeList .jtime').html(formatDate(response['data']['order']['list']['0']['jtime']));
            $('.ordertimeList .atime').html(formatDate(response['data']['order']['list']['0']['atime']));
            $('.ordertimeList .etime').html(formatDate(response['data']['order']['list']['0']['etime']));
            $('.ordertimeList .dtime').html(formatDate(response['data']['order']['list']['0']['dtime']));
            list = '<a href="javascript:;" class="save fl" onclick = "savedis('+response['data']['order']['list']['0']['lid']+')">保存</a>';
            list += '<a href="javascript:;" class="noSave fl" onclick = "cance()">取消</a>';
            $('.luxuryBoxRevise .btns').html(list);
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
    }
	AjaxRequest(u,d,f);
}
//获取品牌
function getbrand(id){
	var u = '/index.php/center/luxurys/getbrands';
	var d = 'brandId='+id;
	var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
			if (response['data']==false) {
				alert('没有此类型的品牌!');
			    $('.UlRevise').html('');
			    $('.UlRevise').attr('tid','');
			    $('.UlwRevise').html('');
                $('.UlwRevise').attr('tid','');
				return ;
			};
			var list = '';
            $.each(response['data'], function(k, v) {//类型
                list += '<li><a href="javascript:;" tid='+v['bdid']+'>'+v['bdname']+'</a></li>';
            })
            $('.Ulwup').html(list);
			$('.UlwRevise').html('');
            $('.UlwRevise').attr('tid','');
            $('.UlwRevise').trigger('click');
		}
	}
	AjaxRequest(u,d,f);
}
function savedis(id){
	var orderId = id;
    var orderNum = $('.luxuryBoxRevise .reviseInput').val();
    var shopId = $('.luxuryBoxRevise .UlsRevise').attr('tid');
    var articleId = $('.luxuryBoxRevise .UlwRevise').attr('tid');
    var orderStatus = $('.luxuryBoxRevise .UlstRevise').attr('tid');
    var u = '/index.php/center/luxurys/editeorder';
    var d = 'orderId='+orderId+'&orderNum='+orderNum+'&shopId='+shopId+'&articleId='+articleId+'&orderStatus='+orderStatus;
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert('修改成功');
            location.href=response['url'];
		} 
    }
	AjaxRequest(u,d,f);
}
//打开添加表单
function openorder(){
	$('.luxuryBox').css('display', 'none');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'block');
	$('.UlRevise').html('');
	$('.UlwRevise').html('');
	$('.UlsRevise').html('');
	$('.Ulwup').html('');
    $('.UlRevise').attr('tid','');
    $('.UlwRevise').attr('tid','');
    $('.UlsRevise').attr('tid','');
	var u = '/index.php/center/luxurys/getyeinfo';
	var d = '';
	var f = function(res){
        var response = eval(res);
        var list = '';
		if (response['status'] == request_succ) {
			$.each(response['data']['type'], function(k, v) {//类型列表
                list += '<li><a href="javascript:;" onclick="getbrand('+v['prid']+')" tid='+v['prid']+'>'+v['prname']+'</a></li>';
			})
			$('.luxuryBoxAdd .Ultype').html(list);
			list = '';
			$.each(response['data']['branch'], function(k, v) {//品牌列表
                list += '<li><a href="javascript:;" tid='+v['id']+'>'+v['bname']+'</a></li>';
			})
			$('.luxuryBoxAdd .Ulshop').html(list);
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
	}
	AjaxRequest(u,d,f);
}
function addorder(){
	var orderNum = $('.luxuryBoxAdd .reviseInput').val();
    var shopId = $('.luxuryBoxAdd .UlsRevise').attr('tid');
    var articleId = $('.luxuryBoxAdd .UlwRevise').attr('tid');
    var u = '/index.php/center/luxurys/addorder';
    var d = 'orderNum='+orderNum+'&shopId='+shopId+'&articleId='+articleId;
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert(response['msg']);
            location.href=response['url'];
		}else{
            alert(response['msg']);
            if (response['url']!='') {
                location.href=response['url'];
            };
		}
    }
	AjaxRequest(u,d,f);
}
function getheor(){
	var orderNum = $('#inputnum').val();
    var u = '/index.php/center/luxurys/numquery';
    var d = 'orderNum='+orderNum;
	var f = function(res){
        var response = eval(res);
        var list = '';
		if (response['status'] == request_succ) {
			$.each(response['data']['list'], function(k, v) {
	        	switch(v['status']){
                    case '0':
                        status='无效/删除';
                        break;
                    case '1':
                        status='检验通过 待上架';
                        break;
                    case '2':
                        status='已售出 待付款';
                        break;
                    case '3':
                        status='已付款';
                        break;
                    case '4':
                        status='上架成功';
                        break;
                    case '5':
                        status='寄售期到 已下架';
                        break;
                    case '6':
                        status='退回';
                        break;
                    default:
                        break;
	        	}
	        	list += '<ul class="clearfix"><li>'+(k+1)+'</li><li>'
	        	           +v['num']+'</li><li>'
	        	           +formatDate(v['jtime'])+'</li><li>'
	        	           +v['bdname']+'</li><li>'
	        	           +v['pname']+'</li><li>'
	        	           +v['bhname']+'</li><li>'+status+'</li><li class="infoEdit clearfix">'
	        	           +'<a href="javascript:;" class="revise fl" onclick="editinfo('
	        	           	+v['lid']+')"></a></li></ul>';
	        });
	        $('.luxuryBox .luxuryInfo').html(list);
            // console.info(response);
		}else{
            alert(response['msg']);
            if (response['url']!='') {
                location.href=response['url'];
            };
		}
    }
	AjaxRequest(u,d,f);
}
//打开列表
function cance(){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
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
function page(one_pag,now,num,statu){
	var page = Math.ceil(num/one_pag);//可以分的页数
	var pages = '';
	if (num<=one_pag) {
		return ;
	}
	if (now>=one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getorder('+statu+',0)">上一页</a></li>';
	};
	if (page<=5) {
	    for (var i = 0; i < page; i++) {
	        pages += '<li class="active"><a href="javascript:;" id="'+(i*one_pag)+'" onclick="getorder('+statu+','+i*one_pag+')">'+(i+1)+'</a></li>';
	    };
	}else{
	    if ((now/one_pag)<3) {
	    	for (var i = 1; i <= 5; i++) {
	        	pages += '<li class="active"><a href="javascript:; " id="'+((i-1)*one_pag)+'" onclick="getorder('+statu+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else if (now/one_pag>=(page-3)) {
	        for (var i = (page-4); i <= page; i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getorder('+statu+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }else{
            for (var i = (now/one_pag-1); i < (now/one_pag+4); i++) {
	        	pages += '<li class="active"><a href="javascript:;" " id="'+((i-1)*one_pag)+'" onclick="getorder('+statu+','+(i-1)*one_pag+')">'+i+'</a></li>';
	        };
	    }
	}
	if (now<(page-1)*one_pag) {
		pages += '<li class="active"><a href="javascript:;" onclick="getorder('+statu+','+(now+one_pag)+')">下一页</a></li>';
	};
	$('.pagination').html(pages);
	$('#'+now+'').css({ background: '#337ab7',color: '#fff'});
}