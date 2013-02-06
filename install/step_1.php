<?php
/**
 * 第一步
 */
define('IN_TP_COUPON', TRUE);
include_once('./global.php');
$dirs = array('../Runtime', '../Runtime/Cache/Admin', '../Runtime/Cache/Home', '../Runtime/Cache/Public'
				, '../Runtime/Data', '../Runtime/Data/_fields', '../Runtime/Logs', '../Runtime/Temp', '../Public/Uploads');
foreach ($dirs as $dir){
	if(! is_dir($dir)){
		mk_dir($dir);
	}
}
$files = array('../Conf/db_config.php', '../Conf/config_ucenter.php');
foreach ($files as $file){
	if(! is_file($file)){
		file_put_contents($file, "<?php\n?>");
	}
}
include($template->getfile('step_1'));