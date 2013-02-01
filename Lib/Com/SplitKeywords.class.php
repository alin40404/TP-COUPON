<?php
/**
 * SplitKeywords.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Mon Apr 02 22:25:31 CST 2012
 */
class SplitKeywords
{
	/**
	 * 使用phpcms在线分词系统获取关键词
	 *
	 * 示例：SplitKeywords::phpcms(iconv('utf-8', 'gbk', '人民日报：统一思想凝共识 稳中求进谋发展'))
	 * @param string $data			文本内容，需要使用gbk编码
	 * @param int	 $number		需要获取关键词数量
	 * @return string
	 */
	public static function phpcms($data, $number = 3) {
		$apiUrl = 'http://tool.phpcms.cn/api/get_keywords.php';
		$data = trim(strip_tags($data));
		if(empty($data)) return '';
		$params = 'siteurl='.$_SERVER['HTTP_HOST'];
		$params .= '&charset=gbk&number='.$number;
		$params .= '&data='.urlencode($data);
		return dCurl($apiUrl, $params, true);
	}
	
	/**
	 * 使用phpcms在线分词系统获取关键词
	 *
	 * 示例：SplitKeywords::discuz('人民日报 统一思想 共识')
	 * @param string $contents			文本内容
	 * @return string
	 */
	function discuz($contents, $number=3){
		$rows = strip_tags($contents);
		$arr = array(' ',' ',"\s", "\r\n", "\n", "\r", "\t", ">", "“", "”","<br />");
		$qc_rows = str_replace($arr, '', $rows);
		if(strlen($qc_rows)>2400){
			if(function_exists('msubstr')){
				$qc_rows = msubstr($contents, 0, 2400, "utf-8", false);
			}else{
				$qc_rows = substr($qc_rows, '0', '2400');
			}
		}
		$data = @implode('', file("http://keyword.discuz.com/related_kw.html?title=$qc_rows&ics=utf-8&ocs=utf-8"));
		preg_match_all("/<kw>(.*)A\[(.*)\]\](.*)><\/kw>/",$data, $out, PREG_SET_ORDER);
		$key="";
		for($i=0;$i<$number;$i++){
			$key=$key.$out[$i][2];
			if($out[$i][2])$key=$key." ";
		}
		return $key;
	}
}