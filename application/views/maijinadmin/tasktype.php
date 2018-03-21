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
	<script src="/maijinadmin/js/tasktype.js"></script>
	<script src="/maijinadmin/js/common.js"></script>
	<link rel="stylesheet" href="/maijinadmin/kindeditor/themes/default/default.css" />
	<script charset="utf-8" src="/maijinadmin/kindeditor/kindeditor-min.js"></script>
	<script charset="utf-8" src="/maijinadmin/kindeditor/lang/zh_CN.js"></script>
	
  </head>
  <body>
      <div id="page-wrapper">
      <div class="row">		 
		  <!--营业网点管理-->		
          <div class="col-lg-6" style="width:100%;">
            <h2>使用说明管理</h2>
			<div class="table-responsive">			
			  <button type="button" class="btn btn-primary btn-instruction">添加任务类型</button>			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>类型数字</th>
                    <th>具体任务的id</th>
					<th>创建时间</th>
					<th>修改时间</th>
					<th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id='instruction_list'>
                <?php foreach ($list as $k => $v) { ?>
                	<tr>
						<td><?php echo $v['type_num']; ?></td>
						<td><?php if ($v['task_id']==0) {echo '无';}else{ echo $v['task_id'];} ?></td>
						<td><?php echo date("Y-m-d H:i",$v['task_jointime']); ?></td>
						<td><?php if($v['task_uptime']!='') {echo date("Y-m-d H:i",$v['task_uptime']);} ?></td>
						<?php if ($v['task_status']==1) {
							echo '<td>有效</td>';
						}else{
							echo '<td><font color="red">无效</font></td>';
							} ?>
						<td><a class="tasktype-edit" href="javascript:void(0);" orgid="<?php echo $v['type_id'] ?>" >修改</a>&nbsp;&nbsp;<a class="tasktype-delete" href="javascript:void(0);" orgid="<?php echo $v['type_id'] ?>">禁用</a></td>
					</tr>
                <?php } ?>
                </tbody>
              </table>
            </div>			
		  </div>
		  
		  <!--添加营业网点页面,内容隐藏-->
		  <div id="dialog-instruction" title="添加使用说明" style="display:none;">
				<p class="help-block">填写下面信息</p>
				<input type="hidden" id="tasktypeid" value="">
				
				<div class="form-group">
					<label>任务类型</label>
					<select class="form-control" id="types">
						<option value="1">签到</option>
						<option value="2">回收</option>
						<option value="3">分享</option>
						<option value="4">精华</option>
						<option value="5">邀请用户</option>
						<option value="6">邀请回收商</option>
						<option value="7">主线</option>
						<option value="8">活动</option>
						<option value="9">点链接</option>
						<option value="10">投票</option>
					</select>
				</div>
				<div>
					<label>具体任务id</label>
					<input class="form-control datepicker" placeholder="请输入名称（只能是数字,可以不填）" id="taskid">
				</div>
				<div class="form-group">
					<label>任务过程</label>
					<textarea id="i_content" style="width:100%;height:200px;visibility:hidden;" name="i_content"></textarea>
				</div>
				<div class="form-group">
					<label>状态</label>
					<select class="form-control" id="i_status">
						<option value="1">有效</option>
						<option value="0">无效</option>
					</select>
				</div>					
				<button type="button" class="btn btn-primary addtasktype">保存</button>
			</div>
			
			<!--内容预览,内容隐藏-->
			<div id="dialog-instruction-condiv" title="内容预览" style="display:none;">
				<div style="padding:10px;margin-top:20px;border:1px solid #428bca;border-radius: 4px;" id="dialog-instruction-content">
					
				</div>
			</div>
        </div><!-- /.row -->
		
      </div><!-- /#page-wrapper -->
	
	<script src="/maijinadmin/js/bootstrap.js"></script>
  </body>
</html>
