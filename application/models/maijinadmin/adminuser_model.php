<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:员工管理类
 */
class Adminuser_model extends CI_Model {
    
    //员工表
    private $h_admin='h_admin';
    //代金券日志表
    private $h_voucher_log='h_voucher_log';
    //订单表
    private $h_order='h_order';
    //粉丝表
    private $h_wxuser='h_wxuser';
    //h_admin_role
    private $h_admin_role='h_admin_role';
    
    function __construct(){
        parent::__construct();
    }
    
    /**
     * 功能描述:根据员工的id查询员工的信息
     */
    public function get_admin_user($id){
        $sql='select * from '.$this->h_admin.' where id='.$id;
        $query=$this->db->query($sql);        
        if($query !== false){
            $data=$query->row_array();
            $this->db->close();
            return $data;
        }else{
            $this->db->close();
            return false;
        } 
    }
    
    /**
     * 功能描述:添加员工
     */
    public function add_admin_user($data){
        
        $adddate=date("Y-m-d H:i:s");    
        
        $sql='insert into '.$this->h_admin.'(xingming,name,password,maile,mobile,address,pay_type,power_type,power_name,adddate,status)
            values("'.$data['xingming'].'","'.$data['name'].'","'.$data['password'].'","'.$data['maile'].'","'.$data['mobile'].'"
                ,"'.$data['address'].'","'.$data['pay_type'].'","'.$data['power_type'].'","'.$data['power_name'].'"
                    ,"'.$adddate.'","'.$data['status'].'")';
        $query=$this->db->query($sql);
        if($query !== false){
            //生成渠道二维码
            $id=$this->db->insert_id();
            return $id;
        }else{            
            $this->db->close();
            return false;
        }        
    }
    
    public function update_admin_wxcode($id,$imginfo){
        $filename='./wxcode/'.$id.'.jpg';
        $local_file=fopen($filename,'w');
        if(false!==$local_file){
            if(false!==fwrite($local_file,$imginfo['body'])){
                fclose($local_file);
            }
        }
        $wxcode_path=str_replace('./','',$filename);
        $update_sql='update '.$this->h_admin.' set weixin_code="'.$wxcode_path.'" where id='.$id;
        $query_update=$this->db->query($update_sql);
        if($query_update !== false){
            $this->db->close();
            return true;
        }else{
            $this->db->close();
            return false;
        }
    }
    /**
     * 功能描述:修改员工
     */
    public function update_admin_user($data,$pwdupdate){             
       
        $updatedate=date("Y-m-d H:i:s");
        
        if($pwdupdate===true){
            $sql='update '.$this->h_admin.' set xingming="'.$data['xingming']
            .'",name="'.$data['name'].'",password="'.$data['password'].'",maile="'.$data['maile'].'",mobile="'.$data['mobile']
            .'",address="'.$data['address'].'",pay_type="'.$data['pay_type'].'",power_type="'.$data['power_type'].'",power_name="'.$data['power_name']
            .'",lastdate="'.$data['updatedate'].'",status="'.$data['status'].'" where id='.$data['id'];            
        }else{
            $sql='update '.$this->h_admin.' set xingming="'.$data['xingming']
            .'",name="'.$data['name'].'",maile="'.$data['maile'].'",mobile="'.$data['mobile']
            .'",address="'.$data['address'].'",pay_type="'.$data['pay_type'].'",power_type="'.$data['power_type'].'",power_name="'.$data['power_name']
            .'",lastdate="'.$data['updatedate'].'",status="'.$data['status'].'" where id='.$data['id'];
        }
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
     * 功能描述:根据员工的姓名查询员工的信息
     */
    public function get_admin_user_name($name){
        $sql='select * from '.$this->h_admin.' where xingming="'.$name.'"';
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $data=$query->row_array();
                $this->db->close();
                return array('num'=>'','data'=>$data);
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
     * 功能描述:根据员工的用户名查询员工的信息
     */
    public function get_admin_username($username){
        $sql='select * from '.$this->h_admin.' where name="'.$username.'"';
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $data=$query->row_array();
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
     * 功能描述:禁用员工，设置为无效
     */
    public function delete_admin_user($id){
        $updatedate=date("Y-m-d H:i:s");
        $sql='update '.$this->h_admin.' set status="0",lastdate="'.$updatedate.'" where id='.$id;
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
    /**
     * 功能描述:员工列表
     */
    public function select_admin_user($data)
    {
        $searchsql = 'select id, xingming, power_name, maile, mobile, address, pay_type, weixin_code, name, status from ' .$this->h_admin;
        $res_count = $this->db->query($searchsql);        
        if($res_count === false){
	        return false;
	    } else {
            $data['num'] = $res_count->num_rows();
            if (!$data['num']) {	            
                return '-1';
            }
            $sql = $searchsql . ' order by adddate desc limit '.$data['page'].','.$data['per_page'];
            $data['list'] = $this->db->customize_query($sql);
            if($data['list'] === false){
                return false;
            }else{
                return $data;
            }
	    }      
    }
    
    /**
     * 功能描述:检索员工
     */
    public function searchuser($data)
    {
        $searchsql = 'select id, xingming, power_name, maile, mobile, address, pay_type, weixin_code, name, status from ' .$this->h_admin;
        $where = ' where 1=1 ';
        if(isset($data['xingming'])){
            if($data['xingming']!=""){
                $where.=' and xingming like "%'.$data['xingming'].'%"';
            }
        }
        if(isset($data['address'])){
            if($data['address']!=""){
                $where.=' and address like "%'.$data['address'].'%"';
            }
        }
        if(isset($data['pay_type'])){
            if($data['pay_type']!=""){
                $where.=' and pay_type="'.$data['pay_type'].'"';
            }
        }
        if(isset($data['status'])){
            if($data['status']!=""){
                $where.=' and status="'.$data['status'].'"';
            }
        }
        if(isset($data['power_type'])){
            if($data['power_type']!=""){
                $where.=' and power_type="'.$data['power_type'].'"';
            }
        }
        $searchsql.=$where;
        
        $res_count = $this->db->query($searchsql);
        if($res_count === false){
            return false;
        } else {
            $data['num'] = $res_count->num_rows();
            if (!$data['num']) {
                return '-1';
            }
            $sql = $searchsql . ' order by adddate desc limit '.$data['page'].','.$data['per_page'];
            $data['list'] = $this->db->customize_query($sql);
            if($data['list'] === false){
                return false;
            }else{
                return $data;
            }
        }
    }
    
    /**
     * 功能描述:检索员工
     */
    public function select_admin_user_group($data){
        
        $total=0;
        
        $sql='select * from '.$this->h_admin.' ';
        $where=" where 1=1 and group_id=0 ";
        
        if(isset($data['status'])){
            if($data['status']!=""){
                $where.=' and status="'.$data['status'].'"';
            }
        }
        if(isset($data['power_type'])){
            if($data['power_type']!=""){
                $where.=' and power_type="'.$data['power_type'].'"';
            }
        }
        if(isset($data['group_id'])){
            if($data['group_id']!=""){
                $where.=' or group_id='.$data['group_id'];
            }
        }
        $sql.=$where.' order by id desc';
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $num=$query->num_rows();
                $data=$this->db->fetch_query($query);
                $this->db->close();
                return array('num'=>$num,'data'=>$data);               
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
     * 功能描述:查询所有有效的员工处理的订单
     */
    public function getadminorder($data){
        $where='';
        if($data != ''){
            if(isset($data['userid'])&&$data['userid']!=""){
                $where.=' and a.id='.$data['userid'];
            }
            if(isset($data['data_time'])&&$data['data_time']!=""){
                $where.=' and b.order_lastdate between "'.$data['data_time'].' 00:00:00" and "'.$data['data_time'].' 23:59:59" ';
            }
        }
        $sql='select order_weight, order_pic, voucher_id from '.$this->h_admin.' a inner join '.$this->h_order.' b on a.id=b.user_id and a.`status`=1 and b.order_status=0 '.$where;        
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $num = $query->num_rows();
                $data=$this->db->fetch_query($query);
                $this->db->close();
                return array('num'=>$num,'data'=>$data);
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
     * 功能描述:查询所有有效的员工未成交的订单
     */
    public function getadminorder_D($data){
        $where='';
        if($data != ''){
            if(isset($data['userid'])&&$data['userid']!=""){
                $where.=' and a.id='.$data['userid'];
            }
            if(isset($data['data_time'])&&$data['data_time']!=""){
                $where.=' and b.order_lastdate between "'.$data['data_time'].' 00:00:00" and "'.$data['data_time'].' 23:59:59" ';
            }
        }
        $sql='select count(*) as num from '.$this->h_admin.' a inner join '.$this->h_order.' b on a.id=b.user_id and a.`status`=1 and b.order_status=1 '.$where;
        $query=$this->db->query($sql);
        if($query !== false){
            $data=$query->row_array();
            $this->db->close();
            return $data['num'];
        }else{
            $this->db->close();
            return '-1';
        }
    }
    /**
     * 功能描述:查询共收了多少公斤,共花了多少钱,共兑现了多少代金券
     */
    public function getsum_wei_order_vou($data){
        $sumweight=0;
        $sumpic=0;
        $voucher_string='';
        foreach($data as $order){
            $sumweight=$sumweight*1+$order['order_weight']*1;
            $sumpic=$sumpic*1+$order['order_pic']*1;
            if($order['voucher_id']!=''){
                $voucher_string.=$order['voucher_id'].',';
            }
        }    
        $voucher_string=rtrim($voucher_string,',');
        return array('sumpic'=>$sumpic,'sumweight'=>$sumweight,'voucher_string'=>$voucher_string);
    }
    /**
     * 功能描述:查询共兑现了多少代金券
     */
    public function getsumvoucher($voucher_string){
        $sql='select count(*) as sumvouchernum, sum(voucher_pic) as sumvoucherpic from '.$this->h_voucher_log.' where log_return_code="SUCCESS"';
        $query=$this->db->query($sql);        
        if($query !== false){
            $data=$query->row_array();
            $this->db->close();
            return $data;
        }else{
            $this->db->close();
            return false;
        }
    }
    /**
     * 功能描述:查询有多少人关注
     */
    public function getsumguanzhu($data){
        $wheregz='';
        if($data != ''){
            if(isset($data['userid'])&&$data['userid']!=""){
                $wheregz.=' and suser_id='.$data['userid'];
            }
            if(isset($data['data_time'])&&$data['data_time']!=""){
                $wheregz.=' and wx_jointime between "'.$data['data_time'].' 00:00:00" and "'.$data['data_time'].' 23:59:59" ';
            }
        }
        $sql='select count(*) as num from '.$this->h_wxuser.' where wx_status=1 '.$wheregz;
        $query=$this->db->query($sql);
        if($query !== false){
            $data=$query->row_array();
            $this->db->close();
            return $data['num'];
        }else{
            $this->db->close();
            return '-1';
        }
    }
   
}

?>