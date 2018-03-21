<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>回收通微信后台管理系统</title>
    <link rel="icon" href="../../../../maijinadmin/img/recytl.ico">
    <link rel="stylesheet" href="../../../../maijinadmin/css/bootstrap.css" >
    <link rel="stylesheet" href="../../../../maijinadmin/css/sb-admin.css" >
    <link rel="stylesheet" href="../../../../maijinadmin/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../../../maijinadmin/css/morris-0.4.3.min.css">
    <link rel="stylesheet" href="../../../../maijinadmin/css/common.css">
    <script type="text/javascript" src="../../../../maijinadmin/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../../../../maijinadmin/js/common.js"></script>
  </head>
  <body>
    <div id="wrapper">
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="navbar-header">
          <a class="navbar-brand" href="javascript:void(0);">回收通微信后台管理系统</a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav side-nav">
            <?php               
                foreach ($modellist as $model) {
                    if($model['model_isview'] == '1'){
                        
                            echo '<li class="active"><a href="'.$model['model_url'].'"><i class="fa"></i>'.$model['model_name'].'</a></li>';
                        
                    }              
                }
            ?> 
          </ul>

          <ul class="nav navbar-nav navbar-right navbar-user">
            <li class="dropdown user-dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 张三 <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#" class="logoutadmin"><i class="fa fa-power-off"></i> 退出</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>