<?php
class qqauth_utils
{
	/*
	 * POST 请求
	 */
	public static function post($sUrl,$aPOSTParam){
		$oCurl = curl_init();
		if(stripos($sUrl,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		}
		$aPOST = array();
		foreach($aPOSTParam as $key=>$val){
			$aPOST[] = $key."=".urlencode($val);
		}
		curl_setopt($oCurl, CURLOPT_URL, $sUrl);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS, join("&", $aPOST));
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return FALSE;
		}
	}

	public static function do_post($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);

		curl_close($ch);
		return $ret;
	}

	public static function get_url_contents($url)
	{
		if (ini_get("allow_url_fopen") == "1")
		return file_get_contents($url);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result =  curl_exec($ch);
		curl_close($ch);

		return $result;
	}
	
	public static function parseJson($input)
	{
		if(!function_exists('json_decode'))
		{
			function json_decode($input)
			{
				$comment = false;
				$out = '$x=';
	 
				for ($i=0; $i<strlen($input); $i++)
				{
					if (!$comment)
					{
					if (($input[$i] == '{') || ($input[$i] == '['))       $out .= ' array(';
					else if (($input[$i] == '}') || ($input[$i] == ']'))   $out .= ')';
					else if ($input[$i] == ':')    $out .= '=>';
					else                         $out .= $input[$i];         
				}
				else $out .= $input[$i];
				if ($input[$i] == '"' && $input[($i-1)]!="\\")    $comment = !$comment;
				}
				eval($out . ';');
				return $x;
			}
		}
		return json_decode($input,1);	
	}
}

class qqauth
{
	private static $_instance = null;
	private $_appid;
	private $_appkey;
	private $_callback;
	private $_scope;
	private function __construct($appid, $appkey, $callback, $scope)
	{
		$this->_appid = $appid;
		$this->_appkey = $appkey;
		$this->_callback = $callback;
		$this->_scope = $scope;
	}
	
	public static function getInstance($appid, $appkey, $callback, $scope)
	{
		if(self::$_instance === null){
			self::$_instance = new self($appid, $appkey, $callback, $scope);
		}
		return self::$_instance;
	}
	
	public function qq_login()
	{
		$_SESSION['qq']['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
		. $this->_appid . "&redirect_uri=" . urlencode($this->_callback)
		. "&state=" . $_SESSION['qq']['state']
		. "&scope=".$this->_scope;
		return $login_url;
	}

	public function qq_callback()
	{
		if($_REQUEST['state'] == $_SESSION['qq']['state']) //csrf
		{
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
			. "client_id=" . $this->_appid. "&redirect_uri=" . urlencode($this->_callback)
			. "&client_secret=" . $this->_appkey. "&code=" . $_REQUEST["code"];

			$response = qqauth_utils::get_url_contents($token_url);
			if (strpos($response, "callback") !== false)
			{
				$lpos = strpos($response, "(");
				$rpos = strrpos($response, ")");
				$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
				$msg = qqauth_utils::parseJson($response);
				if (isset($msg['error']))
				{
					echo "<h3>error:</h3>" . $msg['error'];
					echo "<h3>msg  :</h3>" . $msg['error_description'];
					exit;
				}
			}

			$params = array();
			parse_str($response, $params);

			//debug
			//print_r($params);

			//set access token to session
			$_SESSION['qq']["access_token"] = $params["access_token"];

		}
		else
		{
			exit("The state does not match. You may be a victim of CSRF.");
		}
	}

	public function get_openid()
	{
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
		. $_SESSION['qq']['access_token'];

		$str  = qqauth_utils::get_url_contents($graph_url);
		if (strpos($str, "callback") !== false)
		{
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}

		$user = qqauth_utils::parseJson($str);
		if (isset($user['error']))
		{
			echo "<h3>error:</h3>" . $user['error'];
			echo "<h3>msg  :</h3>" . $user['error_description'];
			exit;
		}

		//set openid to session
		return $_SESSION['qq']["openid"] = $user['openid'];
	}
	
	public function get_user_info($token, $openid)
	{
		$get_user_info = "https://graph.qq.com/user/get_user_info?"
		. "access_token=" . $token
		. "&oauth_consumer_key=" . $this->_appid
		. "&openid=" . $openid
		. "&format=json";

		$info = qqauth_utils::get_url_contents($get_user_info);
		$arr = qqauth_utils::parseJson($info);

		return $arr;
	}
	
	/**
	 * 发表一条微博
	 *
	 */
	public function add_t($text, $token, $openid)
	{
		$url = "https://graph.qq.com/t/add_t";
		$data = array(
            "access_token" 			=> $token,
            "oauth_consumer_key"    => $this->_appid,
            "openid"                => $openid,
            "format"                => "json",
            "content"               => $text,
            'syncflag'				=> 1
        );
		$info = qqauth_utils::post($url, $data);
		$arr = qqauth_utils::parseJson($info);
		return $arr;
	}
	
	/**
	 * 发表一条空间分享
	 *
	 */
	public function add_share($title, $_url, $site, $images, $token, $openid)
	{
		$url = "https://graph.qq.com/share/add_share";
		$data = array(
            "access_token" 			=> $token,
            "oauth_consumer_key"    => $this->_appid,
            "openid"                => $openid,
            "format"                => "json",
            'title'					=> $title,
            'url'					=> $_url,
            'site'					=> $site,
            'images'				=> $images,
            'nswb'					=> 1
        );
		$info = qqauth_utils::post($url, $data);
		$arr = qqauth_utils::parseJson($info);
		return $arr;
	}
}