$(function(){
	/******************************城市选择cityCelect  页面js***********************************/
	var city = {
		A: [{
			place: "鞍山市",
			dataId: "210300"
		},
		{
			place: "阿拉善盟",
			dataId: "152900"
		},
		{
			place: "安庆市",
			dataId: "340800"
		},
		{
			place: "安阳市",
			dataId: "410500"
		},
		{
			place: "阿里地区",
			dataId: "542500"
		},
		{
			place: "安康市",
			dataId: "610900"
		},
		{
			place: "安顺市",
			dataId: "520400"
		},
		{
			place: "阿坝藏族羌族自治州",
			dataId: "513200"
		},
		{
			place: "阿拉尔市",
			dataId: "659002"
		},
		{
			place: "阿克苏地区",
			dataId: "652900"
		},
		{
			place: "澳门特别行政区",
			dataId: "820100"
		},
		{
			place: "阿勒泰地区",
			dataId: "654300"
		}
		],
		B: [{
			place: "白城市",
			dataId: "220800"
		},
		{
			place: "包头市",
			dataId: "150200"
		},
		{
			place: "巴彦淖尔市",
			dataId: "150800"
		},
		{
			place: "保定市",
			dataId: "130600"
		},
		{
			place: "本溪市",
			dataId: "210500"
		},
		{
			place: "白山市",
			dataId: "220600"
		},
		{
			place: "亳州市",
			dataId: "341600"
		},
		{
			place: "蚌埠市",
			dataId: "340300"
		},
		{
			place: "滨州市",
			dataId: "371600"
		},
		{
			place: "白银市",
			dataId: "620400"
		},
		{
			place: "宝鸡市",
			dataId: "610300"
		},
		{
			place: "保山市",
			dataId: "530500"
		},
		{
			place: "白沙黎族自治县",
			dataId: "469030"
		},
		{
			place: "百色市",
			dataId: "451000"
		},
		{
			place: "毕节市",
			dataId: "522401"
		}
,
		{
			place: "北海市",
			dataId: "450500"
		},
		{
			place: "巴中市",
			dataId: "511900"
		},
		{
			place: "保亭黎族苗族自治县",
			dataId: "469035"
		},
		{
			place: "巴音郭楞蒙古自治州",
			dataId: "652800"
		},
		{
			place: "博尔塔拉蒙古自治州",
			dataId: "652700"
		},
		{
			place: "北京市",
			dataId: "110100"
		}
		],
		C: [{
			place: "长治市",
			dataId: "140400"
		},
		{
			place: "沧州市",
			dataId: "130900"
		},
		{
			place: "常州市",
			dataId: "320400"
		},
		{
			place: "慈溪市",
			dataId: "330282"
		},
		{
			place: "常熟市",
			dataId: "320581"
		},
		{
			place: "承德市",
			dataId: "130800"
		},
		{
			place: "赤峰市",
			dataId: "150400"
		},
		{
			place: "长春市",
			dataId: "220100"
		},
		{
			place: "郴州市",
			dataId: "431000"
		},
		{
			place: "长沙市",
			dataId: "430100"
		},
		{
			place: "滁州市",
			dataId: "341100"
		},
		{
			place: "常德市",
			dataId: "430700"
		},
		{
			place: "巢湖市",
			dataId: "341400"
		},
		{
			place: "池州市",
			dataId: "341700"
		},
		{
			place: "澄迈县",
			dataId: "469027"
		}
		,
		{
			place: "崇左市",
			dataId: "451400"
		},
		{
			place: "昌江黎族自治县",
			dataId: "469031"
		},
		{
			place: "楚雄彝族自治州",
			dataId: "532300"
		},
		{
			place: "潮州市",
			dataId: "445100"
		},
		{
			place: "重庆市",
			dataId: "500100"
		},
		{
			place: "成都市",
			dataId: "510100"
		},
		{
			place: "昌都地区",
			dataId: "542100"
		},
		{
			place: "昌吉回族自治州",
			dataId: "652300"
		}
		],
		D: [{
			place: "大理白族自治州",
			dataId: "dali"
		},
		{
			place: "大连市",
			dataId: "dalian"
		},
		{
			place: "丹东市",
			dataId: "dandong"
		},
		{
			place: "儋州市",
			dataId: "danzhou"
		},
		{
			place: "大庆市",
			dataId: "daqing"
		},
		{
			place: "大同市",
			dataId: "datong"
		},
		{
			place: "大兴安岭地区",
			dataId: "daxinganling"
		},
		{
			place: "达州市",
			dataId: "dazhou"
		},
		{
			place: "德宏傣族景颇族自治州",
			dataId: "dehong"
		},
		{
			place: "德阳市",
			dataId: "deyang"
		},
		{
			place: "德州市",
			dataId: "dezhou"
		},
		{
			place: "定西市",
			dataId: "dingxi"
		},
		{
			place: "迪庆市",
			dataId: "diqing"
		},
		{
			place: "东方市",
			dataId: "dongfang"
		},
		{
			place: "东莞市",
			dataId: "dongguan"
		},
		{
			place: "东营市",
			dataId: "dongying"
		},
		{
			place: "丹阳市",
			dataId: "danyang"
		}
		],
		E: [{
			place: "鄂尔多斯市",
			dataId: "eerduosi"
		},
		{
			place: "恩施土家族苗族自治州",
			dataId: "enshi"
		},
		{
			place: "鄂州市",
			dataId: "ezhou"
		}],
		F: [{
			place: "防城港市",
			dataId: "fangchenggang"
		},
		{
			place: "佛山市",
			dataId: "foshan"
		},
		{
			place: "抚顺市",
			dataId: "fushun"
		},
		{
			place: "阜新市",
			dataId: "fuxin"
		},
		{
			place: "阜阳市",
			dataId: "fuyang"
		},
		{
			place: "福州市",
			dataId: "fuzhou"
		},
		{
			place: "抚州市",
			dataId: "fuzhou1"
		},
		{
			place: "福清市",
			dataId: "fuqing"
		},
		{
			place: "肥城市",
			dataId: "feicheng"
		}
		],
		G: [{
			place: "甘南藏族自治州",
			dataId: "gannan"
		},
		{
			place: "赣州市",
			dataId: "ganzhou"
		},
		{
			place: "甘孜藏族自治州",
			dataId: "ganzi"
		},
		{
			place: "广安市",
			dataId: "guang,an"
		},
		{
			place: "广元市",
			dataId: "guangyuan"
		},
		{
			place: "广州市",
			dataId: "guangzhou"
		},
		{
			place: "贵港市",
			dataId: "guigang"
		},
		{
			place: "桂林市",
			dataId: "guilin"
		},
		{
			place: "贵阳市",
			dataId: "guiyang"
		},
		{
			place: "果洛藏族自治州",
			dataId: "guoluo"
		},
		{
			place: "固原市",
			dataId: "guyuan"
		}],
		H: [{
			place: "哈尔滨市",
			dataId: "haerbin"
		},
		{
			place: "海北市",
			dataId: "haibei"
		},
		{
			place: "海东市",
			dataId: "haidong"
		},
		{
			place: "海口市",
			dataId: "haikou"
		},
		{
			place: "海南市",
			dataId: "hainan"
		},
		{
			place: "海西市",
			dataId: "haixi"
		},
		{
			place: "哈密市",
			dataId: "hami"
		},
		{
			place: "邯郸市",
			dataId: "handan"
		},
		{
			place: "杭州市",
			dataId: "hangzhou"
		},
		{
			place: "汉中市",
			dataId: "hanzhong"
		},
		{
			place: "鹤壁市",
			dataId: "hebi"
		},
		{
			place: "河池市",
			dataId: "hechi"
		},
		{
			place: "合肥市",
			dataId: "hefei"
		},
		{
			place: "鹤岗市",
			dataId: "hegang"
		},
		{
			place: "黑河市",
			dataId: "heihe"
		},
		{
			place: "衡水市",
			dataId: "hengshui"
		},
		{
			place: "衡阳市",
			dataId: "hengyang"
		},
		{
			place: "河源市",
			dataId: "heyuan"
		},
		{
			place: "菏泽市",
			dataId: "heze"
		},
		{
			place: "贺州市",
			dataId: "hezhou"
		},
		{
			place: "红河哈尼族彝族自治州",
			dataId: "honghe"
		},
		{
			place: "和田市",
			dataId: "hotan"
		},
		{
			place: "淮安市",
			dataId: "huaian"
		},
		{
			place: "淮北市",
			dataId: "huaibei"
		},
		{
			place: "怀化市",
			dataId: "huaihua"
		},
		{
			place: "淮南市",
			dataId: "huainan"
		},
		{
			place: "黄冈市",
			dataId: "huanggang"
		},
		{
			place: "黄南市",
			dataId: "huangnan"
		},
		{
			place: "黄山市",
			dataId: "huangshan"
		},
		{
			place: "黄石市",
			dataId: "huangshi"
		},
		{
			place: "呼和浩特市",
			dataId: "huhehaote"
		},
		{
			place: "惠州市",
			dataId: "huizhou"
		},
		{
			place: "葫芦岛市",
			dataId: "huludao"
		},
		{
			place: "呼伦贝尔市",
			dataId: "hulunbeier"
		},
		{
			place: "湖州市",
			dataId: "huzhou"
		},
		{
			place: "海宁市",
			dataId: "haining"
		}],
		J: [{
			place: "佳木斯市",
			dataId: "jiamusi"
		},
		{
			place: "吉安市",
			dataId: "jian"
		},
		{
			place: "江门市",
			dataId: "jiangmen"
		},
		{
			place: "焦作市",
			dataId: "jiaozuo"
		},
		{
			place: "嘉兴市",
			dataId: "jiaxing"
		},
		{
			place: "嘉峪关市",
			dataId: "jiayuguan"
		},
		{
			place: "揭阳市",
			dataId: "jieyang"
		},
		{
			place: "吉林市",
			dataId: "jilin"
		},
		{
			place: "济南市",
			dataId: "jinan"
		},
		{
			place: "金昌市",
			dataId: "jinchang"
		},
		{
			place: "晋城市",
			dataId: "jincheng"
		},
		{
			place: "景德镇市",
			dataId: "jingdezhen"
		},
		{
			place: "荆门市",
			dataId: "jingmen"
		},
		{
			place: "荆州市",
			dataId: "jingzhou"
		},
		{
			place: "金华市",
			dataId: "jinhua"
		},
		{
			place: "济宁市",
			dataId: "jining"
		},
		{
			place: "晋中市",
			dataId: "jinzhong"
		},
		{
			place: "锦州市",
			dataId: "jinzhou"
		},
		{
			place: "九江市",
			dataId: "jiujiang"
		},
		{
			place: "酒泉市",
			dataId: "jiuquan"
		},
		{
			place: "鸡西市",
			dataId: "jixi"
		},
		{
			place: "济源市",
			dataId: "jiyuan"
		}],
		K: [{
			place: "开封市",
			dataId: "kaifeng"
		},
		{
			place: "克拉玛依市",
			dataId: "karamay"
		},
		{
			place: "喀什市",
			dataId: "kashi"
		},
		{
			place: "克州市",
			dataId: "kezhou"
		},
		{
			place: "昆明市",
			dataId: "kunming"
		},
		{
			place: "克孜勒苏柯尔克孜自治州",
			dataId: "kezi"
		},
		{
			place: "昆山市",
			dataId: "kunming"
		}],
		L: [{
			place: "来宾市",
			dataId: "laibin"
		},
		{
			place: "莱芜市",
			dataId: "laiwu"
		},
		{
			place: "廊坊市",
			dataId: "langfang"
		},
		{
			place: "兰州市",
			dataId: "lanzhou"
		},
		{
			place: "拉萨市",
			dataId: "lasa"
		},
		{
			place: "乐山市",
			dataId: "leshan"
		},
		{
			place: "凉山市",
			dataId: "liangshan"
		},
		{
			place: "连云港市",
			dataId: "lianyungang"
		},
		{
			place: "聊城市",
			dataId: "liaocheng"
		},
		{
			place: "辽阳市",
			dataId: "liaoyang"
		},
		{
			place: "辽源市",
			dataId: "liaoyuan"
		},
		{
			place: "丽江市",
			dataId: "lijiang"
		},
		{
			place: "临沧市",
			dataId: "lincang"
		},
		{
			place: "临汾市",
			dataId: "linfen"
		},
		{
			place: "临夏市",
			dataId: "linxia"
		},
		{
			place: "临沂市",
			dataId: "linyi"
		},
		{
			place: "林芝市",
			dataId: "linzhi"
		},
		{
			place: "丽水市",
			dataId: "lishui"
		},
		{
			place: "六安市",
			dataId: "liuan"
		},
		{
			place: "六盘水市",
			dataId: "liupanshui"
		},
		{
			place: "柳州市",
			dataId: "liuzhou"
		},
		{
			place: "陇南市",
			dataId: "longnan"
		},
		{
			place: "龙岩市",
			dataId: "longyan"
		},
		{
			place: "娄底市",
			dataId: "loudi"
		},
		{
			place: "漯河市",
			dataId: "luohe"
		},
		{
			place: "洛阳市",
			dataId: "luoyang"
		},
		{
			place: "泸州市",
			dataId: "luzhou"
		},
		{
			place: "吕梁市",
			dataId: "lvliang"
		}],
		M: [{
			place: "马鞍山市",
			dataId: "maanshan"
		},
		{
			place: "茂名市",
			dataId: "maoming"
		},
		{
			place: "眉山市",
			dataId: "meishan"
		},
		{
			place: "梅州市",
			dataId: "meizhou"
		},
		{
			place: "绵阳市",
			dataId: "mianyang"
		},
		{
			place: "牡丹江市",
			dataId: "mudanjiang"
		}],
		N: [{
			place: "南昌市",
			dataId: "nanchang"
		},
		{
			place: "南充市",
			dataId: "nanchong"
		},
		{
			place: "南京市",
			dataId: "nanjing"
		},
		{
			place: "南宁市",
			dataId: "nanning"
		},
		{
			place: "南平市",
			dataId: "nanping"
		},
		{
			place: "南通市",
			dataId: "nantong"
		},
		{
			place: "南阳市",
			dataId: "nanyang"
		},
		{
			place: "那曲市",
			dataId: "naqu"
		},
		{
			place: "内江市",
			dataId: "neijiang"
		},
		{
			place: "宁波市",
			dataId: "ningbo"
		},
		{
			place: "宁德市",
			dataId: "ningde"
		},
		{
			place: "怒江市",
			dataId: "nujiang"
		}],
		P: [{
			place: "盘锦市",
			dataId: "panjin"
		},
		{
			place: "攀枝花市",
			dataId: "panzhihua"
		},
		{
			place: "平顶山市",
			dataId: "pingdingshan"
		},
		{
			place: "平凉市",
			dataId: "pingliang"
		},
		{
			place: "萍乡市",
			dataId: "pingxiang"
		},
		{
			place: "普洱市",
			dataId: "puer"
		},
		{
			place: "莆田市",
			dataId: "putian"
		},
		{
			place: "濮阳市",
			dataId: "puyang"
		}],
		Q: [{
			place: "黔东南市",
			dataId: "qiandongnan"
		},
		{
			place: "潜江市",
			dataId: "qianjiang"
		},
		{
			place: "黔南市",
			dataId: "qiannan"
		},
		{
			place: "黔西南市",
			dataId: "qianxinan"
		},
		{
			place: "青岛市",
			dataId: "qingdao"
		},
		{
			place: "庆阳市",
			dataId: "qingyang"
		},
		{
			place: "清远市",
			dataId: "qingyuan"
		},
		{
			place: "秦皇岛市",
			dataId: "qinhuangdao"
		},
		{
			place: "钦州市",
			dataId: "qinzhou"
		},
		{
			place: "琼海市",
			dataId: "qionghai"
		},
		{
			place: "齐齐哈尔市",
			dataId: "qiqihar"
		},
		{
			place: "七台河市",
			dataId: "qitaihe"
		},
		{
			place: "泉州市",
			dataId: "quanzhou"
		},
		{
			place: "曲靖市",
			dataId: "qujing"
		},
		{
			place: "衢州市",
			dataId: "quzhou"
		}],
		R: [{
			place: "日照市",
			dataId: "rizhao"
		}],
		S: [{
			place: "三门峡市",
			dataId: "sanmenxia"
		},
		{
			place: "三明市",
			dataId: "sanming"
		},
		{
			place: "三亚市",
			dataId: "sanya"
		},
		{
			place: "上海市",
			dataId: "shanghai"
		},
		{
			place: "商洛市",
			dataId: "shangluo"
		},
		{
			place: "商丘市",
			dataId: "shangqiu"
		},
		{
			place: "上饶市",
			dataId: "shangrao"
		},
		{
			place: "山南市",
			dataId: "shannan"
		},
		{
			place: "汕头市",
			dataId: "shantou"
		},
		{
			place: "汕尾市",
			dataId: "shanwei"
		},
		{
			place: "韶关市",
			dataId: "shaoguan"
		},
		{
			place: "绍兴市",
			dataId: "shaoxing"
		},
		{
			place: "邵阳市",
			dataId: "shaoyang"
		},
		{
			place: "神农架市",
			dataId: "shennongjia"
		},
		{
			place: "沈阳市",
			dataId: "shenyang"
		},
		{
			place: "深圳市",
			dataId: "shenzhen"
		},
		{
			place: "石河子市",
			dataId: "shihezi"
		},
		{
			place: "石家庄市",
			dataId: "shijiazhuang"
		},
		{
			place: "十堰市",
			dataId: "shiyan"
		},
		{
			place: "石嘴山市",
			dataId: "shizuishan"
		},
		{
			place: "双鸭山市",
			dataId: "shuangyashan"
		},
		{
			place: "朔州市",
			dataId: "shuozhou"
		},
		{
			place: "四平市",
			dataId: "siping"
		},
		{
			place: "松原市",
			dataId: "songyuan"
		},
		{
			place: "绥化市",
			dataId: "suihua"
		},
		{
			place: "遂宁市",
			dataId: "suining"
		},
		{
			place: "随州市",
			dataId: "suizhou"
		},
		{
			place: "宿迁市",
			dataId: "suqian"
		},
		{
			place: "苏州市",
			dataId: "suzhou"
		},
		{
			place: "宿州市",
			dataId: "suzhou1"
		}],
		T: [{
			place: "塔城市",
			dataId: "tacheng"
		},
		{
			place: "泰安市",
			dataId: "taian"
		},
		{
			place: "台北市",
			dataId: "taibei"
		},
		{
			place: "太原市",
			dataId: "taiyuan"
		},
		{
			place: "台州市",
			dataId: "taizhou"
		},
		{
			place: "泰州市",
			dataId: "taizhou1"
		},
		{
			place: "唐山市",
			dataId: "tangshan"
		},
		{
			place: "天津市",
			dataId: "tianjin"
		},
		{
			place: "天门市",
			dataId: "tianmen"
		},
		{
			place: "天水市",
			dataId: "tianshui"
		},
		{
			place: "铁岭市",
			dataId: "tieling"
		},
		{
			place: "铜川市",
			dataId: "tongchuan"
		},
		{
			place: "通化市",
			dataId: "tonghua"
		},
		{
			place: "通辽市",
			dataId: "tongliao"
		},
		{
			place: "铜陵市",
			dataId: "tongling"
		},
		{
			place: "铜仁市",
			dataId: "tongren"
		},
		{
			place: "吐鲁番市",
			dataId: "turpan"
		}],
		W: [{
			place: "万宁市",
			dataId: "wanning"
		},
		{
			place: "潍坊市",
			dataId: "weifang"
		},
		{
			place: "威海市",
			dataId: "weihai"
		},
		{
			place: "渭南市",
			dataId: "weinan"
		},
		{
			place: "文昌市",
			dataId: "wenchang"
		},
		{
			place: "文山市",
			dataId: "wenshan"
		},
		{
			place: "温州市",
			dataId: "wenzhou"
		},
		{
			place: "乌海市",
			dataId: "wuhai"
		},
		{
			place: "武汉市",
			dataId: "wuhan"
		},
		{
			place: "芜湖市",
			dataId: "wuhu"
		},
		{
			place: "乌兰察布市",
			dataId: "wulanchabu"
		},
		{
			place: "乌鲁木齐市",
			dataId: "wulumuqi"
		},
		{
			place: "武威市",
			dataId: "wuwei"
		},
		{
			place: "无锡市",
			dataId: "wuxi"
		},
		{
			place: "五指山市",
			dataId: "wuzhishan"
		},
		{
			place: "吴忠市",
			dataId: "wuzhong"
		},
		{
			place: "梧州市",
			dataId: "wuzhou"
		}],
		X: [{
			place: "厦门市",
			dataId: "xiamen"
		},
		{
			place: "西安市",
			dataId: "xian"
		},
		{
			place: "香港特别行政区",
			dataId: "xianggang"
		},
		{
			place: "湘潭市",
			dataId: "xiangtan"
		},
		{
			place: "湘西市",
			dataId: "xiangxi"
		},
		{
			place: "襄阳市",
			dataId: "xiangyang"
		},
		{
			place: "咸宁市",
			dataId: "xianning"
		},
		{
			place: "仙桃市",
			dataId: "xiantao"
		},
		{
			place: "咸阳市",
			dataId: "xianyang"
		},
		{
			place: "孝感市",
			dataId: "xiaogan"
		},
		{
			place: "锡林郭勒盟",
			dataId: "xilinguole"
		},
		{
			place: "兴安市",
			dataId: "xingan"
		},
		{
			place: "邢台市",
			dataId: "xingtai"
		},
		{
			place: "西宁市",
			dataId: "xining"
		},
		{
			place: "新乡市",
			dataId: "xinxiang"
		},
		{
			place: "信阳市",
			dataId: "xinyang"
		},
		{
			place: "新余市",
			dataId: "xinyu"
		},
		{
			place: "忻州市",
			dataId: "xinzhou"
		},
		{
			place: "西双版纳傣族自治州",
			dataId: "xishuangbanna"
		},
		{
			place: "宣城市",
			dataId: "xuancheng"
		},
		{
			place: "许昌市",
			dataId: "xuchang"
		},
		{
			place: "徐州市",
			dataId: "xuzhou"
		}],
		Y: [{
			place: "雅安市",
			dataId: "yaan"
		},
		{
			place: "延安市",
			dataId: "yanan"
		},
		{
			place: "延边市",
			dataId: "yanbian"
		},
		{
			place: "盐城市",
			dataId: "yancheng"
		},
		{
			place: "阳江市",
			dataId: "yangjiang"
		},
		{
			place: "阳泉市",
			dataId: "yangquan"
		},
		{
			place: "扬州市",
			dataId: "yangzhou"
		},
		{
			place: "烟台市",
			dataId: "yantai"
		},
		{
			place: "宜宾市",
			dataId: "yibin"
		},
		{
			place: "宜昌市",
			dataId: "yichang"
		},
		{
			place: "宜春市",
			dataId: "yichun"
		},
		{
			place: "伊春市",
			dataId: "yichun1"
		},
		{
			place: "伊犁市",
			dataId: "yili"
		},
		{
			place: "银川市",
			dataId: "yinchuan"
		},
		{
			place: "营口市",
			dataId: "yingkou"
		},
		{
			place: "鹰潭市",
			dataId: "yingtan"
		},
		{
			place: "益阳市",
			dataId: "yiyang"
		},
		{
			place: "永州市",
			dataId: "yongzhou"
		},
		{
			place: "岳阳市",
			dataId: "yueyang"
		},
		{
			place: "玉林市",
			dataId: "yulin"
		},
		{
			place: "榆林市",
			dataId: "yulin1"
		},
		{
			place: "运城市",
			dataId: "yuncheng"
		},
		{
			place: "云浮市",
			dataId: "yunfu"
		},
		{
			place: "玉树市",
			dataId: "yushu"
		},
		{
			place: "玉溪市",
			dataId: "yuxi"
		}],
		Z: [{
			place: "枣庄市",
			dataId: "zaozhuang"
		},
		{
			place: "张家界市",
			dataId: "zhangjiajie"
		},
		{
			place: "张家口市",
			dataId: "zhangjiakou"
		},
		{
			place: "张掖市",
			dataId: "zhangye"
		},
		{
			place: "漳州市",
			dataId: "zhangzhou"
		},
		{
			place: "湛江市",
			dataId: "zhanjiang"
		},
		{
			place: "肇庆市",
			dataId: "zhaoqing"
		},
		{
			place: "昭通市",
			dataId: "zhaotong"
		},
		{
			place: "郑州市",
			dataId: "zhengzhou"
		},
		{
			place: "镇江市",
			dataId: "zhenjiang"
		},
		{
			place: "中山市",
			dataId: "zhongshan"
		},
		{
			place: "中卫市",
			dataId: "zhongwei"
		},
		{
			place: "周口市",
			dataId: "zhoukou"
		},
		{
			place: "舟山市",
			dataId: "zhoushan"
		},
		{
			place: "珠海市",
			dataId: "zhuhai"
		},
		{
			place: "驻马店市",
			dataId: "zhumadian"
		},
		{
			place: "株洲市",
			dataId: "zhuzhou"
		},
		{
			place: "淄博市",
			dataId: "zibo"
		},
		{
			place: "自贡市",
			dataId: "zigong"
		},
		{
			place: "资阳市",
			dataId: "ziyang"
		},
		{
			place: "遵义市",
			dataId: "zunyi"
		}]
	}
	var KEY = ["A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "W", "X", "Y", "Z"];
	//将每个classlist写到页面中
	for (var i = 0; i < KEY.length; i ++) {
	     $('<div />', {
	        class: 'citylist',
	        click: function() {
	          //alert("点我了~~");
	        }
	      }).appendTo($('.container .city'));	
	}
	for( var i = 0; i < KEY.length; i++ ){
		//生成以每个字母开头的城市列表		
		var classlist_I = $('.container .city .citylist').eq(i);	
		//生成span标签及属性并插入
	 	var span = buildHTML("span", ""+KEY[i]+" ( 按字母排序 )", {//   "+KEY[i]+"
	 		class: "cityletter",		
	        id: KEY[i]+"1"       //"+KEY[i]+"
	    });
		$(classlist_I).append(span);
	}           
	//生成p标签及属性并插入	
	for(var i in city){
			//console.info(i);	
		//alert(i); //A B C……
		for(var j in city[i]){
			//alert(city[i][j]['place']);
			//alert(j) //012……
			var classlist_I = $('#'+i+'1');//  "+i+"							
		 	var p = buildHTML("p", city[i][j]['place'], {
		        dataId: city[i][j]['dataId'],
		    });
			classlist_I.after(p);	
		}
	}
	//选择城市 start
	$('body').on('click', '.citylist p', function() {
		$('#gr_zone_ids').html($(this).html()).attr('dataId', $(this).attr('dataId'));
	});
	$('body').on('click', '.letter a', function() {
		var s = $(this).html();
		//$(window).scrollTop($('#' + s + '1').offset().top-235);
		$('body,html').animate({'scrollTop':$('#' + s + '1').offset().top-235},200);
	});
})
//城市选择--标签生成函数
buildHTML = function(tag, html, attrs) {
  if (typeof (html) != 'string') {
    attrs = html;
    html = null;
  }
  var h = '<' + tag;
  for (attr in attrs) {
    if (attrs[attr] === false) continue;
    h += ' ' + attr + '="' + attrs[attr] + '"';
  }
  return h += html ? ">" + html + "</" + tag + ">" : "/>";
};
/******************************自动获取定位信息***********************************/
function getLocation(){
	if (navigator.geolocation){
		navigator.geolocation.getCurrentPosition(showPosition,showError);
	}else{
		alert("浏览器不支持地理定位。");
	}
}
function showPosition(position){
	$("#latlon").html("纬度:"+position.coords.latitude +'，经度:'+ position.coords.longitude);
	var latlon = position.coords.latitude+','+position.coords.longitude;
	//baidu
	var url = "http://api.map.baidu.com/geocoder/v2/?ak=C93b5178d7a8ebdb830b9b557abce78b&callback=renderReverse&location="+latlon+"&output=json&pois=0";
	$.ajax({ 
		type: "GET", 
		dataType: "jsonp", 
		url: url,
		beforeSend: function(){
			$("#gr_zone_ids").html('正在定位...');
		},
		success: function (json) { 
			if(json.status==0){
				$("#gr_zone_ids").html(json.result.addressComponent.city);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {  
			alert("地址位置获取失败");
		}
	});
}
function showError(error){
	switch(error.code) {
		case error.PERMISSION_DENIED:
			alert("定位失败,用户拒绝请求地理定位");
			break;
		case error.POSITION_UNAVAILABLE:
			alert("定位失败,位置信息是不可用");
			break;
		case error.TIMEOUT:
			alert("定位失败,请求获取用户位置超时");
			break;
		case error.UNKNOWN_ERROR:
			alert("定位失败,定位系统失效");
			break;
    }
}