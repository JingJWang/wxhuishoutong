<?php
class crequest_model extends CI_Model{
    /**
     * 校验请求接口的权限     锁定用户当前请求 
     * @param  int      userid     用户id
     * @param  string   fname      接口名称 
     */
    function  requestAuth(){        
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->key='f_'.$this->fname.'_lock_'.$this->userid;
        $cache=$this->zredis->existsKey();
        if($cache === false){
            $this->zredis->val=json_encode(array('time'=>time(),'lock'=>1,'count'=>1));
            $this->zredis->expire=strtotime("+1 week");
            $this->zredis->setKey();
        }else{
            $lock=json_decode($cache,true);            
            $lock_time=$this->config->item($this->fname.'_lock_time');
            $nowtime=time();
            if($nowtime - $lock['time'] <= $lock_time){
               Universal::Output($this->config->item('request_fall'),'您的请求正在处理,请稍后!');
            }
            //校验当前订单锁状态是否超过7天          
            if(time() - $lock['time'] >= 604800){
                $this->zredis->expire=1;
                return true;
            }
            /* if($lock['lock'] == 1){
               Universal::Output($this->config->item('request_fall'),'您的请求正在处理中,请稍后!');
            } */
            $lock_count=$this->config->item($this->fname.'_lock_count');
            if($lock['count'] >= $lock_count){
                Universal::Output($this->config->item('request_fall'),'本周剩余报单数量为0');
            }
            $this->zredis->val=json_encode(array('time'=>time(),'lock'=>1,'count'=>$lock['count']));
            $this->zredis->cover=true;
            $this->zredis->setKey();
        }        
    }
    /**
     * 解锁用户的请求  增加访问次数
     * @param  int       userid  用户id
     * @param  string    fname   接口名称
     */
    function unLock(){
        $this->load->library('redis/zredis',$this->config->item('redis_config_aliyun'));
        $this->zredis->key='f_'.$this->fname.'_lock_'.$this->userid;
        $cache=$this->zredis->existsKey();
        if($cache === false){
            $this->zredis->val=json_encode(array('time'=>time(),'lock'=>0,'count'=>1));
            $this->zredis->expire=strtotime("+1 week");
            $this->zredis->setKey();
        }else{
            $lock=json_decode($cache,true);
            //增加访问次数
            $lock['count']=$lock['count']+1;
            //解除接口对用户的锁定
            $lock['lock']=0;
            $this->zredis->val=json_encode($lock);
            $this->zredis->cover=true;
            $this->zredis->setKey();
        }
    }
}
