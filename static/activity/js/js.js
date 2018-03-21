var turnplate = {
	restaraunts: [], //大转盘奖品名称
	colors: [], //大转盘奖品区块对应背景颜色
	awards:[],
	outsideRadius: 268, //大转盘外圆的半径
	textRadius: 228, //大转盘奖品位置距离圆心的距离
	insideRadius: 117, //大转盘内圆的半径
	startAngle: 0, //开始角度
	rank:[],
	bRotate: false //false:停止;ture:旋转
};	
//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
window.onload = function() {
	drawRouletteWheel();
};
var rotateTimeOut = function() { //超时函数
	$('#wheelcanvas').rotate({
		angle: 0,
		animateTo: 2160, //这里是设置请求超时后返回的角度，所以应该还是回到最原始的位置，2160是因为我要让它转6圈，就是360*6得来的
		duration: 8000,
		callback: function() {
			//alert('网络超时，请检查您的网络设置！');
			$('.gray,.alertBtn').fadeIn();
			$('.alertBtn .text').html("网络超时，请检查您的网络设置！");
			$('.alertBtn a,.alertBtn .close').click(function(e) {
				$('.gray,.alertBtn').fadeOut();
			});
		}
	});
};
function rnd(n, m) {
	var random = Math.floor(Math.random() * (m - n + 1) + n);
	return random;

}
function drawRouletteWheel() {
	var canvas = document.getElementById("wheelcanvas");
	if (canvas.getContext) {
		//根据奖品个数计算圆周角度
		var arc = Math.PI / (turnplate.restaraunts.length / 2);
		var ctx = canvas.getContext("2d");
		//在给定矩形内清空一个矩形
		ctx.clearRect(0, 0, 620, 620);
		//strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
		ctx.strokeStyle = "#f06a62";
		//font 属性设置或返回画布上文本内容的当前字体属性
		ctx.font = '24px Microsoft YaHei';
		for (var i = 0; i < turnplate.restaraunts.length; i++) {
			var angle = turnplate.startAngle + i * arc;
			ctx.fillStyle = turnplate.colors[i];
			ctx.beginPath();
			//arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
			ctx.arc(310, 310, turnplate.outsideRadius, angle, angle + arc, false);
			ctx.arc(310, 310, turnplate.insideRadius, angle + arc, angle, true);
			ctx.stroke();
			ctx.fill();
			//锁画布(为了保存之前的画布状态)
			ctx.save();
			//----绘制奖品开始----
			ctx.fillStyle = "#fff";
			var text = turnplate.restaraunts[i];
			var awardPic = turnplate.awards[i]; //*********
			var line_height = 24;
			//translate方法重新映射画布上的 (0,0) 位置
			ctx.translate(310 + Math.cos(angle + arc / 2) * turnplate.textRadius, 310 + Math.sin(angle + arc / 2) * turnplate.textRadius);
			//rotate方法旋转当前的绘图
			ctx.rotate(angle + arc / 2 + Math.PI / 2);
			/** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
			if (text.indexOf("M") > 0) { //流量包
				var texts = text.split("M");
				for (var j = 0; j < texts.length; j++) {
					ctx.font = j == 0 ? 'bold 20px Microsoft YaHei' : '16px Microsoft YaHei';
					if (j == 0) {
						ctx.fillText(texts[j] + "M", -ctx.measureText(texts[j] + "M").width / 2, j * line_height);
					} else {
						ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
					}
				}
			} else if (text.indexOf("M") == -1 && text.length > 6) { //奖品名称长度超过一定范围 
				text = text.substring(0, 6) + "||" + text.substring(6);
				var texts = text.split("||");
				for (var j = 0; j < texts.length; j++) {
					ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
				}
			} else {
				//在画布上绘制填色的文本。文本的默认颜色是黑色
				//measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
				ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
			}
			//添加对应图标
			for (var a = 1; a <= turnplate.awards.length; a++) {
				img=document.getElementById("img0" + a).getAttribute('src');
				if (awardPic==img) {
					var img = document.getElementById("img0" + a);
					img.onload = function() {
						ctx.drawImage(img, -40, 25);
					};
					ctx.drawImage(img, -40, 25);
					break;
				};
			}
			//把当前画布返回（调整）到上一个save()状态之前
			ctx.restore();
			//----绘制奖品结束----
		}
	}
}