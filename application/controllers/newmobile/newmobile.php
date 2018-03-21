<?php
header('Content-type:text/html;charset=utf-8;');
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Newmobile extends CI_Controller {
	/**
	 * 显示手机品牌名字及及图标
	 */
	function ShowBigView(){
		//获取品牌信息
		$this->load->model('newmobile/newmobile_model');
		$response = $this->newmobile_model->getBrand('5');
		if($response !== false){
			Universal::Output($this->config->item('request_succ'),'','',$response);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果');
		}
	}
	/**
	 * 获取某个手机品牌的所有型号手机
	 */
	function ShowSamilView(){
		$bid = $this->input->post('bid',true);
		if (!is_numeric($bid) || empty($bid)) {
			Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
		}
		$this->load->model('newmobile/newmobile_model');
		$shops = $this->newmobile_model->getShops($bid);
		Universal::Output($this->config->item('request_succ'),'','',$shops);
	}
	/**
	 * 获取某个型号手机的属性信息
	 */
	function nature(){
		 //校验传递的参数
        $typeid=$this->input->post('id',true);
        if(empty($typeid) || !is_numeric($typeid) || isset($typeid{10})){
            Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
        }
        //获取保存的参数信息
        $this->load->model('nonstandard/quote_model');
        $this->quote_model->typeid=$typeid;
        //查询型号参数配置
        $result=$this->quote_model->getOption();
        if(empty($result['0']['types_attr'])){
            Universal::Output($this->config->item('request_fall'),'参数有误,请重新填写!');
        }
        $attr=json_decode($result['0']['types_attr'],true);
        $option=$this->quote_model->getTypeAttr();
        foreach ($attr as $k=>$v){
            foreach ($v as $i=>$n){
                $attr[$k][$i]=str_replace(array('[',']'),array('',''),$n);
            }
        }
        $model=array();
        foreach ($option['model'] as $key=>$val){
            $model[$val['alias']]=array('model'=>$val['model'],'type'=>$val['type'],
                    'name'=>$val['name'],'logic'=>$val['logic']);
        }
        $info=array();
        foreach ($option['info'] as $key=>$val){
            $info[$val['id']]=$val['info'];
        }
        $response=array('attr'=>$attr,'model'=>$model,'info'=>$info);
        Universal::Output($this->config->item('request_succ'),'','',$response);
	}
	/**
	 * 处理自动报价的数据
	 * @param   array  data 报价数据
	 * @return  成功返回array | 失败输出 json  失败原因
	 */
	function handleData($data){
	    $othes=$data;
	    //去除数组中为空的选项
	    array_filter($data);
	    //校验是否存在 多选
	    if(array_key_exists('other',$data)){
	        $other=$data['other'];
	        unset($data['other']);
	    }
	    //校验是否存在 多选
	    if(array_key_exists('other',$othes)){
	        $ote=$othes['other'];
	    }
	    if(empty($data)){
	        Universal::Output($this->config->item('request_fall'),'本次选择的属性不符合规范');
	    }
	    if(empty($data['longitude']) || empty($data['latitude'])){
	        unset($data['longitude']);
	        unset($data['latitude']);
	    }
	    unset($data['name']);
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
	    $otee='';
	    if(array_key_exists('other',$othes)){
	        if(count($othes['other'])>0){
	            $otee=$othes['other'];
	            $other_temp=Universal::safe_replace($otee);
	        }
	    }
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
	    if(isset($other)){
	        $attr['other']=$other;
	    }
	    return $attr;
	}
	
	/**
	 * 保存手机型号的现状选择
	 */
	function saveBigScreen(){
	    $data=$this->input->post();
	    if(empty($data['id']) || !is_numeric($data['id']) || !isset($data['id'])){
	        Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
	    }
	    if(empty($data['name']) || !isset($data['name'])){
	        Universal::Output($this->config->item('request_fall'),'本次请求包含非法字符!');
	    }
	    $response=$this->handleData($data);
	    $this->attr=$response;
	    $this->load->model('nonstandard/quote_model');
	    $this->quote_model->typeid=$data['id'];
	    $plan=$this->quote_model->GetPlan();
	    foreach ($plan as $k=>$v){
	        $this->quote=json_decode($v['plan_content'],true);
	        $this->lock=$plan['0']['plan_base_price'];
	        $pri=$this->reckonRules();
	    }
		$num=$this->create_ordrenumber();
		$this->load->model('newmobile/newmobile_model');
		$this->newmobile_model->phoneid=$data['id'];
		$this->newmobile_model->name=$data['name'];
		unset($data['id']);
		unset($data['name']);
		$this->newmobile_model->content=implode(",",$data);
		$this->newmobile_model->price=$pri;
		$this->newmobile_model->orderid=$num;
		$result=$this->newmobile_model->saveBigScreen();
		if($result){
			Universal::Output($this->config->item('request_succ'),'','/view/newmobile/quote.html',$result);
		}else{
			Universal::Output($this->config->item('request_fall'),'没有获取到结果!');
		} 
	}
	/**
	 *自动报价运算规则
	 *
	 */
	 function reckonRules(){
		$data=$this->input->post();
		$plan=$this->quote;
		//获取自动报价参数 分类
		$model=$this->quote_model->optionModel();
		$attr=$this->attr;
		$quote=array();
		$basics=array();
		$unive=array();
		$special=array();
		foreach ($model as $key=>$val){
			switch ($val['type']){
				case '0':
					array_key_exists($val['alias'],$attr) == true ? $basics[$key]=$attr[$val['alias']] : '';
					break;
				case '1':
					array_key_exists($val['alias'],$attr) == true ? $unive[$key]=$attr[$val['alias']] : '';
					
					break;
				case '2':
					array_key_exists($val['alias'],$attr) == true ? $special=$attr[$val['alias']] : '';
					break;
			}
		}
		$basics=$this->position($basics,$plan,1);
		$unive=$this->position($unive,$plan,2);
		$special=$this->position($special,$plan,2);
		$pri=$this->reckon($this->lock, $basics, $unive, $special);
		return $pri;
	}
	/**
	 * 计算价格
	 */
	function reckon($pri,$basics,$unive,$special){
		if(!is_array($basics)){
			$pri=$pri-$basics;
		}else{
			foreach ($basics as $key=>$val){
				$pri=$pri-$val;
			}
		}
		$pri=$pri-$pri*$unive/100;
		$pri=$pri-$pri*$special/100;
		$pri=round($pri);
		return $pri;
	} 
	/**
	 *得到扣款中同类型百分比最多的一项
	 *@param array  $option  订单参数
	 *@param array  $plan    报价参数
	 *@param int    $type    类型   值为1 不返回最大值 2返回最大值
	 */
	 function position($option,$plan,$type){
		$ratio=array();
		if(!is_array($option)){
			return 0;
		}else{
			foreach ($option as $key=>$val){
				$ratio[]=str_replace(array('-','+','%'),array('','',''),$plan[$val]);
			}
			switch ($type){
				case 1:
					$result=$ratio;
					break;
				case 2:
					$result=@max($ratio);
					break;
			}
			return  $result;
		}
	}
	
	/**
     *  生成订单订单编号
     * @return string
     */
    function create_ordrenumber(){
    	return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(1000,9999);
    }
    /**
     *获取用户下单信息
     */
    function getOneList(){
        $orderid=$this->input->post('oid',true);
        //校验订单编号
        if(empty($orderid)){
            Universal::Output($this->config->item('request_fall'),'出现异常!');
        }
        if(isset($orderid{20}) || !is_numeric($orderid) ){
            Universal::Output($this->config->item('request_fall'),'出现异常!');
        }
        $this->load->model('newmobile/newmobile_model');
        $this->newmobile_model->orderid=$orderid;
        $view=$this->newmobile_model->getOneList();
        $this->load->model('nonstandard/quote_model');
        $model=$this->quote_model->getTypeAttr();
        $view['option']=$model['info'];
        if(!$view){
            Universal::Output($this->config->item('request_fall'),'不存在该订单编号!');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$view);
        }
    }
    /**
     *手机搜索
     */
   /*  function  search(){
        $text = $this->uri->segment(4);
        $text = urldecode($text);
        $text_ne = Universal::SplitWord($text);
        $this->load->model('newmobile/newmobile_model');
        $brand = $this->newmobile_model->getBrand('5');
        if ($brand === false) {
            exit;
        }
        $brand_str = '';//得到品牌所有的id
        foreach ($brand as $k => $v) {
            $brand_str.=$v['id'].',';
        }
        $shops = $this->newmobile_model->seachShop(rtrim($brand_str,','),$text_ne);
       return $shops;
    } */
}