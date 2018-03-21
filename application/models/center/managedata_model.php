<?php
/*
 * 系统数据
 * 首页成交额 成交单数  首页交易详情
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class managedata_model extends CI_Model {
    
    function  __construct(){
        parent::__construct();
        $this->load->database();
    }
    /**
     * 添加新的交易额记录
     * @param    int   data    日期
     * @param    int   volume  交易额度
     * @param    int   number  成交单数
     * @return   bool 记录写入成功 返回true | 记录写入失败 返回 false
     */
    function  saveVolume(){       
        $data=array(
                'data_type'=>1,
                'data_starttime'=>$this->start,
                'data_stoptime'=>$this->stop,
                'data_content'=>$this->content,
                'data_jointime'=>time(),
                'data_status'=>0
        );
        $query=$this->db->insert('h_system_data',$data);
        $row=$this->db->affected_rows();
        if($query && $row == 1){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 校验是否存在重复的记录
     * @param    int   number  成交单数
     * @return   bool  不存在 返回true | 存在 返回 false
     */
    function checkVolume(){
        $sql='select data_id from h_system_data where data_starttime='.
                $this->start.' and data_status=0';
        $query=$this->db->query($sql);
        $row=$this->db->affected_rows();
        if($query === false || $row >= 1){
            return false;
        }else{
            return true;
        }        
    }
    /**
     * 获取当前所有记录中状态正常的交易额记录
     */
    function getVolume(){
        /* //读取redis中的值
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->key='system_data_volume';
        $cache=$this->zredis->existsKey();
        if($cache !== false){
            $response=json_decode($cache,true);
            return $response;
        } */
        //当redis中不存在值 读取数据库中的记录
        $sql='select data_id as id,data_starttime as start,data_stoptime stop,
              data_content as content,data_status as status from h_system_data 
              where data_type = 1  and data_status != -2  order by data_status desc,data_starttime desc ';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $result=$query->result_array();
        /* //保存记录结果写入redis
        $this->zredis->key='system_data_volume';
        $this->zredis->val=json_encode(array('list'=>$result,'total'=>$query->num_rows));
        $this->zredis->cover=true;
        $cache=$this->zredis->setKey(); */
        $list=array('list'=>$result,'total'=>$query->num_rows);
        return $list;
    }
    
    /**
     * 根据当前记录删除交易额于成交记录
     * @param   int  id  ID
     * @return  bool  删除记录成功 返回ture | 删除记录失败返回 false
     */
    function delVolume (){
        $data=array('data_status'=>-2,'data_uptime'=>time());
        $where=array('data_id'=>$this->id,'data_starttime'=>date('Y-m-d 11:00:01'));
        $query=$this->db->update('h_system_data',$data,$where);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }
        return  true;
    }
    /**
     * 根据id记录读取
     * @param  int  id 记录id
     * @return   读取成功返回 array |读取失败返回 false
     */
    function seeVolume(){
        $sql='select data_starttime,data_content from h_system_data where 
              data_id='.$this->id;
        $query=$this->db->query($sql);
        $result=$query->result_array();
        if($query->num_rows < 1){
            return false;
        }
        $content=json_decode($result['0']['data_content'],true);
        $response=array(
                'start'=>date('Y-m-d H:i:s',$result['0']['data_starttime']),
                'volume'=>$content['volume'],
                'number'=>$content['number']
        );
        return $response;
    }
    /**
     * 修改成交单数与交易额
     * @param  int  id  记录id
     * @param  int  start  展示时间
     * @param  string  content  内容
     * @return  修改记录成功返回 true | 修改记录失败返回 false
     */
    function upVolume(){
        $data=array('data_content'=>$this->content,'data_starttime'=>$this->start,
                'data_uptime'=>$this->stop
        );
        $where=array('data_id'=>$this->id);
        $query=$this->db->update('h_system_data',$data,$where);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }
        return  true;
        
    }
    /**
     * 添加成交记录
     * @param    string   strat 开始时间
     * @param    string   stop  结束时间
     * @param    string   list  记录列表
     */
    function  addRecord(){
        $data=array(
                'data_type'=>2,
                'data_starttime'=>$this->start,
                'data_stoptime'=>$this->stop,
                'data_content'=>$this->list,
                'data_jointime'=>time(),
                'data_status'=>0                
        );
        $query=$this->db->insert('h_system_data',$data);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }
        return  true;
    }
    /**
     * 读取交易记录列表
     * @param   int  p 当前页码
     * @return  json 返回结果集
     */
    function getRecord(){
        $sql='select data_id,data_starttime,data_stoptime,data_content,
              data_status from h_system_data where  data_type=2 
              and data_status != -2  order by data_starttime desc,data_status desc';
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $result['total']=$query->num_rows();
        $result['list']=$query->result_array();
        return $result;
    }
    /**
     * 根据id读取读取记录内容
     * @param  int  id  记录id
     * @param  array  成功获取 | 获取失败 返回
     */
    function recordInfo(){
        $sql='select data_content as content from h_system_data where data_id='.$this->id;
        $query=$this->db->query($sql);
        if($query->num_rows < 1){
            return false;
        }
        $result=$query->result_array();
        $response=json_decode($result['0']['content'],true);
        return $response;
    }
    /**
     * 根据记录id修改内容
     * @param  int  id  记录id
     * @param  string  content  内容
     */
    function upRecord() {
        $data=array('data_content'=>$this->content,'data_uptime'=>time());
        $where=array('data_id'=>$this->id);
        $this->db->update('h_system_data',$data,$where);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }
        return  true;
    }
    /**
     * 根据ID删除记录
     * @param  int  id  记录id
     * @return  bool  删除成功 返回 true |删除失败 返回false
     */
    function delRecord(){
        $data=array('data_status'=>-2,'data_uptime'=>time());
        $where=array('data_id'=>$this->id);
        $query=$this->db->update('h_system_data',$data,$where);
        $row=$this->db->affected_rows();
        if($row != 1){
            return false;
        }
        return  true;        
    }
    /**
     * 首页读取成交额,交易记录
     */
    function indexVolume(){
    /*     $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        //读取redis中的值
        $this->zredis->key='index_data_volume';
        $cache=$this->zredis->existsKey();
        if($cache !== false && !empty($cache)){
            $response=json_decode($cache,true);
            $response['volume']=number_format($response['volume']);
            return $response;
        } */
        //当redis中不存在值 读取数据库中的记录
		 //当redis中不存在值 读取数据库中的记录
		$y = date("Y");
		//获取当天的月份
		$m = date("m");
		//获取当天的号数
		$d = date("d");
		//将今天开始的年月日时分秒，转换成unix时间戳(开始示例：2015-10-12 00:00:00)
	    $start1= mktime(0,0,01,$m,$d,$y); 
		$start2=mktime(23,59,59,$m,$d,$y);
		$sql='select data_id,data_content from h_system_data where data_starttime >'.
               $start1.' and data_starttime < '.$start2.' and data_type=1 and  data_status=0';
        $query=$this->db->query($sql);
        $row=$this->db->affected_rows();
        if($query ===false || $row < 1){
            $response=array('volume'=>'结算中','number'=>'结算中');
            return $response;
        }        
        $result=$query->result_array();
        $day=strtotime(date('Y-m-d 23:59:59'));
        $hours=$day-time();
      /*   //保存记录结果写入redis
        $this->zredis->key='index_data_volume';
        $this->zredis->val=$result['0']['data_content'];
        $this->zredis->cover=true;
        $this->zredis->expire=$hours;
        $cache=$this->zredis->setKey(); */
        $this->db->update('h_system_data',array('data_uptime'=>time(),'data_status'=>-1),
                array('data_id'=>$result['0']['data_id']));
        $response=json_decode($result['0']['data_content'],true);
        $response['volume']=number_format($response['volume']);
        return $response;
    }
    /**
     * 读取当前时间段内的成交记录
     * @param   int  start 展示开始时间
     * @return  array  读取成功返回结果 | 读取失败 返回bool  false
     */
    function indexRecord() {
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        //读取redis中的值
        $this->zredis->key='index_data_record';
        $cache=$this->zredis->existsKey();
        if($cache !== false && !empty($cache)){
            $response=json_decode($cache,true);
            return $response;
        }else{
           
            $hours=$this->searchStart(date('H'));
            $start1=strtotime(date('Y-m-d '.$hours.':00:01'));
            $start2=strtotime(date('Y-m-d '.$hours.':00:00'));
            $sql='select data_id,data_content from h_system_data where data_type = 2
                and  (data_starttime='.$start1.' or data_starttime='.$start2.') and data_status=0';
            $query=$this->db->query($sql);
            if($query->num_rows < 1){
                $response=$this->randomRecord();
                return $response;
            }
            $result=$query->result_array();
            //保存记录结果写入redis
            $hours=$hours+3;
            $stop=strtotime(date('Y-m-d '.$hours.':00:01'));
            $expire=$stop-time();
            $this->zredis->key='index_data_record';
            $this->zredis->val=$result['0']['data_content'];
            $this->zredis->cover=true;
            $this->zredis->expire=$expire;
            $cache=$this->zredis->setKey();
            $this->db->update('h_system_data',array('data_uptime'=>time(),'data_status'=>-1),
                    array('data_id'=>$result['0']['data_id']));
            $response=json_decode($result['0']['data_content'],true); 
            return $response;
        }
    }
    function  randomRecord(){
        $response=array(
           array('name'=>'安静','mobile'=>'152*****129','time'=>time()-621,'type'=>'苹果 iPhone 4S','moeny'=>'280.00元现金+300通花','content'=>''),
           array('name'=>'明天会更好','mobile'=>'131*****018','time'=>time()-1643,'type'=>'苹果 iPhone 6Plus','moeny'=>'3400.00元现金+300通花','content'=>''),
           array('name'=>'风尘','mobile'=>'158*****093','time'=>time()-1819,'type'=>'小米 4','moeny'=>'480.00元现金+300通花','content'=>''),
           array('name'=>'仰望天空','mobile'=>'139*****531','time'=>time()-1910,'type'=>'苹果 iPhone 6','moeny'=>'2800.00元现金+300通花','content'=>''),
           array('name'=>'似水流年','mobile'=>'135*****237','time'=>time()-2117,'type'=>'苹果 iPhone 5S','moeny'=>'1000.00元现金+300通花','content'=>''),
         );
        return $response;
    }
    function searchStart($number){
        $start=array(
            '0'=>array(0,1,2),
            '3'=>array(3,4,5),
            '6'=>array(6,7,8),
            '9'=>array(9,10,11),
            '12'=>array(12,13,14),
            '15'=>array(15,16,17),
            '18'=>array(18,19,20),
            '21'=>array(21,22,23),
        );
        foreach ($start as $key=>$val){
            if(in_array($number,$val)){
              return $key;
            }
        }
    }
    
}