$('document').ready(function(){
	load(20);
	getorder(0,0);
});
//获取订单列表
function getorder(statu,id){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
	var data=$("#search").serialize();
	var u='/index.php/nonstandard/homebonus/bonusIncreaseList';
	var d='page='+id+'&status='+statu+'&'+data;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        $('.mySelect .selected').html(response['data']['name']);
	        var list = status = '';
	        if(response['data']['list'].length>0){
	        	 $.each(response['data']['list'], function(k, v) {
	 	        	if(v['memeber']==1){
	 	        		status='年费会员';
	 	        	}else if(v['memeber']==2){
	 	        		status='体验会员';
	 	        	}
	 	        	if(v['aincome']==null){
	 	        		v['aincome']=0;
	 	        	}
	 	        	list += '<ul class="clearfix"><li>'+v['wx_id']+'</li><li>'
	 	        	           +v['mobile']+'</li><li>'
	 	        	           +v['regtime'].substring(0,10)+'</li><li>'
	 	        	           +v['nums']+'</li><li>'
	 	        	           +v['aincome']+'</li><li>'+status+'</li></ul>';
	 	        });
	        }else{
	        	$('.showBut').css('display','none');
	        	list='<div  style="text-align:center;margin-top:10px;border:0px;">暂无数据</div>';
	        }
	        $('.luxuryBox .luxuryInfo').html(list);
	        
	        //下面是分页
	        var one_pag = 10;
	        var now = Number(response['data']['num']['now']);//当前开始数字
	        var num = response['data']['num']['0']['sum'];//总数
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
//添加随机物品奖励
function addbonus(){
	var data=$("#search").serialize();
	var startM=$("#startM").val();
	var endM=$("#endM").val();
	var u='/index.php/nonstandard/homebonus/addBonusRand';
	var d='startM='+startM+'&endM='+endM+'&'+data;
	var f = function(res){
	    var response = eval(res);
		if (response['status'] == request_succ) {
	        alert('奖励发放成功');
	        $(".shadow").css("display","none");
	        getorder(0,0);
		} else{
			alert('输入参数有误,请重新输入');
			$(".shadow").css("display","none");
			getorder(0,0);
		}
	}
	AjaxRequest(u,d,f);
}
