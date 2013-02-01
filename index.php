<?php
if(! is_file('./Runtime/install.lock')){
	exit('<center><strong><a href="./install/">please install first.</a></strong></center>');
}
//解决linux下文件名大小问题
if(! isset($_GET['a'])){
	$_GET['a'] = 'index';
}

//解决因美化URL导致无法加载模块的问题
if(isset($_GET['m'])) $_GET['m'] = ucfirst($_GET['m']);
//项目物理根路径
define('DOC_ROOT_PATH', rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);
// 定义ThinkPHP框架路径   
define('THINK_PATH', DOC_ROOT_PATH . 'ThinkPHP/');
//定义项目名称和路径   
define('APP_NAME', '');   
define('APP_PATH', DOC_ROOT_PATH);
//项目版本
define('APP_VERSION', '1.17');
//开启Debug模式
define('APP_DEBUG',true);
/* 验证码 */
define('CAPTCHA_CODE','seccode'); //session保存验证码的名称
define('CAPTCHA_REGISTER',          1); //注册时使用验证码
define('CAPTCHA_LOGIN',             2); //登录时使用验证码
define('CAPTCHA_COMMENT',           4); //评论时使用验证码
define('CAPTCHA_ADMIN',             8); //后台登录时使用验证码
define('CAPTCHA_LOGIN_FAIL',       16); //登录失败后显示验证码
define('CAPTCHA_MESSAGE',          32); //留言时使用验证码
//字符编码
define('YF_CHARSET', 'UTF-8');
// 加载框架入口文件
require(THINK_PATH."ThinkPHP.php");