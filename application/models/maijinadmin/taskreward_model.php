<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:订单管理类
 */
class taskreward_model extends CI_Model {
    // 任务奖励表
    private  $task_reward='h_task_reward';
    //任务表
    // private  $task_info='h_task_info';
    //h_admin_role
    // private $h_admin_role='h_admin_role';
    /**
     * 功能描述:获取任务奖励列表
     * 参数说明:array $data 任务奖励条件数组
     * 被引用:
     *      controllers/maijinadmin/task.php getAllreward();
     */
    public function select_reward_list($data){
        $sql = 'select reward_id,reward_bonus,reward_integral,reward_all_integral,reward_fund,reward_status from '.$this->task_reward.' order by reward_id asc limit '.$data['page'].','.$data['per_page'];
        $data['list']=$this->db->query($sql);
        $data['list'] = $this->data_result_data($data['list']);

        $sql = 'select count(reward_id) as num from '.$this->task_reward;
        $data['num']=$this->db->query($sql);

        $data['num'] = $this->data_result_data($data['num']);

        return $data;
	}

    /**
     * 功能描述:添加新奖励
     * 参数说明:array $data 任务字段数组
     * 被引用:
     *      controllers/maijinadmin/taskreward.php add_editor_reward();
     */
    public function addtaskreward($data){
        $str=$this->db->insert($this->task_reward,$data);

        if ($str === false) {
            return false;    
        }else{
            return true;
        }
    }

     /**
     * 功能描述:删除奖励
     * 参数说明:要选择奖励的id
     * 被引用:
     *      controllers/maijinadmin/taskreward.php delectreward();
     */
    public function delect_reward($id){
        $updatedate=time();
        $sql='update '.$this->task_reward.' set reward_status="-1",reward_updatetime="'.$updatedate.'" where reward_id='.$id;
        $query=$this->db->query($sql);
        if($query !== false){
            $this->db->close();
            return true;
        }else{            
            $this->db->close();
            return false;
        }
    }

    public function get_reward($id){
        $sql = 'select reward_id,reward_bonus,reward_integral,reward_all_integral,reward_fund,reward_status from '.$this->task_reward.' where reward_id='.$id;
        $query=$this->db->query($sql);
        
        $query=$this->data_result_data($query);
        if($query === false){
            return false;
        }else{            
            return $query;
        }
    }

    public function upreward($data,$id){
        
        $where = array('reward_id'=> $id);
        $str = $this->db->update($this->task_reward,$data,$where);
        if ($str) {
            return true;    
        }else{
            return false;
        }
    }

    /**
     * 功能描述：把从数据库掉出进过ci处理的数据进行判断。
     * 参数说明：调出数据的对象
     * 返回值：错误返回false，正确返回数组，无值返回空字符串
     */
    private function data_result_data($data){
        if ($data !== false) {
            if ($data->num_rows > 0) {
                $data = $data->result_array();
            }else{
                $data = array();
            }

            return $data;

        }else{
            return false;
        }
    }

	
}

?>