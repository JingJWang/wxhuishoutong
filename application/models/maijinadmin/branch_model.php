<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:营业网点管理类
 */
class Branch_model extends CI_Model {
    //营业网点表
    private $h_branch='h_branch';
    
    function __construct(){
        parent::__construct();
    
    }
    /**
     * 功能描述:根据id获取营业网点信息
     * 参数说明:int $int 营业网点id
     * 被引用:
     *      controllers/maijinadmin/branch.php get_branch();
     */
    public function get_branch($id){
        $sql='select * from '.$this->h_branch.' where id='.$id;
        $query=$this->db->query($sql);
        if($query !== false){
            $data=$query->row_array();
            $this->db->close();
            return array('num'=>'','data'=>$data);
        }else{
            $this->db->close();
            return false;
        }        
    }
    /**
     * 功能描述:修改营业网点信息
     * 参数说明:array $data 营业网点内容数组
     * 被引用:
     *      controllers/maijinadmin/branch.php add_edit_branch();
     */
    public function add_branch($data){
        $b_time=$data['b_time'];
        $b_address=$data['b_address'];
        $b_sort=$data['b_sort'];
        $b_status=$data['b_status'];
        $adddate=date("Y-m-d H:i:s");
        $sql='insert into '.$this->h_branch.' (branch_date,branch_address,branch_joindate,status,branch_sort)values("'.$b_time.'","'.$b_address.'","'.$adddate.'","'.$b_status.'","'.$b_sort.'")';
        $query=$this->db->query($sql);
        if($query !== false){
            $this->db->close();
            return true;
        }else{            
            $this->db->close();
            return false;
        }
    }
    /**
     * 功能描述:修改营业网点信息
     * 参数说明:array $data 营业网点内容数组
     * 被引用:
     *      controllers/maijinadmin/branch.php add_edit_branch();
     */
    public function update_branch($data){
        $id=$data['id'];
        $b_time=$data['b_time'];
        $b_address=$data['b_address'];
        $b_sort=$data['b_sort'];
        $b_status=$data['b_status'];
        $updatedate=date("Y-m-d H:i:s");
        $sql='update '.$this->h_branch.' set branch_date="'.$b_time.'",branch_address="'.$b_address.'",status="'.$b_status.'",branch_sort="'.$b_sort.'",branch_lastdate="'.$updatedate.'" where id='.$id;
        $query=$this->db->query($sql);
        if($query !== false){
            $this->db->close();
            return true;
        }else{            
            $this->db->close();
            return false;
        }
    }
    /**
     * 功能描述:禁用营业网点，设置为无效     *
     * 参数说明:int $id 营业网点id
     * 被引用:
     *      controllers/maijinadmin/branch.php delete_branch();
     */
    public function delete_branch($id){
        $updatedate=date("Y-m-d H:i:s");
        $sql='update '.$this->h_branch.' set status="0",branch_lastdate="'.$updatedate.'" where id='.$id;
        $query=$this->db->query($sql);
        if($query !== false){
            $this->db->close();
            return true;
        }else{            
            $this->db->close();
            return false;
        }
    }
    /**
     * 功能描述:查询所有营业网点
     * 被引用:
     *      controllers/maijinadmin/branch.php get_branch_list();
     */
    public function get_branch_list(){
        $sql='select * from '.$this->h_branch.' order by branch_sort desc,branch_date desc';
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $data=$this->db->fetch_query($query);
                $this->db->close();
                return array('num'=>$query->num_rows(),'data'=>$data);
            }else{
                $this->db->close();
                return '0';
            }
        }else{
            $this->db->close();
            return false;
        }        
    }
}

?>