<?php
/**
 * UserService.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 15 01:39:22 CST 2012
 */
class UserService
{
	public function after_logined(array $user, $save=false)
    {
    	if(! $user['user_id'] || ! $user['nick']){
    		exit();
    	}
    	$uModel = D('User');
    	$data = array(
    				'last_login'	=>	LocalTime::getInstance()->gmtime(),
    				'last_ip'		=>	get_client_ip()
    				);
    	$uModel->update($user['user_id'], $data);
    	//$_SESSION[C('SESSION_PREFIX') . 'user_id'] = $user_id;
    	//$_SESSION[C('SESSION_PREFIX') . 'nick'] = $user_name;
    	$life = 0;
    	if($save){
    		$life = 3600*24*30;
    	}
    	cookie('auth', authcode($user['user_id']."\t".$user['nick'], 'ENCODE', C('AUTH')), array('expire'=>$life));
    	cookie('avatar', $user['avatar'], array('expire'=>$life));
    }
    
    public function after_logouted()
    {
    	//$_SESSION[C('SESSION_PREFIX') . 'user_id'] = 0;
    	//$_SESSION[C('SESSION_PREFIX') . 'nick'] = '';
    	unset($_SESSION['qq'], $_SESSION['sina']);
    	cookie('auth', null);
    	cookie('avatar', null);
    }
}