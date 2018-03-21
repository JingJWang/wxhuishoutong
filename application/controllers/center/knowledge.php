<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @author Administrator
 *
 */
class  Knowledge  extends  CI_Controller{
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 系统登录
     * @param   string   name  用户名
     */
    function getlables(){ 
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $this->load->model('center/knowledge_model');
        $result = $this->knowledge_model->getalllabel();
        if ($result === false) {
            Universal::Output($this->config->item('request_fall'),'未找到标签','','');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 检查传过来的文章信息
     * @param      int      lid      标签id
     * @param      string   name     文章名字
     * @param      string   str      文章的内容
     * @param      string   img      图片的地址
     * @param      string   desc     文章的描述
     * @param      array     返回数组
     */
    function checkpost(){
        $info = array();
        $info['lid'] = $this->input->post('lid',true);
        $info['name'] = Universal::filter($this->input->post('name',true));
        $info['image'] = Universal::filter($this->input->post('image',true));
        $info['desc'] = Universal::filter($this->input->post('desc',true));
        if ($this->input->post('is_up',true)==1) {
            $info['is_up'] = 1;
        }else{
            $info['is_up'] = 0;
        }
        $content = $this->input->post('content');
        $content = $this->unescape($content);
        $farr = array(
            "/select|insert|update|delete/",
            "/<(\/?)(script|i?frame|style|html|body|title|link|meta|\?|\%)([^>]*?)>/",  //过滤 <script 等可能引入恶意内容或恶意改变显示布局的代码,如果不需要插入flash等,还可以加入<object的过滤
        );
        $str = preg_replace( $farr,array('',''),$content);
        $info['str'] = json_encode($str);
        if (!is_numeric($info['lid'])) {
            Universal::Output($this->config->item('request_fall'),'','请选择正确的标签','');
        }
        return $info;
    }
    /**
     * 提交文章信息
     */
    function putextinfo(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $info = $this->checkpost();
        $this->load->model('center/knowledge_model');
        $result = $this->knowledge_model->addarticle($info);
        if ($result == false) {
            Universal::Output($this->config->item('request_fall'),'',$this->knowledge_model->msg,'');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
        // echo htmlspecialchars_decode($str);
        // var_dump($lid,$name,$image,$desc,$content);
    }
    /**
     * 获取文章列表
     * @param        int        num       重第num查询文章
     */
    function getarticlelist(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $num = $this->input->post('num',true);
        if (!is_numeric($num)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('center/knowledge_model');
        $result['articles'] = $this->knowledge_model->getarticles($num);
        $result['num'] = $this->knowledge_model->getnums();
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 获取文章列表
     * @param        int        id       文章的id
     * @return       array      文章和文章标签的信息
     */
    function getarticle(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $id = $this->input->post('id',true);
        if (!is_numeric($id)) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('center/knowledge_model');
        $result = $this->knowledge_model->gethisarticle($id);
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'未取得相关信息','','');
        }
        $result['article']['0']['content'] = json_decode($result['article']['0']['content']);
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 更新文章
     */
    function uparticle(){
        if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
            Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
        }
        $info = $this->checkpost();
        if (!is_numeric($this->input->post('id',true))) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('center/knowledge_model');
        $result = $this->knowledge_model->uparticleinfo($info,$this->input->post('id',true));
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),$this->knowledge_model->msg,'','');
        }
        Universal::Output($this->config->item('request_succ'),'更新成功','','');
    }
    /**
     * 删除文章 
     */
    function getdelart(){
    	if (!isset($_SESSION['user']['id'])&&!isset($_SESSION['user']['mobile'])) {//未登录
    		Universal::Output($this->config->item('request_fall'),'请登录','/view/control/login.html','');
    	}
    	$id = $this->input->post('id',true);
    	if (!is_numeric($id)) {
    		Universal::Output($this->config->item('request_fall'),'','','');
    	}
    	$this->load->model('center/knowledge_model');
    	$this->knowledge_model->id=$id;
    	$result = $this->knowledge_model->getdelart();
    	if ($result===false) {
    		Universal::Output($this->config->item('request_fall'),'未取得相关信息','','');
    	}
    	 Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 解析由js传过来的escape编码
     */
    private function unescape($str){
        $ret = ''; 
        $len = strlen($str); 
        for ($i = 0; $i < $len; $i++){ 
        if ($str[$i] == '%' && $str[$i+1] == 'u'){ 
        $val = hexdec(substr($str, $i+2, 4)); 
        if ($val < 0x7f) $ret .= chr($val); 
        else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
        else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
        $i += 5; 
        } 
        else if ($str[$i] == '%'){ 
        $ret .= urldecode(substr($str, $i, 3)); 
        $i += 2; 
        } 
        else $ret .= $str[$i]; 
        } 
        return $ret; 
    }
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}