<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/tasklevel.js"></script>
	<script src="/maijinadmin/ajaxUpload/scirpt/ajaxupload.js"></script>
	<style type="text/css">
      .add_nav{ width:90%; height:auto;overflow:hidden; position:relative; margin:0 auto;}
      .upload{ width:100px; height:100px; text-align:center; }
      .upload img{ height:100%; width:auto;}
      .yin_file{width:100px; height:100px;  display:block; position:absolute; top:0rem;opacity: 0; }
    </style>
    <script type="text/javascript">
            $(function ()
            {
                // 创建一个上传参数
                var uploadOption =
                {
                    // 提交目标
                    action: "<?php echo site_url(); ?>/maijinadmin/tasklevel/upload",
					// 服务端接收的名称
					name: "file",
                    // 自动提交
                    autoSubmit: true,
                    // 选择文件之后…
                    onChange: function (file, extension) {
                        if (new RegExp(/(jpg)|(jpeg)|(bmp)|(gif)|(png)/i).test(extension)) {
                            $("#filepath").val(file);
                        } else {
                            alert("只限上传图片文件，请重新选择！");
                        }
                    },
                    // 开始上传文件
                    onSubmit: function (file, extension) {
                        $("#state").val("正在上传" + file + "..");
                    },
                    // 上传完成之后
                    onComplete: function (file, response) {
                    	// var result=eval(response);
                    	var obj = eval ("(" + response + ")");//解析json
                    	if (obj.status==1) {
                    		$("#img1").attr('src',obj.info);
                    		$("#limg").attr('value',obj.info);
                    		alert('修改成功');
                    	}else{
                    		alert(obj.info);
                    	};
                    	 
                    	
                    }
                }

                // 初始化图片上传框
                var oAjaxUpload = new AjaxUpload('#selector', uploadOption);

                // 给上传按钮增加上传动作
                $("#up").click(function ()
                {
                    oAjaxUpload.submit();
                });
            });
        </script>
  </head>
  <body>
    <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row">	 
		  <!--员工管理-->		
          <div class="col-lg-6" style="width:100%;">
			<h1>任务等级列表</h1>
            
            <!-- <div class="alert alert-success alert-dismissable">
             <p>
			 <b>温馨提示</b>：如果想查看某个员工的业绩，可点击员工的姓名，在弹出窗口中查询
			 </p>
			</div> -->
            <h2>等级列表</h2>
			<div class="table-responsive">
			  <!-- <input class="form-control" placeholder="姓名" style="width:110px;display:inline-block" id="s_xingming">&nbsp;&nbsp;
			  <input class="form-control" placeholder="负责区域" style="width:110px;display:inline-block" id="s_address">&nbsp;&nbsp;
			  <select class="form-control" style="width:150px;display:inline-block" id="s_pay">
				<option value="">-=支付方式=-</option>
				<option value="1">红包</option>
				<option value="0">现金</option>
			  </select>&nbsp;&nbsp;
			  <select class="form-control" style="width:130px;display:inline-block" id="s_status">
				<option value="">-=状态=-</option>
				<option value="1">有效</option>
				<option value="0">无效</option>
			  </select>&nbsp;&nbsp;
			  <button type="button" class="btn btn-primary btn-search">检索</button>&nbsp;&nbsp; -->
			  <button type="button" class="btn btn-primary btn-addEmplyee">添加任务等级</button>
			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>等级名称</th>
					<th>等级级数</th>
                    <th>等级图片</th>
					<th>升级需要的积分（积分）</th>
					<th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id="content_list">
					<?php                      
						$num = $pages;
                       foreach ($list as $level) {
                       		$num++;
                       		$status = $level['level_status'] == '1' ? '<font color="blue">有效</font>' : '<font color="red">无效</font>';
                       		echo '<tr>
                       		<td>'.$level['level_name'].'</td>
                       		<td>'.$level['level_num'].'级</td>
                       		<td><a target="_blank" href="'.$level['level_img'].'">查看</a></td>
                       		<td>'.$level['level_integral'].'</td>
                           	<td>' . $status . '</td>
                       		<td>
			                   <a href="javascript:;" class="task-edit" orgid="' . $level['level_id'] . '">修改</a>
				               &nbsp;&nbsp;
				               <a href="javascript:;" class="task-delete" orgid="' . $level['level_id'] . '">禁用</a>
			            	</td>
                       		</tr>';                       
                      }?>
                </tbody>
              </table>
            </div>			
		  </div>
		  <div class="bs-example" style="text-align:center;" id="content_page">
              <?php echo $page ;?>             
          </div>
		  <div class="bs-example" style="text-align:center;" id="content_ajax_page">
               
          </div>
		  <!--添加任务,内容隐藏-->
		  <div id="dialog-emplyee" title="添加任务奖励" style="display:none;"><!-- 
				<p class="help-block">以下信息都是必填项<br/>添加员工时密码项若不填写，则为系统默认密码</p> -->
				<input type="hidden" id="num" value="">
				<div class="form-group">
					<label>等级名称</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数 单位（元）" name="xingming" id="lname">
				</div>		
				<div class="form-group">
					<label>等级级数</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数 单位（棵）" name="maile" id="levelnum" value="">
				</div>
				<div class="form-group">
					<label>等级图片</label>
					
					<div class="add_nav">
						<input type="hidden" id="limg" >
                  		<input type="file" name="imgs" id="selector" class="yin_file" />
                  		<div  class="upload">
                    		<img src="/static/task/images/addimg.png" id="img1" >     
                  		</div>
                      
            		</div>
				</div>
				<div class="form-group">
					<label>升级需要的积分（积分）</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数 单位（元）" name="maile" id="lfund" value="">
				</div>
				<div class="form-group">
					<label>是否有效</label>
					<select class="form-control" name="power_type" id="lstatus">
						<option value="1">有效</option>
						<option value="-1">无效</option>
					</select>
				</div>		
				
				<button type="button" class="btn btn-primary addemplyee">保存</button>
			</div>	
			
        </div>
       </div>
  </body>
</html>
