<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:使用说明管理类
 */
class Instruction_model extends CI_Model {
    //使用说明表
    private $h_instruction='h_instruction';
    /**
     * 功能描述:查询所有使用说明详情
     * 被引用:
     *      controllers/maijinadmin/instruction.php get_instruction_list();
     */
    public function get_instruction_list(){
        $sql='select * from '.$this->h_instruction;
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
     * 功能描述:根据id获取使用说明详情
     * 参数说明:int $id 使用说明的id
     * 被引用:
     *      controllers/maijinadmin/instruction.php get_instruction();
     */
    public function get_instruction($id){
        $sql='select * from '.$this->h_instruction.' where id='.$id;
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
     * 功能描述:添加使用说明信息
     * 参数说明:array $data 使用说明内容数组
     * 被引用:
     *      controllers/maijinadmin/instruction.php add_edit_instruction();
     */
    public function add_instruction($data){
        $i_name=$data['i_name'];
        $i_content=$data['i_content'];
        $i_status=$data['i_status'];
        $adddate=date("Y-m-d H:i:s"); 
        $sql="insert into ".$this->h_instruction." (instruction_name,instruction_content,instruction_joindate,status)values('".$i_name."','".$i_content."','".$adddate."','".$i_status."')";           
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
     * 功能描述:修改使用说明信息
     * 参数说明:array $data 使用说明内容数组
     * 被引用:
     *      controllers/maijinadmin/instruction.php add_edit_instruction();
     */
    public function update_instruction($data){
        $id=$data['id'];
        $i_name=$data['i_name'];
        $i_content=$data['i_content'];
        $i_status=$data['i_status'];  
        $updatedate=date("Y-m-d H:i:s");
        $sql="update ".$this->h_instruction." set instruction_name='".$i_name."',instruction_content='".$i_content."',status='".$i_status."',instruction_lastdate='".$updatedate."' where id=".$id;
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
     * 功能描述:禁用使用说明，设置为无效
     * 参数说明:int $id 使用说明的id
     * 被引用:
     *      controllers/maijinadmin/instruction.php delete_instruction();
     */
    public function delete_instruction($id){
        $updatedate=date("Y-m-d H:i:s");
        $sql='update '.$this->h_instruction.' set status="0",instruction_lastdate="'.$updatedate.'" where id='.$id;
        $query=$this->db->query($sql);
        if($query !== false){
            $this->db->close();
            return true;
        }else{
            $this->db->close();
            return false;
        }      
    }
}

?>