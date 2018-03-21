<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="autdor" content="">
    <!-- Bootstrap core CSS -->
    <link href="/maijinadmin/css/bootstrap.css" rel="stylesheet">
    <!-- Add custom CSS here -->
    <link href="/maijinadmin/css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="/maijinadmin/font-awesome/css/font-awesome.min.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="/maijinadmin/css/morris-0.4.3.min.css">	
	<link rel="stylesheet" href="/maijinadmin/css/jquery-ui.css">
	<script src="/maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script src="/maijinadmin/js/jquery-ui.js"></script>
	<script src="/maijinadmin/js/time-cn.js"></script>
	<script src="/maijinadmin/js/group.js"></script>
	<script src="/maijinadmin/js/common.js"></script>
	<script src="/maijinadmin/js/bootstrap.js"></script>
  </head>
  <body>    
      <div id="page-wrapper">
        <div class="row">
		 <!--营业网点管理-->		
          <div class="col-lg-6" style="widtd:100%;">
            <h2>地堆管理</h2>
			<div class="table-responsive">
			<?php if(in_array('permit_view_addgroup', $viewpermit)){ ?>			
			  <button type="button" class="btn btn-primary addinstruction"  onclick="viewaddgroup();">添加小组</button>
			 <?php } ?>
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <tdead>
                  <tr>					
                    <td>小组名称</td>
					<td>业绩明细</td>
                    <td>成员配置</td>
                    <td>创建时间</td>
                    <td>状态</td>
					<td>操作</td>
                  </tr>
                </tdead>
                <tbody id='branch_list'>					
					<?php 
					if(is_array($grouplist)){
    					foreach ($grouplist as $group){?>
    					<tr>
        					<td><?php echo $group['group_name'];?></td>
        					<td><?php echo '<a href="javascript:void(0);" onclick="performance('.$group['group_id'].');">查看</a>'?></td>
                            <td><?php if(in_array('permit_list_configure', $listpermit)){
                                           echo '<a href="javascript:void(0);" onclick="editteam('.$group['group_id'].');">配置</a>'; 
                                 }?></td>
                            <td><?php echo $group['group_jointime'];?></td>
                            <td><?php echo $data_status[$group['group_status']];?></td>
        					<td><?php 
        					           if(in_array('permit_list_editgroup', $listpermit)){
        					               echo '<a href="javascript:void(0);" onclick="editgroup('.$group['group_id'].');">编辑</a>';
                                       }
                                       if(in_array('permit_list_delgroup',  $listpermit)){
                                           echo '<a href="javascript:void(0);" onclick="delgroup('.$group['group_id'].');">删除</a></td>';
                                       }
                				 } 
            					}else{
            					    echo $this->lang->item('notgrouplist');
            					}?>
					   </tr>
                </tbody>
              </table>
              <div id="page-ajax-list">
                   
              </div>              
            </div>			
		  </div>
        </div>
      </div> 
      <!-- 添加小组 -->
      <div id="dialog-addgroup" title="添加小组" style="display:none;">
            <form method="post" action="#" id="groupdata">
                <div class="form-group">
                    <label>小组名称</label>
                    <input type="text" class="form-control datepicker" placeholder="请输入名称" name="group_name" id="group_name"/>
                </div>
                <div class="form-group">
                    <label>选择组长</label>
                    <select  class="form-control datepicker"  name="group_leader" id="group_leader"/>
                            <option value="0">请选择组长</option>
                    </select>
                </div> 
                <div class="form-group">
                    <label>选择主管</label>
                    <select  class="form-control datepicker"  name="group_executives" id="group_executives"/>
                            <option value="0">请选择主管</option>
                    </select>
                </div> 
                <div class="form-group">
                    <label>选择总监</label>
                    <select class="form-control datepicker"  name="group_majordomo" id="group_majordomo"/>
                            <option value="0">请选择总监</option>
                    </select>
                </div>  
                <div id="error-info" style="font-size:15px;color:red;"></div>              
                <button  type="button" class="btn btn-primary addinstruction" onclick="group_groupadd();" id="button-add-group">保存</button>
           </form>        
      </div>
      <!-- 配置组员 -->
      <div id="dialog-editteam" title="配置组员" style="display:none;">
            <div>
                <input type="hidden" id="teamgroupid"/>
                <ul id="userlistcontent" class="group-userlistcontent">
                    
                </ul>
            </div>
            <div id="error-info-editteam" style="font-size:15px;color:red;width:100%;height:50px;
					text-align:center;float: left;"></div> 
            <div style="width:100%;height:50px;text-align:center;float: left;">
            <button  type="button" class="btn btn-primary addinstruction" onclick="group_edit_groupteam();" id="button-edit-groupteam">修改</button>
            <button  type="button" class="btn btn-primary addinstruction" onclick="group_clear_groupteam();" id="button-clear-groupteam">移除</button>
            </div>
      </div>
      <!-- 修改小组 -->
      <div id="dialog-group-editsave" title="修改小组" style="display:none;">            
            <form method="post" action="#" id="editgroupdata">
                <input type="hidden" id="save_edit_groupid" name="group_edit_id"/>
                <div class="form-group">
                    <label>小组名称</label>
                    <input type="text" class="form-control datepicker" name="group_name" id="group_edit_name"/>
                </div>
                <div class="form-group">
                    <label>选择组长</label>
                    <select  class="form-control datepicker"  name="group_leader" id="group_edit_leader"/>
                            <option value="0">请选择组长</option>
                    </select>
                </div> 
                <div class="form-group">
                    <label>选择主管</label>
                    <select  class="form-control datepicker"  name="group_executives" id="group_edit_executives"/>
                            <option value="0">请选择主管</option>
                    </select>
                </div> 
                <div class="form-group">
                    <label>选择总监</label>
                    <select class="form-control datepicker"  name="group_majordomo" id="group_edit_majordomo"/>
                            <option value="0">请选择总监</option>
                    </select>
                </div>  
                <div id="editgroup-error-info" style="font-size:15px;color:red;"></div>              
                <button  type="button" class="btn btn-primary addinstruction" onclick="save_edit_groupinfo();" id="button-edit-group">保存修改</button>
           </form> 
      </div>
      <!-- 业绩明细 -->
      <div id="dialog-group-performance" title="业绩明细" style="display:none;">
             <div>
                 <p id="manageinfo" style="font-size: 15px;   color:blue;"></p>
                 <ul id="performance" class="group-performance">
                    
                </ul>
            </div>
            <div id="error-info-performance" style="font-size:15px;color:red;width:100%;height:50px;
			text-align:center;float: left;"></div> 
      </div>
  </body>
</html>
