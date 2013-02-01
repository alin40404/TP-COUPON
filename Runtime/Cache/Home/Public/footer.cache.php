<? if (!class_exists('template')) die('Access Denied');$template->getInstance()->check('../Public/footer.html', '309ddb9bfeb5c563f10ee0d696f67f3e', 1359708022);?>
<div class="w990 clear" id="footer">
        <ul class="sections clear">
            <li>
                <h4>
                    相关链接</h4>
                <ul class="twocol">
                    <li><a href="__ROOT__/Html/about.html">关于我们</a></li>
                    <li><a href="__ROOT__/Html/contact.html">联系我们</a></li>
                    <li><a href="__ROOT__/Html/pay.html">如何充值</a></li>
                    <li><a href="__APP__?m=Article&amp;a=links">友情链接</a></li>
                </ul>
            </li>
            
            <li>
                <h4>
                    新浪微博关注<?=$_CFG['site_name']?></h4>
                <p class="inline">
                    如果您新浪微博用户，您可以关注<?=$_CFG['site_name']?>官方微博，以便及时获取最新优惠信息。<br>
                    <a rel="nofollow" target="_blank" title="关注我们" href="http://weibo.com/jihaoju">关注我们</a></p>
                <a rel="nofollow" class="follow sina" title="<?=$_CFG['site_name']?>新浪微博" target="_blank" href="http://weibo.com/jihaoju">微博主页</a> </li>
            <li>
                <h4>
                    QQ空间关注<?=$_CFG['site_name']?></h4>
                <p class="inline">
                    如果您是QQ空间的用户，您可以通过点击我喜欢来关注<?=$_CFG['site_name']?>，随时查看优惠信息。<br>
                    <a rel="nofollow" title="关注我们" href="javascript:void(0);">关注我们</a></p>
                <a rel="nofollow" class="follow qq" title="<?=$_CFG['site_name']?>腾讯认证空间" href="javascript:void(0);">我喜欢</a> </li>
        </ul>
        <div id="copyright">
            <?=$_CFG['site_name']?>优惠券网&copy;版权所有，未经许可严禁复制或镜像 ICP证：<?=$_CFG['icp_number']?>&nbsp;&nbsp;<?=$_CFG['statis_code']?><br>
            Powered by <a href="http://soft.qfanqie.com" target="_blank">TP-COUPON</a> 技术支持：<a href="http://www.jihaoju.com" target="_blank">jihaoju.com</a>&nbsp;&amp;&nbsp;<a href="http://www.ijiaqu.com" target="_blank">ijiaqu.com</a>&nbsp;2012-2015 All rights reserved<br>
            
        </div>
    </div>
<!-- Baidu Button BEGIN -->
<script type="text/javascript" id="bdshare_js" data="type=slide&amp;img=1&amp;uid=668204" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
<!-- Baidu Button END -->
</body>
</html>