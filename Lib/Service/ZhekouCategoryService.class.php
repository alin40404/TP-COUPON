<?php
/**
 * ZhekouCategoryService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:47:10 CST 2012
 */
class ZhekouCategoryService
{	
	public function getAll()
	{
		$result = array();
		$cates = F('zhekou_cates');
		if(!$cates){
			$cates = $this->_createCaches();
		}
		return $cates;
	}
	
	public function clearCaches()
	{
		F('zhekou_cates', null);
	}
	
	private function _createCaches()
	{
		$result = array();
        $res = M('zhekou_category')->order("sort_order ASC, id DESC")->select();
        foreach ($res as $rs) {
        	$result[$rs['id']] = $rs;
        }
        F('zhekou_cates', $result);
        return $result;
	}
}