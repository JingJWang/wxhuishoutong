<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:订单管理类
 */
class tasklevel_model extends CI_Model {
 //    // 任务等级
    private  $wxuser_level='h_wxuser_level';

    /**
     * 功能描述:获取任务奖励列表
     * 参数说明:array $data 任务奖励条件数组
     * 被引用:
     *      controllers/maijinadmin/task.php getAllreward();
     */
    public function select_level_list($data){
        $sql = 'select level_id,level_num,level_name,level_integral,level_img,level_status from '.$this->wxuser_level.' order by level_num asc limit '.$data['page'].','.$data['per_page'];
        $data['list']=$this->db->query($sql);
        $data['list'] = $this->data_result_data($data['list']);

        $sql = 'select count(level_id) as num from '.$this->wxuser_level;
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
    public function addlevel($data){
        $str=$this->db->insert($this->wxuser_level,$data);

        if ($str === false) {
            return false;    
        }else{
            return true;
        }
    }

     /**
     * 功能描述:删除等级
     * 参数说明:要选择奖励的id
     * 被引用:
     *      controllers/maijinadmin/taskreward.php delectreward();
     */
    public function delect_level($id){
        $updatedate=time();
        $sql='update '.$this->wxuser_level.' set level_status="-1",level_updatetime="'.$updatedate.'" where level_id='.$id;
        $query=$this->db->query($sql);
        if($query !== false){
            $this->db->close();
            return true;
        }else{            
            $this->db->close();
            return false;
        }
    }

    public function get_level($id){
        $sql = 'select level_id,level_num,level_name,level_integral,level_img,level_status from '.$this->wxuser_level.' where level_id='.$id;
        $query=$this->db->query($sql);
        
        $query=$this->data_result_data($query);
        if($query === false){
            return false;
        }else{            
            return $query;
        }
    }

    public function uplevel($data,$id){
        
        $where = array('level_id'=> $id);
        $str = $this->db->update($this->wxuser_level,$data,$where);
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