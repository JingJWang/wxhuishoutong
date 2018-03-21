<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Administrator
 *
 */
class  Preshop  extends  CI_Controller{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取用户预购的信息
     * @return      json        返回json字符串
     */
    function getpreinfo(){
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $num = $this->input->post('num',true);
        $type = $this->input->post('type',true);
        if (!is_numeric($num)||$num<0||($type!=1&&$type!=2)) {
            Universal::Output($this->config->item('request_fall'),'参数错误');
        }
        $this->load->model('center/preshop_model');
        $result = $this->preshop_model->getpreinfo($num,$type);
        if (!$result) {
            Universal::Output($this->config->item('request_fall'),'未找到相关信息');
        }
        foreach ($result as $k => $v) {
            $result[$k]['content'] = json_decode($result[$k]['content'],true);
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 改变已经打过电话的状态
     */
    function havecall(){
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id = $this->input->post('id',true);
        if (!is_numeric($id)) {
            Universal::Output($this->config->item('request_fall'));
        }
        $this->load->model('center/preshop_model');
        $result = $this->preshop_model->changecall($id);
        if ($result == false) {
            Universal::Output($this->config->item('request_fall'),'修改失败','');
        }
        Universal::Output($this->config->item('request_succ'),'修改成功','');
    }
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}