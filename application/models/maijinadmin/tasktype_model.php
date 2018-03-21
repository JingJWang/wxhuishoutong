<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:订单管理类
 */
class tasktype_model extends CI_Model {
    // 任务奖励表
    // private  $task_reward='h_task_reward';
    //任务表
    private  $task_type='h_task_type';

    /**
     * 获取所有任务类型信息
     * @param   array       $data   分页信息
     * @return  array       $data   任务类型列表
     */
    function select_task_type($data){

        $sql = 'select count(type_id) as num from '.$this->task_type;
        $data['num']=$this->db->query($sql)->result_array();

        $sql = 'select type_id,type_num,task_id,task_process,task_jointime,task_uptime,task_status from '.$this->task_type.' order by type_num asc limit '.$data['page'].','.$data['per_page'];
        $result=$this->db->query($sql);
        if ($result->num_rows<=0) {
            return 0;
        }
        $data['list'] = $result->result_array();
        return $data;
    }
    /**
     * 获取所有任务类型信息
     * @param   int       id      类型id
     * @return  array      data   任务类型列表
     */
    function select_type($id){
        $sql = 'select type_id as id,type_num as num,task_id as tid,task_process as text,task_jointime as jtime,task_uptime as utime,task_status as status from '.$this->task_type.' where type_id='.$id;
        $result=$this->db->query($sql);
        
        if($result->num_rows <= 0){
            return 0;
        }else{            
            return $result->result_array();
        }
    }
    /**
     * 插入任务类型信息
     * @param   array       $data   插入表的信息
     * @return  bool        $str    返回布尔值   
     */
    function addtasktype($data){
        $inset = array(
            'type_num' => $data['type'],
            'task_id' => $data['taskid'],
            'task_process' => $data['i_content'],
            'task_status' => $data['i_status'],
            'task_jointime' => time(),
        );
        $str=$this->db->insert($this->task_type,$inset);
        if (!$str)  return false;
        return $str;
    }
    /**
     * 插入任务类型信息
     * @param   array       $data   插入表的信息
     * @return  bool        $str    返回布尔值   
     */
    function uptypetask($data){
        $updata = array(
            'type_num' => $data['type'],
            'task_id' => $data['taskid'],
            'task_process' => $data['i_content'],
            'task_status' => $data['i_status'],
            'task_uptime' => time(),
        );
        $where = array('type_id'=> $data['id']);
        $str = $this->db->update($this->task_type,$updata,$where);
        if (!$str)  return false;
        return $str;
    }
	/**
     * 功能描述:禁用某个任务
     *传入参数：任务的id
     * 被引用:
     *      controllers/maijinadmin/task.php delecttask();
     */
	function delect_type($id){
		$updatedate=time();
        $sql='update '.$this->task_type.' set task_status="-1",task_uptime="'.$updatedate.'" where type_id='.$id;
        $query=$this->db->query($sql);
        if($query === false){
            $this->db->close();
            return false;
        }
	}
}

?>