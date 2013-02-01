<?php
/**
 * PrivAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Wed May 02 17:37:58 CST 2012
 */
class PrivAction extends AdminCommonAction
{
	public function set()
	{
		$module = $_REQUEST['module'];
		$privRoleObj = AdminPriv::getInstance();
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if($privRoleObj->_update('module', $module, $_REQUEST['priv_roleid'])){
				$this->assign('jumpUrl', $this->_refererUrl);
				$this->success('修改成功');
			}else{
				$this->assign('jumpUrl', $this->_refererUrl);
				$this->success('修改失败');
			}
		}
		$privs_conf = C('_privs_.Admin');
		$module_privs = $privs_conf[$module];
		$this->assign('module_privs',$module_privs);
    	$this->assign('privs',$privRoleObj->getPrivs($module));
    	$this->assign('module', $module);
    	$sysRoles = null;
    	$roleModule = D('AdminRole');
    	$sysRoles = $roleModule->getAll();
    	$this->assign('sysRoles',$sysRoles);
    	
		$this->assign('ur_href', '权限管理');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
}