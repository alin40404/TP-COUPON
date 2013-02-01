<? if (!class_exists('template')) die('Access Denied');$template->getInstance()->check('../Public/header.html', 'c94eeb5f90ded877c433363d090b71d0', 1359708022);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><? if($page_title) { ?><?=$page_title?><? } ?><?=$_CFG['site_name']?> - <?=$_CFG['site_title']?> - Powered by TP-COUPON</title>
<link rel="shortcut icon" href="favicon.ico" />
<meta name="keywords" content="<? if($page_keywords) { ?><?=$page_keywords?><? } else { ?><?=$_CFG['site_keywords']?><? } ?>">
<meta name="description" content="<? if($page_description) { ?><?=$page_description?><? } else { ?><?=$_CFG['site_description']?><? } ?>">
<meta name="author" content="anqiu xiao" />
<meta name="copyright" content="2012-2015 jihaoju.com ijiaqu.com" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/Css/Home/style.css" />
<script type="text/javascript">var _public_='__PUBLIC__';</script>
<script src="__PUBLIC__/Js/common.js" type="text/javascript"></script>
<script type="text/javascript">
var images = '__PUBLIC__/Images/Home/';
var user_id = "<?=$user['user_id']?>";
var user_nick = "<?=$user['nick']?>";
var login_url = '<? echo reUrl("User/login"); ?>';
var service_qq = '<?=$_CFG["service_qq"]?>';
var weibo_sina = '<?=$_CFG["weibo_sina"]?>';
var weibo_qq = '<?=$_CFG["weibo_qq"]?>';
var user = {'user_id':user_id,'nick':user_nick};
jQuery(function() {
		    window.mainObj = window.mainObj ? window.mainObj : $("#main");
							if (mainObj.length > 0) {
                                var goWeibo = jQuery('<div id="go_weibo" class="float_bar"><a href="http://weibo.com/'+weibo_sina+'" target="_blank"><img src="'+images+'sina.gif" title="新浪微博" /></a><a href="http://t.qq.com/'+weibo_qq+'" target="_blank"><img src="'+images+'tencent.gif" title="腾讯微博" /></a><a href="http://wpa.qq.com/msgrd?v=3&amp;uin='+service_qq+'&amp;site=qq&amp;menu=yes" target="_blank"><img src="'+images+'qq.gif" title="联系在线客服" /></a></div>').appendTo(document.body);
                                var goTopObj = jQuery('<div id="go_top" class="float_bar"><div class="return"><a href="javascript:;" title="回到顶部">回顶部</a></div></div>').appendTo(document.body);
                                jQuery(".return a,a.return").live("click", function() {
                                    jQuery("html,body").animate({
                                        scrollTop: 0
                                    }, 500);
                                });
                                jQuery(".suggest a,a.suggest").live("click", function() {
                                    Youhui.common.suggest.init();
                                });
                                jQuery(window).bind("resize.gotop", function() {
                                    goTopObj.css("left", mainObj.outerWidth() + mainObj.offset().left + 10);
                                    goWeibo.css("left", mainObj.outerWidth() + mainObj.offset().left + 10).show();
                                }).bind("scroll.gotop", function() {
                                    //if(jQuery(window).scrollTop() > jQuery(window).height()*1.2){
                                    if (jQuery(window).scrollTop() > 30) {
                                        goTopObj.fadeIn('fast');
                                    } else {
                                        goTopObj.fadeOut('fast');
                                    }
                                    if (jQuery(window).scrollTop() > 100) {
                                        if (jQuery(".float_nav").length == 0) {
                                            jQuery("#nav").clone(true).addClass("float_nav").appendTo(document.body);
                                        }
                                    } else {
                                        jQuery(".float_nav").remove();
                                    }
                                }).triggerHandler("resize.gotop");
                            }
		/*当有ajax请求时显示
                            var loading_lite = jQuery('<div class="loading_lite" style="display:none">加载中，请稍候...</div>').appendTo(document.body).ajaxStart(function() {
                            jQuery(this).html('加载中，请稍候...').fadeIn('normal');
                            }).ajaxSuccess(function() {
                            jQuery(this).stop().hide();
                            }).ajaxError(function() {
                            jQuery(this).html('加载异常，请稍候刷新重试').show().delay(3000).fadeOut(1500);
                            });
                            */
		if(jQuery("#usercenter")){
			jQuery("#usercenter").hover(function() {
                                jQuery(".nav_i", this).show().prev().addClass("active");
                            }, function() {
                                jQuery(".nav_i", this).hide().prev().removeClass("active"); ;
                            });
		}
});
</script>

