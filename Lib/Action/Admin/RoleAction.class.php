<?php
/**
 * RoleAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:14:27 CST 2012
 */
/**
 * 角色管理
 * 
 */
class RoleAction extends AdminCommonAction
{
	public function index()
	{
    	$arModel = D('AdminRole');
    	$roles = $arModel->getAll();
    	$this->assign('roles', $roles);
		$this->assign('ur_href', '角色管理 &gt; 角色列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['role_name']){
				die('data invalid.');
			}
			$arModel = D('AdminRole');
			$id = 0;
			$data = array(
						'role_name'					=>	$_REQUEST['role_name'],
						);
			if($arModel->add_role($id, $data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$this->assign('ur_href', '角色管理 &gt; 添加角色');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function edit()
	{
		$role_id = intval($_REQUEST['role_id']);
		$arModel = D('AdminRole');
		$role = $arModel->info($role_id);
		if(! $role){
			die('id invalid.');
		}
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['role_name']){
				die('data invalid.');
			}
			$data = array(
						'role_name'					=>	$_REQUEST['role_name'],
						);
			if($arModel->edit_role($role_id, $data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$this->assign('role', $role);
		$this->assign('ur_href', '角色管理 &gt; 编辑角色');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function del()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$role_id = intval($_REQUEST['id']);
			$arModel = D('AdminRole');
			$role = $arModel->info($role_id);
			if(! $role){
				$this->ajaxReturn('','id invalid',0);
			}
			if($arModel->del_role($role_id)){
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
}