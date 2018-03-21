<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Flow_model extends CI_Model {
    function  __construct(){
          parent::__construct();
          $this->load->database();    
    }
    /**
     * 搜索商品
     * @param        int        text        搜索商品文字
     * @return       array      return      返回商品信息
     */
	function seachtext($ar_text){
		$where = '';
        foreach ($ar_text as $key => $val) {
            $where .= ' and goods_name like "%'.$val.'%" ';
        }
        $sql = 'select goods_id as id,goods_typeid as tyid,goods_name as name,goods_img as img,goods_opri 
                as opri,goods_ppri as ppri,goods_integral as integral,goods_property as property,goods_sellnum as selln from 
                h_shop_goods where goods_status=1 '.$where;
        $result=$this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        return $return;
	}
	/**
	 * 获取热销商品
     * @return       array      return      返回商品信息
	 */
	function gethotshop(){
		$sql = 'select goods_id as id,goods_name as name from h_shop_goods where goods_status=1 and goods_property like "{\"type\":\"1\"%" limit 3';
        $result=$this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        return $return;
	}
    /**
     * 获取推广商品
     */
    function ProGoods(){
        $sql = 'select goods_id as id,goods_typeid as tid,goods_name as name,goods_img as img,goods_wxshare as shareinfo,
                goods_number as number,goods_opri as opri,goods_ppri as ppri from h_shop_goods where (goods_pshow=3 or goods_pshow=2) and goods_status=1 and goods_number>0 order by goods_sort desc';
        $result =$this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        return $return;
    }
    /**
     * 分享更新后增加分享的次数
     * @param     int     tid          获取的文章id
     * @return       array      返回数组数据
     */
    function addshare($tid){
        $sql = 'update h_shop_goods set goods_sharenum=goods_sharenum+1,goods_updatetime='.time().' where goods_id='.$tid.' and goods_status=1';
        $result = $this->db->query($sql);
        if ($this->db->affected_rows<1 || $result==false) {
            return false;
        }
        return true;
    }
    /**
     * 判断用户电话号码是否和充值的合适
     * @param        int        mobile       电话号码
     * @param        int        where        运营商编号
     * @return       bool       匹配正确返回true|错误返回false
     */
    function mobisrig($mobile,$where){
        $l_mobile = substr($mobile,0,3);
        $this->config->load('mobile',true);//配置项加载
        $operators = $this->config->item('mobile');
        switch ($where) {
            case '1':
                $re = (in_array($l_mobile, $operators['mobile_operators']['corporation']))?true:false;
                break;
            case '2':
                $re = (in_array($l_mobile, $operators['mobile_operators']['unicom']))?true:false;
                break;
            case '3':
                $re = (in_array($l_mobile, $operators['mobile_operators']['telecom']))?true:false;
                break;
            default:
                return false;
                break;
        }
        return $re;
    }
    /**
     * 给用户充值
     * @param        string       fm        流量
     * @param        int          content   电话号码
     * @param        int          number    订单号
     */
    function mobilereg($fm,$content,$number){
        if(!is_numeric($content)){
            return false;
        }
        $this->config->load('mobile',true);//配置项加载
        $info = $this->config->item('mobile');
        $toke = $info['bitefeng']['toke'];
        $corpid = $info['bitefeng']['corpid'];
        $client_order_id = $number;
        $timestamp = time();
        $nonce = $this->createNonceStr();
        $this->mobile = $content;
        $mobile = $content;
        $amount = $fm;
        $signature = sha1('{"token":"'.$toke.'","timestamp":"'.$timestamp.'","nonce":"'.$nonce.'","amount":"'.$amount.'","mobile":"'.$mobile.'","client_order_id":"'.$client_order_id.'"}');
        $url = $info['bitefeng']['url'].'?corpid='.$corpid.'&timestamp='.$timestamp.'&nonce='.$nonce.'&mobile='.$mobile.'&amount='.$amount.'&client_order_id='.$client_order_id.'&signature='.$signature;
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        $info=curl_exec($curl);
        curl_close($curl);
        $this->jsoninfo=json_decode($info,true);
        return $this->jsoninfo;
    }
    /**
     * 更新流量订单数据
     */
    function uporder($gid,$number,$extendnum){
        //更新商品库存
        $shop='update h_shop_goods set goods_number=goods_number-1,goods_sellnum=goods_sellnum+1 where goods_id='.$gid;
        $this->db->query($shop);
        if($this->db->affected_rows() != 1){
            return  false;
        }
        //更新成交记录中的 状态
        $time = time();
        $jinfo = $this->jsoninfo;
        if($jinfo['status']=='E10000'){
            $cinfo = $jinfo['order_id'].','.$jinfo['status'].','.$jinfo['desc'];
        }else{
            $cinfo = $jinfo['status'].','.$jinfo['desc'];
        }
        $record=array('record_updatetime'=>$time,'record_status'=>1,'record_content'=> $this->mobile.','.$cinfo,
                'record_time'=>$time,'record_invitation'=>$extendnum);
        $this->db->update('h_shop_record',$record,array('record_payid'=>$number));
        if($this->db->affected_rows() != 1){
            $this->msg='交易记录出现异常!';
            return false;
        }
        return true;
    }
    /**
     * 功能描述:获取随机字符串
     * 参数描述:$length 随机字符串长度
     */
    function createNonceStr($length = 7) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    /**
     * 检查订单
     */
    function getOrder($number){
        $sql = 'select record_status as status,record_name as name,record_id as id,record_payid as shopnum,record_goodid as goodsid,record_price as price, 
        		record_invitation as invita from h_shop_record where record_payid='.$number;
        $result = $this->db->query($sql);    
        if ($result->num_rows<1) {
            return false;
        }
        $return = $result->result_array();
        return $return;
    }
    /**
     * 检查是否从推广页面进来
     * @param   string  type    要请求接口的类型  默认为enter ，reg 为注册
     * @param   int     id      用户的唯一标识  当填写了type参数为reg使用此参数
     */
    function isfromSpread($type='enter',$id=0){
        if (isset($_SESSION['userinfo']['spreadRand'])) {
            $param = array(
                'config' => array('url' => 'http://60.205.142.49/Analysis/Server/Analysis/www/json/Gateway.php'), 
                'dataName' => 'smartplatform', 
                'key' => 'abcdefgABCDEFG0987654321'
            );
            $this->load->library('spread/AnalysisServer.php',$param,'analysisServer');
            if ($type=='enter') {
                $addResult = $this->analysisServer->enterTrack($_SESSION['userinfo']['spreadRand'],'HST');
            }elseif($type=='reg'&&$id!=0){
                $registResult = $this->analysisServer->registTrack($_SESSION['userinfo']['spreadRand'], 'HST',$id);
            }
            
        }
    }
	 /**
     * 通过物品编号查询在h_shop_bonus会员系统 通花商城奖金设置表 是否有设置
     * @param shopid  物品订单编号 int
     */
    function getBonus($shopid){
    	$sql= 'select shop_goodid as goodid,shop_type as type,shop_value as value from h_shop_bonus 
    			where shop_status = 1 and shop_goodid ="'.$shopid.'"';
    	$result = $this->db->query($sql);
    	if ($result->num_rows<=0) {
    		return '';
    	}else{
    		$return = $result->result_array();
    		return $return;
    	}
    }
    
    /**
     * 添加该笔订单中邀请者所获取的奖金 
     */
    function addGoodDeal(){
    	if($this->fixed==1){
    		if($this->price>0){
    			$bonus= round($this->price*$this->value*100/100,2);
    		}else{
    			$bonus=0;
    		}
    	}else if($this->fixed==2){
    		$bonus=$this->value;
    	}
    	$data=array(
    		'gdeal_source'=>1,
    		'gdeal_goodsid'=>$this->goodsid,
    		'gdeal_goodid'=>$this->goodid,
    		'gdeal_goodname'=>$this->goodname,
    		'gdeal_method'=>2,
    		'gdeal_fixed'=>$this->fixed,
    		'gdeal_bonus'=>$bonus,
    		'gdeal_userid'=>$this->userid,
    		'gdeal_invitation'=>$this->invitation,
    		'gdeal_jointime'=>time(),
    		'gdeal_status'=>1
    	);
    	$sql=$this->db->insert('h_goodsdeal_bonus',$data);
    	if($sql){
    		//邀请者获取奖金收益时 发送推行提醒
    		$open_sql='select a.wx_id as id,a.wx_mobile as mobile,a.wx_openid as
    						openid from h_wxuser a left join h_wxuser_task b
    					on a.wx_id=b.wx_id where b.center_extend_num="'.$this->invitation.'"';
    		$open_query=$this->db->query($open_sql);
    		$open_result=$open_query->result_array();
    		//$openid=$open_result['0']['openid'];
    		$openid=$_SESSION['userinfo']['user_openid'];
    		$this->load->model('common/wxcode_model');
    		if (isset($openid)&&$openid!='') {
    			$temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2fnonstandard/mybonus/mybonusList&response_type=code&scope=snsapi_base&state=#wechat_redirect';
    			$sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的奖金已经发放到你的奖金里面",
                        "description":"点此进入我的奖金页面",
                        "url":"%s", "picurl":""}]}}';
    			$content = sprintf($sendtext,$openid,$temp_url);
    	 		$response_wx=$this->wxcode_model->sendmessage($content);
    		}
    		return true;
    	}else{
    		return false;
    	}
    }
    /**
     * 通过邀请码查询该用户是否是会员
     */
   /*  function selectMem($order_invi){
        //查询该邀请码的用户是否是会员
        $mem_sql='select a.wx_member as mem,a.wx_expire as expire from h_wxuser a left join h_wxuser_task as b on a.wx_id=b.wx_id where b.center_extend_num='.$order_invi;
        $mem_query=$this->db->query($mem_sql);
        $mem_result=$mem_query->result_array();
        if($mem_result['0']['mem']==0 || empty($mem_result['0']['mem']) && ($mem_result['0']['expire']<time() || empty($mem_result['0']['expire']))){
            $bonus_num = 1;
        }else{
            $bonus_num = 0;
        }
        return $bonus_num;
    } */
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}