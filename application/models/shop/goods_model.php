<?php
/**
 * 积分商城 商品
 * @author .ma
 *
 */
class  Goods_model extends  CI_Model{
     
    public $msg = '';
    
    function  __construct(){
          parent::__construct();
          $this->load->database();    
    }
    
    /**
     * 查询当前的商品列表
     * @return   查询成功 返回 array | 当查询失败 或者没有商品的时候 返回false
     */
    function goodsList(){
        $sql = 'select a.goods_id as id,a.goods_typeid as tyid,a.goods_name as name,a.goods_img as img,a.goods_opri 
                as opri,a.goods_ppri as ppri,a.goods_integral as integral,b.type_fid as fid,b.type_name as tname,
                a.goods_property as property,a.goods_sellnum as selln,goods_number as num,b.type_sort as sort from 
                h_shop_goods as a left join h_shop_type as b on a.goods_typeid=b.type_id where a.goods_status=1 and b.type_status=1
                 order by a.goods_sort desc';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='暂时还没有商品,敬请期待!';
            return false;
        }
        $response=$result->result_array();
        foreach ($response as $key=>$val){
            $response[$key]['opri']=$val['opri']/100;
            $response[$key]['ppri']=$val['ppri']/100;
            // $response[$key]['name']=mb_substr($val['name'],0,9);
        }
        return $response;
    }
    /**
     * 查询当前的商品详细信息
     * @param    int  id   商品 id
     * @return  查询成功返回商品详细信息  | 失败 返回 bool  false
     */
    function goodsInfo(){
        $sql = 'select a.goods_id as id,a.goods_typeid as tid,a.goods_number as number,
                a.goods_name as name,a.goods_imgs as imgs,a.goods_otherprice as otprice,
                a.goods_color as color,
                a.goods_ppri as ppri,a.goods_integral as integral,a.goods_content as content,
                b.type_fid as fid,a.goods_wxshare as share from h_shop_goods as a left join 
                h_shop_type as b on a.goods_typeid=b.type_id where a.goods_id='.$this->goods_id.' 
              and a.goods_status=1 and b.type_status=1';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='商品已经下架,敬请关注!';
            return false;
        }
        $response=$result->result_array();
        $response['0']['otprice'] = json_decode($response['0']['otprice'],true);
        $response['0']['otprice']['0']['p'] = $response['0']['ppri'];
        $response['0']['otprice']['0']['in'] = $response['0']['integral'];
        $response['0']['otprice']['0']['color'] = $response['0']['color'];
        return $response;
    }    
    /**
     * 查询当前的库存是否还足够
     * @param    int   goods_id     商品id
     * @param    int   goods_limit  限购数量   
     * @return   库存足够 返回 bool true | 库存不足 返回的bool false
     */
    function  checkStock(){
        $sql='select  a.goods_name as name,a.goods_number as number,a.goods_limit as limits,a.goods_divide as divide,a.goods_ppri as pri,
              a.goods_integral as integral,a.goods_typeid as tid,a.goods_id as id,a.goods_property as property,a.goods_otherprice as otprice,
              b.type_fid as type from h_shop_goods as a left join h_shop_type as b on a.goods_typeid=b.type_id
              where a.goods_id='.$this->goods_id.' and a.goods_status=1 and b.type_status=1';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='';
            return false;
        }
        $data=$result->result_array();
        $data['0']['otprice'] = json_decode($data['0']['otprice'],true);//得到奖励
        $data['0']['otprice']['0']['p'] = $data['0']['pri'];
        $data['0']['otprice']['0']['in'] = $data['0']['integral'];
        if ($this->goods_prid<0||(count($data['0']['otprice'])-1)<$this->goods_prid) { //奖励的值不能大于总数
            $this->msg = '请选择价格！';
            return false;
        }
        $data['0']['property'] = json_decode($data['0']['property'],true);
        ($data['0']['type']==0)?$data['0']['type']=$data['0']['tid']:'';
        if ($data['0']['type']==3) {
            $this->msg = '购买出错，请刷新界面！';
            return false;
        }elseif ($data['0']['type']==2&&($this->input->post('adress',true)==0)) {
            Universal::Output($this->config->item('request_fall'),'地址不正确','','');
        }elseif ($data['0']['type']==4) {//类型为4必须要有手机号，否则无效，商品多了以后需要扩展
            if(!preg_match("/^(1[3|4|5|7|8][0-9]{9})$/",$this->input->post('phone',true))){
                Universal::Output($this->config->item('request_fall'),'手机格式不对','','');
            }
            $this->record_content = $this->input->post('phone',true);
            $this->load->model('shop/flow_model');
            $result = $this->flow_model->mobisrig($this->record_content,$data['0']['property']['ft']);
            if($result===false){
                $this->msg = '您的手机号不能充值此流量！';
                return false;
            }
        }else{
            $this->record_content = '';
        }
        if($data['0']['number'] < 1){
            $this->msg='商品库存不足!';
            return false;
        }
        if($this->goods_limit > $data['0']['limits']){
            $this->msg='购买的数量超过商品限制!';
            return false;
        }
        $this->goods_buynum = $data['0']['property']['canbuynum'];
        $this->goods_pri=$data['0']['otprice'][$this->goods_prid]['p'];
        $this->goods_integral=$data['0']['otprice'][$this->goods_prid]['in'];
        $this->goods_name=$data['0']['name'];
        $this->goods_divide=$data['0']['divide'];
        $this->goods_id=$data['0']['id'];
        return true;        
    }
    /**
     * 校验当前用户 购买商品所需的通花是否足够
     * @param   int   userid   用户id
     * @return  满足返回 bool true | 不满足 返回 bool false 原因
     */
    function checkIntegral() {
        $sql='select center_integral from h_wxuser_task where wx_id='.$this->userid;
        $result=$this->db->query($sql);
        if($result->num_rows > 1){
            $this->msg='没有获取到您的积分!';
            return false;
        }
        $data=$result->result_array();
        if(isset($this->goods_integral)){
            if($data['0']['center_integral'] < $this->goods_integral){
                $this->msg='通花不足以支付!';
                return   false;
            }
        }
        if ($this->goods_buynum>0) {
            $return = $this->gamebuyone($this->goods_id);
            if($return == false){
                $this->msg='您已经购买过此商品!';
                return   false;
            }
        }
        $this->integral=$data['0']['center_integral'];
        return  true;
    }
    /**
     * 创建订单交易记录
     * @param   array   交易详情
     * @return  成功返回 bool true | 失败返回 bool false 原因
     */
    function   createOrder(){
        $addrecord=$this->db->insert('h_shop_record',$this->record);
        if($this->db->affected_rows() != 1){
            $this->msg='创建订单出现异常!';
            return false;
        }
        return  true;
    }
    /**
     * 查询交易记录
     * @param     int  number 交易编号
     * @return    成功返回当前  当前交易详情  | 失败返回 bool false 原因
     */
    function  getRecord(){
        $sql='select a.record_price as price,a.record_goodid as record_goodid,a.record_id as record_id,a.record_divide as divide,
              a.record_integral as integral,b.goods_name as name,b.goods_property as property,a.record_userid as uid,
              a.record_type as paytype,record_status as status,a.record_invitation as invita,a.record_payid as payid,
              a.record_content as content,b.goods_number,b.goods_typeid as tid,c.type_fid as type from h_shop_record a 
              left join  h_shop_goods b on a.record_goodid=b.goods_id left join h_shop_type as c on b.goods_typeid=c.type_id where 
              a.record_payid="'.$this->number.'"';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='没有该笔订单!';
            return false;
        }
        $data=$result->result_array();
        if ($data['0']['status']!=0) {
        	Universal::Output($this->config->item('request_succ'),'','/view/shop/details.html?id='.$data['0']['record_id'],$data['0']['record_id']);
        }
        if ($data['0']['goods_number']<=0) {
            $this->msg='没有存货了！如果支付成功，请联系我们。';
            return false;
        }
        $data['0']['property'] = json_decode($data['0']['property'],true);
        ($data['0']['type']==0)?$data['0']['type']=$data['0']['tid']:'';
        $this->userid = $data['0']['uid'];
        $this->goods_type=$data['0']['type'];
        $this->goods_paytype=$data['0']['paytype'];
        $this->goods_id=$data['0']['record_goodid'];
        $this->recordid=$data['0']['record_id'];        
        $this->integral=$data['0']['integral'];
        $this->divide=$data['0']['divide'];
        $this->invita=$data['0']['invita'];
        $this->price=$data['0']['price'];
        $this->goods_name=$data['0']['name'];
        $this->goods_content=$data['0']['content'];
        $this->property = $data['0']['property'];
        $this->goods_istask = $data['0']['property']['istask'];
        $this->goods_buynum = $data['0']['property']['canbuynum'];
        return  $data;
    }
    /**
     * 更新库存 更新订单记录信息
     * @param   int    goods_id  商品类型
     * @return  当有库存的时候 返回string| 当库存不足的时候 返回false 
     */
    function uporder(){        
        //更新商品库存
        if($this->nums>0)
        {
        	$shop='update h_shop_goods set goods_number=goods_number-'.$this->nums.',goods_sellnum=goods_sellnum+'.$this->nums.'where goods_id='.$this->goods_id;
        }else{
        	$shop='update h_shop_goods set goods_number=goods_number-1,goods_sellnum=goods_sellnum+1 where goods_id='.$this->goods_id;
        }
        $shop='update h_shop_goods set goods_number=goods_number-1,goods_sellnum=goods_sellnum+1 where goods_id='.$this->goods_id;
        $this->db->query($shop);
        if($this->db->affected_rows() != 1){
            return  false;
        }
        //更新成交记录中的 状态
        $record=array('record_updatetime'=>time(),'record_status'=>2,
                'record_time'=>time(),'record_invitation'=>$this->extendnum);
        $this->db->update('h_shop_record',$record,array('record_payid'=>$this->number));
        if($this->db->affected_rows() != 1){
            $this->msg='交易记录出现异常!';
            return false;
        }
        switch ($this->goods_type) {
            case '2':
                $this->code='订单成功，我们马上为您配送！';
                break;
            case '4':
                $this->code='订单成功，我们尽快为您充值！';
              break;
          default:
            break;
        }
        return true;
    }
    /**
     * 获取激活码 更新库存 更新订单记录信息
     * @param   int    goods_id  商品类型
     * @return  当有库存的时候 返回string  激活码| 当库存不足的时候 返回false 
     */
    function getCode(){
        $sql='select  code_id,code_code from h_shop_code where code_goodsid='.
              $this->goods_id.' and code_status=1 limit 0,1';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='库存不足!';            
            return false;
        }
        //更新当前激活码 状态为无效
        $data=$result->result_array();
        $this->db->update('h_shop_code',array('code_status'=>'-1'),array('code_id'=>$data['0']['code_id']));
        if($this->db->affected_rows() != 1){
            $this->msg='';
            return false;
        }
        //更新商品库存
        $shop='update h_shop_goods set goods_number=goods_number-1,goods_sellnum=goods_sellnum+1 where goods_id='.$this->goods_id;
        $this->db->query($shop);
        if($this->db->affected_rows() != 1){
            return  false;
        }
        //更新成交记录中的 内容 为当前获取的激活码
        $record=array('record_express'=>$data['0']['code_code'],
                'record_updatetime'=>time(),'record_status'=>1,
                'record_time'=>time(),'record_invitation'=>$this->extendnum);
        $this->db->update('h_shop_record',$record,array('record_payid'=>$this->number));
        if($this->db->affected_rows() != 1){
            $this->msg='交易记录出现异常!';
            return false;
        }
        $this->code=$data['0']['code_code'];
        return true;
    }
    /**
     * 创建收入记录
     * @param    array    
     * @return   成功返回 bool  true | 失败返回bool false
     */
    function  createIncome(){
        $income=$this->db->insert('h_bill_income',$this->paydata);
        if($this->db->affected_rows() != 1){
            $this->msg='订单交易出现异常';
            return   false;
        }
        return true;
    }
   
    /**
     * 更新当前用户通花余额
     * @param  int   userid  当前用户id
     * @param  int   integral   更新当前用户通花数量
     * @return  更新成功 返回 bool true |  更新失败 返回 false
     */
    function editIntegral(){
        $sql='update h_wxuser_task set center_integral=center_integral-'.
              $this->integral.' where wx_id='.$this->userid;
        $this->db->query($sql);
        if($this->db->affected_rows() != 1){
            $this->msg='订单交易出现异常';
            return   false;
        }
        $integral = $this->integral;//添加通花日志
        $tonghua_log = array(
            'log_userid' => $this->userid,
            'log_total' => (-1)*$integral,
            'log_content' => '通花商城购买商品',
            'log_status' => 1,
            'log_jointime' => time(),
        );
        $result = $this->db->insert('h_tonghua_log',$tonghua_log);
        if($this->db->affected_rows() != 1){
            $this->msg='订单交易出现异常';
            return   false;
        }
        return true;
    }
    /**
     * 处理当前订单
     * @return 订单处理完成返回bool true|订单处理过程出现异常 false
     */
    function transaction(){
    	
        //支付校验 微信
        if ($this->price>0) {
            $this->checkNumber();
            $respay = $income=$this->createIncome();
            if ($respay==false) {
                $this->msg='未成功支付！';
                return false;
            }
        }
        if ($this->goods_buynum>0) {
            $return = $this->gamebuyone($this->goods_id);
            if($return == false){
                $this->msg='您已经购买过此商品!';
                return   false;
            }
        }
        $this->db->trans_begin();        
        //提取商品 更细交易记录
        if ($this->goods_type==1) {
            $record=$this->getCode();
        }elseif($this->goods_type==2){
            $record=$this->uporder();
        }elseif($this->goods_type==4){
            $this->load->model('shop/flow_model');
            $arr = $this->property;
            $result = $this->flow_model->mobilereg($arr['fm'],$this->goods_content,$this->number);
            if($result === false){
                $this->msg = '充值失败!如果已经付款，请联系客服！';
                return false;
            }
            $record = $this->flow_model->uporder($this->goods_id,$this->number,$this->extendnum);
            if ($record === false) {
                $this->msg = '充值失败!如果已经付款，请联系客服！';
                $this->db->trans_rollback();
                return false;
            }
            if ($result['status']!='E10000') {
                $this->msg = '充值失败!如果已经付款，请联系客服！';
                $this->db->trans_commit();
                return false;
            }
        }
        $integral=$task=true;
        //更新用户通花余额
        if ($this->integral>0) {
            $integral=$this->editIntegral(); 
        }
        if ($this->goods_istask==1) {
            $task = $this->shoptask();
        }  
        if($this->db->trans_status() === true && $record === true && $integral === true && $task===true){
            $this->db->trans_commit();
            return true;
        }else{
            $this->db->trans_rollback();
            return false;
        }
    }
    /**
     * 查看订单是否交易完成
     */
    function checkNumber(){
        if ($this->goods_paytype==3) {//支付宝
            $out_trade_no=$this->number;
            $trade_no='';
            $this->load->library('zhifubao/zhifubao.php');
            $this->zhifubao->out_trade_no=$out_trade_no;
            $this->zhifubao->trade_no=$trade_no;
            $this->zhifubao->config=$this->config->item('zhifubao_attr');
            $res=$this->zhifubao->queryPay();
            $res = $this->object_array($res);
            if ($res['alipay_trade_query_response']['code']!='10000'||$res['alipay_trade_query_response']['msg']!='Success'
                ||$res['alipay_trade_query_response']['trade_status']!='TRADE_SUCCESS') {
                return false;
            }
            $this->paydata=array(
                    'income_userid'=>$this->userid,//用户id
                    'income_orderid'=>$this->recordid,
                    'income_type'=>'3',//付款类型
                    'income_number'=>$this->number,//支付编号
                    'income_payid'=>$res['alipay_trade_query_response']['trade_no'],//商户流水号
                    'income_totalfee'=>$res['alipay_trade_query_response']['receipt_amount']*100,//付款金额
                    'income_time'=>'',
                    'income_jointime'=>time(),//付款时间
                    'income_result'=>1
            );
            return true;
        }elseif($this->goods_paytype==1||$this->goods_paytype==2){
            if ($this->goods_paytype==1) {//回收通
                $this->load->library('wxpay/jspay');
                $jspay=new jspay();
            }elseif($this->goods_paytype==2){//寄售通
                $this->load->library('wxsdk/wxpay');
                $jspay=new Wxpay();
            }
            $jspay->recordid=$this->recordid;
            $jspay->userid=$this->userid;
            $jspay->number=$this->number;        
            $payresult=$jspay->query(); 
            $this->paydata=$jspay->paydata;        
            if (!isset($this->paydata['income_payid'])) {
                return false;
            }
            return true;
        }
    }
    /**
     * 将对象转换数组
     * @param       object      要转换的对象
     * @return      array       返回转换好的数组
     */
    function object_array($array){
        if(is_object($array)){
            $array = (array)$array;
        }
        if(is_array($array)){
            foreach($array as $key=>$value){
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }
    /**
     * 游戏只能买一个
     */
    function gamebuyone($id){
        $sql = 'select record_id from h_shop_record where record_goodid='.$id.' and record_userid='.$this->userid.' and (record_status=1 or record_status=2)';
        $result = $this->db->query($sql);
        if ($result->num_rows>=1&&$result!=false) {
           return false;
        }
        return true;
    }
    /**
     * 检查是否有购买商品任务完成
     * @param       int      　 userid        用户id
     * @return      bool      　任务情况
     */
    function shoptask(){
        if ($this->goods_type!=1&&$this->goods_type!=2) {
            return true;
        }
        $sql = 'select b.log_id,b.task_process from h_task_info as a left join h_task_log as b on a.task_id=b.task_id 
                and b.wx_id='.$this->userid.' and b.task_status=1 and b.cycle_is_finish=-1 where a.task_status=1 and a.task_id=8';
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {//没有任务直接返回
            return true;
        }
        $result = $result->result_array();
        if ($result['0']['log_id']=='') {//没有找到任务
            $time = time();
            $input = array(//得到要输入的基本信息
              'wx_id' => $this->userid,
              'task_id' => 8,
              'task_jointime' => $time,
              'task_finishtime' => $time,
              'task_process' => 3,
            );
            $this->load->model('task/tasks_model');
            $return = $this->tasks_model->puttasklog($input);//插入信息
            if ($return==false) {
                return false;
            }
            return true;
        }
        if ($result['0']['task_process']==3) {//有未领取的奖励则直接跳过
            return true;
        }elseif($result['0']['task_process']==2){
            $this->load->model('task/taskfinish_model');
            $str=$this->taskfinish_model->uptaskprocess($this->userid,8,3);
            if ($str==false) {
                return false;
            }
        }
        return true;
    }
    /**
     * 查询当前订单交易记录
     * @param   int   number  交易记录编号
     * @return  成功返回当前交易记录  array | 失败返回bool false 原因 
     */
   function recordInfo(){
      $sql='select  a.record_express as code,a.record_price as price,a.record_integral as aintegral,
            a.record_jointime as jointime,a.record_status as status,b.goods_id as goodsid,
            b.goods_img as img,b.goods_ppri as ppri,b.goods_integral as integral,b.goods_typeid as tid,
            b.goods_name as name,b.goods_expire as expire,c.type_fid as fid from h_shop_record a 
            left join h_shop_goods b on a.record_goodid=b.goods_id left join h_shop_type as c on b.goods_typeid=c.type_id where 
            a.record_id="'.$this->number.'" and   a.record_userid='.$this->userid;
       $result=$this->db->query($sql);       
       if($result->num_rows < 1){
           $this->msg='该笔交易不存在!';           
           return false;
       }
       $data=$result->result_array();
       return $data;
    }
    /**
     * 查询当前订单交易记录(通过交易订单号查询)
     * @param   int   number  交易记录编号
     * @return  成功返回当前交易记录  array | 失败返回bool false 原因 
     */
    function recordInfoNumber(){
        $sql = 'select a.record_price as price,a.record_goodid as record_goodid,a.record_id as record_id,a.record_divide as divide,
              a.record_adress as adress,a.record_integral as integral,b.goods_name as name,b.goods_property as property,
              a.record_userid as uid,a.record_type as paytype,a.record_content as content,b.goods_number,
              b.goods_typeid as tid,c.type_fid as type from h_shop_record a 
              left join  h_shop_goods b on a.record_goodid=b.goods_id left join h_shop_type as c on b.goods_typeid=c.type_id where 
              a.record_payid="'.$this->number.'" and record_userid='.$this->userid;
        $result=$this->db->query($sql);       
        if($result->num_rows < 1){
            $this->msg='该笔交易不存在!';           
            return false;
        }
        $data=$result->result_array();
        return $data;
    }
   /**
    * 获取当前用户的交易记录
    * @param   int  userid  当前用户的id
    * @return  成功返回当前用户的交易记录 array | 失败返回 bool fasle  原因
    */   
    function getDetail(){
        $sql='SELECT goods_name as name,goods_img as img,record_jointime as jointime,record_id as id,
             record_price as pri,record_integral as integral,record_content as content  FROM  h_shop_record as a 
             LEFT JOIN h_shop_goods as  b ON a.record_goodid = b.goods_id WHERE 
             record_userid = '.$this->userid.' and (a.record_status=1 or a.record_status=2) order by record_jointime desc';
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='当前没有成交记录';
            return false;
        }
        $data=$result->result_array();
        foreach ($data as $key=>$val){
            $data[$key]['pri']=$val['pri']/100;
        }
        return $data;
    }
    /**
     * 获取商品信息
     * @param      int     goods_id     商品id
     * @return     array   商品的信息
     */
    function getgoodinfo(){
        $sql='select  goods_name as name,goods_number as number,goods_typeid as type,goods_limit 
              as limits,goods_ppri as pri ,goods_integral as integral from 
              h_shop_goods where goods_id='.$this->goods_id;
        $result=$this->db->query($sql);
        if($result->num_rows < 1){
            $this->msg='';
            return false;
        }
        return $result->result_array();
    }
    /**
     * 获取此用户的某个商品的全部订单
     * @param      int      goods_id        商品的id
     * @param      int      userid          用户的id
     * @return     bool/array     false/result           无值返回false/正确返回数组
     */
    function getorderinfo(){
        $sql = 'select record_id from h_shop_record where record_goodid='.$this->goods_id.' and record_userid='.$this->userid;
        $result = $this->db->query($sql);
        if ($result->num_rows<1) {
            return false;
        }
        return $result->result_array();
    }
    /**
     * 给微信用户发送微信通知
     */
    function sendNotice(){
        $sql = 'select a.wx_id as id,a.wx_openid as openid from h_wxuser as a,h_wxuser_task as b 
                where b.center_extend_num="'.$this->extendnum.'" and a.wx_id = b.wx_id';
        $userinfo = $this->db->query($sql);
        if ($userinfo->num_rows<1||$userinfo==false) {
            return true;
        }
        $userinfo = $userinfo->result_array();
        $this->load->model('common/wxcode_model');
        $temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2findex.php/task/usercenter/taskcenter&response_type=code&scope=snsapi_base&state=#wechat_redirect';
        $sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"有人购买了您推荐的商品",
                    "description":"您推荐的'.$this->goods_name.'商品已被购买，您获得'
                    .(intval(($this->divide)*($this->price))/100).'元奖励。购买方7天内完成交易（不进行退货操作）即可完成任务并提现。更多详细信息可参见任务流程图",
                    "url":"%s", "picurl":""}]}}';
        $content = sprintf($sendtext,$userinfo['0']['openid'],$temp_url);
        $this->load->database();
        $response_wx=$this->wxcode_model->sendmessage($content);
    }
    /**
     * 通花兑换基金
     * @param      int     goods_id     商品id
     * @param      int     user_id      用户的id
     * @return     array   商品的信息
     */
    function exchangefunds(){
        $time = time();
        $record = array(
            'record_userid' => $this->userid,
            'record_goodid' => $this->goods_id,
            'record_integral' => $this->goods_integral,
            'record_payid' => $this->ordernumber,
            'record_type' => 0,
            'record_adressid' => 0,
            'record_price' => 0,
            'record_content' => '兑换基金',
            'record_time' => $time,
            'record_jointime' => $time,
            'record_status' => 1,
        );
        $sql = 'update h_wxuser_task set center_integral=center_integral-1000,center_fund=center_fund+10,
                center_updatetime='.$time.' where wx_id='.$this->userid;
        $this->db->trans_begin();  
        $addrecord=$this->db->insert('h_shop_record',$record);
        if($this->db->affected_rows() != 1){
            $this->msg='创建订单出现异常!';
            return false;
        }
        $this->recordid = $this->db->insert_id();
        $integral = $this->goods_integral;//添加通花日志
        $tonghua_log = array(
            'log_userid' => $this->userid,
            'log_total' => (-1)*$integral,
            'log_content' => '通花商城兑换基金',
            'log_status' => 1,
            'log_jointime' => time(),
        );
        $result = $this->db->insert('h_tonghua_log',$tonghua_log);
        if($this->db->affected_rows() != 1){
            $this->db->trans_rollback();
            return   false;
        }
        $result = $this->db->query($sql);
        if ($this->db->trans_status() === false || $this->db->affected_rows() != 1 || $result === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return  true;
    }
    /**
     *购买9.9大礼包成为会员后修改用户的个人信息
     */
    function updatePerson(){
    	$wx_expire='';
    	$user_sql='select wx_member as member,wx_expire as expire,wx_openid as openid from h_wxuser where wx_id='.$this->userid.' and wx_status=1';
        $user_query=$this->db->query($user_sql);
        $result=$user_query->result_array();
        if($result!='' && $result['0']['member']>0 && $result['0']['expire']>time()){
            //体验会员
            $times=$result['0']['expire']+365*24*60*60;
            $wx_expire=$times;
        }else if($result!='' && $result['0']['member']>0 && $result['0']['expire']<time()){
            //会员时间已经到期了
            $wx_expire=strtotime("next year");
        }else if ($result!='' && $result['0']['member']==0 && ($result['0']['expire']==null || empty($result['0']['expire'])) ){
            //老用户购买9.9
            $wx_expire=strtotime("next year");
        }else{
            //数据库查不到此人
           return false;
        }
    	//修改用户的会员状态和会员到期时间
    	$sql='update h_wxuser a set a.wx_expire="'.$wx_expire.'",a.wx_member=1 where a.wx_id='.$this->userid;
    	$this->db->trans_begin();
    	$query=$this->db->query($sql);
    	//$result['0']['openid']='o9nlJt2dHqi7vsNZKmPrXE5sAIz8';
    	$result['0']['openid']=$_SESSION['userinfo']['user_openid'];
    	$this->load->model('common/wxcode_model');
    	if($this->db->affected_rows() == 1){
    		//修改用户的通花和环保基金
    		$task_sql='update h_wxuser_task a set a.center_integral=center_integral+'.$this->integral.',a.center_fund=center_fund+'.$this->fund.
    					' where a.wx_id='.$this->userid;
    		$task_query=$this->db->query($task_sql);
    		if($this->db->affected_rows() == 1 || $this->db->trans_status() === true ){
    			$this->db->trans_commit();
    			if (isset($result['0']['openid'])&&$result['0']['openid']!='') {
    				$sendtext='{"touser":"%s", "msgtype":"text","text":{"content":"恭喜您成为年费会员,有效期至'.date("Y-m-d ", $wx_expire).',如果疑问,请咨询客服"}}';
    				$content = sprintf($sendtext,$result['0']['openid']);
    				$response_wx=$this->wxcode_model->sendmessage($content);
    			}
    			return true;
    		}else{
    			$this->db->trans_rollback();
    			return false;
    		}
    	}else{
    		$this->db->trans_rollback();
    		return false;
    	}
    }
    /**
     * 获取用户的邀请码
     */
    function getInvitation(){
        $sql='select a.wx_invitation as inv from h_wxuser as a where a.wx_id='.$this->uid;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if(!$result){
            return false;
        }
           return $result;
    }
}