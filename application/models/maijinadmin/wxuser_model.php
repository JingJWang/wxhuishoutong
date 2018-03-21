<?php
header('Content-type:text/html;charset=utf-8');
class Wxuser_model extends CI_Model {
    
    private $table_wxuser='h_wxuser';
    
    function __construct(){
        parent::__construct();
        
    }
   /**
    * 2015-06-28 mxt
    * 功能描述:校验用户是否已经关注
    */
    public function check_wxuser_exist($openid){
       $sql='select wx_openid from h_wxuser where wx_openid="'.$openid.'"';
       $data=$this->db->customize_query($sql);
       return $data;
    }
     /**
     * 功能描述:获取用户的详细信息
     */
    public function getuserinfo($openid){
        $sql='select wx_openid from h_wxuser where wx_openid="'.$openid.'"';
        $data=$this->db->customize_query($sql);
        return $data;
    }
    
    /** 
     * 功能描述:根据条件检索粉丝
     * 参数说明:array $data 查询条件数组
     * 被引用:
     *      controllers/maijinadmin/wxuser.php select_weixin_user();
     */
    public function select_weixin_user($data){
        $pagenum_back=$this->config->item('PAGENUM_BACK')*1;
        
        $total=0;
        $todaytotal=0;
        $yesterdaytotal=0;
        $monthtotal=0;
    
        $total_wx=0;
        $todaytotal_wx=0;
        $yesterdaytotal_wx=0;
        $monthtotal_wx=0;
    
        $totalwhere='';
        $todaytotalwhere='and wx_jointime between "'.date("Y-m-d 00:00:00").'" and "'.date("Y-m-d 23:59:59").'"';
        $yesterdaytotalwhere='and wx_jointime between "'.date("Y-m-d 00:00:00",strtotime("-1 day")).'" and "'.date("Y-m-d 23:59:59",strtotime("-1 day")).'"';
        $monthtotalwhere='and wx_jointime between "'.date("Y-m-01 00:00:00").'" and "'.date("Y-m-d 23:59:59").'"';
        $where='';
        $sql='select * from h_wxuser where 1=1 ';
        $youxiao='';
        if(isset($data['timetype'])&&$data['timetype']!=""){            
            switch($data['timetype']){
                case 'total':
                    $youxiao=true;
                    $where.=' '.$totalwhere.' ';
                    break;
                case 'todaytotal':
                    $youxiao=true;
                    $where.=' '.$todaytotalwhere.' ';
                    break;
                case 'yesterdaytotal':
                    $youxiao=true;
                    $where.=' '.$yesterdaytotalwhere.' ';
                    break;
                case 'monthtotal':
                    $youxiao=true;
                    $where.=' '.$monthtotalwhere.' ';
                    break;
                case 'total_wx':
                    $youxiao=false;
                    $where.=' '.$totalwhere.' ';
                    break;
                case 'todaytotal_wx':
                    $youxiao=false;
                    $where.=' '.$todaytotalwhere.' ';
                    break;
                case 'yesterdaytotal_wx':
                    $youxiao=false;
                    $where.=' '.$yesterdaytotalwhere.' ';
                    break;
                case 'monthtotal_wx':
                    $youxiao=false;
                    $where.=' '.$monthtotalwhere.' ';
                    break;
            }
            $sql.=$where;
        }
        if($youxiao===true){
            $total=$this->getweixin_user_num($where,'1');//关注总数
            $sql.=' and wx_status=1 ';
        }elseif($youxiao===false){
            $total=$this->getweixin_user_num($where,'-1');//取消关注总数
            $sql.=' and wx_status=-1 ';
        }else{
            $sql.=' and wx_status=1 ';
        }
        $sql.=' order by wx_jointime desc ';
        if(isset($data['page'])&&$data['page']!=""){
            $page=$data['page'];
            $start=($page-1)*$pagenum_back;
            $end=$pagenum_back;
            $sql.=' limit '.$start.','.$end;
        }else{
            $sql.=' limit 0,'.$pagenum_back;
            if((!isset($data['timetype']))||$data['timetype']==""){
                $total=$this->getweixin_user_num($totalwhere,'1');//关注总数
                $todaytotal=$this->getweixin_user_num($todaytotalwhere,'1');//今天关注总数
                $yesterdaytotal=$this->getweixin_user_num($yesterdaytotalwhere,'1');//昨天关注总数
                $monthtotal=$this->getweixin_user_num($monthtotalwhere,'1');//本月关注总数
    
                $total_wx=$this->getweixin_user_num($totalwhere,'-1');//取消关注总数
                $todaytotal_wx=$this->getweixin_user_num($todaytotalwhere,'-1');//今天取消关注总数
                $yesterdaytotal_wx=$this->getweixin_user_num($yesterdaytotalwhere,'-1');//昨天取消关注总数
                $monthtotal_wx=$this->getweixin_user_num($monthtotalwhere,'-1');//本月取消关注总数
            }
        }       
        $pagetotal=ceil($total/$pagenum_back);
        $query=$this->db->query($sql);
        if($query !== false){
            if($query->num_rows() > 0){
                $num=$query->num_rows();
                $data=$this->db->fetch_query($query);                
                $address='';
				$adminxingming='';
                foreach($data as $user){
                    $address='';
                    $adminxingming='';
                    $suser_id=$user['suser_id'];
                    $this->load->database();
                    $this->load->model('maijinadmin/adminuser_model','',TRUE);
                    if($suser_id!=0){
                        $adminuserinfo=$this->adminuser_model->get_admin_user($suser_id);
                        if($adminuserinfo !== false){
                            $address=$adminuserinfo['data']['address'];
                            $adminxingming=$adminuserinfo['data']['xingming'];
                        }
                    }
                    $user['address']=$address;
                    $user['adminxingming']=$adminxingming;
                    $userlist[]=$user;
                }
                $this->db->close();
                return array('pagetotal'=>$pagetotal,'pagenum'=>$total,'todaytotal'=>$todaytotal,'yesterdaytotal'=>$yesterdaytotal,'monthtotal'=>$monthtotal,'total_wx'=>$total_wx,'todaytotal_wx'=>$todaytotal_wx,'yesterdaytotal_wx'=>$yesterdaytotal_wx,'monthtotal_wx'=>$monthtotal_wx,'num'=>$num,'data'=>$userlist);                
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
     * 功能描述:根据条件获取粉丝总数
     * 参数说明:string $where 查询条件字符串
     * 被引用:私有方法
     */
    private function getweixin_user_num($where,$youxiao){
        $sql="select count(*) as num from h_wxuser where 1=1 and wx_status=".$youxiao." ".$where;
        $query=$this->db->query($sql);
        if($query !== false){
            $data=$query->row_array();
            $this->db->close();
            return $data['num'];
        }else{
            $this->db->close();
            return 0;
        }        
    }
    /**
     * 功能描述:根据openid查询粉丝的信息
     * 参数说明:string $openid 粉丝的openid
     * 被引用:
     *      models/maijinadmin/order_model.php select_order_list($data);
     */
    public function getwxuser_info($openid){
        $sql='select * from h_wxuser where wx_openid="'.$openid.'"';
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