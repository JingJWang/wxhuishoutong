function fBut(){
document.getElementById('but-03').disabled=true;
document.getElementById('but-03').style.background = "#ccc";
document.getElementById('but-03').innerHTML='正在加载...';	
}

function dk(sId)
{
  var oImg = document.getElementById('kks').getElementsByTagName('a');
  for (var i = 0; i < oImg.length; i++)
  {
		if (oImg[i].id == sId)
		{
			oImg[i].style.color = '#f59116';
		}
		else
		{
			oImg[i].style.color = '#888888';
		}
  }
}

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
	$('#back').css('display','none');
	$('#mesWindow').css('display','none');
}
////////////////////////////////////////////////////////////////////弹窗结束//////////////////////////////////////////
function Content($type){
switch($type){
case 'tonghua':{
	Contents = "<div class='tongHua'><b onclick='closeWindow();'>x</b>"
	Contents += "<div class='tongDiv'>"+tit0+"</div><p>"+tit1+"</p><p>"+tit2+"</p><p>"+tit3+"</p><p><img src='"+tit4+"'></p>";
	Contents += "</div>";}break;
}return Contents;
}
//签到页面广告随机
function board(){
	var number = parseInt(Math.random() * 3 + 1);
	if(number == 1){
		$(".print img").attr("src", "/static/task/task_two/img/photo1.png");
		$(".print").attr("href", "/view/shop/list.html?hmsr=usercenter&hmpl=fristgood&hmcu=&hmkw=&hmci=");
	}else if(number == 2){
		$(".print img").attr("src", "/static/task/task_two/img/photo2.png");
		$(".print").attr("href", "/view/shop/list.html?hmsr=usercenter&hmpl=twicegood&hmcu=&hmkw=&hmci=");
	}else{
		$(".print img").attr("src", "/static/task/task_two/img/photo3.png");
		$(".print").attr("href", "/view/shop/list.html?hmsr=usercenter&hmpl=thirdgood&hmcu=&hmkw=&hmci=");
	}
}
//弹框内容---公用
function selectprice(){
	$('.blighted').css('display', 'block');
	$('.commodity').css('display', 'block');
};
//弹框内容--音乐会购票
function selectpricea(){
	$('html,body').animate({scrollTop: $('.goumai').offset().top},500);return false;
};
//弹框内容---猪肉
function selectprice_zr(){
	var num = $('#money').html().slice(1);
	$('#meatP,#allmoney').html(num);
	$('.musicBox').css('display', 'block');
	$('body').css('overflow','hidden');
};
$(document).on('click', '.commodity .amount .selection', function(event) {
	$(this).find('.dot').addClass('active');
	$(this).siblings('.selection').find('.dot').removeClass('active');
});