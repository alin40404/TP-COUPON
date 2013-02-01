<?php
/**
 * CacheAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu Apr 26 15:19:47 CST 2012
 */
class CacheAction extends AdminCommonAction
{
	/**
	 * 清除缓存
	 *
	 */
	public function clear()
	{
		if($this->isPost() && $this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			set_time_limit(0);
			//清空数据缓存
			clear_all_caches();
			$this->ajaxReturn('', buildFormToken(), 1);
		}
		$this->assign('_hash_', buildFormToken());
		$this->assign('ur_href', '清除系统缓存');
		$this->display();
	}
}