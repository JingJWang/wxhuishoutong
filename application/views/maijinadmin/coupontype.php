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
	<link rel="stylesheet" href="/maijinadmin/css/common.css">
	<link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/coupon.js"></script>
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
            <h2>代金券列表</h2>
			<div class="table-responsive">			 
              <table class="table table-bordered table-hover tablesorter">
                <thead>
                  <tr>					
                    <th>名称</th>
                    <th>金额</th>  
					<th>有效期</th>					
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id="coupon_list">
				
                </tbody>
              </table>
            </div>			
		  </div>
		 <!--修改优惠券-->
		 <div id="dialog-coupon" title="修改代金券金额" style="display:none;">
			<input type="hidden" value="" id="id-coupon">
			<div class="form-group">
				<label>金额<span style="color:red;">*</span></label>
				<input class="form-control pic-coupon" placeholder="请输入金额">
				<p class="help-block">类型为整数或小数</p>
			</div>
			<div class="form-group">
				<label>有效期<span style="color:red;">*</span></label>
				<input class="form-control day-coupon" placeholder="请输入有效期">
				<p class="help-block">填写整数，单位默认是天,无需填写</p>
			</div>
			<button type="button" class="btn btn-primary edit-coupon">保存</button>				
			</div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->    
  </body>
</html>
