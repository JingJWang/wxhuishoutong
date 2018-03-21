<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:代金券管理类
 */
class voucher_model extends CI_Model {
    //代金券表
    private $h_voucher='h_voucher';
    /**
     * 功能描述:查询所有的代金券
     * 被引用:
     *      controllers/maijinadmin/voucher.php select_voucher();
     */
    public function select_voucher(){
        $sql='select * from '.$this->h_voucher.' where status=1';
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
    /**
     * 功能描述:修改代金券的金额
     * 参数说明:array $data 代金券内容数组
     * 被引用:
     *      controllers/maijinadmin/voucher.php update_voucher();
     */
    public function update_voucher($data){
        $sql='update '.$this->h_voucher.' set voucher_pic="'.$data['voucher_pic'].'",voucher_day="'.$data['voucher_day'].'" where id='.$data['id'];        
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
     * 功能描述:根据id查询代金券信息
     * 参数说明:int $id 代金券id
     * 被引用:
     *      controllers/maijinadmin/voucher.php get_voucher_id();
     */
    public function get_voucher_id($id){
        $sql='select * from '.$this->h_voucher.' where status=1 and id='.$id;
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
}

?>