$('document').ready(function(){
	load(21);
	getorder(2,0);
});
//获取订单列表
function getorder(statu,page){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
	var u='/index.php/nonstandard/homebonus/rankingAuditList';
	var d='status='+statu;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        var list = status = '';
	        if(response['data'].length>0){
	        	$.each(response['data'], function(k, v) {
	        		var star='';
	        		var sts='';
	        		var money='';
	        		/*if(v['status']==1){
	        			status='已发放奖励'+'</li><li>'+v['wsum']+v['ysum']+'</li><li style="color:#ccc">'
	        	           +'发放奖励'+'</li></ul>';
	        		}else{
	        			status='未发放奖励'+'</li><li>0</li><li onclick="showshadow('+v['mobile']+');">'
	        	           +'发放奖励'+'</li></ul>';
	        		}*/
	        		if(v['logbonus_money']!=null){
	        		 	money=v['logbonus_money'];
	        		 	sts='已发放';
	        		 	star=userDate(v['logbonus_jointime'])
	        		}else{
	        			money='0';
	        			sts='未发放';
	        			star='';
	        		}
	        		var cou=Number(v['wsum'])+Number(v['ysum']);
		        	list += '<ul class="clearfix">'
		        		+'<li>'+v['wx_id']+'1</li>'
		        		+'<li>'+v['mobile']+'</li>'
		        		+'<li>'+cou+'</li>'
		        		+'<li>'+v['ysum']+'</li>'
		        		+'<li>'+v['total']+'</li>'
		        		+'<li>'+star+'</li>'
		        		+'<li>'+sts+'</li>'
		        		+'<li>'+money+'</li>'
		        		+'<li onclick="showshadow('+v['mobile']+');">'+'发放奖励'+'</li></ul>';
		        });
	        }else{
	        	list='<div style="text-align:center;margin-top:10px;border:0px;">暂无数据</div>';
	        }
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
function userDate(uData){
	var myDate = new Date(uData*1000);
	var year = myDate.getFullYear();
	var month = myDate.getMonth() + 1;
	var day = myDate.getDate();
	return year + '-' + month + '-' + day;
}
//点击发放奖励
function showshadow(phone){
	$('.shadow').css('display','block');
	$('#phone').val(phone);
};
//点击弹框---确定
function sureShadow(){
	$('.shadow').css('display','none');
	var value=$("#value").val();
	var phone=$("#phone").val();
	var u='/index.php/nonstandard/homebonus/saveRandingaudit';
	var d='phone='+phone+'&value='+value;
	var f = function(res){
		 var response = eval(res);
			if (response['status'] == request_succ) {
	            alert('发放奖励成功');
	            location.href=response['url'];
			} 
	}
	AjaxRequest(u,d,f);
};

//取消
function closeShadow(){
	$('.shadow').css('display','none');
};