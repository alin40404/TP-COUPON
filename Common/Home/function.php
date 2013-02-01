<?php
/**
 * function.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Fri May 25 14:45:18 CST 2012
 */
/*==========================================商家函数库=========================================================*/
/**
 * 获取所有商家分类树
 *
 * @return array
 */
function get_mall_category_tree()
{
	static $cates = array();
	if(! empty($cates)) return $cates;
	//商家分类
	$cccService = service('CouponCodeCategory');
	return $cates = $cccService->getTree();
}

/**
 * 获取所有商家分类
 *
 * @return array
 */
function get_mall_category()
{
	static $cates = array();
	if(! empty($cates)) return $cates;
	//商家分类
	$cccService = service('CouponCodeCategory');
	return $cates = $cccService->getAll();
}

/**
 * 推荐商家
 *
 * @param int $position_id				推荐位ID
 */
function rec_malls($position_id)
{
	static $mall_recs = array();
	if(isset($mall_recs[$position_id])) return $mall_recs[$position_id];
	$ccmrService = service('CouponCodeMallRecs');
    return $mall_recs[$position_id] = $ccmrService->recs_by_position($position_id);
}

/**
 * 热门搜索商家
 *
 * @param string $type:yesterday(昨日)、day（今日）、week（本周）、month（本月）
 * @param int $limit
 * @return array
 */
function hot_malls($type='week', $limit=10)
{
	$malls = array();
	$mallService = service('CouponCodeMall');
	return $malls = $mallService->hottest($type, $limit);
}
/*==========================================优惠券函数库=========================================================*/
/**
 * 最新优惠券
 *
 * @param int $m_id				商家ID
 * @param int $limit			数量
 */
function coupon_codes_latest($m_id=null, $limit=10)
{
	static $return = array();
	if(isset($return[$m_id.$limit])) return $return[$m_id.$limit];
	$ccService = service('CouponCode');
	return $return[$m_id.$limit] = $ccService->mall_latest($m_id, $limit);
}

/**
 * 领取最多的优惠券
 *
 * @param string $type:yesterday(昨日最多)、day（今日最多）、week（本周最多）、month（本月最多）
 * @param int $limit			数量
 * @return array
 */
function coupon_codes_hot($type, $limit=10)
{
	static $return = array();
	if(isset($return[$type.$limit])) return $return[$type.$limit];
	$ccService = service('CouponCode');
	return $return[$type.$limit] = $ccService->hottest($type, $limit);
}

/**
 * 最近被领取的优惠券
 *
 * @param int $limit
 * @return array
 */
function coupon_codes_fetched($limit=10)
{
	static $return = array();
	if(isset($return[$limit])) return $return[$limit];
	$return[$limit] = array();
	$localTimeObj = LocalTime::getInstance();
	$timezone = $localTimeObj->server_timezone();
	$ccService = service('CouponCode');
	$data = $ccService->fetch_latest($limit);
	foreach ($data as $d){
		$d['fetch_time'] = $d['fetch_time']+$timezone*3600;
		$return[$limit][] = $d;
	}
	return $return[$limit];
}

/**
 * 每日精选优惠券
 *
 * @param int $limit
 * @return array
 */
function coupon_codes_daybest($limit)
{
	static $return = array();
	if(isset($return[$limit])) return $return[$limit];
	$ccService = service('CouponCode');
	$localTimeObj = LocalTime::getInstance();
	$time = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00'));
	return $return[$limit] = $ccService->daybest($time, $limit);
}

/**
 * 获取分类优惠券
 *
 * @param int $cate_id			分类ID
 * @param int $limit
 * @return array
 */
function coupon_codes_cate($cate_id, $limit)
{
	static $ccModel=null,$cates=null,$cates=null,$cfg=null,$localTimeObj=null;
	if($ccModel === null) $ccModel = D('CouponCode');
	if($cates === null) $cates = get_mall_category_tree();
	if($all_cates === null) $all_cates = get_mall_category();
	if($cfg === null) $cfg = load_config();
	if($localTimeObj === null) $localTimeObj = LocalTime::getInstance();
	$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
	$coupons = array();
	$c = $all_cates[$cate_id];
	$cate_ids = is_array($cates[$c['id']]['childs']) ? $cates[$c['id']]['childs'] : array();
	$cate_ids[] = $c['id'];
	$cate_ids = implode(',', $cate_ids);
	$res = $ccModel->coupons4cate($cate_ids, $limit);
	foreach ($res as $rs){
		if($rs['expiry_type'] == 1){
			$rs['expiry_timestamp'] = $rs['expiry'] + $cfg['timezone']*3600;
			if(($rs['expiry'] - $today) == 0){
				$rs['expiry'] = 1;
			}else{
				$rs['expiry'] = ($rs['expiry'] - $today) > 0 ? ceil(($rs['expiry'] - $today)/(3600*24)) : 0;
			}
		}
		$coupons[] = $rs;
	}
	return $coupons;
}