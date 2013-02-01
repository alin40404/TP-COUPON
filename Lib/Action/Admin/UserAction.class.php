<?php
/**
 * UserAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:18:59 CST 2012
 */
class UserAction extends AdminCommonAction
{
	/**
	 * 管理员登陆
	 *
	 */
	public function login()
	{
		if($this->isAjax()){
			$this->_doLogin();
		}
		
		//验证码
		$enabled_captcha = false;
		$captcha=intval($this->_CFG['captcha']);
		//dump($captcha);
		if (($captcha & CAPTCHA_ADMIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL)
		&& $_SESSION['login_fail'] > 2)) > 0){
			$enabled_captcha=true;
		}
		$this->assign('captcha',$enabled_captcha);
		$this->assign('mt',mt_rand());
 		$this->display();
		//echo 'login';
	}
	
	public function logout()
	{
		unset($_SESSION[C('SESSION_PREFIX') . 'user_id']);
		unset($_SESSION[C('SESSION_PREFIX') . 'user_name']);
		unset($_SESSION[C('SESSION_PREFIX') . 'is_super']);
		unset($_SESSION[C('SESSION_PREFIX') . 'name']);
		unset($_SESSION[C('SESSION_PREFIX') . 'user_roles']);
		redirect('?g='.GROUP_NAME.'&m=User&a=login');
	}
	
	public function edit_pwd()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$auModel = D('AdminUsers');
			$user_id = $_SESSION[C('SESSION_PREFIX').'user_id'];
			$auInfo = $auModel->info($user_id);
			if($auInfo['password'] != md5(md5($_REQUEST['old_password']))){
				$this->error('旧密码输入错误');
			}
			$data = array(
						'password' =>	md5(md5($_REQUEST['password'])),
						);
			if($auModel->edit_user($user_id, $data)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m=User&a=logout');
				$this->success('修改成功.请重新登陆');
			}else{
				$this->error('修改失败，请重试');
			}
		}
		$this->assign('ur_href', '修改密码');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	private function _doLogin()
	{
		$enabled_captcha = false;
		$captcha=intval($this->_CFG['captcha']);
		if (($captcha & CAPTCHA_ADMIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL)
		&& $_SESSION['login_fail'] > 2)) > 0){
			$enabled_captcha=true;
		}
		if ($enabled_captcha &&
		isset($_SESSION[CAPTCHA_CODE]) && !empty($_SESSION[CAPTCHA_CODE]))
		{
			/* 检查验证码是否正确 */
			if (empty($_REQUEST['verify']) || ! SeccodeUtil::check_word(CAPTCHA_CODE,$_REQUEST['verify']))
			{
				$this->ajaxReturn('','验证码输入错误',0);
			}
		}
		$userObj = D('AdminUsers');
		$user_name		=	$_REQUEST['admin_name'];
		$password		=	$_REQUEST['admin_pwd'];
		$password		=	md5(md5($password));
		$userInfo = $userObj->infoByUserName($user_name);
		if(! isset($_SESSION['login_fail'])){
			$_SESSION['login_fail'] = 0;
		}
		//用户名不存在
		if(!$userInfo || ! $userInfo['user_id']){
			$_SESSION['login_fail'] += 1;
    		$this->ajaxReturn('','用户名不存在',0);
		}
		//密码不正确
		if($userInfo['password'] != $password){
			$_SESSION['login_fail']+=1;
    		$this->ajaxReturn('','密码不正确',0);
		}
		//已被锁定
		if($userInfo['is_locked']){
    		$this->ajaxReturn('','帐号已被锁定',0);
		}
		$lastLogin = LocalTime::getInstance()->gmtime();
		$userObj->edit_user($userInfo['user_id'],array('last_login'=>$lastLogin,'last_ip'=>get_client_ip()));
		$_SESSION[C('SESSION_PREFIX') . 'user_id'] 		= $userInfo['user_id'];
		$_SESSION[C('SESSION_PREFIX') . 'user_name'] 	= $userInfo['user_name'];
		//是否超级管理员
		$_SESSION[C('SESSION_PREFIX') . 'is_super'] 	= $userInfo['is_super'];
		$_SESSION[C('SESSION_PREFIX') . 'name'] 		= $userInfo['name'];
		//用户的角色
		$aurModel = D('AdminUserRole');
		$_SESSION[C('SESSION_PREFIX') . 'user_roles'] = $aurModel->getUserRole($userInfo['user_id']);
		unset($_SESSION['login_fail']);
		$this->ajaxReturn('','',1);
	}
}