<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>回收通微信后台管理系统</title>
    <link href="/maijinadmin/css/bootstrap.css" rel="stylesheet">
    <link href="/maijinadmin/css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="/maijinadmin/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/maijinadmin/css/morris-0.4.3.min.css">
	<link rel="stylesheet" href="/maijinadmin/css/common.css">
	<link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/addgroup.js"></script>
	<script src="/maijinadmin/js/common.js"></script>
	<script src="/maijinadmin/js/bootstrap.js"></script>
  </head>
  <body>
      <div id="page-wrapper">
        <div class="row">
		 <!--营业网点管理-->		
          <div class="col-lg-6" style="width:100%;">
            <h2>添加/修改地堆小组</h2>
			<div class="table-responsive">			
			    <a class="btn btn-primary" href="javascript:history.go(-1);">返回</a><br/><br/>
				<input type="hidden" id="groupid" value="">
				<div class="form-group">
					<label>小组名称</label><br/>
					<input type="text" class="form-control" placeholder="请输入小组名称" id="g_name" value="" style="width:70%;">
				</div>
				<div class="form-group">
					<label>组长</label><br/>
                    <input type="text" class="form-control" placeholder="请选择组长" id="g_leader_name" style="width:70%;display:inline-block;" value="" readonly="readonly">
                    <input type="hidden" id="g_leader_id" value="">
                    <button type="button" class="btn btn-primary btn-sm btn-select" data-type="leader" onclick="oprionleader();">选择</button>
				</div>
				<div class="form-group">
					<label>主管</label><br/>
                    <input type="text" class="form-control" placeholder="请选择主管" id="g_executives_name" value="" style="width:70%;display:inline-block;" readonly="readonly">
                    <input type="hidden" id="g_executives_id" value="">
                    <button type="button" class="btn btn-primary btn-sm btn-select" data-type="executives">选择</button>
				</div>
                <div class="form-group">
					<label>总监</label><br/>
                    <input type="text" class="form-control" placeholder="请选择总监" id="g_majordomo_name" value="" style="width:70%;display:inline-block;" readonly="readonly">
                    <input type="hidden" id="g_majordomo_id" value="">
                    <button type="button" class="btn btn-primary btn-sm btn-select" data-type="majordomo">选择</button>
				</div>
                <div class="form-group">
					<label>组员</label><br/>
                    <input type="text" class="form-control" placeholder="请选择组员" id="g_member_name" value="" style="width:70%;display:inline-block;" readonly="readonly">
                    <input type="hidden" id="g_member_id" value="">
                    <input type="hidden" id="g_member_old_id" value="">
                    <button type="button" class="btn btn-primary btn-sm btn-select-group">选择</button>
				</div>
				<div class="form-group">
					<label>状态</label><br/>
					<select class="form-control" id="g_status" style="width:70%;">
						<option value="1">有效</option>
						<option value="0">无效</option>
					</select>
				</div>					
				<button type="button" class="btn btn-primary addgroup">保存</button>
		  </div>
        </div>
        <!--选择员工-->
		  <div id="dialog-select" title="选择员工" style="display:none;">
			  <div class="col-lg-6" style="width:100%;">
					<p class="help-block">数据默认不展示，需点击检索查询</p>
                    <input type="hidden" value="" id="gx_type">
					<input class="form-control" placeholder="姓名" style="width:110px;display:inline-block" id="gx_xingming">&nbsp;&nbsp;
			  <input class="form-control" placeholder="负责区域" style="width:110px;display:inline-block" id="gx_address">&nbsp;&nbsp;
			  <button type="button" class="btn btn-primary btn-search">检索</button>
			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>姓名</th>
					<th>邮箱</th>
					<th>手机</th>
                    <th>负责区域</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id='listinfo'>
                </tbody>
              </table>
			  </div>
              <div class="bs-example" style="text-align:center;">
              <ul class="pagination">
				<li><a href="javascript:void(0);">共<span id="pagenum"></span>条 当前页<span id="pagenow"></span>/<span id="pagetotal"></span></a></li>
                <li class="fengye disabled" orgid="first"><a href="javascript:void(0);">首页</a></li>
                <li class="fengye disabled" orgid="prev"><a href="javascript:void(0);">上一页</a></li>
                <li class="fengye disabled" orgid="next"><a href="javascript:void(0);">下一页</a></li>
                <li class="fengye disabled" orgid="last"><a href="javascript:void(0);">尾页</a></li>
              </ul>             
          </div>
		  </div>
          <!--选择组员-->
		  <div id="dialog-select-group" title="选择员工" style="display:none;">
			  <div class="col-lg-6" style="width:100%;">
                <button type="button" class="btn btn-primary btn-xz-group">选择</button>
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>
                    <th><label><input type="checkbox" name="idcell" id="idcell">全选</label></th>
                    <th>姓名</th>
					<th>邮箱</th>
					<th>手机</th>
                    <th>负责区域</th>
                  </tr>
                </thead>
                <tbody id='grouplistinfo'>
                </tbody>
              </table>
			  </div>
		  </div>
      </div>
    </div>   
  </body>
</html>
