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
	<script type="text/javascript" src="/maijinadmin/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/maijinadmin/js/jquery-ui.js"></script>
	<script type="text/javascript"  src="/maijinadmin/js/time-cn.js"></script>
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
<script src="/maijinadmin/js/bootstrap.js"></script>
<script src="/maijinadmin/js/common.js"></script>
 <script>

 
 /* $(document).ready(function(){
	 var  coopinfo=$('#myModal');
	 //coopinfo.modal('hide');
	  coopinfo.on('hide.bs.modal', function () {
	      alert('嘿，我听说您喜欢模态框...');
	 }); 
	 coopinfo.on('shown.bs.modal',coopInfo);
	    
	 
 }); */
 function ViewCoop(mobile){
	   var u='/index.php/maijinadmin/cooperator/GetCoopInfo';
	   var d='mobile='+mobile;
	   var f=function(res){
		   var response=eval(res);
		   var content='';
		   if(response['status'] == request_succ){
			   var opened='';
			   var img = '';
			   $.each(response['data']['opened'],function(n,val){
				   opened = opened + ',' +val['name'];
			   });
			   $.each(response['data']['auth'],function(n,val){
				   img = img +'<div class="imglist"> <img  src='+val.replace(".","")+'></div>';
			   });
			   var auto_option='<select id="auto">';
			   $.each(response['data']['aptitudes'],function(n,val){
					   auto_option = auto_option +'<option data-key='+n+'>'+val+'</option>';
			   });
			   auto_optiop = auto_option +'</select>'+
			   '<button onclick="CoopAuto('+response['data']['mobile']+');">确定</button>';
			  
			   content="<p>商户姓名:<span>"+response['data']['name']+"</span></p>"+
                   "<p>联系电话:<span>"+response['data']['mobile']+"(绑定)</span></p>"+
                   "<p>商户性别:<span>"+response['data']['sex']+"</span></p>"+
                   "<p>审核状态:<span>"+response['data']['status']+"</span></p>"+
                   "<p>是否营业:<span>"+response['data']['switch']+"</span></p>"+
                   "<p>开通服务:<span>"+opened+"</span></p>"+
                   "<p>从业年限:<span>"+response['data']['year']+"年</span></p>"+
                   "<p>是否有店:<span>"+response['data']['work_place']+"</span></p>"+
                   "<p>店铺地址:<span>"+response['data']['shopaddress']+"</span></p>"+
                   "<p>车辆类型:<span>"+response['data']['cars']+"</span></p>"+
                   "<p>服务范围:<span>"+response['data']['distance']+"千米</span></p>"+
                   "<p>保证金额:<span>"+response['data']['moery']+"</span></p>"+
                   "<p>加入时间:<span>"+response['data']['time']+"</span></p>"+img+
                   "<p>认证资质:<span>当前状态:<font style='color:red;'>"+
                   response['data']['aptitudes'][response['data']['autotype']]+
                   "</font></span><span>修改为"+auto_optiop+"</span></p>";
			   $(".modal-body").html(content);
			   $("#myModal").modal('show');
		   }
	   }
	   AjaxRequest(u,d,f);
 }
 /**
 *  修改当前用户认证状态
 */
 function CoopAuto(m){
	 var  u='/index.php/maijinadmin/cooperator/EditCoopAuto';
	 var  d='mobile='+m+'&auto='+$('#auto option:selected').text();
	 var  f=function(){
		 var response=eval(data);
	    	if(response['status'] == request_succ){
	    	   
		    }
		    if(response['status'] == request_fall){
		    	alert(response['msg']);
			}    
	    	location.reload();
     }
     AjaxRequest(u,d,f);
 }
 /**
 * 审核回收商
 */
 function UserCheck(mobile){
	    var u='/index.php/appsunny/back/auth/';
	    var d='phone='+mobile;
	    var f=function(data){
	        var response=eval(data);
	    	if(response['status'] == request_succ){
	    	    alert('审核成功');
		    }
		    if(response['status'] == request_fall){
		    	alert(response['msg']);
			}    
	    	location.reload();
	    }
	    AjaxRequest(u,d,f);
}
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
