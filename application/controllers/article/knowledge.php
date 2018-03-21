<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
/**
 * @author   王仁杰    2016-06-12
 */
class Knowledge extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取标签
     * @return    json    返回json字符串
     */
    function getlabel(){
        $this->load->model('auto/userauth_model');
        $this->load->model('article/knowledge_model');
        $result = $this->knowledge_model->getlabel();//获取文章信息
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'没有标签','','');
        }
        if($this->userauth_model->UserIsLogin()){//校验用户是否登录，登录则获取用户信息
            $wx_id = $_SESSION['userinfo']['user_id'];
            $this->load->model('task/tasks_model');
            $center_info = $this->tasks_model->getextentnum($wx_id);//获取用户的推广码
            if ($center_info==0) {
                $this->load->model('task/user_model');
                $str = $this->user_model->is_have_user($wx_id);//判断用户是否登入过任务中心，不是侧插入。
                $center_info = $this->tasks_model->getextentnum($wx_id);//获取用户的推广码
            }
            $return['extendnum'] = $center_info['0']['center_extend_num'];
            $return['isreg'] = true;
        }else{
            $return['isreg'] = false;
        }
    	$return['label']=$result;
    	Universal::Output($this->config->item('request_succ'),'','',$return);
    }
    /**
     * 获取知识库文章列表
     * @param     int     type        获取文章列表的类型（0表示所有类型的文章）
     * @param     int     sortby      通过某种方法排序 1为时间  2为点击量
     * @return    json    返回json字符串
     */
    function getlist(){
        if (!is_numeric($type = $this->input->post('type',true))
        	||!is_numeric($sortby = $this->input->post('sortby',true))) {
    		Universal::Output($this->config->item('request_fall'),'出错','','');
        }
    	$this->load->model('article/knowledge_model');
    	$result['heatar'] = $this->knowledge_model->getmostheat($type);//1、获取本周最热门的文章
    	$result['article'] = $this->knowledge_model->getarticlelist($type,$sortby,0,0,0);//2、获取文章列表
    	if ($result['article'] === false) {
    		Universal::Output($this->config->item('request_fall'),'没有此类文章','','');
    	}
    	Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 加载知识库文章列表
     * @param     int     type        获取文章列表的类型（0表示所有类型的文章）
     * @param     int     lastime     获取此时间后的文章列表
     * @param     int     sortby      通过某种方法排序 1为时间  2为点击量
     * @return    json    返回json字符串
     */
    function loadlist(){
        if (!is_numeric($type = $this->input->post('type',true))
        	||!is_numeric($ltime = $this->input->post('lastime',true))
        	||!is_numeric($sortby = $this->input->post('sortby',true))
        	||!is_numeric($num = $this->input->post('num',true))) {
    		Universal::Output($this->config->item('request_fall'),'出错','','');
        }
    	$this->load->model('article/knowledge_model');
    	$result['article'] = $this->knowledge_model->getarticlelist($type,$sortby,$ltime,$num);//2、加载后续的文章列表
    	if ($result['article'] === false) {
    		Universal::Output($this->config->item('request_fall'),'没有此类文章','','');
    	}
    	Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 获取文章   1、获取文章信息   2、检查是否重置本周点击次数，更新点击次数。
     * @param     int     id          获取的文章id
     * @param     int     wx_id       获取用户的id
     * @param     string     url      此网页的路径
     * @return    json    返回json字符串
     */
    function getarticle(){
        $extendnum_instruction = $this->input->post('extendnum',true);
        if (is_numeric($extendnum_instruction)) {
            $tid = $extendnum_instruction;
        }else{
            $extendnum = substr($extendnum_instruction,0,6);
            $arr = explode('_',$extendnum_instruction);
            $tid = end($arr);
        }
        if (!is_numeric($tid)||(isset($extendnum)&&!ctype_alnum ($extendnum))) {
            Universal::Output($this->config->item('request_fall'),'','','');
        }
        $this->load->model('common/wxcode_model','',TRUE);
        $this->load->model('auto/userauth_model','',TRUE);
        $code = $this->input->post('code',true);
        if($this->userauth_model->UserIsLogin()){//已经登录
            if (!is_numeric($wx_id = $_SESSION['userinfo']['user_id'])) {
                Universal::Output($this->config->item('request_fall'),'','','');
            }
            $isreg = true;
            ($code=='null')?$shcode = '':$shcode = '&code='.$code.'&state=';
        }else{
            $wx_id = '';
            $isreg = false;
            $this->load->library('user_agent');
            $user_agent= $this->agent->agent_string();
            if (strpos($user_agent, 'MicroMessenger')) {
                if ($code=='null') {
                    Universal::Output($this->config->item('request_fall'),'',
                    'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22'
                   .'&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Farticle%2Farticlesh.html?extendnum='.$extendnum_instruction
                   .'&response_type=code&scope=snsapi_base&state=#wechat_redirect','');
                }else{
                    if (isset($extendnum)) {
                        $_SESSION['userinfo']['invite'] = $extendnum;
                    }
                    $this->load->library('user_agent');
                    $user_agent= $this->agent->agent_string();
                    $result = $this->userauth_model->wxLogin($code);
                    $shcode = '&code='.$code.'&state=';
                }
            }else{
                $shcode = '';
            }
        }
        $shareurl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22'
               .'&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Fview%2Farticle%2Farticlesh.html?extendnum='.$extendnum_instruction
               .'&response_type=code&scope=snsapi_base&state=#wechat_redirect';
        if ($this->input->post('status',true)==1) {
            $url = 'http://wx.recytl.com/view/article/article.html?extendnum='.$extendnum_instruction.$shcode;
        }elseif($this->input->post('status',true)==2){
            $url = 'http://wx.recytl.com/view/article/articlesh.html?extendnum='.$extendnum_instruction.$shcode;
        }
        $signPackage=$this->wxcode_model->getSignPackageAjax($url);//分享的信息
    	$this->load->model('article/knowledge_model');
    	$article = $this->knowledge_model->getarticleinfo($tid);//1、获取文章具体信息
    	if ($article===false) {
    		Universal::Output($this->config->item('request_fall'),'没有此文章','','');
    	}
        $result = $this->knowledge_model->uparticleinfo($tid,$wx_id,$article['0']);//添加日志和查看任务
        if ($result===false) {
    		Universal::Output($this->config->item('request_fall'),'出错','','');
        }
        $info = array(
        	'name' => $article['0']['text_name'],
        	'content' =>json_decode($article['0']['text_content']),
        	'desc' => $article['0']['des'],
        	'img' => $article['0']['img'],
        	'signPackage' => $signPackage,
        	'isreg' => $isreg,
            'shareurl' => $shareurl,
        );
    	Universal::Output($this->config->item('request_succ'),'','',$info);
    }
    /**
     * 搜查文章
     * @param     string     text         搜索文字
     * @return    json    返回json字符串
     */
    function search(){
        $text = $this->input->post('text',true);
        $str_key = trim($text,' ');
        $str_key = Universal::SplitWord($str_key);
        if(empty($str_key)){
            exit();
        }
    	$this->load->model('article/knowledge_model');
    	$result = $this->knowledge_model->seachtext($str_key);
    	if ($result===false) {
    		Universal::Output($this->config->item('request_fall'),'未搜到相关文章','','');
    	}
    	Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 分享后执行的函数
     * @param     string  extendnum             邀请码
     * @return    json    返回json字符串
     */
    function aftershare(){
        $extendnum_instruction = $this->input->post('extendnum',true);
        if (is_numeric($extendnum_instruction)) {
            $tid = $extendnum_instruction;
        }else{
            $arr = explode('_',$extendnum_instruction);
            $tid = end($arr);
            if (!is_numeric($tid)) {
                Universal::Output($this->config->item('request_fall'),'','','');
            }
        }
    	$this->load->model('article/knowledge_model');
    	$result = $this->knowledge_model->addshare($tid);
    }
    /**
     * 关闭数据库
     */
    function __destruct(){
        $this->db->close();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */