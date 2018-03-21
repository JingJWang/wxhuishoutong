<?php
/*
 * 功能描述:用户控制层
 */
include 'weixindo/model/Mysql.class.php';
include 'weixindo/model/WxcodeModel.class.php';
include 'weixindo/model/WxUserModel.class.php';
include 'weixindo/model/AdminModel.class.php';
include 'weixindo/model/OrderModel.class.php';
class UserControl{
    /*
     * 功能描述:检测是否是否存在用户
     */
    public function check_user($openid,$scene){       
        $wx=new WxCodeModel();
        $userinfo=$wx->userinfo($openid);
        if(array_key_exists('errcode', $userinfo) && $userinfo['errcode'] == 40001){
            $wx->update_token();
            $userinfo=$wx->userinfo($openid);
        }
        $wxuser=new WxUserModel();        
        $userinfo['suser_id']=str_replace('qrscene_',' ',$scene);        
        $message=$wxuser->check_user($userinfo);
        $voucherAll=$wxuser->get_AllVoucher();        
        $url=urlencode('http://wx.recytl.com/');
        $content[] = array("Title"=>"【回收通】欢乐“送”话费、签到再送1元，赶紧来参与","Description"=>'',"PicUrl"=>"http://wx.recytl.com/public/img/guanzhu1.jpg", "Url" =>"http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=503255275&idx=1&sn=dabb7191b25424837e3cf0ced36d3e71#rd");
		$content[] = array("Title"=>"来回收通高价卖手机，小通不仅懂手机，更懂你","Description"=>'',"PicUrl"=>"http://wx.recytl.com/public/img/guanzhu2.jpg", "Url" =>"http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=503255275&idx=2&sn=c44460cf0da3f63e052cfa02a2c42002#rd");
		$content[] = array("Title"=>"奢侈品寄卖、回收项目开通啦，有闲置的小伙伴火速来围观","Description"=>'',"PicUrl"=>"http://wx.recytl.com/public/img/guanzhu3.jpg", "Url" =>"http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=503255275&idx=3&sn=e8d179f5af90eb2a4d10a4979edbe9da#rd");
		$content[] = array("Title"=>"仅限3天任性送：100通花签到就能拿，通花商城好货来袭【回收通】","Description"=>'',"PicUrl"=>"http://wx.recytl.com/public/img/guanzhu4.jpg", "Url" =>"http://mp.weixin.qq.com/s?__biz=MzA3NTA0NTg3OA==&mid=503255275&idx=4&sn=73a2daf763986767e0ec40e636da67da#rd");
        if($message=="0"){
        //    $content[] = array("Title"=>"关注回收通，获得了".$voucherAll[0]['voucher_pic']."元现金券~", "Description"=>"", "PicUrl"=>"http://wx.recytl.com/weixin/public/img/shouciguanzhu.jpg", "Url" =>"");
        }
        //$content[] = array("Title"=>"马上告知小伙伴，再得现金券~最多10元~", "Description"=>"", "PicUrl"=>"http://wx.recytl.com/weixin/public/img/weeek.jpg", "Url" =>"http://wx.recytl.com/urlrediect.php?url=fenxiang");
        //$content[] = array("Title"=>"现在报单可获得".$voucherAll[1]['voucher_pic']."元现金券奖励，数量有限，赶快报单抢券~", "Description"=>"", "PicUrl"=>"http://wx.recytl.com/weixin/public/img/order.jpg", "Url" =>"http://wx.recytl.com/urlrediect.php?url=create_order");
        //$content[] = array("Title"=>"点击“我要查询-我的现金券”看看我有多少现金券~","Description"=>"", "PicUrl"=>"http://wx.recytl.com/weixin/public/img/myvoucher.jpg", "Url" =>"http://wx.recytl.com/urlrediect.php?url=xianjinquan");
        return $content; 
    }
    /*
     * 功能描述:取消关注事件
     */
    public function user_cancel($openid){
        if(empty($openid)){
            return FALSE;
        }else{            
            $wxuser=new WxUserModel();
            $bool_res=$wxuser->update_suser($openid);
            $userinfo=array('suser_id'=>'','headimgurl'=>'','openid'=>$openid,'nickname'=>'','subscribe_type'=>'-1');
            $log=$wxuser->add_subscribe_log($userinfo);
            if($bool_res){
                return '感谢您的关注!';
            }else{
                return '感谢您的关注';
            }
        }
    }
    /*
     * 功能描述:扫描场景二维码事件
     */
    public function user_scan($openid,$scene){
        if(empty($scene) || empty($openid)){
            return   FALSE;
        }else{           
            $adminobj=new AdminModel();
            $usertype=$adminobj->check_user_type($scene);
             switch ($usertype['power_type']){
				//扫码推广
                case '1':
                    return '感谢你的关注!';
                    break;
				//扫描回收商
				case '2':					
                    $orderobj=new OrderModel();
                    $res=$orderobj->update_order_user($openid,$scene);
                    if($res){
                        return '- 您的订单已成功提交，请提交衣物                                             - 成交后别忘了给小伙伴发红包哦~';
                    
                    }else{
                        return '暂无订单';
                    }
					break;
				//默认选择
                default:
                    return '回收通 价值驱动环保 高价回收二手数码，还有好玩的福利系统等您参与哦~自动回复';
                    break;
            }
        }
        
    }
    /*
     * 功能描述:检测回收员编号是否存在
     */
    function  CheckAdminNumber($username){
        $wxuser=new AdminModel();
        $result=$wxuser->CheckAdmin($username);
        if($result !== false){
            return $result['url'];
        }else{
            return '欢迎关注回收通!';
        }
    }
    /**
     * 添加当前微信用户的经纬度坐标
     */
    function  SaveLocation($data){
        $wxuser=new WxUserModel();
        $result=$wxuser->SaveLocation($data);
        return  '';
    }
}
