<?php
/**
 * CouponCodeMallRecsBehavior.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed Apr 18 11:19:06 CST 2012
 */
class CouponCodeMallRecsBehavior extends Behavior 
{
	public function run(&$params)
	{
		$ccmrService = service('CouponCodeMallRecs');
		$ccmrService->clearCache();
	}
}