function checkphone(){
	var list = '';
    var phone = $('.tel').val();
	if(phone == ""){
        alert("手机号不能为空");return false;
    }else if(!(/^1[34578]\d{9}$/.test(phone))){
        alert("请输入正确的手机号码");return false;
    };
	var u = '/index.php/nonstandard/mymember/getMymember';
	var d = 'phone='+phone;
	var f = function(res){
		var response = eval(res);
		if(response['status'] == request_succ){
			alert("恭喜你成功领取体验会员");
			UrlGoto('/view/welfare/memSucc.html?status=1');
		}else if(response['status'] == request_fall && response['msg']=='您已经是会员了领取失败'){
			alert("您已经领取过体验会员");
			UrlGoto('/view/welfare/memSucc.html?status=2');
		}else if(response['status'] == request_fall && response['msg']=='该手机号码没有注册账户'){
			UrlGoto('http://wx.recytl.com/index.php/nonstandard/system/usereg');
		}
	}
	AjaxRequest(u,d,f);
}

function getphone(status){
		if(status == 1){
			list = '<p class="aleadyetP">恭喜您成为回收通体验会员</p><span class="aleadySpan"></span><a href="/index.php/nonstandard/mybonus/mybonusList" class="alreadya">立马去赚钱</a>';
		}else {
			list='<p class="aleadyetP">您已领取过体验会员</p><span class="yetSpan"></span><a href="/index.php/nonstandard/mybonus/mybonusList" class="alreadya">立马去赚钱</a>';
		}
		$('.aleadyet').html(list);
}