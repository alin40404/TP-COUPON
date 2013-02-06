<?php
//公共函数
function isPost()
{
	if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
		return true;
	}
	return false;
}

function isGet()
{
	if(strtolower($_SERVER['REQUEST_METHOD']) == 'get'){
		return true;
	}
	return false;
}

/**
 * 获得服务器上的 GD 版本
 *
 * @access      public
 * @return      int         可能的值为0，1，2
 */
function gd_version()
{
	static $version = -1;

	if ($version >= 0)
	{
		return $version;
	}

	if (!extension_loaded('gd'))
	{
		$version = 0;
	}
	else
	{
		// 尝试使用gd_info函数
		if (PHP_VERSION >= '4.3')
		{
			if (function_exists('gd_info'))
			{
				$ver_info = gd_info();
				preg_match('/\d/', $ver_info['GD Version'], $match);
				$version = $match[0];
			}
			else
			{
				if (function_exists('imagecreatetruecolor'))
				{
					$version = 2;
				}
				elseif (function_exists('imagecreate'))
				{
					$version = 1;
				}
			}
		}
		else
		{
			if (preg_match('/phpinfo/', ini_get('disable_functions')))
			{
				/* 如果phpinfo被禁用，无法确定gd版本 */
				$version = 1;
			}
			else
			{
				// 使用phpinfo函数
				ob_start();
				phpinfo(8);
				$info = ob_get_contents();
				ob_end_clean();
				$info = stristr($info, 'gd version');
				preg_match('/\d/', $info, $match);
				$version = $match[0];
			}
		}
	}

	return $version;
}

if(!function_exists('file_put_contents')) {
	function file_put_contents($filename, $s) {
		$fp = @fopen($filename, 'w');
		@fwrite($fp, $s);
		@fclose($fp);
		return TRUE;
	}
}

// 循环创建目录
function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

function runquery($sql) {
	global $tablepre, $db;

	if(!isset($sql) || empty($sql)) return;

	$sql = str_replace("\r", "\n", str_replace(' `'.ORIG_TABLEPRE, ' `'.$tablepre, $sql));
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) {
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		foreach($queries as $query) {
			$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) {
		$query = trim($query);
		if($query) {

			if(substr($query, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/CREATE TABLE `([a-z0-9_]+)` .*/is", "\\1", $query);
				showjsmessage('建立数据表 '.$name.' ... 成功');
				$db->query(createtable($query));
			} else {
				$db->query($query);
			}

		}
	}

}

function createtable($sql) {
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
	(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=".DBCHARSET : " TYPE=$type");
}

function showjsmessage($message) {
	echo '<script type="text/javascript">showmessage(\''.addslashes($message).' \');</script>'."\r\n";
	flush();
	ob_flush();
}