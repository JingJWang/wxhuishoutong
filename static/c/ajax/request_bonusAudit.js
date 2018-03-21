$('document').ready(function(){
	load(19);
	getorder(2,0);
});
//获取订单列表
function getorder(statu,number){
	$('.luxuryBox').css('display', 'block');
	$('.luxuryBoxRevise').css('display', 'none');
	$('.luxuryBoxAdd').css('display', 'none');
	var u='/index.php/nonstandard/homebonus/bonusAudit';
	var d='page='+number+'&bonustatus='+statu+'&id='+$('#inputnum').val();
	if(statu==2){
		$(".jBtn").css('display','block');
	}else{
		$(".jBtn").css('display','none');
	}
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {
	        var list = status = '';
	        if(response['data']['list'].length>0){
		        $.each(response['data']['list'], function(k, v) {
		        	if(v['source']==2){
		        		v['source']='商城';
		        	}else if(v['source']==1){
		        		v['source']='手机回收';
		        	}
		        	if(v['bonustatus']==2){
		        		v['bonustatus']='未结算';
		        		last = '<li class="infoEdit clearfix"><a href="javascript:;" class="revise fl" onclick="editinfo('+v['id']+','+v['wxid']+')"></a></li></ul>';
		        	
		        	}else if(v['bonustatus']==1){
		        		v['bonustatus']='已结算';
		        		last = '<li class="infoEdit clearfix"><a href="javascript:;" class="revise fl"></a></li></ul>';
		        	
		        	}
		        	list += '<ul class="clearfix"><li><input type="text" class="reviseInput" readonly value="'+v['goodid']+'"></li><li>'
		        	           +v['goodsid']+'</li><li><input type="text" class="reviseInput" readonly  value="'
		        	           +v['goodname']+'"></li><li>'
		        	           +v['userid']+'</li><li>'
		        	           +v['regtime'].substring(0,10)+'</li><li>'
		        	           +v['source']+'</li><li>'
		        	           +v['wxid']+'</li><li>'
		        	           +v['bonus']+'</li><li>'
		        	           +v['bonustatus']+'</li>'+last;
		        });
	        }else{
	        	list='<div style="text-align:center;margin-top:10px;border:0px;">暂无数据</div>';
	        }
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
function editinfo(id,wxuid){
	$('.luxuryBox').css('display', 'none');
	$('.luxuryBoxRevise').css('display', 'block');
	var u='/index.php/nonstandard/homebonus/bonusAuditEdit';
	d = 'id='+id+'&wxuid='+wxuid;
    f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
			var list = '';
			if(response['data']['0']!=''){
				$('#goodid').html(response['data']['0']['goodid']);
				$('#goodname').html(response['data']['0']['goodname']);
				$('#bonus').html(response['data']['0']['bonus']);
				$('#userid').html(response['data']['0']['userid']);
				if(response['data']['0']['bonustatus']==1){
					$('.selectedRevise').html('已结算');
				}else if(response['data']['0']['bonustatus']==2){
					$('.selectedRevise').html('未结算');
				}
				list = '<a href="javascript:;" class="save fl" onclick = "savedis('+id+','+wxuid+')">保存</a>';
	            list += '<a href="javascript:;" onClick="closeLuxuryBoxRevise()" class="noSave fl" >取消</a>';
	            $('.luxuryBoxRevise .btns').html(list);
			}
		}else{
			alert(response['msg']);
			if (response['url']!='') {
                location.href=response['url'];
            };
		}
    }
	AjaxRequest(u,d,f);
}
function savedis(id,wxuid){
    var bonustatus = $('.UlstRevise').attr('tid');
    var u='/index.php/nonstandard/homebonus/bonusAuditSave';
    var d = 'id='+id+'&bonustatus='+bonustatus+'&wxuid='+wxuid;
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert('修改成功');
            getorder(2,0);
		} 
    }
	AjaxRequest(u,d,f);
}
//批量操作未结算
function bonusAuditUpdate(){
	var id=$('#inputnum').val();
	var bonustatus=$('.downSelect .selected').html();
	var u='/index.php/nonstandard/homebonus/bonusAuditUpdate';
	if(bonustatus='未结算'){
		bonustatus=2;
		var d = 'userid='+id+'&bonustatus='+bonustatus;
	}   
    var f = function(res){
        var response = eval(res);
		if (response['status'] == request_succ) {
            alert('操作成功');
            getorder(2,0);
		} 
    }
	AjaxRequest(u,d,f);
}