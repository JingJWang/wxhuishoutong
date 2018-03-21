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
	<script src="/maijinadmin/js/menu.js"></script>
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
            <h2>菜单管理</h2>
			<div class="alert alert-dismissable alert-warning">
                <p>1、一级菜单个数范围：2-3个，二级菜单个数范围：2-5个，菜单最多支持两层。
				<br>2、点击“保存配置”按钮可以对菜单设置进行保存，但最终只有"<b>发布</b>"后才能生效。
				<br>3、请注意：微信公众平台限制每天可发布100次，请适量操作。发布后请<b>先取消再关注</b>查看实时效果。
				<br>4、一级菜单链接可填可不填，如果一级菜单有链接的话，二级菜单则会失效，只保留一级菜单
				</p>
            </div>
            <div class="bs-example menu-div">
			 <p style="line-height: 35px;margin-bottom: 15px;text-align: center;background: #FF6E01;color: #fff;border-radius: 2px;font-size: 14px;">菜单配置项</p>
			 <div style="line-height:30px;">
				<button type="button" class="btn btn-primary add-menu">增加一级菜单</button>
			 </div>
              <ul class="list-group menu-group">
              </ul>
			  <div class="fabu-div">
				<button type="button" class="btn btn-primary fabu-menu">菜单发布</button>
			  </div>
            </div>
			<div class="menu-info">
				<div class="form-group">
					<label>名称<span style="color:red;">*</span></label>
					<input class="form-control menu-info-name">
				</div>
				<div class="form-group">
					<label>链接<span style="color:red;">*</span></label>
					<textarea class="form-control menu-info-link" rows="3"></textarea>
				</div>
				<div>
					<button type="button" class="btn btn-primary save-edit">保存配置</button>
				</div>
			</div>			
		  </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
  </body>
</html>
