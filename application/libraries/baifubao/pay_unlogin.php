<?php
/**
 * 这个是调用bfb_sdk里生成百付宝即时到账支付接口URL(不需要登录)的DEMO
 *
 * 字符编码转换，百付宝默认的编码是GBK，商户网页的编码如果不是，请转码。涉及到中文的字段请参见接口文档
 * 步骤：
 * 1. URL转码
 * 2. 字符编码转码，转成GBK
 * 
 * $good_name = iconv("UTF-8", "GBK", urldecode($good_name));
 * $good_desc = iconv("UTF-8", "GBK", urldecode($good_desc));
 * 
 *
 */
if (!defined("BFB_SDK_ROOT"))
{
	define("BFB_SDK_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
require_once(BFB_SDK_ROOT . 'bfb_sdk.php');
require_once(BFB_SDK_ROOT . 'bfb_pay.cfg.php');


class Pay_unlogin{
    //订单创建时间         必填    
    public  $order_create_time = ' ' ;
    //交易的超时时间       不得早于订单创建的时间
    public $expire_time = '';
    //订单号，商户须保证订单号在商户系统内部唯一  必填  长度   20
    public $order_no ='  '; 
    //商品分类号   取值由钱包系统分配     
    public $goods_category = ' ' ;
    //商品名称     必填   不超过128个字符或64个汉字  
    public $good_name = ' ';
    //订单简介//不超过255个字符或127个汉字
    public $good_desc = '';
    //商品在商户网站上的URL。  长度  255 
    public $goods_url = ''; 
    //商品单价，以分为单位 以分为单位  非负整数
    public $unit_amount = '';
    //商品数量 非负整数
    public $unit_count = '';
    //运费 非负整数
    public $transport_amount = '';
    //总金额，以分为单位 非负整数  必填   非负证书
    public $total_amount = '';
    //买家在商户网站的用户名 //不超过64字符或32个汉字
    public $buyer_sp_username = '';  
    //百度钱包主动通知商户支付结果的URL  必填  长度 255
    public $return_url = '';
    //用户点击该URL可以返回到商户网站；该URL也可以起到通知支付结果的作用
    public $page_url = '';
    //默认支付方式
    public $pay_type = '';
    //网银支付或银行网关支付时，默认银行的编码
    public $bank_no = '';
    //回收商编号
    public $sp_uno = '';
    //商户自定义数据  //不超过255个字符
    public $extra = '';
    //商户定制服务字段
    public $sp_pass_through = "%7B%22offline_pay%22%3A1%7D"; 
    //统一下单参数
    public $order_params='';
    //百付宝  sdk 对对象
    public $bfb_sdk='';
    
    //初始化统一下单参数
    function __construct(){
         $this->order_create_time=date("YmdHis");
         $this->expire_time=date('YmdHis', strtotime('+2 day'));
         $this->bfb_sdk = new bfb_sdk();
         $this->order_params = array (
                 'service_code' => sp_conf::BFB_PAY_INTERFACE_SERVICE_ID,//表示即时到帐支付
                 'sp_no' => sp_conf::SP_NO,  //百度钱包商户号
                 'order_create_time' => $this->order_create_time,
                 'goods_category' => $this->goods_category,
                 'goods_url' => $this->goods_url,
                 'unit_amount' => $this->unit_amount,
                 'unit_count' => $this->unit_count,
                 'transport_amount' => $this->transport_amount,
                 'currency' => sp_conf::BFB_INTERFACE_CURRENTCY,
                 'return_url' => 'http://test.recytl.com/nonstandard/systemp/bfb',
                 'pay_type' => 2,
                 'bank_no' => $this->bank_no,
                 'expire_time' => $this->expire_time,
                 'input_charset' => sp_conf::BFB_INTERFACE_ENCODING,
                 'version' => sp_conf::BFB_INTERFACE_VERSION,
                 'sign_method' => sp_conf::SIGN_METHOD_MD5,
                 'sp_pass_through' =>$this->sp_pass_through,
                 'extra' =>$this->extra
         );
    }
    // 名称 金额 回调地址
    
    
    /**
     * 百度钱包  生成订单信息
     * @param     int(20)    order_no        订单编号
     * @param     string     goods_name      商品名称
     * @param     string     goods_desc      商品简介
     * @param     int        total_amount    商品金额
     * @param     string     buyer_sp_username  用户在系统中的用户名
     * 
     */
    function  create_orderinfo($type,$order){
        //校验参数是否为空
        if(empty($order['order_no']) || empty($order['goods_name'])  ||
                empty($order['goods_desc']) || empty($order['total_amount']) || 
                empty($order['buyer_sp_username'])){
            return false;
        }
        //校验参数格式是否正确
        if(!is_numeric($order['order_no']) || !is_numeric($order['total_amount']) || $order['total_amount']  < 1){
            return false;
        }
        $this->order_no=$order['order_no'];
        $this->total_amount=$order['total_amount'];
        //过滤 字符串中的部分有特殊字符
        $filter=array( "/",",",":","<",">","(",")");
        $reolace=array(' ',' ',' ',' ',' ',' ',' ');
        $this->goods_name=str_replace($filter,$reolace,$order['goods_name']);
        $this->goods_desc=str_replace($filter,$reolace,$order['goods_desc']);
        $this->buyer_sp_username=str_replace($filter,$reolace,$order['buyer_sp_username']);
        $this->order_params['order_no']=$this->order_no;
        $this->order_params['buyer_sp_username'] = iconv("UTF-8", "GBK", urldecode($this->buyer_sp_username));
        $this->order_params['total_amount'] =$this->total_amount*100;
        $this->order_params['goods_name'] =iconv("UTF-8", "GBK", urldecode($this->goods_name));
        $this->order_params['goods_desc'] = iconv("UTF-8", "GBK", urldecode($this->goods_desc));
        $order_url = $this->bfb_sdk->create_baifubao_pay_order_url($this->order_params,sp_conf::BFB_PAY_DIRECT_NO_LOGIN_URL);
        return str_replace(array(sp_conf::BFB_PAY_DIRECT_NO_LOGIN_URL,'?'),array('',''),$order_url);     
    }
    /**
     * 根据百付宝订单编号 查询 支付结果
     * @param   int      order_no   20  订单编号
     * @return  array                   查询结果
     */
    function  query_order($number){
        if(empty($number)){
            return false;
        }        
        if(!is_numeric($number)){
            return false;
        }
        $content = $this->bfb_sdk->query_baifubao_pay_result_by_order_no($number);
        if($content === false){
            return false;
        }        
        return $content;
    }
    
    
    
    
}










?>