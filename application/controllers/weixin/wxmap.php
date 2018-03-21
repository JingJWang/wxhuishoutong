<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
header("Content-type:text/html;charset=utf-8");
class Wxmap extends CI_Controller {
    /**
     * 进入查询地点页面
     */
    function  addresslist(){
        $code=$this->input->get('code');
        $this->load->database();
        $this->load->model('common/wxcode_model');
        if(!empty($code)){            
            $openid=$this->wxcode_model->getOpenid($code);//获取openid
            if(empty($openid)){
                Header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx29a596b5eac42c22&redirect_uri=http%3A%2F%2Fwx.recytl.com%2Findex.php/weixin/wxmap/addresslist&response_type=code&scope=snsapi_base&state=aaa&connect_redirect=1#wechat_redirect'); 
                exit();
            }
            $this->load->model('weixin/wxinformation_model');
            $localtion=$this->wxinformation_model->GetUserLocation($openid);
            if($localtion  == '-1'){
                $data['coordinate']='';
                $data['addreslist']='';
            }else{
                $data['coordinate']=$this->GetNearbyAddres($localtion);
                $this->load->model('weixin/wxinformation_model');
                $Matlocaltion=array('latitude'=>$data['coordinate']['data']['lat'],
                        'longitude'=>$data['coordinate']['data']['lng']);
                $data['addreslist']=$this->wxinformation_model->MatchingDistance($Matlocaltion);
            }
        }else{
            $data['coordinate']='';
            $data['addreslist']='';
        }
        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置        
        $data['token']=$_SESSION['maptoken']=md5(date('Y-m-d H:i:s'));
        $this->load->view('weixin/addresmap',$data);
    }
    /**
     * 内部员工查询附近的点
     */
    function  staffquery(){
        $code=$this->input->get('code');
        $this->load->database();
        $this->load->model('common/wxcode_model');
        if(!empty($code)){
            $openid=$this->wxcode_model->getOpenid($code);//获取openid
            if(empty($openid)){
                exit('请重新进入!');
            }
            $this->load->model('weixin/wxinformation_model');
            $localtion=$this->wxinformation_model->GetUserLocation($openid);
            if($localtion  == '-1'){
                $data['coordinate']='';
                $data['addreslist']='';
            }else{
                $data['coordinate']=$this->GetNearbyAddres($localtion);
                $this->load->model('weixin/wxinformation_model');
                $Matlocaltion=array('latitude'=>$data['coordinate']['data']['lat'],
                        'longitude'=>$data['coordinate']['data']['lng']);
                $data['addreslist']=$this->wxinformation_model->MatchingDistance($Matlocaltion);
            }
        }else{
            $data['coordinate']='';
            $data['addreslist']='';
        }
        $data['signPackage']=$this->wxcode_model->GetSignPackage();//获取js sdk 配置
        $data['token']=$_SESSION['maptoken']=md5(date('Y-m-d H:i:s'));
        $this->load->view('weixin/myquery',$data);
    }
    /**
     * 查询附近的地点
     */
    function  maplist(){
        $this->load->database();
        $this->load->helper('array');
        $coulms=array('keyword','latitude','longitude','map_token');
        $data=elements($coulms, $this->input->post(), '');
        if($data['map_token'] != $_SESSION['maptoken']){
            $response=array('status'=>$this->config->item('request_fall'),'msg'=>'请求不合法!');
            echo json_encode($response);
            exit();
        } 
        $this->load->model('weixin/wxinformation_model');
        $response=$this->wxinformation_model->GetforAddres($data);
        echo json_encode($response);
        exit();
        //var_dump($response);
    }
    /**
     * 显示百度导航面板
     */
    function  Gnavigation(){
        $this->load->database();
        $this->load->helper('array');
        $coulms=array('method','started','destination');
        $data=elements($coulms, $this->input->get(), '');
        $this->load->view('weixin/navigation',$data);
    }
    /**
     * 转换gps坐标为百度坐标 且 返回经纬度 所在 地点名称
     */
    function  Getbaidumap(){
        $this->load->helper('array');
        $coulms=array('latitude','longitude');
        $data=elements($coulms, $this->input->post(), '');
        $this->load->database();
        $this->load->model('weixin/wxinformation_model');
        $response=$this->GetNearbyAddres($data);
        echo json_encode($response);
        exit();
    }
    /**
     * 转换gps为百度坐标 返回地址
     * @param    string     $data   
     * 
     */
    function GetNearbyAddres($data){
        if(empty($data['latitude']) || empty($data['longitude'])){
            return array('status'=>$this->config->item('request_fall'),'msg'=>'经纬度坐标没有正确获取!');
        }
        $latlon=$data['longitude'].','.$data['latitude'];
        $baidu_loc=$this->wxinformation_model->conversion_gps($latlon);
        if($baidu_loc->status ==0){
            $baidulocalhost=$baidu_loc->result['0']->y.','.$baidu_loc->result['0']->x;
            $baidumap=$this->wxinformation_model->getaddress($baidulocalhost);
        }else{
            return array('status'=>$this->config->item('request_fall'),'msg'=>'获取地址位置失败!');
        }
        if($baidumap->status == 0){
            $response['lat']=$baidumap->result->location->lat;
            $response['lng']=$baidumap->result->location->lng;
            $response['addres']=$baidumap->result->formatted_address;
            if ($baidumap->result->pois != ''){
                foreach ($baidumap->result->pois as $pois){
                    $list[]=$pois->addr;
                }
                $response['list']=array_unique($list);
            }
            return array('status'=>$this->config->item('request_suss'),'msg'=>'','data'=>$response);
        }else{
            return array('status'=>$this->config->item('request_fall'),'msg'=>'获取地址位置失败!');           
        }
    }
    /**
     * 测试开发期间进入新系统首页
     */
    function  newgoto(){
        $code=$this->input->get('code');
        $this->load->database();
        $this->load->model('common/wxcode_model');
        if(!empty($code)){
            $openid=$this->wxcode_model->getOpenid($code);//获取openid
            if(empty($openid)){
                exit('请重新进入!');
            }
            header('Location: http://test.recytl.com/index.php/nonstandard/system/welcome?openid='.$openid);
            //确保重定向后，后续代码不会被执行
            exit;
        }else{
            exit('请重新进入!');
        }
        
    }
    
}