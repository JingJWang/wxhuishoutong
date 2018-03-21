function fBut(){
document.getElementById('but-03').disabled=true;
document.getElementById('but-03').style.background = "#ccc";
document.getElementById('but-03').innerHTML='正在加载...';	
}

$(function(){
	$(document).delegate(".level_k",'touchstart',function(){
		$(this).find(".chooseName").css('color','#f59116');
        $(this).siblings().find(".chooseName").css('color','#888888');
        $(this).siblings().find("#level_sub").prop('checked',false);
        $(this).find('#level_sub').prop('checked',true);
	})
})

//-----------------------------------------------------------------弹窗开始-----------------------------------------------------
//调用弹出onclick="testMessageBox(event,'标题1','内容1');"

var isIe=(document.all)?true:false;
function WC(title,content)
{
	
	messContent="<div>"+content+"</div>";
	showMessageBox(title,messContent,250);
	//setTimeout("closeWindow()",3000);//几秒钟后关闭窗口
}




function showMessageBox(wTitle,content,wWidth)
{
closeWindow();
var bWidth=parseInt(document.documentElement.scrollWidth);
var bHeight=parseInt(document.documentElement.scrollHeight);
var cHeight=document.documentElement.clientHeight;//分辨率高度

if(bHeight>cHeight){heig2 = bHeight;}else{heig2 = cHeight;}

var back=document.createElement("div");
back.id="back";
var styleStr="top:0px;left:0px;position:absolute;background:#000;width:"+bWidth+"px;height:"+heig2+"px;z-index:99999;";
styleStr+=(isIe)?"filter:alpha(opacity=0);":"opacity:0;";
back.style.cssText=styleStr;
document.body.appendChild(back);
showBackground(back,70);
var mesW=document.createElement("div");
mesW.id="mesWindow";
mesW.className="mesWindow";
mesW.innerHTML="<div class='mesWindowContent' id='mesWindowContent'>"+content+"</div>";//<div class=windows_title><strong>"+wTitle+"</strong><span onclick='closeWindow();'>关闭</span></div>
styleStr="left:50%;margin-left:-"+(wWidth/2)+"px;top:20%;position:absolute;width:"+wWidth+"px;z-index:999999;";
//styleStr="left:"+(((pos.x-wWidth)>0)?(pos.x-wWidth):pos.x)+"px;top:"+(pos.y+10)+"px;position:absolute;width:"+wWidth+"px;";
mesW.style.cssText=styleStr;
document.body.appendChild(mesW);

	board();
}
//让背景渐渐变暗
function showBackground(obj,endInt)
{
	if(isIe)
	{
		obj.filters.alpha.opacity+=10;
		if(obj.filters.alpha.opacity<endInt)
		{
		setTimeout(function(){showBackground(obj,endInt)},0);
		}
	}
	else
	{
		var al=parseFloat(obj.style.opacity);al+=0.1;
		obj.style.opacity=al;
		if(al<(endInt/100))
		{
			setTimeout(function(){showBackground(obj,endInt)},0);
		}
	}
}
//关闭窗口
function closeWindow()
{
if(document.getElementById('back')!=null)
{
document.getElementById('back').parentNode.removeChild(document.getElementById('back'));
}
if(document.getElementById('mesWindow')!=null)
{
document.getElementById('mesWindow').parentNode.removeChild(document.getElementById('mesWindow'));
}
}

//***************************************************************************

//选择奖励  弹框出现隐藏
function rwdSelectIn(){
	$('.grayBg,.rwdSelect').slideDown(200);
}
//关闭选择奖励弹框
function rwdSelClose(){
	$('.grayBg,.rwdSelect').slideUp(200);
}
//关闭奖项提示弹出框
function rwdSucClose(){
	$('.grayBg,.rwdSuccess').slideUp(200);
}
//选中某一个奖项
function select(obj){
	if($(obj).hasClass('flour')){
		$(obj).siblings().removeClass('reded');
		$(obj).addClass('floured');
		$(obj).addClass('selected');
		$(obj).siblings().removeClass('selected');
	}else if($(obj).hasClass('red')){
		$(obj).siblings().removeClass('floured');
		$(obj).addClass('reded');
		$(obj).addClass('selected');
		$(obj).siblings().removeClass('selected');
	}
}


//签到页面广告随机
function board(){
	var number = parseInt(Math.random() * 3 + 1);
	if(number == 1){
		$(".print img").attr("src", "/static/task/task_two/img/guangao.jpg");
		$(".print").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}else if(number == 2){
		$(".print img").attr("src", "/static/task/task_two/img/guangao.jpg");
		$(".print").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}else{
		$(".print img").attr("src", "/static/task/task_two/img/guangao.jpg");
		$(".print").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}
}

//任务系列广告随机
function task(){
	var number = parseInt(Math.random() * 3 + 1);
	if(number == 1){
		$(".fax img").attr("src", "/static/task/task_two/img/guangao.jpg");
		$(".fax").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}else if(number == 2){
		$(".fax img").attr("src", "/static/task/task_two/img/guangao.jpg");
		$(".fax").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}else{
		$(".fax img").attr("src", "/static/task/task_two/img/guangao.jpg");
		$(".fax").attr("href", "/view/shop/list.html?type=15&code=0017OPNu1cd2690I9GOu1uQSNu17OPN1&state=");
	}
}