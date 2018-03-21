<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Everdaydata extends CI_Controller {
    
    /**
     * 每天统计的数据
     */
    function select(){
        if (time()<strtotime(date('Y-m-d'))+24*3600-2) {
            exit();
        }
        $this->load->model('polling/everdaydata_model');
        $every = $this->everdaydata_model->getdata();
    }
}