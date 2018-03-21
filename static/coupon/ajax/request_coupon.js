function getphone(code){
	var u = '/index.php/coupon/getcoupon/getphone';
	var d = 'code='+code;
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {//如果已经领取，直接跳转
			UrlGoto('/view/coupon/qobtain.html?mobile='+response['data']['0']['mobile']);
		}else{//如果未领取，显示领取界面和分享信息
			if (response['data']!='') {
				AappId = response.data.signPackage.appId;
				Atimestamp = response.data.signPackage.timestamp;
				AnonceStr = response.data.signPackage.nonceStr;
				Asignature = response.data.signPackage.signature;
				window.shareData.timgUrl = 'http://wx.recytl.com/static/task/task_two/img/pic01.jpg';
				window.shareData.tContent = '【回收通】陪您过暑假，玩具礼包、女生礼包只需10元，还有更多超低秒杀哦';
				window.shareData.timeLineLink = response['data']['shareurl'];
				window.shareData.tTitle = '回收通-通花商城';
				sharetext();
			};
			$('.content').css('display', 'block');
		}
	}
	AjaxRequest(u,d,f);
}
var selfsendcode = false;
function checkphone(){
	if (from==null) {
		from = '';
	};
	var mobile = $('.dimension .introduce .phone').val();
	var mobilecode = $('.frame .covers .embody #phonecode').val();
	if (mobile=='') {
		return ;
	};
	var u = '/index.php/coupon/getcoupon/checkphone';
	var d = 'mobile='+mobile+'&mobilecode='+mobilecode+'&from='+from;
	var f = function(res){
		var response = eval(res);
		if(response['status'] == request_succ){
			UrlGoto('/view/coupon/qobtain.html?mobile='+mobile);
		}else{
			if (!selfsendcode&&response['data']=='code_error') {
				$(".shade , .frame").show();
				sendcode();//自动发送一次验证码
				selfsendcode = true;
			}else if(response['data']=='code_error'){
				$(".shade , .frame").show();
			}else{
				alert(response['msg']);
			}
		}
	}
	AjaxRequest(u,d,f);
}

function sendcode(){
	var mobile = $('.dimension .introduce .phone').val();
	var imgcode = $('.frame .covers #verify .entry').val();
	var u = '/index.php/coupon/getcoupon/getcode';
	var d = 'mobile='+mobile+'&imgcode='+imgcode;
	var f = function(res){
		var response = eval(res);
		if(response['status'] == request_succ){
			sendMessage();//计时器开始计时
		}else{
			if (response['msg']=='imgerror') {
				$('.frame').addClass('code');
				alert('请先输入正确的图形验证码！');
				$('.covers #verify .code img').trigger('click');
			}else{
				sendMessage();//计时器开始计时
			}
		}
	}
	AjaxRequest(u,d,f);
}

function getCouInfo(mobile){
	var u = '/index.php/coupon/getcoupon/usercoupon';
	var d = 'mobile='+mobile;
	var f = function(res){
		var response = eval(res);
		if (response['shareInfo']!=undefined) {
			AappId = response.data.signPackage.appId;
			Atimestamp = response.data.signPackage.timestamp;
			AnonceStr = response.data.signPackage.nonceStr;
			Asignature = response.data.signPackage.signature;
			window.shareData.timgUrl = 'http://wx.recytl.com/static/task/task_two/img/pic01.jpg';
			window.shareData.tContent = '【回收通】陪您过暑假，玩具礼包、女生礼包只需10元，还有更多超低秒杀哦';
			window.shareData.timeLineLink = response['data']['shareurl'];
			window.shareData.tTitle = '回收通-通花商城';
			sharetext();
		};
		if (response['status'] == request_succ) {
			var allcount = 0;
			var list = '';
			$.each(response['data']['coupon'], function(i, v) {
				allcount+= parseInt(v['amount']);
				list += '<div class="zzq"><p class="zzqP">¥<b>'+v['amount']+'</b>元</p>'+'<b class="zzqp_p">增值劵</b>'+'<p class="zzq_p">回收金额<span>'+v['thisrange']+'</span>元可用</p></div>'

			});
			$('.account .mobile').html(mobile);
			$('.zzqDiv').html(list);
			$('.hint .sum').html(allcount);
		}else{

		}
	}
	AjaxRequest(u,d,f);
}