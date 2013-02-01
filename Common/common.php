<?php
/**
 * common.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu Apr 26 15:41:48 CST 2012
 */
/**
 * 清除所有的数据缓存
 *
 */
function clear_all_caches()
{
	if(C('DATA_CACHE_ON') && C('DATA_CACHE_TYPE') == 'File'){
		del_dir(C('DATA_CACHE_PATH'));
		mk_dir(C('DATA_CACHE_PATH'), 0755);
	}elseif(C('DATA_CACHE_ON') && C('DATA_CACHE_TYPE') != 'File'){
		$cacheObj = Cache::getInstance();
		if(method_exists($cacheObj, 'clear')){
			$cacheObj->clear();
		}
	}
	if(is_dir(DATA_PATH)){
		del_dir(DATA_PATH);
		mk_dir(DATA_PATH . '_fields/', 0755);
	}
	if(is_dir(CACHE_PATH)){
		del_dir(CACHE_PATH);
		mk_dir(CACHE_PATH, 0755);
	}
	if(is_file(RUNTIME_PATH . '~runtime.php')) @unlink(RUNTIME_PATH . '~runtime.php');
}

/**
 * 载入配置信息
 *
 * @access  public
 * @return  array
 */
function load_config()
{
	$arr = array();
	$data = F('site_config');
	if ($data === false)
	{
		$m = M('site_config');
		$res = $m->field("code, value")->where("parent_id>0")->select();
		foreach ($res AS $row)
		{
			$arr[$row['code']] = $row['value'];
		}

		/* 对数值型设置处理 */
		$arr['thumb_width']          = intval($arr['thumb_width']);
		$arr['thumb_height']         = intval($arr['thumb_height']);
		$arr['image_width']          = intval($arr['image_width']);
		$arr['image_height']         = intval($arr['image_height']);
		$arr['no_picture']           = !empty($arr['no_picture']) ? str_replace('../', './', $arr['no_picture']) : 'Images/no_picture.gif'; // 修改默认商品图片的路径
		$arr['qq']                   = !empty($arr['qq']) ? $arr['qq'] : '';
		F('site_config', $arr);
	}
	else
	{
		$arr = $data;
	}

	return $arr;
}
	
/**
 * 修正上传的文件路径
 * 
 */
function FixedUploadedFileUrl($imgUrl)
{
	return __ROOT__ . '/Public/Uploads/' . $imgUrl;
}

function service($name)
{
	static $services = array();
	if(isset($services[$name])){
		return $services[$name];
	}
	$class = $name.'Service';
	$services[$name] = new $class();
	return $services[$name];
}

/**
 * 重写URL地址
 * 此方法乃修改Thinkphp内置的U方法
 *
 * @param string $url
 * @return string
 */
function reUrl($url, $params=array(), $redirect=false, $suffix=true) {
	// 解析URL
    $info =  parse_url($url);
    $url   =  !empty($info['path'])?$info['path']:ACTION_NAME;
    // 解析子域名
    if($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 开启子域名部署
            $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
            // '子域名'=>array('项目[/分组]');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                if(false === strpos($key,'*') && 0=== strpos($url,$rule[0])) {
                    $domain = $key.strstr($domain,'.'); // 生成对应子域名
                    $url   =  substr_replace($url,'',0,strlen($rule[0]));
                    break;
                }
            }
        }
    }

    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }

    // URL组装
    $depr = C('URL_PATHINFO_DEPR');
    if($url) {
        if(0=== strpos($url,'/')) {// 定义路由
            $route   =  true;
            $url   =  substr($url,1);
            if('/' != $depr) {
                $url   =  str_replace('/',$depr,$url);
            }
        }else{
            if('/' != $depr) { // 安全替换
                $url   =  str_replace('/',$depr,$url);
            }
            // 解析分组、模块和操作
            $url   =  trim($url,$depr);
            $path = explode($depr,$url);
            $var  =  array();
            $var[C('VAR_ACTION')] = !empty($path)?array_pop($path):ACTION_NAME;
            $var[C('VAR_MODULE')] = !empty($path)?array_pop($path):MODULE_NAME;
            if(C('URL_CASE_INSENSITIVE')) {
                $var[C('VAR_MODULE')] =  parse_name($var[C('VAR_MODULE')]);
            }
            if(C('APP_GROUP_LIST')) {
                $group   = !empty($path)?array_pop($path):GROUP_NAME;
                if($group != C('DEFAULT_GROUP')) {
                    $var[C('VAR_GROUP')]  =   $group;
                }
            }
        }
    }

    if(C('URL_MODEL') == 0) { // 普通模式URL转换
        //$url   =  __APP__.'?'.http_build_query($var);
        $url   =  __ROOT__ . '/index.php?'.http_build_query($var);
        if(!empty($vars)) {
            $vars = http_build_query($vars);
            $url   .= '&'.$vars;
        }
    }else{ // PATHINFO模式或者兼容URL模式
        if(isset($route)) {
            //$url   =  __APP__.'/'.$url;
            $url   =  __ROOT__ . '/index.php/'.$url;
        }else{
            //$url   =  __APP__.'/'.implode($depr,array_reverse($var));
            $url   =  __ROOT__ . '/' . implode($depr,array_reverse($var));
        }
        if(!empty($vars)) { // 添加参数
            $vars = http_build_query($vars);
            $url .= $depr.str_replace(array('=','&'),$depr,$vars);
        }
        if($suffix) {
            $suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
            if($suffix) {
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
    }
    if($domain) {
        $url   =  'http://'.$domain.$url;
    }
    if($redirect) // 直接跳转URL
        redirect($url);
    else
        return strtolower($url);
}

// 取得某月天数,可用于任意月份
function getDays($month, $year)
{
    switch($month)
    {
        case 4:
        case 6:
        case 9:
        case 11:
            $days = 30;
            break;

        case 2:
            if ($year%4==0)
            {
                if ($year%100==0)
                {
                    $days = $year%400==0 ? 29 : 28;
                }
                else
                {
                    $days =29;
                }
            }
            else
            {
                $days = 28;
            }
            break;

        default:
            $days = 31;
            break;
    }

    return $days;
}

/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashes_deep($value)
{
	if (empty($value))
	{
		return $value;
	}
	else
	{
		return is_array($value) ? array_map('addslashes_deep', $value) : addslashes(trim($value));
	}
}

/**
 * 将对象成员变量或者数组的特殊字符进行转义
 *
 * @access   public
 * @param    mix        $obj      对象或者数组
 * @author   Xuan Yan
 *
 * @return   mix                  对象或者数组
 */
function addslashes_deepObj($obj)
{
    if (is_object($obj) == true)
    {
        foreach ($obj AS $key => $val)
        {
            $obj->$key = addslashes_deep($val);
        }
    }
    else
    {
        $obj = addslashes_deep($obj);
    }

    return $obj;
}

/**
 * 递归方式的对变量中的特殊字符去除转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function stripslashesDeep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('stripslashesDeep', $value) : stripslashes($value);
    }
}

function dir_path($path)
{
	$path = str_replace('\\', '/', $path);
	if(substr($path, -1) != '/') $path = $path.'/';
	return $path;
}

/**
 * 递归删除目录
 *
 * @param unknown_type $dir
 * @return unknown
 */
function del_dir($dir)
{
	$dir = dir_path($dir);
	if(!is_dir($dir)) return FALSE;
	$systemdirs = array('');
	if(in_array($dir, $systemdirs)) exit("Cannot remove system dir $dir !");
	$list = glob($dir.'*');
	foreach($list as $v)
	{
		is_dir($v) ? del_dir($v) : @unlink($v);
	}
    return @rmdir($dir);
}

/**
 * 验证表单令牌是否合法
 *
 * @param array $data
 * @return bool
 */
function checkFormToken($data, $name=null) {
	$name   = $name? $name : '_hash_';
	if(isset($_SESSION[C('SESSION_PREFIX') . $name])) {
		// 当前需要令牌验证
		if(empty($data[$name]) || $_SESSION[C('SESSION_PREFIX') . $name] != $data[$name]) {
			// 非法提交
			return false;
		}
		// 验证完成销毁session
		unset($_SESSION[C('SESSION_PREFIX') . $name]);
	}
	return true;
}

/**
 * 生成表单令牌
 *
 * @return string
 */
function buildFormToken($tokenName=null) {
	// 开启表单验证自动生成表单令牌
	$tokenName   = $tokenName ? $tokenName : '_hash_';
	$tokenValue = md5(microtime(TRUE));
	$_SESSION[C('SESSION_PREFIX') . $tokenName]  =  $tokenValue;
	return $tokenValue;
}

function get_upload_path()
{
	return $upload_path = 'Public/Uploads/';
}

/**
 * 上传文件
 * @param array	$upfiles		上传的文件
 * @param array	$config			上传类配置参数
 * @return string
 */
function upload_file(array &$upfiles, array $config = array())
{
	import("ORG.Net.UploadFile"); 
	$upload = new UploadFile(); //  
	$upload->maxSize  = 3145728 ; //  附件上传的最大值
	$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg'); //  
	$upload->savePath =  DOC_ROOT_PATH . get_upload_path(); //
	$upload->saveRule = 'time';
	$upload->autoSub = true;
	$upload->subType = 'date';
	$upload->dateFormat = 'Ym';
	if(! empty($config)){
		foreach ($config as $key=>$val){
			$upload->$key = $val;
		}
	}
	if(!$upload->upload()) {
		return $this->error($upload->getErrorMsg());
	}else{
		$upfiles =  $upload->getUploadFileInfo();
	}
	return '';
}

/**
* 上传文件
@param array $file            //文件数组
@param string $dir            //保存文件的目录
@param string $allow_file_types  //允许上传的文件类型
*/
function upload_one_file($file,$dir='',array $allow_file_types=array()){
    $upload_dir = '';
    $result=array('error'=>'','file_name'=>'');
    if ((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none')){
        if(empty($allow_file_types)){
            //$allow_file_types = '|GIF|JPG|PNG|SWF|DOC|XLS|XLSX|PPT|PPTX|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|CERT|';
            $allow_file_types = array('jpg', 'gif', 'png', 'jpeg','xls','xlsx','ppt','pptx','swf');
        }
        $pathinfo = pathinfo($file['name']);
        $ext = $pathinfo['extension'];
        if (! in_array(strtolower($ext), $allow_file_types)){
            $result['error']='文件类型错误';
            return $result;
        }
        if(empty($dir)){
        	$upload_path = DOC_ROOT_PATH . get_upload_path();
            $upload_dir = $upload_path . date('Ym').'/';
        }else{
        	$upload_dir = $dir;
        }
        if(!file_exists($upload_dir))
        {
            mk_dir($upload_dir, 0777);
        }
        $ext = array_pop(explode('.', $file['name']));
        $file_name = $upload_dir . md5($file['name'] . time()) . '.' . $ext;
        if (move_upload_file($file['tmp_name'], $file_name)){
            if(empty($dir)){
            	$result['file_name'] = str_replace($upload_path,'',$file_name);
            }else{
            	$result['file_name'] = $file_name;
            }
        }else{
            $result['error'] = '上传失败';
        }
        return $result;
    }
}

/**
 * 将上传文件转移到指定位置
 *
 * @param string $file_name
 * @param string $target_name
 * @return blog
 */
function move_upload_file($file_name, $target_name = '')
{
    if (function_exists("move_uploaded_file"))
    {
        if (move_uploaded_file($file_name, $target_name))
        {
            @chmod($target_name,0755);
            return true;
        }
        else if (copy($file_name, $target_name))
        {
            @chmod($target_name,0755);
            return true;
        }
    }
    elseif (copy($file_name, $target_name))
    {
        @chmod($target_name,0755);
        return true;
    }
    return false;
}

/**
 * 字符转换
 *
 * @param string $source_lang
 * @param string $target_lang
 * @param string $source_string
 * @return bool
 */
function ecs_iconv($source_lang, $target_lang, $source_string = '')
{
    static $chs = NULL;

    /* 如果字符串为空或者字符串不需要转换，直接返回 */
    if ($source_lang == $target_lang || $source_string == '' || preg_match("/[\x80-\xFF]+/", $source_string) == 0)
    {
        return $source_string;
    }

    if ($chs === NULL)
    {
    	import('@.ORG.Chinese');
        $chs = new Chinese();
    }

    return $chs->Convert($source_lang, $target_lang, $source_string);
}

/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
    {
        if (preg_match($chars, $user_email))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}

/**
 * 发送邮件
 *
 * @param string $name		用户名
 * @param strin $email		收信人邮件地址
 * @param string $subject	邮件主题
 * @param string $content	邮件内容
 * @param int $type			邮件格式，0：非html、1：html
 * @param bool $notification
 * @return bool
 */
function send_mail($name, $email, $subject, $content, $type = 0, $notification=false)
{
	$_CFG = load_config();
    /* 如果邮件编码不是YF_CHARSET，创建字符集转换对象，转换编码 */
    if ($_CFG['mail_charset'] != YF_CHARSET)
    {
        $name      = ecs_iconv(YF_CHARSET, $_CFG['mail_charset'], $name);
        $subject   = ecs_iconv(YF_CHARSET, $_CFG['mail_charset'], $subject);
        $content   = ecs_iconv(YF_CHARSET, $_CFG['mail_charset'], $content);
        $_CFG['site_name'] = ecs_iconv(YF_CHARSET, $_CFG['mail_charset'], $_CFG['site_name']);
    }
    $charset   = $_CFG['mail_charset'];
    /**
     * 使用mail函数发送邮件
     */
    if ($_CFG['mail_service'] == 0 && function_exists('mail'))
    {
        /* 邮件的头部信息 */
        $content_type = ($type == 0) ? 'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
        $headers = array();
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($_CFG['site_name']) . '?='.'" <' . $_CFG['smtp_mail'] . '>';
        $headers[] = $content_type . '; format=flowed';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($_CFG['site_name']) . '?='.'" <' . $_CFG['smtp_mail'] . '>';
        }

        $res = @mail($email, '=?' . $charset . '?B?' . base64_encode($subject) . '?=', $content, implode("\r\n", $headers));

        if (!$res)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * 使用smtp服务发送邮件
     */
    else
    {
        /* 邮件的头部信息 */
        $content_type = ($type == 0) ?
            'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
        $content   =  base64_encode($content);

        $headers = array();
        $headers[] = 'Date: ' . gmdate('D, j M Y H:i:s') . ' +0800';
        $headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
        $headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($_CFG['site_name']) . '?='.'" <' . $_CFG['smtp_mail'] . '>';
        $headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
        $headers[] = $content_type . '; format=flowed';
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = 'Content-Disposition: inline';
        if ($notification)
        {
            $headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($_CFG['site_name']) . '?='.'" <' . $_CFG['smtp_mail'] . '>';
        }

        /* 获得邮件服务器的参数设置 */
        $params['host'] = $_CFG['smtp_host'];
        $params['port'] = $_CFG['smtp_port'];
        $params['user'] = $_CFG['smtp_user'];
        $params['pass'] = $_CFG['smtp_pass'];

        if (empty($params['host']) || empty($params['port']))
        {
            // 如果没有设置主机和端口直接返回 false
            return false;
        }
        else
        {
            // 发送邮件
            if (!function_exists('fsockopen'))
            {
                //如果fsockopen被禁用，直接返回

                return false;
            }

            static $smtp;

            $send_params['recipients'] = $email;
            $send_params['headers']    = $headers;
            $send_params['from']       = $_CFG['smtp_mail'];
            $send_params['body']       = $content;

            if (!isset($smtp))
            {
            	import('@.ORG.Smtp');
                $smtp = new Smtp($params);
            }

            if ($smtp->connect() && $smtp->send($send_params))
            {
                return true;
            }
            else
            {

                return false;
            }
        }
    }
}

// 加密解密函数
// 参数解释    
// $string： 明文 或 密文    
// $operation：DECODE表示解密,其它表示加密    
// $key： 密匙    
// $expiry：密文有效期    
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
				return '';
			}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

/**
 * 判断是否为搜索引擎蜘蛛
 *
 * @access  public
 * @return  string
 */
function is_spider()
{
    static $spider = NULL;

    if ($spider !== NULL)
    {
        return $spider;
    }

    if (empty($_SERVER['HTTP_USER_AGENT']))
    {
        $spider = '';

        return '';
    }

    $searchengine_bot = array(
        'googlebot',
        'mediapartners-google',
        'baiduspider+',
        'msnbot',
        'yodaobot',
        'yahoo! slurp;',
        'yahoo! slurp china;',
        'iaskspider',
        'sogou web spider',
        'sogou push spider'
    );

    $searchengine_name = array(
        'GOOGLE',
        'GOOGLE ADSENSE',
        'BAIDU',
        'MSN',
        'YODAO',
        'YAHOO',
        'Yahoo China',
        'IASK',
        'SOGOU',
        'SOGOU'
    );

    $spider = strtolower($_SERVER['HTTP_USER_AGENT']);

    foreach ($searchengine_bot AS $key => $value)
    {
        if (strpos($spider, $value) !== false)
        {
            $spider = $searchengine_name[$key];
            return $spider;
        }
    }

    $spider = '';

    return '';
}

/**
 * 以curl方式发送数据
 * 
 * @param string        $url        接收数据的url
 * @param string        $data       需要发送的数据,如：a=1&b=2&c=3
 * @param bool          $post       是否以post方式提交,false为get 方式
 */
function dCurl($url, $data, $post = true)
{
    $ch = curl_init();
    if(true === $post){
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }else{
        $url .= "?$data";
        curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/*-------------------------------------中文分词及全文检索所用方法-------------------------------------------*/
/**
 * utf8字符转Unicode字符
 * @param string $char 要转换的单字符
 * @return void
 */
function utf8_to_unicode($char)
{
	switch(strlen($char))
	{
		case 1:
			return ord($char);
		case 2:
			$n = (ord($char[0]) & 0x3f) << 6;
			$n += ord($char[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($char[0]) & 0x1f) << 12;
			$n += (ord($char[1]) & 0x3f) << 6;
			$n += ord($char[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($char[0]) & 0x0f) << 18;
			$n += (ord($char[1]) & 0x3f) << 12;
			$n += (ord($char[2]) & 0x3f) << 6;
			$n += ord($char[3]) & 0x3f;
			return $n;
	}
}

/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @param string $depart 分隔,默认为空格为单字
 * @return string
 */
function str_to_unicode_word($str,$depart=' ')
{
	$arr = array();
	$str_len = mb_strlen($str,'utf-8');
	for($i = 0;$i < $str_len;$i++)
	{
		$s = mb_substr($str,$i,1,'utf-8');
		if($s != ' ' && $s != '　')
		{
			$arr[] = 'ux'.utf8_to_unicode($s);
		}
	}
	return implode($depart,$arr);
}


/**
 * utf8字符串分隔为unicode字符串
 * @param string $str 要转换的字符串
 * @return string
 */
function str_to_unicode_string($str)
{
	$string = str_to_unicode_word($str,'');
	return $string;
}

//分词
function segment($text, $num=null)
{
	if(strtolower(C('DEFAULT_CHARSET')) == 'utf-8' || strtolower(C('DEFAULT_CHARSET')) == 'utf8'){
		$charset = 'utf8';
	}else{
		$charset = strtolower(C('DEFAULT_CHARSET'));
	}
	$list = array();
	if(empty($text)) return $list;
	$root = LIB_PATH;
	//检测是否已安装php_scws扩展
	if(function_exists("scws_open"))
	{
		$sh = scws_open();
		scws_set_charset($sh,$charset);
		scws_set_dict($sh, $root.'ORG/scws/dict.'.$charset.'.xdb');
		scws_set_rule($sh, $root.'ORG/scws/rules.'.$charset.'.ini');
		scws_set_ignore($sh,true);
		scws_send_text($sh, $text);
		if(is_int($num)){
			$words = scws_get_tops($sh, $num);
			foreach($words as $word)
			{
				$list[] = $word['word'];
			}
		}else{
			while ($tmp = scws_get_result($sh))
			{
				foreach ($tmp as $key => $w){
					$list[] = $w['word'];
				}
			}
		}
		scws_close($sh);
	}
	else
	{
		import('@.ORG.scws.pscws4');
		if(! class_exists('PSCWS4')) return $list;
		$pscws = new PSCWS4();
		$pscws->set_charset($charset);
		$pscws->set_dict($root.'ORG/scws/dict.'.$charset.'.xdb');
		$pscws->set_rule($root.'ORG/scws/rules.'.$charset.'.ini');
		$pscws->set_ignore(true);
		$pscws->send_text($text);
		if(is_int($num)){
			$words = $pscws->get_tops($num);
			foreach($words as $word)
			{
				$list[] = $word['word'];
			}
		}else{
			while ($tmp = $pscws->get_result())
			{
				foreach ($tmp as $key => $w){
					$list[] = $w['word'];
				}
			}
		}
		$pscws->close();
	}
	return $list;
}
/*-------------------------------------框架自带扩展函数库------------------------------------------*/
/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其他编码
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
    if(function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset);
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}
/**
 +----------------------------------------------------------
 * 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0)
{
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 过滤恶意代码，防止XSS攻击
 *
 * @param string $val
 * @return string
 */
function remove_xss($val) {
   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
   // this prevents some character re-spacing such as <java\0script>
   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

   // straight replacements, the user should never need these since they're normal characters
   // this prevents like <IMG SRC=@avascript:alert('XSS')>
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      // ;? matches the ;, which is optional
      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

      // @ @ search for the hex values
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      // @ @ 0{0,7} matches '0' zero to seven times
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }

   // now the only remaining whitespace attacks are \t, \n, and \r
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}

/**
 +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 +----------------------------------------------------------
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
          $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}