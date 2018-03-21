<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
/**
 * 
 * @author wang
 *
 */
class  AdminUser  extends  CI_Controller{
    /**
     * 添加管理员
     * @param     int     rid       所拥有的权限
     * @param     int     name      名字
     * @param     int     mobile    电话号码
     * @param     int     email     邮箱
     * @param     int     pw        密码       
     */
    function joinAdmin(){
        $rid = $this->input->post('rid',true);
        $name = $this->input->post('name',true);
        $mobile = $this->input->post('mobile',true);
        $email = $this->input->post('email',true);
        $pw = $this->input->post('pw',true);
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if(filter_var($mobile,FILTER_VALIDATE_EMAIL) === false &&
                is_numeric($mobile) === false){
            Universal::Output($this->config->item('request_fall'),'电话号码填写错误！');
        }   
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if (!preg_match($pattern, $email)) {
            Universal::Output($this->config->item('request_fall'),'邮箱地址填写错误');
        }
        if (strlen($pw)<6) {
            Universal::Output($this->config->item('request_fall'),'密码个数不少于6位');
        }
        if ( $pw=='' || $name == '') {
            Universal::Output($this->config->item('request_fall'),'请填写正确的信息');
        }
        if (!is_numeric($rid)) {
            Universal::Output($this->config->item('request_fall'),'请选择用户角色');
        }
        $this->load->model('center/adminUser_model');
        $this->adminUser_model->pw = md5(Universal::filter($pw));
        $this->adminUser_model->name = universal::filter($name);
        $this->adminUser_model->mobile = $mobile;
        $this->adminUser_model->email = $email;
        $this->adminUser_model->rid = $rid;
        $this->adminUser_model->checkrepeat();//检查邮箱和手机号码有没有重复
        $result = $this->adminUser_model->joinInfo();
        if (!$result) {
            Universal::Output($this->config->item('request_fall'),'加入失败');
        }
        Universal::Output($this->config->item('request_succ'),'');
    }
    /**
     * 修改管理员信息
     * @param     int     uid     要修改的字段id
     * @param     int     rid     所拥有的权限
     * @param     int     name    更改的名字
     */
    function modifyAdmin(){
        $uid = $this->input->post('uid',true);
        $rid = $this->input->post('rid',true);
        $name = $this->input->post('name',true);
        $pw = $this->input->post('pw',true);
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($rid)||!is_numeric($uid)) {
            Universal::Output($this->config->item('request_fall'),'请填写正确的参数');
        }
        $this->load->model('center/adminUser_model');
        $this->adminUser_model->name = universal::filter($name);
        $this->adminUser_model->rid = $rid;
        $this->adminUser_model->uid = $uid;
        if ($pw!='') {
            if (strlen($pw)<6) {
                Universal::Output($this->config->item('request_fall'),'密码个数不少于6位');
            }
            $this->adminUser_model->pw = md5(Universal::filter($pw));
        }else{
            $this->adminUser_model->pw = '';
        }
        $result = $this->adminUser_model->modifyInfo();
        if ($result === false) {
            Universal::Output($this->config->item('request_fall'),'添加失败');
        }
        Universal::Output($this->config->item('request_succ'),'添加成功');
    }
    /**
     * 查看用户
     * @param      int     page      页数开始
     */
    function checkAdmins(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $page = $this->input->post('page',true);
        if (!is_numeric($page)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $page_per = $page+10;
        $this->load->model('center/adminUser_model');
        $result = $this->adminUser_model->checkInfo($page,$page_per);
        if ($result === false) {
            Universal::Output($this->config->item('request_fall'),'未查到信息','','');
        }
        $result['name'] = $_SESSION['user']['name'];
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 获得某一个用户信息
     */
    function getOneAdmin(){
        $uid= $this->input->post('uid',true);
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($uid)) {
            Universal::Output($this->config->item('request_fall'),'请填写正确的参数');
        }
        $this->load->model('center/adminUser_model');
        $this->adminUser_model->uid = $uid;
        $result = $this->adminUser_model->getOneInfo();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'未查到相关内容','','');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 删除
     */
    function delAdmin(){
        $uid = $this->input->post('uid',true);
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        if (!is_numeric($uid)) {
            Universal::Output($this->config->item('request_fall'),'请填写正确的参数');
        }
        $this->load->model('center/adminUser_model');
        $this->adminUser_model->uid = $uid;
        $result = $this->adminUser_model->delAdmin();
        if ($result === false) {
            Universal::Output($this->config->item('request_fall'),'修改失败','','');
        }
        Universal::Output($this->config->item('request_succ'),'修改成功');
    }
    /**
     * 搜索用户
     * @param     string       mobile     电话号码
     */
    function select(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $mobile = $this->input->post('mobile',true);
        if(filter_var($mobile,FILTER_VALIDATE_EMAIL) === false &&
                is_numeric($mobile) === false){
            Universal::Output($this->config->item('request_fall'),'电话号码填写错误！');
        }  
        $this->load->model('center/adminUser_model');
        $this->adminUser_model->mobile = $mobile;
        $result = $this->adminUser_model->selectInfo();
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'未查到该用户');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
}