<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/task.js"></script>
  </head>
  <body>
    <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row">	 
		  <!--员工管理-->		
          <div class="col-lg-6" style="width:100%;">
			<h1>任务管理</h1>
            
           <!--  <div class="alert alert-success alert-dismissable">
             <p>
			 <b>温馨提示</b>：如果想查看某个员工的业绩，可点击员工的姓名，在弹出窗口中查询
			 </p>
			</div> -->
            <h2>任务列表</h2>
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
			  <button type="button" class="btn btn-primary btn-addEmplyee">添加任务</button>
			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>任务名称</th>
                    <th>任务说明</th>
					<th>任务类型</th>
					<th>任务等级</th>
					<th>任务奖励说明</th>
                    <th>需要签到次数</th>
					<th>需要分享次数</th>
					<th>需要回收金额</th>
					<th>需要邀请用户次数</th>
					<th>需要邀请回收商次数</th>
					<th>需要的分享地址</th>
					<th>需要完成任务地址</th>
					<th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id="content_list">
					<?php                      
                       foreach ($list as $task) {
                       		$status = $task['task_status'] == '1' ? '<font color="blue">有效</font>' : '<font color="red">无效</font>';
                       		echo '<tr>
                       		<td>'.$task['info_name'].'</td>
                       		<td>'.$task['task_content'].'</td>
                       		<td>'.$task['task_type'].'</td>
                       		<td>'.$task['task_level'].'</td>
                       		<td>'.$task['reward_content'].'</td>
                       		<td>'.$task['task_sign'].'</td>
                       		<td>'.$task['task_share'].'</td>
                       		<td>'.$task['task_turnover'].'</td>
                       		<td>'.$task['task_invite_u'].'</td>
                       		<td>'.$task['task_invite_m'].'</td>
                       		<td>'.$task['task_share_url'].'</td>
                       		<td>'.$task['task_url'].'</td>
                           	<td>' . $status . '</td>
                       		<td>
			                   <a href="javascript:;" class="task-edit" orgid="' . $task['task_id'] . '">修改</a>
				               &nbsp;&nbsp;
				               <a href="javascript:;" class="task-delete" orgid="' . $task['task_id'] . '">禁用</a>
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
		  <div id="dialog-emplyee" title="添加员工" style="display:none;">
				<!-- <p class="help-block">以下信息都是必填项<br/>添加员工时密码项若不填写，则为系统默认密码</p> -->
				<input type="hidden" id="task_id" value="">
				<div class="form-group">
					<label>任务名称</label>
					<input type="text" class="form-control" placeholder="请输入名称" name="xingming" id="info_name">
				</div>		
				<div class="form-group">
					<label>任务说明</label>
					<input type="text" class="form-control" placeholder="任务说明" name="maile" id="task_content" value="">
				</div>
				<div class="form-group">
					<label>任务类型</label>
					<select class="form-control" name="power_type" id="task_type">
							<option value="">请选择任务类型</option>
						<?php foreach ($types as $k => $v) { ?>
							<option value="<?php echo $k; ?>"><?php echo $v ?></option>
						<?php } ?>
					</select>
				</div>		
				<div class="form-group">
					<label>任务等级</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数" name="maile" id="task_level" value="">
				</div>
				<div class="form-group">
					<label>任务选择奖励一</label>
					<select class="form-control" name="power_type" id="reward_id1">
						<option value="">无奖励</option>
						<?php foreach ($reward as $k => $v) { ?>
							<option value="<?php echo $v['reward_id']; ?>"><?php echo '奖励 树苗'.$v['reward_integral'].'棵  积分'.$v['reward_all_integral'].'个  奖金'.$v['reward_bonus'].'元  基金'.$v['reward_fund'].'元' ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label>任务选择奖励二</label>
					<select class="form-control" name="power_type" id="reward_id2">
						<option value="">无奖励</option>
						<?php foreach ($reward as $k => $v) { ?>
							<option value="<?php echo $v['reward_id']; ?>"><?php echo '奖励 树苗'.$v['reward_integral'].'棵  积分'.$v['reward_all_integral'].'个  奖金'.$v['reward_bonus'].'元  基金'.$v['reward_fund'].'元' ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label>任务选择奖励三</label>
					<select class="form-control" name="power_type" id="reward_id3">
						<option value="">无奖励</option>
						<?php foreach ($reward as $k => $v) { ?>
							<option value="<?php echo $v['reward_id']; ?>"><?php echo '奖励 树苗'.$v['reward_integral'].'棵  积分'.$v['reward_all_integral'].'个  奖金'.$v['reward_bonus'].'元  基金'.$v['reward_fund'].'元' ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label>任务选择奖励四</label>
					<select class="form-control" name="power_type" id="reward_id4">
						<option value="">无奖励</option>
						<?php foreach ($reward as $k => $v) { ?>
							<option value="<?php echo $v['reward_id']; ?>"><?php echo '奖励 树苗'.$v['reward_integral'].'棵  积分'.$v['reward_all_integral'].'个  奖金'.$v['reward_bonus'].'元  基金'.$v['reward_fund'].'元' ?></option>
						<?php } ?>
					</select>
				</div>		
				<div class="form-group">
					<label>任务奖励可选数量</label>
					<select class="form-control" name="power_type" id="reward_num">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
					</select>
				</div>		
				<div class="form-group">
					<label>任务奖励说明</label>
					<input type="text" class="form-control" placeholder="请输入任务说明" name="mobile" id="reward_content" value="">
				</div>
				<div class="form-group">
					<label>需要签到次数</label>
					<input type="text" class="form-control" placeholder="请输入个数，非此类型任务可以为空" name="address" id="task_sign" value="">
				</div>
				<div class="form-group">
					<label>需要回收金额</label>
					<input type="text" class="form-control" placeholder="请输入个数，非此类型任务可以为空" name="address" id="task_turnover" value="">
				</div>
				<div class="form-group">
					<label>需要邀请用户次数</label>
					<input type="text" class="form-control" placeholder="请输入个数，非此类型任务可以为空" name="address" id="task_invite_u" value="">
				</div>
				<div class="form-group">
					<label>需要邀请回收商次数</label>
					<input type="text" class="form-control" placeholder="请输入个数，非此类型任务可以为空" name="address" id="task_invite_m" value="">
				</div>
				<div class="form-group">
					<label>需要分享次数</label>
					<input type="text" class="form-control" placeholder="请输入个数，非此类型任务可以为空" name="address" id="task_share" value="">
				</div>
				<div class="form-group">
					<label>需要的分享地址</label>
					<input type="text" class="form-control" placeholder="请输入区域，非此类型任务可以为空" name="address" id="task_share_url" value="">
				</div>
				<div class="form-group">
					<label>需要完成任务地址</label>
					<input type="text" class="form-control" placeholder="请输入区域，非此类型任务可以为空" name="address" id="task_url" value="">
				</div>
				<div class="form-group">
					<label>完成任务的期限</label>
					<input type="text" class="form-control" placeholder="请填写大于0的整数，可以不填，单位（天）" name="address" id="task_limit_time" value="">
				</div>
				<div class="form-group">
					<label>在哪个任务完成解锁</label>
					<select class="form-control" name="power_type" id="task_limit_other">
					
					</select>
				</div>	
				<div class="form-group">
					<label>是否有效</label>
					<select class="form-control" name="power_type" id="task_status">
						<option value="1">有效</option>
						<option value="-1">无效</option>
					</select>
				</div>	
				
				<button type="button" class="btn btn-primary addemplyee">保存</button>
			</div>	
			
			<!--查询指定员工业绩页面,内容隐藏-->
		  <div id="dialog-emplyee-yj" title="员工业绩" style="display:none;">				
				<input type="hidden" id="employee-yj-id" value="">
				<input class="form-control datepicker" placeholder="请输入日期" style="width:400px;display:inline-block" id="e_time">
				<button type="button" class="btn btn-primary selectemplyee-yj">检索</button>
				
				<div class="alert alert-success alert-dismissable" style="margin-top:30px;">
					 <p>
					 <b>业绩统计：</b>&nbsp;&nbsp;
					 关注数：<a href="javascript:void(0);" id="e_guanzhu">0</a> &nbsp;&nbsp;&nbsp;&nbsp;
					 已成交：<a href="javascript:void(0);" id="e_order_y">0</a> 单&nbsp;&nbsp;&nbsp;&nbsp;
					 未成交：<a href="javascript:void(0);" id="e_order_d">0</a> 单&nbsp;&nbsp;&nbsp;&nbsp;
					 共收了：<a href="javascript:void(0);" id="e_weight_yj">0</a> 公斤&nbsp;&nbsp;&nbsp;&nbsp;共花费：<a href="javascript:void(0);" id="e_pic_yj">0</a> 元&nbsp;&nbsp;&nbsp;&nbsp;共兑现代金券：<a href="javascript:void(0);" id="e_coupon_num_yj">0</a> 张&nbsp;&nbsp;<a href="javascript:void(0);" id="e_coupon_pic_yj">0</a> 元
					
					 </p>
				</div>
			</div>
        </div>
       </div>
  </body>
</html>
