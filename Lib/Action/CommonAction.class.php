<?php
/**
 * CommonAction.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Tue Apr 03 14:57:07 CST 2012
 */
class CommonAction extends Action
{
	protected $_CFG = null;
	protected $_refererUrl = null;
    protected function _initialize()
    {
    	unset($_SESSION['__hash__']);
    	/* 对用户传入的变量进行转义操作。*/
		if (get_magic_quotes_gpc())
		{
			if (!empty($_GET))
			{
				$_GET  = stripslashesDeep($_GET);
			}
			if (!empty($_POST))
			{
				$_POST = stripslashesDeep($_POST);
			}
		
			$_COOKIE   = stripslashesDeep($_COOKIE);
			$_REQUEST  = stripslashesDeep($_REQUEST);
		}
    	//加载扩展函数库
		//Load('extend');
		$this->_CFG = load_config();
		$this->_refererUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if(! $this->isAjax() && ! $this->isPost()){
			$this->assign('_CFG', $this->_CFG);
			$this->assign('refererUrl', $this->_refererUrl);
		}
	}
	
	/**
     +----------------------------------------------------------
     * 模板显示
     * 重写父类display方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param  string $content 输出内容
     * @param  string $prefix 模板缓存前缀
     * 
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	protected  function display($templateFile='',$charset='',$contentType='text/html',$content='',$prefix='')
	{
		if ($this->_CFG['open_gzip'] && extension_loaded('zlib') && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
			ini_set('zlib.output_compression', 'On');
			ini_set('zlib.output_compression_level',3);
			ob_start('ob_gzhandler');
			parent::display($templateFile,$charset,$contentType,$content,$prefix);
			ob_end_flush();
		}else {
			parent::display($templateFile,$charset,$contentType,$content,$prefix);
		}
		
	}
	
	/**
     +----------------------------------------------------------
     * 操作成功跳转的快捷方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function success($message,$jumpUrl='',$ajax=false) {
    	if(! $ajax) $this->assign('page_title', '提示信息 - ');
        parent::success($message,$jumpUrl,$ajax);
        exit;
    }
    
    /**
     +----------------------------------------------------------
     * 操作错误跳转的快捷方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function error($message,$jumpUrl='',$ajax=false) {
    	if(! $ajax) $this->assign('page_title', '提示信息 - ');
    	parent::error($message,$jumpUrl,$ajax);
        exit();
    }
}