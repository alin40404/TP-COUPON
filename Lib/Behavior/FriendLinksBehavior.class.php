<?php
/**
 * FriendLinksBehavior.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu Apr 26 16:53:47 CST 2012
 */
class FriendLinksBehavior extends Behavior 
{
	public function run(&$params)
	{
		$flService = service('FriendLinks');
		$flService->clearCache();
	}
}