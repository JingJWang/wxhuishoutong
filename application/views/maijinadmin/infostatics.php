<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/infostatics.js"></script>
  </head>
  <body>
    <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row">	 
      	<div class="alert alert-success alert-dismissable">
             <p>
			 <b>所有订单数查询：</b>&nbsp;&nbsp;
			 共收了：<a href="javascript:;"><?php echo $list['order_count']; ?></a> 次&nbsp;&nbsp;&nbsp;&nbsp;
			 总价格：<a href="javascript:;"><?php echo $list['order_sum']; ?></a> 元&nbsp;&nbsp;&nbsp;&nbsp;
			 今日登录的人数：<a href="javascript:;"><?php echo $list['login_num']; ?></a> 人&nbsp;&nbsp;&nbsp;&nbsp;<!-- 共兑现代金券：<a href="javascript:;">1</a> 张&nbsp;&nbsp;<a href="javascript:;">1</a> 元<br/> -->
			 </p>
			</div>
			<!--查询指定员工业绩页面,内容隐藏-->
		  <div id="dialog-emplyee-yj" title="员工业绩" style="">				
				<input type="hidden" id="employee-yj-id" value="">
				<input class="form-control datepicker" placeholder="请输入日期" style="width:400px;display:inline-block" id="e_time">
				<button type="button" class="btn btn-primary selectemplyee-yj">检索</button>
				
				<div class="alert alert-success alert-dismissable alert-success_next" style="margin-top:30px;">
					<!--  <p>
					 <b>数量统计：</b>&nbsp;&nbsp;
					 登录人数：<a href="javascript:void(0);" id="e_guanzhu">0</a>人 &nbsp;&nbsp;&nbsp;&nbsp;
					 新加入人数：<a href="javascript:void(0);" id="e_guanzhu">0</a>人 &nbsp;&nbsp;&nbsp;&nbsp;
					 成交单数：<a href="javascript:void(0);" id="e_guanzhu">0</a> &nbsp;&nbsp;&nbsp;&nbsp;
					 成交额：<a href="javascript:void(0);" id="e_order_y">0</a> 元&nbsp;&nbsp;&nbsp;&nbsp;
					 回收任务：<a href="javascript:void(0);" id="e_order_d">0</a> 次&nbsp;&nbsp;&nbsp;&nbsp;
					 邀请任务：<a href="javascript:void(0);" id="e_weight_yj">0</a> 次&nbsp;&nbsp;&nbsp;&nbsp;
					 游戏任务：<a href="javascript:void(0);" id="e_weight_yj">0</a> 次&nbsp;&nbsp;&nbsp;&nbsp;
					 </p> -->
				</div>
			</div>
        </div>
       </div>
  </body>
</html>
