function pointerate(){
	if (turnplate.bRotate) return;
	turnplate.bRotate = !turnplate.bRotate;
	//获取随机数(奖品个数范围内)//获得1到奖品个数之间的随机数赋值给item)
	var d = '';
	var u = '/index.php/activity/turntable/bigwel';
	var f = function(res){
		var response = eval(res);
		if (response['status'] == request_succ) {				
			for (var i = 0; i < turnplate.rank.length; i++) {
				if (response['data']['id']==turnplate.rank[i]) {
					var item = i+1;
				};
			};
			if (response['data']['type']==1) {
				var text = '您获得了'+parseInt(response['data']['text'])+'通花';
			}else if (response['data']['type']==2) {
				var text = '我们会在活动结束后5个工作日内给您发放！';
			}else{
				var text = '您可以再免费抽一次！';
			}
			rotateFn(item,text,response['data']['type']);
		}else{
			alert(response['msg']);
			UrlGoto(response['url']);
		};
	};
	AjaxRequest(u,d,f);	
	//奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
}
function getarticle(){
    var d = '';
	$.ajax({
        url:'/index.php/activity/turntable/getarticle',
        type:"POST",
        dataType:"json",
        data:d,
        async: false,
        beforeSend: function(){
       	 	$("#turn_gif_box").css('display','block');
    	},
    	success:function(res){
			var response = eval(res);
			if (response['status'] == request_succ) {
				$.each(response['data']['result'],function(k,v){
					turnplate.restaraunts[k] = v.name;
					turnplate.awards[k] = v.image;
					turnplate.colors[k] = k%2!=0?'#f4564e':'#f06a62';
					turnplate.rank[k] = v.id;
				});
				if (response['data']['free']==1) {
					$('.pointer').attr('src', '/static/activity/images/turnplate-pointer01.png');
				}else{
					$('.pointer').attr('src', '/static/activity/images/turnplate-pointer02.png');				
				}
			}else{
				alert(response['msg']);
				UrlGoto(response['url']);
			}
       	},
       	complete: function(res){
       	 	$("#turn_gif_box").css('display','none');
   		},
   		error:function(msg){
   		    alert(msg_request_fall+msg);
   		}
   	});
}
/*
 *转盘结束后。
 *@param 	int 	item 	奖品位置
 *@param 	string	txt 	提示语句
*/
var rotateFn = function(item,text,type) {
	var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length * 2));
	if (angles < 270) {
		angles = 270 - angles;
	} else {
		angles = 360 - angles + 270;
	}
	$('#wheelcanvas').stopRotate();
	$('#wheelcanvas').rotate({
		angle: 0,
		animateTo: angles + 1800, //angles是图片上各奖项对应的角度，1800是我要让指针旋转5圈。所以最后的结束的角度就是这样子^^
		duration: 8000,
		callback: function() {
			//弹出窗口  关闭弹窗
			$('.gray,.alertBtn').fadeIn();
			$('.alertBtn a,.alertBtn .close').click(function(e) {
				$('.gray,.alertBtn').fadeOut();
			});
			//向弹窗中添加获奖文字和获奖图片
			$('.alertBtn .text').html(turnplate.restaraunts[item-1]);
			$('.alertBtn .img img').attr("src", turnplate.awards[item-1]);
			$('.alertBtn .explain .textarea').hide();					
			$('.alertBtn .explain p').html(text);
			if (type==3) {
				$('.pointer').attr('src', '/static/activity/images/turnplate-pointer01.png');
			}else{
				$('.pointer').attr('src', '/static/activity/images/turnplate-pointer02.png');
			}
			turnplate.bRotate = !turnplate.bRotate;
		}
	});
};