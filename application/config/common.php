<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('Asia/Shanghai');
/**********微信配置****************/
$config['TOKEN']                            =   'tgxiaotao';                           //微信token*
$config['APPID']                            =   'wx29a596b5eac42c22';                  //微信APPID*
$config['APPSECRETt']                       =   '2129003c8d29a592d8f39e4cf0ebf8fe';    //微信APPSECRETt*
$config['PARTNERKEY']                       =   "beijingmaijinkejiwangkun20150521";    //微信PARTNERKEY*
$config['FREEZEDAY']                        =   '7';                                   //现金券冻结时间
$config['ORDERNUM']                         =   '10';                                  //订单成交分享次数
$config['GUANZHUNUM']                       =   '20';                                  //每周分享次数
/**********标准化产品模块****************/
$config['weekshare']                        =   'http://wx.recytl.com/index.php/weixin/weekshare';    //每周分享地址
$config['ordershare']                       =   'http://wx.recytl.com/index.php/weixin/ordershare';    //每周分享地址
$config['webhost']                          =   'http://wx.recytl.com/';               //本站域名
$config['CSSPATH']                          =   '/static/weixin/public/css/';
$config['JSPATH']                           =   '/static/weixin/public/js/';
$config['IMGPATH']                          =   '/static/weixin/public/img/';
$config['PAGENUM_BACK']                     =   10;                                    //后台每页展示条数
$config['request_suss']                     =   1;                                     //请求成功
$config['request_codefall']                 =   2001;                                  //验证码错误
$config['request_notdata']                  =   1050;                                  //没有符合条件的记录
$config['request_powerfall']                =   2050;                                  //没有权限执行此操作
/*********系统权限模块***************/
$config['auto_requesttype_wx']              =   1;                                     //来源于微信浏览器
$config['auto_requesttype_app']             =   2;                                     //来源于app
$config['auto_requesttype_pc']              =   3;                                     //来源于pc
$config['power_type_max']                   =   'maijinadmin';                         //权限范围标示
/*********短信模块配置*********/
$config['app_msg_accountsid']               =   'aaf98f894700d34e01471515c80504c9';    //云通讯 开发者主账号
$config['app_msg_accounttoken']             =   '47612a216a6f4693ad0298895ec36f6e';    //token
$config['app_msg_appid']                    =   '8a48b5514f4fc588014f64afabe82971';    //应用Id，
$config['app_msg_serverip']                 =   'sandboxapp.cloopen.com';              //请求地址
$config['app_msg_serverport']               =   '8883';                                //请求端口
$config['app_msg_softversion']              =   '2013-12-26';                          //REST版本号
$config['app_send_succ']                    =   '000000';                              //短信发送成功
$config['msg_model_verifycode']             =    43892;                                //短信模板 用户端绑定手机号啊
$config['msg_model_systemerror']            =    43879;                                //短信模板 系统遇到故障通知
$config['msg_model_extractcash']            =    60857;                                //短信模板 用户提取余额短信验证码
$config['msg_verifycode_number']            =    3;                                    //用户端绑定手机号啊 每天发送短信验证码的次数
$config['msg_verifycode_extract']           =    3;                                    //用户提取余额获取验证码的次数
/*********非标准化产品模块*************/        
$config['request_succ']                     =   1000;                                  //请求成功
$config['request_checkfall']                =   2000;                                  //验证失败
$config['request_fall']                     =   3000;                                  //请求失败
$config['request_optionnull']               =   3050;                                  //参数为空
$config['request_optiontypenull']           =   3060;                                  //参数类型不对
$config['request_option_ischeck']           =   3070;                                  //参数不合法
$config['request_mysql_error']              =   5050;                                  //mysql执行错误
$config['request_power_error']              =   7000;                                  //权限不够
$config['request_illegal_access']           =   6000;                                  //非法访问
$config['baidumap_api_ak']                  =   'bMcQsbwpzGsfWFBGKDHMHjrb';            //百度地图API  API AK参数
$config['page_electronic_type']             =   20;                                    //电子产品型号每页显示数量  
$config['checkcode_Invalid_time']           =   1800;                                  //手机验证码失效时间(单位秒) 
/************极光推送  ****************/
$config['jpush_appkey']                     =   '3290c77e3338296715491ebe';            //极光推送 appkey    
$config['jpush_master_secre']               =   '726f15bee3061c7d23bb755f ';           //极光推送 master_secre
$config['ipone_number_fall']                =    array('170');                         //报单限制手机号码段
$config['ipone_number_succ']                =    array('15324295460','15811310237','18810468975','13141109004','13693333230','18611789828');   //不限制报单次数
/********* APP api **********/
$config['cooperator_token_expire']          =  2592000;  //令牌有效期为30天.
$config['cooperator_reg_msg']               =  45248;    //回收商注册短信模板.
$config['wxuser_done_order']                =  51090;    //回收商支付完成订单,对微信用户短信提醒.
$config['wxuser_cancel_order']              =  52403;    //回收商取消订单,对微信用户短信提醒模板
$config['wxuser_offer_msg_info']            =  51081;    //回收商报价次数提醒模板.
$config['cooperator_auth_cash']             =  1000;     //保证金额. 
$config['msgs_per_day']                     =  3;        //短信一天限制3条。
/***********APP 返回状态码*************/
$config['app_success']                 = 10000; //'成功';
$config['app_agree_fail']              = 10001; // 未接受许可协议;
$config['app_get_data_fail']           = 10002; //获取数据失败.
$config['app_send_code_fail']          = 10003; //发送验证码失败.
$config['app_add_data_fail']           = 10004; //添加数据失败
$config['app_update_data_fail']        = 10005; //更新数据失败
$config['app_delete_data_fail']        = 10006; //删除数据失败.
$config['app_no_more_data']            = 10007; // 没有更多数据.
$config['app_msg_more_than_limit']     = 10008; // 短信条数超过次数限制。
$config['app_server_busy']             = 10010; //'服务器忙';
$config['app_server_protect']          = 10020; //'服务器维护中...';
$config['app_param_null']              = 20000; //'参数为空';
$config['app_param_err']               = 20010; //'参数类型错误';
$config['app_param_illegal']           = 20020; //'参数非法';
$config['app_param_pic_big']           = 20030; //'图片过大';
$config['app_param_pic_type_err']      = 20031; //'图片类型错误';
$config['app_param_pic_exist']         = 20032; //'图片已经上传';
$config['app_pic_upload_err']          = 20033; // 图片上传失败.
$config['app_auth_type_err']           = 20034; // 只能选择一种类型认证.
$config['app_param_location_err']      = 20040; //'经纬度坐标错误';
$config['app_param_code_err']          = 20050; //'验证码错误';
$config['app_param_code_expire']       = 20051; //'验证码过期';
$config['app_req_illegal']             = 30000; //'非法请求';
$config['app_req_freq_big']            = 30010; //'请求频率过高';
$config['app_req_method_err']          = 30020; //'请求方法不支持';
$config['app_req_repeat']              = 30030; //'重复请求';
$config['app_req_version_same']        = 30031; // app版本相同.
$config['app_order_get_err']           = 40000; //'获取订单数据失败';
$config['app_order_null']              = 40010; //'订单不存在';
$config['app_order_more_then_set']     = 40011; //'您超过3天未处理的订单超过10个,不能报价';
$config['app_offer_exist']             = 40012; // 您已经报过价格.请前往我的订单中心查看.
$config['app_order_delete']            = 40013; //用户订单已删除,无法进行报价.
$config['app_order_switch_on']         = 40020; //'开始接收订单信息';
$config['app_order_switch_off']        = 40021; //'您已关闭订单接收功能,无法获取最新订单信息,如需查询请先开启订单接受功能';
$config['app_user_null']               = 50000; //'用户不存在';
$config['app_user_wait']               = 50001; //'待审核';
$config['app_user_freeze']             = 50002; //'冻结中...';
$config['app_user_fail']               = 50003; //'未通过';
$config['app_user_reg_succ']           = 50004; //'注册成功';
$config['app_user_login_succ']         = 50005; //'登陆成功';
$config['app_user_reg_fail']           = 50006; //'注册失败';
$config['app_user_login_fail']         = 50007; //'登陆失败';
$config['app_user_logout_fail']        = 50008; //注销失败.
$config['app_user_key_expire']         = 50010; //'密钥过期';
$config['app_user_key_err']            = 50011; //'密钥错误';
$config['app_user_token_expire']       = 50020; //'令牌过期';
$config['app_user_token_err']          = 50021; //'令牌错误';
$config['app_user_swich_close']        = 50040; //用户开关处于关闭状态.
$config['app_money_less']              = 60010; //'余额不足';
$config['app_pay_err']                 = 60011; //'支付异常,请等待系统处理..'
$config['cancel_pay_success']          = 60012; // '用户取消订单，支付金额充值到余额里'
/************url 跳转***************/
$config['url_submitorder_succ']        = '/index.php/nonstandard/submitorder/Ordersucc'; //订单提交成功
$config['url_verifycode_succ']         = '/index.php/nonstandard/system/welcome';        //查看品牌列表
$config['url_cancelorder_succ']        = '/index.php/nonstandard/order/ViewOrder';       //我的订单
$config['url_edituserdata_succ']       = '/index.php/nonstandard/center/ViewCenter';     //个人中心
$config['url_editorderattr_succ']      = '/index.php/nonstandard/order/EditOrder';       //修改订单
$config['url_viewquote_succ']          = '/index.php/nonstandard/quote/QuoteInfo';       //查看回收商详细信息
$config['url_quotelist_succ']          = '/index.php/nonstandard/quote/ViewQuote';       //查看报价列表
$config['url_quoteinfo_succ']          = '/index.php/nonstandard/quote/transactions';    //?type= 报价id
$config['url_orderinfo_succ']          = '/index.php/nonstandard/wxuser/ViewEvaluation'; //?oid=20151130505398506908  订单编号
$config['url_cancelorder']             = '/index.php/nonstandard/order/Viewcancel';      //?oid='+oid; 参数  订单编号
$config['url_task_succ']               = '/index.php/task/usercenter/taskcenter';                                //任务中心
$config['url_shop_succ']               = '/view/shop/list.html';                                //通花商城
$config['url_pay_callback']            = 'http://test.recytl.com/callback/pay.php';//微信支付通知
/**********系统通知*******/
$config['coop_verify_pass']                 = '恭喜您,您的审核申请已通过';
$config['coop_my_order']                    = "您订单编号为%s的订单已经成交,支付金额为%.2f元"; //我成交的订单通知.
$config['coop_others_order']                = '订单编号为%s的订单,已经由%s完成,订单金额为%.2f元'; // 回收商报价的用户与其他回收商成交时的通知.
$config['coop_new_order']                   = '有新订单等您报价,请注意查看';

/*********阿里大鱼 SDK配置信息***********/
$config['alidayu_appkey']                   = '23309599';                              //阿里大鱼SDK  key
$config['alidayu_secretKey']                = '8064bd60f7da3957208191191f6d759c';      //阿里大鱼SDK  secretKey
$config['alidayu_signname']                 = '回收通';                                  //短信签名
$config['alidayu_extend']                   = '1001';                                  //知通科技 回收通项目编号
$config['alidayu_sendsucc']                 =  0;                                      //阿里大鱼短信发送成功标示
$config['alidayu_shownum']                  = '051482043260';                          //语音验证码呼入号码
$config['alidayu_templte_voicecode']        = 'TTS_5002361';                           //语音验证码模板id
$config['alidayu_templte_extractcash']      = 'SMS_5077262';                           //提现短信验证码模板id
$config['alidayu_templte_reg']              = 'SMS_5002354';                           //注册短信验证码模板id
$config['alidayu_templte_modifypas']        = 'SMS_5002352';                           //更改密码短信验证码模板id
$config['alidayu_templte_cacelorder']       = 'SMS_45465004';                           //回收商取消订单
/***********阿里大鱼ＡＰＰ端***********/
$config['APP_alidayu_reg_login']            = 'SMS_5057502';                          // 回收商注册登陆短信验证
$config['APP_alidayu_offer_msg']            = 'SMS_44715022';                          // 给用户的回收商报价提醒
$config['APP_alidayu_prepay_msg']           = 'SMS_5067491';                          // 回收商预支付成功后短信提醒。
$config['APP_alidayu_modify_price']         = 'SMS_44660051';                          // 回收商修改报价
$config['APP_alidayu_order_success']        = 'SMS_44620025';                          // 交易成功
$config['APP_alidayu_order_cancel']         = 'SMS_5087421';                          // 订单取消
$config['APP_alidayu_cash_msg']             = 'SMS_5077262';                          // 提现验证码
$config['redis_config_aliyun']                =  array(                                  //redis 配置测试环境
        'environment'=>'development',
        'host'=>'123.57.59.0',
        'port'=>6379,
        'user'=>'root',
        'pwd'=>'404error'
);
$config['redis_config_test']                  =  array(                                  //redis 配置测试环境
        'environment'=>'development',
        'host'=>'123.57.59.0',
        'port'=>6379,
        'user'=>'root',
        'pwd'=>'404error'
);
 /**************正式支付宝配置信息**********/
$config['zhifubao_attr']=array(
        //应用ID,您的APPID。
        'app_id'=>'2016121204164758',
        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key'=>'/data/application/webcode/wxmj/application/libraries/zhifubao/key/rsa_private_key.pem',
        //异步通知地址        
        'notify_url'=>'http://test.recytl.com/index.php/callback/pay/zfbCall',
        //同步跳转
        'return_url'=>'http://test.recytl.com/index.php/shop/flowgood/zhifubaoCallback',
        //编码格式
        'charset'=>'UTF-8',
        //支付宝网关
        'gatewayUrl'=>'https://openapi.alipay.com/gateway.do',
        //支付宝公钥
        'alipay_public_key'=>'/data/application/webcode/wxmj/application/libraries/zhifubao/key/rsa_public_key.pem'
);
/****************限制同一个用户同一个接口单位时间内 请求间隔 请求次数*******************/
$config['planOrder_lock_time']                  =  2;       //单位秒
$config['planOrder_lock_count']                 =  10;      //次数
/**************日志 信息**************/
$config['log_wxpay_filename']              = 'logs/'.date('Y-m-d').'WxPay.log';         //微信支付日志

$config['system_service_phone']            = '400-641-5080'; //客服电话

$config['task_number']                     =  1491171;

/* End of file config.php */
/* Location: ./application/config/common.php */
