<?php
/**
 * MallZhekouAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:06:50 CST 2012
 */
/**
 * 超值折扣商品
 *
 */
class MallZhekouAction extends AdminCommonAction
{
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$ccmModel = D('MallZhekou');
    	$cccService = service('ZhekouCategory');
		$categorys = $cccService->getAll();
    	$params = array(
    					'cate_id'	=>	isset($_REQUEST['cate_id']) && $_REQUEST['cate_id'] ? intval($_REQUEST['cate_id']) : 0,
    					'kw'		=>	isset($_REQUEST['kw']) && $_REQUEST['kw'] ? $_REQUEST['kw'] : '',
    					);
    	$keys = array('id,cate_id,title,gourl,sort_order');
    	$res = $ccmModel->getAll($keys, $params, array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$zhekous = array();
    	foreach ($res['data'] as $rs){
    		$rs['category'] = $categorys[$rs['cate_id']]['name'];
    		$zhekous[] = $rs;
    	}
    	$this->assign('zhekous', $zhekous);
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
		$this->assign('ur_href', '折扣商品管理 &gt; 折扣商品列表');
		$this->assign('categorys', $categorys);
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function view()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('MallZhekou');
		$zhekou = $ccmModel->info(array(), $id);
		$zhekou or die('id invalid');
		$cccService = service('ZhekouCategory');
		$categorys = $cccService->getAll();
		$zhekou['category'] = $categorys[$zhekou['cate_id']]['name'];
		$this->assign('zhekou', $zhekou);
		$this->assign('ur_href', '折扣商品管理 &gt; 折扣商品详情');
		$this->display();
	}
	
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST, 'hash')){
				die('hack attemp.');
			}
			if(! $_REQUEST['title'] || ! $_REQUEST['cate_id'] || ! $_REQUEST['gourl']
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
			$data = array(
						'cate_id'		=>	intval($_REQUEST['cate_id']),
						'title'			=>	$_REQUEST['title'],
						'gourl'			=>	$_REQUEST['gourl'],
						'price'			=>	floatval($_REQUEST['price']),
						'description'	=>	$_REQUEST['description'],
						'm_id'			=>	intval($_REQUEST['m_id']),
						'm_name'		=>	$_REQUEST['m_name'],
						'logo'			=>	$logo,
						'sort_order'	=>	intval($_REQUEST['sort_order']),
						'use_coupon'	=>	intval($_REQUEST['use_coupon']),
						'addtime'		=>	$addtime,
						);
			$ccmModel = D('MallZhekou');
			if($ccmModel->_add($data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$categorys = array();
		$service = service('ZhekouCategory');
		$categorys = $service->getAll();
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '折扣商品管理 &gt; 添加折扣商品');
		$this->assign('hash', buildFormToken('hash'));
		$this->display('post');
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$ccmModel = D('MallZhekou');
		$zhekou = $ccmModel->info(array(), $id);
		$zhekou or die('id invalid');
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST, 'hash')){
				die('hack attemp.');
			}
			if(! $_REQUEST['title'] || ! $_REQUEST['cate_id'] || ! $_REQUEST['gourl']
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
			$data = array(
						'cate_id'		=>	intval($_REQUEST['cate_id']),
						'title'			=>	$_REQUEST['title'],
						'gourl'			=>	$_REQUEST['gourl'],
						'price'			=>	floatval($_REQUEST['price']),
						'description'	=>	$_REQUEST['description'],
						'm_id'			=>	intval($_REQUEST['m_id']),
						'm_name'		=>	$_REQUEST['m_name'],
						'sort_order'	=>	intval($_REQUEST['sort_order']),
						'use_coupon'	=>	intval($_REQUEST['use_coupon']),
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
		$this->assign('zhekou', $zhekou);
		$categorys = array();
		$service = service('ZhekouCategory');
		$categorys = $service->getAll();
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '折扣商品管理 &gt; 编辑折扣商品');
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
			$ccmModel = D('MallZhekou');
			$zhekou = $ccmModel->info(array('logo'), $id);
			if($ccmModel->_delete($id)){
				$upload_path = get_upload_path();
				if(is_file(DOC_ROOT_PATH . $upload_path . $zhekou['logo'])){
					@unlink(DOC_ROOT_PATH . $upload_path . $zhekou['logo']);
				}
				$this->ajaxReturn('', buildFormToken(), 1);
			}else{
				$this->ajaxReturn('', '删除失败', 0);
			}
		}
	}
	
	public function category()
	{
		$categorys = M('zhekou_category')->order("sort_order ASC, id DESC")->select();
		$this->assign('categorys', $categorys);
		$this->assign('ur_href', '折扣商品管理 &gt; 折扣分类');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function add_category()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['name'] || ! $_REQUEST['sort_order']){
				exit('data invalid.');
			}
			$data = array('name' => $_REQUEST['name'], 'sort_order' => $_REQUEST['sort_order']);
			if(M('zhekou_category')->add($data)){
				$params = array();
				B('ZhekouCategory', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=category');
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$this->assign('ur_href', '折扣商品管理 &gt; 折扣分类 &gt; 添加分类');
		$this->assign('_hash_', buildFormToken());
		$this->display('category_post');
	}
	
	public function edit_category()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['id'] || ! $_REQUEST['name'] || ! $_REQUEST['sort_order']){
				exit('data invalid.');
			}
			$id = intval($_REQUEST['id']);
			$data = array('name' => $_REQUEST['name'], 'sort_order' => $_REQUEST['sort_order']);
			if(M('zhekou_category')->where("id='$id'")->save($data)){
				$params = array();
				B('ZhekouCategory', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=category');
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$id = intval($_REQUEST['id']);
		$category = M('zhekou_category')->where("id='$id'")->find();
		$this->assign('category', $category);
		$this->assign('ur_href', '折扣商品管理 &gt; 折扣分类 &gt; 编辑分类');
		$this->assign('_hash_', buildFormToken());
		$this->display('category_post');
	}
	
	public function del_category()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$id = intval($_REQUEST['id']);
			if(M('zhekou_category')->where("id='$id'")->delete()){
				$params = array();
				B('ZhekouCategory', $params);
				$this->ajaxReturn('', buildFormToken(), 1);
			}else{
				$this->ajaxReturn('', '删除失败', 0);
			}
		}
	}
}