<?php
/**
 * 通化商城其它商品
 * @author wang
 * 
 */
class  Flowgood extends  CI_Controller{
	/**
	 * 获取用户和商品信息
	 * @param      int     id     商品id、
	 * @param      int     num    商品数量
	 */
    function orderinfo(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(1,$_SESSION);
        if(isset($this->userauth_model->url)){
            Universal::Output($this->config->item('request_fall'),'您还没有登录,正在跳转到首页自动登录',$this->userauth_model->url,'');
        }
        if(empty($_SESSION['userinfo']['user_mobile'])){
            Universal::Output($this->config->item('request_fall'),'请注册后购买此商品','/index.php/task/usercenter/isreg','');
        }
        $wx_id = $_SESSION['userinfo']['user_id'];
        if (!is_numeric($id=$this->input->post('id',true))||!is_numeric($wx_id)
            ||!is_numeric($num=$this->input->post('num',true))) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('shop/goods_model');
        $this->goods_model->goods_id = $id;
        $result=$this->goods_model->goodsInfo();
        if($result === false){
            Universal::Output($this->config->item('request_fall'),'没有此商品','','');
        }
        $return['goodinfo'] = $result['0'];
        $return['goodinfo']['allIntegral'] = $return['goodinfo']['integral']*$num;
        $return['goodinfo']['allPpri'] = $return['goodinfo']['ppri']*$num;
        $return['usermobile'] = $_SESSION['userinfo']['user_mobile'];
        Universal::Output($this->config->item('request_succ'),$this->goods_model->msg,'',$return);
    }
    /**
     * 查询商品
     */
    function seachshop(){
        $text = $this->input->post('text',true);
        $str_key = trim($text,' ');
        $str_key = Universal::SplitWord($str_key);
        if(empty($str_key)){
            exit();
        }
        $this->load->model('shop/flow_model');
        $result = $this->flow_model->seachtext($str_key);
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'未搜到商品','','');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 获得热销商品
     * @param        int        id        商品的id
     */
    function hotshop(){
        $this->load->model('shop/flow_model');
        $result = $this->flow_model->gethotshop();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 添加分享次数
     */
    function addsharenum(){
        if(!is_numeric($id=$this->input->post('id',true))){
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('shop/flow_model');
        $result = $this->flow_model->addshare($id);
        if ($result==false) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        Universal::Output($this->config->item('request_succ'),'','','');
    }
    /**
     * 检查订单是否交易完成
     */
    function checkorder(){
        //校验登录权限
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(1,$_SESSION);
        $number = $this->input->post('number',true);
        if (!is_numeric($number)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('shop/flow_model');
        $result = $this->flow_model->getOrder($number);
        if ($result === false) {
        	Universal::Output($this->config->item('request_fall'),'','','');
        }
        if ($result['0']['status']==0||$result['0']['status']==-1) {
        	Universal::Output($this->config->item('request_fall'),'','',$result['0']);
        }else{
        	Universal::Output($this->config->item('request_succ'),'','',$result['0']);
        } 
    }
    /**
     * 获取推广商品
     */
    function getProGoods(){
        $fromUrl = $this->input->post('fromUrl');
        $spreadFrom = $this->input->post('spreadFrom');
        if ($spreadFrom != false&&ctype_alnum($spreadFrom)) {
            $_SESSION['userinfo']['spreadFrom'] = $spreadFrom;
        }
        $prevent = array(
            '/view/shop/generalize.html',
            '/view/spreads/ydsell.html',
            '/view/spreads/tbsell.html',
            '/view/spreads/txsell.html',
        );
        if (in_array($fromUrl, $prevent)) {//如果是卖商品界面，直接退出
            return ;
        }
        $this->load->model('shop/flow_model');
        $goods = $this->flow_model->ProGoods();
        foreach ($goods as $k => $v) {//每个类型的商品不能超过4个
            if (!isset($num[$v['tid']])) {
                $num[$v['tid']] = 1;
            }else{
                $num[$v['tid']]++;
            }
            if ($num[$v['tid']]<=4) {
                $ngoods[] = $v;
            }
        }
        if ($goods===false) {
            Universal::Output($this->config->item('request_fall'),'没有商品了','','');
        }
        $this->load->model('common/wxcode_model','',TRUE);
        $url = 'http://wx.recytl.com/view/shop/generalize.html';
        $signPackage = $this->wxcode_model->getSignPackageAjax($url);//分享的信息
        $result['goods'] = $ngoods;
        $result['signPackage'] = $signPackage;
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 支付宝支付完成调用的方法
     */
    function zhifubaoCallback(){
        $info = $_GET;
        $this->load->model('auto/userauth_model');
        $this->userauth_model->UserCheck(1,$_SESSION);
        $this->load->model('shop/goods_model');
        $this->goods_model->number = $info['out_trade_no'];
        if (!is_numeric($_SESSION['userinfo']['user_id'])||!is_numeric($info['out_trade_no'])) {
            exit;
        }
        $this->goods_model->userid = $_SESSION['userinfo']['user_id'];
        $record=$this->goods_model->recordInfoNumber();
        $showinfo = array(
            'goods_name'=>$record['0']['name'],
            'ordernum'=>$info['out_trade_no'],
            'pri'=>$record['0']['price'],
            'integral'=>$record['0']['integral'],
        );
        if ($record['0']['adress']!='') {
            $showinfo['adressinfo'] = explode(',', $record['0']['adress']);
        }
        $this->load->view('shop/payment',$showinfo);
    }
    /**
     * 推广界面（买）
     * @param       from        推广界面的参数
     */
    function spreadbuy(){
        $from = $this->uri->segment(4);
        $froms = array('tieba','tengxun','yidian');
        if (!in_array($from , $froms)) {
            exit;
        }
        if (!isset($_SESSION['userinfo']['spreadRand'])) {
            $param = array(
                        'config' => array('url' => 'http://60.205.142.49/Analysis/Server/Analysis/www/json/Gateway.php'), 
                        'dataName' => 'smartplatform', 
                        'key' => 'abcdefgABCDEFG0987654321'
                    );
            $this->load->library('spread/AnalysisServer.php',$param,'analysisServer');
            $addResult = $this->analysisServer->addTrack($this->input->ip_address(), 'unknow', $from);
            if (isset($addResult) && !empty($addResult) && is_array($addResult)) {
                $result = $addResult['result'];
                if ($result == 0) {
                    //成功
                    $_SESSION['userinfo']['spreadRand'] = $addResult['rand'];
                }
            }
        }
        if ($from != false&&ctype_alnum($from)) {
            $_SESSION['userinfo']['spreadFrom'] = $from;
        }
        $this->load->model('shop/flow_model');
        $goods = $this->flow_model->ProGoods();
        foreach ($goods as $k => $v) {//每个类型的商品不能超过4个
            if (!isset($num[$v['tid']])) {
                $num[$v['tid']] = 1;
            }else{
                $num[$v['tid']]++;
            }
            if ($num[$v['tid']]<=4) {
                if ($v['tid']==7) {
                    $ngoods['phone'][] = $v;
                }elseif($v['tid']==10){
                    $ngoods['luxury'][] = $v;
                }
            }
        }
        if ($goods===false) {
            Universal::Output($this->config->item('request_fall'),'没有商品了','','');
        }
        $ngoods['from'] = $from;
        $this->load->view('spreads/'.$from.'buy',$ngoods);
    }
    /**
     * 推广界面（卖）
     * @param       from        推广界面的参数
     */
    function spread(){
        $from = $this->uri->segment(4);
        $froms = array('tieba','tengxun','yidian');
        if (!in_array($from , $froms)) {
            exit;
        }
        if (!isset($_SESSION['userinfo']['spreadRand'])) {
            $param = array(
                        'config' => array('url' => 'http://60.205.142.49/Analysis/Server/Analysis/www/json/Gateway.php'), 
                        'dataName' => 'smartplatform', 
                        'key' => 'abcdefgABCDEFG0987654321'
                    );
            $this->load->library('spread/AnalysisServer.php',$param,'analysisServer');
            $addResult = $this->analysisServer->addTrack($this->input->ip_address(), 'unknow', $from);
            if (isset($addResult) && !empty($addResult) && is_array($addResult)) {
                $result = $addResult['result'];
                if ($result == 0) {
                    //成功
                    $_SESSION['userinfo']['spreadRand'] = $addResult['rand'];
                }
            }
        }
        if ($from != false&&ctype_alnum($from)) {
            $_SESSION['userinfo']['spreadFrom'] = $from;
        }
        $ngoods['rand'] = $_SESSION['userinfo']['spreadRand'];
        $ngoods['from'] = $from;
        $this->load->view('spreads/'.$from.'sell',$ngoods);
    }
}