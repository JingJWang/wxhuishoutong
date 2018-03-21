<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Querys extends CI_Controller {
    /**
     * 奢侈品列表
     * @return 成功返回 json 商品列表 | 失败返回 json 原因
     */
    function luxurys(){
       $this->load->model('weixin/querys_model');
       $lists = $this->querys_model->luxurys();
       if ($lists===false) {
           $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->querys_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $response=array('status'=>$this->config->item('request_succ'),
                'msg'=>'','url'=>'','data'=>$lists);
        echo json_encode($response);exit;
    }
    /**
     * 奢侈品具体品牌列表
     * @param  int  id  奢侈品id
     * @return 成功返回 json 商品列表 | 失败返回 json 原因
     */
    function luxurybrand(){
        if(empty($this->input->post('id',true)) || 
                !is_numeric($this->input->post('id',true))){
            $response=array('statuc'=>$this->config->item('request_fall'),
                    'msg'=>'非法请求!','url'=>'','data'=>'');
            exit(json_encode($response));
        }
        $this->load->model('weixin/querys_model');
        $this->querys_model->luxuryid = $this->input->post('id',true);
        $lists = $this->querys_model->luxurybrand();
        if ($lists===false) {
           $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>$this->querys_model->msg,'url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $response=array('status'=>$this->config->item('request_succ'),
                'msg'=>'','url'=>'','data'=>$lists);
        echo json_encode($response);exit;
    }
    /**
     * 奢侈品具体品牌搜索
     * @param  int  id  奢侈品id
     * @return 成功返回 json 商品列表 | 失败返回 json 原因
     */
    function seachbrand(){
        $text = $this->input->post('text',true);
        if(empty($text)){
            $response=array('status'=>$this->config->item('request_fall'),
                    'msg'=>'','url'=>'','data'=>'');
            echo json_encode($response);exit;
        }
        $str_key=trim($text,' ');
        $this->load->model('weixin/querys_model');
        $this->querys_model->arr_key=$this->SplitWord($str_key);
        $lists = $this->querys_model->seachbrand();
        if($lists===false){
            $response=array('status'=>'3000','msg'=>'','url'=>'','data'=>0);
            echo json_encode($response);exit;
        }
        $response=array('status'=>$this->config->item('request_succ'),
                'msg'=>'','url'=>'','data'=>$lists);
        echo json_encode($response);exit;

    }
    /**
     * 中文二元分词 编码utf-8
     */
    function SplitWord($str){
        $cstr = array();
        $search = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`",
                "^", "(", ")", "?", "-", "\t", "\n", "'", "<", ">",
                "\r", "\r\n", "{1}quot;", "&", "%", "#", "@", "+",
                "=", "{", "}", "[", "]", "：", "）", "（", "．", "。",
                "，", "！", "；", "“", "”", "‘", "’", "［", "］", "、",
                "—", "　", "《", "》", "－", "…", "【", "】",);
        $str = str_replace($search, " ", $str);
        preg_match_all("/[a-zA-Z]+/", $str, $estr);
        preg_match_all("/[0-9]+/", $str, $nstr);
        $str = preg_replace("/[0-9a-zA-Z]+/", " ", $str);
        $str = preg_replace("/\s{2,}/", " ", $str);
        $str = explode(" ", trim($str));
        foreach ($str as $s) {
            $l = strlen($s);
            $bf = null;
            for ($i= 0; $i< $l; $i=$i+3) {
                $ns1 = $s{$i}.$s{$i+1}.$s{$i+2};
                if (isset($s{$i+3})) {
                    $ns2 = $s{$i+3}.$s{$i+4}.$s{$i+5};
                    if (preg_match("/[\x80-\xff]{3}/",$ns2)) $cstr[] = $ns1.$ns2;
                } else if ($i == 0) {
                    $cstr[] = $ns1;
                }
            }
        }
        $estr = isset($estr[0])?$estr[0]:array();
        $nstr = isset($nstr[0])?$nstr[0]:array();
        return array_merge($nstr,$estr,$cstr);
    }
    /**
     * 奢侈品具体品牌搜索
     * @param  string  number  订单号
     * @return 成功返回 json 商品列表 | 失败返回 json 原因
     */
    function orderSeach(){
        $num = $this->input->post('number',true);
        if(empty($num)){
            $response=array('statuc'=>$this->config->item('request_fall'),
                    'msg'=>'请输入订单号','url'=>'','data'=>'');
            exit(json_encode($response));
        }
        if(!ctype_alnum($num)){
            $response=array('statuc'=>$this->config->item('request_fall'),
                    'msg'=>'请输入正确的格式','url'=>'','data'=>'');
            exit(json_encode($response));
        }
        $this->load->model('weixin/querys_model');
        $this->querys_model->str_number = $num;
        $lists = $this->querys_model->orderSearch();
        if ($lists === false || $lists['0']['status']=='0') {
            $response=array('statuc'=>$this->config->item('request_fall'),
                    'msg'=>$this->querys_model->msg,'url'=>'','data'=>'');
            exit(json_encode($response));
        }
        switch ($lists['0']['status']) {
            case '1':
                $lists['0']['status_c']='未上架';
                break;
            case '2':
                $lists['0']['status_c']='已上架';
                break;
            case '3':
                $lists['0']['status_c']='已卖出';
                break;
            case '4':
                $lists['0']['status_c']='已过期';
                break;
            default:
                break;
        }
        $response=array('statuc'=>$this->config->item('request_succ'),'msg'=>'','url'=>'','data'=>$lists);
        echo json_encode($response);
    }
}