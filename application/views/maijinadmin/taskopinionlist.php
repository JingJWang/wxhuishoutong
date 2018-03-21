<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/taskopinion.js"></script>
  </head>
  <body>
    <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row">	 
		  <!--员工管理-->		
          <div class="col-lg-6" style="width:100%;">
			<h1>任务奖励管理</h1>
            
            <!-- <div class="alert alert-success alert-dismissable">
             <p>
			 <b>温馨提示</b>：如果想查看某个员工的业绩，可点击员工的姓名，在弹出窗口中查询
			 </p>
			</div> -->
            <h2>奖励列表</h2>
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
			  <!-- <button type="button" class="btn btn-primary btn-addEmplyee">添加任务奖励</butt	on> -->
			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>用户名称</th>
                    <th>手机号码</th>
                    <th>加入时间</th>
					<th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id="content_list">
					<?php        
                       foreach ($list as $val) {
                       		switch ($val['opinion_status']) {
                       			case 1:
                       				$status = '<td>未采纳</td>';
                       				break;
                       			case 2:
                       				$status = '<td style="color:red">已采纳</td>';
                       				break;
                       			case -1:
                       				$status = '已删除';
                       				break;
                       			default:
                       				break;
                       		}
                       		echo '<tr>
                       		<td>'.$val['wx_name'].'</td>
                       		<td>'.$val['wx_mobile'].'</td>
                       		<td>'.date('Y-m-d H:i:s',$val['opinion_join_time']).'</td>
                           	' . $status . '
                       		<td>
			                   <a href="javascript:;" class="task-edit" orgid="' . $val['opinion_id'] . '">查看</a>
				               &nbsp;&nbsp;';
				               
				            echo '
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
					<label>姓名</label>
					<div class="form-control" id="opinion_name"></div>
				</div>		
				<div class="form-group">
					<label>电话号码</label>
					<div class="form-control" id="user_mobile"></div>
				</div>
				<div class="form-group">
					<label>评论时间</label>
					<div id="user_time"></div>
				</div>
				<div class="form-group">
					<label>评论内容</label>	
					<div style="word-break:break-all" id="content"></div>
				</div>
				<div class="form-group">
					<label>奖励</label>
					<select class="form-control" name="power_type" id="rewards">
						<option value="">无奖励</option>
						<?php foreach ($reward as $k => $v) { ?>
							<option value="<?php echo $v['reward_id']; ?>"><?php echo '奖励 树苗'.$v['reward_integral'].'棵  奖金'.$v['reward_bonus'].'元  基金'.$v['reward_fund'].'元' ?></option>
						<?php } ?>
					</select>
				</div>
				<!-- <div class="form-group">
					<label>是否有效</label>
					<select class="form-control" name="power_type" id="rstatus">
						<option value="1">有效</option>
						<option value="-1">无效</option>
					</select>
				</div>	 -->	
				
				<button type="button" class="btn btn-primary adoption">采纳</button>
			</div>	
			
        </div>
       </div>
  </body>
</html>
