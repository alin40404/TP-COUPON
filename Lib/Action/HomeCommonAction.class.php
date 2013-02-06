<?php
/**
 * HomeCommonAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Mon Apr 16 14:49:49 CST 2012
 */
class HomeCommonAction extends CommonAction
{
	protected $_user = null;
	protected function _initialize()
	{
		parent::_initialize();
		//禁止通过URL访问的操作
    	if(in_array(MODULE_NAME.'.'.strtolower(ACTION_NAME), C('APP_ACTION_DENY_LIST'))){
    		die('Hacking attempt.');
    	}
    	if(intval($this->_CFG['url_rewrite']) == 1){
    		C('URL_MODEL', 2);
    	}else{
    		C('URL_MODEL', 0);
    	}
    	
    	//初始化用户信息
    	$this->_init_user();
    	//验证登陆
    	$this->_check_login();
    	//初始化开放平台信息
    	$this->_init_open_platform();
	}
	
	
	private function _init_user()
	{
		$this->_user = array('user_id' => 0,'nick' => '','avatar'=>'','credit'=>0,'money'=>0);
		$auth = cookie('auth');
		if($auth){
			$auth = authcode($auth, 'DECODE', C('AUTH'));
			$arr = explode("\t", $auth);
			if(count($arr) == 2){
				$avatar = cookie('avatar');
				if(empty($avatar)){
					$ucService = service('Uc');
					$avatar = $ucService->get_avatar($arr[0]);
				}
				$userModel = D('User');
				$user = $userModel->info($arr[0], array('money', 'credit'));
				$this->_user = array(
							  'user_id' => $arr[0],
							  'nick' 	=> $arr[1],
							  'avatar'	=> $avatar,
							  'credit'	=> $user['credit'],
							  'money'	=> $user['money']
							  );
			}
		}
		
    	if(! $this->isAjax() && ! $this->isPost()){
    		$this->assign('user', $this->_user);
    	}
	}
	
	protected function _check_login()
	{
		if(in_array(MODULE_NAME, C('LOGIN_MODULES'))
		 && ! in_array(MODULE_NAME . '.' . strtolower(ACTION_NAME), C('NOT_LOGIN_ACTIONS'))
		 && ! $this->_user['user_id']){
			if($this->isAjax()){
				$this->ajaxReturn('', '未登录', 0);
			}else{
				redirect(reUrl('User/login'));
			}
		}
	}
	
	private function _init_open_platform()
	{
		if($this->_CFG['qq_open']){
			if(! defined('QQ_APPID')) define('QQ_APPID', $this->_CFG['qq_appid']);
			if(! defined('QQ_APPKEY')) define('QQ_APPKEY', $this->_CFG['qq_appkey']);
			if(! defined('QQ_CALLBACK')){
				define('QQ_CALLBACK', 'http://'.$_SERVER['HTTP_HOST'].__ROOT__ . '/?m=User&a=qq_callback');
			}
		}
		
		if($this->_CFG['sina_wb_open']){
			if(! defined('WB_AKEY')) define('WB_AKEY', $this->_CFG['sina_wb_akey']);
			if(! defined('WB_SKEY')) define('WB_SKEY', $this->_CFG['sina_wb_skey']);
			if(! defined('WB_OFFICE_UID')) define('WB_OFFICE_UID', $this->_CFG['sina_wb_office_id']);
			if(! defined('WB_CALLBACK_URL')){
				define('WB_CALLBACK_URL', 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/?m=User&a=sina_callback' );
			}
		}
	}
}