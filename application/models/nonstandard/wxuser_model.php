<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *  非标准化产品
 */
class Wxuser_model extends CI_Model {
    
     //微信用户表
     private $table_wxuser    =      'h_wxuser';
     
     function __construct(){
        parent::__construct();
        $this->load->database();
     }
     
    /**
     * 校验用户是否存在,存在返回用户id,不存在 提示关注
     * @param  string  oprnid  用户openid
     * @return int     id   微信用户id  
     */
    function check_user($option){
        $sqluser='select wx_id,wx_img,wx_mobile,wx_name,wx_openid,wx_status from '.$this->table_wxuser.
                 ' where wx_openid="'.$option['openid'].'"';
        $query = $this->db->query($sqluser);
        //没有用户
        if ($query->num_rows() <= 0){
            return 0;
        }
        //用户状态不对
        $userdata=$query->result_array(); 
        if($userdata['0']['wx_status'] != 1 && $userdata['0']['wx_status'] != -1){
            return false;
        }
        return $userdata;
    }
    /**
     * 校验用户是否存在,存在返回用户id
     * @param  string  oprnid  用户openid
     * @return int     id   微信用户id  
     */
    function get_user($option){
        $sqluser='select wx_id,wx_img,wx_mobile,wx_name,wx_openid,wx_status from '.$this->table_wxuser.
                 ' where wx_openid="'.$option['openid'].'"';
        $query = $this->db->query($sqluser);
        //没有用户
        if ($query->num_rows() <= 0){
            return 0;
        }
        $userdata=$query->result_array();
        return $userdata;
    }
    /**
     * 个人中心-修改个人资料
     * @param     string    province      省份
     * @param     string    city          市/区
     * @param     string    county        县
     * @param     string    residential   小区
     * @param     string    house_number  楼门号
     * @return    array                   返回结果
     * 
     */
    function  update_userinfo(){
        $city=explode('-',$this->city);
        $data=array(
                'wx_province'=>$city['0'],
                'wx_city'=>$city['1'],
                'wx_county'=>$city['2'],
                'wx_esidential_quarters'=>$this->info,
                'wx_updatetime'=>date('Y-m-d H:i:s'),
        );
        $where=array('wx_id'=>$this->userid);
        $query=$this->db->update($this->table_wxuser,$data,$where);
        if($this->db->affected_rows() == 1 && $query){
            return true;
        }
        return false;  
    }
    /**
     * 个人中心-获取个人资料
     * @param     int    userid   用户id
     * @return    array
     */
    function  get_userinfo(){
        $sql='select wx_province as province,wx_city as city,wx_county as county,
              wx_esidential_quarters as residential,wx_house_number as house_number,
              wx_mobile as mobile from '.$this->table_wxuser.' where wx_id='.
              $_SESSION['userinfo']['user_id'];
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $result=$query->result_array();        
        return $result;
        
    } 
    /**
     * 用户模块--检验手机号码  是否已经被占用
     * @param   int   mobile  手机号码
     * @return  bool  结果     
     */
    function CheckBinding($mobile){
        $sql='select wx_id from '.$this->table_wxuser.' where wx_mobile='.$mobile;
        $result=$this->db->query($sql);
        if($result->num_rows() > 0){
            return false;
        }
        return true;
    }
    
    /**
     * 用户模块-绑定手机号码
     * @param       int     userid     员工id
     * @param       int     mobile     手机号码
     * @param       string  password   密码
     * @param       string  invitation 邀请码
     * @return  绑定成功返回 int   插入的id | 绑定失败 返回 bool false               
     */
    function wxuser_binding_mobile(){
    $promoCode=$this->promoCode;
        $password=hash('sha256',$this->passwd);
        if($promoCode!=''){
        	$promoCode=$this->promoCode;
        }else{
        	$promoCode='';
        }
        $times=strtotime($this->next_month_today(date("Y-m-d")));
        $data=array('wx_mobile'=>$this->mobile,
                    'wx_jointime'=>date('Y-m-d H:i:s'),
                    'wx_regtime'=>date('Y-m-d H:i:s'),
                    'wx_loginip'=>$this->input->ip_address(),
                    'wx_password'=>$password,
        			'transuser_invitation'=>$promoCode,
                    'wx_invitation'=>$this->invitation,
        			'wx_member'=>2,
        			'wx_expire'=>$times
        );
        if (isset($_SESSION['userinfo']['spreadFrom'])&&ctype_alnum($_SESSION['userinfo']['spreadFrom'])
            &&strlen($_SESSION['userinfo']['spreadFrom'])<=7) {
            $data['wx_spreadnum'] = $_SESSION['userinfo']['spreadFrom'];
        }
        $query=$this->db->insert($this->table_wxuser,$data);
        if($query && $this->db->affected_rows() == 1){
            $userid = $this->db->insert_id();
            $_SESSION['userinfo']['useronline']  ='ok';
            $_SESSION['userinfo']['userlogin']   ='ok';
            $_SESSION['userinfo']['user_id']     =$userid;
            $_SESSION['userinfo']['user_name']   ='';
            $_SESSION['userinfo']['user_img']    ='';
            $_SESSION['userinfo']['user_mobile'] =$this->mobile;
            $_SESSION['userinfo']['transuser_invitation'] =$promoCode;
            $_SESSION['userinfo']['user_openid'] ='';
			$_SESSION['userinfo']['user_invitation'] =$this->invitation;
            $this->load->model('shop/flow_model');
            $this->flow_model->isfromSpread('reg',$userid);
            return $userid;
        }
        return false;
    }
    /**
     * 查询用户当前的余额
     * @param     int     userid   用户id
     * @param     string  toall    当参数为值为 toall 返回冻结  余额 总额
     * @return    array|成功时返回，其他返回bool|false
     */
    function GetBalance($userid){
        if(empty($userid) || !is_numeric($userid)){
            return false;
        }
        //获取当前用户的  可用余额 冻结余额
        $sql='select a.wx_balance,a.wx_freeze_balance,b.center_fund,
               b.center_integral from  h_wxuser as a left join  h_wxuser_task 
               as b on a.wx_id=b.wx_id where a.wx_id = '.$userid;        
        $result=$this->db->query($sql);
        if($result->num_rows < 1 ){
            return false;
        }        
        $response=$this->db->fetch_query($result);  
        if(is_array($response)){
            $money['balance']=empty($response['0']['wx_balance']) ? '0.00' : $response['0']['wx_balance']/100;
            $money['freeze_balance']=empty($response['0']['wx_freeze_balance']) ? '0.00' : $response['0']['wx_freeze_balance']/100;
            $money['fund']=$response['0']['center_fund'];
            $money['integral']=$response['0']['center_integral'];
            return $money;
        }       
        
        return false;        
    }
    /**
     * 查询当前用户的手机号码
     * @param    int  wx_id  用户id
     * @return   成功返回 int|绑定手机号码,失败 返回 string | 失败原因
     */
    function  getMobile($userid){
        if(empty($userid) ||  !is_numeric($userid)){
             return '绑定手机号码为空或可是不正确';
        }
        $sql='select wx_mobile from h_wxuser where wx_id='.$userid;
        $result=$this->db->query($sql);
        if($result === false){
            return '出现异常,请稍后再试!';
        }
        if($result->num_rows < 1 ){
            return '出现异常,请稍后再试!';
        }
        $data=$this->db->fetch_query($result);
        if(empty($data['0']['wx_mobile'])){
            return '您还没有绑定手机号码!' ;
        }
        return $data['0']['wx_mobile'];
        
    }
    /**
     * 用户申请提现 
     * @param   int  toall  提现金额
     * @return  成功返回 bool| true ,失败失败返回 bool| false     
     */
    function extract(){
        //查询当前用户余额
        $sql='select wx_id,wx_balance,wx_freeze_balance from  h_wxuser where wx_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query === false  || $query->num_rows  != 1){
            return  false;
        }
        $response=$query->result_array();
        //校验余额是否足够
        if($this->moeny > $response['0']['wx_balance'] ){
            return  false;
        }
        //校验当前用户当天的提现额度是否超限
        $cehck_sql='select sum(expenses_totalfee) as total  from h_bill_expenses 
                    where  user_id='.$this->userid.' and expenses_usertype=1 and 
                    expenses_jointime > '.strtotime(date('Y-m-d')).' and  
                    expenses_jointime < '.strtotime(date('Y-m-d').' 23:59:59');
        $check_query=$this->db->query($cehck_sql);
        $total=$check_query->result_array();
        if($total['0']['total'] >= 1800000){
            return false;
        }
        $this->load->library('hongbao/packet');
        $res_pay=$this->packet->_route('transfers',array('openid'=>$this->openid,'money'=>$this->moeny,'name'=>$this->name));    
        $this->ex_type = 1;
        //校验支付结果
        if($res_pay->return_code == 'FAIL' || $res_pay->result_code == 'FAIL' ){            
            $this->code=-1;
            $this->msg=$res_pay->return_msg.$res_pay->err_code.$res_pay->err_code_des;            
        }
        if($res_pay->result_code == 'SUCCESS' && $res_pay->result_code =='SUCCESS'){
            $this->code=1;
            $this->number=$res_pay->partner_trade_no;
            $this->payment_no=$res_pay->payment_no; 
        }
        return  $this->PayHandle();
    }
    /**
     * 支付宝提现
     */
    function zfbextract(){
        //查询当前用户余额
        $sql='select wx_id,wx_balance,wx_freeze_balance from  h_wxuser where wx_id='.$this->userid;
        $query=$this->db->query($sql);
        if($query === false  || $query->num_rows  != 1){
            return  false;
        }
        $response=$query->result_array();
        $this->moeny = ($this->pic)*100;//支付宝是元为单位
        //校验余额是否足够
        if($this->moeny > $response['0']['wx_balance'] ){
            return  false;
        }
        //校验当前用户当天的提现额度是否超限
        $cehck_sql='select sum(expenses_totalfee) as total  from h_bill_expenses 
                    where  user_id='.$this->userid.' and expenses_usertype=1 and 
                    expenses_jointime > '.strtotime(date('Y-m-d')).' and  
                    expenses_jointime < '.strtotime(date('Y-m-d').' 23:59:59');
        $check_query=$this->db->query($cehck_sql);
        $total=$check_query->result_array();
        if($total['0']['total'] >= 1800000){
            return false;
        }
        $this->ex_type = 2;
        $this->load->library('zhifubao/zhifubao.php');
        $this->zhifubao->out_trade_no=$this->out_trade_no;
        $this->zhifubao->userinfo = $this->account;
        $this->zhifubao->total_amount = $this->pic;
        $this->zhifubao->userRealname = $this->name;
        $this->zhifubao->config=$this->config->item('zhifubao_attr');
        $result = $this->zhifubao->transfer();
        if (!empty($result->code)&&$result->code == 10000) {
            $this->code=1;
            $this->number=$result->out_biz_no;
            $this->payment_no=$result->order_id; 
        }else{
            $this->code=-1;
            $this->msg=$result->sub_msg.$result->code.$result->sub_code; 
        }
        return  $this->PayHandle();
    }
    /**
     * 处理支付结果
     * @param    int     code        状态
     * @param    string  msg         提示信息
     * @param    string  number      编号          
     * @param    string  payment_no  微信流水编号
     * @return   成功返回  bool true |失败 返回 bool false 失败原因
     */
    function  PayHandle(){
        //提现出现异常
        if($this->code < 0){
            $data=array('log_type'=>'15','log_user'=>$this->userid,
             'log_name'=>'用户提现出现异常','log_content'=>$this->msg,'log_jointime'=>time());
            $query=$this->db->insert('h_system_log',$data);
            $row=$this->db->affected_rows();
            if($query === true && $row == 1){
                return false;
            }
            return false;
        }
        //提现成功
        if($this->code > 0){
            $logdata=array('expenses_number'=>$this->number,'expenses_username'=>$this->name,'user_id'=>$this->userid,
                    'expenses_usertype'=>1,'expenses_type'=>$this->ex_type,'expenses_payid'=>$this->payment_no,
                    'expenses_totalfee'=>$this->moeny ,'expenses_account'=>$this->account,'expenses_jointime'=>time());
            $sql='update h_wxuser set  wx_balance=wx_balance-'.$this->moeny.' 
                  where wx_id='.$this->userid;            
            $add_res=$this->db->insert('h_bill_expenses',$logdata);
            $add_row=$this->db->affected_rows();
            $edit_res=$this->db->query($sql);
            $edit_row=$this->db->affected_rows();
            if($add_res === true  &&  $add_row == 1 && $edit_res === true && 
                   $edit_row == 1){
                return  true;
            }
            return false;            
        }
    }
    /**
     * 校验当前用户是否满足提现要求
     * @param   int   userid  用户id
     * @return  符合      bool true | 不符合 bool false
     */
    function checkextract(){
        //校验当前用户 当天提现次数是否超过限制
        $sql='select user_id,expenses_jointime from h_bill_expenses where 
               expenses_jointime > '.strtotime(date('Y-m-d',time())).' 
                and expenses_jointime < '.time().' and user_id="'.$this->userid.'"
                  order by expenses_jointime desc';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return true;
        }        
        if($query->num_rows >=3){
            return false;
        }  
        $result=$query->result_array(); 
        $checktime=$result['0']['expenses_jointime'] + 60;
        if($checktime > time()){
            return  false;
        }
        return true;
    }
    /**
     * 根据账号查询用户是否存在 密码是否相等
     * @param   int     mobile  手机号码
     * @param   string  pwd     密码
     * @return  修改成功返回bool true| 修改失败返回 bool false
     */
    function userAuth(){
        $sql='select wx_id,wx_name,wx_mobile,wx_password,wx_openid,wx_img,wx_invitation,wx_expire from 
             h_wxuser where wx_mobile='.$this->name.' and  wx_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {//没有此用户，尝试去寄售通获取账号信息
            $result = $this->GetjstUser('http://platform.91jst.com/Consign/Server/User/public/usr/verifybytel.php?tel='.$this->name
                .'&pwd='.$this->pwd.'&secret=abcdefgABCDEFG0987654321');
            if ($result==false) {//没有的话去寄售商城获取账户
                $return = $this->GetjstUser('http://www.91jst.com/new/verifyshopbytel.php?tel='.$this->name
                    .'&pwd='.$this->pwd.'&secret=abcdefg');
                if (!$return) {
                    return false;
                }
                return true;
            }
            return true;
        }
        $password = hash('sha256',$this->pwd);
        $result = $result->result_array();
        if ($result['0']['wx_password']!=$password) {
            return false;
        }
        $_SESSION['userinfo']['useronline']  ='ok';
        $_SESSION['userinfo']['userlogin']   ='ok';
        $_SESSION['userinfo']['user_id']     =$result['0']['wx_id'];
        $_SESSION['userinfo']['user_name']   =$result['0']['wx_name'];
        $_SESSION['userinfo']['user_img']    =$result['0']['wx_img'];
        $_SESSION['userinfo']['user_mobile'] =$result['0']['wx_mobile'];
        $_SESSION['userinfo']['user_openid'] =$result['0']['wx_openid'];
		$_SESSION['userinfo']['user_expire'] =$result['0']['wx_expire'];
		$_SESSION['userinfo']['user_invitation'] =$result['0']['wx_invitation'];
        $data=array(
                'wx_logintime'=>date('Y-m-d H:i:s'),
                'wx_loginip'=>$this->input->ip_address()
        );
        $where=array(
                'wx_id'=>$result['0']['wx_id']
        );
        $query=$this->db->update('h_wxuser',$data,$where);
        return true;
    }
    /**
     * 获取寄售通的账户信息，并且插入信息
     * @param       url       string         要请求的链接 
     */
    function GetjstUser($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data=json_decode($result,true);
        if ($data['result']==0) {//成功则插入账户信息
            $this->passwd = $this->pwd;
            $this->mobile = $this->name;
            $this->invitation = '';
            $result = $this->wxuser_binding_mobile();
            if ($result==false) {
                return false;
            }
            $this->load->model('task/user_model');
            $this->user_model->is_have_user($result);
            return true;
        }else{//用户密码错误
            return false;
        }
    }
    /**
     * 修改密码，通知寄售通
     * @param    $url       链接的地址
     */
    function noticJstChange($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data=json_decode($result,true);
    }
    /**
     * 根据账号查询用户是否存在  并返回信息
     * @param   int     mobile  手机号码
     * @param   string  pwd     密码
     * @return  修改成功返回用户信息  array| 修改失败返回 bool false
     */
    function getUserInfo(){
        $sql='select wx_name as nick,wx_mobile as tel,wx_img as head,wx_password from 
             h_wxuser where wx_mobile='.$this->name;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $password = hash('sha256',$this->pwd);
        $result = $result->result_array();
        if ($result['0']['wx_password']!=$password) {
            return 'pwerror';
        }
        return $result['0'];
    }
    /**
     * 绑定微信号
     * @param       string      openid      要绑定的微信号
     * @param       array       response    用户的信息
     * @param       int         delop       是否删除原理的openid
     */
    function bindwx($openid,$response,$delop){
        if (!is_numeric($userid=$_SESSION['userinfo']['user_id'])) {
            return false;
        }
        $response['nickname']=Universal::filter($response['nickname']);
        $response['sex']=Universal::filter($response['sex']);
        $response['headimgurl']=Universal::filter($response['headimgurl']);
        $response['province']=Universal::filter($response['province']);
        $response['country']=Universal::filter($response['country']);
        $update = array(
            'wx_name'=>$response['nickname'],
            'wx_openid'=>$openid,
            'wx_sex'=>$response['sex'],
            'wx_img'=>$response['headimgurl'],
            'wx_province'=>$response['province'],
            'wx_county'=>$response['country'],
            'wx_updatetime'=>date('Y-m-d H:i:s'),
        );
        $this->db->trans_begin();
        if ($delop==1) {
            $this->db->delete('h_wxuser',array('wx_openid'=>$openid));
            if ($this->db->affected_rows()!=1) {
                $this->db->trans_rollback();
                return false;
            }
        }
        $result = $this->db->update('h_wxuser',$update,array('wx_id'=>$userid,'wx_openid'=>''));
        if ($result==false||$this->db->affected_rows()!=1||$this->db->trans_status() != true) {
            $this->db->trans_rollback();
            return false;
        }
        //$openid='o9nlJt2dHqi7vsNZKmPrXE5sAIz8';
        $openid=$_SESSION['userinfo']['user_openid'];
        $this->load->model('common/wxcode_model');
        if (isset($openid)&&$openid!='') {
	        $sendtext='{"touser":"%s", "msgtype":"text","text":{"content":"恭喜您成为体验会员,有效期至'.date("Y-m-d ", $wx_expire).',如果疑问,请咨询客服"}}';
	        $content = sprintf($sendtext,$openid);
	        $response_wx=$this->wxcode_model->sendmessage($content);
	        var_dump($response_wx);exit;
        }
        $this->db->trans_commit();
        $_SESSION['userinfo']['user_openid'] =$openid;
        $_SESSION['userinfo']['user_img'] = $response['headimgurl'];
        $_SESSION['userinfo']['user_name'] = $response['nickname'];
        return true;
    }
    /**
     * 修改密码
     */
    function changepwd(){
        $password=hash('sha256',$this->passwd);
        $update = array(
            'wx_password' => $password,
            'wx_updatetime' => date('Y-m-d H:i:s'),
        );
        $where=array('wx_mobile'=>$this->mobile);
        $query=$this->db->update($this->table_wxuser,$update,$where);
        if($this->db->affected_rows() == 1 && $query){
            return true;
        }
        return false; 
    }
    /**
     * 获取下个月最后一天及下个月的总天数
     */
    function getNextMonthEndDate($date){
    	$firstday = date('Y-m-01', strtotime($date));
    	$lastday = date('Y-m-d', strtotime("$firstday +2 month -1 day"));
    	return  $lastday;
    }
    /**
     * 获取下个月的今天  注册会员免费体验一个月的会员
     */
    function next_month_today($date){
    	//获取今天是一个月中的第多少天
    	$current_month_t =  date("t", strtotime($date));
    	$current_month_d= date("d", strtotime($date));
    	$current_month_m= date("m", strtotime($date));
    	 
    	//获取下个月最后一天及下个月的总天数
    	$next_month_end=$this->getNextMonthEndDate($date);
    	$next_month_t =  date("t", strtotime($next_month_end));
    	 
    	$returnDate='';
    	if($current_month_d==$current_month_t){//月末
    		//获取下个月的月末
    		$returnDate=$next_month_end;
    	}else{//非月末
    		//获取下个月的今天
    		if($current_month_d>$next_month_t){ //如 01-30，二月没有今天,直接返回2月最后一天
    			$returnDate=$next_month_end;
    		}else{
    			$returnDate=date("Y-m", strtotime($next_month_end))."-".$current_month_d;
    		}
    	}
    	return $returnDate;
    }
}
/* End of file Wxuser_model.php */
/* Location: ./application/models/nonstandard/Wxuser_model.php */