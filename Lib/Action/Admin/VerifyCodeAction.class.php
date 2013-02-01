<?php
/**
 * VerifyCodeAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:19:06 CST 2012
 */
/**
 * 验证码
 *
 */
class VerifyCodeAction extends AdminCommonAction
{
	/**
	 * 设置
	 *
	 */
	public function setting()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_POST)){
				die('hack attemp.');
			}
			$captcha = 0;
			$captcha = empty($_POST['captcha_register'])    ? $captcha : $captcha | CAPTCHA_REGISTER;
			$captcha = empty($_POST['captcha_login'])       ? $captcha : $captcha | CAPTCHA_LOGIN;
			$captcha = empty($_POST['captcha_comment'])     ? $captcha : $captcha | CAPTCHA_COMMENT;
			$captcha = empty($_POST['captcha_tag'])         ? $captcha : $captcha | CAPTCHA_TAG;
			$captcha = empty($_POST['captcha_admin'])       ? $captcha : $captcha | CAPTCHA_ADMIN;
			$captcha = empty($_POST['captcha_login_fail'])  ? $captcha : $captcha | CAPTCHA_LOGIN_FAIL;
			$captcha = empty($_POST['captcha_message'])     ? $captcha : $captcha | CAPTCHA_MESSAGE;

			$captcha_width = empty($_POST['captcha_width'])     ? 100 : intval($_POST['captcha_width']);
			$captcha_height = empty($_POST['captcha_height'])   ? 30 : intval($_POST['captcha_height']);
			$m = M('site_config');
			$m->where('code="captcha"')->save(array('value'=>$captcha));
			$m->where('code="captcha_width"')->save(array('value'=>$captcha_width));
			$m->where('code="captcha_height"')->save(array('value'=>$captcha_height));
			clear_all_caches();
			$this->success('编辑成功');
		}
		$res = M('site_config')->where('code="captcha"')->find();
		$captcha = $res['value'];
		$captcha_check = array();
		if ($captcha & CAPTCHA_REGISTER)
		{
			$captcha_check['register']          = 'checked="checked"';
		}
		if ($captcha & CAPTCHA_LOGIN)
		{
			$captcha_check['login']             = 'checked="checked"';
		}
		if ($captcha & CAPTCHA_COMMENT)
		{
			$captcha_check['comment']           = 'checked="checked"';
		}
		if ($captcha & CAPTCHA_ADMIN)
		{
			$captcha_check['admin']             = 'checked="checked"';
		}
		if ($captcha & CAPTCHA_MESSAGE)
		{
			$captcha_check['message']    = 'checked="checked"';
		}
		if ($captcha & CAPTCHA_LOGIN_FAIL)
		{
			$captcha_check['login_fail_yes']    = 'checked="checked"';
		}
		else
		{
			$captcha_check['login_fail_no']     = 'checked="checked"';
		}

		$this->assign('captcha',          $captcha_check);
		$res = M('site_config')->where('code="captcha_width"')->find();
		$this->assign('captcha_width',    $res['value']);
		$res = M('site_config')->where('code="captcha_height"')->find();
		$this->assign('captcha_height',   $res['value']);
		$this->assign('ur_href', '验证码管理 &gt; 验证码设置');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
}