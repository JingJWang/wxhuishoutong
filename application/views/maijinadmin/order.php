<!DOCTYPE html>
<html>
  <head>
    <title>回收通微信后台管理系统</title>
    <meta charset="utf-8">
    <script src="/maijinadmin/js/order_list.js"></script>
  </head>
  <body>   	
      <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row" style="overflow:auto;">
		  <!--订单列表页面-->
          <div class="col-lg-6" style="width:100%;">
		     <h3>旧衣订单统计</h3>            
            <div class="alert alert-success alert-dismissable">
                <?php  
                   $ordrenum=$ordernum['1']['num']+$ordernum['0']['num'];
                   echo '&nbsp&nbsp订单总数: '.$ordrenum.'单';
                   echo '&nbsp&nbsp&nbsp&nbsp:未成交 &nbsp&nbsp'.$ordernum['1']['num'].'单';
                   echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp已成交: &nbsp&nbsp'.$ordernum['0']['num'].'单';
                   echo '</br>';
                   echo '&nbsp&nbsp每月统计:</br>';
                   if(is_array($ordermonths)){
                       foreach ($ordermonths as $order){
                           echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$order['months'].'月&nbsp&nbsp&nbsp&nbsp提交订单&nbsp&nbsp&nbsp&nbsp'.$order['num'].'单';
                           echo '</br>';
                       }
                   }
                   echo '</br>';
                   echo '&nbsp&nbsp最近一周内:</br>';
                   if(is_array($orderweek)){
                       foreach ($orderweek as $order){
                           echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$order['joindate'].'日&nbsp&nbsp&nbsp&nbsp提交订单&nbsp&nbsp&nbsp&nbsp'.$order['num'].'单';
                           echo '</br>';
                       }
                   }
                   
                   
                ?>
			</div>
            <h2>订单信息列表</h2>
              <div class="table-responsive">
              <input type="text" id="keyword"/>
			  <button type="button" class="btn  btn-primary  btn-order" onclick="seachorder(url=0);">检索</button>
			  <div class="order_option">
			         <input type="checkbox" values="">已成交
			  </div>
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead id="content_title">
                  <tr>
                    <th>用户昵称</th>
                    <th>用户状态</th>
                    <th>订单编号</th>
                    <th>订单类型</th>
					<th>联系方式</th>
                    <th>归属省份</th>
					<th>归属市/区</th>
					<th>归属县/乡</th>
					<th>详细地址</th>
					<th>数量</th>
					<th>成交金额</th>
					<th>重量</th>
					<th>提交时间</th>
					<th>订单状态</th>
                  </tr>
                </thead>
                <tbody id="content_list">
                      <?php                      
                       foreach($orderlist as $order){
                           $wxuserstatus=$order['wx_status'] == '1'? '关注状态' :'未关注';
                           $orderstatus=$order['order_status'] == '1' ?'未成交':'已成交';                        
                           echo '<tr>
                           <td>'.$order['wx_name'].'</td>
                           <td>'.$wxuserstatus.'</td>
                           <td>'.$order['order_randid'].'</td>
                           <td>'.$standard_product[$order['order_type']].'</td>
                           <td>'.$order['order_mobile'].'</td>
                           <td>'.$order['order_province'].'</td>
                           <td>'.$order['order_city'].'</td>
                           <td>'.$order['order_county'].'</td>
                           <td>'.$order['order_address'].'</td>
                           <td>'.$clothesnum[$order['order_num']].'</td>
                           <td>'.$order['order_pic'].'</td>
                           <td>'.$order['order_weight'].'</td>
	                       <td>'.$order['order_joindate'].'</td>
                           <td>'.$orderstatus.'</td>
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
		  <!--订单的代金券信息-->
		  <div id="dialog-order-coupon" title="订单的代金券信息" style="display:none;">
			  <div class="col-lg-6"> 
					<table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
						<thead>
						  <tr>
							<th>类型</th>
							<th>金额</th>
						  </tr>
						</thead>
						<tbody id="order_coupon_list">
						 
						</tbody>
					  </table>
			  </div>
		  </div> 
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
  </body>
</html>
