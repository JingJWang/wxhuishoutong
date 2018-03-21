<?php  
class WxUserModel extends MySQL{	
	/*
	 * 功能描述:校验用户是否已经存在
	 */
	public function check_user($data){
		if(empty($data['openid'])){
			return false;
		}
		$sql='select * from h_wxuser where wx_openid="'.$data['openid'].'"';
		if($this->Query($sql) === false ){
			return false;		
		}else{
		    $wxuser=$this->RowArray();
		    //效验用户是否是首次关注
		    if($this->RowCount() == '1'){
    		        if($wxuser['wx_status'] == '0'){
    		            //获取代金券类型
    		            $voucherObj=$this->get_voucherByTyid('1');
    		            $voucherObj['exist']='';
    		            //添加关注现金卷
    		            $voucher_id=$this->insert_voucherLog($voucherObj,$data['openid']);
    		            $data['voucher_id']=$voucher_id;
    		            //更新用户信息
    		            $message=$this->updatewxuser_userino($data);
    		        }else{ 
    		            $message=$this->update_userino($data);
    		            // $data['subscribe_type']=2;
    		            // $this->add_subscribe_log($data);
    		        } 
    		        if($message !== false){
    		            return '1';
    		        }else{		            
    		            return false;
    		        }
		    }else{		        
		        //获取代金券类型
		        $voucherObj=$this->get_voucherByTyid('1');
		        $voucherObj['exist']='';
		        //当是首次关注的是给用户添加代金券并保存用户
		        $voucher_id=$this->insert_voucherLog($voucherObj,$data['openid']);
		        if($voucher_id){
		                return $this->add_user($data,$voucher_id);
		        }else{
		            return false;
		        }	
		    }
		}
	}
	/*
	 * 功能描述:更改用户状态
	 */
	private function updatewxuser_status($openid){
	    $sql='update h_wxuser set wx_status=1,wx_updatetime="'.date('Y-m-d H:i:s').'" where wx_openid="'.$openid.'"';
	    if(!$this->Query($sql)){
	        return false;
	    }else{
	        return true;
	    }
	}
	/*
	 * 功能描述:领取过现金券  未关注的用户 更新用户信息
	 */
	private function updatewxuser_userino($data){
	    if($data['suser_id'] != ' '){
	        $userid=$data['suser_id'];
	    }else{
	        $userid='0';
	    }
	    $sql='update h_wxuser set wx_status=1,wx_name="'.$data['nickname'].'",wx_img="'.$data['headimgurl'].
	         '",wx_sex="'.$data['sex'].'",wx_province="'.$data['province'].'",wx_city="'.$data['city'].
	         '",wx_updatetime="'.date('Y-m-d H:i:s').'",voucher=concat(voucher,",'.$data['voucher_id'].'"),suser_id='.$userid.' where wx_openid="'.$data['openid'].'"';
	    if(!$this->Query($sql)){
	        return false;
	    }else{
	        $data['subscribe_type']=3;
	        $this->add_subscribe_log($data);
	        return true;
	    }
	}
	/*
	 * 功能描述: 关注的用户 重新更新用户信息
	 */
	private function update_userino($data){
	    if($data['suser_id'] != ' '){
	        $userid=$data['suser_id'];
	    }else{
	        $userid='0';
	    }
	    $sql='update h_wxuser set wx_status=1,wx_name="'.$data['nickname'].'",wx_img="'.$data['headimgurl'].
	         '",wx_sex="'.$data['sex'].'",wx_province="'.$data['province'].'",wx_city="'.$data['city'].
	         '",wx_updatetime="'.date('Y-m-d H:i:s').'",suser_id='.$userid.' where wx_openid="'.$data['openid'].'"';
	    if(!$this->Query($sql)){
	        return false;
	    }else{
	        $data['subscribe_type']=2;
	        $this->add_subscribe_log($data);
	        // return true;
	    }
	    $sql='update h_wxuser_task set wx_name="'.$data['nickname'].'",wx_img_face="'.$data['headimgurl'].'",center_updatetime='.time().' where wx_id=(select wx_id from h_wxuser where wx_openid="'.$data['openid'].'")';
	    $query = $this->Query($sql);
	    if ($query === false) {
	    	return false;
	    }else{
	    	return true;
	    }
	}
	/*
	 * 功能描述:查询现金券的详细信息
	 */
	private function get_voucherByTyid($voucherId){
	    $sql='select * from h_voucher where status="1" and voucher_tyid="'.$voucherId.'"';
	    if(!$this->Query($sql) || $this->RowCount() === false){
	         return false;
	    }else{
	        $data=$this->RowArray();
	        return $data;
	    }
	}
	/*
	 *根据代金券对象添加代金券日志
	 *$obj,现金券类型 $openid 微信用户openid
	 */
	public function insert_voucherLog($obj,$openid){
	    $vouer_guid=md5(uniqid(rand()));   //代金券id
	    $log_joindate=date("Y-m-d H:i:s"); //添加时间
	    $log_type=$obj['voucher_tyid'];	   //代金券类型
	    $voucher_pic=$obj['voucher_pic'];  //面值
	    $belong_orderid='0';
	    $provide_openid='0';
	    $log_exceed_time=time()+$obj['voucher_day']*24*3600;
	    $log_exceed=date("Y-m-d H:i:s",$log_exceed_time);//过期时间
	    $sql='insert into h_voucher_log(vouer_guid,voucher_pic,log_joindate,log_type,openid,log_exceed,provide_openid,belong_orderid)values
		("'.$vouer_guid.'","'.$voucher_pic.'","'.$log_joindate.'","'.$log_type.'","'.$openid.'","'.$log_exceed.'","'.$provide_openid.'","'.$belong_orderid.'")';
	    if(!$this->Query($sql)){
	        return false;
	    }else{
	        return $this->GetLastInsertID();
	    }
	}
	//添加微信用户
	public function add_user($info,$vlres){
	    // if($info['suser_id'] !='' ){
	    //     $userid=$info['suser_id'];
	    // }else{
	    //     $userid='0';
	    // }
	    // $jointime=date("Y-m-d H:i:s",strtotime("now"));
	    // $sql='insert into h_wxuser(wx_name,wx_openid,wx_img,wx_sex,wx_province,wx_city,voucher,wx_jointime,suser_id)
	    //       values("'.$this->removeEmoji($info['nickname']).'","'.$info['openid'].'","'.$info['headimgurl'].'","'.$info['sex'].'",
	    //       "'.$info['province'].'","'.$info['city'].'","'.$vlres.'","'.$jointime.'","'.$userid.'")';
	    // if(!$this->Query($sql)){
	    //     return false;
	    // }else{
	        $info['subscribe_type']=1;
	        $this->add_subscribe_log($info);
	        return '0';
	    // }
	}
	/*
	 * 功能描述:现有的现金券
	 */
	public function get_AllVoucher(){
	    $sql='select * from h_voucher where status="1"';
	    if(!$this->Query($sql) || $this->RowCount() ===  false){
	        return false;
	    }else{
	        $data=$this->RecordsArray();
	        return $data;
	    }
	}
	/*
	 * 功能 描述:当用户取消的关注的时 更新用户的状态
	 */
	public function update_suser($openid){
	    $sql='update h_wxuser set wx_status=-1,wx_updatetime="'.date('Y-m-d H:i:s').'" where wx_openid="'.$openid.'"';
	    if(!$this->Query($sql)){
	        return false;
	    }else{
	        return true;
	    }
	}
	/*
	 * 功能描述:添加 关注/取消关注  事件记录
	 */
	public function add_subscribe_log($userinfo){
	  $sql='insert into h_subscribe_log(subscribe_name,subscribe_openid,subscribe_img,user_id,
	          subscribe_type,subscribe_jointime)
	          values("'.$this->removeEmoji($userinfo['nickname']).'","'.$userinfo['openid'].'","'.$userinfo['headimgurl'].'","'.$userinfo['suser_id'].'",
	          "'.$userinfo['subscribe_type'].'","'.date('Y-m-d H:i:s').'")';
	    if(!$this->Query($sql)){
	        return false;
	    }else{
	        return true;
	    }
	}
	/*
	 * 功能 描述:当用户取消的关注的时 更新用户的状态
	 */
	public function select_user_yj($id){
	    $guanzhu=0;
	    $quxiao=0;
	    
	    $sql='select * from h_admin where id='.$id;
	    
	    $where='';
	    $where.=' and suser_id='.$id;
	    $where.=' and wx_jointime between "'.date("Y-m-d 00:00:00").'" and "'.date("Y-m-d 23:59:59").'"';  
	    
	    $whereguanzhu=$where.' and wx_status=1 ';
	    $wherequxiao=$where.' and wx_status=-1 ';
	    
	    if(!$this->Query($sql) || $this->RowCount() ===  false){
	        return false;
	    }else{
	        $guanzhu=$this->getsumguanzhu($whereguanzhu);
	        $quxiao=$this->getsumguanzhu($wherequxiao);
	        return array('guanzhu'=>$guanzhu,'quxiao'=>$quxiao);
	    }
	}
	/*
	 * 功能 描述:查询关注数或取消关注数
	 */
	private function getsumguanzhu($where){
	    $sql='select count(*) as num from h_wxuser where 1=1 '.$where;
        if(!$this->Query($sql) || $this->RowCount() ===  false){
	        return 0;
	    }else{
	        $data=$this->RecordsArray();
	        return $data[0]['num'];
	    }
	}
	/**
	 * 过滤移动端表情包
	 * @param  string   $text   内容
	 * @return string
	 */
	public  function removeEmoji($text) {
	    $clean_text = "";
	    // Match Emoticons
	    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
	    $clean_text = preg_replace($regexEmoticons, '', $text);
	
	    // Match Miscellaneous Symbols and Pictographs
	    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
	    $clean_text = preg_replace($regexSymbols, '', $clean_text);
	
	    // Match Transport And Map Symbols
	    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
	    $clean_text = preg_replace($regexTransport, '', $clean_text);
	
	    // Match Miscellaneous Symbols
	    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
	    $clean_text = preg_replace($regexMisc, '', $clean_text);
	
	    // Match Dingbats
	    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
	    $clean_text = preg_replace($regexDingbats, '', $clean_text);
	
	    return $clean_text;
	}
	/**
	 * 添加当前用户的坐标位置
	 */
	function SaveLocation($data) {
	    $sql='insert into h_wxuser_location(location_openid,location_ctime,
	          location_latitude,lcoation_longitude,lcoation_precision,
	          location_jointime)value("'.$data['openid'].'",'.$data['ctime'].',
	          "'.$data['latitude'].'","'.$data['longitude'].'","'.$data['precision'].'",'.time().')';
	    $query=$this->Query($sql);
	    if($query){
	        return  1000;
	    }else{
	        file_put_contents('logs/'.date('Y-m-d').'location.log',date('Y-m-d H:i:s')."\r\n".$data['openid'].'上报位置失败!'."\r\n",FILE_APPEND);
	    }
	}
}
?>