<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:员工控制层
*/
class statistics extends CI_Controller {
    
     function __construct(){
         parent::__construct();
         $this->load->database();
         $this->load->model('maijinadmin/statistics_model');
         
     }   
    
     function viewstatistics(){         
         $orderdata=$this->statistics_model->order_statistics_view();   
          var_dump($orderdata);
         $wxuserdata=$this->statistics_model->wxuser_statistics_view();
          var_dump($wxuserdata);
          $admindata=$this->statistics_model->admin_statistics_view();
          var_dump($admindata);
          $voucherdata=$this->statistics_model->voucher_statistics_view();
          var_dump($voucherdata);
     }
    
    
}