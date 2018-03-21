<?php
/**
 * 通化商城实体商品信息
 * @author wang
 * 
 */
class  Realgood extends  CI_Controller{
	/**
	 * 获取用户和商品信息
	 * @param      int     id     商品id、
	 * @param      int     num    商品数量
	 */
    function orderinfo(){
        $info = $this->input->post();
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION,true);
        if(isset($this->userauth_model->url)){
            $_SESSION['LoginBackUrl'] = '/view/shop/orderInfo.html?id='.$info['id'].'&sprice='.$info['sprice'];
            Universal::Output($this->config->item('request_fall'),'您还没有登录','/index.php/nonstandard/system/Login','');
        }
        $openid=$_SESSION['userinfo']['user_openid'];
        if($openid=='' || empty($openid) ||$openid==null){
        	$temp= 1;
        }else{
        	$temp=0;
        }
        $user_id = $_SESSION['userinfo']['user_id'];
        if(!is_numeric($info['id'])||!is_numeric($info['num'])||!is_numeric($user_id) 
           ||!is_numeric($info['sprice'])|| (!is_numeric($info['adr'])&&$info['adr']!='null')) {
            Universal::Output($this->config->item('request_fall'),'请求非法!','','');
        }
        $this->load->model('shop/goods_model');
        $this->goods_model->goods_id = $info['id'];
        $result=$this->goods_model->goodsInfo();//获取商品信息
        if($result === false){
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        if (isset($result['0']['otprice'][$info['sprice']])) {
            $result['0']['integral'] = $result['0']['otprice'][$info['sprice']]['in'];
            $result['0']['ppri'] = $result['0']['otprice'][$info['sprice']]['p'];
        }
        if ($result['0']['fid']==2) {//获取地址
            $this->load->model('shop/reals_model');
            $addresult = $this->reals_model->getaddress($user_id);
            if ($info['adr']=='null' && $addresult!=false) {
                foreach ($addresult as $k => $v) {
                    if ($v['status']==2) {//获取默认地址
                        $return['address'] = $v;
                        break;
                    }
                }
            }elseif($addresult!=false){
                foreach ($addresult as $k => $v) {
                    if ($v['id']==$info['adr']) {//选中的地址
                        $return['address'] = $v;
                        break;
                    }
                }
            }
            if (!isset($return['address'])&&$addresult!=false) {//当用户选中的地址不存在
                $return['address'] = $addresult['0'];
            }
            if ($addresult===false) {
                $return['address'] = $this->reals_model->msg;//当没有地址时，有地址给session赋值
            }else{
                $_SESSION['good']['selectadrid'] = $return['address']['id'];
            }
        }
        $return['usermobile'] = $_SESSION['userinfo']['user_mobile'];
        $return['goodinfo'] = $result['0'];
        $return['goodinfo']['allIntegral'] = $return['goodinfo']['integral']*$info['num'];
        $return['goodinfo']['allPpri'] = $return['goodinfo']['ppri']*$info['num'];
        $return['temp']=$temp;
        $return['nums']=$info['num'];
        Universal::Output($this->config->item('request_succ'),$this->goods_model->msg,'',$return);
    }
    /**
     * 添加地址
     * @param       post      用户填写的信息
     */
    function addadress(){
        if (!$this->input->post('name',true) || !$this->input->post('phone',true) || !$this->input->post('address',true) 
           || !$this->input->post('detail',true) || !is_numeric($this->input->post('isit',true))) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'请求非法!','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录,正在跳转到首页自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $insert['user_id'] = $_SESSION['userinfo']['user_id'];
        $insert['receive_name'] = $this->input->post('name',true);
        $insert['receive_phone'] = $this->input->post('phone',true);
        $insert['receive_city'] = $this->input->post('address',true);
        if(strstr($insert['receive_city'],'-')){
            $addres=explode('-',$insert['receive_city']);
        }else{
            $addres=0;
        }
        if(!empty($addres)){
            $insert['receive_province']=$addres['0'];
            $insert['receive_area']=$addres['2'];
        }
        $insert['receive_details'] = $this->input->post('detail',true);
        $insert['receive_jointime'] = time();
        $insert['receive_status'] = $this->input->post('isit',true)==1?2:1;
        if (strlen($insert['receive_details'])>200||strlen($insert['receive_city'])>45||strlen($insert['receive_name'])>48) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'输入的名字或地址过长','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$insert['receive_phone'])||!is_numeric($insert['user_id'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'手机格式不对或数据传输错误','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if (!get_magic_quotes_gpc()) {
            $insert['receive_name'] = addslashes($insert['receive_name']);
            $insert['receive_city'] = addslashes($insert['receive_city']);
            $insert['receive_details'] = addslashes($insert['receive_details']);
        }
        $this->load->model('shop/reals_model');
        $this->reals_model->addinsert = $insert;
        $result = $this->reals_model->address();
        if ($result === false) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit; 
        }
        $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
        echo json_encode($response);exit; 
    }
    /**
     * 获取用户所有的地址信息
     * @param       int       user_id     用户id
     */
    function getaddress(){
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录,正在跳转到首页自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $user_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($user_id)) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'请求非法!','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->load->model('shop/reals_model');
        $result = $this->reals_model->getaddress($user_id);
        if ($result == false) {
             $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        foreach ($result as $k => $v) {
            if (isset($_SESSION['good']['selectadrid']) && $v['id']==$_SESSION['good']['selectadrid']) {
                $result[$k]['sel'] = 1;
            }else{
                $result[$k]['sel'] = 0;
            }
        }
        $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>'','url'=>'','data'=>$result);
        echo json_encode($response);exit;
    }
    /**
     * 获取用户某一个地址的信息
     * @param        int         adid       任务的id
     */
    function getanaddress(){
        $adid = $this->input->post('id',true);
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录,正在跳转到首页自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $user_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($adid)) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'请求非法!','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->load->model('shop/reals_model');
        $result = $this->reals_model->getanaddre($adid);
        if (($result['uid'] != $user_id) || $result===false) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'未找到您要的地址','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        unset($result['uid']);
        $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>'','url'=>'','data'=>$result);
        echo json_encode($response);exit;
    }
    /**
     * 更新某个地址
     * @param        int         adid       任务的id
     */
    function upaddre(){
         if (!$this->input->post('name',true) || !$this->input->post('phone',true) || !$this->input->post('address',true) 
             || !$this->input->post('detail',true) || !is_numeric($this->input->post('isit',true))
             || !is_numeric($this->input->post('id',true))) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'请求非法!','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录,正在跳转到首页自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $updata['user_id'] = $_SESSION['userinfo']['user_id'];
        $updata['receive_name'] = $this->input->post('name',true);
        $updata['receive_phone'] = $this->input->post('phone',true);
        $updata['receive_city'] = $this->input->post('address',true);
        $updata['receive_details'] = $this->input->post('detail',true);
        $updata['receive_uptime'] = time();
        $updata['receive_status'] = $this->input->post('isit',true)==1?2:1;
        if (strlen($updata['receive_details'])>300||strlen($updata['receive_city'])>45||strlen($updata['receive_name'])>48) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'输入的名字或地址过长','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$updata['receive_phone'])||!is_numeric($updata['user_id'])){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'手机格式不对或数据传输错误','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        if (!get_magic_quotes_gpc()) {
            $updata['receive_name'] = addslashes($updata['receive_name']);
            $updata['receive_city'] = addslashes($updata['receive_city']);
            $updata['receive_details'] = addslashes($updata['receive_details']);
        }
        $this->load->model('shop/reals_model');
        $adid = $this->input->post('id',true);
        $result = $this->reals_model->getanaddre($adid);
        if ($result['uid']!=$updata['user_id']) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'未找到您要的地址','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->reals_model->updatas = $updata;
        $result = $this->reals_model->upaddre($result['id']);
        if ($result === false) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit; 
        }
        $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
        echo json_encode($response);exit; 
    }
    /**
     * 删除某个地址
     * @param        int         adid       任务的id
     */
    function deladdre(){
        $adid = $this->input->post('id',true);
        if (!is_numeric($adid)) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'请求非法!','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(2,$_SESSION);
        if(isset($this->userauth_model->url)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'您还没有登录,正在跳转到首页自动登录','url'=>$this->userauth_model->url,'data'=>''
            );
            echo json_encode($response);exit;
        }
        $this->load->model('shop/reals_model');
        $result = $this->reals_model->getanaddre($adid);
        if ($result['uid']!=$_SESSION['userinfo']['user_id']) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'未找到您要的地址','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $updata = array(
            'receive_uptime' => time(),
            'receive_status' => -1,
        );
        $this->reals_model->deldatas = $updata;
        $result = $this->reals_model->deladdre($adid);
        if ($result === false) {
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit; 
        }
        $response=array('status'=>$this->config->item('request_succ'),
                    'msg'=>$this->reals_model->msg,'url'=>'','data'=>'');
        echo json_encode($response);exit; 
    }
}