<?php
/*
 * 报价模块
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Quote_model extends CI_Model {
    
    function __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 获取报价列表
     */
    function GetScreening($option){
        $where=' b.wx_id ="'.$_SESSION['userinfo']['user_id'].'" and a.order_id="'.
        $_SESSION['quoteId'].'" and b.order_orderstatus=1 ';
        //是否搜素 选择的服务
        if(!empty($option['option']) ){
           switch ($option['option']){
               case 'd':
                      $where .= ' and  find_in_set("'.'到小区'.'",a.offer_service)';
                   break;
               case 's':
                      $where .= ' and  find_in_set("'.'上门回收'.'",a.offer_service)';
                   break;  
               case 'o':
                      $where .= ' and  find_in_set("'.'快递回收'.'",a.offer_service)';
                   break;
           }
        }
        //是否搜素认证回收商
        if (!empty($option['auto'])){
            $where .= ' and  a.offer_coop_auth != 0';
        }
        //是否  排序
        if(!empty($option['price'])){
            $where .= ' order by  a.offer_price  desc ';
        }
        if(!empty($option['distance'])){
            $where .= ' order by a.offer_distance asc ';
        }
        if(!empty($option['evaluation'])){
            $where .= ' order by a.offer_coop_class desc ';
        }
        if(!empty($option['transaction'])){
            $where .= ' order by  a.offer_done_times  desc ';
        }
        $sql='select a.offer_id as offerid,a.order_id as orderid,a.offer_price as
              price,a.offer_service as service,a.offer_distance as distance,
              a.offer_coop_name as cname,a.offer_coop_class as cclass,
              a.offer_coop_auth as cauth,c.statistic_sum as csum,
              a.cooperator_number as number,b.order_name as name  from
              h_cooperator_offer as a left join h_order_nonstandard as b
              on a.order_id = b.order_number left join h_order_statistic as c on
              a.cooperator_number=c.cooperator_number where '.$where;
        $query=$this->db->query($sql);
        if($query->num_rows() == 0 ){
           return array();
        }
        $data=$query->result_array();
        $arr_company=$this->config->item('coop_auth_company');
        foreach ($data as $key=>$val){
            array_key_exists($val['number'],$arr_company) ? 
            $company=''.$arr_company[$val['number']].'': $company=mb_substr($val['cname'],0,1,'utf-8').'师傅';
            $data[$key]['cname']=$company;
            $auto=$this->config->item('cooperator_auth_type');
            $data[$key]['cauth']=$auto[$data[$key]['cauth']];
            $data[$key]['service'] = strpos(',',$val['service']) ? explode(',',$val['service']): array($val['service']);
            $data[$key]['ctype']=in_array($data[$key]['number'],$this->config->item('js_cooplist')) ? 1 : 0;
        }
        return $data;
    }
    /**
     * 查看报价----获取报价详情信息
     */
    function Gettransactions(){
        $sql='select  a.offer_price,a.offer_distance,a.order_id,a.offer_service,a.cooperator_number,
               b.cooperator_address,b.cooperator_class,a.offer_coop_name,a.offer_coop_auth,a.offer_order_name,
               a.offer_coop_class,c.statistic_down,c.statistic_up,c.statistic_cancel,
               c.statistic_sum,c.statistic_offersum ,c.cooperator_number as number from  h_cooperator_offer as a  
               left join   h_cooperator_info  as b on a.cooperator_number=b.cooperator_number 
               left join h_order_statistic as c on a.cooperator_number=c.cooperator_number
               where a.offer_id='.$this->offerid;
        $query=$this->db->query($sql);
        if($query->num_rows() == 0 ){
            return array();
        }
        $data=$query->result_array();
        return $data;
    }
    /**
     * 查看报价 ---获取回收商的评价信息
     * @param    string      $coopid  回收商编号
     * 
     */
    function  GetEvaluation($coopid){
        if(empty($coopid) || !is_numeric($coopid)){
            exit();
        }
        $sql='select b.wx_name,a.comment_reason from h_wxuser_comment as a 
              left join h_wxuser as b on a.wx_id=b.wx_id where 
              cooperator_number="'.$coopid.'" order by  comment_jointime desc limit 0,3';        
        $query=$this->db->query($sql);
        if($query->num_rows() == 0 ){
            return array();
        }
        $data=$query->result_array();
        return $data;
    }
    /**
     * 查看报价----获取订单的成交记录
     * @param       int     coopid  回收商编号
     * @return      array           结果
     */
    function GetDealOrder($coopid){
        if(empty($coopid) || !is_numeric($coopid)){
            exit();
        }
        $startime =strtotime(date('Y-m-d 9:01:00',strtotime("-1 day")));
        $endtime = strtotime(date('Y-m-d 20:59:00'),strtotime("-1 day"));
        $sql='select data_content as content from h_system_data as a  where a.data_status<0 and
        		data_uptime>="'.$startime.'"and data_uptime<="'.$endtime.'" and data_type=2 order by data_id desc limit 7'; 
        /* $sql='select  order_name as name ,order_number as number,
              ordre_dealtime as dealtime from  h_order_nonstandard
              where order_orderstatus=10 and  cooperator_number="'.
              $coopid.'"  order by dealtime desc limit 0,3 '; */
        $query=$this->db->query($sql);
        if($query->num_rows() == 0){
            return 0;
        }
        $data=$query->result_array();
        return $data;
    }
    /**
     * 用户选择回收商报价
     * @param   int   offerid  报价id
     * @param   int   orderid  订单id
     * @return  成功返回 bool true | 失败返回 bool false
     */
    function ChoiceQuote(){ 
        //校验订单是否  是快递回收
        $sql='select offer_id  from h_cooperator_offer where offer_id="'.$this->offerid.'" and 
              find_in_set("'.'快递回收'.'",offer_service)';
        $result=$this->db->query($sql);
        //当确认是快递回收  订单状态 改为 等待确认 否则 为 等待交易
        $result->num_rows() > 0 ? $orderstatus = 2 : $orderstatus = 3;
        //修改订单 报价状态             
        $this->db->trans_begin();
        $this->db->update('h_order_nonstandard',array('order_orderstatus'=>$orderstatus,
                'order_updatetime'=>time()),array('order_number'=>$this->orderid));
        $up_order=$this->db->affected_rows();        
        $this->db->update('h_cooperator_offer',array('offer_order_status'=>$orderstatus,
                'offer_update_time'=>time()),array('offer_id'=>$this->offerid));   
        $up_offer=$this->db->affected_rows();        
        $this->db->update('h_cooperator_offer',array('offer_status'=>-1,
                'offer_update_time'=>time()),array('order_id'=>$this->orderid,'offer_order_status'=>1));
        //验证 修改结果       
        if ($this->db->trans_status() === false || $up_order != 1 || $up_offer != 1){
            $this->db->trans_rollback();           
            return false;            
        }else{
            $this->db->trans_commit();
            //查询回收商的编号  发送APP通知
            $sql='select cooperator_number from  h_cooperator_offer  where offer_id='.$this->offerid;
            $query=$this->db->query($sql);
            $offer=$query->row_array();
            $this->load->library('vendor/notice');
            $notice[]=$offer['cooperator_number'];
            $response=$this->notice->JPush('voice',$notice,'您有报价已经被用户确认!',array("voice"=>"1", "content"=>"22"));
            return true;
        }
    }
    /**
     * 用户补充订单地址信息
     */
    function saveAddres(){
      /*   $info=explode('-',$this->city);
        if (!isset($info['1'])) {
            $info['1'] = $info['0'];
            $info['2'] = '';
        } */
       /*  $data=array('order_city'=>$info['1'],'order_county'=>$info['2'],'order_mobile'=>$this->mobile,
              'order_province'=>$info['1'],'order_residential_quarters'=>$this->addres); */
        $data=array('order_mobile'=>$this->mobile);
        $where=array('order_number'=>$this->orderid);
        $this->db->update('h_order_nonstandard',$data,$where);
        $up=$this->db->affected_rows();
        if($up == 1){
            return true;
        }
        return false;
    }
    /**
     * 选定报价----查看回收商信息
     * @param   int   $coopid   报价id
     */
    function  GetCoppInfo($coopid){
        $sql='select a.order_id as oid,b.cooperator_mobile,b.cooperator_address,
              b.cooperator_wx  from h_cooperator_offer as a left join h_cooperator_info as 
              b on  a.cooperator_number=b.cooperator_number  where a.offer_id="'.$coopid.'"';
        $query=$this->db->query($sql);
        if($query->num_rows() == 0 ){
            return array();
        }
        $data=$query->result_array();
        return $data;
    }
    /**
     * 校验当前的型号是否存在报价方案
     * @param  int  typeid  型号id
     * @return  存在返回 true  | 不存在 返回false
     */
    function checkQuote(){
        $sql='select plan_id from h_quote_plan  where types_id='.$this->typeid;
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        return true;
    }
    /**
     * 获取配置的参数信息
     * @param  int   id  型号id
     * @return  成功返回 true |失败返回false
     */
    function getOption(){
        $sql='select types_name,types_attr from h_electronic_types where types_id='.$this->typeid;
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return false;
        }
        $attr=$query->result_array();
        return $attr;
    }
    /**
     *获取参数信息
     *@return  成功返回 array || 失败返回 false
     */
    function getTypeAttr(){
        $model_sql='select model_model as model ,model_type as type,model_alias as alias,
                model_logic as logic,model_name as name from h_option_model where model_status=1';
        $model_query=$this->db->query($model_sql);
        if($model_query === false || $model_query->num_rows < 1){
            return false;
        }
        $model=$model_query->result_array();
        $info_sql='select info_id as id ,info_info as info from h_option_info where info_status=1';
        $info_query=$this->db->query($info_sql);
        if($info_query === false || $info_query->num_rows < 1){
            return false;
        }
        $info=$info_query->result_array();
        $response=array('model'=>$model,'info'=>$info);
        return $response;
    }
    /**
     * 获取详细的参数内容
     * @return  成功返回array内容 | 失败返回 false
     */
    function GetOptionInfo(){
        $info_sql='select info_id as id ,info_info as info from 
                h_option_info where info_status=1';
        $info_query=$this->db->query($info_sql);
        if($info_query === false || $info_query->num_rows < 1){
            return false;
        }
        $info=$info_query->result_array();
        return $info;
    }
    /**
     * 获取等待报价的订单
     * @return   array  订单内容 | 没有获取到订单 false
     */
    function GetOrder(){
        $sql='select plan_id,plan_openid,plan_openid,plan_content,
              type_id,type_name,order_number,plan_lat,plan_lng,
              plan_mobile  from h_quote_task  
              where plan_status = -1 limit 0,1';
        $query=$this->db->query($sql);
        if($query  === false || $query->num_rows < 1){
            return false;
        }
        $response=$query->result_array();
        return $response;
    }
    /**
     * 获取报价方案
     * @param   int   typeid   型号id
     * @return  成功的时候返回 array | 失败的时候返回 false
     */
    function GetPlan(){
        $sql='select coop_number,plan_base_price,plan_content,
              plan_garbage_price from h_quote_plan where types_id='.
              $this->typeid.' and plan_status=1';
        $query=$this->db->query($sql);
        if($query  === false && $query->num_rows < 1){
            return false;
        }
        $response=$query->result_array();
        return $response;
    }
    /**
     * 获取回收商的信息
     */
    function GetCoopInfo(){
         $sql='select a.cooperator_name as coop_name,a.cooperator_class as coop_class,
                a.cooperator_auth_type as coop_auth,a.cooperator_number as coop_number,
                a.cooperator_address  as coop_address,a.cooperator_opened as service,
                b.statistic_offersum as offer,a.cooperator_lng as lng,a.cooperator_lat
                as lat,a.cooperator_mobile as coop_mobile from h_cooperator_info as a left join 
                h_order_statistic as b on  a.cooperator_number=b.cooperator_number where 
                 a.cooperator_number in('.$this->coopid.')';
        $query=$this->db->query($sql);
        if($query === false && $query->num_rows < 1){
            return false;
        }
        $coopinfo=$query->result_array();
        return $coopinfo;
    }
    /**
     * 添加自动报价记录
     */
    function savePlanQuote(){
        $offer_data = array(
                'offer_order_name' =>$this->order_name,//订单名称
                'offer_done_times' => $this->done_times,//回收商成交的订单总数
                'offer_coop_name' => $this->coop_name,//回收商姓名
                'offer_coop_class' => $this->coop_class,//回收商星级
                'offer_coop_auth' => $this->coop_auth,//回收商认证
                'cooperator_number' => $this->user_id,//回收商编号
                'offer_coop_addr' => $this->coop_addr,//回收商店铺地址
                'order_id' => $this->order_id,//订单编号
                'offer_price' => $this->price,//报价价格
                'offer_join_time' => time(),//添加时间
                'offer_times' => 1,//报价次数
                'offer_service' => $this->service,//回收商支持的服务
                'offer_remark' => $this->remark,//备注
                'offer_status' => 1,//信息状态
                'offer_distance' => $this->distance,//距离
                'offer_order_status' =>$this->status,//报价状态
                'offer_lng' => $this->lng,//纬度
                'offer_lat' => $this->lat//经度
        );
        $update_data = array(
                'order_updatetime' => time(),
        );
        $update_where = array(
                'order_number' => $this->order_id,
                'order_status' => 1,
        );
        //统计表
        $s_data = array(
                'statistic_updatetime' => time(),
        );
        $s_where = array(
                'cooperator_number' =>$this->user_id,
                'statistic_status' => 1,
        );
        // 开启事物.
        $this->db->trans_begin();
        //添加报价信息
        $this->db->insert('h_cooperator_offer',$offer_data);
        $a = $this->db->affected_rows('h_cooperator_offer');
        // 更新报价次数.
        $this->db->set('order_offer_times','order_offer_times + 1',FALSE);
        $this->db->update('h_order_nonstandard',$update_data,$update_where);
        $b = $this->db->affected_rows('h_order_nonstandard');
        // 更新统计表.
        $this->db->set('statistic_offersum','statistic_offersum + 1',FALSE);
        $this->db->update('h_order_statistic',$s_data,$s_where);
        $c = $this->db->affected_rows('h_order_statistic');        
        if ($this->db->trans_status() === FALSE || $a != 1 || $b != 1 || $c != 1 ){
            $this->db->trans_rollback();
            return false;
        }else{
            $this->db->trans_commit();
            return true;
        }
    }
    /**
     * 修改报价任务的状态
     * @param   int   status  状态id
     * @param   int   plan_id 任务id
     * @return  返回处理结果 bool 成功返回true|失败返回false
     */
    function editPlan(){
        $query=$this->db->update('h_quote_task',array('plan_status'=>$this->status,'plan_updatetime'=>time()),
                array('plan_id'=>$this->plan_id));
        $d = $this->db->affected_rows('h_quote_task');
        if($query !== false && $d == 1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 校验是否存在该用户 并且检验用户是否存在
     * @param   int    mobile   用户手机号码
     * @return   int  存在用户  返回用户的id | 不存在用户 返回 bool false   
     */
    function checkUser(){
        $sql='select wx_id,wx_jointime,wx_regtime,wx_mobile from h_wxuser where  wx_mobile="'.$this->mobile.'"';
        $query=$this->db->query($sql);
        if($query === false || $query->num_rows < 1){
            return  false;
        }
        $user=$query->result_array();
        $this->regt = $user['0']['wx_regtime'];
        $this->joint = strtotime($user['0']['wx_jointime']);
        $this->mobile = $user['0']['wx_mobile'];
        return $user['0']['wx_id'];
    }
    /**
     * 校验用户是否领取过任务 不存在任务 
     * 创建任务记录 存在任务修改任务进度
     * @param  int   userid   用户id
     * @return bool
     */
    function  checkTask(){
        $sql='select log_id,task_process from h_task_log where wx_id="'.$this->userid.'" 
                and task_id=9 and task_status=1';
        $query=$this->db->query($sql);
        if($query === false){
            return false;
        }
        $sql = 'select wx_jointime,wx_regtime,wx_mobile from h_wxuser where wx_id='.$this->userid.' and wx_status!=3';
        $retime = $this->db->query($sql);
        if ($retime->num_rows<=0) {
          return false;
        }
        $retime = $retime->result_array();
        $reg = $this->regt;
        $join = $this->joint;
        if (($reg==='0000-00-00 00:00:00' && $join<1468571400 && $this->mobile!='')||($reg!=='0000-00-00 00:00:00'&&strtotime($reg)<1468571400)) {
            return false;
        }
        //当不存在任务是创建任务 并且任务状态为已完成
        $time = time();
        if($query->num_rows < 1){
            $data=array(
                    'wx_id'=>$this->userid,
                    'task_id'=>9,
                    'task_process'=>3,
                    'task_finishtime'=>$time,
                    'task_jointime'=>$time,
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($data);//插入信息
            if ($return==false) {
                return false;
            }
            return true;
        }
        //当存在任务的时候  如果任务状态为正在进行中  修改任务为已完成
        $task=$query->result_array();
        if($task['0']['task_process'] == 2){
            $this->load->model('task/taskfinish_model');
            $str=$this->taskfinish_model->uptaskprocess($this->userid,9,3);
            if ($str==false) {
                return false;
            }
        }        
        return true;        
    }
    /**
     * 校验报价是否存在
     * @param  int  number 订单编号
     * @param  int  coopid 回收商编号
     * @return bool
     */
    function isQuote(){
        $sql='select offer_id from  h_cooperator_offer where order_id='.
              $this->number.' and cooperator_number='.$this->coopid;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return  true;
        }else{
            return false;
        }
   }
   /**
    * 获取产品参数
    */
   function optionModel(){
       $model_sql='select model_type as type,model_alias as alias
                   from h_option_model where model_status=1 and product_id=1';
       $model_query=$this->db->query($model_sql);
       if($model_query === false || $model_query->num_rows < 1){
           return false;
       }
       $info=$model_query->result_array();
       return $info;
   }
   /**
    * 获取回收商的信息
   */
   function  GetRecover($id) {
   	 $quotesql='select offer_id as id,offer_coop_name as name from
                h_cooperator_offer where order_id="'.$id.'"';
     $result=$this->db->query($quotesql);
     if($result->num_rows < 1){
         $data=0;
     }
    $data=$result->result_array();
    return $data;
   }
}