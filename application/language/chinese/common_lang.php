<?php
$lang['DOSUCCESS']='0';
$lang['DOFAIL']='1';
$lang['INFOSUCCESS']='操作成功!';
$lang['INFOFAIL']='操作失败!';
$lang['ADDSUCCESS']='添加成功!';
$lang['ADDFAIL']='添加失败!';
$lang['UPDATESUCCESS']='修改成功!';
$lang['UPDATEFAIL']='修改失败!';
$lang['DELSUCCESS']='禁用成功!';
$lang['DELFAIL']='禁用失败!';
$lang['PUBLISHSUCCESS']='发布成功!';
$lang['PUBLISHFAIL']='发布失败!';
$lang['OPTIONNONULL']='参数不为空!';
$lang['RESULTNULL']='查询结果为空！';
$lang['PASSWORDDIFF']='两次输入的密码不一致！';
//----------------用户部分-------------
$lang['wxuser_not_exist']            = '用户您走失了!';
$lang['open_wx_page']                = '请在微信客户端打开!';
//----------------common-------------
$lang['common_list_null']            = '没有为您找到合适的结果,试试别的!';
$lang['common_request_optionnull']   = '请求参数不可为空!';
/************非标准化产品***************/
$lang['order_optionnull']            = '必填选项不可为空,请检查!';
$lang['order_optiontypenull']        = '参数不合法,请检查!';
$lang['order_addclothes_succ']       = '订单提交成功,请耐心等待报价!';
$lang['order_addclothes_fall']       = '订单提交遇到问题,我们正在处理!';
$lang['order_system_fall']           = '系统遇到问题,我们正拼命修复,请耐心等待!';
$lang['order_cancel_option']         = '取消订单原因必填!';
$lang['order_cancel_fall']           = '取消订单失败,请联系我们!';
$lang['order_cancel_succ']           = '取消订单成功,正在返回订单列表!';
$lang['remind_mobile_formatfall']    = '手机号码格式不对!';
$lang['remind_mobile_contentfall']   = '发送内容不可为空!';
$lang['send_checkcode_fall']         = '验证码发送失败,系统遇到故障,请联系我们!';
$lang['send_checkcode_succ']         = '验证码发送成功,请注意查询!';
$lang['send_number_fall']            = '输入的验证码不正确!';
$lang['send_msg_number']             = '验证码发送已经超过限制!';



$lang['del_order_succ']              = '订单删除成功!';
$lang['del_order_fall']              = '订单删除失败!';
$lang['checkcode_Invalid']           = '输入的验证码已经过期,请重新获取!';
$lang['update_personaldata_succ']    = '修改个人已经更新';
$lang['update_personaldata_fall']    = '个人中心更新出现问题,请联系我们!';

$lang['edit_order_fall']             =  '订单修改失败!';

$lang['common_user_online']          = '您还没有进入此站!';
$lang['common_user_login']           = '尊敬的用户您还没有登陆!';
$lang['common_orderpay_succ']        = '您的订单已经支付成功!';

$lang['common_upload_imgsize']       = '上传图片大小超过限制,请保持单张照片2M一下!';
$lang['common_upload_type']          = '上传图片类型不正确!';

$lang['selected_order_quote']        = '选定报价出现异常.请稍后再试!';
$lang['quote_list_isnull']           = '我们正在把您的订单推送给回收商，</br>报价期限为24小时，
        当有报价时我们</br>会短信和微信通知您，您可以先去</br>忙点别的，小通祝您卖一个好价格哦！';
$lang['orderstatus_fall_cancal']     = '您当前不能完成改操作!';
$lang['ordertrading_fall']           = '当前订单还没有成交!';

/****************** APP api **********/
$lang['app_success']                 = '成功';
$lang['app_agree_fail']              = '未接受许可协议';
$lang['app_get_data_fail']           = '获取数据失败';
$lang['app_send_code_fail']          = '发送验证码失败'; 
$lang['app_add_data_fail']           = '添加数据失败';
$lang['app_update_data_fail']       = '更新数据失败';
$lang['app_delete_data_fail']        = '删除数据失败';
$lang['app_no_more_data']            = '没有更多数据';
$lang['app_msg_more_than_limit']     = '短信条数超过次数限制';

$lang['app_server_busy']             = '服务器忙';
$lang['app_server_protect']          = '服务器维护中...';

$lang['app_param_null']              = '参数为空';
$lang['app_param_err']               = '参数类型错误';
$lang['app_param_illegal']           = '参数非法';
$lang['app_param_pic_big']           = '图片过大';
$lang['app_param_pic_type_err']      = '图片类型错误';
$lang['app_param_pic_exist']         = '图片已经上传';
$lang['app_pic_upload_err']          = '图片上传失败';
$lang['app_auth_type_err']           = '只能选择一种类型认证';
$lang['app_param_location_err']      = '经纬度坐标错误';
$lang['app_param_code_err']          = '验证码错误';
$lang['app_param_code_expire']       = '验证码过期';

$lang['app_req_illegal']             = '非法请求';
$lang['app_req_freq_big']            = '请求频率过高';
$lang['app_req_method_err']          = '请求方法不支持';
$lang['app_req_repeat']              = '重复请求';
$lang['app_req_version_same']        = '已是最新版本';

$lang['app_order_get_err']           = '获取数据失败';
$lang['app_order_null']              = '订单不存在';
$lang['app_order_more_then_set']     = '您超过3天未处理的订单超过10个,不能报价';
$lang['app_offer_exist']             = '您已经报过价格.请前往我的订单中心查看';
$lang['app_order_delete']            = '用户订单已删除,无法进行报价';
$lang['app_order_switch_on']         = '开始接收订单信息';
$lang['app_order_switch_off']        = '您已关闭订单接收功能,无法获取最新订单信息,如需查询请先开启订单接受功能';

$lang['app_user_null']               = '用户不存在';
$lang['app_user_wait']               = '待审核';
$lang['app_user_freeze']             = '冻结中...';
$lang['app_user_fail']               = '未通过';
$lang['app_user_reg_succ']           = '注册成功';
$lang['app_user_login_succ']         = '登陆成功';
$lang['app_user_reg_fail']           = '注册失败';
$lang['app_user_login_fail']         = '登陆失败';
$lang['app_user_logout_fail']        = '注销失败';
$lang['app_user_key_expire']         = '密钥过期';
$lang['app_user_key_err']            = '密钥错误';
$lang['app_user_token_expire']       = '令牌过期';
$lang['app_user_token_err']          = '令牌错误';
$lang['app_user_swich_close']        = '用户开关处于关闭状态';

$lang['app_money_less']              = '余额不足';
$lang['app_pay_err']                 = '支付异常,请等待系统处理..';
$lang['cancel_pay_success']          = '用户取消订单，充值到账户余额里';
   
$lang['common_contact_number']       = '400-641-5080';

$lang['login_fall']                  = '请重新进入本站';
$lang['order_uploadimg_max']         = '图片上传已达到最大值!';
$lang['order_number_fall']           = '本月报单数量已经达到上限!'
/*************************************/

?>

