<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
   <title>回收通微信后台管理系统</title>
    <!-- Bootstrap core CSS -->
    <link href="/maijinadmin/css/bootstrap.css" rel="stylesheet">
    <!-- Add custom CSS here -->
    <link href="/maijinadmin/css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="/maijinadmin/font-awesome/css/font-awesome.min.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="/maijinadmin/css/morris-0.4.3.min.css">
	<link rel="stylesheet" href="/maijinadmin/css/menu.css">
	<link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/weixin_user.js"></script>
	<script src="/maijinadmin/js/common.js"></script>
	<script src="/maijinadmin/js/bootstrap.js"></script>
  </head>
  <body>
      <!-- Sidebar -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="javascript:void(0);">回收通微信后台管理系统</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          
        </div><!-- /.navbar-collapse -->
      </nav>
		
      <div id="page-wrapper">

       

      <div class="row">		 
		  
		  <!--员工管理-->		
          <div class="col-lg-6" style="width:100%;">
            <h2>微信用户统计</h2>
			<div class="alert alert-success alert-dismissable">
                <p>&nbsp;&nbsp;&nbsp;&nbsp;总用户：<a href="javascript:void(0);" id="total" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;今天新增：<a href="javascript:void(0);" id="todaytotal" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;昨天新增：<a href="javascript:void(0);" id="yesterdaytotal" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;本月新增：<a href="javascript:void(0);" id="monthtotal" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;取消关注用户总数：<a href="javascript:void(0);" id="total_wx" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;今天取消关注：<a href="javascript:void(0);" id="todaytotal_wx" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;昨天取消关注：<a href="javascript:void(0);" id="yesterdaytotal_wx" class="wxuser-count">0</a>&nbsp;&nbsp;&nbsp;&nbsp;本月取消关注：<a href="javascript:void(0);" id="monthtotal_wx" class="wxuser-count">0</a></p>
				<input type="hidden" value="" id="timetype">
            </div>
           <div class="table-responsive">
		   
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>头像</th>
                    <th>昵称</th>
					<th>加入时间</th>
					<th>活跃时间</th>
					<th>扫码来源</th>
                    <th>扫码地区</th>
					<th>订单详情</th>
					<th>代金券详情</th>
                  </tr>
                </thead>
                <tbody id='weixinuser_list'>
					
                </tbody>
              </table>
            </div>			
		  </div>
		  <div class="bs-example" style="text-align:center;">
			  <ul class="pagination">
				<li><a href="javascript:void(0);">共<span id="pagenum"></span>条 当前页<span id="pagenow">1</span>/<span id="pagetotal"></span></a></li>
				<li class="fengye" orgid="first"><a href="javascript:void(0);">首页</a></li>
                <li class="fengye" orgid="prev"><a href="javascript:void(0);">上一页</a></li>
                <li class="fengye" orgid="next"><a href="javascript:void(0);">下一页</a></li>
                <li class="fengye" orgid="last"><a href="javascript:void(0);">尾页</a></li>
			  </ul>             
		 </div>
		 
		  <!--用户的所有订单-->
		  <div id="dialog-user-order" title="用户的所有订单" style="display:none;">
			  <div class="col-lg-6" style="width:100%;">
					<p class="help-block">数据默认不展示，需点击检索查询</p>
					<input type="hidden" value="" id="order-user-openid">
					<input class="form-control" placeholder="处理人" style="width:10%;display:inline-block"id="u_o_user_id">&nbsp;&nbsp;
					<input class="form-control datepicker" placeholder="报单时间" style="width:110px;display:inline-block" id="u_o_time">&nbsp;&nbsp;
					<select class="form-control" style="width:120px;display:inline-block" id="u_o_status">
					    <option value="">-=状态=-</option>
						<option value="1">未成交</option>
						<option value="0">已成交</option>
						<option value="<0">已作废</option>
					</select>&nbsp;&nbsp;
					<button type="button" class="btn  btn-primary  btn-user-order">检索</button>
					<table class="table table-bordered table-hover tablesorter" style="margin-top:10px;width:100%;">
						<thead>
						  <tr>
							<th>手机</th>
							<th>地址</th>
							<th>小区名称</th>
							<th>种类</th>
							<th>件数</th>
							<th>重量(KG)</th>
							<th>价格(元)</th>
							<th>报单时间</th>
							<th>成交时间</th>
							<th>成交地点</th>
							<th>状态</th>
							<th>处理人</th>
							<th>备注</th>
						  </tr>
						</thead>
						<tbody id="user_order_list">
						 
						</tbody>
					  </table>
			  </div>
			  <div class="bs-example" style="text-align:center;">
				  <ul class="pagination u_o_ul">
					<li><a href="javascript:void(0);">共<span id="u_o_pagenum"></span>条 当前页<span id="u_o_pagenow"></span>/<span id="u_o_pagetotal"></span></a></li>
					<li class="u_o_fengye disabled" orgid="first"><a href="javascript:void(0);">首页</a></li>
					<li class="u_o_fengye disabled" orgid="prev"><a href="javascript:void(0);">上一页</a></li>
					<li class="u_o_fengye disabled" orgid="next"><a href="javascript:void(0);">下一页</a></li>
					<li class="u_o_fengye disabled" orgid="last"><a href="javascript:void(0);">尾页</a></li>
				  </ul>             
			</div>
		  </div>
		 
		 
		 <!--用户的所有代金券-->
		  <div id="dialog-user-coupon" title="用户的所有代金券" style="display:none;">
			  <div class="col-lg-6" style="width:100%;">
					<input type="hidden" value="" id="coupon-user-openid">					
					<input class="form-control datepicker" placeholder="使用时间" style="width:110px;display:inline-block" id="u_c_time">&nbsp;&nbsp;
				    <select class="form-control" style="width:150px;display:inline-block" id="u_c_type">
						<option value="">-=类型=-</option>
						<option value="1">关注</option>
						<option value="2">下单</option>
						<option value="3">每周分享</option>
						<option value="4">成交订单分享</option>
				    </select>&nbsp;&nbsp;
				    <select class="form-control" style="width:130px;display:inline-block" id="u_c_status">
						<option value="">-=状态=-</option>
						<option value="2">已使用</option>
						<option value="1">未使用</option>
						<option value="-1">已过期</option>
				    </select>&nbsp;&nbsp;
					<button type="button" class="btn  btn-primary  btn-user-coupon">检索</button>
					<table class="table table-bordered table-hover tablesorter" style="margin-top:10px;width:100%;">
						<thead>
						  <tr>
							<th>类型</th>
							<th>金额</th>
							<th>添加时间</th>
							<th>使用时间</th>
							<th>过期时间</th>
							<th>状态</th>
						  </tr>
						</thead>
						<tbody id="user_coupon_list">						 
						</tbody>
					  </table>
			  </div>
			  <div class="bs-example" style="text-align:center;">
				  <ul class="pagination u_c_ul">
					<li><a href="javascript:void(0);">共<span id="u_c_pagenum"></span>条 当前页<span id="u_c_pagenow"></span>/<span id="u_c_pagetotal"></span></a></li>
					<li class="u_c_fengye disabled" orgid="first"><a href="javascript:void(0);">首页</a></li>
					<li class="u_c_fengye disabled" orgid="prev"><a href="javascript:void(0);">上一页</a></li>
					<li class="u_c_fengye disabled" orgid="next"><a href="javascript:void(0);">下一页</a></li>
					<li class="u_c_fengye disabled" orgid="last"><a href="javascript:void(0);">尾页</a></li>
				  </ul>             
			</div>
		  </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
  </body>
</html>
