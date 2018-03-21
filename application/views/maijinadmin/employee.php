<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/m_user.js"></script>
  </head>
  <body>
    <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row">	 
		  <!--员工管理-->		
          <div class="col-lg-6" style="width:100%;">
			<h1>员工业绩统计</h1>
            
            <div class="alert alert-success alert-dismissable">
             <p>
			 <b>所有有效员工总业绩统计：</b>&nbsp;&nbsp;
			 共收了：<a href="javascript:;"><?php echo $sumweight; ?></a> 公斤&nbsp;&nbsp;&nbsp;&nbsp;共花费：<a href="javascript:;"><?php echo $sumpic; ?></a> 元&nbsp;&nbsp;&nbsp;&nbsp;共兑现代金券：<a href="javascript:;"><?php echo $sumvouchernum; ?></a> 张&nbsp;&nbsp;<a href="javascript:;"><?php echo $sumvoucherpic; ?></a> 元<br/>
			 <b>温馨提示</b>：如果想查看某个员工的业绩，可点击员工的姓名，在弹出窗口中查询
			 </p>
			</div>
            <h2>员工列表</h2>
			<div class="table-responsive">
			  <input class="form-control" placeholder="姓名" style="width:110px;display:inline-block" id="s_xingming">&nbsp;&nbsp;
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
			  <button type="button" class="btn btn-primary btn-search">检索</button>&nbsp;&nbsp;
			  <button type="button" class="btn btn-primary btn-addEmplyee">添加员工</button>
			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>姓名</th>
                    <th>职务</th>
					<th>邮箱</th>
					<th>手机</th>
                    <th>负责区域</th>
					<th>支付方式</th>
                    <th>二维码</th>
					<th>用户名</th>
					<th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id="content_list">
					<?php                      
                       foreach ($userlist as $user) {
                           $status = $user['status'] == '1' ? '<font color="blue">有效</font>' : '<font color="red">无效</font>';
                           $pay_type = $user['pay_type'] == '1' ? '<font color="red">红包</font>' : '<font color="blue">现金</font>';
                           $wxcode = '/' . $user['weixin_code'];                      
                           echo '<tr>
                           <td><a href="javascript:;" orgid="' . $user['id'] . '" title="查看员工业绩" class="employee-yj">' . $user['xingming'] . '</a></td>
                           <td>' . $user['power_name'] . '</td>
                           <td>' . $user['maile'] . '</td>
                           <td>' . $user['mobile'] . '</td>
                           <td>' . $user['address'] . '</td>
                           <td>' . $pay_type . '</td>
                           <td><a href="' . $wxcode . '" target="_blank">查看</a></td>
			               <td>' . $user['name'] . '</td>
                           <td>' . $status . '</td>
                           <td>
			                   <a href="javascript:;" class="emplyee-edit" orgid="' . $user['id'] . '">修改</a>
				               &nbsp;&nbsp;
				               <a href="javascript:;" class="emplyee-delete" orgid="' . $user['id'] . '">禁用</a>
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
		  <!--添加员工页面,内容隐藏-->
		  <div id="dialog-emplyee" title="添加员工" style="display:none;">
				<p class="help-block">以下信息都是必填项<br/>添加员工时密码项若不填写，则为系统默认密码</p>
				<input type="hidden" id="employeeid" value="">
				<div class="form-group">
					<label>姓名</label>
					<input type="text" class="form-control" placeholder="请输入姓名" name="xingming" id="u_xingming">
				</div>
				<div class="form-group">
					<label>权限</label>
					<select class="form-control" name="power_type" id="u_power_type">
					
					</select>
				</div>		
				<div class="form-group">
					<label>邮箱</label>
					<input type="text" class="form-control" placeholder="请输入邮箱" name="maile" id="u_maile" value="">
				</div>
				<div class="form-group">
					<label>手机</label>
					<input type="text" class="form-control" placeholder="请输入手机" name="mobile" id="u_mobile" value="">
				</div>
				<div class="form-group">
					<label>负责区域</label>
					<input type="text" class="form-control" placeholder="请输入区域" name="address" id="u_address" value="">
				</div>
				<div class="form-group">
					<label>支付方式</label>
					<select class="form-control" name="status" id="u_pay">
						<option value="1">红包</option>
						<option value="0">现金</option>
					</select>
				</div>	
				<div class="form-group">
					<label>用户名</label>
					<input type="text" class="form-control" placeholder="请输入用户名" name="name" id="u_name" value="">
				</div>
				<div class="form-group">
					<label>密码</label>
					<input type="password" class="form-control" placeholder="请输入密码" name="password" id="u_password" value="">
				</div>
				<div class="form-group">
					<label>确认密码</label>
					<input type="password" class="form-control" placeholder="请输入确认密码" name="reqpassword" id="u_reqpassword" value="">
				</div>
				<div class="form-group">
					<label>状态</label>
					<select class="form-control" name="status" id="u_status">
						<option value="1">有效</option>
						<option value="0">无效</option>
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
