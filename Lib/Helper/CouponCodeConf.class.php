<?php
/**
 * CouponCodeConf.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:01:25 CST 2012
 */
class CouponCodeConf
{
	public static function fetch_limit_conf()
	{
		return $conf = array(
							'101'		=>	'每个账户一张',
							'102'		=>	'每个账户每天一张',
							'200'		=>	'不限制',
							);
	}
	
	/**
	 * 商家推荐位置配置
	 *
	 */
	public static function mall_rec_position_conf()
	{
		return $conf = array(
							'101'		=>	'首页推荐商家',
							'102'		=>	'详情页推荐商家',
							);
	}
}