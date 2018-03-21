<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 非标准化  订单提交
 * 
 * 
 */
class Submitorder_model extends CI_Model {
    //订单(主表)
    private  $nonstandard_order    =   'h_order_nonstandard';
    //旧衣类型(附表)
    private  $nonstandard_content  =   'h_order_content';
     /**
      * 电子-提交电子产品订单
      * @param     int      userid       用户di
      * @param     int      order_type   订单类型  固定值  1 为数码产品
      * @param     int      mobile       用户手机号码
      * @param     string   latitude     经纬度坐标
      * @param     string   longitude    经纬度坐标
      * @param     int      orderstatus  订单状态
      * @param     string   city         订单所在地址
      * @param     string   quarters     详细地址 
      * @return    订单提交成功的时候返回bool true | 提交失败 返回 bool false
      */
     function submit_electronic($electronic){
         $ipone_succ=$this->config->item('ipone_number_succ');
         //当手机号码 不在白名单的时候限制报单数量
         if(!in_array($this->mobile,$ipone_succ)){
             $start=mktime(0,0,0,date('m'),1,date('Y'));
             $end=mktime(23,59,59,date('m'),date('t'),date('Y'));
             //校验当前是否达到报单限制
             $sql='select order_id from '.$this->nonstandard_order.' where 
                   wx_id='.$this->userid.' and order_jointime > '.
                   $start.' and order_jointime < '.$end;
             $num_result=$this->db->query($sql);
             //截取当前用户手机的前3位
             $check_mobile=substr($this->mobile,0,3);
             $number_fall=$this->config->item('ipone_number_fall');
             in_array($check_mobile,$number_fall) ? $check_num = 2 : $check_num = 10 ;
             //当超过限制数量的时候  返回提示信息
             if($num_result->num_rows() >= $check_num){
                 $this->msg=$this->lang->line('order_number_fall');
                 return true;
             }
         }
         $electronic['order_type']=1;//订单分类
         $orderdata=$this->checkorder_data($electronic);
         if($orderdata === false){
             return  array('status'=>$this->config->item('request_optionnull'),
                     'msg'=>$this->lang->line('order_optionnull'));
         }
         $eledata=$this->checkdata_electronic();
         if($eledata ===  false){
             return  array('status'=>$this->config->item('request_optiontypenull'),
                     'msg'=>$this->lang->line('order_optiontypenull'));
         }
         $this->db->trans_begin();
         $this->db->query($orderdata['0']);
         $eledata['order_id']=$orderdata['1'];
         $this->db->insert($this->nonstandard_content,$eledata);
         if ($this->db->trans_status() === false){
             $this->db->trans_rollback();
             return  array('status'=>$this->config->item('request_fall'),
                     'msg'=>$this->lang->line('order_addclothes_fall'));
         }else{
             $this->db->trans_commit();
             $electronic['orderstatus'] == 1  ? $url = $this->config->item('url_quotelist_succ').'?id='.$orderdata['1'] : 
                $url=$this->config->item('url_cancelorder_succ');
              if($electronic['order_type']  == 1){
                $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));//redis加载
                $relist = $_SESSION['submitorder']['brand']['typename'].','.$orderdata['1'].','.$electronic['longitude'].','.$electronic['latitude'];
                $status = $this->zredis->_redis->rpush('orderlist',$relist);
               
              }
             unset($_SESSION['submitorder']);//销毁订单信息
             return  array('status'=>$this->config->item('request_succ'),
                     'msg'=>'','url'=>$url);
         }
     }
     /**
      * 校验订单数据
      * @param     string   latitude        维度
      * @param     string   longitude       经度
      * @param     string   orderstatus     订单状态  
      * @param     string   province        省份
      * @param     string   city            市
      * @param     string   county          县/区
      * @param     string   residential_quarters    //小区
      * @param     string   isused          //是否可用
      * @return    array                    校验过的数组
      */
     function  checkorder_data(){
         //转换坐标地址
        $this->load->model('weixin/wxinformation_model');
        $latlon=$this->longitude.','.$this->latitude;
        $baidu_loc=$this->wxinformation_model->conversion_gps($latlon);
        if($baidu_loc->status ==0){
            $baidulocalhost=$baidu_loc->result['0']->y.','.$baidu_loc->result['0']->x;
            $this->latitude=$baidu_loc->result['0']->y;
            $this->longitude=$baidu_loc->result['0']->x;
        }
        $attr=$this->attr;
        //生成订单编号
        $this->number=$this->create_ordrenumber();
        $sql='insert into h_order_nonstandard(wx_id, order_name, order_ctype, 
               order_ftype,order_latitude,order_longitude, order_point,
               order_orderstatus,order_isused, order_img,order_jointime,
               order_status, order_number, wx_openid,order_submittime,order_invitation)value('.
               $this->userid.',"'. $attr['typename'].'", '.$attr['proid'].',1,"'.
               $attr['latitude'].'", "'.$attr['longitude'].'",
               GeomFromText("POINT('.$attr['longitude'].' '.$attr['latitude'].')"),
               "1","1"," ",'.time().', 1, "'.$this->number.'","'.$this->openid.'","'.time().'","'.$this->invitation.'")';
         $this->db->trans_begin();
         $query=$this->db->query($sql);
         $order=$this->db->affected_rows();
         unset($attr['proid']);unset($attr['longitude']);unset($attr['longitude']);
         $this->db->insert('h_order_content',array('order_id'=>$this->number,
                           'electronic_oather'=>json_encode($attr),
                           'electronic_jointime'=>time(),
                           'electronic_status'=>1));
         $content=$this->db->affected_rows();
         if($this->db->trans_status() && $content == 1 && $order == 1){
            $this->db->trans_commit();
            return true;
         }else{
            $this->db->trans_rollback();
            return false;
         }
     }    
    /**
     * 电子-检测电子产品属性
     */
    function electronic_attr($ordertype,$product,$electronic){
        //校验产品的属性        
        switch ($product){
            case '5':
                    if( empty($electronic['channel'])  || empty($electronic['capacity']) || empty($electronic['guarantee'])||
                        empty($electronic['frame'])    || empty($electronic['screen'])   || empty($electronic['display']) ||
                        empty($electronic['repair'])  || empty($electronic['gsm']) || empty($electronic['color'])){
                            return false;
                    }
                    //数码产品 手机
                    $attr=array(
                            'channel'=>$electronic['channel'],'oather'=>isset($electronic['oather']) ? trim($electronic['oather'],',') :'',
                            'capacity'=>$electronic['capacity'], 'guarantee'=>$electronic['guarantee'],
                            'frame'=>$electronic['frame'],'screen'=>$electronic['screen'],
                            'display'=>$electronic['display'],'repair'=>$electronic['repair'],
                            'gsm'=>$electronic['gsm'],'color'=>$electronic['color']
                    );
                    $_SESSION['submitorder']['attr']=json_encode($attr);
                    unset($attr);
                    unset($electronic);
                    break;
            case '6':
                    if( empty($electronic['memory'])    || empty($electronic['cddrive'])  || empty($electronic['screen'])||
                        empty($electronic['graphics'])  || empty($electronic['harddisk']) || empty($electronic['camera']) ){
                        return false;
                    }
                    $_SESSION['submitorder']['attr']=json_encode($electronic);
                    break;
            case '7':
                   if( empty($electronic['guarantee']) || empty($electronic['capacity'])  
                        || empty($electronic['network'])|| empty($electronic['channel'])){
                            return false;
                    }
                   
                    $_SESSION['submitorder']['attr']=json_encode($electronic);
                    break;
            case '8':
                   if( empty($electronic['guarantee']) || empty($electronic['channel'])){
                            return false;
                   }
                   $_SESSION['submitorder']['attr']=json_encode($electronic);
                   break;
            case '9':
                  if( empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                  || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                        return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '10':
                  if( empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                  || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                        return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '11':
                  if( empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '12':
                  if(empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '13':
                  if( empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '14':
                   if( empty($electronic['sex'])  || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                   }
                   $electronic['proname'] ='';
                   $electronic['braname'] ='';
                   $electronic['typename'] ='';
                   $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '15':
                  if( empty($electronic['sex']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '16':
                  if( empty($electronic['sex']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff'])|| empty($electronic['oather']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  $electronic['oather']   =trim($electronic['oather'],',');
                  break;
            case '17':
                  if( empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '18':
                  if( empty($electronic['sex']) || empty($electronic['size']) || empty($electronic['used_degree'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) || empty($electronic['type'])){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '19':
                  if( empty($electronic['type']) || empty($electronic['cw']) || empty($electronic['p'])){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '20':
                  if( empty($electronic['type']) || empty($electronic['capacity']) || empty($electronic['power']) ){
                       return false;
                  }
                  $electronic['proname'] ='';
                  $electronic['braname'] ='';
                  $electronic['typename'] ='';
                  $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case '21':
                   if( empty($electronic['type']) || empty($electronic['resolution']) || empty($electronic['3d'])
                        || empty($electronic['3d']) || empty($electronic['stuff']) ){
                       return false;
                   }
                   $electronic['proname'] ='';
                   $electronic['braname'] ='';
                   $electronic['typename'] ='';
                   $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            case  '22':
                    if( empty($electronic['sex']) || empty($electronic['used_degree']) || empty($electronic['size'])
                        || empty($electronic['usedmake']) || empty($electronic['stuff']) ){
                       return false;
                   }
                   $electronic['proname'] ='';
                   $electronic['braname'] ='';
                   $electronic['typename'] ='';
                   $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break; 
            case  '23':
                   if( empty($electronic['volume']) || empty($electronic['temperature']) ||
                    empty($electronic['refrigeration']) || empty($electronic['frequency'])  ){
                       return false;
                   }
                   $electronic['proname'] ='';
                   $electronic['braname'] ='';
                   $electronic['typename'] ='';
                   $_SESSION['submitorder']['attr']=json_encode($electronic);
                  break;
            default:
                   return false;
               
        }    
        return true;
    }
    /** 添加订单 ---附表
     * @param   int     brand_id            品牌id
     * @param   int     electronic_type     型号id
     * @param   string  electronic_buydate  购买时间
     * @param   string  electronic_oather   属性详情
     * @param   string  electronic_img      图片
     * @param   date    electronic_jointime 添加时间
     * @param   int     electronic_status   状态
     * @return  array   ele_info            结果数组
     */
    function checkdata_electronic() {
        $ele_info=array(
                'brand_id'=>$_SESSION['submitorder']['brand']['braid'],
                'electronic_type'=>$_SESSION['submitorder']['brand']['typeid'],
                'electronic_oather'=>$_SESSION['submitorder']['attr'],
                'electronic_img'=>isset($_SESSION['submitorder']['orderimg']) ? 
                                    implode(',', $_SESSION['submitorder']['orderimg']) :'',
                'electronic_jointime'=>time(),
                'electronic_status'=>1
        );
        return $ele_info;
    }
    /**
     * 添加自动报价订单
     * @param  int  id          型号id
     * @param  int  latitude    纬度
     * @param  int  longitude   经度
     */
    function  savePlanOrder(){
        //创建订单信息
        $this->number=$this->create_ordrenumber();
        $sql='insert into h_order_nonstandard(wx_id, order_name, order_ctype,
               order_ftype,order_latitude,order_longitude, order_point,
               order_orderstatus,order_isused, order_img,order_jointime,
               order_status, order_number, wx_openid,order_submittime,types_id,order_invitation)value('.
                       $this->userid.',"'. $this->typename.'", '.$this->pid.',1,"'.
                       $this->latitude.'", "'.$this->longitude.'",
               GeomFromText("POINT('.$this->latitude.' '.$this->longitude.')"),
               "1","1"," ",'.time().', 1, "'.$this->number.'","'.$this->openid.'","'.time().'",'.$this->id.',"'.$this->invitation.'")';
        $this->db->trans_begin();
        $query=$this->db->query($sql);
        $order=$this->db->affected_rows();
        //保存订单属性
        $this->db->insert('h_order_content',
                array(
                        'order_id'=>$this->number,
                        'electronic_oather'=>$this->attr,
                        'electronic_jointime'=>time(),
                        'electronic_status'=>1)
                );
        $content=$this->db->affected_rows();
        if($this->ordertype == 0){
            //添加自动报价任务
            $this->db->insert('h_quote_task',array(
                    'plan_openid'=>$this->openid,'plan_content'=>$this->plan,
                    'plan_lng'=>$this->longitude,'plan_lat'=>$this->latitude,
                    'order_number'=>$this->number,'plan_jointime'=>time(),
                    'plan_mobile'=>$this->mobile,'type_id'=>$this->id,
                    'plan_status'=>-1,'type_name'=>$this->typename
            ));
            $plan=$this->db->affected_rows();
        }else{
            $plan=1;
        }
        if($this->db->trans_status() && $content == 1 && $order == 1 && $plan == 1){
            $this->db->trans_commit();
            return true;
        }else{
            $this->db->trans_rollback();
            return false;
        }
        
    }
    /**
     * 获取笔记本电脑  型号下的cpu 
     * @return 成功返回array | 失败返回 false
     */
    function Getcpu($typename){
          $key=strip_tags($typename);
          $sql='select  configure_cpu  from  h_electronic_configure where configure_type="'.$typename.'"';
          $query=$this->db->query($sql);
          if($query === false){
              return false;
          }
          $data=$query->result_array();
          return $data;
    }
    /**
     * 生成订单编号
     */
    function create_ordrenumber(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
    /**
     *校验当前用户本周报单数量是否超过限制
     *@param  int  mobile 手机号码
     *@param  int  userid 用户id
     *@return bool 校验通过 返回true | 否则返回false
     */
    function checkNum(){
        $ipone_succ=$this->config->item('ipone_number_succ');
        //当手机号码 不在白名单的时候限制报单数量
        if(in_array($this->mobile,$ipone_succ) === false){
            $start=mktime(0,0,0,date('m'),1,date('Y'));
            $end=mktime(23,59,59,date('m'),date('t'),date('Y'));
            //校验当前是否达到报单限制
            $sql='select order_id from '.$this->nonstandard_order.' where
                   wx_id='.$this->userid.' and order_jointime > '.
                       $start.' and order_jointime < '.$end;
            $num_result=$this->db->query($sql);
            //当超过限制数量的时候  返回提示信息
            if($num_result->num_rows() >= 2){
                $this->msg=$this->lang->line('order_number_fall');
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    
    }
}
/* End of file Wxuser_model.php */
/* Location: ./application/models/nonstandard/Wxuser_model.php */