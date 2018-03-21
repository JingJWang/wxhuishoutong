<?php 
 
	function verify_id($id=null) { //判断是否为数字
	    if($id==null) {
    	    exit('没有提交参数！'); 
    	} elseif(!is_numeric($id)) { 
    	    exit('提交的参数非法！'); 
    	} 
    	$id = intval($id); //将变量转成整形
    	 
    	return $id; 
	}
	/* 检查和转义字符 */
	function safe_str($str){
		if(!get_magic_quotes_gpc())	{
			if( is_array($str) ) {
				foreach($str as $key => $value) {
					$str[$key] = safe_str($value);
				}
			}else{
				$str = addslashes($str);
			}
		}
		return $str;
	}


	function post_check(){
		if (isset($_GET)) {
			foreach ($_GET as $_key => $_value) {
				$_GET[$_key] = safe_str($_value);
			}
		}
		if (isset($_POST)) {
			foreach ($_POST as $_key => $_value) {
				$_POST[$_key] = safe_str($_value);
			}
		}
		if (isset($_COOKIE)) {
			foreach ($_COOKIE as $_key => $_value) {
				$_COOKIE[$_key] = safe_str($_value);
			}
		}
		if (isset($_REQUEST)) {
			foreach ($_REQUEST as $_key => $_value) {
				$_REQUEST[$_key] = safe_str($_value);
			}
		}
	}

	function safe_upimg($file,$size){
		$uptypes=array(  
		    'image/jpg',
		    'image/jpeg',
		    'image/png'
		);
		$max_file_size=$size*1024*1024;     //上传文件大小限制, 单位BYTE  
		
    	switch (true) {
			case $file['error']==1:
				$errorMsg = '大小超过了 php.ini 中 upload_max_filesize 限制值';
				break;
			case $file['error']==2:
				$errorMsg = '大小超过 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
				break;
			case $file['error']==3:
				$errorMsg = '文件只有部分被上传。';
				break;
			case $file['error']==4:
				$errorMsg = '没有文件被上传';
				break;
			case !in_array($file["type"], $uptypes):
				$errorMsg = '上传类型不合法';
				break;
			case $max_file_size < $file['size']:
				$errorMsg = '上传文件大小超出框架设置';
				break;
			default:
				break;
		}
		if (isset($errorMsg)) {
			return $errorMsg;
		}else{
			return 1;
		}
	}
