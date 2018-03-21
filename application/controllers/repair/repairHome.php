<?php
class repairHome extends CI_Controller{
    /**
     * 获取品牌列表
     * @return  json
     */
    function getBrand(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $this->load->model('repair/repairhome_model');
        $this->repairhome_model->typeid=5;
        $data=$this->repairhome_model->getBrandList();
        if($data !== false){
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有找到分类下的品牌列表!');
        }
    }
    
    /**
     * 获取手机维修管理模块手机品牌列表
     * @return  json
     */
    function GetPhoneBrand(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->typeid=5;
    	$data=$this->repairhome_model->getPhoneBrandList();
    	if($data !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$data);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有找到分类下的品牌列表!');
    	}
    }
    
    /**
     * 获取产品型号列表
     * @param   int    id  型号id
     * @return  json
     */
    function getTypes(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        $this->load->model('repair/repairhome_model');
        $this->repairhome_model->brandid=$id;
        $this->repairhome_model->coop=empty($_SESSION['user']['coop']) ? 1447299307 :$_SESSION['user']['coop'];
        $data=$this->repairhome_model->getTypeList();
        if($data !== false){
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }else{
            Universal::Output($this->config->item('request_fall'),'没有找到品牌下的型号列表!');
        }
    }     
    
    /**
     * 获取手机维修管理产品型号列表
     * @param   int    id  型号id
     * @return  json
     */
    function getPhoneTypes(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	$id=$this->input->post('id',true);
    	if(empty($id) || !is_numeric($id)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->brandid=$id;
    	$this->repairhome_model->coop=empty($_SESSION['user']['coop']) ? 1447299307 :$_SESSION['user']['coop'];
    	$data=$this->repairhome_model->getPhoneTypeList();
    	if($data !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$data);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有找到品牌下的型号列表!');
    	}
    }
    
    /**
     * 获取手机维修管理故障参数及保证金信息信息
     */
    function  getPhoneOption(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    
    	//校验传递参数是否合法
    	$id=$this->input->post('id',true);
    	if(empty($id) || !is_numeric($id)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	//校验是否已经配置过参数信息
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->mobile=$id;
    	$data=$this->repairhome_model->getPhoneOption();
    	if(!$data){
    		Universal::Output($this->config->item('request_fall'),'未能获取到相关的结果');
    	}else{
    		Universal::Output($this->config->item('request_succ'),'','',$data);
    	}
    }
    
    /**
     * 获取参数信息
     */
    function  getOption(){
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        
        //校验传递参数是否合法
        $id=$this->input->post('id',true);
        if(empty($id) || !is_numeric($id)){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        //校验是否已经配置过参数信息
        $this->load->model('repair/repairhome_model');
        $this->repairhome_model->mobile=$id;
        $data=$this->repairhome_model->getOption();
        if(!$data){
            Universal::Output($this->config->item('request_fall'),'未能获取到相关的结果');
        }else{
            Universal::Output($this->config->item('request_succ'),'','',$data);
        }
    }
    /**
     * 保存选中的型号属性信息
     * @param  string  info  属性信息
     * @return  json
     */
    function  upSaveAttr(){
        $attr=array();
        $con=array();
        $cons=array();
        //校验用户是否在线
        $this->load->model('center/login_model');
        $this->login_model->isOnine();
        //校验参数
        $data=$this->input->post();
        if(empty($data['mid']) || !is_numeric($data['mid']) || !isset($data['mid'])){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        if(empty($data['pid']) || !is_numeric($data['pid']) || !isset($data['pid'])){
            Universal::Output($this->config->item('request_fall'),'本次请求不合法');
        }
        str_replace(',',';',$data['content']);
        $attr=explode(',',$data['content']);
        foreach ($attr as $key=>$val){
            $con[]=explode(':',$val);
        }
        foreach ($con as $key=>$val){
            $cons[$key]=$val;
        }
        $this->load->model('repair/repairhome_model');
        $this->repairhome_model->attr=json_encode($cons);
        $this->repairhome_model->pid=$data['mid'];
        $this->repairhome_model->mid=$data['pid'];
        $data=$this->repairhome_model->upSaveAttr();
        if($data){
            $url='proRepairPhone.html';
            Universal::Output($this->config->item('request_succ'),'修改手机故障成功',$url);
        }else{
            $url='repairPhoneedit.html';
            Universal::Output($this->config->item('request_fall'),'修改属性失败',$url);
        }
    }
    /**
     * 手机维修管理 保存选中的型号属性信息
     * @param  string  info  属性信息
     * @return  json
     */
    function optionPhoneSave(){
    	$attr=array();
    	$con=array();
    	$cons=array();
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	//校验参数
    	$data=$this->input->post();
    	if(empty($data['mid']) || !is_numeric($data['mid']) || !isset($data['mid'])){
    	    Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	if(empty($data['pid']) || !is_numeric($data['pid']) || !isset($data['pid'])){
    	    Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	if(empty($data['bonds']) || !is_numeric($data['bonds']) || !isset($data['bonds'])){
    	    Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	str_replace(',',';',$data['content']);
    	$attr=explode(',',$data['content']); 
    	foreach ($attr as $key=>$val){
    	    $con[]=explode(':',$val);
    	}
    	foreach ($con as $key=>$val){
    	     $cons[$key]=$val;
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->attr=json_encode($cons);
    	$this->repairhome_model->mname=$data['pname'];
    	$this->repairhome_model->pid=$data['mid'];
    	$this->repairhome_model->mid=$data['pid'];
    	$this->repairhome_model->bond=$data['bonds'];
    	$data=$this->repairhome_model->savePhoneAttr();
    	if($data){
    	    $url='proRepairPhone.html';
    		Universal::Output($this->config->item('request_succ'),'保存手机故障成功',$url);
    	}else{
    	    $url='repairPhoneadd.html';
    		Universal::Output($this->config->item('request_fall'),'保存属性失败',$url);
    	}
    }
   
    /**
     * 获取手机维修订单列表
     */
    function repairList(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	$data=$this->input->post();
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->id=$data['id'];
    	$this->repairhome_model->page=$data['page'];
    	$this->repairhome_model->phone=$data['phone'];
    	$this->repairhome_model->status=$data['status'];
    	$this->repairhome_model->start=$data['start'];
    	$this->repairhome_model->end=$data['end'];
    	$response=$this->repairhome_model->repairList();
    	$response['num']['now'] = $data['page'];
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取单条订单列表
     */
    function getOnerepair(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	$data=$this->input->post();
    	if(empty($data['id']) || !is_numeric($data['id'])){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->id=$data['id'];
    	$response=$this->repairhome_model->getOnerepair();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 修改订单记录
     */
    function editorder(){
    	//校验用户是否在线
    	$this->load->model('center/login_model');
    	$this->login_model->isOnine();
    	$data=$this->input->post();
    	if(empty($data['id']) || !is_numeric($data['id'])){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	if(empty($data['ebonus']) || !is_numeric($data['ebonus'])){
    	    Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	if(empty($data['ediscount']) || !is_numeric($data['ediscount'])){
    	    Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	if(empty($data['emoney']) || !is_numeric($data['emoney'])){
    	    Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$con=Array();
    	$con=array_filter(explode(';', $data['cid']));
    	
    	$cons=Array();
    	foreach ($con as $key=>$val){
    	    $cons[]=explode(':',$val);
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->id=$data['id'];
    	$this->repairhome_model->phones=$data['ephones'];
    	$this->repairhome_model->name=$data['ename'];
    	$this->repairhome_model->adr=$data['eadr'];
    	$this->repairhome_model->money=$data['emoney'];
    	$this->repairhome_model->discount=$data['ediscount'];
    	$this->repairhome_model->bonus=$data['ebonus'];
    	$this->repairhome_model->express=$data['eexpress'];
    	$this->repairhome_model->num=$data['enum'];
    	$this->repairhome_model->con=$data['cont'];
    	$this->repairhome_model->conid=json_encode($cons);
    	$this->repairhome_model->wxid=$data['wxid'];
    	$this->repairhome_model->status=$data['statuss'];
    	$this->repairhome_model->comment=$data['ecomment'];
    	$this->repairhome_model->paysta=$data['paysta'];
    	$this->repairhome_model->other=$data['others'];
    	$response=$this->repairhome_model->editorder();
    	if($response !== false){
    		if($response==3){
    			$this->repairhome_model->wxid=$data['wxid'];
    			$res=$this->repairhome_model->sendMess();
    			$this->load->model('common/wxcode_model');
    			if (isset($res) && $res!='') {
    				$temp_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3a%2f%2fwx.recytl.com%2fview/repair/repairMage.html&response_type=code&scope=snsapi_base&state=#wechat_redirect';
    				$sendtext = '{ "touser":"%s","msgtype":"news","news":{"articles":[{"title":"你的手机维修订单已经修改成功",
				"description":"点此进入我的手机维修列表页面",
				"url":"http://wx.recytl.com/view/repair/repairform.html", "picurl":""}]}}';
    				$content = sprintf($sendtext,$res,$temp_url);
    				$response_wx=$this->wxcode_model->sendmessage($content);
    			}
    		}
    		Universal::Output($this->config->item('request_succ'),'修改成功','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 获取手机维修菜单
     */
    function getMenu(){
    	$state=$this->input->post('state',true);
    	$page=$this->input->post('page',true);
    	$mid=$this->input->post('mid');
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->state=$state;
    	$this->repairhome_model->page=$page;
    	$this->repairhome_model->mid=$mid;
    	$response=$this->repairhome_model->getMenu();
    	$response['num']['now'] = $page;
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存添加的内容
     */
    function saveAdd(){
    	$state=$this->input->post('state');
    	if(empty($state) || !is_numeric($state)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    		
    	}
    	$name=$this->input->post('name');
    	$mid=$this->input->post('mid');
    	$pid=$this->input->post('pid');
    	if(empty($name)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->name=$name;
    	$this->repairhome_model->state=$state;
    	$this->repairhome_model->mid=$mid;
    	$this->repairhome_model->pid=$pid;
    	$response=$this->repairhome_model->saveAdd();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 保存修改的菜单内容
     */
    function saveUpMenu(){
    	$id=$this->input->post('id');
    	if(empty($id) || !is_numeric($id)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$name=$this->input->post('name');
    	if(empty($name)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$state=$this->input->post('state');
    	if(empty($state) || !is_numeric($state)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->id=$id;
    	$this->repairhome_model->name=$name;
    	$this->repairhome_model->state=$state;
    	$response=$this->repairhome_model->saveUpMenu();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     * 删除手机维修菜单
     */
    function delMenu(){
    	$id=$this->input->post('id');
    	if(empty($id) || !is_numeric($id)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$state=$this->input->post('state');
    	if(empty($state) || !is_numeric($state)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->id=$id;
    	$this->repairhome_model->state=$state;
    	$response=$this->repairhome_model->delMenu();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     *获取一类菜单故障内容
     */
    function getFault(){
    	$this->load->model('repair/repairhome_model');
    	$response=$this->repairhome_model->getFault();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
    /**
     *获取二类菜单故障内容
     */
    function getSeFault(){
    	$id=$this->input->post('id');
    	if(empty($id) || !is_numeric($id)){
    		Universal::Output($this->config->item('request_fall'),'本次请求不合法');
    	}
    	$this->load->model('repair/repairhome_model');
    	$this->repairhome_model->id=$id;
    	$response=$this->repairhome_model->getSeFault();
    	if($response !== false){
    		Universal::Output($this->config->item('request_succ'),'','',$response);
    	}else{
    		Universal::Output($this->config->item('request_fall'),'没有获取到结果');
    	}
    }
}