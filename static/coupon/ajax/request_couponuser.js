//获取用户当前名下的优惠券信息
function getcouponuserinfo(){
	var u ='/index.php/coupon/couponuser/getCouponUserList';
	var d ='';
    var f =function(res){
    	var response = eval(res);
    	var list = '';
    	var count = Number(0);
    	if(response['status'] == request_succ){
			$.each(response['data'], function(i, v) {
				if(v['statu']=='0'){
					list+= '<a class="dimension" href="">'
						+'<div class="reveal"><div class="covers">'
						  +'<div class="indication">'
						   +'<div class="price" style="font-size:28px">￥<span class="nouserd">'+v['amount']+'</span>元</div>'
						   +'<div class="term">回收金额'+v['ranges']+'以上可用</div>'
						  +'</div>'
						  +'<div class="motif">'+v['name']+'</div>'
						  +'</div></div>'
						  +'<div class="instruction">'
						   +'<div class="time">'+formatDate(v['jointime'])+' — '+formatDate(parseInt(v['jointime'])+parseInt(v['contime']))+'</div>'
						    +'<div class="hint">此劵只可用于回收</div>'
						     +'<div class="state" style="display:block">未使用</div>'
						     +'</div>';	
					count=count+Number(v['amount']);	
				}
				if(v['statu']=='1'){
					list+= ' <a class="dimension used" href="javascript:;">'
						+'<div class="reveal"><div class="covers">'
						  +' <div class="indication">'
						   +'<div class="price" ><span>￥'+v['amount']+'元</span></div>'
						   +'<div class="term">回收金额'+v['ranges']+'以上可用</div>'
						  +'</div>'
						  +'<div class="motif">'+v['name']+'</div>'
						  +'</div></div>'
						  +'<div class="instruction">'
						   +'<div class="time">'+formatDate(v['jointime'])+' — '+formatDate(parseInt(v['jointime'])+parseInt(v['contime']))+'</div>'
						  	+'<div class="hint" style="display:block">此劵只可用于回收</div>'
				            +'<div class="state">已使用</div>'
						  +'</div>';
				}
				if(v['statu']=='-1'){
					list+= ' <a class="dimension used" href="">'
						+'<div class="reveal"><div class="covers">'
						  +' <div class="indication">'
						   +'<div class="price"><span>￥'+v['amount']+'元</span></div>'
						   +'<div class="term">回收金额'+v['ranges']+'以上可用</div>'
						  +'</div>'
						  +'<div class="motif">'+v['name']+'</div>'
						  +'</div></div>'
						  +'<div class="instruction">'
						  +'<div class="time">'+formatDate(v['jointime'])+' — '+formatDate(parseInt(v['jointime'])+parseInt(v['contime']))+'</div>'
						  	+'<div class="hint"style="display:block">此劵只可用于回收</div>'
				            +'<div class="state">已过期</div>'
						  +'</div>';
				}
			});
			$('.listing').html(list);
			var nouserd = Number($('.nouserd').html());
			$('.tips').html("账户可用回收增值券共计"+count+"元");
		}else{
			$('.tips').hide();
			$('.listing').html('你当前还没有可用的优惠券！').css("margin","10px 0px 0px 120px");
		}
    }
	AjaxRequest(u,d,f);
}
//时间转换  
function formatDate(now)   {  
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
	return   year+"."+fixZero(month,2)+"."+fixZero(date,2); 
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