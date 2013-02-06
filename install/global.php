<?php
defined('IN_TP_COUPON') or die('hack attempt.');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
@set_time_limit(1000);
set_magic_quotes_runtime(0);
define('ROOT_PATH', dirname(__FILE__).'/../');
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
if(!IS_CLI) {
	// 当前文件名
	if(!defined('_PHP_FILE_')) {
		if(IS_CGI) {
			//CGI/FASTCGI模式下
			$_temp  = explode('.php',$_SERVER['PHP_SELF']);
			define('_PHP_FILE_',  rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
		}else {
			define('_PHP_FILE_',    rtrim($_SERVER['SCRIPT_NAME'],'/'));
		}
	}
	if(!defined('__ROOT__')) {
		// 网站URL根目录
		$_root = dirname(_PHP_FILE_);
		$_root = ($_root=='/' || $_root=='\\') ? '' : $_root;
		define('__ROOT__',   $_root);
	}
}
define('CHARSET', 'utf-8');
define('DBCHARSET', 'utf8');
define('ORIG_TABLEPRE', 'dbs_');
require_once('./includes/func.php');
require_once ('./classes/template.class.php');
$options = array(
    'template_dir' => 'templates/', //指定模板文件存放目录
    'cache_dir' => 'templates_c/', //指定缓存文件存放目录
    'auto_update' => false, //当模板文件有改动时重新生成缓存 [关闭该项会快一些]
    'cache_lifetime' => 0, //缓存生命周期(分钟)，为 0 表示永久 [设置为 0 会快一些]
    'suffix' => '.html', //后缀
);
$template = Template::getInstance(); //使用单件模式实例化模板类
$template->setOptions($options); //设置模板参数