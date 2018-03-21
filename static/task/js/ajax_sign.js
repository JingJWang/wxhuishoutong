var SignUrl="/index.php/task/finishtask/usersign";
var   request_succ   =   1000;
var   request_fall	 =	 3000;
var   msg_request_fall = '出现异常,请稍后';

function usersign(a){

	var check = /^\d{1,8}$/;
	if (!check.test(a)) {
		alert("参数不合法");
		return '';
	}
	var sign_n=$('.headdis_fr');

	$.ajax({
		url: SignUrl,
		type: 'POST',
		dataType: 'json',
		data: {wx_id: a},
		success:function(res){
			var response = eval(res);
			if(response['status'] == request_succ){
				var getward = '获得奖励';
				var tonhua = '';
				if (response.data.rewards.integral>0) {
					getward+=(' 通花+'+response.data.rewards.integral);
					tonhua = parseInt($('.tonghua').html());
					tonhua += response.data.rewards.integral;
					$('.tonghua').html(tonhua);
				};
				if (response.data.rewards.all_integral>0) {
					getward+=(' 成长值+'+response.data.rewards.all_integral);
					chengz = parseInt($('.chengz').html());
					chengz += response.data.rewards.all_integral;
					$('.chengz').html(chengz);
				};
				if (response.data.rewards.bonus>0) {
					getward+=(' 奖金+'+response.data.rewards.bonus);
				};
				if (response.data.rewards.fund>0) {
					getward+=(' 基金+'+response.data.rewards.fund);
				};
				$('.userImg').attr('src',response.data.img);
				$('.qdCont').append('<img src="../../../../static/task/images/qdSucc.png">');
				/*if(response.data.sign==1 && response.data.lxsign==0){
					$(".award").html('获得奖励：1元+10通花');
				}else{
					$(".award").html('获得奖励：10通花');
				}*/
				$(".award").html(getward);
				$(".aj_task_1").remove();
				$('#back').css('display','block');
				$('#mesWindow').css('display','block');
				$('.huiyuan').html('恭喜您成为会收通会员，帮回收通邀请用户买/卖东西可获得奖金')
			}else{
				$('.qdCont').append('<img src="../../../../static/task/images/qdalready.png">');
				if (response['msg']=='noreg') {
					alert('您未注册');
    			    location.href = response['url'];
					return ;
				};
				$('.userImg').attr('src',response.data.img);
				$(".award").html(response.msg);
				$(".lvTian").html('尊敬的回收通会员，帮回收通邀请用户买/卖东西可得奖金');
				$('#back').css('display','block');
				$('#mesWindow').css('display','block');
			}
		}
	})
	
}