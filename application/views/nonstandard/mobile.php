<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>回收通验证手机</title>
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport"/>
		<link rel="stylesheet" type="text/css" href="/static/register/css/cssReset.css"/>
		<link rel="stylesheet" type="text/css" href="/static/register/css/register.css"/>
	</head>
	<body>
		<a href="javascript:;" class="advert">
			<img src="/static/register/images/enroll.png"/>
		</a>
		<!--  entry start  -->
		<div class="entry" >
			<form action="" method="" name="" >
				<div style="width:100%;height:auto;border:1px solid #d4d4d4;border-radius: 5px;overflow:hidden;" onclick="vanish();" class="newJJ">
					<div class="password clearfix" >
						<input type="" name="" id="imgcode" value="" placeholder="请输入图形验证码" class="fl enter" onblur="codelength()" />
						<span class="fr chart" style="margin:10.5px 0px;"><img src="/codeimg/code_char.php" width="80px" onclick="this.src='/codeimg/code_char.php?name=2&d='+Math.random();" alt=""></span>
					</div>
					<div class="phone clearfix">
						<input type="" name="" id="mobile" value="" placeholder="请输入手机号码" class="fl enter" onblur="checkPhone(this)" onclick="vanish();"/>
						<span class="fr"><input id="invitation" type="button" value="获取验证码" onClick="Getcode(this);"/></span>
					</div>
					<div class="code clearfix">
						<input type="" name="" id="code" value="" placeholder="请输入验证码" class="fl enter" onblur="codelength()" onclick="vanish();"/>
					</div>
					<div class="password clearfix">
						<input type="password" name=""  id="password" value="" placeholder="请设置6位以上密码" class="fl enter" onblur="passwordlength()" onclick="vanish();"/>
						<a href="javascript:;" class="eye fr" onclick="eyePic(this)"></a>
					</div>
					<div id="promoCode" class="password clearfix sn">
                    	<input type="text" name="" id="promoCodes" <?php if($promoCode!='')echo "value=".$promoCode." disabled=disabled style=color:"."#575757;background-color:"."transparent;opacity:1"; ?> placeholder="请输入推广码" class="fl enter"/>
                        <div class="hint" <?php if($promoCode!='')echo " style=display:"."none"; ?>>选填</div>
                    </div>
				</div>

				<p class="tip"></p>
				<div class="submit">
					<input type="button" name="" id="" value="立即注册" onclick="submitSuccess();Checkcode(this);" />
				</div>
			</form>
		</div>
		<!--  entry start      -->
		<div class="discount">
			<img src="/static/register/images/cen.png"/>
		</div>
		<div class="explain">
			<p>1. 高价回收数码奢侈品</p>
			<p>2. 注册就送50环保基金加300通花</p>
			<p>3. 做任务最低得3元现金</p>
			<p>4. 每邀请一个小伙伴可得0.5元,多邀多得</p>

		</div>
		<script type="text/javascript" src="../../../static/register/js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../../../static/register/js/register.js"></script>
        <script type="text/javascript" src="../../../static/home/js/f_js.js"></script>
        <script type="text/javascript" src="../../../static/home/js/ajax_common.js"></script>
        <script type="text/javascript" src="../../../static/home/js/ajax_mobile.js"></script>
	</body>
</html>
