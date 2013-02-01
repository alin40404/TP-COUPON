<?php
/**
 * CouponCodeCategoryAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:06:50 CST 2012
 */
/**
 * 分类
 *
 */
class CouponCodeCategoryAction extends AdminCommonAction
{
	public function index()
	{
		$category = array();
		$cccService = service('CouponCodeCategory');
		$all_cates = $cccService->getAll();
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$category[$rs['id']] = $rs;
			$category[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
			$category[$rs['id']]['show_index'] = $all_cates[$rs['id']]['show_index'];
		}
		$this->assign('category', $category);
		$this->assign('ur_href', '分类管理 &gt; 分类列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['name'] || ! $_REQUEST['sort_order']){
				$this->error('请填写所有的必填项');
			}
			$data = array(
						'parent_id'		=>	intval($_REQUEST['parent_id']),
						'name'			=>	$_REQUEST['name'],
						'sort_order'	=>	intval($_REQUEST['sort_order']),
						);
			$cccModel = D('CouponCodeCategory');
			if($cccModel->_add($data)){
				//清除缓存
				B('CouponCodeCategory');
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
		$this->assign('ur_href', '分类管理 &gt; 添加分类');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function edit()
	{
		$id = intval($_REQUEST['id']);
		$cccService = service('CouponCodeCategory');
		$category = $cccService->info($id);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['name'] || ! $_REQUEST['sort_order']){
				$this->error('请填写所有的必填项');
			}
			$data = array(
						'parent_id'		=>	intval($_REQUEST['parent_id']),
						'name'			=>	$_REQUEST['name'],
						'sort_order'	=>	intval($_REQUEST['sort_order']),
						);
			$cccModel = D('CouponCodeCategory');
			if($cccModel->_edit($id, $data)){
				//清除缓存
				B('CouponCodeCategory');
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$categorys = array();
		$data = $cccService->getTree();
		foreach ($data as $rs){
			$categorys[$rs['id']] = $rs;
			$categorys[$rs['id']]['prefix'] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$rs['level']);
		}
		$this->assign('categorys', $categorys);
		$this->assign('category', $category);
		$this->assign('ur_href', '分类管理 &gt; 编辑分类');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function del()
	{
		if($this->isAjax()){
			$id = intval($_REQUEST['id']);
			$cccModel = D('CouponCodeCategory');
			if($cccModel->_delete($id)){
				//清除缓存
				B('CouponCodeCategory');
				$this->ajaxReturn('', '', 1);
			}else{
				$this->ajaxReturn('', '删除失败', 0);
			}
		}
	}
}