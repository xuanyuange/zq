<?php
define("IS_LOCAL",true);//false是线上，true是本地
define("TOKEN", "zq_weixin");

if(IS_LOCAL){
	define("ZQ_URL","http://zq0704.tunnel.2bdata.com");
}else{
	define("ZQ_URL","http://lr.zqphp.cn");
}

/**
 * 发送HTTP请求方法
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
function http($url, $params, $method = 'GET', $header = array(), $multi = false){
	$opts = array(
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER     => $header
	);

	/* 根据请求类型设置特定参数 */
	switch(strtoupper($method)){
		case 'GET':
			$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
			break;
		case 'POST':
			//判断是否传输文件
			$params = $multi ? $params : http_build_query($params);
			$opts[CURLOPT_URL] = $url;
			$opts[CURLOPT_POST] = 1;
			$opts[CURLOPT_POSTFIELDS] = $params;
			break;
		default:
			throw new Exception('不支持的请求方式！');
	}

	/* 初始化并执行curl请求 */
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data  = curl_exec($ch);
	$error = curl_error($ch);
	curl_close($ch);
	if($error) throw new Exception('请求发生错误：' . $error);
	return  $data;
}
/**
 * 判断是否是微信浏览器打开
 * @return boolean
 */
function is_weixin()
{
	if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
		return true;
	}
	return false;
}
/**
 * 判断是否是本地测试环境
 * @return boolean
 */
function is_local()
{
	if ( $_SERVER['SERVER_NAME']=='localhost') {
		return true;
	}
	return false;
}
/**
 * 生成一个随机字符串
 * @param unknown $length
 * @return Ambigous <NULL, string>
 */
function getRandChar($length){
	$str = null;
	$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	$max = strlen($strPol)-1;

	for($i=0;$i<$length;$i++){
		$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
	}
	return $str;
}
/**
 * 清空某个目录下的所有文件及文件夹
 * @param unknown $up_path
 */
function deldir($up_path){
	$dh=opendir($up_path);
	while ($file=readdir($dh))
	{
		if($file!="." && $file!="..")
		{
			$fullpath=$up_path."/".$file;
			if(!is_dir($fullpath))
			{
				unlink($fullpath);
			}
			else
			{
				deldir($fullpath);
			}
		}
	}
	closedir($dh);
}
/**
 * 判断是不是图片
 * @param unknown $filename
 * @return boolean
 */
function isImage($filename){
	$types = '.gif|.jpeg|.png|.bmp|.jpg';//定义检查的图片类型
	if(file_exists($filename)){
		$info = getimagesize($filename);
		$ext = image_type_to_extension($info['2']);
		return stripos($types,$ext);
	}else{
		return false;
	}
}
