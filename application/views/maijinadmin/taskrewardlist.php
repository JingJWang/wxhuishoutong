<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/taskreward.js"></script>
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
			  <button type="button" class="btn btn-primary btn-addEmplyee">添加任务奖励</button>
			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>奖励</th>
                    <th>奖励的奖金</th>
					<th>奖励的树苗</th>
					<th>奖励的积分</th>
					<th>奖励的基金</th>
					<th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id="content_list">
					<?php                      
						$num = $pages;
                       foreach ($list as $reward) {
                       		$num++;
                       		$status = $reward['reward_status'] == '1' ? '<font color="blue">有效</font>' : '<font color="red">无效</font>';
                       		echo '<tr>
                       		<td>'.'奖励方式'.$num.'</td>
                       		<td>'.$reward['reward_bonus'].'元</td>
                       		<td>'.$reward['reward_integral'].'棵</td>
                       		<td>'.$reward['reward_all_integral'].'</td>
                       		<td>'.$reward['reward_fund'].'元</td>
                           	<td>' . $status . '</td>
                       		<td>
			                   <a href="javascript:;" class="task-edit" orgid="' . $reward['reward_id'] . '">修改</a>
				               &nbsp;&nbsp;
				               <a href="javascript:;" class="task-delete" orgid="' . $reward['reward_id'] . '">禁用</a>
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
					<label>奖励奖金</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数 单位（元）" name="xingming" id="rbonus">
				</div>		
				<div class="form-group">
					<label>奖励通花</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数 单位（棵）" name="maile" id="rintegral" value="">
				</div>	
				<div class="form-group">
					<label>奖励积分</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数" name="maile" id="all_rintegral" value="">
				</div>
				<div class="form-group">
					<label>奖励基金</label>
					<input type="text" class="form-control" placeholder="请输入大于等于0的整数 单位（元）" name="maile" id="rfund" value="">
				</div>
				<div class="form-group">
					<label>是否有效</label>
					<select class="form-control" name="power_type" id="rstatus">
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
