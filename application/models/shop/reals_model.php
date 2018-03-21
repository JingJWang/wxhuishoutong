<?php
/**
 * 实体 商品
 * @author wang
 *
 */
class  Reals_model extends  CI_Model{
    public $msg = '';
    function  __construct(){
        parent::__construct();
        $this->load->database();    
    }
    /**
     * 获取用户地址
     * @param        int        uid       用户id
     */
    function getaddress($uid){
        $sql = 'select receive_id as id,receive_name as name,receive_phone as number,receive_province as province,receive_city as city,
            receive_details as details,receive_status as status from h_wxuser_receiveinfo where user_id='.$uid.' 
            and (receive_status=1 or  receive_status=2)';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = 'noadress';
            return false;
        }
        $result = $result->result_array();
        return $result;
    }
    /**
     * 插入地址
     *@param       array      插入信息
     */
    function address(){
        $addinfo = $this->addinsert;
        $is_havemo = '';
        if ($addinfo['receive_status']==2) {//判断原来是否有默认的地址
            $sql = 'select receive_id as id from h_wxuser_receiveinfo 
                    where user_id='.$addinfo['user_id'].' and receive_status=2';
            $result = $this->db->query($sql);
            if ($result->num_rows>0) {
                $is_havemo = $result->result_array();
            }
        }
        $this->db->trans_begin();
        if (!empty($is_havemo)) {
            $this->db->update('h_wxuser_receiveinfo',array('receive_status'=>'1','receive_uptime'=>time()),
                               array('receive_id'=>$is_havemo['0']['id']));
        }
        $this->db->insert('h_wxuser_receiveinfo',$this->addinsert);
        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            return true;
        }else{
            $this->db->trans_rollback();
            $this->msg='插入地址出现异常!';
            return false;
        }
    }
    /**
     * 更新某个任务信息
     * @param      int      id      任务id
     */
    function upaddre($id){
        $upinfo = $this->updatas;
        $is_havemo = '';
        if ($upinfo['receive_status']==2) {//判断原来是否有默认的地址
            $sql = 'select receive_id as id from h_wxuser_receiveinfo 
                    where user_id='.$upinfo['user_id'].' and receive_status=2';
            $result = $this->db->query($sql);
            if ($result->num_rows>0) {
                $is_havemo = $result->result_array();
            }
        }
        unset($upinfo['user_id']);
        $this->db->trans_begin();
        if (!empty($is_havemo)) {
            $this->db->update('h_wxuser_receiveinfo',array('receive_status'=>'1','receive_uptime'=>time()),
                               array('receive_id'=>$is_havemo['0']['id']));
        }
        $this->db->update('h_wxuser_receiveinfo',$upinfo,array('receive_id'=>$id));
        if ($this->db->trans_status() === true) {
            $this->db->trans_commit();
            return true;
        }else{
            $this->db->trans_rollback();
            $this->msg='更新地址出现异常!';
            return false;
        }
    }
    /**
     * 获取某个任务信息
     * @param      int      id      任务id
     */
    function getanaddre($id){
        $sql = 'select receive_id as id,user_id as uid,receive_name as name,receive_phone as number,
                receive_province as province,receive_city as city,receive_details as details,
                receive_status as status from h_wxuser_receiveinfo where receive_id='.$id.' and receive_status!=-1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            $this->msg = '未找到您要的地址';
            return false;
        }
        $result = $result->result_array();
        return $result['0'];
    }
    /**
     * 删除某个任务信息
     * @param      int      id      任务id
     */
    function deladdre($id){
        if (!isset($this->deldatas)) {
            $this->msg='创建订单出现异常!';
            return  false;
        }
        $this->db->update('h_wxuser_receiveinfo',$this->deldatas,array('receive_id'=>$id));
        if ($this->db->affected_rows()!=1) {
            $this->msg='创建订单出现异常!';
            return false;
        }
        return true;
    }
    /**
     * 插入预购信息
     */
    function addproinfo($content){
        if (!is_numeric($_SESSION['userinfo']['user_id'])||!is_numeric($_SESSION['userinfo']['user_mobile'])) {
            return false;
        }
        $insert = array(
            'pre_content' => $content,
            'pre_userid' => $_SESSION['userinfo']['user_id'],
            'pre_mobile' => $_SESSION['userinfo']['user_mobile'],
            'pre_jointime' => time(),
            'pre_status' => 1,
        );
        $result = $this->db->insert('h_preorder_log',$insert);
        if ($result == false||$this->db->affected_rows()!=1) {
            return false;
        }
        $this->load->model('common/wxcode_model');
        if (isset($_SESSION['userinfo']['user_openid'])&&$_SESSION['userinfo']['user_openid']!='') {
            $temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2fview/shop/info.html?id=336&response_type=code&scope=snsapi_base&state=#wechat_redirect';
            $sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的手机求购信息已收到",
                        "description":"点此进入定金页",
                        "url":"%s", "picurl":""}]}}';
            $content = sprintf($sendtext,$_SESSION['userinfo']['user_openid'],$temp_url);
            $response_wx=$this->wxcode_model->sendmessage($content);
        }
        return true;
    }
}