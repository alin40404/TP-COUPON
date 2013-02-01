<?php
return array(
'LANG_SWITCH_ON' 		=> true,//开启多语言转换
'DEFAULT_LANGUAGE'		=>'zh-cn',     // 设置默认语言为简体中文
'LANG_AUTO_DETECT' 		=> true, // 自动侦测语言 开启多语言功能后有效
'LANG_LIST'        		=> 'zh-cn', // 允许切换的语言列表 用逗号分隔
'URL_MODEL'				=> 0,
'NOT_AUTH_MODULES'		=> array('Index', 'User'),//无需验证权限的模块
'NOT_AUTH_ACTIONS'		=> array('User.login'),//无需验证权限的操作
'NOT_LOGIN_ACTIONS'		=> array('User.login'),//无需登陆的操作
'TOKEN_ON'				=> true,//是否开启表单验证
'SESSION_PREFIX'		=> 'admin_',//session前缀
'COOKIE_PREFIX'			=> 'admin_',//cookie前缀
'TMPL_ENGINE_TYPE' 	=> 'Smarty',	//使用smarty模板引擎
'TMPL_ENGINE_CONFIG' => array( 
    'caching' 		=> false, 
    'template_dir' 	=> TMPL_PATH,
    'compile_dir'  	=> CACHE_PATH . 'Admin/',
    'cache_dir' 	=> TEMP_PATH . 'Admin/',
),	//smarty配置
'TMPL_ACTION_ERROR'		=> TMPL_PATH . 'Admin/Public/success.html',
'TMPL_ACTION_SUCCESS'	=> TMPL_PATH . 'Admin/Public/success.html',
'APP_ACTION_DENY_LIST'	=> array(/*'Article.create_html'*/),//禁止外部访问的操作
);