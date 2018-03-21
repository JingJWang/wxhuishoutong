<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Service extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->model('appsunny/reg_model');
	}		
	
	/* 
	 *  功能 :  辅助函数.
	 */
	 
	private function get_child_array($f_data,$f_id){
		$children = array();
		$index = 1;
		foreach($f_data as $k => $v){
			if ($v['fid'] == $f_id){
				$children = array_merge($children,array($index=>$v));
				$index++;				
			}
		}
		return $children;
	}
	
	/* 
	 *  功能 : 等待报价页面产品类型和筛选条件.
	 */
	public function filter(){		
		//获取产品父类数据.父类编号为0
		$f_data = $this->reg_model->get_product_type();
		if (!$f_data){
			$result = array(
				'status' => $this->config->item('app_get_data_fail'),
				'msg'    => $this->lang->line('app_get_data_fail'),
				'data'   => '',
			);
			echo json_encode($result);
			exit();				
		}
		$opend = array();
		$index = 1;
		foreach ($f_data as $k => $v){
			if ($v['fid'] == '0'){
				$temp_array = array(
					'id' =>$v['id'],
					'name' => $v['name'],
					'fid' => 0,
					'children' => $this->get_child_array($f_data,$v['id']),
				);
			$opend = array_merge($opend,array($index=>$temp_array));
			$index++;	
			}						
		}
		$filter_enable = $this->config->item('cooperator_filter_enable');
		$index = 0;
		$filter = array();
		foreach($filter_enable as $k => $v){
			$temp = array(
				'id' => $k,
				'describe' => $v
			);
			$filter = array_merge($filter,array($index=>$temp));
			$index++;
		}
		$filter_sort = $this->config->item('cooperator_filter_sort');
		$index = 0;
		$sort = array();
		foreach ($filter_sort as $k => $v){
		    $temp = array(
		        'id' => $k,
		        'describe' => $v,
		    );
		    $sort = array_merge($sort,array($index=>$temp));
		    $index++;
		}
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg'    => $this->lang->line('app_success'),
			'data'   => array(
				'type' => $opend,
				'filter' => $filter,
			    'sort' => $sort,
			),
		);
		echo json_encode($result);
		exit();			
	}
	
	/* 
	 *  功能 : 取消订单原因.
	 */
	public function reason(){
		$data_item = $this->config->item('cooperator_cancel_option');
		$index = 0;
		$data = array();
		foreach($data_item as $k => $v){
			$temp = array(
				'id' => $k,
				'describe' => $v,
			);
			$data = array_merge($data,array($index=>$temp));
		}		
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg'    => $this->lang->line('app_success'),
			'data'   => $data,
		);
		echo json_encode($result);
		exit();						
	}	
	
	/* 功能 : 评价描述.
	 *
	 */
	public function describe(){
	    // POST请求.
	    if ($this->input->server('REQUEST_METHOD') != "POST"){
	        $result = array(
	            'status' => $this->config->item('app_req_method_err'),
	            'msg' => $this->lang->line('app_req_method_err'),
	            'data' => '',
	        );
	        echo json_encode($result);
	        exit();
	    }
	    // 获取参数
	    $source = $this->input->post('source');
	    $source_len = ($source == '-1' || $source == '4');
	    if (!$source_len){
	        $result = array(
	            'status' =>$this->config->item('app_param_illegal'),
	            'msg' => $this->lang->line('app_param_illegal'),
	            'data'=>'',
	        );
	        echo json_encode($result);
	        exit();	        
	    }
	    if ($source == '-1'){
	        $data_item = $this->config->item('cooperator_comment_cancel');
	    }
	    else{
		    $data_item = $this->config->item('cooperator_comment_option');
	    }
		$index = 0;
		$data = array();
		foreach($data_item as $k => $v){
			$temp = array(
				'id' => $k,
				'describe' => $v,
			);
			$data = array_merge($data,array($index=>$temp));
		}		
		$result = array(
			'status' => $this->config->item('app_success'),
			'msg'    => $this->lang->line('app_success'),
			'data'   => $data,
		);
		echo json_encode($result);
		exit();			
	}	
		
}

/* End of file service.php */
/* Location: ./application/controllers/cooperation/service.php */
