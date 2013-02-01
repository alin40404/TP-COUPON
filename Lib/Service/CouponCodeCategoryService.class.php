<?php
/**
 * CouponCodeCategoryService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:47:10 CST 2012
 */
class CouponCodeCategoryService
{
	public function info($id)
	{
		$cates = F('coupon_code_cates');
		if(! $cates){
			$cates = $this->_createCaches();
		}
		return $cates['all'][$id];
	}
	
	public function getTree()
	{
		$result = array();
		$cates = F('coupon_code_cates');
		if(!$cates || empty($cates['tree'])){
			$cates = $this->_createCaches();
		}
		return $result = $cates['tree'];
	}
	
	public function getAll()
	{
		$result = array();
		$cates = F('coupon_code_cates');
		if(!$cates || empty($cates['all'])){
			$cates = $this->_createCaches();
		}
		return $result = $cates['all'];
	}
	
	public function clearCaches()
	{
		F('coupon_code_cates', null);
	}
	
	private function _createCaches()
	{
        $treeObj = new Tree();
        $cccModel = D('CouponCodeCategory');
        $cates = array();
        $res = $cccModel->getAll();
        foreach ($res as $rs){
        	$rs['parents'] = $cccModel->getParents($rs['id']);
        	$cates[$rs['id']] = $rs;
            $treeObj->setNode($rs['id'],
                              $rs['parent_id'],
                              $rs['name'],
                              $rs['sort_order']);
        }
        $category = $treeObj->getCateTree(0);
        if(! empty($category)){
            unset($category[0]);
        }
        $result = array('tree'=>$category,'all'=>$cates);
        F('coupon_code_cates', $result);
        return $result;
	}
}