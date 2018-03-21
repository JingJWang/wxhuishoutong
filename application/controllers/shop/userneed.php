<?php
/**
 * 通化商城实体商品信息
 * @author wang
 * 
 */
class  Userneed extends  CI_Controller{
	/**
	 * 保存用户给定的信息
	 * @param      int     id     商品id、
	 * @param      int     num    商品数量
	 */
    function shopinfo(){
		 //获取参数
        $data=$this->input->post();
        if(empty($data) || !is_array($data)){
            Universal::Output($this->config->item('request_fall'),'没有接受到您的选择项');
        }
        $result=$this->handleData($data);
        //校验登录权限
        $this->load->model('auto/userauth_model');
        if (!$this->userauth_model->UserIsLoginJump('/view/shop/attrplan.html?id='.$data['id'],true)) {
            Universal::Output($this->config->item('request_fall'),'请登录后再次操作','/index.php/nonstandard/system/Login');
        }
        //获取当前的品牌  型号 分类信息
        $this->load->model('nonstandard/option_model');
        $this->option_model->typeid=$data['id'];
        $proinfo=$this->option_model->getProInfo();
        unset($proinfo['0']['pid']);
        unset($proinfo['0']['cid']);
        unset($proinfo['0']['bid']);
        $result['proinfo'] = $proinfo['0'];
        $info = json_encode($result);
        $this->load->model('shop/reals_model');
        $return = $this->reals_model->addproinfo($info);
        if ($return==false) {
            Universal::Output($this->config->item('request_fall'),'提交失败！');
        }
        Universal::Output($this->config->item('request_fall'),'提交成功！稍后客服将联系您！','/view/shop/list.html');
    }
    /**
    * 处理自动报价的数据
    * @param   array  data 报价数据
    * @return  成功返回array | 失败输出 json  失败原因 
    */
    function handleData($data){
        //去除数组中为空的选项
        array_filter($data);
        //校验是否存在 多选
        if(array_key_exists('other',$data)){
            $other=$data['other'];
            unset($data['other']);
        }
        if(empty($data)){
            Universal::Output($this->config->item('request_fall'),'本次选择的属性不符合规范');
        }
        //校验参数
        $attr=array();
        //校验请求的参数是否整数
        foreach ($data as $k=>$v){
            if(!is_numeric($v)){
                Universal::Output($this->config->item('request_fall'),'您还没有选择您手机的属性信息');
            }else{
                $attr[$k]=$v;
            }
        }
        //校验时候存在多选参数 存在转换成数组
		$other_temp=Universal::safe_replace($other);
        if(isset($other_temp) && empty($other_temp) === false){
            $sre_other=Universal::safe_replace($other_temp);
            if(!is_numeric($other_temp)){
                Universal::Output($this->config->item('request_fall'),'本次请求不符合规范');
            }
            $other=str_replace(array(' '),array(''),$other);
            $other=trim($other,',');
            $other=explode(',',$other);
        }
        //删除不用于订单内容中属性
        unset($attr['longitude']);
        unset($attr['latitude']);
        unset($attr['id']);
        //获取参数内容
        $this->load->model('nonstandard/quote_model');
        $info=$this->quote_model->GetOptionInfo();
        if(!$info){            
            Universal::Output($this->config->item('request_fall'),'获取参数内容详情出现异常!');
        }
        //转换参数内容数据格式
        $content=array();
        foreach ($info as $k=>$v){
            $content[$v['id']]=$v['info'];
        }
        //获取用于自动报价的参数
        $attrinfo=array();
        foreach ($attr as $k=>$v){
            if(!array_key_exists($v,$content)){
                Universal::Output($this->config->item('request_fall'),'获取参数详情出现异常!');
            }
            $attrinfo[$k]=$content[$v];
        }
        $attr['other']=$other;
        $temp_str='';
        //获取用于订单详情的内容
        if(is_array($other)){
            foreach ($other as $k=>$v){
                if(!array_key_exists($v,$content)){
                    Universal::Output($this->config->item('request_fall'),'获取订单参数详情出现异常!');
                }
                $temp_str.= $content[$v].',';
            }
            $attrinfo['other']=trim($temp_str,',');
        }else{
            $attrinfo['other']='';
        }
        $return = '';
        $result = $this->quote_model->getTypeAttr();
        foreach ($result['model'] as $k => $v) {
        	foreach ($attrinfo as $key => $value) {
        		if ($key == $v['alias']) {
        			$return .= $v['name'].'：'.$value.'，';
        		}
        	}
        }
        $response=array('order'=>$return);
        return $response;
    }
}