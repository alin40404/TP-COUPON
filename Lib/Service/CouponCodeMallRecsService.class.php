<?php
/**
 * CouponCodeMallRecsService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed Apr 18 11:20:20 CST 2012
 */
class CouponCodeMallRecsService
{
	public function getAll()
	{
		if(! C('DATA_CACHE_ON')){
			$data = $this->_getAll();
			return $data;
		}
		$data = F('mall_recs');
		if(! $data){
			$data = $this->_getAll();
			F('mall_recs', $data);
		}
		return $data;
	}
	
	public function recs_by_position($pos_id)
	{
		$all = $this->getAll();
		return $res = isset($all[$pos_id]) && $all[$pos_id] ? $all[$pos_id] : array();
	}
	
	public function clearCache()
	{
		if(C('DATA_CACHE_ON')) F('mall_recs', null);
	}
	
	private function _getAll()
	{
		$rec_table = M('coupon_mall_rec')->getTableName();
		$m_table = M('CouponCodeMall')->getTableName();
		$res = M('coupon_mall_rec')->field("$rec_table.*,m.name,m.logo")->join($m_table." AS m ON m.id=$rec_table.c_id")->order($rec_table.'.sort_order ASC')->select();
		$data = array();
		foreach ($res as $rs){
			if (!isset($data[$rs['position']])) {
				$data[$rs['position']] = array();
			}
			$data[$rs['position']][] = $rs;
		}
		return $data;
	}
}