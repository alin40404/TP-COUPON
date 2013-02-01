<?php
/**
 * CouponCodeService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu Apr 19 14:01:52 CST 2012
 */
class CouponCodeService
{
	private $_expire = null;
	
	public function __construct()
	{
		$_cfg = load_config();
		$this->_expire = 3600*floatval($_cfg['data_cache_time']);
	}
	
	/**
	 * 商家最新发布的10个优惠券
	 * 系统开启动态数据缓存时可缓存2小时
	 *
	 * @param int $m_id
	 * @return array
	 */
	public function mall_latest($m_id=null, $limit=10)
	{
		if(! C('DATA_CACHE_ON')){
			$ccModel = D('CouponCode');
			return $data = $ccModel->latest($m_id, $limit);
		}
		$data = S('mall_latest_'.$limit.$m_id, '', $this->_expire);
		if(! $data){
			$ccModel = D('CouponCode');
			$data = $ccModel->latest($m_id, $limit);
			S('mall_latest_'.$limit.$m_id, $data, $this->_expire);
		}
		return $data;
	}
	
	/**
	 * 最新被领取的10个优惠券
	 * 系统开启动态数据缓存时可缓存30分钟
	 *
	 * @param int $m_id
	 * @return array
	 */
	public function fetch_latest($limit=10)
	{
		if(! C('DATA_CACHE_ON')){
			$cccModel = D('CouponCodeCodes');
			$data = $cccModel->fetch_latest($limit);
			return $data ? $data : array();
		}
		$data = S('fetch_latest'.$limit, '', $this->_expire);
		if(! $data){
			$cccModel = D('CouponCodeCodes');
			$data = $cccModel->fetch_latest($limit);
			S('fetch_latest'.$limit, $data, 1800);
		}
		return $data ? $data : array();
	}
	
	/**
	 * 领取最多的优惠券
	 * 系统开启动态数据缓存时可缓存2小时
	 *
	 * @param string $type:yesterday(昨日最多)、day（今日最多）、week（本周最多）、month（本月最多）
     * @param int $limit
	 * @return array
	 */
	public function hottest($type, $limit=10)
	{
		if(! C('DATA_CACHE_ON')){
			$ccdModel = D('CouponCodeData');
			return $data = $ccdModel->hottest($type, $limit);
		}
		$data = S('code_hottest_'.$type.$limit, '', $this->_expire);
		if(! $data){
			$ccdModel = D('CouponCodeData');
			$data = $ccdModel->hottest($type, $limit);
			S('code_hottest_'.$type.$limit, $data, $this->_expire);
		}
		return $data;
	}
	
	/**
	 * 每日精选优惠券
	 *
	 */
	public function daybest($time, $limit)
	{
		if(! C('DATA_CACHE_ON')){
			$ccbModel = D('CouponCodeBest');
			return $data = $ccbModel->top($time, $limit);
		}
		$data = S('code_daybest_'.$time.$limit, '', $this->_expire);
		if(! $data){
			$ccbModel = D('CouponCodeBest');
			$data = $ccbModel->top($time, $limit);
			S('code_daybest_'.$time.$limit, $data, $this->_expire);
		}
		return $data;
	}
}