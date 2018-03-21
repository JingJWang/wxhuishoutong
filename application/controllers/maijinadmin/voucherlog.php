<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:代金券日志控制层
 */
class Voucherlog extends RA_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('maijinadmin/voucherlog_model','',TRUE);
        $this->load->library('pagination');
    }
    
    /**
     * @description 现金卷列表
     */
    function voucher_list(){
        $this->checkauto();
        $option['page'] = $this->uri->segment(4,0);
        $option['pagenumber'] = $this->config->item('PAGENUM_BACK');
        $voucherdata=$this->voucherlog_model->voucher_list($option);
        $voucherday=$this->voucherlog_model->voucherstatistics_week();
        $voucherall=$this->voucherlog_model->vouchertatistics_num();
        //分页配置
        $config['use_page_numbers'] = FALSE;
        $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
        $config['total_rows'] = $voucherdata['number'];
        $this->pagination->initialize($config);
        $data['page'] = $this->pagination->create_links();
        //展示数据
        $data['modellist'] = $_SESSION['modellist'];
        $data['voucherlist']=$voucherdata['list'];
        $data['weekday']=$voucherday;
        $data['voucherall']=$voucherall;
        //展示模板
        $this->load->view('maijinadmin/common/header', $data);
        $this->load->view('maijinadmin/coupon', $data);
        $this->load->view('maijinadmin/common/footer');
    }
    /**
     * @description 现金卷搜素
     */
    function searchvoucher(){
        $option['page'] = $this->uri->segment(4,0);
        $option['pagenumber'] = $this->config->item('PAGENUM_BACK');
        $option['keyword'] = $this->input->get('keyword',true);
        $voucherdata= $this->voucherlog_model->searchvoucher($option);
        if ($voucherdata === false) {
            $message = array('status' => 3000, 'info' => '查询失败!');
            echo json_encode($message);
            exit();
        } else {
            if ($voucherdata == '-1') {
                $message = array('status' => 1050, 'info' => '没有查询到符合条件的记录!');
                echo json_encode($message);
                exit();
            } else {
                //分页配置
                $config['use_page_numbers'] = FALSE;
                $config['base_url'] ='/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
                $config['total_rows'] = $voucherdata['number'];
                $this->pagination->initialize($config);
                //展示数据
                $data['vouchertype']=$this->config->item('vouchertype');
                $data['voucherstatus']=$this->config->item('voucherstatus');
                $data['page'] = $this->pagination->create_links();
                $data['voucherlist']=$voucherdata['list'];
                $data['status'] = '1000';
                echo json_encode($data);
            }
        }
    }
}
