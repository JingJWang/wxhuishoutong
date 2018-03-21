<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 功能描述:订单控制层
 */

class Order extends RA_Controller{    
    
   function __construct(){
       parent::__construct();
       $this->load->database();
       $this->load->model('maijinadmin/order_model');
       $this->load->library('pagination');
   }
   /*
    * 订单列表
    */
   function select_order_list(){             
        $this->checkauto();
		$orderoption['page'] = $this->uri->segment(4,0);
		$orderoption['per_page'] = $this->config->item('PAGENUM_BACK');
		$orderdata=$this->order_model->select_order_list($orderoption);
        $data['orderlist'] = $orderdata['list'];
        $data['ordernum'] = $this->order_model->orderstatistics_num();
        $data['ordermonths'] = $this->order_model->orderstatistics_months();   
        $data['orderweek'] = $this->order_model->orderstatistics_week();     
        if ($data['orderlist'] === false && $data['orderday'] === false 
            && $data['ordermonths'] === false) {
                $this->load->view('exception/busy');
                $this->output->_display();
                die();
        } else {
            //数据  
            $data['standard_product']=$this->config->item('standard_product');
            $data['clothesnum'] = $this->config->item('ordertype_clothes');
            $data['modellist'] = $_SESSION['modellist'];
            $data['leftmenu'] = '订单管理';
            //分页
            $config['use_page_numbers'] = FALSE;
            $config['base_url'] = '/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
            $config['total_rows'] = $orderdata['number'];
            $this->pagination->initialize($config);
            $data['page'] = $this->pagination->create_links();
            //页面
            $this->load->view('maijinadmin/common/header', $data);
            $this->load->view('maijinadmin/order', $data);
            $this->load->view('maijinadmin/common/footer');
        }        
   }
   /**
    * 功能描述:订单搜素
    */
   function searchordre(){
       $option['page'] = $this->uri->segment(4,0);
       $option['per_page'] = $this->config->item('PAGENUM_BACK');
       $option['keyword'] = $this->input->get('keyword',true);
       $data = $this->order_model->searchorder($option);
       if ($data === false) {
           $message = array('status' => 3000, 'info' => '查询失败!');
           echo json_encode($message);
           exit();
       } else {
           if ($data == '-1') {
               $message = array('status' => 1050, 'info' => '没有查询到符合条件的记录!');
               echo json_encode($message);
               exit();
           } else {
               $config['use_page_numbers'] = FALSE;
               $data['clothesnum'] = $this->config->item('ordertype_clothes');
               $config['base_url'] ='/index.php/' . $this->router->fetch_directory() . '/' . $this->router->fetch_class() . '/' . $this->router->fetch_method();//获取控制器.
               $config['total_rows'] = $data['sum'];
               $this->pagination->initialize($config);
               $data['page'] = $this->pagination->create_links();
               $data['status'] = '1000';
               echo json_encode($data);
           }
       }   
   }
}

?>