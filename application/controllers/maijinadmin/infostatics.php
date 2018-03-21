<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:员工控制层
 */
class Infostatics extends RA_Controller {
        
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('pagination');
    }
    /**
     * 管理数据统计表
     */
    public function statics(){
        $this->checkauto();
        $this->load->model('maijinadmin/infostatics_model','',TRUE); 
        $return = $this->infostatics_model->info_statistics($date=0);
        $data['list'] = $return;
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
        // $config['total_rows'] = $data['num'][0]['num'];
        // $this->pagination->initialize($config);
        $data['modellist'] = $_SESSION['modellist'];//获取session中的列表参数
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/infostatics', $data);
        $this->load->view('maijinadmin/common/footer', $data);
    }
    /**
     * 日期选择
     * @param   string      date    日期
     * @param   json        返回信息
     */
    public function staticda(){
        $this->checkauto();
        $date = $this->input->post('date',true);
        if(empty($date)){
            $result = array('status'=>'403','info'=>'日期未填写','url'=>'','data'=>'');
            exit();
        }
        $a = preg_match('/^\d\d\d\d-\d\d-\d\d$/',$date);
        if ($a==0) {
            $result = array('status'=>'403','info'=>'','url'=>'日期格式错误','data'=>'');
            exit();
        }
        $this->load->model('maijinadmin/infostatics_model','',TRUE); 
        $data['user'] = $this->infostatics_model->info_statistics($date);
        $data['task']['invite_u'] = $this->infostatics_model->task_count(5,$date);//邀请任务
        $data['task']['turnover'] = $this->infostatics_model->task_count(2,$date);//回收任务
        $data['task']['game'] = $this->infostatics_model->task_count(7,$date);//主线（游戏）任务
        $data['shop']['count'] = $this->infostatics_model->shop_count($date);//通化商城交易统计
        $result = array('status'=>'400','info'=>'','url'=>'','data'=>$data);
        echo json_encode($result);
    }
}

?>