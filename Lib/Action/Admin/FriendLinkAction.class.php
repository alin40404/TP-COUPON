<?php
/**
 * FriendLinkAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:14:34 CST 2012
 */
/**
 * 友情链接管理
 *
 */
class FriendLinkAction extends AdminCommonAction
{
	private $_link_position_conf;

	protected function _initialize()
    {
    	parent::_initialize();
    	$this->_link_position_conf = FriendLinkTypeConf::type_conf();
    }
	/**
	 * 列表
	 *
	 */
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$flModel = D('FriendLink');
    	$res = $flModel->getAll(array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$links = array();
    	foreach ($res['data'] as $link){
    		$link['position'] = $this->_link_position_conf[$link['position_id']];
    		if($link['link_type'] == 2){
    			if(is_file(DOC_ROOT_PATH . FixedUploadedFileUrl($link['link_code']))){
    				$link['link_code'] = FixedUploadedFileUrl($link['link_code']);
    			}
    		}
    		$links[] = $link;
    	}
    	$this->assign('links', $links);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]";
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('ur_href', '友情链接管理 &gt; 链接列表');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	/**
	 * 添加
	 *
	 */
	public function add()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['site_name'] || ! $_REQUEST['sort_order']){
				die('data invalid.');
			}
			$link_type = intval($_REQUEST['link_type']);
			$flModel = D('FriendLink');
			$id = 0;
			$data = array(
						'site_name'						=>	$_REQUEST['site_name'],
						'link_type'						=>	$link_type,
						'link_url'						=>	$_REQUEST['link_url'],
						'position_id'					=>	intval($_REQUEST['position_id']),
						'sort_order'					=>	intval($_REQUEST['sort_order']),
						);
			//文字链接
			if($link_type == 1){
				$data['link_code'] = $_REQUEST['link_text'];
			}
			//图片链接
			elseif($link_type == 2){
				if(! $_REQUEST['logo_url'] && (! $_FILES['link_logo']
				 || $_FILES['link_logo']['size'] == 0 || $_FILES['link_logo']['error'] != 0)){
					die('data invalid.');
				}
				if($_REQUEST['logo_url']){
					$data['link_code'] = $_REQUEST['logo_url'];
				}
				if($_FILES['link_logo'] && $_FILES['link_logo']['size'] > 0 && $_FILES['link_logo']['error'] == 0){
					$upfile = array();
					$upfile = upload_one_file($_FILES['link_logo']);
					if($upfile['error']){
						$this->error($upfile['error']);
					}
					$data['link_code'] = $upfile['file_name'];
				}
			}
			if($flModel->addLink($id, $data)){
				$params = null;
				B('FriendLinks', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
    	$this->assign('link_position_conf', $this->_link_position_conf);
		$this->assign('ur_href', '友情链接管理 &gt; 添加链接');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 编辑
	 *
	 */
	public function edit()
	{
		$flModel = D('FriendLink');
		$link_id = intval($_REQUEST['link_id']);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['site_name'] || ! $_REQUEST['sort_order']){
				die('data invalid.');
			}
			$link_type = intval($_REQUEST['link_type']);
			$flModel = D('FriendLink');
			$data = array(
						'site_name'						=>	$_REQUEST['site_name'],
						'link_type'						=>	$link_type,
						'link_url'						=>	$_REQUEST['link_url'],
						'position_id'					=>	intval($_REQUEST['position_id']),
						'sort_order'					=>	intval($_REQUEST['sort_order']),
						);
			//文字链接
			if($link_type == 1){
				$data['link_code'] = $_REQUEST['link_text'];
			}
			//图片链接
			elseif($link_type == 2){
				if(! $_REQUEST['logo_url'] && (! $_FILES['link_logo']
				 || $_FILES['link_logo']['size'] == 0 || $_FILES['link_logo']['error'] != 0)){
					die('data invalid.');
				}
				if($_REQUEST['logo_url']){
					$data['link_code'] = $_REQUEST['logo_url'];
				}
				if($_FILES['link_logo'] && $_FILES['link_logo']['size'] > 0 && $_FILES['link_logo']['error'] == 0){
					$upfile = array();
					$upfile = upload_one_file($_FILES['link_logo']);
					if($upfile['error']){
						$this->error($upfile['error']);
					}
					$data['link_code'] = $upfile['file_name'];
				}
			}
			if($flModel->editLink($link_id, $data)){
				$params = null;
				B('FriendLinks', $params);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$fl = $flModel->info($link_id);
		
		if(is_file(DOC_ROOT_PATH . FixedUploadedFileUrl($fl['link_code']))){
			$fl['link_code'] = 'http://'.$_SERVER['HTTP_HOST'] . FixedUploadedFileUrl($fl['link_code']);
		}
		$this->assign('link', $fl);
    	$this->assign('link_position_conf', $this->_link_position_conf);
		$this->assign('ur_href', '友情链接管理 &gt; 编辑链接');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 删除
	 *
	 */
	public function del()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$link_id = intval($_REQUEST['id']);
			$flModel = D('FriendLink');
			$link = $flModel->info($link_id);
			if($flModel->del($link_id)){
				if(is_file(FixedUploadedFileUrl($link['link_code']))){
					@unlink(FixedUploadedFileUrl($link['link_code']));
				}
				$params = null;
				B('FriendLinks', $params);
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
}