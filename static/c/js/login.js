$(function(){
	//微信二维码显示隐藏
	$('.WeChat span').click(function(){
		if($('.WeChat i').hasClass('showIn')){
			$('.WeChat i').hide().removeClass('showIn');
		}else{
			$('.WeChat i').show().addClass('showIn');
		}
	})
//点击其他区域输入框边框线隐藏	
$(".username").click(function(e){
		$('.password').removeClass('inputClick');
        $(this).addClass('inputClick');
        var ev = e || window.event;
        if(ev.stopPropagation){
            ev.stopPropagation();
        }
        else if(window.event){
            window.event.cancelBubble = true;//兼容IE
        }
})
$(".password").click(function(e){
		$('.username').removeClass('inputClick');
        $(this).addClass('inputClick');
        var ev = e || window.event;
        if(ev.stopPropagation){
            ev.stopPropagation();
        }
        else if(window.event){
            window.event.cancelBubble = true;//兼容IE
        }
})
document.onclick = function(){
        $('.username,.password').removeClass('inputClick');
    }
$(".username,.password").click(function(e){
    var ev = e || window.event;
        if(ev.stopPropagation){
                ev.stopPropagation();
         }
        else if(window.event){
                window.event.cancelBubble = true;//兼容IE
        }
})	
	
})
//记住密码按钮改变样式
function remember(obj){
	if($(obj).hasClass('showIn')){
		$(obj).removeClass('right').removeClass('showIn');
	}else{
		$(obj).addClass('right').addClass('showIn');		
	}
}
//=== 表单验证 ===
 	//手机号和邮箱
	function checkPhone(){
		var account = $(".username").val();
		var length = $(".username").val().length;
		var email = /^\w+[@]\w+[.][\w.]+$/;
		var phoneno = /^(1[3|4|5|7|8][0-9]{9})$/;
		if(length ==""){
			$('.usernameTip').html("用户名不能为空！");
			$('.usernameTip').css('display','block');
			$(".username").css('border','1px solid #f4483f');
			return false;
		}else if(email.test(account)||phoneno.test(account)){
			$('.usernameTip').hide();
			$(".usernameTip").html("");
			$(".username").css('border','1px solid #dfdfdf');
			return true;
		}else{
				$('.usernameTip').css('display','block');
				$(".username").css('border','1px solid #f4483f');
				$('.usernameTip').html("请输入正确的手机号或邮箱!");
				return false;
		}
	}
 	//密码验证
	function checkpassword(){
		var length =$('.password').val().length;	
		if(length ==""){
			$('.passwordTip').html("密码不能为空！");
			$('.passwordTip').css('display','block');
			$(".password").css('border','1px solid #f4483f');
			return false;
		}else if(length < 6){
			$('.passwordTip').html("密码位数不能小于6位！");
			$('.passwordTip').css('display','block');
			$(".password").css('border','1px solid #f4483f');
			return false;
		}else if(length > 32){
			$('.passwordTip').html("密码位数不能超过32位！");
			$('.passwordTip').css('display','block');
			$(".password").css('border','1px solid #f4483f');
			return false;
		}else{
			$('.passwordTip').html("");
			$(".password").css('border','1px solid #dfdfdf');
			return true;
		}
	}
   










