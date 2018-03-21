<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:订单管理类
 */
class taskopinion_model extends CI_Model {
    // 任务评论表
    private $task_opinion='h_task_opinion';
    //用户表
    private $wxuser='h_wxuser';
    //用户任务表
    private $wxuser_task='h_wxuser_task';
    //奖励表
    private $task_reward = 'h_task_reward';
    //等级表
    private $wxuser_level = 'h_wxuser_level';
    //h_admin_role
    // private $h_admin_role='h_admin_role';
    /**
     * 功能描述:获取用户评论列表
     * 参数说明:array $data 任务奖励条件数组
     * 被引用:
     *      controllers/maijinadmin/taskopinion.php getAllopinion();
     */
    public function select_opinion_list($data){
        $sql = 'select a.opinion_id,a.opinion_join_time,a.opinion_status,b.wx_name,b.wx_mobile from '.$this->task_opinion.' as a left join '.$this->wxuser.' as b on a.wx_id=b.wx_id order by a.opinion_join_time desc limit '.$data['page'].','.$data['per_page'];
        $data['list']=$this->db->query($sql);
        $data['list'] = $this->data_result_data($data['list']);

        $sql = 'select count(opinion_id) as num from '.$this->task_opinion;
        $data['num']=$this->db->query($sql);

        $data['num'] = $this->data_result_data($data['num']);

        return $data;
	}
    //获取某条评论信息
    public function get_opinioninfo($id){
        $sql = 'select a.opinion_id,a.wx_id,a.reward_id,a.opinion_status,a.opinion_content,a.opinion_join_time,b.wx_name,b.wx_mobile from '.$this->task_opinion.' as a,'.$this->wxuser.' as b where a.opinion_id='.$id.' and a.wx_id=b.wx_id';
        
        $opinions=$this->db->query($sql);
        if ($opinions===false) {
            return false;
        }
        $opinions = $this->data_result_data($opinions);

        return $opinions;
    }

    public function adoption_opinion($op_id,$reward_id){

        $op_info = $this->get_opinioninfo($op_id);//评论信息

        $sql = 'select reward_bonus,reward_integral,reward_fund from '.$this->task_reward.' where reward_id='.$reward_id.' and reward_status=1';
        $rewards=$this->db->query($sql);
        if ($rewards===false) {
            return false;
        }

        //获取用户信息
        $sql = 'select center_integral,center_all_integral,center_bonus,center_fund,level_num from '.$this->wxuser_task.' where wx_id='.$op_info[0]['wx_id'].' and center_status=1';//获取用户的信息
        $userinfo = $this->db->query($sql)->result_array();

        $rewards = $this->data_result_data($rewards);
        $center_integral = $rewards[0]['reward_integral']+$userinfo[0]['center_integral'];
        $center_all_integral = $rewards[0]['reward_integral']+$userinfo[0]['center_all_integral'];
        $center_bonus = $rewards[0]['reward_bonus']+$userinfo[0]['center_bonus'];
        $center_fund = $rewards[0]['reward_fund']+$userinfo[0]['center_fund']-$rewards[0]['reward_bonus'];
        
        if ($center_fund<0) {//基金如果没有，提示没有足够的基金
            return 'no_fund';
        }
        $up_data = array();

        if ($rewards[0]['reward_integral'] > 0) {

            $sql = 'select level_num,level_name,level_integral,level_img from '.$this->wxuser_level.' where level_status=1 order by level_num asc';//获取等级信息
            $levels = $this->db->query($sql)->result_array();
            $all_level = count($levels);

            $up_data['center_integral'] = $center_integral;//更新用户积分字段
            $up_data['center_all_integral'] = $center_all_integral;

            foreach ($levels as $k => $v) {
                if ($k<$all_level-1) {
                    if ($levels[$k+1]['level_integral']>$center_all_integral && $center_all_integral>=$v['level_integral'] && $v['level_num']!=$userinfo[0]['level_num']) {
                        $level_num = $v['level_num'];
                        $up_data['level_num'] = $level_num;//更新用户等级字段
                        // $return_data['is_level'] = 1;//表示用户已经升级了
                        break;
                    }
                }else{
                    if ($center_all_integral>=$v['level_integral'] && $v['level_num']!=$userinfo[0]['level_num']) {
                        $level_num = $v['level_num'];
                        $up_data['level_num'] = $level_num;//更新用户等级字段
                    }
                }
            }
            var_dump($up_data['level_num']);exit();
        }

        $up_data['center_integral'] = $center_integral;
        $up_data['center_all_integral'] = $center_all_integral;
        $up_data['center_bonus'] = $center_bonus;
        $up_data['center_fund'] = $center_fund;
        $up_data['center_updatetime'] = time();//更新用户更新时间字段

        $where = 'wx_id = '.$op_info[0]['wx_id'].' and center_status = 1';
        $str = $this->db->update($this->wxuser_task,$up_data,$where);//更新用户的信息。
        if (!$str) {
            return false;
        }

        $up_opinion = array(
            'reward_id' => $reward_id,
            'opinion_update_time' => time(),
            'opinion_status' => 2,
        );
        $where = 'opinion_id = '.$op_id;
        $str = $this->db->update($this->task_opinion,$up_opinion,$where);//更新用户的信息。
        if (!$str) {
            return false;
        }

        return true;
        // var_dump($center_integral);
        // var_dump($rewards);
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