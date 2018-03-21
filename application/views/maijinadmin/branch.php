<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
   <title>回收通微信后台管理系统</title>
    <!-- Bootstrap core CSS -->
    <link href="../../../maijinadmin/css/bootstrap.css" rel="stylesheet">
    <!-- Add custom CSS here -->
    <link href="../../../maijinadmin/css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../maijinadmin/font-awesome/css/font-awesome.min.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="../../../maijinadmin/css/morris-0.4.3.min.css">
	<link rel="stylesheet" href="../../../maijinadmin/css/common.css">
	<link rel="stylesheet" href="../../../maijinadmin/css/jquery-ui.css">
	<script src="../../../maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script src="../../../maijinadmin/js/jquery-ui.js"></script>
	<script src="../../../maijinadmin/js/time-cn.js"></script>
	<script src="../../../maijinadmin/js/branch.js"></script>
	<script src="../../../maijinadmin/js/common.js"></script>
  </head>
  <body>
      <div id="page-wrapper">
        <div class="row">
		 <!--营业网点管理-->		
          <div class="col-lg-6" style="width:100%;">
            <h2>营业网点管理</h2>
			<div class="table-responsive">			
			  <button type="button" class="btn btn-primary btn-addbranch">添加营业网点</button>			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>活动时间</th>
                    <th>活动地区</th>
					<th>创建时间</th>
					<th>修改时间</th>
                    <th>排序值</th>
                    <th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id='branch_list'>
					
                </tbody>
              </table>
            </div>			
		  </div>
		  
		  <!--添加营业网点页面,内容隐藏-->
		  <div id="dialog-branch" title="添加营业网点" style="display:none;">
				<p class="help-block">以下信息都是必填项</p>
				<input type="hidden" id="branchid" value="">
				
				<div class="form-group">
					<label>活动时间</label>
					<input type="text" class="form-control datepicker" placeholder="请输入时间" id="b_time" readonly="readonly">
				</div>
				<div class="form-group">
					<label>活动地区</label>
					<input type="text" class="form-control" placeholder="请输入地区" id="b_address" value="">
				</div>
				<div class="form-group">
					<label>排序值</label>
					<input type="text" class="form-control" placeholder="请输入排序值" id="b_sort" value="">
					<p class="help-block">排序值为整数或小数，排序值越大，列表显示越靠前</p>
				</div>
				
				
				<div class="form-group">
					<label>状态</label>
					<select class="form-control" id="b_status">
						<option value="1">有效</option>
						<option value="0">无效</option>
					</select>
				</div>					
				<button type="button" class="btn btn-primary addbranch">保存</button>
			</div>	
			
        </div>
      </div>
    <script src="../../../maijinadmin/js/bootstrap.js"></script>
  </body>
</html>
