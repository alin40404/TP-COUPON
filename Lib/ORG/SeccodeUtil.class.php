<?php
class SeccodeUtil
{
	public static function fileext($filename) {
		return addslashes(trim(substr(strrchr($filename, '.'), 1, 10)));
	}
	
	public static function encrypts_word($word)
	{
		return substr(md5($word), 1, 10);
	}
	
	/**
	 * 将验证码保存到session
     *
 	 * @access  private
     * @param 	string	$idhash		session名称
     * @param   string  $word   	原始字符串
     * @return  void 
     */
	public static function record_word($idhash, $word)
	{
		$seccode = base64_encode(self::encrypts_word($word));
		$_SESSION[$idhash] = $seccode;
		return $seccode;
	}
	
	public static function generate_word($length = 4)
	{
		$chars = '2346789BCEFGHJKMPQRTVWXY';

		for ($i = 0, $count = strlen($chars); $i < $count; $i++)
		{
			$arr[$i] = $chars[$i];
		}

		mt_srand((double) microtime() * 1000000);
		shuffle($arr);

		return substr(implode('', $arr), 5, $length);
	}

	public static function make_seccode($idhash)
	{
		$word = self::generate_word();
		self::record_word($idhash, $word);
		return $word;
	}

	/**
	 * 检查给出的验证码是否和session中的一致
	 *
	 * @access  public
	 * @param   string  $word   验证码
	 * @return  bool
	 */
	public static function check_word($idhash, $word)
	{
		$recorded = isset($_SESSION[$idhash]) ? base64_decode($_SESSION[$idhash]) : '';
		$given    = self::encrypts_word(strtoupper($word));
		$result = (preg_match("/$given/", $recorded));
		unset($_SESSION[$idhash]);
		return $result;
	}
}