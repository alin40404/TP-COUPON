<?php
/**
 * MemberAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue May 01 22:49:57 CST 2012
 */
class MemberAction extends AdminCommonAction 
{
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$localTimeObj = LocalTime::getInstance();
    	$uModel = D('User');
    	$params = array(
    					'nick'		=>	isset($_REQUEST['nick']) && $_REQUEST['nick'] ? $_REQUEST['nick'] : ''
    					);
    	$res = $uModel->getAll($params, array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$users = array();
    	foreach ($res['data'] as $rs){
    		$rs['last_login'] = $localTimeObj->local_date($this->_CFG['time_format'], $rs['last_login']);
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
		$this->assign('_hash_', buildFormToken());
		$this->assign('ur_href', '会员管理 &gt; 会员列表');
		$this->display();
	}
}