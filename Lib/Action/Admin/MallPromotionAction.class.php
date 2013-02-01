<?php
/**
 * MallPromotionAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:06:50 CST 2012
 */
/**
 * 商城促销
 *
 */
class MallPromotionAction extends AdminCommonAction
{
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$ccmModel = D('MallPromotion');
    	$cccService = service('CouponCodeCategory');
    	$params = array(
    					'cate_id'	=>	isset($_REQUEST['cate_id']) && $_REQUEST['cate_id'] ? intval($_REQUEST['cate_id']) : 0,
    					'kw'		=>	isset($_REQUEST['kw']) && $_REQUEST['kw'] ? $_REQUEST['kw'] : '',
    					);
    	$keys = array('id,cate_id,title,gourl,sort_order');
    	$res = $ccmModel->getAll($keys, $params, array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$promotions = array();
    	foreach ($res['data'] as $rs){
    		$category = $cccService->info($rs['cate_id']);
    		$rs['cates'] = $category['parents'];
    		$promotions[] = $rs;
    	}
    	$this->assign('promotions', $promotions);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]";
    	foreach ($params as $key => $val){
    		$page_url .= "&$key=$val";
    	}
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('ur_href', '促销活动管理 &gt; 活动列表');
		$categorys = array();
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function view()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('MallPromotion');
		$promotion = $ccmModel->info(array(), $id);
		$promotion or die('id invalid');
		$cccService = service('CouponCodeCategory');
		$cate = $cccService->info($promotion['cate_id']);
		$promotion['cates'] = $cate['parents'];
		$promotion['expiry'] = LocalTime::getInstance()->local_date($this->_CFG['date_format'], $promotion['expiry']);
		$this->assign('promotion', $promotion);
		$this->assign('ur_href', '促销活动管理 &gt; 活动详情');
		$this->display();
	}
	
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST, 'hash')){
				die('hack attemp.');
			}
			if(! $_REQUEST['title'] || ! $_REQUEST['cate_id'] || ! $_REQUEST['gourl'] || ! $_REQUEST['expiry']
				 || ! $_REQUEST['m_id'] || ! $_REQUEST['description'] || ! $_REQUEST['sort_order']){
				$this->error('请填写所有的必填项');
			}
			if($_FILES['logo']['size'] <= 0 && $_FILES['logo']['error'] > 0){
				$this->error('请上传LOGO');
			}
			$logo='';
			if($_FILES['logo']['size'] > 0 && $_FILES['logo']['error'] == 0){
				$upfile = array();
				$upfile = upload_one_file($_FILES['logo']);
				if($upfile['error']){
					$this->error($upfile['error']);
				}
				$logo = $upfile['file_name'];
			}
			$localTimeObj = LocalTime::getInstance();
			$addtime = $localTimeObj->gmtime();
			$_REQUEST['expiry'] = $localTimeObj->local_strtotime($_REQUEST['expiry'] . ' 23:59:59');
			$data = array(
						'cate_id'		=>	intval($_REQUEST['cate_id']),
						'title'			=>	$_REQUEST['title'],
						'gourl'			=>	$_REQUEST['gourl'],
						'expiry'		=>	$_REQUEST['expiry'],
						'description'	=>	$_REQUEST['description'],
						'm_id'			=>	intval($_REQUEST['m_id']),
						'm_name'		=>	$_REQUEST['m_name'],
						'logo'			=>	$logo,
						'sort_order'	=>	intval($_REQUEST['sort_order']),
						'addtime'		=>	$addtime,
						);
			$ccmModel = D('MallPromotion');
			if($ccmModel->_add($data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$categorys = array();
		$cccService = service('CouponCodeCategory');
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '促销活动管理 &gt; 添加活动');
		$this->assign('hash', buildFormToken('hash'));
		$this->display('post');
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('MallPromotion');
		$promotion = $ccmModel->info(array(), $id);
		$promotion or die('id invalid');
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST, 'hash')){
				die('hack attemp.');
			}
			if(! $_REQUEST['title'] || ! $_REQUEST['cate_id'] || ! $_REQUEST['gourl'] || ! $_REQUEST['expiry']
				 || ! $_REQUEST['m_id'] || ! $_REQUEST['description'] || ! $_REQUEST['sort_order']){
				$this->error('请填写所有的必填项');
			}
			$logo='';
			if($_FILES['logo']['size'] > 0 && $_FILES['logo']['error'] == 0){
				$upfile = array();
				$upfile = upload_one_file($_FILES['logo']);
				if($upfile['error']){
					$this->error($upfile['error']);
				}
				$logo = $upfile['file_name'];
			}
			$localTimeObj = LocalTime::getInstance();
			$addtime = $localTimeObj->gmtime();
			$_REQUEST['expiry'] = $localTimeObj->local_strtotime($_REQUEST['expiry'] . ' 23:59:59');
			$data = array(
						'cate_id'		=>	intval($_REQUEST['cate_id']),
						'title'			=>	$_REQUEST['title'],
						'gourl'			=>	$_REQUEST['gourl'],
						'expiry'		=>	$_REQUEST['expiry'],
						'description'	=>	$_REQUEST['description'],
						'm_id'			=>	intval($_REQUEST['m_id']),
						'm_name'		=>	$_REQUEST['m_name'],
						'sort_order'	=>	intval($_REQUEST['sort_order']),
						);
			if($logo){
				$data['logo'] = $logo;
			}
			if($ccmModel->update($id, $data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$promotion['expiry'] = LocalTime::getInstance()->local_date($this->_CFG['date_format'], $promotion['expiry']);
		$this->assign('promotion', $promotion);
		
		$categorys = array();
		$cccService = service('CouponCodeCategory');
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '促销活动管理 &gt; 编辑活动');
		$this->assign('hash', buildFormToken('hash'));
		$this->display('post');
	}
	
	public function del()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$id = intval($_REQUEST['id']);
			$ccmModel = D('MallPromotion');
			if($ccmModel->_delete($id)){
				$this->ajaxReturn('', buildFormToken(), 1);
			}else{
				$this->ajaxReturn('', '删除失败', 0);
			}
		}
	}
}