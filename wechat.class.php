<?php

// wechat class

class wechat{

	//message type
	const MSGTYPE_TEXT = 'text';
	const MSGTYPE_IMAGE = 'image';
	const MSGTYPE_MUSIC = 'music';
	const MSGTYPE_VOICE = 'voice';
	const MSGTYPE_NEWS = 'news';
	const MSGTYPE_VIDEO = 'video';
	const MSGTYPE_SHORTVIDEO = 'shortvideo';
	const MSGTYPE_URL = 'url';
	const MSGTYPE_LOCATION = 'location';
	
	//api prefix
	const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin/';
	//get token
	const GET_TOKEN_URL = 'token?';
	
	private $token;    //接入公众平台时设置的token
	private $appid;    //开发者公众号appID
	private $appsecret;    //开发者公众号appsecret
	private $access_token;    //接口调用凭证
	private $msg;    //回应的消息
	private $receive;    //接收的消息数据
	public $errMsg = "Hello shiyanlou";    //错误信息
	public $errCode=-1;    //错误代码
	public $dubug;    //是否调试（日志记录）

	
	function __construct($options = []){
		$this->token = isset($options['token'])?$options['token']:'';
        	$this->appid = isset($options['appid'])?$options['appid']:'';
        	$this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        	$this->dubug = isset($options['dubug'])?$options['dubug']:false;
		/*
		if ($this->checkExpire(7200)) {        //检查access_token 是否过期
            		$this->getAccessToken();
        	}
        	$this->access_token = $this->getTokenByCache();  
		*/
	}
	
	public function checkExpire($time = 7200){
		return true;
	}

	public function getAccessToken(){
		$url = self::API_URL_PREFIX . self::GET_TOKEN_URL . 'grant_type=client_credential&appid=' . $this->appid . '&secret=' . $this->appsecret;
		$result = $this->http_get($url);
		if($result){
			$this->log($result, 'Succeed to get access_token');
			$result = json_decode($result);
			$this->cache($result->access_token);
			return true;
		}else{
			$this->log('', 'Fail to get access_token');
			$this->cache('');
			return false;
		}
	}

	//make GET request
	public function http_get($url){
		$ch = curl_init();
		if(stripos($url,"https://")!==FALSE){        //https：// ,绕过证书验证
            		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            		curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        	}
        	curl_setopt($ch, CURLOPT_URL, $url);
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        	$result = curl_exec($ch);
        	$status = curl_getinfo($ch);
        	curl_close($ch);
        	if(intval($status["http_code"])==200){
            		return $result;
        	}else{
            		return false;
        	}
	}

	public function cache($value){
		$fp = fopen('./access_token.txt', 'w+');
		//echo 'fp is 0 ' . $fp . '0 is fp';
		if($fp){
			fwrite($fp, $value);
			fclose($fp);
			return true;
		}
	}

	public function log($data, $option){
		//impliment it latter	
	}

	public function getTokenByCache(){
		if(file_exists('./access_token.txt')){
			$content = file_get_contents('./access_token.txt');
			if($content == ''){
				$this->getAccessToken();
			}
		}else{
			$this->getAccessToken();
		}
		return file_get_contents('./access_token.txt');
	}

	public function getToken(){
		return $this->access_token;
	} 

	public function valid(){
		$echostr = $_GET["echostr"];
		if($this->checkSign()){
			echo $echostr;
			exit;
		}
	}
	
	public function checkSign(){
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$tmpArr = array('hellowx', $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( signature ){
			return true;
		}else{
			return false;
		}
	}

	public function getRec(){
		if($this->receive == NULL){
			$postStr = file_get_contents('php://input');
			if(!empty($postStr)){
				$this->receive = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			}
			//echo 'got here1';
		}
		$fp = fopen('./tmp.txt', 'w+');
		//echo 'got here2 ' . $this->receive;
		fwrite($fp, $this->receive['Content']);
		fclose($fp);
		return $this->receive;
	}
	
}


?>
