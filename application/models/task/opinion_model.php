<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Opinion_model extends CI_Model {

	private $task_opinion	 = 'h_task_opinion';//用户评论表

	/**
	 * 
	 *插入任务评论表
	 *
	 */
	public function addopinion($wx_id,$data){

		$input = array(
			'wx_id' => $wx_id,
			'opinion_content' => $data['opinions'],
			'opinion_join_time' => time(),
			'opinion_status' => 1,
		);

		$str = $this->db->insert($this->task_opinion,$input);
		return $str;

	}


}