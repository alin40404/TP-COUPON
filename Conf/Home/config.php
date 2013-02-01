<?php
return array (
		'URL_MODEL' => 1, // URL模式,0:普通模式、2：REWRITE模式、3：兼容模式
		'URL_HTML_SUFFIX' => '.html',
		'URL_PATHINFO_DEPR' => '-',
		'LOGIN_MODULES' => array (
				'User',
				'Payment' 
		), // 需要登陆的模块
		'NOT_LOGIN_ACTIONS' => array (
				'User.reg',
				'User.login',
				'User.logout',
				'User.check_nick_valid',
				'User.login_sina',
				'User.sina_callback',
				'User.login_qq',
				'User.qq_callback',
				'User.bind',
				'User.uc_synlogin',
				'User.uc_deleteuser',
				'User.uc_updatepw',
				'User.uc_renameuser',
				'User.uc_updatecredit',
				'User.getpwd',
				'Payment.pay_callback',
				'Payment.pay_notify' 
		), // 不需要登陆的操作
		'TOKEN_ON' => true,
		'SESSION_PREFIX' => 'jihaoju_', // session前缀
		'COOKIE_PREFIX' => 'jihaoju_', // cookie前缀
		'HTML_CACHE_ON' => false, // 开启静态缓存
		'HTML_PATH' => '__APP__/Html', // 静态缓存保存路径
		'HTML_CACHE_TIME' => '60', // 静态缓存的有效时间,单位秒
		'DEFAULT_THEME' => 'default', // 默认模板主题
		/*
		 * 'TMPL_ENGINE_TYPE' 	=> 'Smarty',	//使用smarty模板引擎 'TMPL_ENGINE_CONFIG'
		 * => array( 'caching' 		=> false, 'template_dir' 	=> TMPL_PATH,
		 * 'compile_dir' 	=> CACHE_PATH . 'Home/', 'cache_dir' 	=> TEMP_PATH .
		 * 'Home/', ),	//smarty配置
		 */
		
		'TMPL_ENGINE_TYPE' => 'Tpc', // 使用TP-COUPON模板引擎
		'TMPL_L_DELIM' => '{',
		'TMPL_R_DELIM' => '}',
		'TMPL_ENGINE_CONFIG' => array (
				'template_dir' => TMPL_PATH,
				'cache_dir' => CACHE_PATH . 'Home' . DIRECTORY_SEPARATOR . MODULE_NAME . DIRECTORY_SEPARATOR,
				'auto_update' => false,
				'cache_lifetime' => 0,
				'suffix' => '.html' 
		), // Tpc配置
		
		'TMPL_ACTION_ERROR' => TMPL_PATH . 'Home/default/Public/success.html',
		'TMPL_ACTION_SUCCESS' => TMPL_PATH . 'Home/default/Public/success.html',
		'APP_ACTION_DENY_LIST' => array(/*'User.after_logined'*/), // 禁止外部访问的操作
		'SPHINX_ON' => false,  // 开启sphinx检索引擎

);