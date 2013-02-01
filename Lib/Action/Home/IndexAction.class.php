<?php
/**
 * IndexAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue Apr 03 14:56:43 CST 2012
 */
class IndexAction extends HomeCommonAction
{
    /**
     * 默认操作
     * 
     */
    public function index()
    {
    	$page = 1;
		$pageLimit = 20;
		$localTimeObj = LocalTime::getInstance();
		$today = $localTimeObj->local_strtotime(date('Y-m-d 23:59:59'));
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		$codeModel = D('CouponCode');
		$res = $codeModel->front(array(), $limit);
		$codes = array();
		foreach ($res['data'] as $rs){
			if($rs['expiry_type'] == 1){
				$rs['expiry_timestamp'] = $rs['expiry'] + $this->_CFG['timezone']*3600;
				if(($rs['expiry'] - $today) == 0){
					$rs['expiry'] = 1;
				}else{
					$rs['expiry'] = ($rs['expiry'] - $today) > 0 ? ceil(($rs['expiry'] - $today)/(3600*24)) : 0;
				}
			}
			$codes[] = $rs;
		}
		$this->assign('codes', $codes);
		$page_url = reUrl("Code/latest?cate_id=0&t_type=0&cate_id2=0&p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		
		//友情链接
		$friendlinks = array();
		$flService = service('FriendLinks');
		$res = $flService->getAll();
		if(is_array($res[101])){
			foreach ($res[101] as $r){
				$friendlinks[] = $res['all'][$r];
			}
		}
		$this->assign('friendlinks', $friendlinks);
    	$this->assign('page_title', '');
    	$this->assign('page_keywords', $this->_CFG['site_keywords']);
    	$this->assign('page_description', $this->_CFG['site_description']);
     	$this->display();

    }
    
    public function test(){
    	dump("test");
    }
}