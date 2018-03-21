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
	<script type="text/javascript" src="../../../maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../../maijinadmin/js/jquery-ui.js"></script>
	<script type="text/javascript"  src="../../../maijinadmin/js/time-cn.js"></script>
  </head>
  <body>
      <div id="page-wrapper">
        <div class="row">
		 <!--营业网点管理-->		
          <div class="col-lg-6" style="width:100%;">
            <h3>非标准化产品管理</h3>
            <div class="option">
                    <ul>
                        <li>状态:</li>
                        <li><a href="javascript:void(0);">全部</a></li>
                        <li><a href="javascript:void(0);">正常</a></li>
                        <li><a href="javascript:void(0);">待审核</a></li>
                        <li><a href="javascript:void(0);">停用</a></li>
                    </ul>
            </div>
			<div class="table-responsive">			
			  <!-- <button type="button" class="btn btn-primary btn-addbranch">添加营业网点</button> -->			  
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead>
                  <tr>					
                    <th>姓名</th>
                    <th>手机号码</th>
					<th>加入时间</th>
                    <th>状态</th>
					<th>操作</th>
                  </tr>
                </thead>
                <tbody id='branch_list'>
				<?php foreach ($coop as $val){ ?>	
					<tr>
					<td><?php echo $val['cooperator_name']; ?></td>
                    <td><?php echo $val['cooperator_mobile']; ?></td>
					<td><?php echo $val['time']; ?></td>
                    <td><?php echo $status[$val['cooperator_userstatus']]; ?></td>
					<th>
					   <button data-toggle="modal" onclick="ViewCoop('<?php echo $val['cooperator_mobile'];?>');">查看</button>
					   <?php if($val['cooperator_userstatus'] == 0){ ?>
					   <button onclick="UserCheck('<?php echo $val['cooperator_mobile']; ?>');">审核</button>
					   <?php } ?>					   
					</th>
					</tr>
               <?php } ?>
                </tbody>
               
              </table>
               <p><?php echo $page; ?></p>
            </div>			
		  </div>
     <!-- 模态框（Modal） -->
        <div class="modal fade " id="myModal" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
                   <div class="modal-dialog">
                      <div class="modal-content">
                         <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" 
                               aria-hidden="true">
                            </button>
                            <h4 class="modal-title" id="myModalLabel">回收商详情</h4>
                         </div>
                         <div class="modal-body" >
                               
                                
                         </div>
                         <div class="modal-footer">
                            <button type="button" class="btn btn-default"  data-dismiss="modal">关闭</button>
                         </div>
                     </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
<script src="../../../maijinadmin/js/bootstrap.js"></script>
<script src="../../../maijinadmin/js/common.js"></script>
<script type="text/javascript">


</script>
 <style>
 .modal-body p{
	border-bottom:1px dashed #DCDCDC;
 }
  .modal-body span{
	margin-left:3%;
  	
  }
  .imglist img{
	width:500px;  	
  }
  .option ul{
	list-style-type:none;
  }
  .option ul li{
	float:left;margin-left:30px;
  }
 </style>
  </body>
</html>
