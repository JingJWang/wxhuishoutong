function gervotes(id){
	var u = '/index.php/task/otherget/getvoteinfo';
	var d = 'id='+id;
	var f = function(res){
		var response=eval(res);
		var list = '';
		if(response['status'] == request_succ){
			$.each(response['data'], function(i, v) {
				list += '<div class="casket">'
                        +'<div class="square">'
                            +'<div class="graph">'
                                +'<img src="'+v['img']+'"/>'
                            +'</div>'
                            +'<div  class="direction">'
                                +'<button class="sn">'+v['name']+'</button>'
                            +'</div>'
                        +'</div>'
                        +'<div class="group" data='+i+'></div>'
                    +'</div>';
			});
			$('.lists').html(list);
			$('.item').html('<a class="forth" href="javascript:;" onclick="putvote('+id+')">立即投票</a>')
			voteclick();
		}
	}
	AjaxRequest(u,d,f);
}
function putvote(id){
	var vid = $('.casket .on').attr('data');
	var lin = $('.old').val();
	var sexist = $('.sexist .active').attr('data');
	var zhiye = $('.pale').val();
	var harea = $('#harea').val();
	var hproper = $('#hproper').val();
	var hcity = $('#hcity').val();
	if (vid==undefined||sexist==undefined||lin==0||zhiye==0||harea==undefined||hproper==undefined||hcity==undefined) {alert('请把信息填写完整!');return;};
	if (isNaN(id)||isNaN(vid)||isNaN(lin)||isNaN(sexist)||isNaN(zhiye)) {return ;};
	var text = $('.feed').val();
	var u = '/index.php/task/otherget/uservote';
	var d = 'id='+id+'&vid='+vid+'&text='+text+'&lin='+lin+'&sexist='+sexist+'&zhiye='+zhiye+'&harea='+harea+'&hproper='+hproper+'&hcity='+hcity;
	var f = function(res){
		var response=eval(res);
		if(response['status'] == request_succ){
			$('.topcon h3').html('投票成功啦！');
			$('.reawards').html('恭喜您获得 '+response['data']+'通花');
			var url = '<a href="/index.php/task/usercenter/taskcenter">返回福利站</a>';
			$('.reawards').after(url);
			$('.rwdSuccess').css('display', 'block');
		}else{
			if (response['msg']!='') {
				alert(response['msg']);
			};
		}
	}
	AjaxRequest(u,d,f);
}