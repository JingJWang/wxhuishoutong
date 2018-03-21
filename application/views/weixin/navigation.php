<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
body, html{width: 100%;height: 100%;margin:0;font-family:"微软雅黑";}
#l-map{height:300px;width:100%;}
#r-result,#r-result table{width:100%;font-size:12px;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=bMcQsbwpzGsfWFBGKDHMHjrb"></script>
<title>展示公交换乘的结果面板</title>
</head>
<body>
<div id="l-map"></div>
<div id="r-result"></div>
</body>
</html>
<script type="text/javascript">
<?php  
    switch ($method){
        case 'car':
            ?>
            var map = new BMap.Map("l-map");
            map.centerAndZoom(new BMap.Point(116.404, 39.915), 12);
            var driving = new BMap.DrivingRoute(map, {renderOptions: {map: map, panel: "r-result", autoViewport: true}});
            driving.search("<?php echo $started ?>", "<?php echo $destination; ?>");
            <?php 
            break;
        case  'bus':
            ?>
            // 百度地图API功能
            var map = new BMap.Map("l-map");
            map.centerAndZoom(new BMap.Point(116.404, 39.915), 12);
            var transit = new BMap.TransitRoute(map, {
                renderOptions: {map: map, panel: "r-result"}
            });
            transit.search("<?php echo $started ?>", "<?php echo $destination; ?>");
            <?php 
            break;
        case   'walk':
            ?>
            //百度地图API功能
            var map = new BMap.Map("l-map");
            map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);
            var walking = new BMap.WalkingRoute(map, {renderOptions: {map: map, panel: "r-result", autoViewport: true}});
            walking.search("<?php echo $started ?>", "<?php echo $destination; ?>");
            <?php 
            break;
    }
?>
</script>
