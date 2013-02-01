<?php
/**
 * ArticleAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun Apr 08 00:09:31 CST 2012
 */
/**
 * 文章管理
 *
 */
class ArticleAction extends AdminCommonAction
{
	/**
	 * 列表
	 *
	 */
	public function index()
	{
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] >= 1 ? $_REQUEST['page'] : 1;
    	$pageLimit = 15;
    	$aModel = D('Article');
    	$acModel = D('ArticleCategory');
    	$localTimeObj = LocalTime::getInstance();
    	$params = array();
    	$res = $aModel->getAll($params, array('begin'=>($page-1)*$pageLimit, 'offset'=>$pageLimit));
    	$articles = array();
    	foreach ($res['data'] as $rs){
    		$category = $acModel->info($rs['cate_id']);
    		$rs['cate_name'] = $category['cate_name'];
    		$rs['addtime'] = $localTimeObj->local_date($this->_CFG['time_format'], $rs['addtime']);
    		$articles[] = $rs;
    	}
    	$this->assign('articles', $articles);
    	$page_url = "?g=".GROUP_NAME."&m=".MODULE_NAME."&a=".ACTION_NAME."&page=[page]";
    	$p=new Page($page,
    			$pageLimit,
    			$res['count'],
    			$page_url,
    			5,
    			5);
    	$pagelink=$p->showStyle(3);
    	$this->assign('pagelink', $pagelink);
		$this->assign('ur_href', '文章管理 &gt; 文章列表');
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
			if(! $_REQUEST['title'] || ! $_REQUEST['cate_id'] || ! $_REQUEST['content']){
				die('data invalid.');
			}
			$localTimeObj = LocalTime::getInstance();
			$_REQUEST['addtime'] = $localTimeObj->gmtime();
			$aModel = D('Article');
			$id = 0;
			if($aModel->_add($id, $_REQUEST)){
				//生成静态文章
				$this->_buildHtml($id);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$category = D('ArticleCategory')->getAll();
		$this->assign('category', $category);
		$this->assign('ur_href', '文章管理 &gt; 添加文章');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 编辑
	 *
	 */
	public function edit()
	{
		$aModel = D('Article');
		$article_id = intval($_REQUEST['article_id']);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['title'] || ! $_REQUEST['cate_id'] || ! $_REQUEST['content']){
				die('data invalid.');
			}
			$localTimeObj = LocalTime::getInstance();
			if($aModel->_edit($article_id, $_REQUEST)){
				//生成静态文章
				$this->_buildHtml($article_id);
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME);
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$article = $aModel->info($article_id);
		$this->assign('article', $article);
		$category = D('ArticleCategory')->getAll();
		$this->assign('category', $category);
		$this->assign('ur_href', '文章管理 &gt; 编辑文章');
		$this->assign('_hash_', buildFormToken());
		$this->display('post');
	}
	
	/**
	 * 删除文章
	 *
	 */
	public function del()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$article_id = intval($_REQUEST['id']);
			$aModel = D('Article');
			$article = $aModel->info($article_id);
			$file = HTML_PATH . 'Article/';
			if($article['alias']){
				$file .= $article['alias'];
			}else{
				$file .= $article['article_id'];
			}
			$file .= '.' . C('HTML_FILE_SUFFIX');
			if(is_file($file)){
				@unlink($file);
			}
			if($aModel->_delete($article_id)){
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
	
	/**
	 * 文章分类
	 *
	 */
	public function category()
	{
		$category = D('ArticleCategory')->getAll();
		$this->assign('category', $category);
		$this->assign('ur_href', '文章管理 &gt; 文章分类');
		$this->assign('_hash_', buildFormToken());
		$this->display();
	}
	
	/**
	 * 添加分类
	 *
	 */
	public function add_category()
	{
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['cate_name'] || ! $_REQUEST['sort_order']){
				die('data invalid.');
			}
			$acModel = D('ArticleCategory');
			$id = 0;
			if($acModel->_add($id, $_REQUEST)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=category');
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}
		$this->assign('ur_href', '文章管理 &gt; 文章分类');
		$this->assign('_hash_', buildFormToken());
		$this->display('post_category');
	}
	
	/**
	 * 编辑分类
	 *
	 */
	public function edit_category()
	{
		$acModel = D('ArticleCategory');
		$cate_id = intval($_REQUEST['cate_id']);
		if($this->isPost()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['cate_name'] || ! $_REQUEST['sort_order']){
				die('data invalid.');
			}
			if($acModel->_edit($cate_id, $_REQUEST)){
				$this->assign('jumpUrl', '?g='.GROUP_NAME.'&m='.MODULE_NAME.'&a=category');
				$this->success('编辑成功');
			}else{
				$this->error('编辑失败');
			}
		}
		$cate = $acModel->info($cate_id);
		$this->assign('category', $cate);
		$this->assign('ur_href', '文章管理 &gt; 文章分类');
		$this->assign('_hash_', buildFormToken());
		$this->display('post_category');
	}
	
	/**
	 * 删除分类
	 *
	 */
	public function del_category()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			$cate_id = intval($_REQUEST['id']);
			$acModel = D('ArticleCategory');
			if($acModel->_delete($cate_id)){
				//删除相关文章
				$aModel = D('Article');
				$articles = $aModel->getByCateId($cate_id, array('article_id','title','alias'));
				foreach ($articles as $a){
					$file = HTML_PATH . 'Article/';
					if($a['alias']){
						$file .= $a['alias'];
					}else{
						$file .= $a['article_id'];
					}
					$file .= '.' . C('HTML_FILE_SUFFIX');
					if(is_file($file)){
						@unlink($file);
					}
				}
				$aModel->delByCateId($cate_id);
				$this->ajaxReturn('',buildFormToken(),1);
			}else{
				$this->ajaxReturn('','',0);
			}
		}
	}
	
	/**
	 * 批量生成静态
	 *
	 */
	public function build_html()
	{
		if($this->isAjax()){
			if(C('TOKEN_ON') && ! checkFormToken($_REQUEST)){
				die('hack attemp.');
			}
			if(! $_REQUEST['id']){
				$this->ajaxReturn('', '请选择文章', 0);
			}
			set_time_limit(0);
			$ids = explode(',', $_REQUEST['id']);
			foreach ($ids as $id){
				$this->_buildHtml($id);
			}
			$this->ajaxReturn('',buildFormToken(),1);
		}
	}
	
	private function _buildHtml($article_id)
	{
		$time = time();
		$auth = md5($article_id . $time . C('AUTH'));
		$url = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/index.php';
		$data = 'g=Home&m=Article&a=build_html&article_id=' . $article_id . '&time=' . $time . '&auth=' . $auth;
		dCurl($url, $data, false);
	}
}