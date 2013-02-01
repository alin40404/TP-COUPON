<?php
/**
 * CouponCodeMallService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:47:10 CST 2012
 */
class CouponCodeMallService
{
	private $_expire = null;
	
	public function __construct()
	{
		$_cfg = load_config();
		$this->_expire = 3600*floatval($_cfg['data_cache_time']);
	}
	
	public function info($id)
	{
		if(! C('DATA_CACHE_ON')){
			$ccmModel = D('CouponCodeMall');
			return $data = $ccmModel->info($id);
		}
		$data = S('mall_'.$id);
		if(! $data){
			$ccmModel = D('CouponCodeMall');
			$data = $ccmModel->info($id);
			S('mall_'.$id, $data);
		}
		return $data;
	}
	
	/**
	 * 搜索最多的商家
	 * 系统开启动态数据缓存时可缓存2小时
	 *
	 * @param @param string $type:yesterday(昨日最多)、day（今日最多）、week（本周最多）、month（本月最多）
     * @param int $limit
	 * @return array
	 */
	public function hottest($type, $limit=10)
	{
		if(! C('DATA_CACHE_ON')){
			$ccmModel = D('CouponCodeMall');
			return $data = $ccmModel->hottest($type, $limit);
		}
		$data = S('mall_hottest_'.$type, '', $this->_expire);
		if(! $data){
			$ccmModel = D('CouponCodeMall');
			$data = $ccmModel->hottest($type, $limit);
			S('mall_hottest_'.$type, $data, $this->_expire);
		}
		return $data;
	}
	
	public function clearCache($id)
	{
		if(C('DATA_CACHE_ON')) S('mall_'.$id, null);
	}
}