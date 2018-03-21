<?php
if ( ! defined('BASEPATH'))exit('No direct script access allowed');
class Wxinformation_model extends CI_Model {
    //营业网点表
    private $branch='h_branch'; 
    //使用帮助表
    private $instruction='h_instruction';
    /**
     * 功能描述:报单成功页面  查询地址
     */
    function addresslist(){
        $sql='select branch_date,branch_address from '.$this->branch.' 
                where status=1 order by branch_sort desc limit 0,10';
        $res=$this->db->customize_query($sql);
        if($res && $res != '0'){
            return $res;
        }else{
            return false;
        }
    }
    /**
     * 获取当前的营业点
     * 
     */
    function province_list(){
        $sql='select GROUP_CONCAT(distinct branch_city),branch_province from '.$this->branch.'  
              where status=1  group by  branch_province';
        $query=$this->db->query($sql);
        $data=$this->db->fetch_query($query);
        return $data;
    }
    /**
     * 功能描述:使用帮助
     */
    function hellpinfo(){
        $sql='select instruction_name,instruction_content from '.$this->instruction;
        $res=$this->db->customize_query($sql);
        if($res && $res != '0'){
            return $res;
        }else{
            return false;
        }
    }    
    /**
     * 根据地址查询最近门店地址
     */ 
    function GetforAddres($address){        
        /* if(!empty($address['latitude']) && !empty($address['longitude'])){
            
        } */
        if(empty($address['keyword'])){
            return array('status'=>$this->config->item('request_fall'),'msg'=>'请输入您的地址!');
        } 
        $map=$this->Getbaiduip($address['keyword']);
        $response=$this->MatchingDistance(array('latitude'=>$map->result->location->lat,'longitude'=>$map->result->location->lng));
        return $response;
    }
    /**
     * 根据百度经纬度 查询位置最近的3个地点
     * 
     */
    function MatchingDistance($map){
        if(empty($map['latitude']) || empty($map['longitude']) ){
            return array('status'=>$this->config->item('request_fall'),'msg'=>'没有获取到您的位置!');
        }
        $sql='select branch_name as name ,branch_address as address,branch_lat as lat,branch_lon as lon,
              branch_phone as phone,branch_hours as housr from '.$this->branch.'
              where status=1';
        if(!empty($map['province'])){
              $sql =$sql . 'and  branch_province="'.$map['province'].'" ';
        }
        if(!empty($map['city'])){
              $sql =$sql . '  and  branch_city="'.$map['city'].'"';
        }
        if(!empty($map['area'])){
              $sql = $sql. ' and branch_county="'.$map['area'].'"';
        }
        //$p=($map['page']-1)*3;$n=$map['page']*3;
        $toail_query=$this->db->query($sql);
        $toail=ceil($toail_query->num_rows()/3);
        /*if($toail < $map['page']){
            return array('status'=>$this->config->item('request_fall'),'msg'=>'没有更多的数据!');
        }*/
        $query=$this->db->query($sql);
        $data=$this->db->fetch_query($query);
        if(is_null($data)){
            return array('status'=>$this->config->item('request_fall'),'msg'=>'该地区暂时还没有营业点!敬请期待!');
        }
        $loaction=$map['latitude'].','.$map['longitude'];
        $baidumap=$this->getaddress($loaction); 
        if($baidumap->status == 0){
            $started=$baidumap->result->formatted_address;
            if ($baidumap->result->pois != ''){
                $number= 1;
                foreach ($baidumap->result->pois as $pois){
                    $list[]=$pois->addr;
                    ++$number;
                    if($number == 5){
                        break;
                    }
                }
                $nearby=array_unique($list);
            }
        }else{
            return array('status'=>$this->config->item('request_fall'),'msg'=>'没有获取到您的坐标地址,请重新打开!');
        }
        $this->load->library('common/geohash');
        foreach ($data as $list){
            $k=$this->geohash->getDistance($map['latitude'],$map['longitude'],$list['lat'],$list['lon']);
            $maplist[$k]=array('name'=>$list['name'],'address'=>$list['address'],'lat'=>$list['lat'],'lon'=>$list['lon'],
                               'phone'=>$list['phone'],'housr'=>$list['housr'],
                               'map'=>array(
                                        'car'=>'/index.php/weixin/wxmap/Gnavigation?method=car&started='.$started.'&destination='.$list['address'],
                                        'bus'=>'/index.php/weixin/wxmap/Gnavigation?method=bus&started='.$started.'&destination='.$list['address'],
                                        'walk'=>'/index.php/weixin/wxmap/Gnavigation?method=walk&started='.$started.'&destination='.$list['address'],
                                 )
            );            
        }
        ksort($maplist);
        $result=array_slice($maplist,0,5);
        return array('status'=>$this->config->item('request_suss'),'msg'=>'','url'=>'',
                'data'=>array('nearby'=>$nearby,'map'=>$result,'toail'=>$toail,'page'=>''));
    }
    /**
     * 查询当前用户最后一次上报位置
     */
    function  GetUserLocation($openid){
        $sql='select location_id,lcoation_longitude,location_latitude from h_wxuser_location  where 
              location_openid="'.$openid.'" order by location_id desc limit 0,1;';
        $query=$this->db->query($sql);
        $result=$this->db->fetch_query($query);
        if(is_null($result)){
            return -1;
        }
        return array('latitude'=>$result['0']['location_latitude'],'longitude'=>$result['0']['lcoation_longitude']);
    }
    
    /**
     * 百度地图API  根据经纬度查询地址信息
     * @param     string   $localtion   经纬度坐标  ,逗号分隔
     * @return    object   坐标地址
     */
    function  getaddress($localtion){
        $url='http://api.map.baidu.com/geocoder/v2/';
        $data=array(
                'ak'=>$this->config->item('baidumap_api_ak'),
                'callback'=>'renderReverse',
                'location'=>$localtion,
                'output'=>'json',
                'pois'=>1
        );
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL, $url );
        curl_setopt ($ch, CURLOPT_POST, 1 );
        curl_setopt ($ch, CURLOPT_HEADER, 0 );
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $data );
        $return =curl_exec ($ch);
        curl_close ($ch);
        return json_decode($return);
    }
    /**
     * 百度地图Api 根据地址获取坐标
     * @param  string  $addres  详细地址
     * @return mixed
     */
    function  Getbaiduip($addres){
        $url='http://api.map.baidu.com/geocoder/v2/';
        $data=array(
                'ak'=>$this->config->item('baidumap_api_ak'),
                'address'=>$addres,
                'callback'=>'renderOption',
                'city'=>'北京市',
                'output'=>'json',
        );
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL, $url );
        curl_setopt ($ch, CURLOPT_POST, 1 );
        curl_setopt ($ch, CURLOPT_HEADER, 0 );
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $data );
        $return =curl_exec ($ch);
        curl_close ($ch);
        return json_decode($return);
    }
    /**
     * 百度地图API  gps坐标转为百度坐标
     * @param     string   $localtion   经纬度坐标  ,逗号分隔
     * @return    object   坐标地址
     */
    function  conversion_gps($loaction){
        $url='http://api.map.baidu.com/geoconv/v1/?';
        $data=array(
                'ak'=>$this->config->item('baidumap_api_ak'),
                'coords'=>$loaction,
                'form'=>3,
                'to'=>5,
                'output'=>'json',
        );
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL, $url );
        curl_setopt ($ch, CURLOPT_POST, 1 );
        curl_setopt ($ch, CURLOPT_HEADER, 0 );
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $data );
        $return =curl_exec ($ch);
        curl_close ($ch);
        return json_decode($return);
    }
}
/* End of file wxinformation_model.php */
/* Location: ./controllers/model/weixin/wxinformation_model.php */