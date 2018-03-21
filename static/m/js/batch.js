$(".submit").click(function(){
    var num = $("#amount").val();
    var desc = $(".note").val();
    var name = $("#name").val();
    var number = $("#card").val();
    var bank = $("#bank").val();
    if(num == ""){
        alert("请输入手机数量");
    }else if(desc == ""){
        alert("请输入手机品牌型号描述");
    }else if(name == ""){
        alert("请输入姓名");
    }else if(number == ""){
        alert("请输入您的银行卡号");
    }else if(bank == ""){
        alert("请输入开户行");
    }else{

    }
});
