<?php
/**
 * AdminAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:08:06 CST 2012
 */
/**
 * 管理员管理
 * 
 */
class AdminAction extends AdminCommonAction
{
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$auModel = D('AdminUsers');
    	$arModel = D('AdminRole');
    	$aurModel = D('AdminUserRole');
    	$localTimeObj = LocalTime::getInstance();
    	$params = array();
    	$res = $auModel->getAdmins($params, array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$users = array();
    	foreach ($res['data'] as $rs){
    		$roles = $aurModel->getUserRole($rs['user_id']);
    		$rs['roles'] = array();
    		if(is_array($roles)){
    			foreach ($roles as $r){
    				$rs['roles'][] = $arModel->info($r);
    			}
    		}
    		$rs['last_login'] = $rs['last_login']
    							? $localTimeObj->local_date($this->_CFG['time_format'], $rs['last_login'])
    							: '从未登陆';
    		$users[] = $rs;
    	}
    	$this->assign('users', $users);
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
		$this->assign('ur_href', '管理员管理 &gt; 管理员列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! self::_check_user_name_valid($_REQUEST['user_name'], 0)){
				$this->error('用户名已存在');
			}
			if(! $_REQUEST['user_name'] || ! $_REQUEST['password'] || ! is_array($_REQUEST['role_id'])){
				die('data invalid.');
			}
			$auModel = D('AdminUsers');
			$user_id = 0;
			$data = array(
						'user_name'					=>	$_REQUEST['user_name'],
						'password'					=>	md5(md5($_REQUEST['password'])),
						);
			if($auModel->add_user($user_id, $data)){
				//角色
				$aurModel = D('AdminUserRole');
				$aurModel->edit_ur($user_id, $_REQUEST['role_id']);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$arModel = D('AdminRole');
    	$roles = $arModel->getAll();
    	$this->assign('roles', $roles);
		$this->assign('ur_href', '管理员管理 &gt; 添加管理员');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	public function edit()
	{
		$user_id = intval($_REQUEST['user_id']);
		$auModel = D('AdminUsers');
		$user_info = $auModel->info($user_id);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! self::_check_user_name_valid($_REQUEST['user_name'], $user_id)){
				$this->error('用户名已存在');
			}
			if(! $_REQUEST['user_name'] || ! is_array($_REQUEST['role_id'])){
				die('data invalid.');
			}
			$data = array(
						'user_name'					=>	$_REQUEST['user_name'],
						);
			if($_REQUEST['password']){
				$data['password'] = md5(md5($_REQUEST['password']));
			}
			if($auModel->edit_user($user_id, $data)){
				//角色
				$aurModel = D('AdminUserRole');
				$aurModel->edit_ur($user_id, $_REQUEST['role_id']);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$aurModel = D('AdminUserRole');
		$user_info['roles'] = $aurModel->getUserRole($user_id);
		$this->assign('user', $user_info);
		$arModel = D('AdminRole');
    	$roles = $arModel->getAll();
    	$this->assign('roles', $roles);
		$this->assign('ur_href', '管理员管理 &gt; 添加管理员');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 删除管理员账户
	 *
	 */
	public function del()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$user_id 	= intval($_REQUEST['id']);
			$auModel = D('AdminUsers');
			if($auModel->_delete($user_id)){
				//删除角色信息
				$ausModel = D('AdminUserRole');
				$ausModel->_del($user_id);
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
	
	/**
	 * 验证用户名是否重复
	 *
	 */
	public function check_user_name_valid()
	{
		if($this->isAjax()){
			$user_id = intval($_REQUEST['user_id']);
			$user_name = $_REQUEST['user_name'];
			if(self::_check_user_name_valid($user_id, $user_name)){
				$this->ajaxReturn(array('is_exist'=>0), '', 1);
			}else{
				$this->ajaxReturn(array('is_exist'=>1), '', 1);
			}
		}
	}
	
	private static function _check_user_name_valid($user_id, $user_name)
	{
		$auModel = D('AdminUsers');
		$aInfo = $auModel->infoByUserName($user_name);
		if($aInfo && $aInfo['user_id'] != $user_id){
			return false;
		}else{
			return true;
		}
	}
}