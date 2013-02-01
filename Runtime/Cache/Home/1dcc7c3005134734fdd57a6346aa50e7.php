<?php if (!defined('THINK_PATH')) exit();?>{template "../Public/header"}
{template "../Public/nav"}
<div class="w990 clear">
		<ul id="category-tabs">
                    <li class="current"><a href="__ROOT__/">知名商城</a></li>
                    <!--{eval $cates = get_mall_category_tree();}-->
                    <!--{eval $ii=1;}-->
                    <!--{loop $cates $c}-->
                    <!--{if $c['level']==0 && $ii<11}-->
                    <li><a href="{echo reUrl('Code/latest?cate_id='.$c['id'].'&t_type=0&cate_id2=0&p=1');}"><?php echo ($c["name"]); ?></a></li>
                    <!--{/if}-->
                    <!--{eval $ii++;}-->
                    <!--{/loop}-->
                </ul>
        <div class="gray" id="indexshop">
            <ul>
            	<!--{eval $mall_recs=rec_malls(101);}-->
                <!--{loop $mall_recs $item}-->
                <li>
                    <div class="store-item">
                        <a href="{echo reUrl('Mall/view?id='.$item[c_id])}" title="<?php echo ($item["name"]); ?>" class="store-logo" target="_blank">
                            <img width="80px" height="40px" alt="<?php echo ($item["name"]); ?>" src="{echo FixedUploadedFileUrl($item[logo])}" style="display: block;">
                        </a><a href="{echo reUrl('Mall/view?id='.$item[c_id]);}" title="<?php echo ($item["name"]); ?>" class="store-name" target="_blank"><?php echo ($item["name"]); ?></a>
                    </div>
                </li>
                <!--{/loop}-->
            </ul>
        </div>
        <div href="javascript:;" class="" id="moreshop">
            <b class="arrow"></b><span class="more-text"><a href="{echo reUrl('Mall/lists');}">更多商家</a></span> <span class="less-text">收起</span>
        </div>
        <div style="padding-top: 0;" id="main">
            <div class="main_l">
            <!-- Baidu Button BEGIN -->
    <div id="bdshare" class="bdshare_t bds_tools_32 get-codes-bdshare">
		<a class="bds_qzone" title="分享到QQ空间"></a>
        <a class="bds_tsina" title="分享到新浪微博"></a>
        <a class="bds_renren" title="分享到人人网"></a>
        <a class="bds_kaixin001" title="分享到开心网"></a>
        <a class="bds_tqf" title="分享到腾讯朋友"></a>
        <a class="bds_douban" title="分享到豆瓣网"></a>
        <a class="bds_taobao" title="分享到淘宝"></a>
        <a class="bds_ty" title="分享到天涯社区"></a>
        <a class="bds_baidu" title="分享到百度搜藏"></a>
        <span class="bds_more">更多</span>
		<a class="shareCount"></a>                
    </div>
<!-- Baidu Button END -->
<br />
<span>&nbsp;</span>
<div class="clear">&nbsp;</div>
                <div class="main_l_i">
                    <ul class="coupons-list" id="J_CouponsList" style="margin-left: 5px;">
<!--{loop $codes $item}-->          
<li>
    <div class="coupon-wrapper">
        <div class="scissors">
        </div>
        <h2><a href="{echo reUrl('Code/view?id='.$item[c_id]);}" title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" target="_blank"><!--{if $item['title']}--><?php echo ($item["title"]); ?><!--{else}--><?php echo ($item["m_name"]); ?><!--{if $item['c_type']==1}-->满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠券<!--{else}--><?php echo ($item["money_amount"]); ?>元代金券<!--{/if}--><!--{/if}--></a><!--<i class="hot">hot</i>--></h2>
        <a title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" href="{echo reUrl('Code/view?id='.$item[c_id]);}" class="coupon" target="_blank">
            <span class="left"><span class="description"><!--{if $item['c_type']==1}-->满<em><?php echo ($item["money_max"]); ?></em>减<em><?php echo ($item["money_reduce"]); ?></em><!--{else}--><em><?php echo ($item["money_amount"]); ?></em>元代金券<!--{/if}--></span><span class="store-logo">
                <img src="{echo FixedUploadedFileUrl($item[logo]);}" style="display: block;"></span> </span>
            <span class="right">
                
                <em class="free"><!--{if $item['price_type']==1}-->免费<!--{elseif $item['price_type']==2}--><?php echo ($item["price"]); ?>元<!--{elseif $item['price_type']==3}--><?php echo ($item["price"]); ?>积分<!--{/if}--></em>
                
                <span class="{if $item['amount']-$item['fetched_amount']>0}{if $item['expiry_type']==1 && $item['expiry']>0}{if $item['price_type']==1}get{else}sale{/if}{else}expire{/if}{else}pulled{/if}"><b></b>立即领取</span>
                
            </span></a>
        
        <div class="info">
            
            <span><!--{if $item['expiry_type']==1}--><!--{if $item['expiry']==0}-->已结束<!--{else}-->还剩 <i><!--{if $item['expiry']>$_CFG['max_left_days']}--><?php echo ($_CFG['max_left_days']); ?><!--{else}--><?php echo ($item["expiry"]); ?><!--{/if}--></i> 天<!--{/if}--><!--{else}-->长期有效<!--{/if}--> </span><span>已领数量：<i><?php echo ($item["fetched_amount"]); ?></i></span><span>收录券</span>
            
            <span><a class="store-from" href="{echo reUrl('Mall/view?id='.$item[m_id])}"><?php echo ($item["m_name"]); ?>优惠券</a></span>
        </div>
        
    </div>
</li>
<!--{/loop}-->
</ul>
<ul class="pager"><?php echo ($pagelink); ?></ul>
</div>
</div>
            <div style="width: 320px;" class="main_r">
                <ul class="sidebar">
                    
                    <li class="gray">
                        <h3>
                            每日精选促销({echo date('m-d')})</h3>
                        <ul class="hot_coupon">
                        	<!--{eval $daybest=coupon_codes_daybest(10);}-->
                            <!--{loop $daybest $item}-->
                            <li><a target="_blank" title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" class="img_wrap" href="{echo reUrl('Code/view?id='.$item[c_id]);}">
                                <img alt="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" src="{echo FixedUploadedFileUrl($item[logo]);}" align="absmiddle"></a>
                                <div class="img_detail">
                                    <p class="name">
                                        <a target="_blank" title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" href="{echo reUrl('Code/view?id='.$item[c_id]);}">{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}</a> </p>
                                    <p class="btn_wrap">
                                        <a target="_blank" title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" class="btn" href="{echo reUrl('Code/view?id='.$item[c_id]);}"><span>去看看</span></a></p>
                                        
                                       <p class="score_old flwindex_tuijian">
                                    </p>
                                    
                                </div>
                            </li>
                            <!--{/loop}-->
                         </ul>
                    </li>
                    <li class="gray">
                        <h3>
                            热门优惠券</h3>
                        <ul class="hot_coupon">
                        	<!--{eval $hot=coupon_codes_hot('week', 10);}-->
                            <!--{loop $hot $item}-->
                            <li><a class="img_wrap" href="{echo reUrl('Code/view?id='.$item[c_id]);}">
                                <img src="{echo FixedUploadedFileUrl($item[logo]);}"></a>
                                <div class="img_detail">
                                    <p class="name">
                                        <a title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" href="{echo reUrl('Code/view?id='.$item[c_id]);}">{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠券{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}</a></p>
                                    <p class="btn_wrap">
                                        <a title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" class="btn" href="{echo reUrl('Code/view?id='.$item[c_id]);}"><span>立即领取</span></a></p>
                                    <p class="score_now">
                                        已领：<?php echo ($item["fetched_amount"]); ?>张</p>
                                    <p class="score_old">
                                        {if $item['c_type']==1}满<del><?php echo ($item["money_max"]); ?></del>减<del><?php echo ($item["money_reduce"]); ?></del>{else}<del><?php echo ($item["money_amount"]); ?></del>元代金券{/if}
                                    </p>
                                </div>
                            </li>
                            <!--{/loop}-->
                        </ul>
                    </li>
                    <li class="gray">
                        <h3>
                            大家都在领什么券</h3>
                        <ul class="hot_coupon">
                        	<!--{eval $fetched=coupon_codes_fetched(10);}-->
                        	<!--{loop $fetched $item}-->
                            <li><a class="img_wrap" href="{echo reUrl('Code/view?id='.$item[c_id]);}">
                                <img src="{echo FixedUploadedFileUrl($item[logo]);}"></a>
                                <div class="img_detail">
                                    <p class="name">
                                        <a title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" href="{echo reUrl('Code/view?id='.$item[c_id]);}">{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠券{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}</a></p>
                                    <p>
                                        已领数量：<?php echo ($item["fetched_amount"]); ?>张</p>
                                    <p>
                                        领取时间：{echo date('H:i:s',$item['fetch_time'])}</p>
                                    <p>
                                        领取用户：
                                        <?php echo ($item["nick"]); ?>
                                    </p>
                                </div>
                            </li>
                            <!--{/loop}-->
                        </ul>
                    </li>
                    <li class="general">
                        <h3>
                            优惠券分类列表</h3>
                        <ul class="twocol">
                            
                            <li><a href="{echo reUrl('Code/latest?cate_id=0&t_type=0&cate_id2=0&p=1');}" target="_blank" title="全部优惠券">
                                全部</a></li>
                            <!--{loop $cates $item}-->
                            <!--{if $item['level']==0}-->
                            <li><a href="{echo reUrl('Code/latest?cate_id='.$item[id].'&t_type=0&cate_id2=0&p=1');}" target="_blank" title="<?php echo ($item["name"]); ?>优惠券">
                                <?php echo ($item["name"]); ?></a></li>
                             <!--{/if}-->
                        	<!--{/loop}-->
                        </ul>
                    </li>
                    <li class="yellow">
                        <h3>
                            最新发布优惠券</h3>
                        <ol class="rank_coupon">
                            <!--{eval $latest=coupon_codes_latest();}--> 
                            <!--{loop $latest $item}-->
                            <li><a title="{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠码{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}" href="{echo reUrl('Code/view?id='.$item[c_id]);}">{if $item['title']}<?php echo ($item["title"]); ?>{else}<?php echo ($item["m_name"]); ?>{if $item['c_type']==1}满<?php echo ($item["money_max"]); ?>减<?php echo ($item["money_reduce"]); ?>元优惠券{else}<?php echo ($item["money_amount"]); ?>元代金券{/if}{/if}</a></li>
                            <!--{/loop}-->
                            
                        </ol>
                    </li>
                    <li class="green">
                        <h3>
                            热门搜索</h3>
                        <ul class="multicol">
                            <!--{eval $mall_hot20=hot_malls('week', 20);}-->
                            <!--{loop $mall_hot20 $item}-->
                            <li><a title="<?php echo ($item["name"]); ?>优惠券" href="{echo reUrl('Mall/view?id='.$item[id]);}"><?php echo ($item["name"]); ?>优惠券</a></li>
                            <!--{/loop}-->
                        </ul>
                    </li>
                    <li class="gray">
                        <h3>友情链接</h3>
                        <ul class="threecol">
                            <!--{loop $friendlinks $item}-->
                            <!--{if $item['link_type']==1}-->
                            <li><a href="<?php echo ($item["link_url"]); ?>" target="_blank">{if $item['link_code']}<?php echo ($item["link_code"]); ?>{else}<?php echo ($item["site_name"]); ?>{/if}</a></li>
                            <!--{/if}-->
                            <!--{/loop}-->
                            <div class="clear">
                            </div>
                        </ul>
                        <ul class="threecol">
                            <!--{loop $friendlinks $item}-->
                            <!--{if $item['link_type']==2}-->
                            <li><a href="<?php echo ($item["link_url"]); ?>" target="_blank"><img src="{echo FixedUploadedFileUrl($item[link_code]);}" width="75" height="45" /></a></li>
                            <!--{/if}-->
                            <!--{/loop}-->
                            <div class="clear">
                            </div>
                        </ul>
                        <p style="padding-left:15px;">友情链接合作请联系 QQ:<?php echo ($_CFG["service_qq"]); ?></p>
                    </li>
                    
                </ul>
            </div>
        </div>
    </div>
<script type="text/javascript" id="bdshare_js" data="type=tools" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
{template "../Public/footer"}