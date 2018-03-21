<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:订单管理类
 */
class task_model extends CI_Model {
    // 任务奖励表
    private  $task_reward='h_task_reward';
    //任务表
    private  $task_info='h_task_info';
    //h_admin_role
    private $h_admin_role='h_admin_role';
    /**
     * 功能描述:获取任务列表
     * 参数说明:array $data 任务条件数组
     * 被引用:
     *      controllers/maijinadmin/task.php getAlltask();
     */
    function select_task_list($data){
        $sql = 'select task_id,info_name,reward_id,reward_content,reward_num,task_limit_other,task_type,task_level,task_content,task_url,task_share_url,task_share,task_invite_u,task_invite_m,task_sign,task_turnover,task_limit_time,task_status from '.$this->task_info.' order by task_level asc limit '.$data['page'].','.$data['per_page'];
        $data['list']=$this->db->query($sql)->result_array();

        $sql = 'select count(task_id) as num from '.$this->task_info;
        $data['num']=$this->db->query($sql)->result_array();

        if($data['list'] === false){
            return false;
        }else{            
            return $data;
        }
	}

	/**
     * 功能描述:获取全部任务
     * 被引用:
     *      controllers/maijinadmin/task.php gettask();
     */
	function select_all_task(){
		$sql = 'select task_id,info_name,task_level from '.$this->task_info.' where task_status=1 order by task_level asc';
		$data['task']=$this->db->query($sql)->result_array();
		if($data['task'] === false){
            return false;
        }else{            
            return $data['task'];
        }
	}

	/**
     * 功能描述:获取全部奖励
     * 被引用:
     *      controllers/maijinadmin/task.php addtask();
     */
	function rewards(){
		$sql = 'select reward_id,reward_bonus,reward_integral,reward_all_integral,reward_fund from '.$this->task_reward.' where reward_status=1';
        $reward=$this->db->query($sql)->result_array();
        if($reward === false){
            return false;
        }else{            
            return $reward;
        }
	}
	/**
     * 功能描述:添加新任务
     * 参数说明:array $data 任务字段数组
     * 被引用:
     *      controllers/maijinadmin/task.php addtask();
     */
	function addtask($data){
		$str=$this->db->insert($this->task_info,$data);
		if (!$str) 	return false;

        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        $str = $this->zredis->_redis->set('taskinfouptime',time());
        if ($str===true) {
            return true;
        }else{
            return false;
        }
    
	}

	/**
     * 功能描述:禁用某个任务
     *传入参数：任务的id
     * 被引用:
     *      controllers/maijinadmin/task.php delecttask();
     */
	function delect_task($id){
		$updatedate=time();
        $sql='update '.$this->task_info.' set task_status="-1",task_updatetime="'.$updatedate.'" where task_id='.$id;
        $query=$this->db->query($sql);
        if($query === false){
            $this->db->close();
            return false;
        }
        
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        $str = $this->zredis->_redis->set('taskinfouptime',time());
        if ($str===true) {
            $this->db->close();
            return true;
        }else{
            $this->db->close();
            return false;
        }

	}

	/**
     * 功能描述:选择一个任务
     *传入参数：任务的id
     * 被引用:
     *      controllers/maijinadmin/task.php selecttask();
     */
	function select_task($id){

		$sql = 'select task_id,info_name,reward_id,reward_content,reward_num,task_limit_other,task_type,task_level,task_content,task_url,task_share_url,task_share,task_invite_u,task_invite_m,task_sign,task_turnover,task_limit_time,task_status from '.$this->task_info.' where task_id='.$id;
        $query=$this->db->query($sql)->result_array();
        
        if($query === false){
            return false;
        }else{            
            return $query;
        }

	}

	/**
     * 功能描述:获得权重低于自己的权限
     */
    public function get_role($data){
        $sql='select role_id, role_name from '.$this->h_admin_role.' where role_status=1 and role_weight<' . $data['role_weight'] . 'and role_flag=' . $data['role_flag'];
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $data=$this->db->fetch_query($query);
                $this->db->close();
                return $data;
            }else{
                $this->db->close();
                return '-1';
            }
        }else{
            $this->db->close();
            return false;
        }
    }
    /**
     * 功能描述:获得所有权限
     */
    public function get_role_all(){
        $sql='select role_id, role_name from '.$this->h_admin_role.' where role_status=1';
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $data=$this->db->fetch_query($query);
                $this->db->close();
                return $data;
            }else{
                $this->db->close();
                return '-1';
            }
        }else{
            $this->db->close();
            return false;
        }
    }

    public function updatetask($data,$id){

    	$where = array('task_id'=> $id);
    	$str = $this->db->update($this->task_info,$data,$where);

    	if (!$str) return false;

        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
        $str = $this->zredis->_redis->set('taskinfouptime',time());
        if ($str===true) {
            return true;
        }else{
            return false;
        }

    }
	
}

?>