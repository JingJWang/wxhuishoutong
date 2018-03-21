<!doctype html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="chrome=1" >
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no">
<title>回收通</title>
<link rel="stylesheet" type="text/css" href="/static/task/task_two/css/public.css"/>
<link rel="stylesheet" type="text/css" href="/static/task/task_two/css/style.css?v=1016"/>
<link rel="stylesheet" type="text/css" href="/static/task/task_two/css/bounced.css"/>
<script src="../../../static/gold/js/jquery-1.11.1.min.js"></script>
<style type="text/css">
    /*弹出的关注提示框*/
.guangzhu{
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    left: 0;
    top: 0;
    z-index: 100;
}
.guangzhu img{
    position: absolute;
    top: 15%;
    left: 10%;
    width: 80%;
}
.guangzhu span{
    position: absolute;
    color: #fff;
    top: 10%;
    right: 10%;
    text-decoration: underline;
}
.circliful {position: relative;}
.circle-text, .circle-info, .circle-text-half, .circle-info-half {
    width: 100%;
    position: absolute;
    text-align: center;
    display: inline-block;}


.circliful .fa {
    margin: -10px 3px 0 3px;
    position: relative;
    bottom: 4px;
}
.chooseName img{
    border-radius: 50%;
}
.classbtn{
	height:40px;
	background:#f5f5f5 url(/static/task/task_two/img/more.png) no-repeat;
	background-position:61% 45%;
}
.abtn{
	width:115px;
	margin-top:10px;
	margin-left:30%;
	border:none;
	background:none;
	color:#888888;
	font-size:12px;
}
.btnhid{
	display:none;
}
</style>
<script type='text/javascript' src='/static/task/js/jquery-1.9.1.min.js'></script>
<script type='text/javascript' src='/static/task/task_two/js/jquery.js'></script>
<script type='text/javascript' src='/static/task/task_two/js/jquery.circliful.min.js'></script>
<script type="text/javascript">
  $(function(){
    $('#myStat').circliful();
    $('.guangzhu span').click(function() {
      $('.guangzhu').css('display', 'none');
    });
  })
</script>

<script type="text/javascript" charset='utf-8' src="/static/task/task_two/js/public.js"></script>
<script>
/*$(document).ready(function(){
    $("#but-03").click(function(){fBut();});
    $(".rpic").click(function(e){
        $("#connDiv").slideToggle("slideToggle");
        e.stopPropagation();
    });
    $('body').click(function(e) {
        var target = $(e.target);
        if(!target.is('#rpic') && !target.is('#connDiv')) {
            if ( $('#connDiv').is(':visible') ) $('#connDiv').hide();
        }
        });
});*/
			function timer01(){
				$("#connDiv").slideDown(300);
				var timer01 = setTimeout(function(){
					$("#connDiv").hide();
				},5000);
			}
			$(document).ready(function() {
				$("#but-03").click(function() {
					fBut();
				});
			});

</script>
</head>

<body <?php if (isset($userinfo['can_select'])) { ?>onLoad="WC('',Content('raising'))"<?php } ?>>
<!----头部返回按钮---->
<!--<div class="topBut">
<a href="/index.php/nonstandard/system/welcome" class="back">返回首页</a>
<a href="http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=401817569&idx=1&sn=2efaf2d1353a2e318f3ba4fa4be4edee#rd" class="help">帮助中心</a>
</div>-->
<?php if (isset($is_attention)&&$is_attention==1) { ?>
<div class="guangzhu">
    <img src="/static/task/images/guangzhu.jpg" alt="" />
    <span>关闭</span>
</div> 
<?php } ?>

<div class="whole">
    <!----广告位轮播---->
    <div class="banner">
    <div class="topBut">
    	<a href="http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=401817569&idx=1&sn=2efaf2d1353a2e318f3ba4fa4be4edee#rd" class="help">帮助中心</a>
    </div>
    <ul id="blist">
        <li><a href="#">
        	<img src="../../../static/m/images/banner09091.jpg"></a></li>
        <li><a href="javascript:;" onclick="tourl(646)">
        	<img src="../../../static/m/images/bannerFuLiZhan.jpg"></a></li>
        <li><a href="#">
        	<img src="../../../static/m/images/banner09092.jpg"></a></li>
        <li><a href="/index.php/task/usercenter/taskcenter">
        	<img src="../../../static/m/images/banner09093.jpg"></a></li>
        <li><a href="/view/shop/list.html">
        	<img src="../../../static/m/images/banner09094.jpg"></a></li>
        <li><a href="/view/article/knowledgeIdx.html">
        	<img src="../../../static/m/images/banner09095.jpg"></a></li>
    </ul>
    <div id="pagenavi2">
    <a href="javascript:void(0);" class=active>1</a>
    <a href="javascript:void(0);">2</a>
    <a href="javascript:void(0);">3</a>
    <a href="javascript:void(0);">4</a>
    <a href="javascript:void(0);">5</a>
    <a href="javascript:void(0);">6</a>
    </div>
    </div>
    <!----广告位轮播---->
                    <div class="infoDiv">
                        <!-- <div class="inforight">
                            <div class="sign"></div>
                            <div class="report">
                                <a href="javascript:;">
                                    <span onclick="WC('标题',Content('reg'))">签到</span>
                                </a>
                            </div>
                        </div> -->
                        <div class="inforight">
                            <div class="logo"></div>
                            <a <?php if (isset($userinfo)) { ?> onclick="WC('标题',Content('reg'))"<?php }else{ ?>href="/index.php/nonstandard/system/Login"<?php } ?> class="past">
                                <span>签到<span>
                            </a>
                        </div>
                        <div class="inforleft">
            				<div class="notice">
            					<div class="affiche"></div>
                                <?php if (!empty($announ)) { ?>
            					<div class="detail">
                                    <?php foreach ($announ as $k => $v) { ?><?php echo $v.'<br>'; } ?>
                                </div>
                                <?php } ?>
            				</div>
            				<div class="dynamic"><strong>动态</strong>
            					<p>
            						<MARQUEE scrollAmount=1 scrollDelay=77 direction=left width=90% height=18><?php shuffle($taskinscr);  foreach ($taskinscr as $k => $v) { ?><?php echo $v.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?><?php } ?></MARQUEE>
            					</p>
            				</div>
            			</div>
                    </div>

                    <div class="mainstream">
            		    <a class="trading" href="/view/shop/list.html">
            		        <p class="bulk">通花商城</p>
            		        <p class="weal">通花兑换超值礼品<p>
            		        <div class="lcon"></div>
            		    </a>
            		    <div class="channel">
            		        <a class="taste" href="/view/article/knowledgeIdx.html">
            		            <div class="pint"></div>
            		            <div class="brief">
                                    <p class="notable">生活攻略库</p>
                                    <p class="recap">生活百科集锦</p>
                                 </div>
            		        </a>
            		        <div class="casual">
            		            <a class="comprise" href="/view/games/gameIndex.html">
            		                <div class="consist">
            		                    <p class="falir">游戏中心<p>
            		                    <p class="trait">多款游戏持续更新</p>
            		                    <div class="graphic"></div>
            		                </div>
            		            </a>
            		            <a class="comprise anchor" href="/view/games/anchor.html">
                                     <div class="consist">
                                          <p class="falir">主播专区<p>
                                          <p class="trait">美女直播实时观看</p>
                                          <div class="graphic"></div>
                                     </div>
                                </a>
                                <div class="line"></div>
            		        </div>
            		    </div>

            		</div>

    <div class="taskDiv"><strong><sapn href="">做任务</span>　<span href="">领奖励</span></strong><span>活动参与人数：<font class="green"><?php echo $all_user; ?></font></div>
    <div class="locate">
        <div class="mission">
            <div class="include">
                <div class="classify">
                    <a class="motif active" onclick="taskSort(0)" href="javascript:;">全部任务</a>
                </div>
                <div class="classify">
                     <a class="motif" onclick="taskSort(1)" href="javascript:;">得现金任务</a>
                </div>
                <div class="classify">
                      <a class="motif" onclick="taskSort(2)" href="javascript:;">得通花任务</a>
                </div>
                <div class="classify">
                      <a class="motif" onclick="taskSort(3)" href="javascript:;">已完成任务</a>
                </div>
            </div>
        </div>

    </div>


    <!----List Start---->
    <div class="inList">
    <ul class="tasklist">
    </ul>
    <div class="clear"></div>
    </div>
    <!----List End---->


    <button id="but-03">点击加载</button>
    <div class="footTxt seefinal" style="margin-top:0px;">神秘任务持续更新中，敬请期待！</div>
    <div class="footTxt" style="margin-top:60px"></div>
    <div class="bottomNav">
        <div class="select">
            <a href="/index.php/nonstandard/system/welcome">回收/寄售</a>
            <a href="/index.php/task/usercenter/taskcenter" class="select02">福利</a>
            <a href="/view/shop/list.html">商城</a>
            <a href="/index.php/nonstandard/center/ViewCenter">我的</a>
        </div>
    </div>
</div>

<?php if ($news==1) { ?>
<!-- <div class="blighted"></div>
<div class="framed">
    <a class="graphic" href="/view/task/awards.html">
        <img />
    </a>
    <div class="gown" align="center">
        <div class="line"></div>
    </div>
    <div class="shut" align="center">
        <a class="close-btn" href="javascript:;"></a>
    </div>
</div>  -->
<?php } ?>

<script type="text/javascript" src="/static/task/task_two/js/touchScroll.js"></script>
<script type="text/javascript" src="/static/task/task_two/js/touchslider.dev.js"></script>
<script type="text/javascript">
var w = document.documentElement.clientWidth;
var h = document.documentElement.clientHeight;
var t1=new TouchScroll({id:'wrapper','width':5,'opacity':0.7,color:'#555',minLength:20});
$("#pagenavi2").css("display","block");
	var active=0,
	as=document.getElementById('pagenavi2').getElementsByTagName('a');
	
	for(var i=0;i<as.length;i++){
		(function(){
			var j=i;
			as[i].onclick=function(){
				t2.slide(j);
				return false;
			}
		})();
	}
	
	$("#blist").css("display","block");
	var t2=new TouchSlider({id:'blist', speed:600, timeout:3000, before:function(index){
	as[active].className='';
	active=index;
	as[active].className='active';
	}});
// window.onresize=function(){location.reload()}
</script>

<script> 
/* var oMarquee = document.getElementById("mq"); //滚动对象 
var iLineHeight = 30; //单行高度，像素 
var iLineCount = 3; //实际行数 
var iScrollAmount = 1; //每次滚动高度，像素 
function run() { 
oMarquee.scrollTop += iScrollAmount; 
if ( oMarquee.scrollTop == iLineCount * iLineHeight ) 
oMarquee.scrollTop = 0; 
if ( oMarquee.scrollTop % iLineHeight == 0 ) { 
window.setTimeout( "run()", 6000 ); 	
} else { 
window.setTimeout( "run()", 50 ); 
}
} 
oMarquee.innerHTML += oMarquee.innerHTML; 
window.setTimeout( "run()", 3500 ); 

 */
</script>

<script src="/static/task/js/ajax_sign.js?v=1000"></script>
<script type="text/javascript" src="/static/task/js/bounced.js"></script>
<script type="text/javascript" src="/static/task/js/task_list.js?v=1005"></script>
<script type="text/javascript" src="/static/home/js/ajax_common.js"></script>
<script type="text/javascript">
    fBut();
    getasklist();

<?php if (isset($userinfo)) { ?>
function Content($type){
	switch($type){
		case 'reg':{
		    Contents = '<div class="box">'
		   	+ "<img class='userImg'> "
		   	+"<b onclick='closeWindow();'></b>"
		   	+"<font size='+1' class='qdCont'></font>"
		   	+"<p class='award'></p>"
		   	+"<p class='huiyuan lvTian'></p>"
		   	+"<div class='giftBag'><a href='javascript:;' class='awayBuy'></a><a href='javascript:;' onClick='closeqd()' class='laterBuy'></a></div>"
		    +"</div>"
		   	;
		   	var signdata = usersign(<?php echo $wx_id; ?>);
		}
		break;
		case 'raising':{
			 /** <?php if (isset($userinfo['can_select'])){ ?>
		        Contents = "<form action="+'<?php echo site_url('task/usercenter/select_level_title'); ?>'+" method='post'><div class='wcDiv'><div class='raisBg'><b onclick='closeWindow();'>x</b><div class='sImg'><img src='/static/task/task_two/img/jiang.png'></div><span class='f14 green'>恭喜您升级啦！现在到达第"+<?php echo $medal_num+1; ?>+"级</span><br>点击下方选项卡选择新的称号<br></div><div id='kks' class=rankUl><ul>";
		        <?php foreach ($userinfo['can_select'] as $k => $v) { ?>
		            Contents += "<li class='level_k'><a class='chooseName' id='k"+<?php echo $k ?>+"'><span><img src='"+'<?php echo $v['level_img']; ?>'+"'></span>"+'<?php echo $v['level_name']; ?>'+"</a><input style='display:none;' type='radio'  name='select_title' id='level_sub' value='"+<?php echo $v['level_id']; ?>+"' /></li>";
		        <?php } ?>
		        Contents += "</ul></div><button id='but-04' type='submit'>确定</button>";
		        Contents += "</div></form>";
		    <?php } ?>*/
		}
	    break;
	}return Contents;
}
<?php }; ?>
</script>
<!-- 百度统计 -->
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?a337a5249adb71bc3f563821242e0c34";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
//tourl方法  在shop  list.js方法	
function tourl(id){
	var an = $('.left .sn').attr("data-id");
	var a = $('.right').scrollTop();
	window.history.pushState(null, null, '/view/shop/list.html?type='+an+'_'+a);//修改url
	location.href = '/view/shop/info.html?id='+id;
};
</script>
</body>
</html>
