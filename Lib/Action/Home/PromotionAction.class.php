<?php
/**
 * PromotionAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue Apr 03 14:56:43 CST 2012
 */
class PromotionAction extends HomeCommonAction
{
    public function index()
    {
    	$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 30;
    	$cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
    	$localTimeObj = LocalTime::getInstance();
    	//商家分类
		$cccService = service('CouponCodeCategory');
		$cates = $cccService->getTree();
		//商家子分类
		$cate_ids = array();
		$cate_ids = is_array($cates[$cid]['childs']) ? $cates[$cid]['childs'] : array();
		$cate_ids[] = $cid;
		$cate_ids = implode(',', $cate_ids);
		$params = array('cate_id' => $cate_ids);
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		$codeMallModel = D('MallPromotion');
		$keys = array();
		$res = $codeMallModel->front($keys, $params, $limit);
		$promotions = array();
		foreach ($res['data'] as $rs){
			$rs['expiry_timestamp'] = $rs['expiry'] + $this->_CFG['timezone']*3600;
			$rs['expiry'] = $localTimeObj->local_date($this->_CFG['date_format'], $rs['expiry']);
			$promotions[] = $rs;
		}
		$this->assign('promotions', $promotions);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?cid=$cid&p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('cates', $cates);
		$this->assign('cid', $cid);
    	$this->assign('page_title', '促销活动 - ');
    	$this->assign('page_keywords', $this->_CFG['site_keywords']);
    	$this->assign('page_description', $this->_CFG['site_description']);
    	$this->display();
    }
    
    /**
     * 跳转到商家购物链接
     *
     */
    public function out()
    {
    	$id = intval($_REQUEST['id']);
    	$proModel = D('MallPromotion');
    	$promotion = $proModel->info(array('gourl'), $id);
    	$promotion or die('id invliad.');
    	//可加入统计代码
    	
    	redirect($promotion['gourl']);
    }
}