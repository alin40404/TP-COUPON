<? if (!class_exists('template')) die('Access Denied');$template->getInstance()->check('lists.html', 'ff998d18e433a13210d152e278d37149', 1359708056);?>

<? include($template->getfile("../Public/header")); include($template->getfile("../Public/nav")); ?>
<div class="w990 clear">
        <div style="padding-top: 0;" id="main" class="mt10">
        <div style="width:990px;" class="main_l">
        <div class="clear" id="main_l_b">
            <h2>优惠券商家</h2>
            <dl style="width:950px;" class="filter clear"><dt style="height:20px;">商家类型：</dt>
                <dd <? if($cid==0) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid=0&t_type='.$t_type.'&cid2=0&p=1');; ?>">全部</a></dd>
                        <? if(is_array($cates)) { foreach($cates as $item) { ?>                        <? if($item['level']==0) { ?>
                        <dd <? if($cid==$item['id']) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$item['id'].'&t_type='.$t_type.'&cid2=0&p=1');; ?>"><?=$item['name']?></a></dd>
                        <? } ?>
                        <? } } ?>            </dl>
            <? if($cate_children) { ?>
            <dl style="width:950px;" class="filter clear">
                    	<dt style="height: 40px;">二级类型：</dt>
                        <? if(is_array($cate_children)) { foreach($cate_children as $item) { ?>                        <dd <? if($cid2==$item['id']) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type='.$t_type.'&cid2='.$item['id'].'&p=1');; ?>"><?=$item['name']?></a></dd>
                        <? } } ?>            </dl>
            <? } ?>
            <dl style="width:950px;" class="filter clear"><dt style="height:20px;">排序：</dt>
                <dd <? if($t_type==0) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type=0&cid2='.$cid2.'&p=1');; ?>">不限</a></dd>
                <dd <? if($t_type==1) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type=1&cid2='.$cid2.'&p=1');; ?>">发布时间</a></dd>
                <dd <? if($t_type==2) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type=2&cid2='.$cid2.'&p=1');; ?>">昨日最热</a></dd>
                <dd <? if($t_type==3) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type=3&cid2='.$cid2.'&p=1');; ?>">今日最热</a></dd>
                <dd <? if($t_type==4) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type=4&cid2='.$cid2.'&p=1');; ?>">本周最热</a></dd>
                <dd <? if($t_type==5) { ?>class="current"<? } ?>><a href="<? echo reUrl('Mall/lists?cid='.$cid.'&t_type=5&cid2='.$cid2.'&p=1');; ?>">本月最热</a></dd>
            </dl>
            
            <ul style="margin-bottom:15px;" class="blist bshop clear">
            <? if(is_array($malls)) { foreach($malls as $item) { ?>                        <li>
                        <div class="blist_p">
                            <a title="<?=$item['name']?>" href="<? echo reUrl('Mall/view?id='.$item['id']);; ?>"><img alt="<?=$item['name']?>" src="<? echo FixedUploadedFileUrl($item['logo']);; ?>"></a>
                            <div class="functions">
                                <a title="<?=$item['name']?>" class="favourite" href="<? echo reUrl('Mall/view?id='.$item['id']);; ?>" onclick="AddFavorite(this.href, this.title); return false;">收藏</a>
                            </div>
                        </div>
                        <div class="blist_n">
                            <h4>
                                <a class="bname" title="" href="<? echo reUrl('Mall/view?id='.$item['id']);; ?>"><?=$item['name']?></a>
                                                            </h4>
                            <div class="outlink"><a target="_blank" rel="nofollow" href="<? echo reUrl('Mall/out?id='.$item['id']);; ?>"><?=$item['website']?></a></div>                            <div class="intro"><? echo msubstr($item['description'],0,110);; ?></div>
                            <!--<div class="project">主营：化妆品,护肤品,中国领先化妆品特卖网站</div>-->
                            <div class="button">
                                <a class="btn salebuy" target="_blank" rel="nofollow" href="<? echo reUrl('Mall/out?id='.$item['id']);; ?>"><span>直接购物</span></a>
                                <a class="btn" href="<? echo reUrl('Mall/view?id='.$item['id']);; ?>"><span>领取优惠券</span></a>
                            </div>

                        </div>
                    </li>
                    <? } } ?>                            </ul>
            <ul class="pager"><?=$pagelink?></ul>
        </div>
                <div class="main_l_t">
            <h2>温馨提示：优惠券使用说明</h2>
            <div id="bottom">
                <p>如何使用优惠券？</p><p>1.通过优惠券网点击商家的优惠券链接，复制优惠代码。<br>2.在商家（比如当当、凡客等）选择商品.<br>3.选定商品,去结算中心.<br>4.在提示填入优惠券号码的框内，粘贴优惠券号码。<br> 淘宝商家在留言栏，粘贴优惠券号码，并和商家交谈提示有优惠券号码。<br>5.查看是否得到该优惠.<br>6.提交订单完成交易。<br></p>            </div>
        </div>
    </div>
        </div>
    </div>
<? include($template->getfile("../Public/footer")); ?>
