<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Knowledge_model extends CI_Model {
    /**
     * 获取文章列表
     * @return       array       返回数组数据
     */
    function getlabel(){
        $sql = 'select label_id as id,label_name as name,label_icon as icon from h_article_label where label_status=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取最热门的文章
     * @param        int        type        获取文章列表的类型（0表示所有类型的文章）
     * @return       array       返回数组数据
     */
    function getmostheat($type){
        $week = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'))+86400;
        if ($type==0) {
            $where = 'b.text_recordtime='.$week.' and b.text_status=1 and a.link_status=1 and b.text_delstatus=1';
        }else{
            $where = 'a.link_lableid='.$type.' and b.text_recordtime='.$week.' 
                      and b.text_status=1 and a.link_status=1 and b.text_delstatus=1 ';
        }
        $sql = 'select b.text_id as id,b.text_name as name,b.text_jointime as time,b.text_label as label,
                b.text_clicknum as click,text_sharenum as sharenum from h_article_link as a 
                left join h_article_text as b on a.link_textid=b.text_id 
                where '.$where.' and text_clicknum>0 order by text_weekclicknum desc limit 1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取文章列表
     * @param        int        type        获取文章列表的类型（0表示所有类型的文章）
     * @param        int        keytime     时间戳（获取小于此时间的文章）
     * @param        int        sortby      通过某种方法排序 1为时间  2为点击量
     * @param        int        click       点击量（获取小于此点击量的文章）
     * @param        int        num         已经加载文章的个数
     * @return       array      返回数组数据
     */
    function getarticlelist($type,$sortby,$ltime,$num){
        switch ($sortby) {//更具排序方式不同来选择查询
            case '1':
                $type==0?$order = 'text_jointime':$order = 'b.text_jointime';
                $ltime<=0?$minum = '':$minum = $order.'<'.$ltime.' and';
                $limit = '0,20';
                break;
            case '2':
                $type==0?$order = 'text_clicknum':$order = 'b.text_clicknum';
                $minum = '';
                $limit = $num.','.($num+20);
                break;
            default:
                return false;
                break;
        }
        if ($type==0) {
            $sql = 'select text_id as id,text_name as name,text_jointime as time,text_label as label,text_clicknum as click,
                    text_sharenum as sharenum from h_article_text where '.$minum.' text_status=1 and text_delstatus=1
                    order by '.$order.' desc limit '.$limit;
        }else{
            $sql = 'select b.text_id as id,b.text_name as name,b.text_jointime as time,b.text_label as label,
                    b.text_clicknum as click,text_sharenum as sharenum from h_article_link as a left join h_article_text as b 
                    on a.link_textid=b.text_id where a.link_lableid='.$type.' and '.$minum.' b.text_status=1 and b.text_delstatus=1
                    and a.link_status=1 order by '.$order.' desc limit '.$limit;
        }
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取文章信息
     * @param     int     tid          获取的文章id
     * @return       array      返回数组数据
     */
    function getarticleinfo($tid){
        $sql = 'select text_name,text_content,text_clicknum,text_weekclicknum,text_recordtime,text_descrip as des,
                text_image as img from h_article_text where text_id='.$tid.' and text_status=1 and text_delstatus=1';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 读取文章后更新信息已经检查任务信息
     * @param     int     id          获取的文章id
     * @param     int     wx_id       获取用户的id
     * @param     int     info        文章的信息
     * @return       array      返回数组数据
     */
    function uparticleinfo($tid,$wxid,$info){
        $this->db->trans_begin();
        $week = mktime(0,0,0,date('m'),date('d')-date('w'),date('Y'))+86400;
        $insert = array(
            'record_textid' => $tid,
            'record_userid' => $wxid,
            'record_jointime' => time(),
            'record_status' => 1,
        );
        $this->db->insert('h_article_readlog',$insert);
        if ($this->db->affected_rows()!=1) {
            $this->db->trans_rollback();
            return false;
        }
        $update = array(
            'text_clicknum' => $info['text_clicknum']+1,
            'text_weekclicknum' => $info['text_weekclicknum']+1,
            'text_uptime' => time(),
        );
        if ($info['text_recordtime']<$week) {
            $update['text_weekclicknum'] = 1;
            $update['text_recordtime'] = $week;
        }
        $this->db->update('h_article_text',$update,array('text_id'=>$tid,'text_status'=>1));
        if ($this->db->affected_rows()!=1) {
            $this->db->trans_rollback();
            return false;
        }
        if ($wxid!='') {
            $result = $this->kledgetask($wxid);
        }
        if ($this->db->trans_status() === false || (isset($result)&&$result==false)){
            $this->db->trans_rollback();
             return false;
        }
        $this->db->trans_commit();
        return true;
    }
    /**
     * 检查未注册用户是否看过文章
     * @param     int     id          获取的文章id
     * @param     int     wx_id       获取用户的id
     * @return       array      返回数组数据
     */
    function checkuser($tid,$wxid){
        $sql = 'select record_textid from h_article_readlog where record_userid='.$wxid;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {//此人没有看过，可以试看一篇
            return true;
        }
        if ($result['0']['record_textid']==$tid) {//以前看过的文章
            return true;
        }
        return false;
    }
    /**
     * 搜索文章
     * @param     array     text         搜索文字
     * @return       array      返回数组数据
     */
    function seachtext($ar_text){
        $where = '';
        foreach ($ar_text as $key => $val) {
            $where .= ' and text_name like "%'.$val.'%" ';
        }
        $sql = 'select text_id as id,text_name as name,text_jointime as time,text_label as label,text_clicknum as click,
                text_sharenum as sharenum from h_article_text where text_status=1 '.$where.' 
                order by text_jointime desc limit 0,10';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        $return['info'] = $result->result_array();
        $return['num'] = $result->num_rows;
        return $return;
    }
    /**
     * 分享更新后增加分享的次数
     * @param     int     tid          获取的文章id
     * @return       array      返回数组数据
     */
    function addshare($tid){
        $sql = 'update h_article_text set text_sharenum=text_sharenum+1,text_uptime='.time().' where text_id='.$tid.' and text_status=1';
        $result = $this->db->query($sql);
        if ($this->db->affected_rows<1 || $result==false) {
            return false;
        }
        return true;
    }
    /**
     * 检查每日看知识库任务是否完成
     * @param        int        wx_id        用户的id
     * @return       bool       成功返回true失败返回false
     */
    function kledgetask($wx_id){
        $this->load->model('task/user_model');
        $str = $this->user_model->is_have_user($wx_id,',center_klegdtime');//判断用户是否登入过任务中心，不是侧插入。
        if ($str===false) {
            return false;
        }elseif($str === true){
            $klegdtime = 0;
        }else{
            $klegdtime = $str['0']['center_klegdtime'];
        }
        $ttime = strtotime(date('Y-m-d'));
        if ($klegdtime>$ttime) {//今天已经看过，直接返回
            return true;
        }
        $sql = 'select b.log_id,b.task_process from h_task_info as a left join h_task_log as b on a.task_id=b.task_id 
                and b.wx_id='.$wx_id.' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_status=1 and a.task_id=7';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {//没有任务直接返回
            return true;
        }
        $time = time();
        $result = $result->result_array();
        if ($result['0']['log_id']=='') {
            $input = array(//得到要输入的基本信息
              'wx_id' => $wx_id,
              'task_id' => 7,
              'task_jointime' => $time,
              'task_finishtime' => $time,
              'task_process' => 3,
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($input);//插入信息
            if ($return==false) {
                return false;
            }
        }else{
            if($result['0']['task_process']==2){
                $this->load->model('task/taskfinish_model');
                $str=$this->taskfinish_model->uptaskprocess($wx_id,7,3);
                if ($str==false) {
                    return false;
                }
            }
        }
        $sql = 'update h_wxuser_task set center_klegdtime='.$time.',center_updatetime='.$time.' 
                where wx_id='.$wx_id.' and center_status=1';
        $result = $this->db->query($sql);
        if ($this->db->affected_rows()!=1) {
            return false;
        }
        return true;
    }
}