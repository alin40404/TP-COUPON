<? if (!class_exists('template')) die('Access Denied');$template->getInstance()->check('lastestpulled.html', 'fc571bc153a6894b082516e25a6a4890', 1359708054);?>

<? include($template->getfile("../Public/header")); include($template->getfile("../Public/nav")); ?>
<div class="w990 clear">
        <div id="main">
            <div class="main_l">
                <div id="main_l_l" class="clear">
                    <h2>最近被领取的优惠券</h2>
                    <ul class="coupons-list" id="J_CouponsList" style="margin-left: 5px;"><? if(is_array($codes)) { foreach($codes as $item) { ?>          
<li>
    <div class="coupon-wrapper">
        <div class="scissors">
        </div>
        <h2><a href="<? echo reUrl('Code/view?id='.$item['c_id']);; ?>" title="<? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠码<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?>" target="_blank"><? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠券<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?></a><!--<i class="hot">hot</i>--></h2>
        <a title="<? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠码<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?>" href="<? echo reUrl('Code/view?id='.$item['c_id']);; ?>" class="coupon" target="_blank">
            <span class="left"><span class="description"><? if($item['c_type']==1) { ?>满<em><?=$item['money_max']?></em>减<em><?=$item['money_reduce']?></em><? } else { ?><em><?=$item['money_amount']?></em>元代金券<? } ?></span><span class="store-logo">
                <img src="<? echo FixedUploadedFileUrl($item['logo']);; ?>" style="display: block;"></span> </span>
            <span class="right">
                
                <em class="free"><? if($item['price_type']==1) { ?>免费<? } elseif($item['price_type']==2) { ?><?=$item['price']?>元<? } elseif($item['price_type']==3) { ?><?=$item['price']?>积分<? } ?></em>
                
                <span class="<? if($item['amount']-$item['fetched_amount']>0) { if($item['expiry_type']==1 && $item['expiry']>0) { if($item['price_type']==1) { ?>get<? } else { ?>sale<? } } else { ?>expire<? } } else { ?>pulled<? } ?>"><b></b>立即领取</span>
                
            </span></a>
        
        <div class="info">
            
            <span><? if($item['expiry_type']==1) { if($item['expiry']==0) { ?>已结束<? } else { ?>还剩 <i><? if($item['expiry']>$_CFG['max_left_days']) { ?><?=$_CFG['max_left_days']?><? } else { ?><?=$item['expiry']?><? } ?></i> 天<? } } else { ?>长期有效<? } ?> </span><span>已领数量：<i><?=$item['fetched_amount']?></i></span><span>最近领取</span>
            
            <span><?=$item['nick']?></span>
        </div>
        
    </div>
</li><? } } ?></ul>
<ul class="pager"><?=$pagelink?></ul>
                </div>
            </div>
            <div class="main_r" style="width: 320px;">
                <ul class="sidebar">
                    <li class="gray">
                        <h3>
                            热门优惠券</h3>
                        <ul class="hot_coupon">
                            <? $hot=coupon_codes_hot('week', 10); ?>                            <? if(is_array($hot)) { foreach($hot as $item) { ?>                            <li><a class="img_wrap" href="<? echo reUrl('Code/view?id='.$item['c_id']);; ?>">
                                <img src="<? echo FixedUploadedFileUrl($item['logo']);; ?>"></a>
                                <div class="img_detail">
                                    <p class="name">
                                        <a title="<? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠码<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?>" href="<? echo reUrl('Code/view?id='.$item['c_id']);; ?>"><? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠券<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?></a></p>
                                    <p class="btn_wrap">
                                        <a title="<? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠码<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?>" class="btn" href="<? echo reUrl('Code/view?id='.$item['c_id']);; ?>"><span>立即领取</span></a></p>
                                    <p class="score_now">
                                        已领：<?=$item['fetched_amount']?>张</p>
                                    <p class="score_old">
                                        <? if($item['c_type']==1) { ?>满<del><?=$item['money_max']?></del>减<del><?=$item['money_reduce']?></del><? } else { ?><del><?=$item['money_amount']?></del>元代金券<? } ?>
                                    </p>
                                </div>
                            </li>
                            <? } } ?>                        </ul>
                    </li>
                    <li class="yellow">
                        <h3>
                            最新发布优惠券</h3>
                        <ol class="rank_coupon">
                            
                            <? $latest=coupon_codes_latest(); ?> 
                            <? if(is_array($latest)) { foreach($latest as $item) { ?>                            <li><a title="<? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠码<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?>" href="<? echo reUrl('Code/view?id='.$item['c_id']);; ?>"><? if($item['title']) { ?><?=$item['title']?><? } else { ?><?=$item['m_name']?><? if($item['c_type']==1) { ?>满<?=$item['money_max']?>减<?=$item['money_reduce']?>元优惠券<? } else { ?><?=$item['money_amount']?>元代金券<? } } ?></a></li>
                            <? } } ?>                            
                        </ol>
                    </li>
                    <li class="green">
                        <h3>
                            热门搜索</h3>
                        <ul class="multicol">
                        <? $mall_hot20=hot_malls('week', 20); ?>                            <? if(is_array($mall_hot20)) { foreach($mall_hot20 as $item) { ?>                            <li><a title="<?=$item['name']?>优惠券" href="<? echo reUrl('Mall/view?id='.$item['id']);; ?>"><?=$item['name']?>优惠券</a></li>
                            <? } } ?>                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<? include($template->getfile("../Public/footer")); ?>
