<? if (!class_exists('template')) die('Access Denied');$template->getInstance()->check('../Public/nav.html', '570fe8605a3a3069bfd11faefc2bf345', 1359708022);?>
</head>
<body>
<div class="toplink">
        <div class="w990">
            <div id="header">
               
                <div id="site_nav">
                 <div class="favl"> 
                        
                </div>
                    <ul class="quick_menu">
                        <li><iframe width="136" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&amp;width=136&amp;height=24&amp;uid=2507337250&amp;style=2&amp;btn=red&amp;dpc=1"></iframe>
                        </li>
                        <li class="fav"><a title="<?=$_CFG['site_name']?>" href="javascript:;" onClick="AddFavorite('http://'+window.location.host+'/__ROOT__', '<?=$_CFG['site_name']?>')"><font color="red">收藏<?=$_CFG['site_name']?></font></a></li>
                        <li><a href="__ROOT__/" title="网站首页">网站首页</a></li>
                        <li style="background: none;"><a title="联系我们" href="__ROOT__/Html/contact.html">联系我们</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<div class="w990 clear">
        <div id="header">
            <div class="top">
                <div class="logo">
                    <a title="<?=$_CFG['site_name']?> - 中国最大的优惠券网站" href="__ROOT__/">
                        <img alt="<?=$_CFG['site_name']?>" style="width: 303px; height: 82px;" src="__PUBLIC__/Images/Home/logo.jpg"></a></div>
                <div class="search">
                    <div class="search_f">
                        <form action="__ROOT__/index.php" method="get" id="searchform" name="searchform" onSubmit="if(this.kw.value==this.kw.defaultValue)return false;">
                        <ul>
                            <li id="search_c">
                                <input type="text" value="输入要搜索的商家名称，比如：京东或当当" autocomplete="off" id="search_i" name="kw" onFocus="if(this.value==this.defaultValue)this.value=''" onBlur="if(!this.value.length)this.value=this.defaultValue"></li>
                        </ul>
                        <div id="search_b"><input name="sub" type="image" value="搜索" src="__PUBLIC__/Images/Home/search.jpg"></div>
                        <input name="m" type="hidden" value="mall">
                        <input name="a" type="hidden" value="search">
                        </form>
                    </div>
                    <div class="search_t">
                    <? $hot_malls_10=hot_malls('week', 10); ?>                    <? if(is_array($hot_malls_10)) { foreach($hot_malls_10 as $key => $item) { ?>                    <a title="<?=$item['name']?>优惠券" href="<? echo reUrl("Home/Mall/view?id=".$item['id']); ?>"><?=$item['name']?></a>
                    <? } } ?>                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="w990 clear">
        <div id="nav">
            <div class="w990">
                <ul id="nav_l">
                    <li><a href="__ROOT__/" <? if(MODULE_NAME == 'Index' && ACTION_NAME=='index') { ?>class="active"<? } ?>><span></span>首页</a></li>
                    <li>|</li>
                    <li><a href="<? echo reUrl('Home/Code/latest'); ?>" <? if(MODULE_NAME == 'Code' && ACTION_NAME=='latest') { ?>class="active"<? } ?>><span></span>最新优惠券</a></li>
                    <li>|</li>
                    <li><a href="<? echo reUrl('Home/Code/hot'); ?>" <? if(MODULE_NAME == 'Code' && ACTION_NAME=='hot') { ?>class="active"<? } ?>><span></span>热门优惠券</a></li>
                    <li>|</li>
                    <li><a href="<? echo reUrl('Home/Code/lastestpulled'); ?>" <? if(MODULE_NAME == 'Code' && ACTION_NAME=='lastestpulled') { ?>class="active"<? } ?>><span></span>最近被领取的优惠券</a></li>
                    <li>|</li>
                    <li><a href="<? echo reUrl('Home/Mall/lists'); ?>" <? if(MODULE_NAME == 'Mall' && ACTION_NAME=='lists') { ?>class="active"<? } ?>><span></span>商家大全</a></li>
                    <li>|</li>
                    <li><a href="<? echo reUrl('Home/Promotion/index'); ?>" <? if(MODULE_NAME == 'Promotion' && ACTION_NAME=='index') { ?>class="active"<? } ?>><span></span>促销活动</a></li>
                    <li>|</li>
                    <li><a href="<? echo reUrl('Home/Zhekou/index'); ?>" <? if(MODULE_NAME == 'Zhekou' && ACTION_NAME=='index') { ?>class="active"<? } ?>><span></span>超值折扣</a></li>
                </ul>
                
<ul id="nav_r">
    <? if($user['user_id']) { ?>
    <li id="usercenter"><a href="<? echo reUrl('Home/User/codes'); ?>" class=""><span></span>账号中心</a>
        <div style="display: none;" class="nav_i">
            <div class="nav_i_u">
                <div class="nav_i_u_l">
                    <a href="javascript:void(0);"><img src="<?=$user['avatar']?>" onerror="this.src='__PUBLIC__/Images/Home/avatar.jpeg'"></a>
                </div>
                <div class="nav_i_u_i">
                    
                    <a href="javascript:void(0);"><?=$user['nick']?></a><br>
                    
                </div>
            </div>
            <ul>
                <li><a href="<? echo reUrl('Home/User/codes'); ?>" style="color: #f00">我领取的优惠券</a></li>
                <li><a href="<? echo reUrl('Home/Payment/pay'); ?>">账号充值</a></li>
                <li><a href="<? echo reUrl('Home/User/consume_records'); ?>">消费记录</a></li>
                <li><a href="<? echo reUrl('Home/User/invite'); ?>">邀请好友</a></li>
                <li><a href="<? echo reUrl('Home/User/editpwd'); ?>">修改密码</a></li>
                <li><a href="<? echo reUrl('Home/User/logout'); ?>">注销登录</a></li>
            </ul>
        </div>
    </li>
    <? } else { ?>
    <li id="userweibo">
    <a href="<? echo reUrl('Home/User/login'); ?>">登陆</a><a href="<? echo reUrl('Home/User/reg'); ?>">注册</a>
    </li>
    <? } ?>
</ul>

            </div>
        </div>
         
        
    </div>
<div class="clear"></div>