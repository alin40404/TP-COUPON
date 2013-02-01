<?php
/**
 * TemplateTpc.class.php
 * @copyright			copyright(c) 2011 - 2012 极好居
 * @author				anqiu xiao
 * @contact				QQ:89249294 E-mail:jihaoju@qq.com
 * @date				Sun May 13 14:00:54 CST 2012
 */

/**
 +-------------------------------------
 * TP-COUPON模板引擎驱动类
 +-------------------------------------
 */
class TemplateTpc {

    /**
     +----------------------------------------------------------
     * 渲染模板输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile 模板文件名
     * @param array $var 模板变量
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function fetch($templateFile,$_var) {
    	$template_dir = dirname($templateFile) . DIRECTORY_SEPARATOR;
    	vendor('TpcTemplate.template#class');
    	if(C('TMPL_ENGINE_CONFIG')) {
    		$config  =  C('TMPL_ENGINE_CONFIG');
    		$options = array();
    		foreach ($config as $key=>$val){
    			$options[$key] = $val;
    		}
    		unset($config);
    	}else{
    		$options = array(
    					'template_dir' => TMPL_PATH, //指定模板文件存放目录
    					'cache_dir' => CACHE_PATH, //指定模板缓存文件存放目录
    					'auto_update' => true, //当模板文件有改动时重新生成缓存 [关闭该项会快一些]
    					'cache_lifetime' => 0, //缓存生命周期(分钟)，为 0 表示永久 [设置为 0 会快一些]
    					'suffix' => '.html', //后缀
    					);
    	}
    	if(is_dir($template_dir)){
    		$options['template_dir'] = $template_dir;
    	}
    	$template = Template::getInstance(); //使用单件模式实例化模板类
    	$template->setOptions($options); //设置模板参数
    	unset($template_dir);
        extract($_var);
        if(is_file($templateFile)){
    		include($template->getfile(basename($templateFile, $options['suffix'])));
    	}else{
    		include($template->getfile(basename($template_dir . $templateFile, $options['suffix'])));
    	}
    }
}