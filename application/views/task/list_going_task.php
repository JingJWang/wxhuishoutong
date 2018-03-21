<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
     <title>我的福利</title>
     <link href="/static/task/css/both.css" type="text/css" rel="stylesheet"/>
     <link href="/static/task/css/hou.css" type="text/css" rel="stylesheet"/>
</head>

<body class="bg_color">
	 <!--head start-->
        <div class="main head">
             <div class="head_nav pos_re">
                     <!-- <a href="javascript:history.back()" class="iall_left"></a>  -->
                     <a class="headdis_cen">我的福利</a>
                     
             </div>
            
        
        </div>
    <!--head end-->
    
    
     <!--content start-->
         <div class="both">
              

              <div class="tlist_nav bg_colorw pos_re">
                       <div class="ltwo_left tlone">
                             <p style=" max-width:180px;"><img src="/static/task/images/jingbi.png" style="width:20px;margin-right:10px;margin-bottom:5px;" alt="" />环保基金：<span style=" color:#58ab22;"><?php echo $usertaskinfo['0']['center_fund']; ?></span>元</p>
                             <p><img src="/static/task/images/t4.png" style="width:20px;margin-right:10px;margin-bottom:5px;" alt="" />通      花：<?php echo $usertaskinfo['0']['center_integral']; ?></p>
                             <p><img src="/static/task/images/t2.png" style="width:20px;margin-right:10px;margin-bottom:3px;" alt="" />等      级：<?php echo $usertaskinfo['0']['level_name']; ?></p>
                             
                                
                        </div>
                        
                     
                        <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="ltwo_right"><img src="/static/task/images/listrbg.png" height="100%" alt="" /></a>
                        
                        <div class="show_two">
                            <div class="show_twonav">点击进入环保中心</div>
                            <s><i></i></s>
                        </div>
                        <!--/*<<<2015.12.07修改的地方>>>*/-->
                        <a class="RenWuXiTong" href="http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=401817569&idx=1&sn=2efaf2d1353a2e318f3ba4fa4be4edee#rd">福利系统介绍</a>
                        <!--/*<<<修改结束>>>*/-->
                </div>

               
               
               <div class=" both  mt20">
                      
                       <div id="con1">
                          <?php if (empty($mytask)) { ?>
                              <p class="tlbot1"><?php echo $this->lang->line('no_get_finish');?></p>
                          <?php }else{ ?>
                              <?php foreach ($mytask as $k => $v) { ?>
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
                                            <a class="TishiLianJie" href="javascript:;">点击领福利</a>     
                                         </div>
                                        </a>
                                  </div>
                                  <!--循环此处 end-->
                              <?php } ?>
                          <?php } ?>
                       </div>
                      
               </div>
               <!-- <a href="#" class="all_go bg_c">任务系统介绍</a> -->
               <a href="<?php echo site_url('task/usercenter/taskcenter'); ?>" class="all_go bg_c">领取福利</a>
               
         </div>
     <!--content end-->
     
<script type="text/javascript" src="/static/task/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/static/task/js/tab.js"></script>

</body>
</html>
