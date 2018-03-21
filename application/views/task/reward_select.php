<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>选择奖励</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
     <script type="text/javascript" src="/static/task/js/jquery-1.9.1.min.js"></script>
     <script type="text/javascript">
     $(function(){
     	var can_get_reward = <?php echo $getonetask['reward_num']; ?>;
      var reward_num = <?php echo count($getonetask['rewards']); ?>;
     	
     	$('.submit').click(function() {
            // if (confirm("确定选择此项")) {
    		    var len = $('.checksreward:checkbox:checked').length;
    		    if (len == can_get_reward) {
    		    	document.theform.submit();
    		    }else{
    		    	alert('请选择'+can_get_reward+'个奖励');
    		    };
            
        });
     })

     </script>
<!--gggggggggggggggggggggggggggg  111111111-->
    <style>
        .choose{width:106px; height:auto;display: inline-block; background: url("/static/task/images/nochooseBg2.png") no-repeat 42px 100px; background-size:20px 20px;  padding-bottom:50px;}
        /*.btnY{margin-left:3rem;}*/
        /*.btnN{ margin-left:1rem;}*/
        label.active{background: url("/static/task/images/chooseBg2.png") no-repeat 42px  100px; background-size:20px 20px; padding-bottom:50px;}
        .fhide{display:none;}
    </style>
<!--gggggggggggggggggggggggggggg 11111-->
 </head>

<body class="bg_color">
	 <!--head start-->
    <div class="main head">
    	 <div class="head_nav pos_re">
         		 <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
         	     <a class="headdis_cen">选择奖励</a>
         		 
         </div>
    </div>

    <div style="font-size:18px;">
          <div class="fa_nav" style="text-align:center;">
          <form action="<?php echo site_url('task/task_detail/obainaward').'/'.$getonetask['task_id']; ?>" method="post" name="theform">
      <?php if (empty($getonetask['rewards'])) { ?>
        <div>奖金可能已经被删除</div>
      <?php }else{ ?>
			<?php foreach ($getonetask['rewards'] as $k => $v) {
				$all_reward=''; 
				$jin = $shu = 0;
				if ($v['reward_bonus']>0) { $all_reward.=$v['reward_bonus'].'元奖金<br>'; $jin = 1;}
				if ($v['reward_integral']>0) { $all_reward.=$v['reward_integral'].'个通花<br>'; $shu = 1; }
        if ($v['reward_all_integral']>0) { $all_reward.=$v['reward_all_integral'].'积分<br>'; $shu = 1; }
				if ($v['reward_fund']>0) { $all_reward.=$v['reward_fund'].'元基金<br>'; } 
				$all_reward=rtrim($all_reward,' ');
				?>
				<a>
          <label class="btnY choose " for="f2">
					<?php if ($jin == 1 && $shu ==0) { ?>
						<div class="fa_img"><img src="/static/task/images/hb.png" alt=""></div>
					<?php }elseif($jin == 0 && $shu == 1){ ?>
						<div class="fa_img"><img src="/static/task/images/sm.png" alt=""></div>
					<?php }else{ ?>
						<div class="fa_img"><img src="/static/task/images/hm.png" alt=""></div>
					<?php } ?>
               	   	
                   	<p class="fa_txt"><?php echo $all_reward; ?></p>
               		<input class="checksreward fhide" type="checkbox" name="<?php echo $v['reward_id']; ?>" style="width:25px;height:25px;border:1px solid red;background:#000;margin:auto;" />
               
               	</a>
                </label>
			<?php } ?>
	    <?php } ?>
          </form>
          </div>
          <div class="both">
        	  <a href="#" class="all_go bg_c submit"  id="one1">确定选择</a>
          </div>
    	
    </div>   
 
     <!--content end-->

     <script>
         var oChoose = $(".choose");
         oChoose.click(function() {
             $(this).parents(".fa_nav").find(".choose").removeClass("active");
             $(".fa_nav").find(".checksreward").prop('checked',false);
             $(this).toggleClass("active");
             $(this).find('.checksreward').prop('checked',true);
         });
     </script>

</body>
</html>
