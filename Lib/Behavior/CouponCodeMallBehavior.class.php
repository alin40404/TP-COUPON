<?php
/**
 * CouponCodeMallBehavior.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 15:28:14 CST 2012
 */
class CouponCodeMallBehavior extends Behavior 
{
	public function run(&$params)
	{
		$ccmService = service('CouponCodeMall');
		$ccmService->clearCache($params['id']);
		$params = null;
		B('CouponCodeMallRecs', $params);
	}
}