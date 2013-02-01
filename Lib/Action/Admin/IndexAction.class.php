<?php
/**
 * IndexAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 01:06:50 CST 2012
 */
class IndexAction extends AdminCommonAction
{	
	public function index()
	{
		$this->display();
	}
	
	public function top()
	{
		$nav_conf = C('_menus_.Admin');
		$top_navs = array();
		foreach ($nav_conf as $nav){
			$top_navs[] = array(
								'id'	=> $nav['id'],
								'name'	=> $nav['name'],
								);
		}
		$this->assign('top_navs', $top_navs);
		$this->assign('user_id', $_SESSION[C('SESSION_PREFIX').'user_id']);
		$this->assign('name', $_SESSION[C('SESSION_PREFIX').'name']);
		$this->assign('user_name', $_SESSION[C('SESSION_PREFIX').'user_name']);
		$this->display();
	}
	
	public function left()
	{
		$nav_conf = C('_menus_.Admin');
		if(isset($_REQUEST['id'])){
			$id = $_REQUEST['id'];
		}else{
			$id = key($nav_conf);
		}
		if(! isset($nav_conf[$id])){
			die('id is invalid.');
		}
		if($id != 'priv'){
			$navs = $nav_conf[$id]['sub_nav'];
		}else{
			$navs = array();
			$navs[0] = array(
						'module'		=>	'Priv',
						'displayName'	=>	'权限管理',
						'actions'		=>	array()
						);
			unset($nav_conf['admin'], $nav_conf['system']);
			foreach ($nav_conf as $c){
				foreach ($c['sub_nav'] as $sn){
					$navs[0]['actions'][] = array(
												'action'		=>	'set',
												'displayName'	=>	$sn['displayName'],
												'params'		=>	array('module'=>$sn['module']),
												);
				}
			}
		}
		$this->assign('navs', $navs);
		$this->display();
	}
	
	public function main()
	{
		
	}
	
	public function bar()
	{
		$this->display();
	}
}