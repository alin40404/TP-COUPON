<?php
/**
 * 第三步
 */
define('IN_TP_COUPON', TRUE);
include_once('./global.php');
include_once('./classes/db.class.php');
if(isPost()){
	$dbhost = $_POST['dbinfo']['dbhost'];
	$dbuser = $_POST['dbinfo']['dbuser'];
	$dbpw = $_POST['dbinfo']['dbpw'];
	$dbname = $_POST['dbinfo']['dbname'];
	$tablepre = $_POST['dbinfo']['tablepre'];
	if(empty($dbname)) exit('dbname invalid.');
	//创建数据库
	if(!$link = @mysql_connect($dbhost, $dbuser, $dbpw)) {
		$errno = mysql_errno($link);
		$error = mysql_error($link);
		if($errno == 1045) {
			exit('database_errno_1045' . $error);
		} elseif($errno == 2003) {
			exit('database_errno_2003' . $error);
		} else {
			exit('database_connect_error' . $error);
		}
	}
	if(mysql_get_server_info() > '4.1') {
		mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET ".DBCHARSET, $link);
	} else {
		mysql_query("CREATE DATABASE IF NOT EXISTS `$dbname`", $link);
	}

	if(mysql_errno()) {
		exit('database_errno_1044' . mysql_error());
	}
	mysql_close($link);
	
	//创建数据表
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, DBCHARSET);

	$sqlfile = 'quan.sql';
	$sql = file_get_contents($sqlfile);
	$sql = str_replace("\r\n", "\n", $sql);
	
	include($template->getfile('step_3'));
}

function create_db_conf()
{
	$dbinfo = $_POST['dbinfo'];
	$value = "array(\"DB_TYPE\"=> \"mysql\",\n";
	$value .= "\"DB_HOST\"=> \"$dbinfo[dbhost]\",\n";
	$value .= "\"DB_NAME\"=>\"$dbinfo[dbname]\",\n";
	$value .= "\"DB_USER\"=>\"$dbinfo[dbuser]\",\n";
	$value .= "\"DB_PWD\"=>\"$dbinfo[dbpw]\",\n";
	$value .= "\"DB_PORT\"=>\"$dbinfo[dbport]\",\n";
	$value .= "\"DB_PREFIX\"=>\"$dbinfo[tablepre]\")";
	$content = "<?php\nreturn " . $value . ";\n?>";
	file_put_contents('../Conf/db_config.php', $content);
	showjsmessage('创建数据库配置文件 ... 成功');
}

function create_uc_conf()
{
	$ucinfo = $_POST['uc'];
	$value = "define(\"UC_CONNECT\", \"\");\n";
	$value .= "define(\"UC_DBHOST\", \"".$ucinfo['UC_DBHOST']."\");\n";
	$value .= "define(\"UC_DBUSER\", \"".$ucinfo['UC_DBUSER']."\");\n";
	$value .= "define(\"UC_DBPW\", \"".$ucinfo['UC_DBPW']."\");\n";
	$value .= "define(\"UC_DBNAME\", \"".$ucinfo['UC_DBNAME']."\");\n";
	$value .= "define(\"UC_DBCHARSET\", \"".DBCHARSET."\");\n";
	$value .= "define(\"UC_DBTABLEPRE\", \"".$ucinfo['UC_DBTABLEPRE']."\");\n";
	$value .= "define(\"UC_DBCONNECT\", 0);\n";
	$value .= "define(\"UC_KEY\", \"".$ucinfo['UC_KEY']."\");\n";
	$value .= "define(\"UC_API\", \"".$ucinfo['UC_API']."\");\n";
	$value .= "define(\"UC_CHARSET\", \"".CHARSET."\");\n";
	$value .= "define(\"UC_IP\", \"\");\n";
	$value .= "define(\"UC_APPID\", ".$ucinfo['UC_APPID'].");\n";
	$value .= "define(\"UC_PPP\", ".$ucinfo['UC_PPP'].");";
	$content = "<?php\n" . $value . "\n?>";
	file_put_contents('../Conf/config_ucenter.php', $content);
	showjsmessage('创建Ucenter配置文件 ... 成功');
}

function create_install_lock()
{
	file_put_contents('../Runtime/install.lock', '');
}

function init_administrator()
{
	global $tablepre, $db;
	$admininfo = $_POST['admininfo'];
	$sql = "INSERT INTO `".$tablepre."admin_users` (`user_name`, `password`, `is_super`) VALUES ('$admininfo[user_name]', '".md5(md5($admininfo['founderpw']))."', 1)";
	$db->query($sql);
	showjsmessage('创建系统创始人帐号 ... 成功');
}

function finish_install()
{
	global $tablepre, $db;
	$sql = "INSERT INTO `".$tablepre."friend_link` (`link_id`, `site_name`, `position_id`, `link_type`, `link_url`, `sort_order`, `link_code`) VALUES
(null, '嫁娶网', 101, 1, 'http://www.ijiaqu.com', 1, '嫁娶网'),
(null, '极好居', 101, 1, 'http://www.jihaoju.com', 2, '极好居'),
(null, '青番茄', 101, 1, 'http://www.qfanqie.com', 3, '青番茄'),
(null, '青番茄', 101, 1, 'http://www.tp-coupon.com', 4, 'TP-COUPON')";
	$db->query($sql);
	showjsmessage('安装完成');
}

function insert_config()
{
	global $tablepre, $db;
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, 0, 'site_info', 'group', '', '', '', 1)";
	$db->query($sql);
	$group_site_info_id = $db->insert_id();
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, 0, 'smtp', 'group', '', '', '', 1)";
	$db->query($sql);
	$group_smtp_id = $db->insert_id();
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, 0, 'hidden', 'hidden', '', '', '', 1)";
	$db->query($sql);
	$group_hidden_id = $db->insert_id();
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, 0, 'sms', 'group', '', '', '', 1)";
	$db->query($sql);
	$group_sms_id = $db->insert_id();
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, 0, 'payment', 'group', '', '', '', 1)";
	$db->query($sql);
	$group_payment_id = $db->insert_id();
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, 0, 'open_platform', 'group', '', '', '', 24)";
	$db->query($sql);
	$group_open_platform_id = $db->insert_id();
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_hidden_id', 'captcha', 'hidden', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_hidden_id', 'captcha_width', 'hidden', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_hidden_id', 'captcha_height', 'hidden', '', '', '', 1)";
	$db->query($sql);
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'smtp_host', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'smtp_port', 'text', '', '', '25', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'smtp_user', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'smtp_pass', 'password', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'smtp_mail', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'mail_charset', 'select', 'UTF8,GB2312,BIG5', '', 'UTF8', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_smtp_id', 'mail_service', 'select', '0,1', '', '1', 0)";
	$db->query($sql);
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'site_name', 'text', '', '', 'TP-COUPON', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'site_title', 'text', '', '', '中国领先的优惠券平台', 2)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'site_keywords', 'textarea', '', '', '网购 优惠券 京东优惠券 当当优惠券 凡客优惠券', 3)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'site_description', 'textarea', '', '', '中国领先的优惠券平台', 4)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'time_format', 'text', '', '', 'Y-m-d H:i:s', 6)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'date_format', 'text', '', '', 'Y-m-d', 5)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'timezone', 'options', '-12,-11,-10,-9,-8,-7,-6,-5,-4,-3.5,-3,-2,-1,0,1,2,3,3.5,4,4.5,5,5.5,5.75,6,6.5,7,8,9,9.5,10,11,12', '', '8', 7)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'open_gzip', 'select', '0,1', '', '1', 8)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'thumb_width', 'text', '', '', '75', 9)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'thumb_height', 'text', '', '', '75', 10)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'image_water_path', 'text', '', '', './Public/Images/logo.png', 11)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'site_domain', 'text', '', '', '', 12)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'data_cache_time', 'text', '', '', '2', 13)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'service_qq', 'text', '', '', '89249294', 14)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'weibo_sina', 'text', '', '', 'jihaoju', 15)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'weibo_qq', 'text', '', '', 'jihaoju', 16)";
	$db->query($sql);
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'icp_number', 'text', '', '', '', 19)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'code_in_secret', 'text', '', '', '4', 22)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'url_rewrite', 'options', '0,1', '', '0', 21)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'invite_credit', 'text', '', '', '6', 23)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'statis_code', 'textarea', '', '', '', 23)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_site_info_id', 'max_left_days', 'text', '', '', '2', '32')";
	$db->query($sql);
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_sms_id', 'sms_url_send', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_sms_id', 'sms_url_sendtime', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_sms_id', 'sms_url_get', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_sms_id', 'sms_cdkey', 'text', '', '', '', 1)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_sms_id', 'sms_password', 'text', '', '', '', 1)";
	$db->query($sql);
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_payment_id', 'alipay_partner', 'text', '', '', '', 16)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_payment_id', 'alipay_key', 'password', '', '', '', 17)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_payment_id', 'alipay_seller_email', 'text', '', '', '', 18)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_payment_id', 'alipay_type', 'options', 'direct,warrant', '', 'warrant', 20)";
	$db->query($sql);
	
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'qq_appid', 'text', '', '', '', 25)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'qq_appkey', 'text', '', '', '', 26)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'qq_open', 'select', '0,1', '', '0', 27)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'sina_wb_akey', 'text', '', '', '', 28)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'sina_wb_skey', 'text', '', '', '', 29)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'sina_wb_office_id', 'text', '', '', '0', 30)";
	$db->query($sql);
	$sql = "INSERT INTO `".$tablepre."site_config` (`id`, `parent_id`, `code`, `type`, `store_range`, `store_dir`, `value`, `sort_order`)
			 VALUES(null, '$group_open_platform_id', 'sina_wb_open', 'select', '0,1', '', '0', 31)";
	$db->query($sql);
	showjsmessage('初始化系统设置 ... 成功');
}