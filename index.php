<?php
require 'wechat.class.php';
require 'config.php';
$options = [
	'token' => TOKEN,
	'appid' => APPID,
	'appsecret' => APPSECRET,
	'dubug' => true,
	];
$wechatObj = new wechat($options);
if(isset($_GET["echostr"])){
	$wechatObj->valid();
}
//echo $wechatObj->getToken();
//$messageRec = $wechatObj->getRec();

/*
echo $_GET["echostr"];
exit;
*/
?>
