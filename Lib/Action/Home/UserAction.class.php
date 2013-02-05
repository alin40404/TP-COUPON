<?php
/**
 * UserAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue Apr 03 14:56:43 CST 2012
 */
class UserAction extends HomeCommonAction
{
	/**
     * 默认操作
     * 
     */
	public function index()
	{
		$this->codes();
	}

	public function reg()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST, 'hash')){
				die('hack attemp.');
			}
			if(!$_REQUEST['nick'] || !$_REQUEST['email'] || !$_REQUEST['pw']){
				exit('data invalid.');
			}
			$nick = $_REQUEST['nick'];
			$email = $_REQUEST['email'];
			$pw = $_REQUEST['pw'];
			$ucService = service('Uc');
			$uid = $ucService->register($nick, $pw, $email);
		
			//成功注册到UC
			if(!is_string($uid)){
				//本地注册
				$uModel = D('User');
				if(! $uModel->info($uid)){
					//添加邀请记录
					$invite = 0;
					if(cookie('invite')){
						$invite = intval(authcode(cookie('invite'), 'DECODE', C('AUTH')));
						//奖励邀请者积分
						Consume::increase($invite, $this->_CFG['invite_credit'], Consume::TYPE_CREDIT);
					}
					$addtime = LocalTime::getInstance()->gmtime();
					$uModel->_add(array('user_id'=>$uid,'nick'=>$nick, 'email'=>$email,'password'=>md5($pw),'invite'=>$invite,'addtime'=>$addtime));
				}
				if(isset($_REQUEST['ac']) && $_REQUEST['ac'] == 'dobind'){
					if($_REQUEST['type'] == 'sina'){
						include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
						$sina = new sina();
						$openid = $sina->get_openid();
					}elseif ($_REQUEST['type'] == 'qq'){
						include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
						$qq = new qq();
						$openid = $qq->get_openid();
					}
					$platform = M('user_platform');
					$data = array(
					'user_id'		=>	$uid,
					'type'			=>	$_REQUEST['type'],
					'openid'		=>	$openid
					);
					$platform->data($data)->add();
				}
				$this->ajaxReturn('', $uid, 1);
			}else{
				$this->ajaxReturn('', $uid, 0);
			}
		}
		if(isset($_REQUEST['invite']) && $_REQUEST['invite']){
			cookie('invite', authcode($_REQUEST['invite'], 'AECODE', C('AUTH')));
		}
		$this->assign('hash', buildFormToken('hash'));
		$this->assign('page_title', '用户注册 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}

	public function check_nick_valid()
	{
		if($this->isAjax()){
			$nick = $_REQUEST['nick'];
			$ucService = service('Uc');
			$result = $ucService->check_nick_valid($nick);
			$result = $result ? 1 : 0;
			$this->ajaxReturn('', '', $result);
		}
	}

	public function logout()
	{
		$userService = service('User');
		$userService->after_logouted();
		$ucService = service('Uc');
		$syncHtml = $ucService->build_synlogout();
		$this->assign('jumpUrl', __ROOT__ . '/');
		$this->success('退出成功'.$syncHtml);
	}

	public function login()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(!$_REQUEST['nick'] || !$_REQUEST['pw']){
				exit('data invalid.');
			}
			$nick = $_REQUEST['nick'];
			$save = isset($_REQUEST['save']) && $_REQUEST['save'] ? true : false;
			$pw = $_REQUEST['pw'];
			$ucService = service('Uc');
			$user = $ucService->login($nick, $pw);
			//成功登录到UC
			if(is_array($user)){
				//获取本地信息
				$uModel = D('User');
				$_user = $uModel->info($user['uid'], array('user_id', 'nick', 'email', 'password', 'is_locked'));
				//在本地注册
				if(! $_user){
					$uModel->_add(array('user_id'=>$user['uid'],'nick'=>$user['username'], 'email'=>$user['email'],'password'=>md5($user['password'])));
				}else{
					//是否被锁定
					if($_user['is_locked']){
						if($this->isAjax()){
							$this->ajaxReturn('', '该账号已被锁定', 0);
						}else{
							$this->error('该账号已被锁定');
						}
					}
					$uModel->update($user['uid'], array('password' => md5($user['password'])));
				}
				//绑定
				if(isset($_REQUEST['ac']) && $_REQUEST['ac'] == 'dobind'){
					if($_REQUEST['type'] == 'sina'){
						include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
						$sina = new sina();
						$openid = $sina->get_openid();
					}elseif ($_REQUEST['type'] == 'qq'){
						include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
						$qq = new qq();
						$openid = $qq->get_openid();
					}
					$platform = M('user_platform');
					if($platform->where("user_id='$user[uid]' AND `type`='$_REQUEST[type]' AND openid='$openid'")->find()){
						$platform->where("user_id='$user[uid]' AND `type`='$_REQUEST[type]' AND openid='$openid'")->delete();
					}
					$data = array(
					'user_id'		=>	$user['uid'],
					'type'			=>	$_REQUEST['type'],
					'openid'		=>	$openid
					);
					$platform->data($data)->add();
					$this->ajaxReturn('', '', 1);
				}
				$userService = service('User');
				$avatar = $ucService->get_avatar($user['uid']);
				$userService->after_logined(array('user_id'=>$user['uid'],'nick'=>$user['username'],'avatar'=>$avatar), $save);
				$syncHtml =  $ucService->build_synlogin($user['uid']);
				$this->assign('jumpUrl', reUrl('User/index'));
				$this->success('登陆成功'.$syncHtml);
			}else{
				if($this->isAjax()){
					$this->ajaxReturn('', $user, 0);
				}else{
					$this->error($user);
				}
			}
		}
		$this->assign('_hash_', buildFormToken());
		$this->assign('page_title', '用户登陆 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	public function editpwd()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$nick = $this->_user['nick'];
			$ucService = service('Uc');
			$oldpwd = $_REQUEST['oldpwd'];
			$newpw = $_REQUEST['newpwd'];
			$result = $ucService->uc_user_edit($nick , $oldpwd , $newpw);
			//更新成功
			if($result === true){
				//更新本地密码
				$uModel = D('User');
				$uModel->update($this->_user['user_id'], array('password' => md5($newpw)));
				$this->ajaxReturn('', '', 1);
			}
			else{
				$this->ajaxReturn('', $result, 0);
			}
		}
		$this->assign('_hash_', buildFormToken());
		$this->assign('page_title', '修改密码 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	public function getpwd()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$nick = $_REQUEST['nick'];
			$ucService = service('Uc');
			$user = $ucService->get_user_info($nick);
			if(! $user){
				$this->ajaxReturn('', '帐号错误', 0);
			}
			$newpw = rand_string(6);
			$result = $ucService->uc_user_edit($nick , '' , $newpw , '' , 1);
			//更新成功
			if($result === true){
				//更新本地密码
				$uModel = D('User');
				$uModel->update($user[0], array('password' => md5($newpw)));
				//发送邮件
				$email = $user[2];
				$subject = '取回密码——' . $this->_CFG['site_name'];
				$content = '尊敬的' . $nick . '：<br />';
				$content .= '您在' . $this->_CFG['site_name'] . '的登陆密码已更改为：' . $newpw . '<br />';
				$content .= '请尽快登陆' . $this->_CFG['site_name'] . '修改密码，登陆地址：<br />';
				$content .= '<a href="http://'. $_SERVER['HTTP_HOST'] .reUrl('User/editpwd').'" target="_blank">http://'. $_SERVER['HTTP_HOST'] .reUrl('User/editpwd').'</a>';
				send_mail($this->_CFG['site_name'], $email, $subject, $content, 1);
				$this->ajaxReturn('', '密码已发送到你注册的邮箱，请注意查收', 1);
			}
			else{
				$this->ajaxReturn('', $result, 0);
			}
		}
		$this->assign('_hash_', buildFormToken());
		$this->assign('page_title', '取回密码 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}

	public function login_sina()
	{
		include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
		$sina = new sina();
		$login_url = $sina->getUrl();
		redirect($login_url);
	}

	public function sina_callback()
	{
		if(! isset($_REQUEST['code']) || empty($_REQUEST['code'])){
			$this->assign('jumUrl', reUrl('User/login_sina'));
			$this->error('授权失败');
		}
		include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
		$sina = new sina();
		$token = $sina->initToken($_REQUEST['code']);
		if ($token) {
			$this->_on_sina_logined();
		}else {
			$this->assign('jumUrl', reUrl('User/login_sina'));
			$this->error('授权失败');
		}
	}

	public function login_qq()
	{
		include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
		$o = new qq();
		$login_url = $o->getUrl();
		redirect($login_url);
	}

	public function qq_callback()
	{
		include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
		$qq = new qq();
		$token = $qq->initToken();
		if ($token) {
			$this->_on_qq_logined();
		}else {
			$this->assign('jumUrl', reUrl('User/login_qq'));
			$this->error('授权失败');
		}
	}

	/**
     * 开放平台用户绑定帐号
     *
     */
	public function bind()
	{
		$type = $_REQUEST['type'];
		if(! in_array($type, C('OPEN_PLATFORM'))){
			$this->error('参数错误');
		}
		if(isset($_REQUEST['ac']) && $_REQUEST['ac'] == 'dobind'){
			if($type == 'sina'){
				include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
				$sina = new sina();
				//关注官方微博
				$sina->follow_office();
				$this->_on_sina_logined();
			}elseif($type == 'qq'){
				$this->_on_qq_logined();
			}
		}
		$nick = '';
		if($type == 'sina'){
			include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
			$sina = new sina();
			$u_info = $sina->userInfo();
			$nick = $u_info['screen_name'];
		}elseif($type == 'qq'){
			include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
			$qq = new qq();
			$u_info = $qq->userInfo();
			$nick = $u_info['nickname'];
		}
		$this->assign('nick', $nick);
		$this->assign('type', $type);
		$this->assign('_hash_', buildFormToken());
		$this->assign('hash', buildFormToken('hash'));
		$this->display();
	}

	private function _on_sina_logined()
	{
		include_once( DOC_ROOT_PATH . 'Addons/plugins/login/sina.class.php' );
		$sina = new sina();
		//检查是否已绑定帐号
		$openid = $sina->get_openid();
		$platformModel = M('user_platform');
		$user = $platformModel->field('id,user_id')->where("`type`='sina' AND openid='$openid'")->find();
		//已绑定
		if($user){
			//从UC中获取资料并同步UC应用
			$ucService = service('Uc');
			$userService = service('User');
			$_user = $ucService->get_user_info($user['user_id'], 1);
			if($_user === null){
				$platformModel->where("id='$user[id]'")->delete();
				redirect(reUrl('User/bind?type=sina'));
			}
			$sina_u_info = $sina->userInfo();
			$userService->after_logined(array('user_id'=>$_user[0],'nick'=>$sina_u_info['screen_name'],'avatar'=>$sina_u_info['profile_image_url']), false);
			$syncHtml =  $ucService->build_synlogin($_user[0]);
			$this->assign('jumpUrl', reUrl('User/index'));
			$this->success('登陆成功'.$syncHtml);
		}
		//未绑定，跳转到绑定页面
		else{
			redirect(reUrl('User/bind?type=sina'));
		}
	}

	private function _on_qq_logined()
	{
		include_once( DOC_ROOT_PATH . 'Addons/plugins/login/qq.class.php' );
		$qq = new qq();
		//检查是否已绑定帐号
		$openid = $qq->get_openid();
		$platformModel = M('user_platform');
		$user = $platformModel->field('id,user_id')->where("`type`='qq' AND openid='$openid'")->find();
		//已绑定
		if($user){
			//从UC中获取资料并同步UC应用
			$ucService = service('Uc');
			$userService = service('User');
			$_user = $ucService->get_user_info($user['user_id'], 1);
			if($_user === null){
				$platformModel->where("id='$user[id]'")->delete();
				redirect(reUrl('User/bind?type=qq'));
			}
			$qq_u_info = $qq->userInfo();
			$userService->after_logined(array('user_id'=>$_user[0],'nick'=>$qq_u_info['nickname'],'avatar'=>$qq_u_info['figureurl_2']), false);
			$syncHtml =  $ucService->build_synlogin($_user[0]);
			$this->assign('jumpUrl', reUrl('User/index'));
			$this->success('登陆成功'.$syncHtml);
		}
		//未绑定，跳转到绑定页面
		else{
			redirect(reUrl('User/bind?type=qq'));
		}
	}

	/**********************************会员中心****************************************************/
	/**
     * 领取的优惠券列表
     *
     */
	public function codes()
	{
		$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 10;
		$localTimeObj = LocalTime::getInstance();
		$limit = array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit);
		import('@.Com.Util.Ubb');
		$cccModel = D('CouponCodeCodes');
		$res = $cccModel->myCodes($this->_user['user_id'], $limit);
		$codes = array();
		foreach ($res['data'] as $rs){
			if($rs['expiry_type'] == 1){
				$rs['expiry'] = $localTimeObj->local_date($this->_CFG['date_format'], $rs['expiry']);
			}
			$rs['fetch_time'] = $localTimeObj->local_date($this->_CFG['time_format'], $rs['fetch_time']);
			$rs['directions'] = Ubb::ubb2html($rs['directions']);
			$codes[] = $rs;
		}
		$this->assign('codes', $codes);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$res['count'],
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$ccService = service('CouponCode');
		//最近被领取的10个优惠券
		$fetched10 = array();
		$data = $ccService->fetch_latest(10);
		foreach ($data as $d){
			$d['fetch_time'] = $localTimeObj->local_date('H:i:s', $d['fetch_time']);
			$fetched10[] = $d;
		}
		$this->assign('fetched10', $fetched10);
		//每日精选
		$daybest10 = array();
		$time = $localTimeObj->local_strtotime(date('Y-m-d 00:00:00'));
		$daybest10 = $ccService->daybest($time, 10);
		$this->assign('daybest10', $daybest10);
		$this->assign('page_title', '我领取的优惠券 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display('codes');
	}
	
	/**
	 * 积分兑换
	 *
	 */
	public function credit()
	{
		$ucService = service('Uc');
		$uc_apps = $ucService->uc_app_ls();
		//用户积分
		$uc_credit = $ucService->uc_user_getcredit(6, $this->_user['user_id'], 2);
		
		$this->assign('page_title', '积分兑换 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	/**
	 * 消费记录
	 *
	 */
	public function consume_records()
	{
		$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 10;
		$localTimeObj = LocalTime::getInstance();
		$c_count = M('consume_records')->where("user_id='".$this->_user['user_id']."'")->count();
		$res = M('consume_records')->where("user_id='".$this->_user['user_id']."'")
								   ->order("id DESC")
								   ->limit(($page-1)*$pageLimit.", ".$pageLimit)
								   ->select();
		$records = array();
		foreach ($res as $rs){
			$rs['addtime']		=	$localTimeObj->local_date($this->_CFG['time_format'], $rs['addtime']);
			$records[] = $rs;
		}
		$this->assign('records', $records);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$c_count,
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('page_title', '消费记录 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	public function invite()
	{
		$invite_link = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/?m=User&a=reg&invite=' . $this->_user['user_id'];
		$this->assign('invite_link', $invite_link);
		$this->assign('page_title', '邀请好友 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	public function myinvite()
	{
		$page = isset($_REQUEST['p']) && $_REQUEST['p'] >= 1 ? $_REQUEST['p'] : 1;
		$pageLimit = 15;
		$localTimeObj = LocalTime::getInstance();
		$c_count = M('user')->where("invite='".$this->_user['user_id']."'")->count();
		$res = M('user')->field('user_id,nick,addtime')->where("invite='".$this->_user['user_id']."'")
								   ->order("user_id DESC")
								   ->limit(($page-1)*$pageLimit.", ".$pageLimit)
								   ->select();
		$users = array();
		foreach ($res as $rs){
			$rs['addtime']		=	$localTimeObj->local_date($this->_CFG['time_format'], $rs['addtime']);
			$users[] = $rs;
		}
		$this->assign('users', $users);
		$page_url = reUrl(MODULE_NAME."/".ACTION_NAME."?p=[page]");
		$page_url = str_replace('%5bpage%5d', '[page]', $page_url);
		$p=new Page($page,
		$pageLimit,
		$c_count,
		$page_url,
		5,
		5);
		$pagelink=$p->showStyle(3);
		$this->assign('pagelink', $pagelink);
		$this->assign('page_title', '我的邀请记录 - ');
		$this->assign('page_keywords', $this->_CFG['site_keywords']);
		$this->assign('page_description', $this->_CFG['site_description']);
		$this->display();
	}
	
	/********************************接受UC通知***************************************************/
	public function uc_synlogin()
	{
		$time = $_GET['time'];
		if((time() - $time)>10) exit();
		$auth = $_GET['auth'];
		$username = $_GET['username'];
		$uid = $_GET['uid'];
		if(empty($username) || empty($auth) || empty($uid) || (md5($uid.$username.$time.C('AUTH'))!=$auth)) return ;
		$userService = service('User');
		$ucService = service('Uc');
		$avatar = $ucService->get_avatar($uid);
		$userService->after_logined(array('user_id'=>$uid,'nick'=>$username,'avatar'=>$avatar), false);
	}

	public function uc_deleteuser()
	{
		$time = $_GET['time'];
		if((time() - $time)>10) exit();
		$auth = $_GET['auth'];
		$ids = $_GET['ids'];
		if(empty($ids) || empty($auth) || (md5($ids.$time.C('AUTH'))!=$auth)) return ;
		$userModel = D('User');
		$userModel->_delete($ids);
		M('user_platform')->where("user IN ($ids)")->delete();
	}

	public function uc_updatepw()
	{
		$time = $_GET['time'];
		if((time() - $time)>10) exit();
		$auth = $_GET['auth'];
		$username = $_GET['username'];
		$password = $_GET['password'];
		if(empty($username) || empty($auth) || empty($password) || (md5($username.$password.$time.C('AUTH'))!=$auth)) return ;
		$userModel = D('User');
		$user = $userModel->infoByNick($username, array('user_id'));
		if($user){
			$userModel->update($user['user_id'], array('password'=>md5($password)));
		}
	}

	public function uc_renameuser()
	{
		$time = $_GET['time'];
		if((time() - $time)>10) exit();
		$auth = $_GET['auth'];
		$uid = intval($_GET['uid']);
		$oldusername = $_GET['oldusername'];
		$newusername = $_GET['newusername'];
		if(!$uid || empty($oldusername) || empty($auth) || empty($newusername) || (md5($uid.$oldusername.$newusername.$time.C('AUTH'))!=$auth)) return ;
		$userModel = D('User');
		$user = $userModel->info($uid, array('user_id'));
		if($user){
			$userModel->update($user['user_id'], array('nick'=>$newusername));
		}
	}
	
	public function uc_updatecredit()
	{
		$time = $_GET['time'];
		if((time() - $time)>10) exit();
		$auth = $_GET['auth'];
		$uid = intval($_GET['uid']);
		$credit = intval($_GET['credit']);
		$amount = intval($_GET['amount']);
		if(!$uid || !$credit || empty($auth) || !$amount || (md5($uid.$credit.$amount.$time.C('AUTH'))!=$auth)) return ;
		$increase = Consume::increase($uid, $amount, Consume::TYPE_CREDIT);
	}
}