<?php
header('Content-type:text/html;charset=utf-8');
/**
 * 功能描述:菜单管理类
 */
class menu_model extends CI_Model {
    //菜单表
    private $h_menu='h_menu';
    /**
     * 功能描述:获取菜单
     * 被引用:
     *      controllers/maijinadmin/menu.php getmenu_L();
     */
	public function getmenu_L(){
	    $sql='select * from '.$this->h_menu.' where id=1';
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
	/**
	 * 功能描述:保存菜单，并发布
	 * 参数说明:string $menu 菜单json字符串
     * 被引用:
     *      controllers/maijinadmin/menu.php addmenu_L();
	 */
	public function addmenu_L($menu,$access_token){
		$sql="update ".$this->h_menu." set content='".$menu."' where id=1";		
		$query=$this->db->query($sql);
		if($query !== false){            
		    $info=$this->createMenu($menu,$access_token);
		    if($info['errmsg']=='ok'&&$info['errcode']=='0'){
		        $this->db->close();
		        return true;
		    }else{
		        $this->db->close();
		        return false;
		    }
		}else{
		    $this->db->close();
		    return false;
		}		
	}
	/**
	 * 功能描述:发布菜单方法
	 * 参数说明:
	 *     string $menu 菜单json字符串
	 *     string $access_token token值
     * 被引用:私有方法
	 */
	private function createMenu($menu,$access_token){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $menu);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$info = curl_exec($ch);		
		curl_close($ch);
		$jsoninfo=json_decode($info,true);
		return $jsoninfo;
	}
}

?>