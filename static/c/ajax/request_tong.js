$('document').ready(function(){
	load(6);
	getorder(2,0);
});
//获取订单列表
function getorder(statu,id){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
	var u='/index.php/center/shopOrder/queryorder';
	var d='page='+id+'&orderStatus='+statu;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        $('.mySelect .selected').html(response['data']['name']);
	        var list = status = '';
	        var adress = Array();
	        $.each(response['data']['list'], function(k, v) {
	        	adress = Array();
	        	if (v['adress']!='') {
	        		adress=v['adress'].split(",");
	        		v['name']=adress['0'];
	        		v['phone']=adress['1'];
	        		v['detail']=adress['2'];
	        	};
	        	v['detail']=v['city']+v['detail'];
	            list += '<ul class="clearfix"><li>'
	                    +'<input type="text" name="" id="" value="'+(k+1)+'、'+v['gname']+'（'+v['price']/100+'元）" class="reviseInput"></li><li>'
	                    +'<input type="text" name="" id="" value="'+v['text']+'" class="reviseInput"></li><li>'
	                    +v['name']+'</li><li>'
	                    +'<input type="text" name="" id="" value="'+v['detail']+'" class="reviseInput"><li>'
	                    +v['phone']+'</li><li>'
	                    +v['number']+'</li><li>'
	                    +formatDate(v['time'])+'</li><li class="infoEdit clearfix">'
	        	        +'<a href="javascript:;" class="revise fl" onclick="editinfo('
	        	        +v['id']+')"></a></li></ul>';
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
	u = '/index.php/center/shopOrder/onequery';
	d = 'id='+id;
    f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
			$('#tid').html(response['data']['pid']);
			$('#jtime').html(formatDate(response['data']['time']));
			$('#company').val(response['data']['company']);
			$('#number').val(response['data']['number']);
            var status = $('.Ulstatus').find('li a');//状态列表
            var le = status.length;
            for (var i = 0; i <le; i++) {
            	var tid = status.eq(i).attr('tid');
            	if (tid==response['data']['status']) {
                    status.eq(i).parent('li').addClass('current');
                    $('.UlstRevise').attr('tid', tid);
                    $('.UlstRevise').html(status.eq(i).html());
            	};
            };
            list = '<a href="javascript:;" class="save fl" onclick = "savedis('+response['data']['id']+')">保存</a>';
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
function savedis(id){
	var orderId = id;
	var company= $('#company').val();
    var orderNum = $('#number').val();
    var orderStatus = $('.luxuryBoxRevise .UlstRevise').attr('tid');
    var u = '/index.php/center/shopOrder/editeorder';
    var d = 'orderId='+orderId+'&orderNum='+orderNum+'&company='+company+'&orderStatus='+orderStatus;
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert('修改成功');
            location.href=response['url'];
		}else{
			alert(response['msg']);
		}
    }
	AjaxRequest(u,d,f);
}
function getheor(){
	var orderNum = $('#inputnum').val();
    var u = '/index.php/center/shopOrder/numquery';
    var d = 'orderNum='+orderNum;
	var f = function(res){
        var response = eval(res);
        var list = '';
		if (response['status'] == request_succ) {
			$.each(response['data']['list'], function(k, v) {
	        	list += '<ul class="clearfix"><li>'
	                    +v['gname']+'</li><li>'
	                    +v['text']+'</li><li>'
	                    +v['name']+'</li><li>'
	                    +v['city']+v['detail']+'</li><li>'
	                    +v['phone']+'</li><li>'
	                    +v['number']+'</li><li>'
	                    +formatDate(v['time'])+'</li><li class="infoEdit clearfix">'
	        	        +'<a href="javascript:;" class="revise fl" onclick="editinfo('
	        	        +v['id']+')"></a></li></ul>';
	        });
	        $('.luxuryBox .luxuryInfo').html(list);
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