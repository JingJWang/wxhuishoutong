$(".draw").click(function(){
    var num = $(".phone").val();
    if(num == ""){
        alert("手机号不能为空");
    }else if( num.match(/^(1[3|4|5|7|8][0-9]{9})$/) ){
        // $(".shade , .frame").show();
        //确认添加
        $(".affirm").click(function(){
            $(".shade , .frame").hide();
        });
        //取消
        $(".close-btn").click(function(){
            $(".shade , .frame").hide();
        });
        //如果提交多次，显示输入验证码
        //$(".frame").addClass("code");
    }else{
        alert("请输入正确的手机号码");
    }
});