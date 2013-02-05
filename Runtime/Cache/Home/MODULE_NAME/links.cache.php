<? if (!class_exists('template')) die('Access Denied');$template->getInstance()->check('links.html', '99d79347228831e53ff02236fc0f8937', 1360029054);?>

<? include($template->getfile("../Public/header")); include($template->getfile("../Public/nav")); ?>
<div class="w990 clear">
        <div id="main">
            <div class="main_l">
                <div id="main_l_b" class="clear">
                    <h2>
                        友情链接</h2>
                    <div class="w630">
                        <ul class="flink">
                            <? if(is_array($friendlinks)) { foreach($friendlinks as $item) { ?>                            <? if($item['link_type']==1) { ?>
                            <li><a href="<?=$item['link_url']?>" target="_blank"><? if($item['link_code']) { ?><?=$item['link_code']?><? } else { ?><?=$item['site_name']?><? } ?></a></li>
                            <? } ?>
                            <? } } ?>                        </ul>
                        <div class="clear">
                        </div>
                        <ul class="flink_img">
                            <? if(is_array($friendlinks)) { foreach($friendlinks as $item) { ?>                            <? if($item['link_type']==2) { ?>
                            <li><a href="<?=$item['link_url']?>" target="_blank"><img src="<? echo FixedUploadedFileUrl($item['link_code']);; ?>" width="75" height="45" /></a></li>
                            <? } ?>
                            <? } } ?>                        </ul>
                        <div class="clear">
                        </div>
                        <p>
                            友情链接请联系 QQ：<?=$_CFG['service_qq']?>&nbsp;<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?=$_CFG['service_qq']?>&amp;site=qq&amp;menu=yes" target="_blank"><img border="0" title="点击这里联系<?=$_CFG['site_name']?>" alt="点击这里联系<?=$_CFG['site_name']?>" src="http://wpa.qq.com/pa?p=2:<?=$_CFG['service_qq']?>:41"></a></p>
                        <div class="clear">
                        </div>
                    </div>
                </div>
            </div>
            <div class="main_r" style="width: 320px;">
                
<ul class="sidebar">
    <li class="blue green">
        <h3>
            相关信息</h3>
        <ul class="twocol">
            <li class="curr"><a href="__ROOT__/Html/about.html" tittle="关于我们">关于我们</a></li><li><a href="__ROOT__/Html/contact.html" tittle="联系我们">联系我们</a></li><li><a href="__APP__?m=Article&amp;a=links" tittle="友情链接">友情链接</a></li>
        </ul>
    </li>
</ul>

            </div>
        </div>
    </div>
<? include($template->getfile("../Public/footer")); ?>
