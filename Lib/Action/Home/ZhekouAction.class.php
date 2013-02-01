<?php
class ZhekouAction extends HomeCommonAction 
{
	public function index()
    {
    	$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 15;
		$addtime = 0;
    	$cid = isset($_REQUEST['cid']) ? intval($_REQUEST['cid']) : 0;
    	$t_type = isset($_REQUEST['t_type']) ? intval($_REQUEST['t_type']) : 0;
    	$localTimeObj = LocalTime::getInstance();
    	//商家分类
		$cccService = service('ZhekouCategory');
		$categorys = $cccService->getAll();
		$params = array('cate_id' => $cid);
		switch ($t_type){
			case 1:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00'));
				break;
			case 2:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00', strtotime('-3 day')));
				break;
			case 3:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00', strtotime('-7 day')));
				break;
			case 4:
				$addtime = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00', strtotime('-30 day')));
				break;
		}
		$params['addtime'] = $addtime;
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		$codeMallModel = D('MallZhekou');
		$keys = array();
		$res = $codeMallModel->front($keys, $params, $limit);
		$zhekous = $res['data'];
		$this->assign('zhekous', $zhekous);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?cid=$cid&t_type=$t_type&p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('categorys', $categorys);
		$this->assign('cid', $cid);
		$this->assign('t_type', $t_type);
    	$this->assign('page_title', '超值折扣 - ');
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
    	$proModel = D('MallZhekou');
    	$zhekou = $proModel->info(array('gourl'), $id);
    	$zhekou or die('id invliad.');
    	//可加入统计代码
    	
    	redirect($zhekou['gourl']);
    }
}