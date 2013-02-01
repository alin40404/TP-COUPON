<?php
/**
 * AdvBehavior.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed May 02 14:42:29 CST 2012
 */
class AdvBehavior extends Behavior 
{
	public function run(&$params)
	{
		if(isset($params['ad_id'])){
			$advS = service('Adv');
			$advS->clearCache($params['ad_id']);
		}
		if(isset($params['pos_id'])){
			$advS = service('Adv');
			$advS->clearPosCache($params['pos_id']);
		}
	}
}