<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通验证手机</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="/static/register/css/cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="/static/register/css/register.css"/>
		<script type="text/javascript" src="/static/register/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="/static/register/js/register.js"></script>		
	</head>
	<body>
		<!-- <a href="javascript:;" class="advert">
			<img src="/static/register/images/enroll.png"/>
		</a> -->
		<!--  entry start      -->
		<div class="logo"></div>
		<div class="cue">
		    <span>回收通/寄售通用户请在此登录</span>
		</div>
		<div class="entry login">
			<form action="" method="" name="">
				<div style="width:100%;height:auto;border:1px solid #d4d4d4;border-radius: 5px;overflow:hidden;">
					<div class="phone clearfix">
						<input type="" name="" id="mobile" value="" placeholder="请输入手机号码" class="fl enter" onblur="checkPhone(this)" onclick="vanish();"/>
						<!-- <span class="fr"><input id="invitation" type="button" value="获取验证码" onClick="Getcode(this);"/></span> -->
					</div>
					<div class="code clearfix">
						<input type="password" name=""  id="password" value="" placeholder="请输入密码" class="fl enter" onblur="passwordlength()" onclick="vanish();"/>
						<a href="javascript:;" class="eye fr" onclick="eyePic(this)"></a>
					</div>
					
					<div class="password clearfix sn" <?php if ($code!=1) { ?>style="display:none"<?php } ?>>
						<input type="" name="" id="code" value="" placeholder="请输入验证码" class="fl enter" onblur="codelength()" onclick="vanish();"/>
						<span class="fr chart" style="margin:11.5px 0px;"><img src="/codeimg/code_char.php" width="80px" onclick="this.src='/codeimg/code_char.php?name=2&d='+Math.random();" alt=""></span>
					</div>
				</div>

                <div class="forget">
                    <a class="forget-btn fr" href="/index.php/nonstandard/system/changepwdin">忘记密码？</a>
                </div>
				<p class="tip" style="height:0px;"></p>

                <!--				<p style="font-size:13px;"><a href="/index.php/nonstandard/system/usereg">去注册</a><a style="margin-left:20px" href="/index.php/nonstandard/system/changepwdin" style="font-size:14px;">忘记密码</a></p>-->
				<p class="seize"></p>
				<div class="submit">
					<input style="-webkit-appearance : none ;" type="button" name="" id="" value="登录" onclick="login()" />
				</div>
			</form>
			<div class="enroll" align="center">
			    <a class="enroll-btn" href="/index.php/nonstandard/system/usereg">新用户注册</a>
			</div>
		</div>
		<!--  entry start      -->
		<!-- <div class="discount">
			<img src="/static/register/images/cen.png"/>
		</div> -->
        <script src="/static/home/js/f_js.js"></script>
        <script src="/static/m/ajax/r_common.js"></script>
        <script src="/static/home/js/ajax_mobile.js?v=1000"></script>
	</body>
</html>
