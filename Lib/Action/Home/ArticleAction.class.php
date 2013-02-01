<?php
/**
 * ArticleAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Thu Apr 26 23:42:11 CST 2012
 */
class ArticleAction extends HomeCommonAction
{
    /**
     * 友情链接
     * 
     */
    public function links()
    {
		//友情链接
		$friendlinks = array();
		$flService = service('FriendLinks');
		$res = $flService->getAll();
		$friendlinks = $res['all'];
		$this->assign('friendlinks', $friendlinks);
    	$this->assign('page_title', '友情链接 - ');
    	$this->assign('page_keywords', $this->_CFG['site_keywords']);
    	$this->assign('page_description', $this->_CFG['site_description']);
    	$this->display();
    }
    
    public function build_html()
    {
    	$article_id = intval($_REQUEST['article_id']);
    	$time = intval($_REQUEST['time']);
    	$auth = $_REQUEST['auth'];
    	if((time()-$time)>5 || md5($article_id . $time . C('AUTH')) != $auth){
    		exit();
    	}
    	$aModel = D('Article');
		$article = $aModel->info($article_id);
		if($article['alias']){
			$htmlfile = $article['alias'];
		}else{
			$htmlfile = $article_id;
		}
		import('@.Com.Util.Ubb');
		$article['content'] = Ubb::ubb2html($article['content']);
		$this->assign('article', $article);
		//其他文章
		$other_articles = $aModel->getByCateId($article['cate_id'], array('article_id','title','alias'));
		$this->assign('other_articles', $other_articles);
		$page_title = $article['title'] . ' - ';
		$this->assign(array('page_title'=>$page_title));
		$this->buildHtml($htmlfile, HTML_PATH, 'article');
    }
}