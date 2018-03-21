<!--回收商管理 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="/static/system/css/public.css" rel="stylesheet" type="text/css" />
<link href="/static/system/css/temp.css" rel="stylesheet" type="text/css" />
<script src="/static/system/js/jquery-1.7.2.js"></script>
<script src="/static/system/js/public.js"></script>
<title>回收商管理</title>
</head>
<body>
<!--深色头部-->
<div class="head">
	<!--左侧标题-->
	<h2>欢迎进入回收通管理系统！</h2>
    <!--右侧信息-->
    <div class="head-right">
    	<ul>
    		<li><p class="p1">退出</p></li>
        	<li class="l1"><p class="p2"><span class="sp2"></span>50</p></li>
        	<li><p>你好，管理员<span class="sp1"></span></p></li>
        </ul>
    </div>
</div>
<!--深色头部-->
<!--头部导航-->
<div class="navtop">
	<div class="navlogo"><img src="/static/system/img/logo-nav.png" width="178" height="60" /></div>
    <div class="navtop_right">
    	<ul>
        	<li><a href="#">上月销售榜</a></li>
            <li><a href="#">本月销售榜</a><span>|</span></li>
            <li><a href="#">区域排行</a><span>|</span></li>
            <li><a href="#">通讯录</a><span>|</span></li>
        	<li><a href="#">我的</a><span>|</span></li>  
        </ul>
    </div>
</div>
<!--头部导航-->
<div class="mainall">
<!--左侧导航-->
<div class="navleft" id="navleft">
	<ul>
    	<li class="on"><span></span><a href="#">回收商管理</a><span class="sp3"></span></li>
        <li><span class="sp1"></span><a href="#">学生查询</a></li>
        <li><span class="sp2"></span><a href="#">消息通知</a></li>
    </ul>
</div>
<!--左侧导航-->
<div class="main" id="right">
	<div class="studenttab">
    	<div class="tabhead">
			<ul>
            	<li class="on"><span class="label_student"></span>学生管理</li>
				<li><span class="label_adminClass"></span>班级设置</li>	
				<li><span class="label_Evaluation"></span>讲师测评</li>
				<li><span class="label_Planner"></span>职业规划师测评</li>
                <li><span class="label_Discipline"></span>学员违纪</li>
                <li><span class="label_employment"></span>就业信息</li>
                <li><span class="label_Journal"></span>教学日志</li>	
   			</ul>
        </div>
        <div class="tabmid">
        	<div class="tabmidDiv" id="tabmidDiv1">
				<p>地区：</p>
				<a href="javascript:void(0)" class="on">全部</a>
				<a href="javascript:void(0)">北京中心</a>
				<a href="javascript:void(0)">大连中心</a>
				<a href="javascript:void(0)">上海中心</a>
				<a href="javascript:void(0)">广州中心</a>
				<a href="javascript:void(0)">郑州中心</a>
				<a href="javascript:void(0)">西安中心</a>
				<br class="cle" />
			</div>
			<div class="tabmidDiv" id="tabmidDiv2">
				<p>班级类型：</p>
				<a href="javascript:void(0)" class="on">全部</a>
				<a href="javascript:void(0)">iOS就业班</a>
				<a href="javascript:void(0)">Android就业班</a>
				<a href="javascript:void(0)">Unity-3D就业班</a>
				<a href="javascript:void(0)">HTML5就业班</a>
				<br class="cle" />
			</div>
			<div class="lookFor lookFor_student" id="lookFor"><!--搜索栏-->
                <input type="text" value="" placeholder="搜索班级" class="text"  maxlength="15" />
                <input type="button" class="butt" />
                <div class="lookForHide">
                    <ul>
                        <li>aa心</li>
                        <li>nihao </li>
                        <li>aoo</li>
                        <li>ios</li>
                    </ul>
                </div>
            </div>
        </div>
</div>
</div>
</body>
</html>