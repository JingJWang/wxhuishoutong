<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:地堆小组管理类
 */
class Group_model extends CI_Model {
    
    private $h_promotion_group='h_promotion_group';//地堆小组表
    
    private $h_admin='h_admin';//员工表
    
    /**
     * @description  地推小组列表
     * @return array 有效的小组
     */
    function getgrouplist($option){
        //标示 读取数据范围
        if($_SESSION['role_flag'] == $this->config->item('power_type_max')){
            $powertype='';
        }else{
            $userid=$_SESSION['id'];
            $powertype='(group_leader='.$userid.' or group_majordomo='.$userid.' or group_executives='.$userid.') and ';
        }
        $sql_count='select group_id from '.$this->h_promotion_group.' where '.$powertype.'  group_status=1';
        $total=$this->db->query($sql_count);
        if($total === false){
            return false;
        }
        if($total->num_rows() < 1){
            return -1;
        }
        $sql_list='select group_id,group_name,group_jointime,group_status from '
                   .$this->h_promotion_group.' where '.$powertype.'  group_status=1 limit '.$option['page'].','.$option['pagenumber'];
        $data['list']=$this->db->customize_query($sql_list);
        if($data['list'] === false){
            return false;
        }else{
            $data['total']=$total->num_rows();
            return $data;
        }        
    }    
    /**
     * @description 获取有效组长 主管 总监
     */
    function getgroupleader(){
        $leader='select  id,xingming from h_admin where power_type = 4';        
        $executives='select  id,xingming from h_admin where power_type = 5';        
        $majordomo='select  id,xingming from h_admin where power_type = 6';
        $data['leader']=$this->db->customize_query($leader);
        $data['executives']=$this->db->customize_query($executives);
        $data['majordomo']=$this->db->customize_query($majordomo);
        if(!$data['leader'] && !$data['executives'] && !$data['majordomo']){
            return false;
        }else{
            return $data;
        }
    }
    /**
     * @description 添加小组
     */
    function addgroup($data){
        $sql='insert into '.$this->h_promotion_group.' (group_name,group_leader,
              group_executives,group_majordomo,group_jointime,group_status)values(
               "'.$data['group_name'].'","'.$data['group_leader'].'","'.$data['group_executives'].'",
              "'.$data["group_majordomo"].'","'.date('Y-m-d H:i:s').'",1)';
        $result=$this->db->query($sql);
        if ($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @description 获取地推成员
     */
    function getgroupteam($data){
        $sql='select  id,xingming,group_id from h_admin where power_type = 1 and status=1 
              and (group_id=0 or group_id='.$data['group_id'].')';
        $data['userlist']=$this->db->customize_query($sql);
        if($data['userlist'] === false){
            return false;
        }else{
            return $data;
        }
    }
    /**
     * @description 修改小组内成员
     */
    function editgroupmember($data){
        $sql='update  '.$this->h_promotion_group.' set group_member="'.$data['userid'].'",group_updatetime="'.
              date('Y-m-d H:i:s').'" where group_id ='.$data['group_id'];
        $result=$this->db->query($sql);
        if($result === false){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @description修改用户 用户的所属小组
     */
    function edit_admin_groupid($data){         
        $value='';
        foreach ($data['userid'] as $userid){
           $value .='('.$userid.','.$data['group_id'].'),';
        }        
        $sql='insert  into '.$this->h_admin.' (id,group_id) values'.trim($value,',').' 
              on duplicate key update group_id=values(group_id)';
        $result=$this->db->query($sql);
        if($result === false){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @description 重置用户为初始状态
     */
    function clear_group_member($data){
        $resetvalues='';
        foreach ($data['userid'] as $userid){
            $resetvalues .='('.$userid.',0),';
        }
        $sql='update h_promotion_group set group_member=REPLACE(group_member, "'.trim($resetvalues,',').'", "") where group_id='.$data['groupid'];
        $reset_group=$this->db->query($sql);
        if($reset_group === false){
            return false;
        }
        $reset='insert  into '.$this->h_admin.' (id,group_id) values'.trim($resetvalues,',').' 
                on duplicate key update group_id=values(group_id)';
        $reset_res=$this->db->query($reset);
        if($reset_res === false){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @description 查看当前小组的信息
     */
    function group_performance($data){
        $group_sql='select group_id,group_name,group_leader,group_executives,group_majordomo,
                    group_member,group_jointime from  h_promotion_group  where group_id='.$data['group_id'];
        $groupresult=$this->db->customize_query($group_sql);
        if($groupresult === false){
            return false;
        }else{
            return $groupresult;
        }
    }
    /**
     * @description 获取管理员的信息
     */
    function group_manage_userinfo($userid){
        $sql='select xingming,power_name from h_admin where id in('.$userid.')';
        $result=$this->db->customize_query($sql);
        if($result === false){
            return false;
        }else{
            return $result;
        }
    }
    /**
     * @description 查询小组内取消 关注统计信息
     */
    function group_userperformance_info($userid){
        $start=date("Y-m-d");
        $end=date("Y-m-d",strtotime("+1 day"));
        $subsql='select count(DISTINCT subscribe_openid,user_id) as number,user_id from h_subscribe_log where 
                 user_id in('.$userid.')  and subscribe_type=1  and subscribe_jointime >="'.$start.'"  and subscribe_jointime <="'.$end.'" 
                 group by user_id';
        $data['sub']=$this->db->customize_query($subsql);
        if($data['sub'] === false){
            return false;
        }
        if($data['sub'] == '0'){
            return -1;
        }
        $unsubsql='select count(DISTINCT a.subscribe_openid,a.user_id) as number ,
                   a.user_id from h_subscribe_log a inner join h_subscribe_log b on 
                   a.subscribe_openid=b.subscribe_openid where a.user_id in ('.$userid.') 
                   and a.subscribe_jointime >= "'.$start.'" and b.subscribe_jointime 
                   <= "'.$end.'" and a.subscribe_type=1 and b.subscribe_type-1 and 
                   a.user_id !=0 group by a.user_id';
        $data['unsub']=$this->db->customize_query($unsubsql);
        if($data['unsub'] !== false){
            return $data;
        }else{
            return false;
        }
    }
    /**
     * @description  编辑当前小组
     */
    function editgroup($data){
        $group_sql='select group_id,group_name,group_leader,group_executives,group_majordomo
                     from  h_promotion_group  where group_id='.$data['group_id'];
        $groupdata['groupinfo']=$this->db->customize_query($group_sql);
        if(!$groupdata['groupinfo']){
            return false;
        }
        $groupdata['option']=$this->getgroupleader();
        if(!$groupdata['option']){
            return false;
        }else{
            return $groupdata;
        }
        
    }
    
    /**
     * @description 保存修改的小组
     */
    function save_edit_datainfo($groupdata){
        $sql='update h_promotion_group set group_name="'.$groupdata['group_name'].
             '",group_leader='.$groupdata['group_leader'].',group_majordomo='.$groupdata['group_majordomo'].
             ',group_executives='.$groupdata['group_executives'].',group_updatetime="'.date('Y-m-d H:i:s').'" 
              where group_id='.$groupdata['group_id'];
        $result=$this->db->query($sql);
        if(!$result){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @description 删除地推小组
     */
    function delgroup($groupdata){
        $sql='update h_promotion_group set group_status=-1,group_updatetime="'
              .date('Y-m-d H:i:s').'" where group_id='.$groupdata['group_id'];
        $result=$this->db->query($sql);
        if(!$result){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @description 检测小组内是否有组员
     */
    function check_grouo_isnull($groupdata){
        $sql='select group_member from h_promotion_group  where group_id='.$groupdata['group_id'];
        $result=$this->db->customize_query($sql);
        if($result === false){
            return false;
        }else{
            return $result;
        }
    }
}