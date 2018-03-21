<?php
/*
 *  校验用户模块
 *  
 */
class Userauth_model extends CI_Model{
    //初始化
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    /**
     * 用户登录模块
     * @param   string  method   登录方式(微信中   标示wx,浏览器中  标示ordinary)
     * @param   string  keyword  账号    wx openid   ordinary  手机号码
     * @return  bool    结果
     */
    function UserLogin($method,$name,$pwd=''){
        //判断那个登录方式
        switch ($method){
            case 'wx':
                //在微信公众号中
                $bool=$this->WxCheck($name);
                break;
            case 'ordinary': 
                //正常登录进入系统
                $bool=$this->IsLogin();               
                break;
        }        
        return $bool;
    }
    /**
     * 校验是否是合法用户
     * @param    string    code   微信链接中code标示
     */
    function  WxCheck($code){
        exit();
        //开发测试  
        if($code == 'a'){
            $openid=$this->input->get('openid');
        }else{
            //获取openid
            $openid=$this->wxcode_model->getOpenid($code);
        }
        //没有获取到openid
        if(empty($openid)){
            header('Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/nonstandard/system/welcome&response_type=code&scope=snsapi_base&state=#wechat_redirect');
            exit;
        }
        $this->load->model('nonstandard/wxuser_model');
        //校验用户是否存在
        $userid=$this->wxuser_model->check_user(array('openid'=>$openid)); 
        //用户状态不低
        if($userid === false){
            return false;
        }   
        //获取用户详细信息
        $response=$this->wxcode_model->userinfo($openid);
        //校验 当前用户是否关注
        $_SESSION['userinfo']['subscribe'] =$response['subscribe'];
        //校验当前用户是否是关注用户
        if(!empty($response['subscribe'])){
            $userdata=array(
                'wx_name'=>$response['nickname'],
                'wx_openid'=>$response['openid'],
                'wx_sex'=>$response['sex'],
                'wx_img'=>$response['headimgurl'],
                'wx_province'=>$response['province'],
                'wx_county'=>$response['country'],
                'wx_jointime'=>date('Y-m-d H:i:s'),
                'wx_logintime'=>date('Y-m-d H:i:s'),
                'wx_loginip'=>$this->input->ip_address(),
                'wx_status'=>1
            );
        }
        //校验当前用户不在的时候
        if(empty($userid) && $response['subscribe'] == 1){
            //添加用户
            $this->db->insert('h_wxuser',$userdata);
            $_SESSION['userinfo']['useronline']  = 'ok';
            $_SESSION['userinfo']['userlogin']   = 'ok';
            $_SESSION['userinfo']['user_mobile'] = '';
            $_SESSION['userinfo']['user_name']   = $response['nickname'];
            $_SESSION['userinfo']['user_img']    = $response['headimgurl'];
            $_SESSION['userinfo']['user_openid'] = $response['openid'];
            $_SESSION['userinfo']['user_id']=$this->db->insert_id();
            $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],110);//设置微信分组 未注册组
            return true;
        }
        //当存在用户
        if(is_array($userid)){
            if(!empty($response['subscribe'])){
                unset($userdata['wx_jointime']);
                unset($userdata['wx_province']);
                unset($userdata['wx_county']);
                unset($userdata['wx_openid']);
                $this->db->update('h_wxuser',$userdata,array('wx_id'=>$userid['0']['wx_id']));
            }
            $_SESSION['userinfo']['useronline']  ='ok';
            $_SESSION['userinfo']['userlogin']   ='ok';
            $_SESSION['userinfo']['user_id']     =$userid['0']['wx_id'];
            $_SESSION['userinfo']['user_name']   =$userid['0']['wx_name'];
            $_SESSION['userinfo']['user_img']    =$userid['0']['wx_img'];
            $_SESSION['userinfo']['user_mobile'] =$userid['0']['wx_mobile'];
            $_SESSION['userinfo']['user_openid'] =$userid['0']['wx_openid'];
            $this->wxcode_model->setPacket($_SESSION['userinfo']['user_openid'],110);//设置微信分组 未注册组
            return true;
        }
        return false;
    }
    /**
     * 添加未关注用户的openid，并且登录wx
     * @param    string    code   微信链接中code标示
     */
    function wxLogin($code,$fromUrl=''){
        if($code == 'a'){
            $openid=$this->input->get('openid');
        }else{
            if ($code==false) {
                header('Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com'.$fromUrl.'&response_type=code&scope=snsapi_base&state=#wechat_redirect');
                exit;
            }else{
                $openid=$this->wxcode_model->getOpenid($code);
            }
        }
        //没有获取到openid
        if(empty($openid)){
            $_SESSION['userid']['Login_openid'] = '';
            return false;
        }else{
            $_SESSION['userinfo']['Login_openid'] = $openid;
        }
        $this->load->model('nonstandard/wxuser_model');
        //校验用户是否存在
        $userid=$this->wxuser_model->get_user(array('openid'=>$openid));
        if($userid==0 || empty($userid['0']['wx_mobile'])){//没有取得或者用户没有注册
            $this->wxcode_model->setPacket($_SESSION['userinfo']['Login_openid'],110);//设置微信分组 未注册组
            return true;
        }elseif(is_array($userid)&&!empty($userid['0']['wx_mobile'])){
            $userdata=array(
                'wx_logintime'=>date('Y-m-d H:i:s'),
                'wx_loginip'=>$this->input->ip_address(),
            );
            $this->db->update('h_wxuser',$userdata,array('wx_id'=>$userid['0']['wx_id']));
            //保存用户信息
            $_SESSION['userinfo']['useronline']  ='ok';
            $_SESSION['userinfo']['userlogin']   ='ok';
            $_SESSION['userinfo']['user_mobile'] =$userid['0']['wx_mobile'];
            $_SESSION['userinfo']['user_openid'] =$userid['0']['wx_openid'];
            $_SESSION['userinfo']['user_id']     =$userid['0']['wx_id'];
            $_SESSION['userinfo']['user_name']   =$userid['0']['wx_name'];
            $_SESSION['userinfo']['user_img']    =$userid['0']['wx_img'];
            return true;
        }
        return false;
    }
    /**
     * 用户权限模块-----用户权限校验
     * @param   int     level    校验级别
     * @param   data    userdata 用户信息  
     * @return  bool             校验结果
     */
    function UserCheck($level,$userdata,$isAjax=false){
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        if(!isset($_SESSION['userinfo']['user_mobile'])||empty($_SESSION['userinfo']['user_mobile'])
            ||!isset($_SESSION['userinfo']['user_id'])){
            $this->load->library('user_agent');
            $user_agent= $this->agent->agent_string();
            if (strpos($user_agent, 'MicroMessenger')){
                $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/nonstandard/system/welcome&response_type=code&scope=snsapi_base&state=#wechat_redirect';
            }else{
                $url='http://'.$_SERVER['HTTP_HOST'].'/index.php/nonstandard/system/Login';
            }
            $method=$this->input->is_ajax_request();
            if($method&&$isAjax==true){
                $this->url=$url;
                return false;
           }
           header("Location:".$url);
           exit();
        }
        switch ($level){
            //校验等级为1 校验用户是否在本站
            case 1:
                 $bool=$this->IsUserLogin($userdata['userinfo']['useronline']);                 
                 break;
            //校验等级为2 校验用户在本站 且为登陆状态    
            case 2: 
                 $login=$this->IsUserLogin($userdata['userinfo']['userlogin']);
                break;
        }
    }    
    /**
     * 用户权限模块-----校验用户是否是存在
     * @param   string     sign   是否存在标示
     * @return  bool       校验结果
     */
    function IsUserOnline($sign){
        //标示是否被初始化
        if(!isset($sign)){
            $response=array('status'=>$this->config->item('request_power_error'),
                        'msg'=>$this->lang->line('common_user_online'));
            echo json_encode($response);exit;
        }
        //标示是否是预设标示
        if($sign  !=  'ok'){
             $response=array('status'=>$this->config->item('request_power_error'),
                        'msg'=>$this->lang->line('common_user_online'));
            echo json_encode($response);exit;
        }
    }
    /**
     * 用户权限模块----校验用户是否登陆
     * @param     string    sign   是否登陆标示
     * @param     bool             校验结果 
     */
    function IsUserLogin($sign){
        $this->IsUserOnline($sign);
        //校验登陆标示是否被初化
        if(!isset($sign)){
            $response=array('status'=>$this->config->item('request_power_error'),
                       'msg'=>$this->lang->line('common_user_login')      
                     );
            echo json_encode($response);exit;
        }
        //校验用户是否登陆
        if($sign != 'ok'){
            $response=array('status'=>$this->config->item('request_power_error'),
                       'msg'=>$this->lang->line('common_user_login')      
                     );
            echo json_encode($response);exit;
        }
    }
    /**
     * 
     * 用户权限模块--校验是否绑定手机
     */
    function IsBindIphone(){
        //校验用户是否绑定手机号码
        if(empty($_SESSION['userinfo']['user_mobile'])){
            $this->load->view('nonstandard/mobile');
            return false;
        }
    }
    /**
     * 检查用户终端
     * @param       string      fromUrl     重那个网址传过来的，自动登录的话跳转此页面
     * @param       string      toPcUrl     对应的pc网址
     */
    function UserPort($fromUrl,$toPcUrl='www.recytl.com'){
        $this->load->library('user_agent');
        $user_agent= $this->agent->agent_string();
        if ((strpos($user_agent, 'MicroMessenger')||$this->input->get('code',true)=='a')&&
            (!isset($_SESSION['userinfo']['Login_openid']))) {//微信平台直接自动登入
            $code = $this->input->get('code',true);
            $this->load->model('common/wxcode_model');
            $bool_login=$this->wxLogin($code,$fromUrl);
        }elseif(!strrpos($user_agent, 'Mobile')){
            header("Location:http://".$toPcUrl);
        }
    }
    /**
     * 检查用户是否登录
     * @return          登录返回true|错误返回false
     */
    function UserIsLogin(){
        if (!isset($_SESSION['userinfo']['user_mobile'])||empty($_SESSION['userinfo']['user_mobile'])
            ||!isset($_SESSION['userinfo']['user_id'])) {
            return false;
        }
        return true;
    }
    /**
     * 检查用户是否跳转，未登录跳转到登录界面，并记录登录后要跳转的界面。如果ajax请求，则记录登录后要跳转的界面
     * @param       string          LoginToUrl      登录后要跳转的链接（默认为不是）
     * @param       bool            isAjax          是否为ajax请求          
     * @return      直接跳转登录界面 | 如果是ajax请，返回false
     */
    function UserIsLoginJump($LoginToUrl='',$isAjax=false){
        if (!isset($_SESSION['userinfo']['user_mobile'])||empty($_SESSION['userinfo']['user_mobile'])
            ||!isset($_SESSION['userinfo']['user_id'])) {
            if (!empty($LoginToUrl)) {
                $_SESSION['LoginBackUrl'] = $LoginToUrl;
            }
            $method=$this->input->is_ajax_request();
            if($method&&$isAjax===true){
                return false;
            }
            header('Location:http://'.$_SERVER['HTTP_HOST'].'/index.php/nonstandard/system/Login');
            exit();
        }
        return true;
    }
}
?>
