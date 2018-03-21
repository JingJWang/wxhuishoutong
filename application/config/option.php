<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/***********标准化模块********/
//旧衣数量
$config['ordertype_clothes']       =   array('1'=>'10以下','2'=>'10-40','3'=>'40-80','4'=>'80以上');
//现金卷类型
$config['vouchertype']             =   array('1'=>'关注卷','2'=>'报单卷','3'=>'每周分享卷','4'=>'订单分享卷');
$config['voucherstatus']           =   array('1'=>'未使用','2'=>'已使用');
//产品类型
$config['standard_product']        =   array('1'=>'旧衣产品','2'=>'旧书产品');
//数据状态
$config['data_status']             =   array('1'=>'有效','0'=>'不可用','-1'=>'无效');
/************非标准模块****************/
$config['nonstandard_producd']     =   array(1,2,3);

$config['nonstandard_product_type']=   array( 
                                            array('type_id'=>1,'type_name'=>'数码产品'),
                                            array('type_id'=>2,'type_name'=>'家电产品'),
                                            array('type_id'=>3,'type_name'=>'旧衣产品'));
//奢侈品 -属性
$config['luxurygoods_attribute_key'] = array(
               '9'=>array('sex'=>'性别','used_degree'=>'成色',
                        'size'=>'尺码', 'usedmake'=>'使用痕迹','stuff'=>"材质"
               ),        
              '10'=>array('sex'=>'性别','used_degree'=>'成色',
                        'size'=>'尺码','usedmake'=>'使用痕迹','stuff'=>"材质"
               ),
               '11'=>array('sex'=>'性别','used_degree'=>'成色',
                         'size'=>'尺码','usedmake'=>'使用痕迹','stuff'=>"材质"
               ),
               '12'=>array('sex'=>'性别','used_degree'=>'成色',
                         'size'=>'尺码','usedmake'=>'使用痕迹','stuff'=>"材质"
               ),
               '13'=>array('sex'=>'性别','used_degree'=>'成色',
                         'size'=>'尺码','usedmake'=>'使用痕迹','stuff'=>"材质"
                ),
               '14'=>array('sex'=>'性别','used_degree'=>'成色',
                         'usedmake'=>'使用痕迹','stuff'=>"材质"
                ),   
               '15'=>array('sex'=>'性别','used_degree'=>'成色',
                         'usedmake'=>'使用痕迹','stuff'=>"材质"
                ), 
                '16'=>array('sex'=>'性别','used_degree'=>'成色',
                        'usedmake'=>'使用痕迹','stuff'=>"材质",'oather'=>'其他'
                ),  
                '17'=>array('sex'=>'性别','used_degree'=>'成色',
                        'usedmake'=>'使用痕迹','stuff'=>"材质",'size'=>'尺码',
                ),
                '18'=>array('sex'=>'性别','used_degree'=>'成色',
                        'usedmake'=>'使用痕迹','stuff'=>"材质",'size'=>'尺码','type'=>'类型'
                ),
                '22'=>array('sex'=>'性别','used_degree'=>'成色',
                        'size'=>'尺码','usedmake'=>'使用痕迹','stuff'=>"材质"
                )
);          
//奢侈品 -属性
$config['luxurygoods_attribute_val'] = array(
        '9'=>array('sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                   'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                   'size'=>-1, //代表该字段需要 用户输入
                   'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                   'stuff'=>array('1'=>'真皮','2'=>'pvc+配皮','3'=>'人造革','4'=>'其他')
        ),
        '10'=>array('sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'size'=>array('1'=>'155/S','2'=>'160/M','3'=>'170/L','4'=>'180/XL','5'=>'172/XXL','6'=>'175/XXXL'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                    'stuff'=>array('1'=>'皮革','2'=>'呢绒','3'=>'丝绸','4'=>'棉','5'=>'其他'),
        ),
        '11'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'size'=>array('1'=>'155/S','2'=>'160/M','3'=>'170/L','4'=>'180/XL','5'=>'172/XXL','6'=>'175/XXXL'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                    'stuff'=>array('1'=>'皮革','2'=>'呢绒','3'=>'丝绸','4'=>'棉','5'=>'其他'),
        ),
        '12'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'size'=>array('1'=>'155/S','2'=>'160/M','3'=>'170/L','4'=>'180/XL','5'=>'172/XXL','6'=>'175/XXXL'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                    'stuff'=>array('1'=>'皮革','2'=>'呢绒','3'=>'丝绸','4'=>'棉','5'=>'其他'),
        ),
        '13'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'size'=>array('1'=>35,'2'=>36,'3'=>37,'4'=>38,'5'=>39,'6'=>40,'7'=>41,'8'=>42,'9'=>43,'10'=>44,'11'=>45,'12'=>46,'13'=>47),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色','6'=>'轻微变形','7'=>'明显变形'),
                    'stuff'=>array('1'=>'真皮','2'=>'布面','3'=>'其他'),
        ),
        '14'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                    'stuff'=>array('1'=>'棉','2'=>'真丝','3'=>'丝棉','4'=>'其他'),
        ),
        '15'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微变色','3'=>'明显变色'),
                    'stuff'=>array('1'=>'皮革','2'=>'呢绒','3'=>'布面','4'=>'其他'),
        ),
        '16'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕'),
                    'stuff'=>array('1'=>'树脂','2'=>'玻璃','3'=>'塑料','4'=>'其他'),
                    'oather'=>array('1'=>'原厂眼镜盒'),
        ),
        '17'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                    'stuff'=>array('1'=>'真皮','2'=>'pvc+配皮','3'=>'人造革','4'=>'其它'),
                    'size'=>-1,
        ),
        '18'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕'),
                    'stuff'=>array('1'=>'真皮','2'=>'其它'),
                    'type'=>array('1'=>'板扣','2'=>'自动扣'),
                    'size'=>-1,
        ),
        '22'=>array(
                    'sex'=>array('1'=>'女','2'=>'男','3'=>'男女均可(中性)'),
                    'used_degree'=>array('1'=>'九成新','2'=>'八成新','3'=>'七成新','4'=>'六成新','5'=>'五成新及以下'),
                    'size'=>array('1'=>'155/S','2'=>'160/M','3'=>'170/L','4'=>'180/XL','5'=>'172/XXL','6'=>'175/XXXL'),
                    'usedmake'=>array('1'=>'无使用痕迹','2'=>'轻微划痕','3'=>'明显划痕','4'=>'轻微变色','5'=>'明显变色'),
                    'stuff'=>array('1'=>'皮革','2'=>'呢绒','3'=>'丝绸','4'=>'棉','5'=>'其他'),
        )
);
//电子产品属性名称
$config['electronic_attribute_key']   =  array( 
         '5'=>array('channel'=>'购买渠道','capacity'=>'存储容量','guarantee'=>'保修情况','frame'=>'边框背板',
                    'screen'=>'屏幕外观','display'=>'屏幕显示','repair'=>'维修拆机史','gsm'=>'制式',
                    'color'=>'颜色','oather'=>'其他问题'
          ),
         '6'=>array('memory'=>'内存容量','cddrive'=>'光驱类型','screen'=>'屏幕尺寸','graphics'=>'显卡类型',
                    'harddisk'=>'硬盘容量','camera'=>'摄像头','graphics'=>'显卡类型','color'=>'颜色',
                    'hardtype'=>'硬盘类型','battery'=>'电池情况','repair'=>'维修拆机史','bc'=>'屏幕及键盘面',
                    'ad'=>'屏后盖及底面','run'=>'开机运行','oather'=>'其他问题(多选)',
         ),
         '7'=>array('channel'=>'购买渠道','network'=>'网络模式','capacity'=>'存储容量','guarantee'=>'保修情况',
                    'color'=>'颜色','battery'=>'电池情况','repair'=>'维修拆机史','frame'=>'边框背板','screen'=>'屏幕外观',
                    'oather'=>'其他问题'
         ),
         '8'=>array('channel'=>'购买渠道','guarantee'=>'保修情况','appearance'=>'外观成色','screen'=>'屏幕外观',
                 'oather'=>'其他问题'
         ),//{"amount":"121","content":"12312312","name":"3123","cardid":"12312","cardname":"312312"}
        '10'=>array('amount'=>'数量','oather'=>'备注','name'=>'姓名','cardid'=>'卡号','cardname'=>'开户行'),
        '11'=>array('product'=>'产品','purity'=>'纯度','total'=>'总价','type'=>'交易类型','weight'=>'重量','metaltype'=>'分类','original'=>'用户填写')
);
//电子产品属性样式
$config['electronic_attribute_style']=array(
       '5'=>array('channel'=>array('isnull'=>1,'repe'=>0),
                  'capacity'=>array('isnull'=>1,'repe'=>0),
                  'guarantee'=>array('isnull'=>1,'repe'=>0),
                  'frame'=>array('isnull'=>1,'repe'=>0),
                  'screen'=>array('isnull'=>1,'repe'=>0),
                  'display'=>array('isnull'=>1,'repe'=>0),
                  'repair'=>array('isnull'=>1,'repe'=>0),
                  'gsm'=>array('isnull'=>1,'repe'=>0),
                  'color'=>array('isnull'=>1,'repe'=>0),
                  'oather'=>array('isnull'=>0,'repe'=>1),
        ),
        '6'=>array('memory'=>array('isnull'=>1,'repe'=>0),
                   'cddrive'=>array('isnull'=>1,'repe'=>0),
                   'screen'=>array('isnull'=>1,'repe'=>0),
                   'graphics'=>array('isnull'=>1,'repe'=>0),
                   'harddisk'=>array('isnull'=>1,'repe'=>0),
                   'camera'=>array('isnull'=>1,'repe'=>0),
                   'graphics'=>array('isnull'=>1,'repe'=>0),
                   'color'=>array('isnull'=>1,'repe'=>0),
                   'hardtype'=>array('isnull'=>1,'repe'=>0),
                   'battery'=>array('isnull'=>1,'repe'=>0),
                   'repair'=>array('isnull'=>1,'repe'=>0),
                   'bc'=>array('isnull'=>1,'repe'=>0),
                   'ad'=>array('isnull'=>1,'repe'=>0),
                   'run'=>array('isnull'=>1,'repe'=>0),
                   'oather'=>array('isnull'=>0,'repe'=>1),
        ),
        '7'=>array(
                'channel'=>array('isnull'=>1,'repe'=>0),
                'network'=>array('isnull'=>1,'repe'=>0),
                'capacity'=>array('isnull'=>1,'repe'=>0),
                'guarantee'=>array('isnull'=>1,'repe'=>0),
                'color'=>array('isnull'=>1,'repe'=>0),
                'battery'=>array('isnull'=>1,'repe'=>0),
                'repair'=>array('isnull'=>1,'repe'=>0),
                'frame'=>array('isnull'=>1,'repe'=>0),
                'screen'=>array('isnull'=>1,'repe'=>0),
                'oather'=>array('isnull'=>0,'repe'=>1),
        ),
        '8'=>array(
                'channel'=>array('isnull'=>1,'repe'=>0),
                'guarantee'=>array('isnull'=>1,'repe'=>0),
                'appearance'=>array('isnull'=>1,'repe'=>0),
                'screen'=>array('isnull'=>1,'repe'=>0),
                'enclosure'=>array('isnull'=>1,'repe'=>0),
                'oather'=>array('isnull'=>0,'repe'=>1),
        )
);
//电子产品属性值
$config['electronic_attribute_val']   =  array( 
                                            '5'=>array(
                                                    'channel'=>array('大陆国行','香港行货','水货无锁','水货有锁'),
                                                    'capacity'=>array('2G以下','2G','4G','8G','16G','32G','64G','128G'),
                                                    'guarantee'=>array('保修一个月以上','保修一个月内或过保'),
                                                    'frame'=>array('全新机器','外壳完好','外壳有划痕','外壳有磕碰或掉漆'),
                                                    'screen'=>array('屏幕完好','屏幕有划痕','屏幕有缺角或碎裂'),
                                                    'display'=>array('屏幕正常显示','屏幕有亮坏点或色差','屏幕无法显示','屏幕显示异常'),
                                                    'repair'=>array('无拆机无维修','小拆修','大拆修','报废'),
                                                    'gsm'=>array('三网4G','移动联通4G','联通电信4G','电信4G','移动4G','联通4G','非4G'),
                                                    'color'=>array('黑 ','白','土豪金','银','灰','其他','玫瑰金'),
                                                    'oather'=>array('按键不正常','无法正常开机','通话不正常','触摸功能不正常','拍照摄像不正常',
                                                             '充电不正常','无线不正常','屏幕进灰','机身进水或受潮','机身变形','指南针功能不正常',
                                                            '震动功能不正常','无触摸笔或无法正常使用','重力感应不正常','已破解Root','icloud无法注销',
                                                    'id忘记无法激活')
                                            ),
                                            '6'=>array(
                                                    'memory'=>array('1G','2G','3G','4G'),
                                                    'cddrive'=>array('DVD刻录机','蓝光光驱'),
                                                    'screen'=>array('10.1英寸及以下','11.6英寸','12.5英寸','12.5英寸',
                                                            '14.0英寸','15.6英寸','17.3英寸','18.4英寸'),
                                                    'color'=>array('灰','黑','银','蓝','红','白','粉'),
                                                    'graphics'=>array('双显卡切换（独立+集成）','中低端独立显卡','集成显卡','独立显卡'),
                                                    'hardtype'=>array('固态硬盘(SSD)','机械硬盘(HDD)','混合硬盘(HHD)'),
                                                    'harddisk'=>array('64G及以下','128G','256G','512G','1T及以上'),
                                                    'battery'=>array('电池完好','无电池/有损耗'),
                                                    'repair'=>array('无维修','有维修','报废'),
                                                    'bc'=>array('屏幕及键盘面完好','屏幕及键盘面有划痕油光','屏幕及键盘面有磕碰掉漆'),
                                                    'ad'=>array('屏后盖及底面完好','屏后盖及底面有划痕油光','屏后盖及底面有磕碰掉漆'),
                                                    'run'=>array('正常运行','完全无法开机','能开机但无法进入系统'),
                                                    'camera'=>array('集成摄像头','集成200万像素摄像头','集成30万像素摄像头'),
                                                    'graphics'=>array('独立显卡','集成显卡','独立集成双显卡'),
                                                    'oather'=>array('USB无法正常读取','光驱损坏','显卡损坏','摄像头损坏','硬盘损坏',
                                                            '键盘无法正常使用',' 触摸板无法正常使用')
                                            ),
                                            '7'=>array(
                                                    'channel'=>array('大陆国行','香港行货','水货','水货有锁'),
                                                    'network'=>array('WIFI','WIFI+3G','WIFI+4G'),
                                                    'capacity'=>array('8G','16G','32G','64G','128G'),
                                                    'guarantee'=>array('保修1个月以上','保修1个月内或过保'),
                                                    'color'=>array('黑色','白色','银色','金色','其他'),
                                                    'battery'=>array('电池完好','无电池/有损耗'),
                                                    'repair'=>array('无维修','有维修','报废'),
                                                    'frame'=>array('全新机器','外壳完好','外壳有划痕','外壳有磕碰或掉漆'),
                                                    'screen'=>array('屏幕完好','屏幕有划痕','屏幕有缺角或碎裂'),
                                                    'oather'=>array('无法正常开机','触摸功能不正常','充电功能不正常','拍照设想不正常','机身弯曲',
                                                            '机身进水或受潮','无线不正常 ','屏幕进灰' )
                                            ),
                                            '8'=>array(
                                                    'channel'=>array('行货','水货'),
                                                    'guarantee'=>array('全国联保在保','无或已过保'),
                                                    'appearance'=>array('全新机器','外壳完好','外壳有划痕','外壳有磕碰或掉漆'),
                                                    'screen'=>array('屏幕完好','屏幕有划痕','屏幕有缺角或碎裂'),
                                                    'oather'=>array('无法正常开机','按键不正常','液晶屏显示不正常','机身进水',
                                                            '有拆修','拍摄功能不正常','对焦阻尼不正常')
                                            ),
);
//家电产品属性
$config['appliance_attribute_key']  = array(
                                            '19'=>array('type'=>'类别','cw'=>'冷暖类型','p'=>'商品匹数'),
                                            '20'=>array('type'=>'产品类型','capacity'=>'洗涤容量','power'=>'功率'),
                                            '21'=>array('type'=>'产品类型','resolution'=>'分辨率','3d'=>'是否支持3d','size'=>'电视尺寸'),
                                            '23'=>array('volume'=>'容积','temperature'=>'控温','refrigeration'=>'制冷方式','frequency'=>'压缩机')
);
$config['appliance_attribute_val']  = array(
                                            '19'=>array(
                                                    'type' =>array('1'=>'变频空调','2'=>'圆柱式柜机','3'=>'智能空调','4'=>'超静音空调',
                                                       '5'=>'壁挂式空调','6'=>'立柜式空调','7'=>'中央空调','8'=>'移动空调','9'=>'天花机'),
                                                    'cw'=>array('1'=>'冷暖空调','2'=>'单冷空调'),
                                                    'p'=>array('1'=>'1P','2'=>'1.5P','3'=>'2P','4'=>'2.5P','5'=>'3P')
                                            ),
                                            '20'=>array(
                                                      'type'=>array('1'=>'滚筒','2'=>'洗烘一体','3'=>'斜式滚筒','4'=>'波轮',
                                                                '5'=>'全自动消毒洗衣机','6'=>'干衣机','7'=>'手持洗衣机','8'=>'双缸','9'=>'迷你洗衣机'
                                                       ),
                                                      'capacity'=>array(
                                                              '1'=>'4.5kg及以下','2'=>'4.6-5.9kg','3'=>'6.0kg','4'=>'6.1-6.9kg',
                                                              '5'=>'7kg','6'=>'7.1-7.9kg','7'=>'8kg','8'=>'8.1-8.9kg','9'=>'9kg',
                                                              '10'=>'9.1-9.9kg','11'=>'10kg及以上'
                                                        ),
                                                      'power'=>array('1'=>'190W以下','2'=>'190-249W','3'=>'250-349W',
                                                               '4'=>'350-399W','5'=>'400W以上')    
                                            ),
                                            '21'=>array(
                                                    'type'=>array('1'=>'4K超清电视','2'=>'智能电视','3'=>'非智能电视','4'=>'曲面电视','5'=>'电影放映机'),
                                                    'resolution'=>array('1'=>'4K超高清(3840x2160)','2'=>'全高清(1920x1080)','3'=>'高清(1366x768)','4'=>'其他'),
                                                     '3d'=>array('1'=>'支持','2'=>'不支持'),
                                                    'size'=>array('1'=>'65英寸及以上','2'=>'58-60英寸','3'=>'55英寸','4'=>'50-52英寸','5'=>'46-49英寸','6'=>'42-43英寸',
                                                            '7'=>'39-40英寸','8'=>'32英寸及以下')
                                            ),
                                            '23'=>array(
                                                'volume'=>array('1'=>'150L以下','2'=>'150-210升','3'=>'211-230升','4'=>'231-280升','5'=>'281-450升','6'=>'451-560升','7'=>'561以上'),
                                                'temperature'=>array('1'=>'电脑控温','2'=>'机械控温'),
                                                'refrigeration'=>array('1'=>'直冷','2'=>' 风冷(无霜)','3'=>'风直冷'),
                                                'frequency'=>array('1'=>'变频','2'=>'定频'),
                                            )
        
);
//订单模块-取消订单-原因
$config['order_cancel_option']      =  array('临时有事','联系不上回收商','不想卖了','回收商报价不符');
//订单模块--订单成交--原因
$config['order_deal_option']        =  array('期待下次交易 ','诚实可信','价格公道','付款及时','需要继续努力');
$config['order_deal_cancel']        =  array('暂时不想卖了','信息填写错误，重新发单','回收商回应不及时','其他原因');
/*********** APP api ********************/

$config['cooperator_auth_type'] = array('0' => '未认证','1'=>'个人认证','2'=>'企业认证','3'=>'保证金认证',
        '4'=>'个人+保证金认证','5' =>'企业+保证金认证');
$config['cooperator_sex'] = array('1' => '男','2'=>'女');
$config['cooperator_switch'] = array('1' => '开收','-1'=>'关闭');
$config['cooperator_userstatus'] = array('0' =>'待审核','1' => '冻结','2'=>'未通过','3' => '通过');
$config['cooperator_car_type'] = array('1' => '金杯','2'=>'五菱');
$config['cooperator_service_range'] = array('1' => '1公里以内','5' => '5公里以内','10' => '10公里以内',
        '20' => '20公里以内','50' => '50公里以内');
$config['cooperator_has_store'] = array('-1' => '没有','2'=>'有');
$config['cooperator_opening'] = array('1' => '家具','2'=>'珠宝','3'=>'古玩字画','4'=>'药材','5'=>'烟酒礼品'); 
$config['cooperator_service_content'] = array('6'=>'许可协议','1'=>'关于我们','2'=>'版本更新','3'=>'使用指南','4'=>'意见反馈','5'=>'法律条款');   
$config['cooperator_filter_enable'] = array('1'=>'可用','-1'=>'不可用');   
$config['cooperator_filter_sort'] = array('1'=>'距离','2'=>'结束时间','3'=>'客户信誉');
$config['cooperator_cancel_option'] = array('1'=>'临时有事','2'=>'联系不上客户','3'=>'客户不想卖了','4'=>'描述不符');
$config['cooperator_comment_option'] = array('1'=>'期待下次交易','2'=>'描述准确','3'=>'诚实可信','4'=>'通情达理'); // 成交时回收商评价用户选项.
$config['cooperator_comment_cancel'] = array('1' => '期待下次交易','2' => '价格报错，重新填写','3'=>'其他原因','4'=>'联系不上客户'); //取消时回收商评价用户选项.
$config['cooperator_offer_service'] = array('1'=>'上门回收','2'=>'到小区','3'=>'送来回收');
$config['cooperator_special_service'] = array('id' => '4','describe' => '快递包邮'); //特别服务项.
$config['cooperator_cell_mail'] = array('13167310237','15324295460','13693333230','18810468975','13552837578',
'18610560865','18611017073','15230051651','18210580651','18811540989',
'18811541951','13501295499','15369322638','13141109004','13126834813'); // 能提供快递包邮服务的手机号.
$config['cooperator_offer_list_size'] = 10; // 每页返回的记录数.
$config['cooperator_order_vaild_time'] = 86400; //订单有效期为24小时.
$config['cooperator_wxuser_msg'] = array(1,3,6); // 回收商报价时对用户的短信提醒. 
$config['coop_wxuser_offer_info'] = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的订单有新报价", "description":"您的订单有新报价,请点击报价查询查看!",
                "url":"%s", "picurl":""}]}}';//回收商报价时对微信用户的通知.
$config['coop_wxuser_offer_url'] = '/index.php/nonstandard/quote/ViewQuote?id=%s';
$config['coop_wxuser_prepay_done_info'] = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"订单预支付完成", "description":"尊敬的用户,您在回收通出售的%s,回收商已支付，价格为%s元,请点击查看!",
                "url":"%s", "picurl":""}]}}'; //回收商完成预支付时对微信用户的通知
$config['coop_wxuser_modify_url'] = '/index.php/nonstandard/order/ViewOrderInfo?id=%s'; //修改报价提醒地址。
$config['coop_wxuser_modify_info'] = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"修改报价", "description":"尊敬的用户,您在回收通出售的%s报价被更新,请点击查看!",
                "url":"%s", "picurl":""}]}}'; // 回收商修改报价后的微信提醒。
$config['coop_wxuser_done_info'] = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"订单完成", "description":"尊敬的用户,您在回收通出售的%s,已经成交 成交价格为%s元,请点击查看!",
                "url":"%s", "picurl":""}]}}'; //回收商完成订单时对微信用户的通知
$config['coop_wxuser_done_url'] = '/index.php/nonstandard/order/ViewOrder?status=e';
$config['coop_wxuser_cancel_info'] = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"订单取消", "description":"尊敬的用户,您在回收通出售的%s,在%s被回收商取消订单,请点击查看详情!",
                "url":"%s", "picurl":""}]}}';//回收商取消订单时对微信用户的通知
$config['coop_wxuser_cancel_url'] = '/index.php/nonstandard/order/ViewOrder?status=q';
// 固定地址的回收商
$config['coop_addr_fixed'] = array(
    array('user_id' => '1449722419','lng' => '116.445695','lat' => '39.891817','addr' => '光明楼37号'),
    array('user_id' => '1449812456','lng' => '116.444223','lat' => '39.976381','addr' => '西坝河'),
    array('user_id' => '1449728702','lng' => '116.442983','lat' => '39.990804','addr' => '芍药居'),
    array('user_id' => '151215593588660','lng' => '116.427536','lat' => '39.993423','addr' => '小关街道'),
    array('user_id' => '160216480407797','lng' => '116.445545','lat' => '39.890209','addr' => '光明楼'),
    array('user_id' => '160129441816953','lng' => '116.465716','lat' => '39.911135','addr' => '建外SOHO'),
    array('user_id' => '160129436549154','lng' => '116.404801','lat' => '39.864449','addr' => '木樨园'),
    array('user_id' => '160129449155290','lng' => '116.426102','lat' => '39.993523','addr' => '千鹤家园'),
    array('user_id' => '160629486457725','lng' => '116.301726','lat' => '39.913909','addr' => '公主坟'),
);
/***************************************/

/**************用户报价筛选条件*********************/
$config['user_quote_option']    =      array('distance'=>'距离','price'=>'价格','evaluation'=>'评价','transaction'=>'成交单数');
//用户取消订单原因
$config['user_cancel_order']    =      array('1'=>'临时有事','2'=>'联系不上回收商','3'=>'不想卖了','4'=>'回收商报价不符');
//首页交易记录
$config['dynamic'] =array(
        '0'=>array(
                'name'=>'苹果 iPhone 6Plus ',
                'moeny'=>'3400.00元现金+300通花',
                'type'=>'霓裳起舞',
                'mobile'=>'153*****089',
                'time'=>'1467187400',
                'content'=>''

        ),
        '1'=>array(
                'name'=>'小米 4 ',
                'moeny'=>'480.00元现金+300通花',
                'type'=>'你好明天',
                'mobile'=>'139*****906',
                'time'=>'1467186540',
                'content'=>''
        ),
        '2'=>array(
                'name'=>'苹果 iPhone 4S',
                'moeny'=>'280.00元现金+300通花',
                'type'=>'流年',
                'mobile'=>'152*****080',
                'time'=>'1467178560',
                'content'=>''
        ),
        '3'=>array(
                'name'=>'苹果 iPhone 6',
                'moeny'=>'2800.00元现金+300通花',
                'type'=>'茤於d囬苡',
                'mobile'=>'187*****634',
                'time'=>'1467172860',
                'content'=>''
        ),
        '4'=>array(
                'name'=>'苹果 iPhone 5S',
                'moeny'=>'1000.00元现金+300通花',
                'type'=>'泪炎',
                'mobile'=>'156*****738',
                'time'=>'1467168120',
                'content'=>''
        )
);
$config['index_data']=array(
        '0725'=>array(8463,'13,028,418'),
        '0726'=>array(8489,'13,065,578'),
        '0727'=>array(8507,'13,093,948'),
        '0728'=>array(8452,'13,018,618'),
        '0706'=>array(8463,'13,028,418'),
        '0707'=>array(8489,'13,065,578'),
        '0708'=>array(8507,'13,093,948'),
        '0709'=>array(8452,'13,018,618'),
        '0710'=>array(8463,'13,028,418'),
        '0711'=>array(8463,'13,028,418'),
        '0712'=>array(8489,'13,065,578'),
        '0713'=>array(8507,'13,093,948'),
        '0714'=>array(8452,'13,018,618'),
        '0715'=>array(8463,'13,028,418'),
        '0716'=>array(8489,'13,065,578'),
        '0717'=>array(8507,'13,093,948')
);
//标示寄售商
$config['js_cooplist']=array('160129449155290');
//自动报价列表 默认使用 手机号码13167310237 报价方案
$config['quote_plan_coop']=array(
        '151215593588660',
        '160629486457725',
        '160129441816953',
        '160129436549154',
);
//回收商认证单位
$config['coop_auth_company']=array(
        '151215593588660'=>'苏博电子',
        '160629486457725'=>'展讯通信',
        '160129436549154'=>'迪华通讯',
        '160129441816953'=>'程维科技',
        '160129449155290'=>'苏博电子'
);
/********任务中心********/
/**任务类型**/
$config['task_types']=array('1'=>'签到','2'=>'回收','3'=>'分享','4'=>'精华','5'=>'邀请用户','6'=>'邀请回收商','7'=>'主线','8'=>'活动','9'=>'点击链接','10'=>'投票');
$config['task_types_data']=array('1'=>'task_sign','2'=>'task_turnover','3'=>'task_share','4'=>'jinghua','5'=>'task_invite_u','6'=>'task_invite_m');
/**任务进度  1：未领取、2：进行中、3：未领奖、4：已完成、5：已过期**/
$config['task_process']=array('1'=>'weiwancheng.png','2'=>'weiwancheng.png','3'=>'weilingjiang.png','4'=>'ywc.png','5'=>'yiguoqi.png');
$config['task_process_eng']=array('noReceive'=>1,'processing'=>2, 'noAward'=>3,'completed'=>4,'expired'=>5);
/**奖金选择类型**/
$config['reward_type']=array('1'=>'','2'=>'二选一','3'=>'三选一','4'=>'三选二');
$config['reward_select']=array('1'=>'奖金','2'=>'树苗','3'=>'基金','4'=>'奖金和树苗各一半');
$config['taskshare']    =   'http://wx.recytl.com/index.php/task/otherget/getothersay';    //任务分享地址
$config['share_rand']		 = 3;	//分享任务中，可以随机抽取的个数。
$config['share_limit_time']  = 3;	//用户分享一次后，离下一次可以的时间间隔（单位：小时）
/********会员中心********/
$config['fund']=800;//环保基金
$config['integral']=800;//通花