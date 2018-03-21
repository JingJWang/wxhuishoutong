<?php
class Taskverify extends CI_Controller{
    /**
     * 获取任务列表
     * @param        int        star       开始的页数
     * @param        int        type       需要数据的类型
     * @param        int        type       需要数据的时间
     * @return  json
     */
    function getregtask(){
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $star = $this->input->post('star',true);
        $type = $this->input->post('type',true);
        $stime = $this->input->post('stime',true);
        $etime = $this->input->post('etime',true);
        if($stime==''&&$etime==''){
            $time = '';
        }else{
            $stime = strtotime($stime);
            $etime = strtotime($etime);
            if ($stime==false||$etime==false||$stime>=$etime) {
                Universal::Output($this->config->item('request_fall'),'未找到信息','');
            }
            $time = 'and log_content>"'.$stime.'" and log_content<"'.$etime.'"';
        }
        if ($star==-1&&isset($_SESSION['user']['tasknowstar'])) {//开始页面处理
            $star = $_SESSION['user']['tasknowstar'];
        }elseif($star==-1){
            $star = 0;
        }
        if ($type==0&&isset($_SESSION['user']['tasknowtype'])) {//类型处理
            $type = $_SESSION['user']['tasknowtype'];
        }elseif($type==0){
            $type = 6;
        }
        $pro_arr = array(3,6,7);
        if (!is_numeric($star)||!in_array($type,$pro_arr)) {
            Universal::Output($this->config->item('request_fall'),'','');
        }
        $_SESSION['user']['tasknowstar'] = $star;
        $_SESSION['user']['tasknowtype'] = $type;
        $this->load->model('center/taskverify_model');
        $result = $this->taskverify_model->getlist($star,$type,$time);
        if ($result===false) {
            Universal::Output($this->config->item('request_fall'),'未找到信息','');
        }
        $num = $this->taskverify_model->gettasknum($type,$time);
        $data['re'] = $result;
        $data['num'] = $num;
        $data['type'] = $type;
        $data['star'] = $star;
        Universal::Output($this->config->item('request_succ'),'','',$data);
    }
    /**
     * 更新数据
     * @param        string       data        任务id的字符串
     * @param        string       nodata      未通过的字符串
     * @param        string       openid      用户的openid
     * @return  json
     */
    function uptaskinfo(){
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $data = $this->input->post('data',true);
        $nodata = $this->input->post('nodata',true);
        $openid = $this->input->post('openid',true);
        if ($data != false && !empty($data)) {
            $fin_str = $this->checkstr($data = explode(',',$data));//可以完成的任务列表
        }else{
            $fin_str = '';
        }
        if ($nodata != false && !empty($nodata)) {
            $nofin_str = $this->checkstr($nodata = explode(',',$nodata));//审核未通过的
        }else{
            $nofin_str = '';
        }
        if ($nofin_str==''&&$fin_str=='') {
            Universal::Output($this->config->item('request_fall'),'','');
        }elseif($nofin_str!=''&&$fin_str!=''){//检查两个数组里面是否有重复的数字
            if(!empty(array_intersect($data,$nodata))){//判断交集
                Universal::Output($this->config->item('request_fall'),'数据有误，请刷新界面','');
            }
        }
        $opnum = count($openid = explode(',',$openid));
        if ($fin_str!=''&&$opnum!=$fin_str['num']) {
            Universal::Output($this->config->item('request_fall'),'','');
        }
        $this->load->model('center/taskverify_model');
        $result = $this->taskverify_model->uptaskdata($fin_str,$nofin_str);//更新信息
        if ($result==false) {
            Universal::Output($this->config->item('request_fall'),'更新失败','');
        }
        //微信推送信息
        $this->load->model('common/wxcode_model');
        $temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/task/usercenter/taskcenter&response_type=code&scope=snsapi_base&state=#wechat_redirect';
        $sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的借贷宝已经审核通过",
                    "description":"您的借贷宝注册成功并通过审核，请点击详情前往领取奖励",
                    "url":"%s", "picurl":""}]}}';
        foreach ($openid as $k => $v) {
            $content = sprintf($sendtext,$v,$temp_url);
            $response_wx=$this->wxcode_model->sendmessage($content);
        }
        Universal::Output($this->config->item('request_succ'),'更新成功','');
    }
    /**
     * 检查字符串
     * @param        string       data       要更新的任务id
     * @return       sting        result['data']     返回检查过的id
     * @return       sting        result['num']      更新字段的个数
     */
    function checkstr($data){
        $result['num'] = count($data);
        $data = array_filter($data,'is_numeric');
        if (count($data)!=$result['num']) {
            Universal::Output($this->config->item('request_fall'),'','');
        }
        $data = implode(',', $data);
        $result['data'] = $data;
        return $result;
    }
    /**
     * 通过电话号码，查询用户任务情况
     * @param       string       mobile       用户的电话号码
     */
    function XgetUser(){
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $mobiles = $this->input->post('mobiles',true);
        $nnum = count($mobiles = explode(',', $mobiles));
        $snum = count($mobiles = array_filter($mobiles,'is_numeric'));
        if ($snum>10) {
            Universal::Output($this->config->item('request_fall'),'请输入少于10条的数据','');
        }
        if ($nnum!=$snum) {
            Universal::Output($this->config->item('request_fall'),'手机号码有误，请刷新界面重新输入','');
        }
        $this->load->model('center/taskverify_model');
        $result = $this->taskverify_model->xgetinfo($mobiles);
        if($result===false){
            Universal::Output($this->config->item('request_fall'),'没有找到信息','');
        }
        Universal::Output($this->config->item('request_succ'),'','',$result);
    }
    /**
     * 更新用户信息
     */
    function Xupuser(){
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $data = $this->input->post('data',true);
        $openid = $this->input->post('openid',true);
        $nnum = count($data = explode(',', $data));
        $snum = count($data = array_filter($data,'is_numeric'));
        $opnum = count($openid = explode(',',$openid));
        if ($nnum!=$snum||$snum!=$opnum) {
            Universal::Output($this->config->item('request_fall'),'数据有误，请刷新界面重新输入','');
        }
        $s_data = implode(',', $data);
        $this->load->model('center/taskverify_model');
        $result = $this->taskverify_model->xupinfo(array('m'=>$s_data,'n'=>$snum));
        if ($result==false) {
            Universal::Output($this->config->item('request_fall'),'更新失败','');
        }
        //微信推送信息
        $this->load->model('common/wxcode_model');
        $temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/task/usercenter/taskcenter&response_type=code&scope=snsapi_base&state=#wechat_redirect';
        $sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"您的信而富已经审核通过",
                    "description":"您的信而富注册成功并通过审核，请点击详情前往领取奖励",
                    "url":"%s", "picurl":""}]}}';
        foreach ($openid as $k => $v) {
            $content = sprintf($sendtext,$v,$temp_url);
            $response_wx=$this->wxcode_model->sendmessage($content);
        }
        Universal::Output($this->config->item('request_succ'),'更新成功','');
    }
}