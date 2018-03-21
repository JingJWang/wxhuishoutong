<!DOCTYPE html>
<html>
  <head>
  <title></title>
    <meta charset="utf-8">
	<script src="/maijinadmin/js/coupon-log.js"></script>
  </head>
  <body>   	
      <div id="page-wrapper" style="overflow-x:hidden;">
      <div class="row" style="overflow:auto;">
		  <!--现金卷领取记录-->
          <div class="col-lg-6" style="width:100%;">
		     <h3>现金卷领取记录</h3>            
            <div class="alert alert-success alert-dismissable">
                <?php 
                   $vouchertype=$this->config->item('vouchertype');
                   echo '概览</br>'; 
                   $num=0;
                   $pic=0;
                      
                  
                   echo '   总计发送',$num,'张  总计金额',$pic,'RMB','</br>';
                   echo '-----------------------------------------------------','</br>';
                   echo '最近一周统计</br>';
                   foreach ($weekday as $day){
                       echo $day['joindate'],'日   总计发送',$day['num'],'张  总计金额',$day['sum'],'RMB','</br>';
                       echo '-----------------------------------------------------','</br>';
                   }
                ?>
			</div>
              <div class="table-responsive">
              <input type="text" id="keyword"/>
			  <button type="button" class="btn  btn-primary  btn-order" onclick="seachvoucher(url=0);">检索</button>
              <table class="table table-bordered table-hover tablesorter" style="margin-top:10px;">
                <thead id="content_title">
                  <tr>
                    <td>类型</td>
                    <td>金额</td>
                    <td>领取时间</td>
					<td>使用时间</td>
                    <td>过期时间</td>
					<td>状态</td>
                  </tr>
                </thead>
                <tbody id="content_list">
                      <?php
                      $voucherstatus=$this->config->item('voucherstatus');
                      foreach ($voucherlist as $voucher){
                          echo '<tr><td>'.$vouchertype[$voucher['log_type']].'</td>
                            <td>'.$voucher['voucher_pic'].'</td>
                            <td>'.$voucher['log_joindate'].'</td>
                            <td>'.$voucher['log_lastdate'].'</td>
                            <td>'.$voucher['log_exceed'].'</td>
                            <td>'.$voucherstatus[$voucher['log_voucher_status']].'</td></tr>';
                      }                 
                       ?>  
                </tbody>
              </table>
            </div>
          </div>
		  <div class="bs-example" style="text-align:center;" id="content_page">
                <?php echo $page ;?>
          </div>
          <div class="bs-example" style="text-align:center;" id="content_ajax_page">
                
          </div>
        </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
  </body>
</html>
