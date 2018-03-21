<?php
/*
 *  回收上班模块
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Merchant extends  CI_Controller{
    
    
    function  ViewMerchant(){
        $this->load->view('maijinadmin/merchant');
    }
}