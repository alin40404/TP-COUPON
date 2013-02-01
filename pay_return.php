<?php
/**
 * alipay.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Fri Apr 27 16:01:55 CST 2012
 */
header("Content-type: text/html; charset=utf-8");
define('IN_PAYMENT', true);
$_GET['m'] = 'Payment';
$_GET['a'] = 'pay_callback';
$_GET['type'] = 'alipay';
include_once('./index.php');