<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>回收通-公益环保福利站</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
</head>

<body class="bg_color">
    <?php if (isset($is_attention)&&$is_attention==1) { ?>
    <div class="guangzhu">
      <img src="/static/task/images/guangzhu.jpg" alt="" />
      <span>关闭</span>
    </div>    
    <?php } ?>
	 <!--head start-->
        <div class="main head">
             <div class="head_nav pos_re">
                     <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
                     <a class="headdis_cen">公益环保福利站</a>
                     
             </div>
             <a href="<?php echo site_url('nonstandard/system/welcome'); ?>"><img src="/static/task/images/hstfx2.png" style="position:absolute;right:14px;top:19px;width:40px;" alt="" /></a>
        
        </div>
    <!--head end-->
    
     <!--content start-->
         <div class="both">
              

              <div style="background:#fff;position:relative;padding-top:10px;font-size:14px;"><span style="margin-left:12px;">参与人数：<span style="color:#58ab22;"><?php echo $all_user; ?></span></span><a href="<?php echo site_url('task/usercenter/user_rank'); ?>"><span style="float:right;margin-right:16px;color:#58ab22;">查看我的排名</span></a></div>
              <div class="tlist_nav bg_colorw pos_re" style="padding-top:10px;">
                       <div class="ltwo_left tlone">
                             <p style=" max-width:180px;"><img src="/static/task/images/jingbi.png" style="width:20px;margin-right:10px;margin-bottom:5px;" alt="" />环保基金：<span style=" color:#58ab22;"><?php echo $usertaskinfo['0']['center_fund']; ?></span>元</p>
                             <p><img src="/static/task/images/t4.png" style="width:20px;margin-right:10px;margin-bottom:5px;" alt="" />通      花：<a href="/view/shop/list.html" style="color:#58ab22"><?php echo $usertaskinfo['0']['center_integral']; ?><span style="font-size:12px;color:#FFAA0A;">→去兑换</span></a></p>
                             <p><img src="/static/task/images/t2.png" style="width:20px;margin-right:10px;margin-bottom:3px;" alt="" />等      级：<a href="<?php echo site_url('task/usercenter/my_medal'); ?>" style="color:#58ab22"><?php echo $usertaskinfo['0']['level_name']; ?></a></p>
                             
                                
                        </div>
                        
                     
                        <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="ltwo_right"><img src="/static/task/images/listrbg.png" height="100%" alt="" /></a>
                        
                        <div class="show_two">
                            <div class="show_twonav">点击进入环保中心</div>
                            <s><i></i></s>
                        </div>
                        <!--/*<<<2015.12.07修改的地方>>>*/-->
                        <!--/*<<<修改结束>>>*/-->
                </div>

               
               
               <div class=" both  mt20"style="margin-top:8px;">
               		<!--类别切换 start-->
                  <div style="font-size:12px;margin-left:10px;color:#58AB22;">提示：亲们，做任务领红包，玩游戏额外再得环保基金~<br><span style="color:#FFAA0A;">后续会有神秘任务持续上线，请保持关注~</span></div>
                       <div class="tlist_two bg_colorw bor_bot1" style="margin-top:8px;">
                                <ul>
                                
                                    <li id="tab1">
                                        <a class="tl_on"  onclick="switchTab('tab1','con1');this.blur();return false;">
                                           未完成
                                        </a>
                                        
                                    </li>
                                    <li id="tab2">
                                        <a class="tl_no"  onclick="switchTab('tab2','con2');this.blur();return false;">
                                            已完成
                                        </a>
                                    </li>
                                    
                                    
                                </ul>
                        </div>
                    <!--类别切换 end-->
                       
                       
                       <!--未完成 start-->
                       <div id="con1">
                          <?php if (empty($nofinishlist)){ ?>
                            <p class="tlbot1"><?php echo $this->lang->line('all_task_finish');?></p>
                          <?php }else{ ?>
                            <?php if (isset($nofinishlist['active'])) { ?>
                              <?php foreach ($nofinishlist['active'] as $k => $v) { ?>
                                  <!--循环此处 start-->
                                  
                                  <div class="con1  ptb10 bg_colorw mb15">
                                        <img src="/static/task/images/task_types<?php echo $v['task_type']; ?>.png" class="tlcon_left" alt="" />
                                        <a href="<?php echo site_url("task/taskspecific/taskdetail/".$v["task_id"]); ?>">
                                        <div class="tlcon_right">
                                            <div class="tlcon_top">
                                                <span class="tlt2"><?php echo $v['info_name']; ?></span>
                                                <!-- <span class="tlt2">200树苗</span> -->
                                                <?php if ($v['task_type']!=7&&$v['task_type']!=8) { ?>
                                                  <?php 
                                                    $fiele_name=$task_types_data[$v['task_type']];
                                                    if ($fiele_name=='jinghua') {
                                                      foreach ($task_types_data as $ke => $va) {
                                                        if ($va!='jinghua' && $v[$va] != 0) { ?>
                                                          <span class="tlc2"><?php echo $usertasktype[$va].'/'.$v[$va]; ?></span>
                                                        <?php }
                                                      }
                                                    }else{
                                                      $uer_have_num=$usertasktype[$fiele_name];
                                                      $num=$v[$fiele_name]; ?>
                                                      <span class="tlc2"><?php echo $uer_have_num.'/'.$num; ?></span>
                                                    <?php }
                                                  ?>
                                                <?php } ?>
                                                <!-- <span class="tlc2">666/6666</span> -->
                                                            
                                            </div>
                                                <?php if (isset($v['remaintime'])) { ?>
                                                  <span class="tlt3">距离结束还剩<?php echo $v['remaintime']; ?></span>
                                                <?php } ?>
                                                <!-- <span class="tlt3">距离结束还剩12小时</span> -->
                                                <div class="tlcon_bot">
                                                    <div class="tlbot1">奖励:<?php echo $v['reward_content']; ?></div>
                                                </div>
                                                <div class="newlist_nav"><img src="/static/task/images/<?php echo $task_process[$v['task_process']]; ?>"  alt="" /></div><!--替换状态图片--> 
                                            <a class="TishiLianJie" href="javascript:;">点击领福利</a>     
                                         </div>
                                        </a>
                                  </div>
                                    <!--循环此处 end-->

                              <?php } ?>
                            <?php } ?>

                            <?php if (isset($nofinishlist['jinghua'])) { ?>
                              <?php foreach ($nofinishlist['jinghua'] as $k => $v) { ?>
                                  <!--循环此处 start-->
                                  
                                  <div class="con1  ptb10 bg_colorw mb15">
                                        <img src="/static/task/images/task_types<?php echo $v['task_type']; ?>.png" class="tlcon_left" alt="" />
                                        <a href="<?php echo site_url("task/taskspecific/taskdetail/".$v["task_id"]); ?>">
                                        <div class="tlcon_right">
                                            <div class="tlcon_top">
                                                <span class="tlt2"><?php echo $v['info_name']; ?></span>
                                                <!-- <span class="tlt2">200树苗</span> -->
                                                <?php if ($v['task_type']!=7&&$v['task_type']!=8) { ?>
                                                  <?php 
                                                    $fiele_name=$task_types_data[$v['task_type']];
                                                    if ($fiele_name=='jinghua') {
                                                      foreach ($task_types_data as $ke => $va) {
                                                        if ($va!='jinghua' && $v[$va] != 0) { ?>
                                                          <span class="tlc2"><?php echo $usertasktype[$va].'/'.$v[$va]; ?></span>
                                                        <?php }
                                                      }
                                                    }else{
                                                      $uer_have_num=$usertasktype[$fiele_name];
                                                      $num=$v[$fiele_name]; ?>
                                                      <span class="tlc2"><?php echo $uer_have_num.'/'.$num; ?></span>
                                                    <?php }
                                                  ?>
                                                <?php } ?>
                                                <!-- <span class="tlc2">666/6666</span> -->
                                                            
                                            </div>
                                                <?php if (isset($v['remaintime'])) { ?>
                                                  <span class="tlt3">距离结束还剩<?php echo $v['remaintime']; ?></span>
                                                <?php } ?>
                                                <!-- <span class="tlt3">距离结束还剩12小时</span> -->
                                                <div class="tlcon_bot">
                                                    <div class="tlbot1">奖励:<?php echo $v['reward_content']; ?></div>
                                                </div>
                                                <div class="newlist_nav"><img src="/static/task/images/<?php echo $task_process[$v['task_process']]; ?>"  alt="" /></div><!--替换状态图片-->  
                                            <span class="TishiLianJie" href="javascript:;">点击领福利</span>      
                                         </div>
                                        </a>
                                  </div>
                                    <!--循环此处 end-->

                              <?php } ?>
                            <?php } ?>

                            <?php if (isset($nofinishlist['main'])) { ?>
                              <?php foreach ($nofinishlist['main'] as $k => $v) { ?>
                                  <!--循环此处 start-->
                                  
                                  <div class="con1  ptb10 bg_colorw mb15">
                                        <?php switch ($v['task_id']) {
                                          case 5:
                                            $main_task_icon = 'game';
                                            break;
                                          case 6:
                                            $main_task_icon = 'guanzhu';
                                           break;
                                          default:
                                            $main_task_icon = 'task_types7';
                                            break;
                                        } ?>
                                        <img src="/static/task/images/<?php echo $main_task_icon; ?>.png" class="tlcon_left" alt="" />
                                        <a href="<?php echo site_url("task/taskspecific/taskdetail/".$v["task_id"]); ?>">
                                        <div class="tlcon_right">
                                            <div class="tlcon_top">
                                                <span class="tlt2"><?php echo $v['info_name']; ?></span>
                                                <!-- <span class="tlt2">200树苗</span> -->
                                                <?php if ($v['task_type']!=7&&$v['task_type']!=8) { ?>
                                                  <?php 
                                                    $fiele_name=$task_types_data[$v['task_type']];
                                                    if ($fiele_name=='jinghua') {
                                                      foreach ($task_types_data as $ke => $va) {
                                                        if ($va!='jinghua' && $v[$va] != 0) { ?>
                                                          <span class="tlc2"><?php echo $usertasktype[$va].'/'.$v[$va]; ?></span>
                                                        <?php }
                                                      }
                                                    }else{
                                                      $uer_have_num=$usertasktype[$fiele_name];
                                                      $num=$v[$fiele_name]; ?>
                                                      <span class="tlc2"><?php echo $uer_have_num.'/'.$num; ?></span>
                                                    <?php }
                                                  ?>
                                                <?php } ?>
                                                <!-- <span class="tlc2">666/6666</span> -->
                                                            
                                            </div>
                                                <?php if (isset($v['remaintime'])) { ?>
                                                  <span class="tlt3">距离结束还剩<?php echo $v['remaintime']; ?></span>
                                                <?php } ?>
                                                <!-- <span class="tlt3">距离结束还剩12小时</span> -->
                                                <div class="tlcon_bot">
                                                    <div class="tlbot1">奖励:<?php echo $v['reward_content']; ?></div>
                                                </div>
                                                <div class="newlist_nav"><img src="/static/task/images/<?php if (isset($v['task_have_finish'])==1) {echo $task_process['3'];}else{ echo $task_process[$v['task_process']];} ?>"  alt="" /></div><!--替换状态图片-->  
                                            <span class="TishiLianJie" href="javascript:;">点击领福利</span>      
                                         </div>
                                        </a>
                                  </div>
                                    <!--循环此处 end-->

                              <?php } ?>
                            <?php } ?>

                            <?php if (isset($nofinishlist['other'])) { ?>
                              <?php foreach ($nofinishlist['other'] as $k => $v) { ?>
                                  <!--循环此处 start-->
                                  <?php if ($v['task_type']==1 && isset($sign_task) && $sign_task==1) {
                                    continue;
                                  } ?>
                                  <div class="con1  ptb10 bg_colorw mb15">
                                        <img src="/static/task/images/task_types<?php echo $v['task_type']; ?>.png" class="tlcon_left" alt="" />
                                        <a href="<?php echo site_url("task/taskspecific/taskdetail/".$v["task_id"]); ?>">
                                        <div class="tlcon_right">
                                            <div class="tlcon_top">
                                                <span class="tlt2"><?php echo $v['info_name']; ?></span>
                                                <!-- <span class="tlt2">200树苗</span> -->
                                                <?php if ($v['task_type']!=7&&$v['task_type']!=8) { ?>
                                                  <?php 
                                                    $fiele_name=$task_types_data[$v['task_type']];
                                                    if ($fiele_name=='jinghua') {
                                                      foreach ($task_types_data as $ke => $va) {
                                                        if ($va!='jinghua' && $v[$va] != 0) { ?>
                                                          <span class="tlc2"><?php echo $usertasktype[$va].'/'.$v[$va]; ?></span>
                                                        <?php }
                                                      }
                                                    }else{
                                                      if (isset($v['type_max'])) {
                                                        $uer_have_num=$usertasktype[$fiele_name]-$v['type_max'];
                                                        $num=$v[$fiele_name]-$v['type_max']; 
                                                      }else{
                                                        $uer_have_num=$usertasktype[$fiele_name];
                                                        $num=$v[$fiele_name];
                                                      } ?>
                                                      <span class="tlc2"><?php if ($uer_have_num>$num) {
                                                        echo $num.'/'.$num;}else{echo $uer_have_num.'/'.$num;} ?></span>
                                                    <?php }
                                                  ?>
                                                <?php } ?>
                                                <!-- <span class="tlc2">666/6666</span> -->
                                                            
                                            </div>
                                            <?php if (isset($v['remaintime'])) { ?>
                                              <span class="tlt3">距离结束还剩<?php echo $v['remaintime']; ?></span>
                                            <?php } ?>
                                            <!-- <span class="tlt3">距离结束还剩12小时</span> -->
                                            <div class="tlcon_bot">
                                                <div class="tlbot1">奖励:<?php echo $v['reward_content']; ?></div>
                                            </div>
                                            <div class="newlist_nav"><img src="/static/task/images/<?php if ($uer_have_num>=$num) {echo $task_process[3];}else{echo $task_process[$v['task_process']];} ?>"  alt="" /></div><!--替换状态图片-->  
                                            <span class="TishiLianJie" href="javascript:;">点击领福利</span>      
                                         </div>
                                        </a>
                                  </div>
                                    <!--循环此处 end-->

                              <?php } ?>
                            <?php } ?>

                          <?php } ?>

                                <!-- 提意见 -->
                                  <!-- <div class="con1  ptb10 bg_colorw mb15">
                                      <img src="/static/task/images/task_types4.png" class="tlcon_left" alt="" />
                                      <a href="">
                                                <div class="tlcon_right">
                                                      <div class="tlcon_top">
                                                            <span class="tlt2">提意见</span>   
                                                      </div>
                                                     
                                                      <div class="tlcon_bot">
                                                            
                                                             <div href="#" class="tlbot1">如果意见越好，奖励丰厚</div>

                                                      </div>
                                                     
                                                </div>
                                      </a>
                                              
                                    </div> -->
                                <!-- 提意见 结束 -->  
                
                       </div>
                      <!--未完成 end-->
                      
                      
                      
                       <!--已完成 start-->
                       <div id="con2" style=" display:none;">
                                  
                          <?php if (empty($finishlist)) { ?>
                              <p class="tlbot1"><?php echo $this->lang->line('no_task_finish');?></p>
                            <?php }else{ ?>
                              <?php foreach ($finishlist as $k => $v) { ?>
                                    
                                   <!--循环此处 start-->
                                    <div class="con1  ptb10 bg_colorw mb15">
                                        <?php switch ($v['task_id']) {
                                          case 5:
                                            echo '<img src="/static/task/images/game.png" class="tlcon_left" alt="" />';
                                            break;
                                          case 6:
                                            echo '<img src="/static/task/images/guanzhu.png" class="tlcon_left" alt="" />';
                                           break;
                                          default:
                                            echo '<img src="/static/task/images/task_types'.$v['task_type'].'.png" class="tlcon_left" alt="" />';
                                            break;
                                        } ?>
                                        <a href="<?php echo site_url("task/taskspecific/taskdetail/".$v["task_id"]); ?>">
                                                <div class="tlcon_right">
                                                      <div class="tlcon_top">
                                                            <span class="tlt2"><?php echo $v['info_name']; ?></span>
                                                            <!-- <span class="tlt2">200树苗</span>       -->
                                                             <!-- <span class="tlc2">1/1</span> -->
                                                             <?php if ($v['task_type']!=7&&$v['task_type']!=8) { ?>
                                                              <?php 
                                                                $fiele_name=$task_types_data[$v['task_type']];
                                                                if ($fiele_name=='jinghua') {
                                                                  foreach ($task_types_data as $ke => $va) {
                                                                    if ($va!='jinghua' && $v[$va] != 0) {
                                                                      ?><span class="tlc2"><?php echo $v[$va].'/'.$v[$va];
                                                                    }
                                                                  } ?></span>
                                                                <?php }else{
                                                                    $num=$v[$fiele_name];
                                                                  if ($v['task_type']==5 && $num<=45) {
                                                                    $num = mb_substr($v['info_name'],2,1,'utf8');
                                                                  }elseif($v['task_type']==5){
                                                                    $num = mb_substr($v['info_name'],2,2,'utf8');
                                                                  }
                                                                  
                                                                  ?><span class="tlc2"><?php echo $num.'/'.$num;
                                                                }   
                                                                ?></span>
                                                                <?php } ?>
                                                      </div>
                                                     
                                                      <div class="tlcon_bot">
                                                            
                                                             <p href="#" class="tlbot1">奖励:<?php echo $v['reward_content']; ?></p>
                                                             
                                                             
                                                      </div>
                                                     
                                                </div>
                                                </a>
                                                 <div class="newlist_nav"><img src="/static/task/images/ywc.png"  alt="" /></div>
                                              
                                    </div>
                                  <!--循环此处 end-->
                              <?php } ?>
                            <?php } ?>
                        
                       </div>
                       <!--已完成  end-->
                       
                       
               </div>
               <!-- <a href="#" class="all_go bg_c">任务系统介绍</a> -->
	 		   <div style="width:100%;position:relative;text-align:center;padding-bottom:10px;"><a href="http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=401817569&idx=1&sn=2efaf2d1353a2e318f3ba4fa4be4edee#rd" style="color:#58ab22;">福利系统介绍</a></div>
               
         </div>
     <!--content end-->
     
<script type="text/javascript" src="/static/task/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/static/task/js/tab.js"></script>
<script type="text/javascript">
  $(function(){
    $('.guangzhu span').click(function() {
      $('.guangzhu').css('display', 'none');
    });
  })
</script>

</body>
</html>
