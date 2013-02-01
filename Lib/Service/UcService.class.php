<?php
/**
 * UcService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 15 01:39:22 CST 2012
 */
class UcService
{
	public function __construct()
	{
		include_once(DOC_ROOT_PATH . 'Conf/config_ucenter.php');
		include_once(DOC_ROOT_PATH . 'uc_client/client.php');
	}

	/**
	 * 验证帐号是否有效
	 *
	 * @param string $nick
	 * @return bool
	 */
	public function check_nick_valid($username)
	{
		if($data = uc_get_user($username)) {
			list($uid, $username, $email) = $data;
			return false;
		}
		return true;
	}

	/**
	 * 用户注册
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @return int|string
	 */
	public function register($username, $password, $email)
	{
		$uid = uc_user_register($username, $password, $email);
		if($uid <= 0) {
			if($uid == -1) {
				return '用户名不合法';
			} elseif($uid == -2) {
				return '包含不允许注册的词语';
			} elseif($uid == -3) {
				return '用户名已经存在';
			} elseif($uid == -4) {
				return 'Email 格式有误';
			} elseif($uid == -5) {
				return 'Email 不允许注册';
			} elseif($uid == -6) {
				return '该 Email 已经被注册';
			} else {
				return '未定义';
			}
		} else {
			return intval($uid);
		}
	}

	/**
	 * 会员登陆
	 *
	 * @param string $username
	 * @param string $password
	 * @return array|string
	 */
	public function login($username, $password)
	{
		list($uid, $username, $password, $email) = uc_user_login($username, $password);
		if($uid > 0) {
			return array(
						'uid'			=>	$uid,
						'username'		=>	$username,
						'password'		=>	$password,
						'email'			=>	$email
						);
		} elseif($uid == -1) {
			return  '用户不存在,或者被删除';
		} elseif($uid == -2) {
			return '密码错误';
		} elseif($uid == -3) {
			return '安全提问错误';
		} else {
			return '未定义';
		}
	}
	
	/**
	 * 获取用户资料
	 *
	 * @param unknown_type $username
	 * @param unknown_type $isuid
	 * @return unknown
	 */
	public function get_user_info($username, $isuid=0)
	{
		if($data = uc_get_user($username, $isuid)) {
			return $data;
		} else {
			return null;
		}
	}
	
	public function uc_user_edit($username , $oldpw , $newpw , $email='' , $ignoreoldpw=0, $questionid=0 , $answer='')
	{
		$result = uc_user_edit($username , $oldpw , $newpw , $email , $ignoreoldpw, $questionid , $answer);
		//更新成功
		if($result == 1){
			return true;
		}
		else if($result == 0){
			return '没有做任何修改';
		}
		else if($result == -1){
			return '旧密码不正确';
		}
		else if($result == -4){
			return 'Email 格式有误';
		}
		else if($result == -5){
			return 'Email 不允许注册';
		}
		else if($result == -6){
			return '该 Email 已经被注册';
		}
		else if($result == -7){
			return '没有做任何修改';
		}
		else if($result == -8){
			return '该用户受保护无权限更改';
		}
	}
	
	public function get_avatar($uid, $size='middle')
	{
		return UC_API . '/uc_server/avatar.php?uid='.$uid.'&type=real&size='.$size;
	}
	
	/**
	 * 生成同步登陆代码
	 *
	 * @param int $uid
	 * @return string
	 */
	public function build_synlogin($uid)
	{
		return uc_user_synlogin($uid);
	}
	
	/**
	 * 生成同步退出代码
	 *
	 * @param int $uid
	 * @return string
	 */
	public function build_synlogout()
	{
		return uc_user_synlogout();
	}
	
	/**
	 * UC应用列表
	 *
	 * @return unknown
	 */
	public function uc_app_ls()
	{
		return uc_app_ls();
	}
	
	public function uc_user_getcredit($appid, $uid, $credit)
	{
		return uc_user_getcredit($appid, $uid, $credit);
	}
}