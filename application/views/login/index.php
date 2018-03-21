<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8">
        <title>回收通系统管理</title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <!-- CSS -->
        <link rel="stylesheet" href="../../../static/weixin/public/css/reset.css">
        <link rel="stylesheet" href="../../../static/weixin/public/css/supersized.css">
        <link rel="stylesheet" href="../../../static/weixin/public/css/style.css">
        <!-- Javascript -->
        <script src="../../../static/weixin/public/js/jquery-1.8.2.min.js"></script>
        <script src="../../../static/weixin/public/js/supersized.3.2.7.min.js"></script>
        <script src="../../../static/weixin/public/js/supersized-init.js"></script>
        <script src="../../../static/weixin/public/js/scripts.js"></script>
        <script src="../../../static/weixin/public/js/common.js"></script>
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script>
			$(document).ready(function(){
				$("#getcode_char").click(function(){
					$(this).attr("src",'/codeimg/code_char.php?' + Math.random());
				});
			});
		</script>		
    </head>
    <body>
        <div class="page-container">
            <h1>回收通系统管理</h1>
            <form action="#" method="post">
                <input type="text" name="username" class="username" id="login_name"  placeholder="用户名">
                <input type="password" name="password" class="password" id="login_pwd" placeholder="密码">
                <div id="code"><input style="width:200px;" type="text" class="input" id="code_char" maxlength="4" placeholder="验证码"/> <img src="/codeimg/code_char.php" id="getcode_char" title="看不清，点击换一张" align="absmiddle"></p></div>
				<div class="error" id="errorinfo"><span></span></div>
				<button type="button" onclick="login();">提交</button>
            </form>
            <div class="connect">
                <p>Or connect with:</p>
                <p>
                    <a class="facebook" href=""></a>
                    <a class="twitter" href=""></a>
                </p>
            </div>
        </div>
    </body>
</html>


