<?php
return array(
'Admin'	=>	array
(
					'couponCode'=>array(
							'id' 	  => 'couponCode',
							'name' 	  => '网购优惠券',
							'sub_nav' => array(
											array(
											'module'		=>	'CouponCode',
											'displayName'	=>	'优惠券管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'优惠券列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'best',
																		'displayName'	=>	'每日精选优惠券',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加优惠券',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'CouponCodeMall',
											'displayName'	=>	'商家管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'商家列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'recs',
																		'displayName'	=>	'推荐商家列表',
																		'params'		=>	array(),
																		),
																	 array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加商家',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'MallPromotion',
											'displayName'	=>	'促销活动管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'活动列表',
																		'params'		=>	array(),
																		),
																	 array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加活动',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'MallZhekou',
											'displayName'	=>	'折扣商品管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'折扣列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'category',
																		'displayName'	=>	'折扣分类列表',
																		'params'		=>	array(),
																		),
																	 array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加折扣商品',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'CouponCodeCategory',
											'displayName'	=>	'分类管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'分类列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加分类',
																		'params'		=>	array(),
																		),
																	),
											)
											),
					),
					'front'=>array(
							'id' 	  => 'front',
							'name' 	  => '前台',
							'sub_nav' => array(
											array(
											'module'		=>	'Article',
											'displayName'	=>	'文章管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'文章列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加文章',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'category',
																		'displayName'	=>	'文章分类',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'Adv',
											'displayName'	=>	'广告管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'广告列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加广告',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'AdvPosition',
											'displayName'	=>	'广告位管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'广告位列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加广告位',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'FriendLink',
											'displayName'	=>	'友情链接管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'链接列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加链接',
																		'params'		=>	array(),
																		),
																	),
											)
											),
					),
					'member'=>array(
							'id' 	  => 'member',
							'name' 	  => '会员管理',
							'sub_nav' => array(
											array(
											'module'		=>	'Member',
											'displayName'	=>	'会员管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'会员列表',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'Payment',
											'displayName'	=>	'充值管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'充值记录',
																		'params'		=>	array(),
																		),
																	),
											),
											),
					),
					'priv'=>array(
							'id' 	  => 'priv',
							'name' 	  => '权限管理',
					),
					'admin'=>array(
							'id' 	  => 'admin',
							'name' 	  => '管理员',
							'sub_nav' => array(
											array(
											'module'		=>	'Admin',
											'displayName'	=>	'管理员管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'管理员列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加管理员',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'Role',
											'displayName'	=>	'角色管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'角色列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加角色',
																		'params'		=>	array(),
																		),
																	),
											),
											),
					),
					'system'=>array(
							'id' 	  => 'system',
							'name' 	  => '系统',
							'sub_nav' => array(
											array(
											'module'		=>	'SysConf',
											'displayName'	=>	'系统管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'setting',
																		'displayName'	=>	'系统设置',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'VerifyCode',
											'displayName'	=>	'验证码管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'setting',
																		'displayName'	=>	'验证码设置',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'City',
											'displayName'	=>	'城市管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'城市列表',
																		'params'		=>	array(),
																		),
																	array(
																		'action'		=>	'add',
																		'displayName'	=>	'添加城市',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'TempFile',
											'displayName'	=>	'临时文件管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'index',
																		'displayName'	=>	'临时文件管理',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'Cache',
											'displayName'	=>	'缓存管理',
											'actions'		=>	array(
																	array(
																		'action'		=>	'clear',
																		'displayName'	=>	'清除系统缓存',
																		'params'		=>	array(),
																		),
																	),
											),
											array(
											'module'		=>	'SearchIndex',
											'displayName'	=>	'更新全文索引',
											'actions'		=>	array(
																	array(
																		'action'		=>	'update',
																		'displayName'	=>	'更新全文索引',
																		'params'		=>	array(),
																		),
																	),
											),
											),
					),
)
);