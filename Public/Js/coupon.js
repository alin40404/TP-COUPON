// JavaScript Document
var coupon = {
	init : function(){
		var self=this;
		$('a#pull').click(function() {
			if(user.user_id == 0){
				window.location.href = login_url;
				return false;
			}
			var html = '';
			if(parseInt($(this).attr('ptype'))==2){
				html += '<font color="#ff6600">确定购买该优惠券吗？<br />';
				html += '需要支付'+parseFloat($(this).attr('price'))+'元购买该优惠券</font>';
			}else if(parseInt($(this).attr('ptype'))==3){
				html += '<font color="#ff6600">确定购买该优惠券吗？<br />';
				html += '需要支付'+parseInt($(this).attr('price'))+'积分购买该优惠券</font>';
			}else{
				self.pull(parseInt($(this).attr('cid')));
				return;
			}
			html += '<br /><br />是否继续领取？<br />';
			$.fn.jmodal({
                    data: { c_id:parseInt($(this).attr('cid')) },
                    title: '温馨提示',
                    content: html,
                    buttonText: { ok: '确定', cancel: '取消' },
                    fixed: false,
					marginTop: 200,
					okEvent: function(data, args) {
                        self.pull(data.c_id);
                    }
                });
            });
	},
	pull : function(c_id){
		$.getJSON(pull_url+'&ajax=1&c_id='+c_id, function(data){
												   var html = '';
												   if(data.status == 0){
													   html = data.info;
												   }else{
													   html = '<ul id="pullinfo" class="clear"><li><ins>优惠券代码(复制下面代码去购物网站使用)：</ins></li><li id="code_3074" class="code"><font color="red"><b>优惠代码：'+data.data.code+'</b></font></li><li class="code">&nbsp;</li><li class="code">领取的优惠券在"账号中心"也都可以查到：</li><li class="code"><a target="_blank" href="'+mycodes_url+'">&nbsp;&nbsp;账号中心地址</a></li><li class="code">&nbsp;</li><li class="code">领取的优惠券如何使用：</li><li class="code"><a target="_blank" title="使用方法" href="'+how2use_url+'">使用方法</a></li><li><ins><a target="_blank" id="go_shopping" href="'+shopping_url+'"><span>去商家购物</span></a></ins></li></ul>';
													   window.open(shopping_url);
												   }
												   $.fn.jmodal({
															data: {},
                    										title: '温馨提示',
                    										content: html,
                    										buttonText: { ok: '确定', cancel: '' },
                    										fixed: false,
															marginTop: 200,
															okEvent: function(data, args) {
                        													args.hide();
                    										}
                											});
												   });
	},
	tips:function(text,left,top,arrow,loc,obj,closefunc){
        var id=left+'_'+top+'_'+arrow+'_'+loc;
        if(document.getCookie('close_'+id)!=1){
            var c = '<div id="tips_'+id+'" class="newtips" style="top: '+top+'px; left: '+left+'px; ">';
            c = c + '<div class="tipcontainer" style="opacity: 1; ">';
            c = arrow == 'up' ? c + '<span class="arrowup" style="margin-left: '+loc+'px; "></span>' : c;
            c = c + '<div class="t_container"><div class="tcc"><div class="t_content">';
            c = c + '<div class="tipsicon "><span class="tipsico tipsico_normal"></span></div>';
            c = c + '<div class="tipstxt"><span class="black">'+text+'</span></div>';
            c = c + '<div class="tipsrightpanel"><div class="opertbar"><a class="" id="close_'+id+'" href="javascript:;">我知道了</a></div></div>';
            c = c + '<div class="clear"></div>';
            c = c + '</div></div></div>';
            c = arrow == 'down' ?  c + '<span class="arrowdown" style="display:none; margin-left: '+loc+'px; "></span>' : c;
            c = c + '</div></div>';

            $(c).appendTo((obj!=undefined && obj!='' ? obj : document.body));
            $("#close_"+id).click(function(){
                //关闭层和写Cookie
                $("#tips_"+id).fadeOut();
                document.setCookie('close_'+id,1,3600);
                if(closefunc!=undefined){
                    closefunc();
                }
            });
        }
    }
};