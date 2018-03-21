$('document').ready(function(){
	load(4);
});
//获取用户信息
function moveuser(id){
	var type = $('#type').val();
	var mobile = $('#smobile').val();
	var u = '/index.php/center/userpacket/moveprice';
	var d = 'mobile='+mobile+'&type='+type;
	var f = function(res){
		var response = eval(res);
        // if (response['status']==request_succ) {
        	alert(response['msg']);
 //        	var list='';
 //        	$.each(response['data']['list'], function(k, v) {
 //        		list += '<ul class="clearfix">'
	// 				    +'<li>'+(id+k+1)+'</li>'
	// 				    +'<li>'+v['name']+'</li>'
	// 				    +'<li>'+v['mobile']+'</li>'
	// 				    +'<li>'+v['email']+'</li>'
	// 				    +'<li>'+formatDate(v['time'])+'</li>'
	// 				    +'<li>普通用户</li>'
	// 				    +'<li class="clearfix">';
	// 		    if (v['status']==1) {
	// 		    	list += '<a href="javascript:;" class="revise fl" onclick="getInRevise('+v['uid']+')">修改</a>'
	// 		    	        +'<a href="javascript:;" class="delete fl" onclick="delInfo('+v['uid']+')">删除</a>'
	// 						+'</li></ul>';
	// 		    }else if(v['status']==-1){
	// 		    	list += '<a href="javascript:;" class="revise fl" onclick="getInRevise()">修改</a>'
	// 		    	        +'<a href="javascript:;" class="delete fl">已删除</a>'
	// 						+'</li></ul>';
	// 		    }
	// 		    $('#userScroll').html(list); 
 //        	});
 //        	$('.mySelect a').html(response['data']['name']);
	// 		var one_pag = 10;
	//         var now = id;//当前开始数字
	//         var num = Number(response['data']['num']['number']);//总数
	// 		page(one_pag,now,num);
        // };
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