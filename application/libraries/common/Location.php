<?php
/*
 *  地理位置扩展
 *            
 *  
 *  函数列表
 *          returnSquarePoint()  查询  一个中心点   4个角  的坐标位置
 *          getaddress()         百度地图API  根据经纬度查询地址信息
 *          Getbaiduip()         百度地图Api 根据地址获取坐标
 *          conversion_gps()     百度地图API  gps坐标转为百度坐标
 *          getDistance()        根据两点间的经纬度计算距离
 */
class Location extends CI_Model{    
    /**
     * 查询  一个中心点   4个角  的坐标位置
     * @param      double         $lng  经度
     * @param      double         $lat  纬度
     * @param      int            $distance  范围 单位千米 默认1千米以内
     * @return     array          符合距离 4个点的坐标
     */
    function returnSquarePoint($lng, $lat,$distance = 1){
        $earth_radius=6371;
        $dlng =  2 * asin(sin($distance / (2 * $earth_radius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance/$earth_radius;
        $dlat = rad2deg($dlat);
        return array(
                'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
                'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
                'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
                'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
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
    /** @desc 根据两点间的经纬度计算距离
     *  @param      float       $lat1 第一个纬度值
     *  @param      float       $lng1 第一个经度值
     *  @param      float       $lat2 第二个纬度值
     *  @param      float       $lng2 第二个经度值
     *  @return     int         距离
     */
       static function getDistance($lat1, $lng1, $lat2, $lng2)  {
           
        if(empty($lat1) || empty($lng1) || empty($lat2) || empty($lng2)){
            return 0;
        }   
        //地球半径单位米 approximate radius of earth in meters
        $earthRadius = 6367000; 
        /* 
         * Convert these degrees to radians
         * to work with the formula
         */
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        /*
         Using theHaversine formula
         http://en.wikipedia.org/wiki/Haversine_formula
         calculate the distance
         */
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;        
        return round($calculatedDistance);
    }
}